<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ExternalServices;
use App\Http\Controllers\Controller;
use App\Models\ExternalServiceUser;
use App\Models\User;
use App\Services\Music\SpotifyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpotifyAuthController extends Controller
{
    public function __construct(
        protected SpotifyService $spotifyService
    ) {}

    public function redirect(): RedirectResponse
    {
        return redirect($this->spotifyService->getAuthorizationUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $authUser = $this->spotifyService->user($request);

        $user = User::updateOrCreate(
            ['email' => $authUser['email']],
            [
                'name' => $authUser['id'],
                'password' => $authUser['id'],
            ]
        );

        $user->refresh();

        ExternalServiceUser::updateOrCreate(
            [
                'user_id' => $user->id,
                'service' => ExternalServices::SPOTIFY,
            ],
            [
                'name' => $user->id,
                'email' => $authUser['email'],
                'access_token' => $authUser['access_token'],
                'refresh_token' => $authUser['refresh_token'],
                'token_expires_at' => now()->addSeconds($authUser['expires_in'] ?? 3600),
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
