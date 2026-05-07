<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $raffle->title }}
            </h2>
            @if (Auth::guard('web')->check())
                <a href="{{ route('raffle.edit', $raffle) }}">
                    <x-secondary-button>Edit Raffle</x-secondary-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Expired state for sponsor --}}
            @if (Auth::guard('sponsor')->check())
                @php
                    $myReserved = $raffle->tickets()
                        ->where('sponsor_id', Auth::guard('sponsor')->id())
                        ->where('status', 'reserved')
                        ->count();

                    $hasExpired = $raffle->tickets()
                        ->where('sponsor_id', Auth::guard('sponsor')->id())
                        ->where('status', 'reserved')
                        ->where('reserved_until', '<', now())
                        ->exists();
                @endphp

            @endif

            {{-- Payment status component --}}
            <div class="mt-4">
                <x-payment-status :raffle="$raffle" :sponsorPayments="$sponsorPayments" />
            </div>

            {{-- Raffle details --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600 mb-4">{{ $raffle->description }}</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Status</p>
                        <span @class([
                            'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                            'bg-gray-100 text-gray-600'   => $raffle->status->value === 'draft',
                            'bg-blue-100 text-blue-700'   => $raffle->status->value === 'generating',
                            'bg-green-100 text-green-700' => $raffle->status->value === 'active',
                            'bg-red-100 text-red-700'     => $raffle->status->value === 'closed',
                        ])>
                            {{ $raffle->status->value }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-400">Draw Date</p>
                        <p class="font-semibold">{{ $raffle->draw_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Ticket Price</p>
                        <p class="font-semibold">₱{{ number_format($raffle->ticket_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Tickets</p>
                        <p class="font-semibold">{{ number_format($raffle->total_tickets) }}</p>
                    </div>
                </div>
            </div>

            {{-- Prizes --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Prizes</h3>

                <div class="space-y-2">
                    @foreach ($raffle->prizes->whereIn('type', ['first', 'second', 'third'])->sortBy('position') as $prize)
                        <div class="flex items-center gap-3">
                            <span class="text-gray-400 text-sm w-24">
                                {{ match($prize->type) {
                                    'first'  => '🥇 1st',
                                    'second' => '🥈 2nd',
                                    'third'  => '🥉 3rd',
                                    default  => $prize->type,
                                } }}
                            </span>
                            <span class="font-medium">{{ peso($prize->name)}}</span>
                        </div>
                    @endforeach

                    {{-- Consolation prizes --}}
                    @php $consolations = $raffle->prizes->where('type', 'consolation'); @endphp

                    @if ($consolations->isNotEmpty())
                        <hr class="my-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-400">
                                Consolation Prizes ({{ $consolations->count() }})
                            </p>
                            @if ($consolations->first()?->prize)
                                <span class="text-sm font-bold text-indigo-600">
                                    {{ peso($consolations->first()->prize) }} each
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach ($consolations as $prize)
                                <div class="flex items-center gap-2 text-sm">
                                    <span>🎁</span>
                                    <span>{{ $prize->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tickets --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-700">Tickets</h3>
                    <a href="{{ route('ticket.index', $raffle) }}"
                       class="text-sm text-indigo-600 hover:underline">
                        View all tickets →
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="p-4 bg-green-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-green-700">
                            {{ $raffle->availableTickets()->count() }}
                        </p>
                        <p class="text-green-600 text-xs mt-1">Available</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-yellow-700">
                            {{ $raffle->reservedTickets()->count() }}
                        </p>
                        <p class="text-yellow-600 text-xs mt-1">Reserved</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-gray-700">
                            {{ $raffle->soldTickets()->count() }}
                        </p>
                        <p class="text-gray-600 text-xs mt-1">Sold</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-indigo-700">
                            {{ number_format($raffle->total_tickets) }}
                        </p>
                        <p class="text-indigo-600 text-xs mt-1">Total</p>
                    </div>
                </div>

                @if (Auth::guard('sponsor')->check())
                    <div class="mt-4">
                        <a href="{{ route('ticket.index', $raffle) }}"
                           class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                            Browse & Buy Tickets
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>