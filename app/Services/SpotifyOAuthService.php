<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SpotifyOAuthService
{

    public function redirect($request)
    {
        // $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.spotify.redirect'),
            'scope' => 'user-read-email user-read-private user-read-currently-playing user-read-playback-state',
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $query);
    }

    protected function getToken($request)
    {
        // $state = $request->session()->pull('state');

        // throw_unless(
        //     strlen($state) > 0 && $state === $request->state,
        //     InvalidArgumentException::class,
        //     'Invalid state value.'
        // );

        $code = $request->get('code');

        $response = Http::asForm()->withBasicAuth(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret')
        )->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
        ]);

        return $response;
    }

    public function user($request)
    {
        $response = $this->getToken($request);

        $accessToken = $response->json('access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not received', 'response' => $response->json()], 400);
        }

        $user = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/me')
            ->json();

        $user = response()->json($user)->getData(true);
        $user['access_token'] = $accessToken;

        return $user;
    }
}
