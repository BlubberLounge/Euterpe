<x-app-layout>
    <x-slot name="title">New Jam</x-slot>

    <x-slot name="headerLeft">
        <a href="{{ route('jam.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    </x-slot>

    <div class="px-4 py-6">
        <div class="max-w-lg mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Start a New Jam</h1>
                <p class="text-gray-400 mt-2">Create a session and invite your friends</p>
            </div>

            <!-- Form -->
            <form action="{{ route('jam.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Jam Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Saturday Night Party"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Card -->
                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700">
                    <h3 class="font-medium text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        What happens next?
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 text-green-500 text-xs font-bold">1</span>
                            <span>You'll get a unique QR code & join code</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 text-green-500 text-xs font-bold">2</span>
                            <span>Friends scan or enter the code to join</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 text-green-500 text-xs font-bold">3</span>
                            <span>Everyone can search and add songs</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 text-green-500 text-xs font-bold">4</span>
                            <span>Music plays on your Spotify device</span>
                        </li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 bg-green-500 hover:bg-green-400 text-gray-900 font-semibold rounded-xl transition-colors active:scale-[0.98]">
                    Create Jam
                </button>

                <p class="text-center text-sm text-gray-500">
                    Make sure Spotify is open on a device
                </p>
            </form>
        </div>
    </div>
</x-app-layout>
