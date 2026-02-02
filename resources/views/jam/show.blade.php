@push('scripts')
<script>
window.jamId = {{ $jam->id }};
window.isHost = {{ $isHost ? 'true' : 'false' }};
@if($isHost && isset($accessToken))
window.accessToken = '{{ $accessToken }}';

// Define callback BEFORE loading SDK
window.onSpotifyWebPlaybackSDKReady = () => {
    const player = new Spotify.Player({
        name: 'Euterpe Jam',
        getOAuthToken: cb => { cb(window.accessToken); },
        volume: 0.5
    });

    player.addListener('ready', ({ device_id }) => {
        console.log('Spotify Player Ready. Device ID:', device_id);
        window.spotifyDeviceId = device_id;
    });

    player.addListener('not_ready', ({ device_id }) => {
        console.log('Device has gone offline:', device_id);
    });

    player.addListener('initialization_error', ({ message }) => {
        console.error('Initialization Error:', message);
    });

    player.addListener('authentication_error', ({ message }) => {
        console.error('Authentication Error:', message);
    });

    player.addListener('account_error', ({ message }) => {
        console.error('Account Error:', message);
    });

    player.connect().then(success => {
        if (success) {
            console.log('Successfully connected to Spotify!');
        }
    });

    window.spotifyPlayer = player;
};
@endif
</script>
@if($isHost && isset($accessToken))
<script src="https://sdk.scdn.co/spotify-player.js"></script>
@endif
@endpush

