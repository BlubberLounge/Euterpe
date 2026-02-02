@if(empty($tracks))
    <p class="text-gray-500">No results found.</p>
@else
    <ul class="divide-y divide-gray-200">
        @foreach($tracks as $track)
            <li class="flex items-center justify-between py-3">
                <div class="flex items-center space-x-4">
                    @if($track['image'])
                        <img src="{{ $track['image'] }}" alt="{{ $track['name'] }}" class="w-12 h-12 rounded">
                    @endif
                    <div>
                        <p class="font-semibold">{{ $track['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $track['artist'] }} — {{ $track['album'] }}</p>
                    </div>
                </div>
                <button
                    class="add-to-queue bg-green-500 text-white px-3 py-1 rounded"
                    data-uri="{{ $track['uri'] }}">
                    ➕ Add
                </button>
            </li>
        @endforeach
    </ul>
@endif
