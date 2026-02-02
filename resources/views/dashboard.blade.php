<x-app-layout>
    <x-slot name="title">Home</x-slot>

    <div class="px-4 py-6 space-y-6">
        <!-- Welcome Section -->
        <div class="text-center py-4">
            <h1 class="text-2xl font-bold text-white">Welcome back!</h1>
            <p class="text-gray-400 mt-1">Ready to make some noise?</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('jam.create') }}"
               class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg shadow-green-500/20 active:scale-95 transition-transform">
                <svg class="w-10 h-10 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-white font-semibold">Start a Jam</span>
                <span class="text-green-100 text-xs mt-1">Host a session</span>
            </a>

            <a href="{{ route('jam.index') }}"
               class="flex flex-col items-center justify-center p-6 bg-gray-800 rounded-2xl border border-gray-700 active:scale-95 transition-transform">
                <svg class="w-10 h-10 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
                <span class="text-white font-semibold">My Jams</span>
                <span class="text-gray-400 text-xs mt-1">View all sessions</span>
            </a>
        </div>

        <!-- Active Jam (if any) -->
        @php
            $activeJam = Auth::user()->hostedJams()->where('is_active', true)->first()
                ?? Auth::user()->jams()->wherePivot('left_at', null)->whereHas('creator', fn($q) => $q->whereHas('hostedJams', fn($q2) => $q2->where('is_active', true)))->first();
        @endphp

        @if($activeJam)
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-4 border border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-green-500 text-sm font-medium">Live Now</span>
                </div>
                <span class="text-gray-500 text-xs">{{ $activeJam->users->count() }} people</span>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">{{ $activeJam->name }}</h3>
            <p class="text-gray-400 text-sm mb-4">Hosted by {{ $activeJam->creator->name }}</p>
            <a href="{{ route('jam.show', $activeJam) }}"
               class="block w-full py-3 bg-green-500 hover:bg-green-400 text-center text-gray-900 font-semibold rounded-xl transition-colors">
                Rejoin Jam
            </a>
        </div>
        @endif

        <!-- Join with Code -->
        <div class="bg-gray-800 rounded-2xl p-4 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-3">Join a Jam</h3>
            <form action="{{ route('jam.index') }}" method="GET" class="flex gap-2">
                <input type="text"
                       name="code"
                       placeholder="Enter 6-digit code"
                       maxlength="6"
                       class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 uppercase tracking-widest text-center font-mono focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <button type="submit"
                        class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-medium transition-colors">
                    Join
                </button>
            </form>
        </div>

        <!-- Recent Jams -->
        @php
            $recentJams = Auth::user()->jams()
                ->with('creator')
                ->orderByPivot('joined_at', 'desc')
                ->take(3)
                ->get();
        @endphp

        @if($recentJams->count() > 0)
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-white">Recent</h3>
                <a href="{{ route('jam.index') }}" class="text-green-500 text-sm font-medium">See all</a>
            </div>
            <div class="space-y-2">
                @foreach($recentJams as $jam)
                <a href="{{ route('jam.show', $jam) }}"
                   class="flex items-center gap-4 p-3 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500/20 to-green-600/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-white truncate">{{ $jam->name }}</h4>
                        <p class="text-sm text-gray-400 truncate">{{ $jam->creator->name }} · {{ $jam->users->count() }} joined</p>
                    </div>
                    <div class="flex items-center">
                        @if($jam->is_active)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        @else
                            <span class="text-gray-500 text-xs">Ended</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- How it Works -->
        <div class="bg-gray-800/50 rounded-2xl p-4">
            <h3 class="text-lg font-semibold text-white mb-4">How Euterpe Works</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-green-500 font-bold text-sm">1</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-white">Start a Jam</h4>
                        <p class="text-sm text-gray-400">Connect your Spotify and create a session</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-green-500 font-bold text-sm">2</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-white">Share the Code</h4>
                        <p class="text-sm text-gray-400">Friends scan QR or enter the 6-digit code</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-green-500 font-bold text-sm">3</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-white">Queue Together</h4>
                        <p class="text-sm text-gray-400">Everyone adds songs, you control the music</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
