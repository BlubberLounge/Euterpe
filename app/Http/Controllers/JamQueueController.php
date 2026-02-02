<?php

namespace App\Http\Controllers;

use App\Enums\ExternalServices;
use App\Models\Jam;
use App\Models\JamQueue;
use App\Http\Requests\StoreJamQueueRequest;
use App\Services\Music\MusicServiceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class JamQueueController extends Controller
{
    public function store(StoreJamQueueRequest $request, Jam $jam): JsonResponse|RedirectResponse
    {
        $this->authorize('addToQueue', $jam);

        $user = Auth::user();

        $queueItem = JamQueue::create([
            'jam_id' => $jam->id,
            'added_by_user_id' => $user->id,
            'spotify_uri' => $request->validated('spotify_uri'),
            'track_name' => $request->validated('track_name'),
            'artist_name' => $request->validated('artist_name'),
            'album_name' => $request->validated('album_name'),
            'album_image_url' => $request->validated('album_image_url'),
            'duration_ms' => $request->validated('duration_ms'),
            'position' => $jam->getNextPosition(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'track' => $queueItem->load('addedBy'),
            ]);
        }

        return back()->with('success', 'Track added to queue!');
    }

    public function destroy(Jam $jam, JamQueue $queueItem): JsonResponse|RedirectResponse
    {
        $this->authorize('removeFromQueue', [$jam, $queueItem]);

        $position = $queueItem->position;
        $queueItem->delete();

        $jam->upcomingTracks()
            ->where('position', '>', $position)
            ->decrement('position');

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Track removed from queue.');
    }

    public function reorder(Request $request, Jam $jam): JsonResponse
    {
        $this->authorize('update', $jam);

        $request->validate([
            'track_ids' => 'required|array',
            'track_ids.*' => 'exists:jam_queues,id',
        ]);

        foreach ($request->track_ids as $position => $trackId) {
            JamQueue::where('id', $trackId)
                ->where('jam_id', $jam->id)
                ->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function markPlaying(Jam $jam, JamQueue $queueItem): JsonResponse
    {
        $this->authorize('update', $jam);

        if ($jam->currentTrack) {
            $jam->currentTrack->markAsPlayed();
        }

        $queueItem->markAsPlaying();

        return response()->json(['success' => true]);
    }

    public function pushToSpotify(Request $request, Jam $jam, JamQueue $queueItem, MusicServiceManager $manager): JsonResponse
    {
        $this->authorize('update', $jam);

        $service = $manager->resolve('spotify');
        $hostToken = $jam->creator->getAccessTokenFor(ExternalServices::SPOTIFY);

        if (!$hostToken) {
            return response()->json(['error' => 'Host Spotify not connected'], 400);
        }

        $result = $service->addToQueue($queueItem->spotify_uri, $hostToken, $request->device_id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to add to Spotify queue',
            ], $result['status'] ?? 400);
        }

        return response()->json(['success' => true]);
    }
}
