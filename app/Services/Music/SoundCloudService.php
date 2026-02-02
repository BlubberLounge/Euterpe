<?php

namespace App\Services\Music;

use App\Models\ExternalServiceUser;

class SoundCloudService implements MusicServiceInterface
{
    public function getAuthorizationUrl(array $scopes = []): string
    {
        // SoundCloud OAuth endpoint
        return 'https://soundcloud.com/connect?...';
    }

    public function exchangeCodeForToken(string $code): array
    {
        // POST to SoundCloud token endpoint
        return [];
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        return [];
    }

    public function ensureValidAccessToken(ExternalServiceUser $account): string
    {
        return $account->access_token;
    }

    public function searchTracks(string $query, string $accessToken, int $limit = 10): array
    {
        return [];
    }

    public function search(string $query, string $accessToken, array $types = ['track', 'artist'], int $limit = 15): array
    {
        return [
            'tracks' => [],
            'artists' => [],
            'total' => ['tracks' => 0, 'artists' => 0],
        ];
    }

    public function getArtistTopTracks(string $artistId, string $accessToken, string $market = 'DE'): array
    {
        return [];
    }

    public function addToQueue(string $uri, string $accessToken, ?string $deviceId = null): array
    {
        return ['success' => false, 'error' => 'SoundCloud queue not implemented'];
    }
}
