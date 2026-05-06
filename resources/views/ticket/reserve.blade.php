<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reserve Ticket
            </h2>
            <a href="{{ route('ticket.index', $raffle) }}">
                <x-secondary-button>← Back to Tickets</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Ticket summary --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Raffle</p>
                    <p class="font-semibold text-gray-800">{{ $raffle->title }}</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mt-3">Ticket Number</p>
                    <p class="font-mono font-bold text-3xl text-indigo-600">{{ $ticket->ticket_number }}</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mt-3">Price</p>
                    <p class="font-semibold text-gray-800">₱{{ number_format($raffle->ticket_price, 2) }}</p>
                </div>

                <form method="POST"
                      action="{{ route('ticket.reserve', [$raffle, $ticket]) }}">
                    @csrf

                    {{-- Use different name --}}
                    <div class="mb-4 flex items-center gap-3">
                        <input type="checkbox" name="use_other_name" id="use_other_name"
                               value="1"
                               class="rounded border-gray-300 text-indigo-600"
                               {{ old('use_other_name') ? 'checked' : '' }}
                               onchange="toggleHolderFields(this)">
                        <label for="use_other_name" class="text-sm text-gray-700">
                            This ticket is for someone else — use a different name
                        </label>
                    </div>

                    {{-- Holder name fields --}}
                    <div id="holder-fields"
                         style="{{ old('use_other_name') ? '' : 'display:none' }}"
                         class="mb-4 p-4 bg-indigo-50 rounded-lg space-y-4">

                        <div>
                            <x-input-label for="holder_first_name" value="First Name on Ticket" />
                            <x-text-input id="holder_first_name" name="holder_first_name"
                                          type="text" class="mt-1 block w-full"
                                          :value="old('holder_first_name')" />
                            <x-input-error :messages="$errors->get('holder_first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="holder_last_name" value="Last Name on Ticket" />
                            <x-text-input id="holder_last_name" name="holder_last_name"
                                          type="text" class="mt-1 block w-full"
                                          :value="old('holder_last_name')" />
                            <x-input-error :messages="$errors->get('holder_last_name')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mb-6">
                        This ticket will be reserved for {{ \App\Models\Setting::get('reservation_minutes', 30) }} minutes.
                        You must submit payment proof within that time.
                    </p>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ticket.index', $raffle) }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        <x-primary-button>Reserve Ticket</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleHolderFields(checkbox) {
            document.getElementById('holder-fields').style.display =
                checkbox.checked ? '' : 'none';
        }
    </script>
</x-app-layout>