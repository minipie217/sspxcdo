<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-semibold text-gray-800">Admin Login</h2>
        <p class="text-sm text-gray-500 mt-1">Enter your email to receive a verification code.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Step 1: Email — hidden once OTP is sent --}}
    @unless(session('otp_sent'))
        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="email" value="Email Address" />
                <x-text-input id="email" name="email" type="email"
                              class="mt-1 block w-full"
                              :value="old('email')"
                              required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                Send Verification Code
            </x-primary-button>
        </form>
    @endunless

    {{-- Step 2: OTP — appears after code is sent --}}
    @if(session('otp_sent'))
        <p class="mb-4 text-sm text-gray-600">
            A 6-digit code was sent to
            <strong>{{ session('otp_email') }}</strong>.
        </p>

        <form method="POST" action="{{ route('admin.otp.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('otp_email') }}">

            <div class="mb-4">
                <x-input-label for="otp" value="Verification Code" />
                <x-text-input id="otp" name="otp" type="text"
                              class="mt-1 block w-full tracking-widest text-center text-lg"
                              inputmode="numeric" maxlength="6"
                              autocomplete="one-time-code"
                              placeholder="000000"
                              required autofocus />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                Verify and Log In
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('admin.otp.resend') }}" class="mt-3">
            @csrf
            <input type="hidden" name="email" value="{{ session('otp_email') }}">
            <button type="submit" class="text-sm text-indigo-600 hover:underline">
                Resend code
            </button>
        </form>
    @endif

    <p class="mt-6 text-sm text-gray-500 text-center">
        Don't have an account?
        <a href="{{ route('admin.register') }}" class="text-indigo-600 hover:underline">
            Register here
        </a>.
    </p>
</x-guest-layout>