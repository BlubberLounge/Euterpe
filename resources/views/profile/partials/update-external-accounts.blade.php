<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('External Accounts') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's linked external music streaming accounts") }}
        </p>
    </header>

    @php
        use App\Enums\ExternalServices;
    @endphp

    @foreach (\App\Enums\ExternalServices::cases() as $service)
        @php $isLinked = in_array($service->value, $linkedServices); @endphp

        <div class="flex items-center justify-between bg-gray-100 p-3 rounded-xl">
            <span class="font-semibold">{{ ucfirst($service->value) }}</span>

            @if ($isLinked)
                <span class="text-green-600 font-medium">
                    Linked
                </span>
            @else
                {{-- <a href="{{ route("auth.{$service->value}.redirect") }}" class="text-blue-600 font-medium hover:underline">
                    Connect
                </a> --}}
                <a href="{{ route('auth.spotify.redirect') }}" class="text-black bg-[#1ED760] hover:bg-[#1fbb55] focus:ring-4 focus:outline-none focus:ring-[#3b5998]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#3b5998]/55 me-2 mb-2">
                    <img src="{{ asset('assets/Primary_Logo_Black_RGB.svg') }}" class="w-7 me-2 -ms-1">
                    Sign in with Spotify
                </a>
            @endif
        </div>
    @endforeach

    <div class="flex items-center mt-6">

        <a href="{{ route('auth.spotify.redirect') }}" class="me-2 mb-2">
            <img src="{{ asset('assets/btn-connect-sc-l.png') }}" class="">
        </a>
    </div>
</section>
