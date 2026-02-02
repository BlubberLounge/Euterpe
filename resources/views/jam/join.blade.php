<x-guest-layout>
    <div class="text-center">
        <!-- Jam Icon -->
        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
        </div>

        <h1 class="text-xl font-bold text-white mb-1">Join "{{ $jam->name }}"</h1>
        <p class="text-gray-400 text-sm mb-6">Hosted by {{ $jam->creator->name }}</p>

        @auth
            <p class="text-gray-400 text-sm mb-6">You're about to join this Jam session and add songs to the queue.</p>
            <form action="{{ route('jam.join.submit', $jam->join_code) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3.5 bg-green-500 hover:bg-green-400 text-gray-900 font-semibold rounded-xl transition-colors active:scale-[0.98]">
                    Join Jam
                </button>
            </form>
        @else
            <p class="text-gray-400 text-sm mb-6">Sign in or create an account to join this Jam.</p>
            <div class="space-y-3">
                <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                   class="block w-full py-3.5 bg-green-500 hover:bg-green-400 text-gray-900 font-semibold rounded-xl text-center transition-colors active:scale-[0.98]">
                    Sign In
                </a>
                <a href="{{ route('register', ['redirect' => url()->current()]) }}"
                   class="block w-full py-3.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-center transition-colors active:scale-[0.98]">
                    Create Account
                </a>
            </div>
        @endauth
    </div>
</x-guest-layout>
