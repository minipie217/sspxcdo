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
                    <button @click="tab = 'homepage'"
                            :class="tab === 'homepage'
                                ? 'border-b-2 border-indigo-600 text-indigo-600'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-3 text-sm font-medium focus:outline-none">
                        🧩 Homepage
                    </button>
                </div>

                {{-- General Settings --}}
                <div x-show="tab === 'general'" x-cloak>
                    <form method="POST"
                        action="{{ route('admin.settings.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tab" value="general">

                        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                            {{-- Site Name --}}
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Site Identity</h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    Displayed in the navigation and browser tab.
                                </p>

                                @php
                                    $siteNameSetting = $groups->get('general')?->firstWhere('key', 'site_name');
                                    $siteLogoSetting = $groups->get('general')?->firstWhere('key', 'site_logo');
                                @endphp

                                <div class="mb-4">
                                    <x-input-label for="site_name" value="Website Name" />
                                    <x-text-input id="site_name"
                                                name="settings[site_name]"
                                                type="text"
                                                class="mt-1 block w-full"
                                                :value="old('settings.site_name', $siteNameSetting?->value)" />
                                </div>

                                {{-- Logo upload --}}
                                <div>
                                    <x-input-label value="Site Logo" />

                                    {{-- Current logo preview --}}
                                    @if ($siteLogoSetting?->value)
                                        <div class="mt-2 mb-3 flex items-center gap-4">
                                            <img src="{{ Storage::url($siteLogoSetting->value) }}"
                                                alt="Current logo"
                                                class="h-12 w-auto object-contain border rounded p-1 bg-gray-50" />
                                            <div>
                                                <p class="text-xs text-gray-500">Current logo</p>
                                                <label class="flex items-center gap-2 mt-1 cursor-pointer">
                                                    <input type="checkbox" name="settings[remove_logo]" value="1"
                                                        class="rounded border-gray-300 text-red-600">
                                                    <span class="text-xs text-red-500">Remove logo</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <input type="file"
                                        id="site_logo"
                                        name="site_logo"
                                        accept=".jpg,.jpeg,.png,.svg"
                                        class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4 file:rounded-md
                                                file:border-0 file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100" />
                                    <p class="text-xs text-gray-400 mt-1">
                                        JPG, PNG or SVG. Max 2MB. Recommended height: 40px.
                                    </p>
                                    <x-input-error :messages="$errors->get('site_logo')" class="mt-2" />

                                    {{-- Live preview --}}
                                    <div id="logo-preview-wrapper" class="mt-3 hidden">
                                        <p class="text-xs text-gray-400 mb-1">Preview:</p>
                                        <img id="logo-preview"
                                            src=""
                                            alt="Logo preview"
                                            class="h-12 w-auto object-contain border rounded p-1 bg-gray-50" />
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Reservation Time --}}
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Reservation Time</h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    How long a sponsor has to complete payment after reserving a ticket.
                                </p>

                                @php
                                    $reservationSetting = $groups->get('general')?->firstWhere('key', 'reservation_minutes');
                                @endphp

                                <div>
                                    <x-input-label for="reservation_minutes" value="Reservation Time (minutes)" />
                                    <div class="mt-1 flex items-center gap-3">
                                        <x-text-input id="reservation_minutes"
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

                            <hr>

                            {{-- Recent Updates Limit --}}
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Recent Updates</h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    How many items to show in the Recent Updates feed on the dashboard.
                                </p>

                                @php
                                    $recentUpdatesSetting = $groups->get('general')
                                        ?->firstWhere('key', 'recent_updates_limit');
                                @endphp

                                <div>
                                    <x-input-label for="recent_updates_limit" value="Number of items to show" />
                                    <div class="mt-1 flex items-center gap-3">
                                        <x-text-input id="recent_updates_limit"
                                                    name="settings[recent_updates_limit]"
                                                    type="number"
                                                    min="5"
                                                    max="50"
                                                    class="block w-40"
                                                    :value="old('settings.recent_updates_limit', $recentUpdatesSetting?->value ?? 10)" />
                                        <span class="text-sm text-gray-500">items</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Min 5, max 50.
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

                {{-- Homepage Content --}}
                <div x-show="tab === 'homepage'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tab" value="homepage">

                        @php
                            $homepageSettings = $groups->get('homepage') ?? collect();
                            $heroBackgroundSetting = $homepageSettings->firstWhere('key', 'homepage_hero_background');
                            $settingsByKey = $homepageSettings->keyBy('key');
                            $settingValue = fn (string $key) => old('settings.' . $key, $settingsByKey->get($key)?->value);
                            $settingLabel = fn (string $key, string $fallback) => $settingsByKey->get($key)?->label ?? $fallback;
                        @endphp

                        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Homepage Sections</h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    Edit the public homepage copy without changing Blade files.
                                </p>
                            </div>

                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <h4 class="font-semibold text-gray-700 mb-1">Section Layout</h4>
                                <p class="text-sm text-gray-500 mb-4">
                                    Drag sections to reorder them. Each row's checkbox controls whether that section appears on the homepage. Changes are saved to <code class="font-mono">storage/app/home.json</code>.
                                </p>

                                <div x-data="homepageLayout()" x-init="updateJson()" class="space-y-3">
                                    <input type="hidden" name="home_layout_json" x-ref="layoutJson">

                                    <div x-ref="sections" class="space-y-3">
                                        @foreach (($homeLayout['sections'] ?? []) as $section)
                                            <section draggable="true"
                                                data-section-key="{{ $section['key'] }}"
                                                data-section-label="{{ $section['label'] }}"
                                                @dragstart="dragged = $event.currentTarget"
                                                @dragover.prevent
                                                @drop="move(dragged, $event.currentTarget)"
                                                class="rounded-lg border border-gray-200 bg-white shadow-sm">
                                                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-4 py-3">
                                                    <div class="flex min-w-0 items-center gap-3">
                                                        <button type="button"
                                                            class="cursor-grab rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-xs font-semibold text-gray-500"
                                                            title="Drag to reorder">
                                                            Drag
                                                        </button>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold text-gray-800">{{ $section['label'] }}</p>
                                                            <p class="font-mono text-xs text-gray-400">{{ $section['key'] }}</p>
                                                        </div>
                                                    </div>

                                                    <label class="flex items-center gap-2 whitespace-nowrap">
                                                        <input type="checkbox"
                                                            data-section-visible
                                                            @change="updateJson()"
                                                            @checked($section['visible'])
                                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                        <span class="text-sm font-medium text-gray-700">Show</span>
                                                    </label>
                                                </div>

                                                <div class="space-y-4 p-4">
                                                    @switch($section['key'])
                                                        @case('hero')
                                                            <div>
                                                                <h5 class="font-semibold text-gray-700">Hero Banner Image</h5>
                                                                <p class="text-sm text-gray-500 mb-3">Upload an image to turn the hero into a full-width banner with text overlay.</p>

                                                                @if ($heroBackgroundSetting?->value)
                                                                    <div class="mb-4 overflow-hidden rounded-lg border bg-white">
                                                                        <img src="{{ Storage::url($heroBackgroundSetting->value) }}"
                                                                            alt="Current hero background"
                                                                            class="h-40 w-full object-cover" />
                                                                        <label class="flex items-center gap-2 p-3 cursor-pointer">
                                                                            <input type="checkbox" name="settings[remove_hero_background]" value="1"
                                                                                class="rounded border-gray-300 text-red-600">
                                                                            <span class="text-sm text-red-600">Remove hero background image</span>
                                                                        </label>
                                                                    </div>
                                                                @endif

                                                                <input type="file"
                                                                    id="hero_background"
                                                                    name="hero_background"
                                                                    accept=".jpg,.jpeg,.png,.webp"
                                                                    class="mt-1 block w-full text-sm text-gray-500
                                                                            file:mr-4 file:py-2 file:px-4 file:rounded-md
                                                                            file:border-0 file:text-sm file:font-semibold
                                                                            file:bg-indigo-50 file:text-indigo-700
                                                                            hover:file:bg-indigo-100" />
                                                                <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 4MB. Recommended: 1800x900 or wider.</p>
                                                                <x-input-error :messages="$errors->get('hero_background')" class="mt-2" />
                                                            </div>

                                                            @foreach ([
                                                                'homepage_badge' => 'Hero Badge',
                                                                'homepage_hero_title' => 'Hero Title',
                                                                'homepage_hero_body' => 'Hero Body',
                                                                'homepage_primary_cta' => 'Primary Button Text',
                                                                'homepage_secondary_cta' => 'Secondary Button Text',
                                                                'homepage_stat_tickets' => 'Hero Tickets Stat',
                                                                'homepage_stat_sold' => 'Hero Sold Stat',
                                                                'homepage_stat_price' => 'Hero Price Stat',
                                                            ] as $key => $fallbackLabel)
                                                                <div>
                                                                    <x-input-label :for="$key" :value="$settingLabel($key, $fallbackLabel)" />
                                                                    @if ($key === 'homepage_hero_body')
                                                                        <textarea id="{{ $key }}"
                                                                            name="settings[{{ $key }}]"
                                                                            rows="4"
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settingValue($key) }}</textarea>
                                                                    @else
                                                                        <x-text-input
                                                                            :id="$key"
                                                                            :name="'settings[' . $key . ']'"
                                                                            type="text"
                                                                            class="mt-1 block w-full"
                                                                            :value="$settingValue($key)" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @break

                                                        @case('features')
                                                            @foreach ([
                                                                'homepage_feature_intro' => 'Features Eyebrow',
                                                                'homepage_feature_heading' => 'Features Heading',
                                                                'homepage_feature_one_title' => 'Feature 1 Title',
                                                                'homepage_feature_one_body' => 'Feature 1 Body',
                                                                'homepage_feature_two_title' => 'Feature 2 Title',
                                                                'homepage_feature_two_body' => 'Feature 2 Body',
                                                                'homepage_feature_three_title' => 'Feature 3 Title',
                                                                'homepage_feature_three_body' => 'Feature 3 Body',
                                                            ] as $key => $fallbackLabel)
                                                                <div>
                                                                    <x-input-label :for="$key" :value="$settingLabel($key, $fallbackLabel)" />
                                                                    @if (in_array($key, ['homepage_feature_one_body', 'homepage_feature_two_body', 'homepage_feature_three_body'], true))
                                                                        <textarea id="{{ $key }}"
                                                                            name="settings[{{ $key }}]"
                                                                            rows="4"
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settingValue($key) }}</textarea>
                                                                    @else
                                                                        <x-text-input
                                                                            :id="$key"
                                                                            :name="'settings[' . $key . ']'"
                                                                            type="text"
                                                                            class="mt-1 block w-full"
                                                                            :value="$settingValue($key)" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @break

                                                        @case('workflow')
                                                            @foreach ([
                                                                'homepage_workflow_heading' => 'Workflow Heading',
                                                                'homepage_workflow_body' => 'Workflow Body',
                                                            ] as $key => $fallbackLabel)
                                                                <div>
                                                                    <x-input-label :for="$key" :value="$settingLabel($key, $fallbackLabel)" />
                                                                    @if ($key === 'homepage_workflow_body')
                                                                        <textarea id="{{ $key }}"
                                                                            name="settings[{{ $key }}]"
                                                                            rows="4"
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settingValue($key) }}</textarea>
                                                                    @else
                                                                        <x-text-input :id="$key" :name="'settings[' . $key . ']'" type="text" class="mt-1 block w-full" :value="$settingValue($key)" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @break

                                                        @case('section_system')
                                                            @foreach ([
                                                                'homepage_sections_heading' => 'Sections Heading',
                                                                'homepage_sections_body' => 'Sections Body',
                                                            ] as $key => $fallbackLabel)
                                                                <div>
                                                                    <x-input-label :for="$key" :value="$settingLabel($key, $fallbackLabel)" />
                                                                    @if ($key === 'homepage_sections_body')
                                                                        <textarea id="{{ $key }}"
                                                                            name="settings[{{ $key }}]"
                                                                            rows="4"
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settingValue($key) }}</textarea>
                                                                    @else
                                                                        <x-text-input :id="$key" :name="'settings[' . $key . ']'" type="text" class="mt-1 block w-full" :value="$settingValue($key)" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @break

                                                        @case('testimonials')
                                                            <p class="text-sm text-gray-500">This section currently uses static testimonial cards. Its position and visibility are controlled here.</p>
                                                            @break

                                                        @case('final_cta')
                                                            @foreach ([
                                                                'homepage_final_cta_heading' => 'Final CTA Heading',
                                                                'homepage_final_cta_body' => 'Final CTA Body',
                                                            ] as $key => $fallbackLabel)
                                                                <div>
                                                                    <x-input-label :for="$key" :value="$settingLabel($key, $fallbackLabel)" />
                                                                    @if ($key === 'homepage_final_cta_body')
                                                                        <textarea id="{{ $key }}"
                                                                            name="settings[{{ $key }}]"
                                                                            rows="4"
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settingValue($key) }}</textarea>
                                                                    @else
                                                                        <x-text-input :id="$key" :name="'settings[' . $key . ']'" type="text" class="mt-1 block w-full" :value="$settingValue($key)" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @break
                                                    @endswitch
                                                </div>
                                            </section>
                                        @endforeach
                                    </div>

                                    <x-input-error :messages="$errors->get('home_layout_json')" class="mt-2" />

                                    <details class="pt-2">
                                        <summary class="cursor-pointer text-sm font-medium text-gray-500">View generated home.json</summary>
                                        <pre class="mt-2 overflow-auto rounded-md bg-gray-900 p-3 text-xs text-gray-100" x-text="json()"></pre>
                                    </details>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-primary-button>Save Homepage Content</x-primary-button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function homepageLayout() {
        return {
            dragged: null,
            move(from, to) {
                if (!from || !to || from === to) return;

                const parent = this.$refs.sections;
                const cards = Array.from(parent.children);
                const fromIndex = cards.indexOf(from);
                const toIndex = cards.indexOf(to);

                if (fromIndex < toIndex) {
                    parent.insertBefore(from, to.nextSibling);
                } else {
                    parent.insertBefore(from, to);
                }

                this.dragged = null;
                this.updateJson();
            },
            json() {
                return JSON.stringify({ sections: this.sections() }, null, 2);
            },
            sections() {
                return Array.from(this.$refs.sections.children).map((section) => ({
                    key: section.dataset.sectionKey,
                    label: section.dataset.sectionLabel,
                    visible: section.querySelector('[data-section-visible]')?.checked ?? true,
                }));
            },
            updateJson() {
                this.$refs.layoutJson.value = this.json();
            },
        };
    }

    // Live logo preview
    document.getElementById('site_logo')?.addEventListener('change', function (e) {
        const file    = e.target.files[0];
        const wrapper = document.getElementById('logo-preview-wrapper');
        const preview = document.getElementById('logo-preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                wrapper.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            wrapper.classList.add('hidden');
        }
    });
</script>