<x-app-layout>
    <x-slot name="title">{{ $jam->name }}</x-slot>

    <x-slot name="headerLeft">
        <a href="{{ route('jam.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    </x-slot>

    <x-slot name="headerRight">
        @if($isHost)
            <span class="px-2.5 py-1 bg-green-500/20 text-green-500 text-xs font-medium rounded-full">Host</span>
        @else
            <span class="px-2.5 py-1 bg-blue-500/20 text-blue-500 text-xs font-medium rounded-full">Guest</span>
        @endif
    </x-slot>

    <div class="px-4 py-4 space-y-4">
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

        <!-- Jam Info Bar -->
        <div class="flex items-center justify-between bg-gray-800/50 rounded-xl p-3 border border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Hosted by</p>
                    <p class="font-medium text-white">{{ $jam->creator->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">Join Code</p>
                <p class="font-mono font-bold text-green-500 tracking-wider">{{ $jam->join_code }}</p>
            </div>
        </div>

        <!-- Now Playing -->
        @if($currentTrack)
        <div class="bg-gradient-to-r from-green-900/50 to-gray-800 rounded-2xl p-4 border border-green-500/20">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-green-500 text-xs font-medium uppercase tracking-wider">Now Playing</span>
            </div>
            <div class="flex items-center gap-4">
                @if($currentTrack->album_image_url)
                    <img src="{{ $currentTrack->album_image_url }}" class="w-20 h-20 rounded-xl shadow-lg" alt="Album cover">
                @else
                    <div class="w-20 h-20 rounded-xl bg-gray-700 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-white text-lg truncate">{{ $currentTrack->track_name }}</h3>
                    <p class="text-gray-400 truncate">{{ $currentTrack->artist_name }}</p>
                    <p class="text-xs text-gray-500 mt-1">Added by {{ $currentTrack->addedBy->name }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Tab Navigation -->
        <div class="flex bg-gray-800 rounded-xl p-1 sticky top-16 z-30">
            <button id="tab-queue" class="flex-1 py-2.5 text-sm font-medium rounded-lg bg-gray-700 text-white transition-colors flex items-center justify-center gap-2" onclick="showJamTab('queue')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Queue
                <span class="bg-gray-600 text-gray-300 text-xs px-1.5 py-0.5 rounded-full">{{ $upcomingTracks->count() }}</span>
            </button>
            <button id="tab-search" class="flex-1 py-2.5 text-sm font-medium rounded-lg text-gray-400 transition-colors flex items-center justify-center gap-2" onclick="showJamTab('search')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Add Song
            </button>
            @if($isHost)
            <button id="tab-share" class="flex-1 py-2.5 text-sm font-medium rounded-lg text-gray-400 transition-colors flex items-center justify-center gap-2" onclick="showJamTab('share')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                Share
            </button>
            @endif
        </div>

        <!-- Queue Tab Content -->
        <div id="content-queue" class="space-y-3">
            <div id="queue-list" class="space-y-2">
                @forelse($upcomingTracks as $track)
                    @include('jam.partials.queue-item', ['track' => $track])
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-400 mb-2">Queue is empty</h3>
                        <p class="text-gray-500 text-sm mb-4">Search and add songs to get the party started!</p>
                        <button onclick="showJamTab('search')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-400 text-gray-900 font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add a Song
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Search Tab Content -->
        <div id="content-search" class="hidden space-y-4">
            @include('jam.partials.search-form')
            <div id="search-results" class="space-y-2"></div>
        </div>

        <!-- Share Tab Content (Host only) -->
        @if($isHost)
        <div id="content-share" class="hidden space-y-4">
            <!-- QR Code -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 text-center">
                <h3 class="font-semibold text-white mb-4">Scan to Join</h3>
                @include('jam.partials.qr-code', ['jam' => $jam])
                <div class="mt-4 space-y-2">
                    <p class="text-gray-400 text-sm">Or share the code</p>
                    <div class="inline-flex items-center gap-2 bg-gray-900 rounded-xl px-4 py-3">
                        <span class="font-mono font-bold text-2xl text-green-500 tracking-widest">{{ $jam->join_code }}</span>
                        <button onclick="copyJoinCode()" class="p-2 text-gray-400 hover:text-white transition-colors" title="Copy code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 break-all">{{ $jam->getJoinUrl() }}</p>
                </div>
            </div>

            <!-- Members List -->
            <div class="bg-gray-800 rounded-2xl p-4 border border-gray-700">
                <h3 class="font-semibold text-white mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Members ({{ $jam->users->count() }})
                </h3>
                <ul class="space-y-2">
                    @foreach($jam->users as $member)
                    <li class="flex items-center gap-3 p-2 rounded-lg {{ $member->pivot->role === 'host' ? 'bg-green-500/10' : '' }}">
                        <div class="w-8 h-8 rounded-full {{ $member->pivot->role === 'host' ? 'bg-green-500' : 'bg-gray-600' }} flex items-center justify-center text-sm font-medium {{ $member->pivot->role === 'host' ? 'text-gray-900' : 'text-white' }}">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <span class="text-white flex-1">{{ $member->name }}</span>
                        @if($member->pivot->role === 'host')
                            <span class="text-xs text-green-500 font-medium">Host</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- End Jam Button -->
            <form action="{{ route('jam.destroy', $jam) }}" method="POST" onsubmit="return confirm('Are you sure you want to end this Jam? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 font-medium rounded-xl border border-red-500/30 transition-colors">
                    End Jam
                </button>
            </form>
        </div>
        @endif

        <!-- Guest: Members & Leave (shown below queue) -->
        @if(!$isHost)
        <div class="space-y-4 mt-6 pt-6 border-t border-gray-800">
            <!-- Members Preview -->
            <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-medium text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        {{ $jam->users->count() }} {{ Str::plural('member', $jam->users->count()) }}
                    </h3>
                </div>
                <div class="flex -space-x-2">
                    @foreach($jam->users->take(5) as $member)
                    <div class="w-8 h-8 rounded-full {{ $member->pivot->role === 'host' ? 'bg-green-500 text-gray-900' : 'bg-gray-600 text-white' }} flex items-center justify-center text-xs font-medium border-2 border-gray-800" title="{{ $member->name }}">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    @endforeach
                    @if($jam->users->count() > 5)
                    <div class="w-8 h-8 rounded-full bg-gray-700 text-gray-400 flex items-center justify-center text-xs font-medium border-2 border-gray-800">
                        +{{ $jam->users->count() - 5 }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Leave Jam Button -->
            <form action="{{ route('jam.leave', $jam) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 bg-gray-800 hover:bg-gray-700 text-gray-400 font-medium rounded-xl border border-gray-700 transition-colors">
                    Leave Jam
                </button>
            </form>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
    // Tab switching
    function showJamTab(tab) {
        const tabs = ['queue', 'search', 'share'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            const content = document.getElementById(`content-${t}`);
            if (btn && content) {
                if (t === tab) {
                    btn.classList.add('bg-gray-700', 'text-white');
                    btn.classList.remove('text-gray-400');
                    content.classList.remove('hidden');
                } else {
                    btn.classList.remove('bg-gray-700', 'text-white');
                    btn.classList.add('text-gray-400');
                    content.classList.add('hidden');
                }
            }
        });
    }

    // Copy join code
    function copyJoinCode() {
        navigator.clipboard.writeText('{{ $jam->join_code }}').then(() => {
            // Show brief feedback
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            setTimeout(() => { btn.innerHTML = originalHTML; }, 1500);
        });
    }

    // Search functionality
    console.log('Search script loaded');
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded fired for search');
        const jamId = window.jamId;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.content : null;

        console.log('Jam Search initialized - jamId:', jamId, 'csrfToken:', csrfToken ? 'found' : 'MISSING');

        // Elements
        const searchInput = document.getElementById('jam-search-input');
        const searchResults = document.getElementById('search-results');
        const searchSpinner = document.getElementById('search-spinner');
        const searchClear = document.getElementById('search-clear');
        const filterButtons = document.querySelectorAll('.search-filter-btn');

        console.log('Elements found - searchInput:', !!searchInput, 'searchResults:', !!searchResults);

        if (!searchInput) {
            console.error('Search input not found!');
            return;
        }

        if (!csrfToken) {
            console.error('CSRF token not found!');
            return;
        }

        // Current filter state
        let currentFilter = 'all';
        let lastQuery = '';

        // Debounce helper
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Format duration
        function formatDuration(ms) {
            if (!ms) return '';
            const minutes = Math.floor(ms / 60000);
            const seconds = Math.floor((ms % 60000) / 1000);
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Format follower count
        function formatFollowers(count) {
            if (!count) return '';
            if (count >= 1000000) return (count / 1000000).toFixed(1) + 'M';
            if (count >= 1000) return (count / 1000).toFixed(1) + 'K';
            return count.toString();
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Search function
        async function performSearch(query, filter = currentFilter) {
            if (!query || query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            lastQuery = query;
            console.log('Searching for:', query, 'filter:', filter);

            // Show spinner
            if (searchSpinner) searchSpinner.classList.remove('hidden');
            if (searchClear) searchClear.classList.add('hidden');

            try {
                const response = await fetch(`/jam/${jamId}/search`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ q: query, type: filter })
                });

                console.log('Search response status:', response.status);
                const data = await response.json();
                console.log('Search data:', data);

                // Hide spinner, show clear
                if (searchSpinner) searchSpinner.classList.add('hidden');
                if (searchClear && query) searchClear.classList.remove('hidden');

                if (!response.ok) {
                    console.error('Search error:', data);
                    searchResults.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-red-400 text-sm">${data.message || data.error || 'Search failed'}</p>
                        </div>`;
                    return;
                }

                if (data.error) {
                    searchResults.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-red-400 text-sm">${data.error}</p>
                        </div>`;
                    return;
                }

                const tracks = data.tracks || [];
                const artists = data.artists || [];
                const totalResults = tracks.length + artists.length;

                if (totalResults === 0) {
                    searchResults.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 text-sm">No results for "${escapeHtml(query)}"</p>
                        </div>`;
                    return;
                }

                let html = '';

                // Show artists section if we have artists and filter allows
                if (artists.length > 0 && (filter === 'all' || filter === 'artist')) {
                    html += `
                        <div class="mb-4">
                            ${filter === 'all' ? '<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Artists</h4>' : ''}
                            <div class="space-y-2">
                                ${artists.slice(0, filter === 'all' ? 3 : 10).map(artist => `
                                    <div class="artist-item group flex items-center p-3 bg-gray-800 hover:bg-gray-750 rounded-xl transition-colors cursor-pointer border border-gray-700"
                                         data-artist-id="${artist.id}"
                                         data-artist-name="${escapeHtml(artist.name)}">
                                        <div class="relative flex-shrink-0">
                                            <img src="${artist.images?.[0]?.url || ''}"
                                                 class="w-12 h-12 rounded-full object-cover"
                                                 alt="${escapeHtml(artist.name)}"
                                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%236b7280%22><path d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/></svg>'">
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <p class="font-medium text-white truncate text-sm">${escapeHtml(artist.name)}</p>
                                            <p class="text-xs text-gray-500">
                                                ${artist.followers?.total ? formatFollowers(artist.followers.total) + ' followers' : 'Artist'}
                                            </p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-600 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                // Show tracks section
                if (tracks.length > 0 && (filter === 'all' || filter === 'track')) {
                    html += `
                        <div>
                            ${filter === 'all' && artists.length > 0 ? '<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tracks</h4>' : ''}
                            <div class="space-y-2">
                                ${tracks.map(track => `
                                    <div class="group flex items-center p-3 bg-gray-800 hover:bg-gray-750 rounded-xl transition-colors border border-gray-700">
                                        <div class="relative flex-shrink-0">
                                            <img src="${track.album?.images?.[0]?.url || ''}"
                                                 class="w-12 h-12 rounded-lg"
                                                 alt="${escapeHtml(track.album?.name || '')}"
                                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%236b7280%22><path d=%22M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z%22/></svg>'">
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <p class="font-medium text-white truncate text-sm">${escapeHtml(track.name)}</p>
                                            <p class="text-xs text-gray-400 truncate">${track.artists?.map(a => escapeHtml(a.name)).join(', ')}</p>
                                        </div>
                                        <button
                                            class="add-to-queue-btn ml-2 w-10 h-10 bg-green-500 hover:bg-green-400 text-gray-900 rounded-full flex items-center justify-center flex-shrink-0 transition-all active:scale-95"
                                            data-uri="${track.uri}"
                                            data-name="${escapeHtml(track.name)}"
                                            data-artist="${escapeHtml(track.artists?.map(a => a.name).join(', '))}"
                                            data-album="${escapeHtml(track.album?.name || '')}"
                                            data-image="${track.album?.images?.[0]?.url || ''}"
                                            data-duration="${track.duration_ms || 0}"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                searchResults.innerHTML = html;
                attachAddHandlers();
                attachArtistClickHandlers();

            } catch (error) {
                console.error('Search exception:', error);
                if (searchSpinner) searchSpinner.classList.add('hidden');
                searchResults.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-400 text-sm">Error searching. Please try again.</p>
                    </div>`;
            }
        }

        // Fetch and display artist's top tracks
        async function showArtistTopTracks(artistId, artistName) {
            if (searchSpinner) searchSpinner.classList.remove('hidden');

            try {
                const response = await fetch(`/jam/${jamId}/artist/${artistId}/top-tracks`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const tracks = await response.json();
                if (searchSpinner) searchSpinner.classList.add('hidden');

                if (!tracks || tracks.length === 0) {
                    searchResults.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No top tracks found for this artist.</p>
                            <button class="back-to-search mt-2 text-green-500 hover:text-green-400 text-sm font-medium">← Back to search</button>
                        </div>`;
                    attachBackHandler();
                    return;
                }

                searchResults.innerHTML = `
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-white">${escapeHtml(artistName)}'s Top Tracks</h4>
                            <button class="back-to-search text-green-500 hover:text-green-400 text-xs font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back
                            </button>
                        </div>
                        <div class="space-y-2">
                            ${tracks.map((track, index) => `
                                <div class="group flex items-center p-3 bg-gray-800 hover:bg-gray-750 rounded-xl transition-colors border border-gray-700">
                                    <span class="w-6 text-center text-xs text-gray-500 font-medium">${index + 1}</span>
                                    <div class="relative flex-shrink-0 ml-2">
                                        <img src="${track.album?.images?.[0]?.url || ''}"
                                             class="w-10 h-10 rounded-lg"
                                             alt="${escapeHtml(track.album?.name || '')}"
                                             onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%236b7280%22><path d=%22M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z%22/></svg>'">
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="font-medium text-white truncate text-sm">${escapeHtml(track.name)}</p>
                                        <p class="text-xs text-gray-500 truncate">${escapeHtml(track.album?.name || '')}</p>
                                    </div>
                                    <button
                                        class="add-to-queue-btn ml-2 w-9 h-9 bg-green-500 hover:bg-green-400 text-gray-900 rounded-full flex items-center justify-center flex-shrink-0 transition-all active:scale-95"
                                        data-uri="${track.uri}"
                                        data-name="${escapeHtml(track.name)}"
                                        data-artist="${escapeHtml(track.artists?.map(a => a.name).join(', '))}"
                                        data-album="${escapeHtml(track.album?.name || '')}"
                                        data-image="${track.album?.images?.[0]?.url || ''}"
                                        data-duration="${track.duration_ms || 0}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
                attachAddHandlers();
                attachBackHandler();
            } catch (error) {
                if (searchSpinner) searchSpinner.classList.add('hidden');
                searchResults.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-400 text-sm">Error loading artist tracks.</p>
                        <button class="back-to-search mt-2 text-green-500 hover:text-green-400 text-sm font-medium">← Back to search</button>
                    </div>`;
                attachBackHandler();
            }
        }

        // Attach click handlers to artist items
        function attachArtistClickHandlers() {
            document.querySelectorAll('.artist-item').forEach(item => {
                item.addEventListener('click', function() {
                    const artistId = this.dataset.artistId;
                    const artistName = this.dataset.artistName;
                    showArtistTopTracks(artistId, artistName);
                });
            });
        }

        // Attach back to search handler
        function attachBackHandler() {
            document.querySelectorAll('.back-to-search').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (lastQuery) {
                        performSearch(lastQuery);
                    } else {
                        searchResults.innerHTML = '';
                    }
                });
            });
        }

        // Debounced search (300ms delay)
        const debouncedSearch = debounce(performSearch, 300);

        // Filter button handling
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                filterButtons.forEach(b => {
                    b.classList.remove('bg-gray-700', 'text-white', 'shadow-sm');
                    b.classList.add('text-gray-400');
                });
                this.classList.add('bg-gray-700', 'text-white', 'shadow-sm');
                this.classList.remove('text-gray-400');

                // Update filter and re-search
                currentFilter = this.dataset.filter;
                const query = searchInput?.value.trim();
                if (query && query.length >= 2) {
                    performSearch(query, currentFilter);
                }
            });
        });

        // Search as you type
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                if (query.length >= 2) {
                    debouncedSearch(query);
                } else {
                    searchResults.innerHTML = '';
                    if (searchClear) searchClear.classList.add('hidden');
                }
            });

            // Also search on Enter
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        performSearch(query);
                    }
                }
            });
        }

        // Clear search
        if (searchClear) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchResults.innerHTML = '';
                this.classList.add('hidden');
                searchInput.focus();
                lastQuery = '';
            });
        }

        // Add to queue handler
        function attachAddHandlers() {
            document.querySelectorAll('.add-to-queue-btn').forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.stopPropagation();
                    const button = this;
                    const originalContent = button.innerHTML;

                    button.disabled = true;
                    button.innerHTML = `
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    `;

                    const payload = {
                        spotify_uri: button.dataset.uri,
                        track_name: button.dataset.name,
                        artist_name: button.dataset.artist,
                        album_name: button.dataset.album || null,
                        album_image_url: button.dataset.image || null,
                        duration_ms: parseInt(button.dataset.duration) || null
                    };

                    console.log('Adding to queue:', payload);

                    try {
                        const response = await fetch(`/jam/${jamId}/queue`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();
                        console.log('Queue response:', response.status, result);

                        if (response.ok && result.success) {
                            button.innerHTML = `
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            `;
                            button.classList.remove('bg-green-500', 'hover:bg-green-400');
                            button.classList.add('bg-gray-600', 'cursor-not-allowed');

                            // Update queue display
                            updateQueueDisplay(result.track);
                        } else {
                            if (result.errors) {
                                console.error('Validation errors:', result.errors);
                            }
                            if (result.message) {
                                console.error('Error message:', result.message);
                            }

                            button.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                            button.classList.remove('bg-green-500');
                            button.classList.add('bg-red-500');
                            setTimeout(() => {
                                button.innerHTML = originalContent;
                                button.classList.remove('bg-red-500');
                                button.classList.add('bg-green-500');
                                button.disabled = false;
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Add to queue error:', error);
                        button.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                        button.classList.remove('bg-green-500');
                        button.classList.add('bg-red-500');
                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.classList.remove('bg-red-500');
                            button.classList.add('bg-green-500');
                            button.disabled = false;
                        }, 2000);
                    }
                });
            });
        }

        // Update queue display without reload
        function updateQueueDisplay(track) {
            const queueList = document.getElementById('queue-list');
            if (!queueList) return;

            // Remove empty state if present
            const emptyState = queueList.querySelector('.text-center.py-12');
            if (emptyState) {
                emptyState.remove();
            }

            // Add new track to queue
            const trackHtml = `
                <div class="flex items-center p-3 bg-gray-800 rounded-xl border border-gray-700 animate-pulse-once">
                    ${track.album_image_url
                        ? `<img src="${track.album_image_url}" class="w-12 h-12 rounded-lg" alt="Album cover">`
                        : `<div class="w-12 h-12 rounded-lg bg-gray-700 flex items-center justify-center"><svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg></div>`
                    }
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="font-medium text-white truncate">${escapeHtml(track.track_name)}</p>
                        <p class="text-sm text-gray-400 truncate">${escapeHtml(track.artist_name)}</p>
                        <p class="text-xs text-gray-500">Just added</p>
                    </div>
                </div>
            `;
            queueList.insertAdjacentHTML('beforeend', trackHtml);

            // Update queue count in tab
            const queueTab = document.getElementById('tab-queue');
            if (queueTab) {
                const countSpan = queueTab.querySelector('span');
                if (countSpan) {
                    const currentCount = parseInt(countSpan.textContent) || 0;
                    countSpan.textContent = currentCount + 1;
                }
            }
        }

        // Host: Push to Spotify functionality
        if (window.isHost) {
            document.querySelectorAll('.push-to-spotify-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const trackId = this.dataset.trackId;
                    const button = this;
                    const originalHTML = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

                    try {
                        const response = await fetch(`/jam/${jamId}/queue/${trackId}/push-spotify`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ device_id: window.spotifyDeviceId || null })
                        });

                        const result = await response.json();
                        console.log('Push to Spotify result:', result);

                        if (response.ok && result.success) {
                            button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                            button.classList.remove('bg-green-500', 'hover:bg-green-400');
                            button.classList.add('bg-gray-600');
                        } else {
                            // Show error to user
                            const errorMsg = result.error || 'Failed to add to Spotify';
                            console.error('Spotify queue error:', errorMsg);
                            alert('Spotify Error: ' + errorMsg + '\n\nMake sure Spotify is open and playing on a device.');
                            button.innerHTML = originalHTML;
                            button.disabled = false;
                        }
                    } catch (error) {
                        console.error('Error pushing to Spotify:', error);
                        alert('Network error. Please try again.');
                        button.innerHTML = originalHTML;
                        button.disabled = false;
                    }
                });
            });
        }
    });
    </script>
    <style>
    @keyframes pulse-once {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; background-color: rgb(34 197 94 / 0.2); }
    }
    .animate-pulse-once {
        animation: pulse-once 0.5s ease-in-out;
    }
    .bg-gray-750 {
        background-color: rgb(55, 65, 81);
    }
    </style>
    @endpush
</x-app-layout>
