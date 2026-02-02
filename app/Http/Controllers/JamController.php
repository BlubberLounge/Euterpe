<?php

namespace App\Http\Controllers;

use App\Enums\ExternalServices;
use App\Enums\JamRole;
use App\Models\Jam;
use App\Http\Requests\StoreJamRequest;
use App\Http\Requests\UpdateJamRequest;
use App\Services\Music\MusicServiceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class JamController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('jam.index', [
            'hostedJams' => $user->hostedJams()->with('users')->latest()->get(),
            'joinedJams' => $user->jams()->wherePivot('role', JamRole::MEMBER->value)->latest()->get(),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $hasSpotify = Auth::user()->externalAccounts()
            ->where('service', ExternalServices::SPOTIFY)
            ->exists();

        if (!$hasSpotify) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please connect your Spotify account to host a Jam.');
        }

        return view('jam.create');
    }

    public function store(StoreJamRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->hostedJams()->where('is_active', true)->update(['is_active' => false]);

        $jam = Jam::create([
            'created_by' => $user->id,
            'name' => $request->validated('name'),
            'is_active' => true,
        ]);

        $jam->users()->attach($user->id, [
            'role' => JamRole::HOST->value,
            'joined_at' => now(),
        ]);

        return redirect()->route('jam.show', $jam)
            ->with('success', 'Jam created! Share the QR code with your friends.');
    }

    public function show(Jam $jam): View|RedirectResponse
    {
        $this->authorize('view', $jam);

        $user = Auth::user();
        $isHost = $jam->isHost($user);

        $jam->load(['queueItems.addedBy', 'users', 'currentTrack']);

        $data = [
            'jam' => $jam,
            'isHost' => $isHost,
            'upcomingTracks' => $jam->upcomingTracks()->with('addedBy')->get(),
            'currentTrack' => $jam->currentTrack,
        ];

        if ($isHost) {
            $data['accessToken'] = $user->getAccessTokenFor(ExternalServices::SPOTIFY);
        }

        return view('jam.show', $data);
    }

    public function edit(Jam $jam): View
    {
        $this->authorize('update', $jam);

        return view('jam.edit', compact('jam'));
    }

    public function update(UpdateJamRequest $request, Jam $jam): RedirectResponse
    {
        $this->authorize('update', $jam);

        $jam->update($request->validated());

        return redirect()->route('jam.show', $jam)
            ->with('success', 'Jam updated.');
    }

    public function destroy(Jam $jam): RedirectResponse
    {
        $this->authorize('delete', $jam);

        $jam->update(['is_active' => false]);
        $jam->delete();

        return redirect()->route('jam.index')
            ->with('success', 'Jam ended.');
    }

    public function joinPage(string $code): View|RedirectResponse
    {
        $jam = Jam::where('join_code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        if (Auth::check() && $jam->isMember(Auth::user())) {
            return redirect()->route('jam.show', $jam);
        }

        return view('jam.join', compact('jam'));
    }

    public function join(Request $request, string $code): RedirectResponse
    {
        $jam = Jam::where('join_code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        $user = Auth::user();

        if ($jam->isMember($user)) {
            return redirect()->route('jam.show', $jam)
                ->with('info', 'You are already in this Jam.');
        }

        $jam->users()->attach($user->id, [
            'role' => JamRole::MEMBER->value,
            'joined_at' => now(),
        ]);

        return redirect()->route('jam.show', $jam)
            ->with('success', "You've joined {$jam->name}!");
    }

    public function leave(Jam $jam): RedirectResponse
    {
        $user = Auth::user();

        if ($jam->isHost($user)) {
            return back()->with('error', 'Hosts cannot leave their own Jam. End it instead.');
        }

        $jam->users()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return redirect()->route('jam.index')
            ->with('success', 'You have left the Jam.');
    }

    public function search(Request $request, Jam $jam, MusicServiceManager $manager): JsonResponse
    {
        $this->authorize('view', $jam);

        $request->validate([
            'q' => 'required|string|min:1',
            'type' => 'nullable|string|in:all,track,artist',
        ]);

        $service = $manager->resolve('spotify');
        $hostToken = $jam->creator->getAccessTokenFor(ExternalServices::SPOTIFY);

        if (!$hostToken) {
            return response()->json(['error' => 'Host Spotify not connected'], 400);
        }

        $searchType = $request->input('type', 'all');

        // Determine what to search for
        $types = match ($searchType) {
            'track' => ['track'],
            'artist' => ['artist'],
            default => ['track', 'artist'],
        };

        $results = $service->search($request->q, $hostToken, $types, 15);

        return response()->json($results);
    }

    /**
     * Get top tracks for an artist to add to queue.
     */
    public function artistTopTracks(Request $request, Jam $jam, string $artistId, MusicServiceManager $manager): JsonResponse
    {
        $this->authorize('view', $jam);

        $service = $manager->resolve('spotify');
        $hostToken = $jam->creator->getAccessTokenFor(ExternalServices::SPOTIFY);

        if (!$hostToken) {
            return response()->json(['error' => 'Host Spotify not connected'], 400);
        }

        $tracks = $service->getArtistTopTracks($artistId, $hostToken);

        return response()->json($tracks);
    }
}
