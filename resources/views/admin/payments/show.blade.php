<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Payment
            </h2>
            <a href="{{ route('admin.payments.index') }}">
                <x-secondary-button>← Back</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Payment details --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Payment Details</h3>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Ticket Number</p>
                        <p class="font-mono font-bold text-lg">{{ $payment->ticket->ticket_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Raffle</p>
                        <p class="font-semibold">{{ $payment->ticket->raffle->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Sponsor</p>
                        <p class="font-semibold">{{ $payment->sponsor->fullName() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Email</p>
                        <p class="font-semibold">{{ $payment->sponsor->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Ticket Holder</p>
                        <p class="font-semibold">{{ $payment->ticket->holderName() ?? $payment->sponsor->fullName() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Amount</p>
                        <p class="font-bold text-indigo-600">₱{{ number_format($payment->ticket->raffle->ticket_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Submitted</p>
                        <p>{{ $payment->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Status</p>
                        <span @class([
                            'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                            'bg-yellow-100 text-yellow-700' => $payment->status->value === 'pending',
                            'bg-green-100 text-green-700'   => $payment->status->value === 'confirmed',
                            'bg-red-100 text-red-700'       => $payment->status->value === 'rejected',
                        ])>
                            {{ $payment->status->value }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Proof of payment --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Proof of Payment</h3>

                @if ($payment->proof_type->value === 'image')
                    <img src="{{ Storage::url($payment->proof_value) }}"
                         alt="Payment proof"
                         class="max-w-full rounded-lg border" />
                @else
                    <p class="text-sm text-gray-500">Transaction Number:</p>
                    <p class="font-mono font-bold text-lg mt-1">{{ $payment->proof_value }}</p>
                @endif
            </div>

            {{-- Actions — only show if pending --}}
            @if ($payment->status->value === 'pending')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Decision</h3>

                    {{-- Confirm --}}
                    <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}">
                        @csrf
                        <x-primary-button class="w-full justify-center bg-green-600 hover:bg-green-700">
                            ✓ Confirm Payment — Mark Ticket as Sold
                        </x-primary-button>
                    </form>

                    {{-- Reject --}}
                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                        @csrf
                        <div class="mb-3">
                            <x-input-label for="notes" value="Rejection Reason (optional)" />
                            <textarea id="notes" name="notes" rows="2"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"
                                      placeholder="e.g. Amount does not match, unclear screenshot..."></textarea>
                        </div>
                        <x-danger-button class="w-full justify-center">
                            ✗ Reject Payment — Release Ticket
                        </x-danger-button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>