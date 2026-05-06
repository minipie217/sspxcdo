<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tickets — {{ $raffle->title }}
            </h2>
            <a href="{{ route('raffle.show', $raffle) }}"
               class="text-sm text-indigo-600 hover:underline">
                ← Back to Raffle
            </a>
        </div>
    </x-slot>

    <div class="py-12 pb-32">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Status filter --}}
            <div class="mb-4 flex gap-3 text-sm">
                @foreach (['', 'available', 'reserved', 'sold'] as $s)
                    <a href="{{ route('ticket.index', [$raffle, 'status' => $s]) }}"
                       class="{{ ($status === $s || ($s === '' && ! $status)) ? 'text-indigo-600 font-semibold underline' : 'text-gray-400' }} capitalize hover:underline">
                        {{ $s ?: 'All' }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Ticket No.</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Holder</th>
                            <th class="px-4 py-3">Reserved Until</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td class="px-4 py-3 font-mono font-semibold">
                                    {{ $ticket->ticket_number }}
                                </td>

                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                                        'bg-green-100 text-green-700'   => $ticket->status->value === 'available',
                                        'bg-yellow-100 text-yellow-700' => $ticket->status->value === 'reserved',
                                        'bg-gray-100 text-gray-600'     => $ticket->status->value === 'sold',
                                    ])>
                                        {{ $ticket->statusLabel() }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    @if ($ticket->status->value === 'available')

                                        @if (Auth::guard('sponsor')->check())
                                            {{-- Inline buy form --}}
                                            <div x-data="{ useDifferentName: false }" class="space-y-2">
                                                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                                    <input type="checkbox" x-model="useDifferentName"
                                                           class="rounded border-gray-300 text-indigo-600">
                                                    <span>Use a different name</span>
                                                </label>

                                                <form method="POST"
                                                      action="{{ route('ticket.reserve', [$raffle, $ticket]) }}"
                                                      class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="use_other_name"
                                                           :value="useDifferentName ? '1' : '0'">

                                                    <div x-show="useDifferentName" x-cloak class="space-y-1">
                                                        <input type="text"
                                                               name="holder_first_name"
                                                               placeholder="First name"
                                                               class="w-full px-2 py-1 border border-gray-300 rounded-md text-xs" />
                                                        <input type="text"
                                                               name="holder_last_name"
                                                               placeholder="Last name"
                                                               class="w-full px-2 py-1 border border-gray-300 rounded-md text-xs" />
                                                    </div>

                                                    <button type="submit"
                                                            class="w-full px-3 py-1.5 bg-green-500 hover:bg-green-700 text-white text-xs font-semibold rounded-md">
                                                        Buy Ticket
                                                    </button>
                                                </form>
                                            </div>

                                        @else
                                            {{-- Guest --}}
                                            <a href="{{ route('sponsor.login') }}"
                                               class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 font-semibold">
                                                Log in 
                                                @if (Auth::guard('web')->check())
                                                    as Sponsor
                                                @endif
                                                to Buy
                                            </a>
                                        @endif
                                    @else
                                        {{ $ticket->holderName() ?? '—' }}
                                    @endif                                    
                                </td>

                                <td class="px-4 py-3 text-gray-500">
                                    @if ($ticket->status->value === 'available')
                                        ---
                                    @elseif (
                                        $ticket->status->value === 'reserved' &&
                                        Auth::guard('sponsor')->check() &&
                                        $ticket->sponsor_id === Auth::guard('sponsor')->id()
                                    )
                                        {{-- Sponsor's own reservation --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-yellow-600 font-semibold">
                                                Reserved by you
                                            </span>
                                            <form method="POST"
                                                  action="{{ route('ticket.cancel', [$raffle, $ticket]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs text-red-500 hover:underline"
                                                        onclick="return confirm('Cancel this reservation?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        {{ $ticket->reserved_until?->format('M d, H:i') ?? '—' }}
                                    @endif                                    
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    No tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-4 py-4">
                    {{ $tickets->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Floating bar — sponsor reserved tickets --}}
    @if (Auth::guard('sponsor')->check())
        @php
            $myReserved = $raffle->tickets()
                ->where('sponsor_id', Auth::guard('sponsor')->id())
                ->where('status', 'reserved')
                ->count();
        @endphp

        @if ($myReserved > 0)
            <div class="fixed bottom-0 left-0 right-0 bg-indigo-600 text-white shadow-lg z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold">
                            {{ $myReserved }} ticket{{ $myReserved > 1 ? 's' : '' }} reserved
                        </p>
                        <p class="text-xs text-indigo-200">
                            Complete payment before your reservations expire.
                        </p>
                    </div>
                    <a href="{{ route('ticket.payment', $raffle) }}"
                       class="px-6 py-2 bg-white text-indigo-600 rounded-lg font-semibold text-sm hover:bg-indigo-50">
                        Proceed to Payment →
                    </a>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>