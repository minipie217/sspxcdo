<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Complete Payment
            </h2>
            <a href="{{ route('ticket.index', $raffle) }}">
                <x-secondary-button>← Reserve More Tickets</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Reserved tickets summary --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">
                    Reserved Tickets (<span id="ticket-count">{{ $tickets->count() }}</span>)
                </h3>

                <table class="w-full text-sm">
                    <thead class="text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="text-left py-2">Ticket No.</th>
                            <th class="text-left py-2">Holder</th>
                            <th class="text-right py-2">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td class="py-3">
                                    <p class="font-mono font-bold text-gray-800">
                                        {{ $ticket->ticket_number }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Expires
                                        <span class="ticket-countdown font-mono"
                                              data-expires="{{ $ticket->reserved_until->toIso8601String() }}">
                                        </span>
                                    </p>
                                </td>
                                <td class="py-3 text-gray-600">
                                    {{ $ticket->holderName() ?? Auth::guard('sponsor')->user()->fullName() }}
                                </td>
                                <td class="py-3 text-right">
                                    ₱{{ number_format($raffle->ticket_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200">
                        <tr>
                            <td colspan="2" class="py-3 font-semibold">Total</td>
                            <td class="py-3 text-right font-bold text-indigo-600 text-lg"
                                id="payment-total">
                                ₱{{ number_format($raffle->ticket_price * $tickets->count(), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payment instructions --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6" id="payment-instructions">
                <h3 class="font-semibold text-gray-700 mb-3">Payment Instructions</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Send exactly
                    <strong id="payment-amount">
                        ₱{{ number_format($raffle->ticket_price * $tickets->count(), 2) }}
                    </strong>
                    to any of the following:
                </p>

                <div class="space-y-3">
                    @forelse ($instructions as $account)
                        <div class="p-4 border rounded-lg">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-gray-700">
                                        {{ $account['icon'] }} {{ $account['label'] }}
                                    </p>
                                    @if ($account['account_name'])
                                        <p class="text-sm text-gray-500 mt-1">
                                            Account Name:
                                            <span class="font-medium text-gray-800">
                                                {{ $account['account_name'] }}
                                            </span>
                                        </p>
                                    @endif
                                    @if ($account['account_number'])
                                        <p class="text-sm text-gray-500">
                                            Account Number:
                                            <span class="font-mono font-bold text-gray-800">
                                                {{ $account['account_number'] }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                                @if ($account['qr_code'])
                                    <img src="{{ Storage::url($account['qr_code']) }}"
                                        alt="{{ $account['label'] }} QR"
                                        class="h-32 w-32 md:h-48 md:w-48 object-contain border rounded-lg p-2 bg-white shrink-0" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No payment accounts configured yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Proof of payment --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6" id="payment-proof">
                <h3 class="font-semibold text-gray-700 mb-2">Submit Payment Proof</h3>
                <p class="text-sm text-gray-500 mb-4" id="proof-label">
                    One proof covers all {{ $tickets->count() }} ticket(s).
                </p>

                <form method="POST"
                      action="{{ route('ticket.proof', $raffle) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label value="Proof Type" />
                        <div class="mt-2 flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="proof_type"
                                       value="transaction_number"
                                       class="text-indigo-600"
                                       {{ old('proof_type', 'transaction_number') === 'transaction_number' ? 'checked' : '' }}
                                       onchange="toggleProofFields(this.value)">
                                <span class="text-sm text-gray-700">Transaction Number</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="proof_type"
                                       value="image"
                                       class="text-indigo-600"
                                       {{ old('proof_type') === 'image' ? 'checked' : '' }}
                                       onchange="toggleProofFields(this.value)">
                                <span class="text-sm text-gray-700">Upload Screenshot</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('proof_type')" class="mt-2" />
                    </div>

                    <div id="field-transaction_number"
                         style="{{ old('proof_type') === 'image' ? 'display:none' : '' }}">
                        <x-input-label for="transaction_number" value="Transaction / Reference Number" />
                        <x-text-input id="transaction_number" name="transaction_number"
                                      type="text" class="mt-1 block w-full"
                                      :value="old('transaction_number')"
                                      placeholder="e.g. 1234567890" />
                        <x-input-error :messages="$errors->get('transaction_number')" class="mt-2" />
                    </div>

                    <div id="field-image"
                         style="{{ old('proof_type') === 'image' ? '' : 'display:none' }}">
                        <x-input-label for="proof_image" value="Upload Screenshot" />
                        <input type="file" id="proof_image" name="proof_image"
                               accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4 file:rounded-md
                                      file:border-0 file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100" />
                        <p class="text-xs text-gray-400 mt-1">Max 5MB. JPG, PNG accepted.</p>
                        <x-input-error :messages="$errors->get('proof_image')" class="mt-2" />
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>Submit Payment Proof</x-primary-button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        function updateCountdowns() {
            document.querySelectorAll('.ticket-countdown').forEach(el => {
                const expiresAt = new Date(el.dataset.expires);
                const diff      = Math.floor((expiresAt - new Date()) / 1000);

                if (diff <= 0) {
                    const row = el.closest('tr');
                    if (row) {
                        row.remove();
                        recalculateTotal();
                    }

                    const remaining = document.querySelectorAll('.ticket-countdown').length;
                    if (remaining === 0) {
                        document.getElementById('payment-instructions').style.display = 'none';
                        document.getElementById('payment-proof').style.display        = 'none';
                        setTimeout(() => {
                            window.location.href = "{{ route('ticket.index', $raffle) }}";
                        }, 2000);
                    }
                    return;
                }

                const mins = Math.floor(diff / 60).toString().padStart(2, '0');
                const secs = (diff % 60).toString().padStart(2, '0');
                el.textContent = `in ${mins}:${secs}`;

                if (diff < 300) {
                    el.classList.add('text-red-500', 'font-semibold');
                    el.classList.remove('text-gray-400');
                } else {
                    el.classList.remove('text-red-500', 'font-semibold');
                    el.classList.add('text-gray-400');
                }
            });
        }

        function recalculateTotal() {
            const ticketPrice = {{ $raffle->ticket_price }};
            const remaining   = document.querySelectorAll('.ticket-countdown').length;
            const total       = ticketPrice * remaining;
            const formatted   = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2 });

            document.getElementById('ticket-count').textContent   = remaining;
            document.getElementById('payment-total').textContent  = formatted;
            document.getElementById('payment-amount').textContent = formatted;

            const proofLabel = document.getElementById('proof-label');
            if (proofLabel) {
                proofLabel.textContent = `One proof covers all ${remaining} ticket(s).`;
            }
        }

        updateCountdowns();
        setInterval(updateCountdowns, 1000);

        function toggleProofFields(type) {
            document.getElementById('field-transaction_number').style.display =
                type === 'transaction_number' ? '' : 'none';
            document.getElementById('field-image').style.display =
                type === 'image' ? '' : 'none';
        }
    </script>
</x-app-layout>