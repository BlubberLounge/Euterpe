<?php

namespace App\Services\Music;

use App\Models\ExternalServiceUser;

interface MusicServiceInterface
{
    /**
     * Return the OAuth authorization URL for this service.
     */
    public function getAuthorizationUrl(array $scopes = []): string;

    /**
     * Exchange authorization code for tokens.
     */
    public function exchangeCodeForToken(string $code): array;

    /**
     * Refresh an expired access token.
     */
    public function refreshAccessToken(string $refreshToken): array;

    /**
     * Ensure and return a valid access token, refreshing if necessary.
     */
    public function ensureValidAccessToken(ExternalServiceUser $account): string;

    /**
     * Search for tracks by query.
     */
    public function searchTracks(string $query, string $accessToken, int $limit = 10): array;

    /**
     * Enhanced search returning tracks and artists.
     */
    public function search(string $query, string $accessToken, array $types = ['track', 'artist'], int $limit = 15): array;

    /**
     * Get an artist's top tracks.
     */
    public function getArtistTopTracks(string $artistId, string $accessToken, string $market = 'DE'): array;

    /**
     * Add a track to the current playback queue.
     * Returns ['success' => bool, 'error' => string|null]
     */
    public function addToQueue(string $uri, string $accessToken, ?string $deviceId = null): array;
}
