<?php

namespace App\Providers;

use App\Services\Music\MusicServiceManager;
use App\Services\Music\SoundCloudService;
use App\Services\Music\SpotifyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MusicServiceManager::class);
        $this->app->singleton(SpotifyService::class);
        $this->app->singleton(SoundCloudService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
