<x-app-layout>
    <x-slot name="title">Edit Jam</x-slot>

    <x-slot name="headerLeft">
        <a href="{{ route('jam.show', $jam) }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    </x-slot>

    <div class="px-4 py-6">
        <div class="max-w-lg mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Edit Jam</h1>
                <p class="text-gray-400 mt-2">Update your jam settings</p>
            </div>

            <!-- Form -->
            <form action="{{ route('jam.update', $jam) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Jam Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $jam->name) }}"
                        required
                        autofocus
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Join Code (read-only info) -->
                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700">
                    <label class="block text-sm font-medium text-gray-400 mb-1">Join Code</label>
                    <p class="font-mono font-bold text-lg text-green-500 tracking-wider">{{ $jam->join_code }}</p>
                    <p class="text-xs text-gray-500 mt-1">This code cannot be changed</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('jam.show', $jam) }}"
                       class="flex-1 py-3.5 bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-xl text-center transition-colors border border-gray-700">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 py-3.5 bg-green-500 hover:bg-green-400 text-gray-900 font-semibold rounded-xl transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
