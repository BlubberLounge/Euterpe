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
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="Euterpe">
        <meta name="msapplication-TileColor" content="#111827">
        <meta name="msapplication-tap-highlight" content="no">

        <!-- PWA Links -->
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon.svg">
        <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

        <title>{{ config('app.name', 'Euterpe') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('scripts')

        <style>
            /* Safe area for notched devices */
            :root {
                --sat: env(safe-area-inset-top);
                --sar: env(safe-area-inset-right);
                --sab: env(safe-area-inset-bottom);
                --sal: env(safe-area-inset-left);
            }

            /* Hide scrollbar but allow scrolling */
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }

            /* Bottom nav safe area */
            .pb-safe {
                padding-bottom: calc(4rem + var(--sab, 0px));
            }

            /* Pull to refresh indicator */
            .pull-indicator {
                transition: transform 0.2s ease;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-900 text-white">
        <div class="min-h-screen flex flex-col">
            <!-- Top Header (minimal on mobile) -->
            <header class="sticky top-0 z-40 bg-gray-900/95 backdrop-blur-sm border-b border-gray-800 safe-top">
                <div class="flex items-center justify-between px-4 h-14">
                    <!-- Logo/Title -->
                    <div class="flex items-center gap-3">
                        @isset($headerLeft)
                            {{ $headerLeft }}
                        @else
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                                <svg class="w-8 h-8 text-green-500" viewBox="0 0 512 512" fill="currentColor">
                                    <circle cx="256" cy="300" r="120"/>
                                    <circle cx="256" cy="300" r="40" fill="#111827"/>
                                    <rect x="320" y="120" width="40" height="200" rx="8"/>
                                    <rect x="280" y="100" width="120" height="40" rx="8"/>
                                </svg>
                                <span class="font-bold text-lg hidden sm:block">Euterpe</span>
                            </a>
                        @endisset
                    </div>

                    <!-- Page Title (center, mobile) -->
                    @isset($title)
                        <h1 class="absolute left-1/2 -translate-x-1/2 font-semibold text-base truncate max-w-[50%]">
                            {{ $title }}
                        </h1>
                    @endisset

                    <!-- Right Actions -->
                    <div class="flex items-center gap-2">
                        @isset($headerRight)
                            {{ $headerRight }}
                        @else
                            @auth
                                <a href="{{ route('profile.edit') }}" class="p-2 rounded-full hover:bg-gray-800 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-sm font-medium text-gray-900">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                </a>
                            @endauth
                        @endisset
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 pb-safe overflow-y-auto hide-scrollbar">
                {{ $slot }}
            </main>

            <!-- Bottom Navigation (Mobile) -->
            @auth
            <nav class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900/95 backdrop-blur-sm border-t border-gray-800" style="padding-bottom: var(--sab, 0px);">
                <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
                    <a href="{{ route('dashboard') }}"
                       class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('dashboard') ? 'text-green-500' : 'text-gray-400 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-[10px] mt-1 font-medium">Home</span>
                    </a>

                    <a href="{{ route('jam.index') }}"
                       class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('jam.*') ? 'text-green-500' : 'text-gray-400 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        <span class="text-[10px] mt-1 font-medium">Jams</span>
                    </a>

                    <!-- Center Action Button -->
                    <a href="{{ route('jam.create') }}"
                       class="flex items-center justify-center w-14 h-14 -mt-6 rounded-full bg-green-500 hover:bg-green-400 text-gray-900 shadow-lg shadow-green-500/30 transition-all active:scale-95">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>

                    <a href="#"
                       class="flex flex-col items-center justify-center w-16 h-full text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-[10px] mt-1 font-medium">Search</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('profile.*') ? 'text-green-500' : 'text-gray-400 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-[10px] mt-1 font-medium">Profile</span>
                    </a>
                </div>
            </nav>
            @endauth
        </div>

        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('SW registered'))
                        .catch(err => console.log('SW registration failed:', err));
                });
            }
        </script>
    </body>
</html>
