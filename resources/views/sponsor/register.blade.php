<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Register as a Sponsor
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-6 text-gray-600">
                    By registering as a sponsor, you can support our church and participate in our raffle events.
                </p>

                @if (session('success'))
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                {{-- Step 1: Registration form --}}
                <form method="POST" action="{{ route('sponsor.register.store') }}">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <x-input-label for="title" value="Title" />
                        <select name="title" id="title"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                            <option value="" disabled @selected(!old('title'))>— select title —</option>
                            <option value="Mr"  @selected(old('title') === 'Mr')>Mr</option>
                            <option value="Mrs" @selected(old('title') === 'Mrs')>Mrs</option>
                            <option value="Ms"  @selected(old('title') === 'Ms')>Ms</option>
                        </select>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- First name --}}
                    <div class="mb-4">
                        <x-input-label for="first_name" value="First Name" />
                        <x-text-input id="first_name" name="first_name" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('first_name')" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    {{-- Last name --}}
                    <div class="mb-4">
                        <x-input-label for="last_name" value="Last Name" />
                        <x-text-input id="last_name" name="last_name" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <x-input-label for="email" value="Email Address" />
                        <x-text-input id="email" name="email" type="email"
                                      class="mt-1 block w-full"
                                      :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4">
                        <x-input-label for="phone" value="Phone Number" />
                        <x-text-input id="phone" name="phone" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('phone')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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
                        Enter it below to complete your registration.
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
                            Verify and Continue
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
                    Already registered?
                    <a href="{{ route('sponsor.login') }}" class="text-indigo-600 hover:underline">Log in here</a>.
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