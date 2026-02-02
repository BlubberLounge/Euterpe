<?php

namespace App\Services\Music;

use App\Enums\ExternalServices;
use InvalidArgumentException;

class MusicServiceManager
{
    public function resolve(ExternalServices|string $service): MusicServiceInterface
    {
        $serviceName = $service instanceof ExternalServices ? $service->value : $service;

        return match ($serviceName) {
            'spotify' => app(SpotifyService::class),
            'soundcloud' => app(SoundCloudService::class),
            default => throw new InvalidArgumentException("Unsupported music service: {$serviceName}"),
        };
    }
}
