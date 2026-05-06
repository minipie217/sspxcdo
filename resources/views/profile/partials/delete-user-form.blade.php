<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    @if (session('success'))
        <div class="p-4 bg-green-50 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Step 1: Request OTP --}}
    <form method="POST" action="{{ route('profile.delete.otp') }}">
        @csrf
        <x-primary-button
            class="bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900">
            {{ __('Send Verification Code') }}
        </x-primary-button>
    </form>

    {{-- Step 2: OTP + confirm delete — appears after code is sent --}}
    <div id="delete-otp-section"
         style="{{ session('delete_otp_sent') ? '' : 'display:none' }}">
        <hr class="my-4">

        <p class="text-sm text-gray-600 mb-4">
            A 6-digit code was sent to <strong>{{ Auth::guard('web')->user()->email }}</strong>.
            Enter it below to permanently delete your account.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div class="mb-4">
                <x-input-label for="otp" value="Verification Code" />
                <x-text-input id="otp" name="otp" type="text"
                              class="mt-1 block w-full tracking-widest text-center text-lg"
                              inputmode="numeric" maxlength="6"
                              autocomplete="one-time-code"
                              placeholder="000000" required />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <x-danger-button>
                {{ __('Permanently Delete Account') }}
            </x-danger-button>
        </form>

        <form method="POST" action="{{ route('profile.delete.otp') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm text-indigo-600 hover:underline">
                Resend code
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const section = document.getElementById('delete-otp-section');
            if (section && section.style.display !== 'none') {
                section.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('otp').focus();
            }
        });
    </script>
</section>