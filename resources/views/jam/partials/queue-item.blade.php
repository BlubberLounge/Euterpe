<div class="flex items-center p-3 bg-gray-800 rounded-xl border border-gray-700">
    <div class="flex items-center flex-1 min-w-0">
        @if($track->album_image_url)
            <img src="{{ $track->album_image_url }}" class="w-12 h-12 rounded-lg flex-shrink-0" alt="Album cover">
        @else
            <div class="w-12 h-12 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                </svg>
            </div>
        @endif
        <div class="ml-3 min-w-0 flex-1">
            <p class="font-medium text-white truncate">{{ $track->track_name }}</p>
            <p class="text-sm text-gray-400 truncate">{{ $track->artist_name }}</p>
            <p class="text-xs text-gray-500">Added by {{ $track->addedBy->name }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 ml-2 flex-shrink-0">
        @if($isHost ?? false)
            <button
                class="push-to-spotify-btn w-9 h-9 bg-green-500 hover:bg-green-400 text-gray-900 rounded-full flex items-center justify-center transition-all active:scale-95"
                data-track-id="{{ $track->id }}"
                title="Add to Spotify queue"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        @endif
        @if(($isHost ?? false) || $track->added_by_user_id === auth()->id())
            <form action="{{ route('jam.queue.destroy', [$jam ?? $track->jam, $track]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-full flex items-center justify-center transition-colors" title="Remove from queue">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        @endif
    </div>
</div>
