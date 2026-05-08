<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Total Funds Raised --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Total Funds Raised
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                ₱{{ number_format($totalFunds, 2) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                From confirmed payments
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active Raffles --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Active Raffles
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ $activeRaffles }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Currently running
                            </p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tickets Sold --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Tickets Sold
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ number_format($ticketsSold) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Across all raffles
                            </p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Registered Sponsors --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Registered Sponsors
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ number_format($totalSponsors) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Total participants
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
            {{-- Next section goes here --}}

            {{-- Pending Tasks --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700">Pending Tasks</h3>
                    @if ($pendingPayments + $expiringReservations + $generatingRaffles === 0)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
                            All clear
                        </span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-semibold">
                            {{ $pendingPayments + $expiringReservations + $generatingRaffles }} needs attention
                        </span>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">

                    {{-- Pending payments --}}
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div @class([
                                'p-2 rounded-full',
                                'bg-yellow-100' => $pendingPayments > 0,
                                'bg-gray-100'   => $pendingPayments === 0,
                            ])>
                                <svg @class([
                                    'w-5 h-5',
                                    'text-yellow-600' => $pendingPayments > 0,
                                    'text-gray-400'   => $pendingPayments === 0,
                                ]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Pending Payments
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if ($pendingPayments > 0)
                                        {{ $pendingPayments }} payment{{ $pendingPayments > 1 ? 's' : '' }}
                                        waiting for confirmation
                                    @else
                                        No pending payments
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($pendingPayments > 0)
                            <a href="{{ route('admin.payments.index') }}"
                            class="flex items-center gap-1 px-4 py-2 bg-yellow-50 text-yellow-700 text-xs font-semibold rounded-md hover:bg-yellow-100">
                                Review
                                <span class="px-1.5 py-0.5 bg-yellow-200 text-yellow-800 rounded-full">
                                    {{ $pendingPayments }}
                                </span>
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Nothing to do</span>
                        @endif
                    </div>

                    {{-- Expiring reservations --}}
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div @class([
                                'p-2 rounded-full',
                                'bg-orange-100' => $expiringReservations > 0,
                                'bg-gray-100'   => $expiringReservations === 0,
                            ])>
                                <svg @class([
                                    'w-5 h-5',
                                    'text-orange-600' => $expiringReservations > 0,
                                    'text-gray-400'   => $expiringReservations === 0,
                                ]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Expiring Reservations
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if ($expiringReservations > 0)
                                        {{ $expiringReservations }} reservation{{ $expiringReservations > 1 ? 's' : '' }}
                                        expiring within 10 minutes
                                    @else
                                        No reservations expiring soon
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($expiringReservations > 0)
                            <a href="{{ route('raffle.index') }}"
                            class="flex items-center gap-1 px-4 py-2 bg-orange-50 text-orange-700 text-xs font-semibold rounded-md hover:bg-orange-100">
                                View Raffles
                                <span class="px-1.5 py-0.5 bg-orange-200 text-orange-800 rounded-full">
                                    {{ $expiringReservations }}
                                </span>
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Nothing to do</span>
                        @endif
                    </div>

                    {{-- Generating raffles --}}
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div @class([
                                'p-2 rounded-full',
                                'bg-blue-100' => $generatingRaffles > 0,
                                'bg-gray-100' => $generatingRaffles === 0,
                            ])>
                                <svg @class([
                                    'w-5 h-5',
                                    'text-blue-600' => $generatingRaffles > 0,
                                    'text-gray-400' => $generatingRaffles === 0,
                                ]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Generating Tickets
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if ($generatingRaffles > 0)
                                        {{ $generatingRaffles }} raffle{{ $generatingRaffles > 1 ? 's' : '' }}
                                        still generating tickets
                                    @else
                                        All raffles are ready
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($generatingRaffles > 0)
                            <a href="{{ route('raffle.index') }}"
                            class="flex items-center gap-1 px-4 py-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-md hover:bg-blue-100">
                                View Raffles
                                <span class="px-1.5 py-0.5 bg-blue-200 text-blue-800 rounded-full">
                                    {{ $generatingRaffles }}
                                </span>
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Nothing to do</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Next section goes here --}}

        </div>
    </div>
</x-app-layout>