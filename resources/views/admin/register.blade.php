<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-semibold text-gray-800">Create Admin Account</h2>
        <p class="text-sm text-gray-500 mt-1">Enter your details to get started.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Step 1: Registration form --}}
    <form method="POST" action="{{ route('admin.register.store') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="name" value="Full Name" />
            <x-text-input id="name" name="name" type="text"
                          class="mt-1 block w-full"
                          :value="old('name')"
                          required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" name="email" type="email"
                          class="mt-1 block w-full"
                          :value="old('email')"
                          required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Create Account
        </x-primary-button>
    </form>

    {{-- Step 2: OTP form — appears after code is sent --}}
    <div id="otp-section" style="{{ session('otp_sent') ? '' : 'display:none' }}">
        <hr class="my-6">

        <p class="mb-4 text-sm text-gray-600">
            A 6-digit code was sent to
            <strong>{{ session('otp_email') }}</strong>.
            Enter it below to complete your registration.
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
                              placeholder="000000" required />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                Verify and Continue
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('admin.otp.resend') }}" class="mt-3">
            @csrf
            <input type="hidden" name="email" value="{{ session('otp_email') }}">
            <button type="submit" class="text-sm text-indigo-600 hover:underline">
                Resend code
            </button>
        </form>
    </div>

    <p class="mt-6 text-sm text-gray-500 text-center">
        Already have an account?
        <a href="{{ route('admin.login') }}" class="text-indigo-600 hover:underline">
            Log in here
        </a>.
    </p>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const otp = document.getElementById('otp-section');
            if (otp && otp.style.display !== 'none') {
                otp.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('otp').focus();
            }
        });
    </script>
</x-guest-layout>