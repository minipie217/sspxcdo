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
                    Reserved Tickets ({{ $tickets->count() }})
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
                            <td class="py-3 text-right font-bold text-indigo-600 text-lg">
                                ₱{{ number_format($raffle->ticket_price * $tickets->count(), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payment instructions --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Payment Instructions</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Send exactly <strong>₱{{ number_format($raffle->ticket_price * $tickets->count(), 2) }}</strong>
                    to any of the following:
                </p>

                <div class="space-y-3">
                    @foreach (['bdo', 'bpi', 'metrobank', 'unionbank'] as $bank)
                        @if ($instructions[$bank]['account_number'])
                            <div class="p-4 border rounded-lg">
                                <p class="font-semibold text-gray-700">{{ $instructions[$bank]['label'] }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Account Name:
                                    <span class="font-medium text-gray-800">{{ $instructions[$bank]['account_name'] }}</span>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Account Number:
                                    <span class="font-mono font-bold text-gray-800">{{ $instructions[$bank]['account_number'] }}</span>
                                </p>
                            </div>
                        @endif
                    @endforeach

                    @foreach (['gcash', 'maya'] as $wallet)
                        @if ($instructions[$wallet]['number'])
                            <div class="p-4 border rounded-lg">
                                <p class="font-semibold text-gray-700">{{ $instructions[$wallet]['label'] }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Name:
                                    <span class="font-medium text-gray-800">{{ $instructions[$wallet]['name'] }}</span>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Number:
                                    <span class="font-mono font-bold text-gray-800">{{ $instructions[$wallet]['number'] }}</span>
                                </p>
                            </div>
                        @endif
                    @endforeach

                    @if ($instructions['other']['label'])
                        <div class="p-4 border rounded-lg">
                            <p class="font-semibold text-gray-700">{{ $instructions['other']['label'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $instructions['other']['details'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Proof of payment --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-2">Submit Payment Proof</h3>
                <p class="text-sm text-gray-500 mb-4">
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
        // Per-ticket countdown
        function updateCountdowns() {
            let anyExpired = false;

            document.querySelectorAll('.ticket-countdown').forEach(el => {
                const expiresAt = new Date(el.dataset.expires);
                const diff      = Math.max(0, Math.floor((expiresAt - new Date()) / 1000));

                if (diff === 0) {
                    el.textContent = 'Expired';
                    el.classList.add('text-red-500', 'font-semibold');
                    anyExpired = true;
                    return;
                }

                const mins = Math.floor(diff / 60).toString().padStart(2, '0');
                const secs = (diff % 60).toString().padStart(2, '0');
                el.textContent = `in ${mins}:${secs}`;

                // Turn red when under 5 minutes
                if (diff < 300) {
                    el.classList.add('text-red-500', 'font-semibold');
                    el.classList.remove('text-gray-400');
                } else {
                    el.classList.add('text-gray-400');
                }
            });

            // Redirect only if ALL tickets have expired
            const allCountdowns = document.querySelectorAll('.ticket-countdown');
            const expiredCount  = [...allCountdowns].filter(el => el.textContent === 'Expired').length;

            if (expiredCount === allCountdowns.length) {
                setTimeout(() => {
                    window.location.href = "{{ route('ticket.index', $raffle) }}";
                }, 2000);
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