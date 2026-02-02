<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#111827">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Euterpe">

        <title>{{ config('app.name', 'Euterpe') }}</title>

        <!-- PWA Links -->
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon.svg">
        <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --sat: env(safe-area-inset-top);
                --sab: env(safe-area-inset-bottom);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-900 text-white">
        <div class="min-h-dvh flex flex-col justify-center items-center px-4 py-8">
            <div class="w-full max-w-sm flex flex-col justify-center items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 mb-8">
                    <svg class="w-12 h-12 text-green-500" viewBox="0 0 512 512" fill="currentColor">
                        <circle cx="256" cy="300" r="120"/>
                        <circle cx="256" cy="300" r="40" fill="#111827"/>
                        <rect x="320" y="120" width="40" height="200" rx="8"/>
                        <rect x="280" y="100" width="120" height="40" rx="8"/>
                    </svg>
                    <span class="font-bold text-2xl text-white">Euterpe</span>
                </a>

                <!-- Content Card -->
                <div class="w-full bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl">
                    {{ $slot }}
                </div>

                <!-- Spotify Sign In (Login page only) -->
                @if(Route::is('login'))
                    <div class="w-full mt-4">
                        <a href="{{ route('auth.spotify.redirect') }}"
                           class="flex items-center justify-center gap-3 w-full py-3.5 bg-[#1ED760] hover:bg-[#1fbb55] text-gray-900 font-semibold rounded-xl transition-colors active:scale-[0.98]">
                            <img src="{{ asset('assets/Primary_Logo_Black_RGB.svg') }}" class="w-6 h-6" alt="Spotify">
                            Continue with Spotify
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        @php
            use Carbon\Carbon;
        @endphp
        <div class="fixed bottom-0 left-0 right-0 pb-safe text-center py-4 px-4 text-xs text-gray-600">
            <p>{{ env('APP_NAME', 'Euterpe') }} v{{ env('APP_VERSION', '1.0') }}</p>
            @if (App::environment(['local', 'dev', 'development']) || config('app.debug'))
                <p class="mt-1">Laravel v{{ Illuminate\Foundation\Application::VERSION }} · PHP v{{ PHP_VERSION }}</p>
            @endif
        </div>
    </body>
</html>
