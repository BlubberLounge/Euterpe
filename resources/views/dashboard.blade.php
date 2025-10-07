<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold mb-6 text-green-400">🎧 Aktuelle Spotify Queue</h1>

                @if(isset($error))
                    <div class="bg-red-500 text-white p-3 rounded mb-4">{{ $error }}</div>
                @endif

                @if($currently_playing)
                    <div class="bg-gray-800 p-4 rounded-lg mb-6 shadow-md">
                        <h2 class="text-xl font-semibold text-green-300 mb-2">Läuft gerade</h2>
                        <div class="flex items-center">
                            <img src="{{ $currently_playing['album']['images'][0]['url'] ?? '' }}"
                                alt="Album Cover"
                                class="w-20 h-20 rounded mr-4">
                            <div>
                                <div class="text-lg font-semibold">{{ $currently_playing['name'] }}</div>
                                <div class="text-gray-400">
                                    {{ collect($currently_playing['artists'])->pluck('name')->join(', ') }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $currently_playing['album']['name'] }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <h2 class="text-xl font-semibold mb-4">Als Nächstes in der Queue</h2>
                <div class="space-y-4">
                    @forelse($queue as $track)
                        <div class="flex items-center bg-gray-800 p-3 rounded-lg shadow">
                            <img src="{{ $track['album']['images'][0]['url'] ?? '' }}"
                                alt="Album Cover"
                                class="w-16 h-16 rounded mr-4">
                            <div>
                                <div class="font-semibold">{{ $track['name'] }}</div>
                                <div class="text-gray-400">
                                    {{ collect($track['artists'])->pluck('name')->join(', ') }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $track['album']['name'] }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400">Keine weiteren Songs in der Queue.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
