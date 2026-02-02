<?php

namespace App\Http\Controllers;

use App\Enums\ExternalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\Music\MusicServiceManager;

class DashboardController extends Controller
{
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request): never
    {
        abort(404);
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }

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

        $account = $user->externalServiceUsers()
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
        $account = $user->externalServiceUsers()
            ->where('service', $request->service)
            ->firstOrFail();

        $token = $service->ensureValidAccessToken($account);
        $service->addToQueue($request->uri, $token);

        return response()->json(['status' => 'queued']);
    }
}
