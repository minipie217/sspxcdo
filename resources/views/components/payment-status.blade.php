@props(['raffle', 'sponsorPayments'])

@if (Auth::guard('sponsor')->check())
    @php
        $sponsorId    = Auth::guard('sponsor')->id();

        $reserved     = $raffle->tickets()
            ->where('sponsor_id', $sponsorId)
            ->where('status', 'reserved')
            ->get();

        $myReserved   = $reserved->count();
        $hasExpired   = $reserved->filter(fn($t) => $t->reserved_until?->isPast())->isNotEmpty();

        $pending      = $sponsorPayments?->filter(fn($p) => $p->status->value === 'pending') ?? collect();
        $confirmed    = $sponsorPayments?->filter(fn($p) => $p->status->value === 'confirmed') ?? collect();
        $rejected     = $sponsorPayments?->filter(fn($p) => $p->status->value === 'rejected') ?? collect();

        $hasPayments  = $sponsorPayments?->isNotEmpty() ?? false;
    @endphp

    {{-- 1. Expired reservations --}}
    @if ($hasExpired)
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="font-semibold text-red-700">Your reservations have expired.</p>
            <p class="text-sm text-red-600 mt-1">
                Your reserved tickets have been released back to the pool.
            </p>
            <a href="{{ route('ticket.index', $raffle) }}"
               class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                Browse Tickets Again
            </a>
        </div>

    {{-- 2. Payment submitted — show per-ticket status --}}
    @elseif ($hasPayments)
        <div class="p-4 bg-white border rounded-lg shadow-sm">
            <h4 class="font-semibold text-gray-700 mb-3">Your Payment Status</h4>

            <div class="space-y-0 divide-y divide-gray-100">
                @foreach ($sponsorPayments as $payment)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-mono font-bold text-gray-800">
                                {{ $payment->ticket->ticket_number }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Submitted {{ $payment->created_at->diffForHumans() }}
                            </p>
                            @if ($payment->status->value === 'rejected' && $payment->notes)
                                <p class="text-xs text-red-500 mt-0.5">
                                    Reason: {{ $payment->notes }}
                                </p>
                            @endif
                        </div>

                        <span @class([
                            'px-3 py-1 rounded-full text-xs font-semibold',
                            'bg-yellow-100 text-yellow-700' => $payment->status->value === 'pending',
                            'bg-green-100 text-green-700'   => $payment->status->value === 'confirmed',
                            'bg-red-100 text-red-700'       => $payment->status->value === 'rejected',
                        ])>
                            {{ match($payment->status->value) {
                                'pending'   => '⏳ Pending Confirmation',
                                'confirmed' => '✓ Confirmed',
                                'rejected'  => '✗ Rejected',
                            } }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Resubmit only if rejected AND still has reserved tickets --}}
            @if ($rejected->isNotEmpty() && $myReserved > 0)
                <div class="mt-4 pt-3 border-t">
                    <p class="text-sm text-red-600 mb-2">
                        Some payments were rejected. Please resubmit proof for the remaining reserved tickets.
                    </p>
                    <a href="{{ route('ticket.payment', $raffle) }}"
                       class="inline-block px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                        Resubmit Payment Proof
                    </a>
                </div>
            @endif
        </div>

    {{-- 3. Has reserved tickets — proceed to payment --}}
    @elseif ($myReserved > 0)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center justify-between">
            <div>
                <p class="font-semibold text-yellow-700">
                    You have {{ $myReserved }} reserved ticket{{ $myReserved > 1 ? 's' : '' }}.
                </p>
                <p class="text-xs text-yellow-600 mt-1">
                    Complete payment before your reservation expires.
                </p>
            </div>
            <a href="{{ route('ticket.payment', $raffle) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                Proceed to Payment →
            </a>
        </div>

    {{-- 4. No reservations — browse --}}
    @else
        <a href="{{ route('ticket.index', $raffle) }}"
           class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
            Browse & Buy Tickets
        </a>
    @endif

@endif