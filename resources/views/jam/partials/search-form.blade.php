<div class="space-y-3">
    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            id="jam-search-input"
            name="q"
            placeholder="Search songs, artists..."
            class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-10 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm transition-colors"
            autocomplete="off"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <div id="search-spinner" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <button
            type="button"
            id="search-clear"
            class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-white transition-colors"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-1 bg-gray-800 rounded-xl p-1">
        <button
            type="button"
            id="filter-all"
            class="search-filter-btn flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors bg-gray-700 text-white shadow-sm"
            data-filter="all"
        >
            All
        </button>
        <button
            type="button"
            id="filter-tracks"
            class="search-filter-btn flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-400 hover:bg-gray-700"
            data-filter="track"
        >
            <span class="flex items-center justify-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                Tracks
            </span>
        </button>
        <button
            type="button"
            id="filter-artists"
            class="search-filter-btn flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-400 hover:bg-gray-700"
            data-filter="artist"
        >
            <span class="flex items-center justify-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Artists
            </span>
        </button>
    </div>

    <!-- Search Tips -->
    <p class="text-xs text-gray-500">
        Try <code class="bg-gray-800 px-1.5 py-0.5 rounded text-gray-400">artist:Coldplay</code> or <code class="bg-gray-800 px-1.5 py-0.5 rounded text-gray-400">track:Yellow</code>
    </p>
</div>
