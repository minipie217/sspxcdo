<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    @foreach ($groups as $group => $settings)
                        <h3 class="font-semibold text-gray-700 capitalize mb-3 mt-6 first:mt-0">
                            {{ $group === 'general' ? '⚙️ General' : '' }}
                            {{ $group === 'bdo' ? '🏦 BDO' : '' }}
                            {{ $group === 'bpi' ? '🏦 BPI' : '' }}
                            {{ $group === 'metrobank' ? '🏦 Metrobank' : '' }}
                            {{ $group === 'unionbank' ? '🏦 UnionBank' : '' }}
                            {{ $group === 'gcash' ? '📱 GCash' : '' }}
                            {{ $group === 'maya' ? '📱 Maya' : '' }}
                            {{ $group === 'other' ? '💳 Other' : '' }}
                        </h3>

                        <div class="space-y-4 mb-6">
                            @foreach ($settings as $setting)
                                <div>
                                    <x-input-label :for="$setting->key" :value="$setting->label" />
                                    <x-text-input
                                        :id="$setting->key"
                                        :name="'settings[' . $setting->key . ']'"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('settings.' . $setting->key, $setting->value)" />
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">
                    @endforeach

                    <div class="flex justify-end mt-6">
                        <x-primary-button>Save Settings</x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>