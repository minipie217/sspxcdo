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
                    <button @click="tab = 'emails'"
                            :class="tab === 'emails'
                                ? 'border-b-2 border-indigo-600 text-indigo-600'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-3 text-sm font-medium focus:outline-none">
                        ✉️ Email Templates
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

                                    @if ($siteLogoSetting?->value)
                                        <div class="mt-2 mb-3 flex items-center gap-4">
                                            <img src="{{ Storage::url($siteLogoSetting->value) }}"
                                                alt="Current logo"
                                                class="h-12 w-auto object-contain border rounded p-1 bg-gray-50" />
                                            <div>
                                                <p class="text-xs text-gray-500 mb-2">Current logo</p>

                                                {{-- Delete button --}}
                                                <button type="button"
                                                        onclick="deleteItem('{{ route('admin.settings.logo.delete') }}', 'Remove this logo?')"
                                                        class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-md hover:bg-red-100 border border-red-200">
                                                    🗑 Remove Logo
                                                </button>
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

                            {{-- Max tickets per sponsor --}}
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Ticket Limit</h3>
                                <p class="text-sm text-gray-400 mb-4">
                                    Maximum number of tickets a single sponsor can reserve per raffle.
                                </p>

                                @php
                                    $maxTicketsSetting = $groups->get('general')
                                        ?->firstWhere('key', 'max_tickets_per_sponsor');
                                @endphp

                                <div>
                                    <x-input-label for="max_tickets_per_sponsor" value="Max Tickets Per Sponsor" />
                                    <div class="mt-1 flex items-center gap-3">
                                        <x-text-input id="max_tickets_per_sponsor"
                                                    name="settings[max_tickets_per_sponsor]"
                                                    type="number"
                                                    min="1"
                                                    max="100"
                                                    class="block w-40"
                                                    :value="old('settings.max_tickets_per_sponsor', $maxTicketsSetting?->value ?? 5)" />
                                        <span class="text-sm text-gray-500">tickets per raffle</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        e.g. 5 = each sponsor can reserve up to 5 tickets per raffle.
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

                        <div class="sticky bottom-4 z-50 mt-6 flex justify-end pointer-events-none">
                            <div class="pointer-events-auto rounded-2xl bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl px-4 py-3">
                                <x-primary-button>Save General Settings</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Payment Accounts --}}
                <div x-show="tab === 'payment'" x-cloak>

                    {{-- Existing accounts --}}
                    <div class="space-y-3 mb-6">
                        @forelse ($paymentAccounts as $account)
                            <div x-data="{ open: false }"
                                class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                                {{-- Account header --}}
                                <div class="px-6 py-4 flex items-center justify-between">
                                    <button @click="open = !open"
                                            type="button"
                                            class="flex items-center gap-3 flex-1 text-left">
                                        <span class="text-lg">{{ $account->typeIcon() }}</span>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $account->label }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $account->typeLabel() }}
                                                @if ($account->account_number)
                                                    · {{ $account->account_number }}
                                                @endif
                                            </p>
                                        </div>
                                        <span @class([
                                            'ml-2 px-2 py-0.5 text-xs rounded-full font-semibold',
                                            'bg-green-100 text-green-700' => $account->is_active,
                                            'bg-gray-100 text-gray-500'   => ! $account->is_active,
                                        ])>
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </button>

                                    <div class="flex items-center gap-2 ml-4">
                                        <button @click="open = !open" type="button"
                                                class="px-3 py-1.5 bg-gray-50 text-gray-600 text-xs font-semibold rounded-md hover:bg-gray-100 border border-gray-200">
                                            Edit
                                        </button>
                                        <button type="button"
                                                onclick="deleteItem('{{ route('admin.payment-accounts.destroy', $account) }}', 'Delete this payment account?')"
                                                class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-md hover:bg-red-100 border border-red-200">
                                            Delete
                                        </button>
                                    </div>
                                </div>

                                {{-- Edit form --}}
                                <div x-show="open"
                                    x-transition
                                    class="border-t">
                                    <form method="POST"
                                        action="{{ route('admin.payment-accounts.update', $account) }}"
                                        enctype="multipart/form-data"
                                        class="px-6 py-4 space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label value="Type" />
                                                <select name="type"
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                    @foreach (\App\Models\PaymentAccount::typeLabels() as $value => $label)
                                                        <option value="{{ $value }}"
                                                                @selected($account->type === $value)>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label value="Label" />
                                                <x-text-input name="label" type="text"
                                                            class="mt-1 block w-full"
                                                            :value="$account->label" />
                                            </div>
                                            <div>
                                                <x-input-label value="Account Name" />
                                                <x-text-input name="account_name" type="text"
                                                            class="mt-1 block w-full"
                                                            :value="$account->account_name" />
                                            </div>
                                            <div>
                                                <x-input-label value="Account Number" />
                                                <x-text-input name="account_number" type="text"
                                                            class="mt-1 block w-full"
                                                            :value="$account->account_number" />
                                            </div>
                                        </div>

                                        {{-- Active toggle --}}
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="is_active" value="1"
                                                id="is_active_{{ $account->id }}"
                                                class="rounded border-gray-300 text-indigo-600"
                                                {{ $account->is_active ? 'checked' : '' }}>
                                            <label for="is_active_{{ $account->id }}"
                                                class="text-sm text-gray-700">
                                                Active — show on payment page
                                            </label>
                                        </div>

                                        {{-- QR Code --}}
                                        <div>
                                            <x-input-label value="QR Code" />
                                            @if ($account->qr_code)
                                                <div class="mt-2 mb-3 flex items-start gap-4">
                                                    <img src="{{ Storage::url($account->qr_code) }}"
                                                        alt="QR Code"
                                                        class="h-24 w-24 object-contain border rounded-lg p-1 bg-white" />
                                                    <button type="button"
                                                            onclick="deleteItem('{{ route('admin.payment-accounts.qr.delete', $account) }}', 'Remove this QR code?')"
                                                            class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-md hover:bg-red-100 border border-red-200">
                                                        🗑 Remove QR
                                                    </button>
                                                </div>
                                            @endif
                                            <input type="file" name="qr_code"
                                                accept=".jpg,.jpeg,.png,.svg"
                                                class="mt-1 block w-full text-sm text-gray-500
                                                        file:mr-4 file:py-2 file:px-4 file:rounded-md
                                                        file:border-0 file:text-sm file:font-semibold
                                                        file:bg-indigo-50 file:text-indigo-700
                                                        hover:file:bg-indigo-100" />
                                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or SVG. Max 2MB.</p>
                                        </div>

                                        <div class="flex justify-end">
                                            <x-primary-button>Update Account</x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-400">
                                No payment accounts yet. Add one below.
                            </div>
                        @endforelse
                    </div>

                    {{-- Add new account --}}
                    <div x-data="{ open: false }" class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <button @click="open = !open"
                                type="button"
                                class="w-full px-6 py-4 flex items-center gap-3 text-left hover:bg-gray-50">
                            <span class="text-lg">➕</span>
                            <p class="font-semibold text-indigo-600">Add Payment Account</p>
                        </button>

                        <div x-show="open" x-transition class="border-t">
                            <form method="POST"
                                action="{{ route('admin.payment-accounts.store') }}"
                                enctype="multipart/form-data"
                                class="px-6 py-4 space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label value="Type" />
                                        <select name="type"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                            <option value="">— select type —</option>
                                            @foreach (\App\Models\PaymentAccount::typeLabels() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label value="Label" />
                                        <x-text-input name="label" type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="e.g. BDO Savings Account" required />
                                    </div>
                                    <div>
                                        <x-input-label value="Account Name" />
                                        <x-text-input name="account_name" type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="e.g. SSPX CDO" />
                                    </div>
                                    <div>
                                        <x-input-label value="Account Number" />
                                        <x-text-input name="account_number" type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="e.g. 1234567890" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="is_active" value="1"
                                        id="new_is_active"
                                        class="rounded border-gray-300 text-indigo-600"
                                        checked>
                                    <label for="new_is_active" class="text-sm text-gray-700">
                                        Active — show on payment page
                                    </label>
                                </div>

                                <div>
                                    <x-input-label value="QR Code (optional)" />
                                    <input type="file" name="qr_code"
                                        accept=".jpg,.jpeg,.png,.svg"
                                        class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4 file:rounded-md
                                                file:border-0 file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100" />
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG or SVG. Max 2MB.</p>
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button>Add Account</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- Email Templates --}}
                <div x-show="tab === 'emails'" x-cloak>

                    @php
                        $placeholders = [
                            'sponsor_registration_otp' => ['{name}', '{otp}'],
                            'sponsor_login_otp'        => ['{name}', '{otp}'],
                            'admin_otp'                => ['{name}', '{otp}'],
                            'reservation_expired'      => ['{name}', '{ticket_number}'],
                            'payment_received'         => ['{admin_name}', '{ticket_number}', '{sponsor_name}', '{raffle_title}'],
                            'payment_confirmed'        => ['{name}', '{ticket_number}', '{raffle_title}', '{draw_date}'],
                            'payment_rejected'         => ['{name}', '{ticket_number}', '{rejection_reason}'],
                            'raffle_created'           => ['{name}', '{raffle_title}', '{raffle_description}', '{ticket_price}', '{draw_date}', '{raffle_url}'],
                        ];
                    @endphp

                    <div class="space-y-2">
                        @foreach ($emailTemplates as $index => $template)
                            <div x-data="{ open: false }"
                                class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                                {{-- Accordion header --}}
                                <button @click="open = !open"
                                        type="button"
                                        class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $template->label }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-md">
                                            Subject: {{ $template->subject }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 ml-4">
                                        {{-- Placeholders --}}
                                        @if (isset($placeholders[$template->key]))
                                            <div class="hidden md:flex items-center gap-1 flex-wrap">
                                                @foreach ($placeholders[$template->key] as $placeholder)
                                                    <code class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 text-xs rounded cursor-pointer"
                                                        @click.stop="
                                                            if (editors[{{ $template->id }}]) {
                                                                const range = editors[{{ $template->id }}].getSelection(true);
                                                                editors[{{ $template->id }}].insertText(range.index, '{{ $placeholder }}');
                                                                document.getElementById('body_{{ $template->id }}').value =
                                                                    editors[{{ $template->id }}].root.innerHTML;
                                                            }
                                                        ">
                                                        {{ $placeholder }}
                                                    </code>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Chevron --}}
                                        <svg :class="open ? 'rotate-180' : ''"
                                            class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Accordion body --}}
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1">

                                    <form method="POST"
                                        action="{{ route('admin.email-templates.update', $template) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="tab" value="emails">

                                        <div class="px-6 pb-4 space-y-4 border-t">

                                            {{-- Mobile placeholders --}}
                                            @if (isset($placeholders[$template->key]))
                                                <div class="md:hidden pt-4">
                                                    <p class="text-xs text-gray-400 mb-2">Placeholders — click to insert:</p>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($placeholders[$template->key] as $placeholder)
                                                            <code class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 text-xs rounded cursor-pointer"
                                                                onclick="insertPlaceholder({{ $template->id }}, '{{ $placeholder }}')">
                                                                {{ $placeholder }}
                                                            </code>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Subject --}}
                                            <div class="pt-4">
                                                <x-input-label for="subject_{{ $template->id }}" value="Subject" />
                                                <x-text-input id="subject_{{ $template->id }}"
                                                            name="subject"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            :value="old('subject', $template->subject)" />
                                            </div>

                                            {{-- Body --}}
                                            <div>
                                                <x-input-label value="Body" />
                                                <div class="mt-1 border border-gray-300 rounded-md overflow-hidden">
                                                    <div id="editor_{{ $template->id }}"
                                                        style="min-height: 200px;">{!! $template->body !!}</div>
                                                </div>
                                                <input type="hidden"
                                                    name="body"
                                                    id="body_{{ $template->id }}"
                                                    value="{{ $template->body }}">
                                            </div>

                                        </div>

                                        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end">
                                            <x-primary-button>Save Template</x-primary-button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        @endforeach
                    </div>

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

                        <div class="sticky bottom-4 z-50 mt-6 flex justify-end pointer-events-none">
                            <div class="pointer-events-auto rounded-2xl bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl px-4 py-3">
                                <x-primary-button>
                                    Save Homepage Content
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

        <script>
            const editors = {};

            document.addEventListener('DOMContentLoaded', function () {
                @foreach ($emailTemplates as $template)
                    editors[{{ $template->id }}] = new Quill('#editor_{{ $template->id }}', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['link'],
                                ['clean'],
                            ]
                        }
                    });

                    // Sync editor content to hidden input before form submit
                    editors[{{ $template->id }}].on('text-change', function () {
                        document.getElementById('body_{{ $template->id }}').value =
                            editors[{{ $template->id }}].root.innerHTML;
                    });
                @endforeach
            });

            // Insert placeholder at cursor position
            function insertPlaceholder(templateId, placeholder) {
                const editor = editors[templateId];
                if (! editor) return;

                const range = editor.getSelection(true);
                editor.insertText(range.index, placeholder);
                editor.setSelection(range.index + placeholder.length);

                document.getElementById('body_' + templateId).value =
                    editor.root.innerHTML;
            }
        </script>
        <script>
            function previewQR(input, group) {
                const file    = input.files[0];
                const wrapper = document.getElementById('qr-preview-' + group);
                const img     = document.getElementById('qr-preview-img-' + group);

                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        img.src = e.target.result;
                        wrapper.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    wrapper.classList.add('hidden');
                }
            }
        </script>
    @endpush
    {{-- Reusable DELETE form — triggered by deleteItem() --}}
    <form id="delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        function deleteItem(url, message) {
            if (! confirm(message ?? 'Are you sure? This cannot be undone.')) return;
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    </script>
    @endpush
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
