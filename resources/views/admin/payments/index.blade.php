<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pending payments — 3 column cards --}}
            <div>
                <h3 class="font-semibold text-gray-700 text-lg mb-4">
                    Pending Payments
                    <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                        {{ $pending->flatten()->count() }}
                    </span>
                </h3>

                @if ($pending->isEmpty())
                    <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center text-gray-400">
                        No pending payments.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($pending as $sponsorId => $payments)
                            @php
                                $sponsor = $payments->first()->sponsor;
                                $raffle  = $payments->first()->ticket->raffle;
                            @endphp

                            <div class="bg-white shadow-sm rounded-lg overflow-hidden flex flex-col">

                                {{-- Card header --}}
                                <div class="px-5 py-4 bg-gray-50 border-b">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ $sponsor->fullName() }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $sponsor->email }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">
                                            {{ $payments->count() }} ticket{{ $payments->count() > 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-indigo-600 mt-1 font-medium">
                                        {{ $raffle->title }}
                                    </p>
                                </div>

                                {{-- Ticket list --}}
                                <div class="px-5 py-2 flex-1 divide-y divide-gray-100">
                                    @foreach ($payments as $payment)
                                        @php
                                            $expiresAt  = $payment->ticket->reserved_until;
                                            $isExpiring = $expiresAt && $expiresAt->diffInMinutes(now()) >= -5;
                                        @endphp

                                        <div class="py-3">
                                            {{-- Ticket number --}}
                                            <p class="font-mono font-bold text-gray-800">
                                                {{ $payment->ticket->ticket_number }}
                                            </p>

                                            {{-- Submitted --}}
                                            <p class="text-xs text-gray-400 mt-1">
                                                Submitted {{ $payment->created_at->diffForHumans() }}
                                            </p>

                                            {{-- Expiry --}}
                                            @if ($expiresAt)
                                                <p class="text-xs mt-1 {{ $isExpiring ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                    Expiresss
                                                    <span class="countdown-timer"
                                                          data-expires="{{ $expiresAt->toIso8601String() }}">
                                                        {{ $expiresAt->diffForHumans() }}
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total --}}
                                <div class="px-5 py-3 bg-gray-50 border-t flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Total</span>
                                    <span class="font-bold text-indigo-600">
                                        ₱{{ number_format($payments->sum(fn($p) => $p->ticket->raffle->ticket_price), 2) }}
                                    </span>
                                </div>

                                {{-- Review button --}}
                                <div class="px-5 py-4 border-t">
                                    <a href="{{ route('admin.payments.show', $payments->first()) }}"
                                       class="block w-full text-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                                        Review All Tickets
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent decisions --}}
            <div>
                <h3 class="font-semibold text-gray-700 text-lg mb-4">Recent Decisions</h3>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Ticket</th>
                                <th class="px-4 py-3">Raffle</th>
                                <th class="px-4 py-3">Sponsor</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Decided By</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recent as $payment)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-semibold">
                                        {{ $payment->ticket->ticket_number }}
                                    </td>
                                    <td class="px-4 py-3">{{ $payment->ticket->raffle->title }}</td>
                                    <td class="px-4 py-3">{{ $payment->sponsor->fullName() }}</td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                                            'bg-green-100 text-green-700' => $payment->status->value === 'confirmed',
                                            'bg-red-100 text-red-700'     => $payment->status->value === 'rejected',
                                        ])>
                                            {{ $payment->status->value }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $payment->confirmedBy?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $payment->confirmed_at?->format('M d, Y H:i') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                        No decisions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-4 py-3">{{ $recent->links() }}</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Live countdown for each ticket expiry
        function updateCountdowns() {
            document.querySelectorAll('.countdown-timer').forEach(el => {
                const expiresAt = new Date(el.dataset.expires);
                const diff      = Math.floor((expiresAt - new Date()) / 1000);

                if (diff <= 0) {
                    el.textContent = 'Expired';
                    el.classList.add('text-red-600');
                    // Reload page after 3 seconds to remove expired cards
                    setTimeout(() => window.location.reload(), 3000);
                    return;
                }

                const mins = Math.floor(diff / 60).toString().padStart(2, '0');
                const secs = (diff % 60).toString().padStart(2, '0');
                el.textContent = `in ${mins}:${secs}`;

                // Turn red when under 5 minutes
                if (diff < 300) {
                    el.classList.add('text-red-500', 'font-semibold');
                    el.classList.remove('text-gray-400');
                }
            });
        }

        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    </script>
</x-app-layout>