<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabs --}}
            <div x-data="{ tab: '{{ session('tab', 'general') }}' }">

                <div class="flex border-b border-gray-200 mb-6">
                    <button @click="tab = 'general'"
                            :class="tab === 'general'
                                ? 'border-b-2 border-indigo-600 text-indigo-600'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-3 text-sm font-medium focus:outline-none">
                        ⚙️ General
                    </button>
                    <button @click="tab = 'payment'"
                            :class="tab === 'payment'
                                ? 'border-b-2 border-indigo-600 text-indigo-600'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-3 text-sm font-medium focus:outline-none">
                        🏦 Payment Accounts
                    </button>
                </div>

                {{-- General Settings --}}
                <div x-show="tab === 'general'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="tab" value="general">

                        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">
                                    Reservation Time
                                </h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    How long a sponsor has to complete payment after reserving a ticket.
                                </p>

                                @php
                                    $reservationSetting = $groups->get('general')
                                        ?->firstWhere('key', 'reservation_minutes');
                                @endphp

                                <div>
                                    <x-input-label
                                        for="reservation_minutes"
                                        value="Reservation Time (minutes)" />
                                    <div class="mt-1 flex items-center gap-3">
                                        <x-text-input
                                            id="reservation_minutes"
                                            name="settings[reservation_minutes]"
                                            type="number"
                                            min="1"
                                            max="1440"
                                            class="block w-40"
                                            :value="old('settings.reservation_minutes', $reservationSetting?->value ?? 30)" />
                                        <span class="text-sm text-gray-500">minutes</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        e.g. 30 = sponsor has 30 minutes to pay after reserving.
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="flex justify-end mt-4">
                            <x-primary-button>Save General Settings</x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Payment Accounts --}}
                <div x-show="tab === 'payment'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="tab" value="payment">

                        <div class="space-y-4">

                            @foreach (['bdo', 'bpi', 'metrobank', 'unionbank'] as $bank)
                                @php
                                    $bankSettings = $groups->get($bank);
                                    $label = strtoupper($bank);
                                @endphp

                                @if ($bankSettings)
                                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                                        <h3 class="font-semibold text-gray-700 mb-4">🏦 {{ $label }}</h3>
                                        <div class="space-y-4">
                                            @foreach ($bankSettings as $setting)
                                                <div>
                                                    <x-input-label
                                                        :for="$setting->key"
                                                        :value="$setting->label" />
                                                    <x-text-input
                                                        :id="$setting->key"
                                                        :name="'settings[' . $setting->key . ']'"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        :value="old('settings.' . $setting->key, $setting->value)" />
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @foreach (['gcash', 'maya'] as $wallet)
                                @php
                                    $walletSettings = $groups->get($wallet);
                                    $label = ucfirst($wallet);
                                @endphp

                                @if ($walletSettings)
                                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                                        <h3 class="font-semibold text-gray-700 mb-4">📱 {{ $label }}</h3>
                                        <div class="space-y-4">
                                            @foreach ($walletSettings as $setting)
                                                <div>
                                                    <x-input-label
                                                        :for="$setting->key"
                                                        :value="$setting->label" />
                                                    <x-text-input
                                                        :id="$setting->key"
                                                        :name="'settings[' . $setting->key . ']'"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        :value="old('settings.' . $setting->key, $setting->value)" />
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @php $otherSettings = $groups->get('other'); @endphp
                            @if ($otherSettings)
                                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                                    <h3 class="font-semibold text-gray-700 mb-4">💳 Other</h3>
                                    <div class="space-y-4">
                                        @foreach ($otherSettings as $setting)
                                            <div>
                                                <x-input-label
                                                    :for="$setting->key"
                                                    :value="$setting->label" />
                                                <x-text-input
                                                    :id="$setting->key"
                                                    :name="'settings[' . $setting->key . ']'"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :value="old('settings.' . $setting->key, $setting->value)" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="flex justify-end mt-4">
                            <x-primary-button>Save Payment Accounts</x-primary-button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>