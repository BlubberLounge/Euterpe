<?php

namespace App\Services\Music;

use App\Exceptions\SpotifyException;
use App\Models\ExternalServiceUser;
use Illuminate\Support\Facades\Http;

class SpotifyService implements MusicServiceInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected array $defaultScopes = [
        'streaming',
        'user-read-email',
        'user-read-private',
        'user-read-playback-state',
        'user-modify-playback-state',
        'user-read-currently-playing',
    ];

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        $this->redirectUri = config('services.spotify.redirect');
    }

    public function getAuthorizationUrl(array $scopes = []): string
    {
        $scopes = empty($scopes) ? $this->defaultScopes : $scopes;

        $query = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $scopes),
        ]);

        return 'https://accounts.spotify.com/authorize?' . $query;
    }

    public function exchangeCodeForToken(string $code): array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]);

        return $response->json();
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        return $response->json();
    }

    public function ensureValidAccessToken(ExternalServiceUser $account): string
    {
        if ($account->token_expires_at && now()->lt($account->token_expires_at)) {
            return $account->access_token;
        }

        $data = $this->refreshAccessToken($account->refresh_token);

        $account->update([
            'access_token' => $data['access_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
            'refresh_token' => $data['refresh_token'] ?? $account->refresh_token,
        ]);

        return $account->access_token;
    }

    public function user($request)
    {
        $response = $this->exchangeCodeForToken($request['code']);

        $accessToken = $response['access_token'];

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not received', 'response' => $response], 400);
        }

        $user = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/me')
            ->json();

        $user = response()->json($user)->getData(true);
        $user['access_token'] = $accessToken;
        $user['refresh_token'] = $response['refresh_token'];
        $user['expires_in'] = $response['expires_in'];

        return $user;
    }

    public function searchTracks(string $query, string $accessToken, int $limit = 10): array
    {
        $response = Http::withToken($accessToken)->get('https://api.spotify.com/v1/search', [
            'q' => $query,
            'type' => 'track',
            'market' => 'DE',
            'limit' => $limit,
        ]);

        return $response->json('tracks.items') ?? [];
    }

    /**
     * Enhanced search returning tracks and artists with filter support.
     * Supports Spotify field filters: artist:, track:, album:, genre:, year:
     */
    public function search(string $query, string $accessToken, array $types = ['track', 'artist'], int $limit = 15): array
    {
        $response = Http::withToken($accessToken)->get('https://api.spotify.com/v1/search', [
            'q' => $query,
            'type' => implode(',', $types),
            'market' => 'DE',
            'limit' => $limit,
        ]);

        $data = $response->json();

        return [
            'tracks' => $data['tracks']['items'] ?? [],
            'artists' => $data['artists']['items'] ?? [],
            'total' => [
                'tracks' => $data['tracks']['total'] ?? 0,
                'artists' => $data['artists']['total'] ?? 0,
            ],
        ];
    }

    /**
     * Get an artist's top tracks.
     */
    public function getArtistTopTracks(string $artistId, string $accessToken, string $market = 'DE'): array
    {
        $response = Http::withToken($accessToken)->get(
            "https://api.spotify.com/v1/artists/{$artistId}/top-tracks",
            ['market' => $market]
        );

        return $response->json('tracks') ?? [];
    }

    public function addToQueue(string $uri, string $accessToken, ?string $deviceId = null): array
    {
        $query = ['uri' => $uri];
        if ($deviceId) {
            $query['device_id'] = $deviceId;
        }

        // Spotify requires query params, not body for this endpoint
        $url = 'https://api.spotify.com/v1/me/player/queue?' . http_build_query($query);

        $response = Http::withToken($accessToken)->post($url);

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => $response->json('error.message') ?? 'Failed to add to queue',
            'status' => $response->status(),
        ];
    }
}
