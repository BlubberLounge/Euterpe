<?php

namespace App\Http\Controllers;

use App\Enums\ExternalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Music\MusicServiceManager;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function search(Request $request, MusicServiceManager $manager)
    {
        $request->validate([
            'service' => 'required|string|in:spotify,soundcloud',
            'q' => 'required|string'
        ]);

        $user = $request->user();
        $service = $manager->resolve($request->service);

        $account = $user->externalAccounts()
            ->where('service', $request->service)
            ->firstOrFail();

        $token = $service->ensureValidAccessToken($account);
        $tracks = $service->searchTracks($request->q, $token);

        return response()->json($tracks);
    }

    public function queue(Request $request, MusicServiceManager $manager)
    {
        $request->validate([
            'service' => 'required|string|in:spotify,soundcloud',
            'uri' => 'required|string'
        ]);

        $user = $request->user();
        $service = $manager->resolve($request->service);
        $account = $user->externalAccounts()
            ->where('service', $request->service)
            ->firstOrFail();

        $token = $service->ensureValidAccessToken($account);
        $service->addToQueue($request->uri, $token);

        return response()->json(['status' => 'queued']);
    }
}
