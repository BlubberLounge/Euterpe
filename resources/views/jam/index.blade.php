<x-app-layout>
    <x-slot name="title">My Jams</x-slot>

    <div class="px-4 py-6 space-y-6">
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabs -->
        <div class="flex bg-gray-800 rounded-xl p-1">
            <button id="tab-hosted" class="flex-1 py-2.5 text-sm font-medium rounded-lg bg-gray-700 text-white transition-colors" onclick="showTab('hosted')">
                Hosting
            </button>
            <button id="tab-joined" class="flex-1 py-2.5 text-sm font-medium rounded-lg text-gray-400 transition-colors" onclick="showTab('joined')">
                Joined
            </button>
        </div>

        <!-- Hosted Jams -->
        <div id="hosted-jams" class="space-y-3">
            @forelse($hostedJams as $jam)
                <a href="{{ route('jam.show', $jam) }}"
                   class="block bg-gray-800 rounded-xl p-4 border border-gray-700 active:bg-gray-750 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if($jam->is_active)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span class="text-green-500 text-xs font-medium">Active</span>
                                @else
                                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                    <span class="text-gray-500 text-xs font-medium">Ended</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-white truncate">{{ $jam->name }}</h3>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ $jam->users->count() }} {{ Str::plural('member', $jam->users->count()) }}
                                · Code: <span class="font-mono">{{ $jam->join_code }}</span>
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-400 mb-2">No jams yet</h3>
                    <p class="text-gray-500 text-sm mb-6">Start your first jam and invite friends!</p>
                    <a href="{{ route('jam.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-400 text-gray-900 font-semibold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Start a Jam
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Joined Jams -->
        <div id="joined-jams" class="space-y-3 hidden">
            @forelse($joinedJams as $jam)
                <a href="{{ route('jam.show', $jam) }}"
                   class="block bg-gray-800 rounded-xl p-4 border border-gray-700 active:bg-gray-750 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if($jam->is_active)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span class="text-green-500 text-xs font-medium">Active</span>
                                @else
                                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                    <span class="text-gray-500 text-xs font-medium">Ended</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-white truncate">{{ $jam->name }}</h3>
                            <p class="text-sm text-gray-400 mt-1">
                                Hosted by {{ $jam->creator->name }}
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-400 mb-2">No jams joined</h3>
                    <p class="text-gray-500 text-sm">Ask a friend for their jam code!</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function showTab(tab) {
            const hostedBtn = document.getElementById('tab-hosted');
            const joinedBtn = document.getElementById('tab-joined');
            const hostedJams = document.getElementById('hosted-jams');
            const joinedJams = document.getElementById('joined-jams');

            if (tab === 'hosted') {
                hostedBtn.classList.add('bg-gray-700', 'text-white');
                hostedBtn.classList.remove('text-gray-400');
                joinedBtn.classList.remove('bg-gray-700', 'text-white');
                joinedBtn.classList.add('text-gray-400');
                hostedJams.classList.remove('hidden');
                joinedJams.classList.add('hidden');
            } else {
                joinedBtn.classList.add('bg-gray-700', 'text-white');
                joinedBtn.classList.remove('text-gray-400');
                hostedBtn.classList.remove('bg-gray-700', 'text-white');
                hostedBtn.classList.add('text-gray-400');
                joinedJams.classList.remove('hidden');
                hostedJams.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
