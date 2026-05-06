<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sponsor Login
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-6 text-gray-600">
                    Enter your registered email and we'll send you a verification code.
                </p>

                @if (session('success'))
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="mb-4 text-red-600">{{ session('error') }}</div>
                @endif

                {{-- Step 1: Email form --}}
                <form method="POST" action="{{ route('sponsor.login.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="email" value="Email Address" />
                        <x-text-input id="email" name="email" type="email"
                                      class="mt-1 block w-full"
                                      :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center">
                        Send Verification Code
                    </x-primary-button>
                </form>

                {{-- Step 2: OTP form — appears after code is sent --}}
                <div id="otp-section" style="{{ session('otp_sent') ? '' : 'display:none' }}">
                    <hr class="my-6">

                    <p class="mb-4 text-gray-600">
                        A 6-digit code was sent to
                        <strong>{{ session('otp_email') }}</strong>.
                    </p>

                    <form method="POST" action="{{ route('sponsor.otp.verify') }}">
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
                            Verify and Log In
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('sponsor.otp.resend') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('otp_email') }}">
                        <button type="submit" class="text-sm text-indigo-600 hover:underline">
                            Resend code
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-sm text-gray-500 text-center">
                    Not registered yet?
                    <a href="{{ route('sponsor.register') }}" class="text-indigo-600 hover:underline">Register here</a>.
                </p>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const otp = document.getElementById('otp-section');
            if (otp && otp.style.display !== 'none') {
                otp.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('otp').focus();
            }
        });
    </script>
</x-app-layout>

// https://claude.ai/share/56a31b0a-f29b-445a-b0b9-313ae114d17f