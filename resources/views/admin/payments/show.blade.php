<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Payment — {{ $payment->sponsor->fullName() }}
            </h2>
            <a href="{{ route('admin.payments.index') }}">
                <x-secondary-button>← Back</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sponsor info --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Sponsor</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Name</p>
                        <p class="font-semibold">{{ $payment->sponsor->fullName() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Email</p>
                        <p class="font-semibold">{{ $payment->sponsor->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Phone</p>
                        <p class="font-semibold">{{ $payment->sponsor->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Raffle</p>
                        <p class="font-semibold">{{ $payment->ticket->raffle->title }}</p>
                    </div>
                </div>
            </div>

            {{-- All tickets --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">
                    Tickets ({{ $allPayments->count() }})
                </h3>

                <div class="divide-y divide-gray-100">
                    @foreach ($allPayments as $p)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-mono font-bold text-gray-800">
                                    {{ $p->ticket->ticket_number }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Holder: {{ $p->ticket->holderName() ?? $payment->sponsor->fullName() }}
                                </p>
                            </div>
                            <span class="font-semibold text-indigo-600">
                                ₱{{ number_format($p->ticket->raffle->ticket_price, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Total</span>
                    <span class="font-bold text-indigo-600 text-lg">
                        ₱{{ number_format($allPayments->sum(fn($p) => $p->ticket->raffle->ticket_price), 2) }}
                    </span>
                </div>
            </div>

            {{-- Proof of payment --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Proof of Payment</h3>

                @if ($payment->proof_type->value === 'image')
                    <img src="{{ Storage::url($payment->proof_value) }}"
                         alt="Payment proof"
                         class="max-w-full rounded-lg border" />
                @else
                    <p class="text-sm text-gray-500">Transaction / Reference Number:</p>
                    <p class="font-mono font-bold text-xl mt-1">{{ $payment->proof_value }}</p>
                @endif
            </div>

            {{-- Reserved tickets with no payment yet --}}
            @if ($reservedTickets->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">
                        Also Reserved — No Payment Submitted Yet
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @foreach ($reservedTickets as $ticket)
                            <div class="py-2 font-mono text-sm text-gray-600">
                                {{ $ticket->ticket_number }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            @if ($payment->status->value === 'pending')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700">Decision</h3>
                    <p class="text-sm text-gray-500">
                        This will apply to all {{ $allPayments->count() }} pending ticket(s) for this sponsor.
                    </p>

                    {{-- Confirm all --}}
                    <form method="POST" action="{{ route('admin.payments.confirm-all') }}">
                        @csrf
                        <input type="hidden" name="sponsor_id" value="{{ $payment->sponsor_id }}">
                        <input type="hidden" name="raffle_id"  value="{{ $payment->ticket->raffle_id }}">
                        <x-primary-button class="w-full justify-center bg-green-600 hover:bg-green-700 focus:bg-green-700">
                            ✓ Confirm All — Mark {{ $allPayments->count() }} Ticket(s) as Sold
                        </x-primary-button>
                    </form>

                    {{-- Reject all --}}
                    <form method="POST" action="{{ route('admin.payments.reject-all') }}">
                        @csrf
                        <input type="hidden" name="sponsor_id" value="{{ $payment->sponsor_id }}">
                        <input type="hidden" name="raffle_id"  value="{{ $payment->ticket->raffle_id }}">
                        <div class="mb-3">
                            <x-input-label for="notes" value="Rejection Reason (optional)" />
                            <textarea id="notes" name="notes" rows="2"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"
                                      placeholder="e.g. Amount does not match, unclear screenshot..."></textarea>
                        </div>
                        <x-danger-button class="w-full justify-center">
                            ✗ Reject All — Release {{ $allPayments->count() }} Ticket(s)
                        </x-danger-button>
                    </form>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">
                        This payment has already been
                        <span class="font-semibold capitalize">{{ $payment->status->value }}</span>
                        by {{ $payment->confirmedBy?->name ?? '—' }}
                        on {{ $payment->confirmed_at?->format('M d, Y H:i') }}.
                    </p>
                    @if ($payment->notes)
                        <p class="text-sm text-red-500 mt-2">Reason: {{ $payment->notes }}</p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>