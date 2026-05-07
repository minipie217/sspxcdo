<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Raffle
            </h2>
            <a href="{{ route('raffle.show', $raffle) }}">
                <x-secondary-button>← Back</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('raffle.update', $raffle) }}">
                    @csrf
                    @method('PUT')

                    {{-- -------------------------------------------------- --}}
                    {{-- Raffle Details --}}
                    {{-- -------------------------------------------------- --}}
                    <h3 class="font-semibold text-gray-700 mb-4">Raffle Details</h3>

                    <div class="mb-4">
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('title', $raffle->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $raffle->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                            <option value="draft"  @selected(old('status', $raffle->status->value) === 'draft')>Draft</option>
                            <option value="active" @selected(old('status', $raffle->status->value) === 'active')>Active — tickets will be generated if not yet created</option>
                            <option value="closed" @selected(old('status', $raffle->status->value) === 'closed')>Closed</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <hr class="my-6">

                    {{-- -------------------------------------------------- --}}
                    {{-- Schedule --}}
                    {{-- -------------------------------------------------- --}}
                    <h3 class="font-semibold text-gray-700 mb-4">Schedule</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <x-input-label for="start_date" value="Start Date" />
                            <x-text-input id="start_date" name="start_date" type="date"
                                          class="mt-1 block w-full"
                                          :value="old('start_date', $raffle->start_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="End Date" />
                            <x-text-input id="end_date" name="end_date" type="date"
                                          class="mt-1 block w-full"
                                          :value="old('end_date', $raffle->end_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="draw_date" value="Draw Date" />
                            <x-text-input id="draw_date" name="draw_date" type="date"
                                          class="mt-1 block w-full"
                                          :value="old('draw_date', $raffle->draw_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('draw_date')" class="mt-2" />
                        </div>
                    </div>

                    <hr class="my-6">

                    {{-- -------------------------------------------------- --}}
                    {{-- Ticket Configuration --}}
                    {{-- -------------------------------------------------- --}}
                    <h3 class="font-semibold text-gray-700 mb-4">Ticket Configuration</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="ticket_price" value="Ticket Price (₱)" />
                            <x-text-input id="ticket_price" name="ticket_price" type="number"
                                          step="0.01" min="0"
                                          class="mt-1 block w-full"
                                          :value="old('ticket_price', $raffle->ticket_price)" required />
                            <x-input-error :messages="$errors->get('ticket_price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="total_tickets" value="Total Tickets" />
                            <x-text-input id="total_tickets" name="total_tickets" type="number"
                                          min="1" max="100000"
                                          class="mt-1 block w-full"
                                          :value="old('total_tickets', $raffle->total_tickets)" required />
                            <x-input-error :messages="$errors->get('total_tickets')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="ticket_prefix" value="Ticket Prefix (optional)" />
                            <x-text-input id="ticket_prefix" name="ticket_prefix" type="text"
                                          class="mt-1 block w-full"
                                          :value="old('ticket_prefix')"
                                          placeholder="e.g. TKT-" />
                            <p class="text-xs text-gray-400 mt-1">e.g. TKT-TKT0001</p>
                            <x-input-error :messages="$errors->get('ticket_prefix')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="ticket_digits" value="Ticket Number Digits" />
                            <x-text-input id="ticket_digits" name="ticket_digits" type="number"
                                          min="1" max="10"
                                          class="mt-1 block w-full"
                                          :value="old('ticket_digits', 4)" required />
                            <p class="text-xs text-gray-400 mt-1">e.g. 4 digits → 0001</p>
                            <x-input-error :messages="$errors->get('ticket_digits')" class="mt-2" />
                        </div>
                    </div>

                    <hr class="my-6">

                    {{-- -------------------------------------------------- --}}
                    {{-- Prizes --}}
                    {{-- -------------------------------------------------- --}}
                    <h3 class="font-semibold text-gray-700 mb-4">Prizes</h3>

                    @php
                        $firstPrize       = $raffle->prizes->firstWhere('type', 'first');
                        $secondPrize      = $raffle->prizes->firstWhere('type', 'second');
                        $thirdPrize       = $raffle->prizes->firstWhere('type', 'third');
                        $consolationPrize = $raffle->prizes->firstWhere('type', 'consolation');
                        $consolationCount = $raffle->prizes->where('type', 'consolation')->count();
                    @endphp

                    {{-- 1st prize --}}
                    <div class="mb-4">
                        <x-input-label value="1st Prize" />
                        <div class="grid grid-cols-2 gap-3 mt-1">
                            <div>
                                <x-text-input name="first_prize" type="text"
                                            class="block w-full"
                                            :value="old('first_prize', $firstPrize?->name)"
                                            placeholder="Prize name" required />
                                <x-input-error :messages="$errors->get('first_prize')" class="mt-1" />
                            </div>
                            <div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">₱</span>
                                    <x-text-input name="first_prize_amount" type="number"
                                                step="0.01" min="0"
                                                class="block w-full pl-7"
                                                :value="old('first_prize_amount', $firstPrize?->prize)"
                                                placeholder="Amount (optional)" />
                                </div>
                                <x-input-error :messages="$errors->get('first_prize_amount')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- 2nd prize --}}
                    <div class="mb-4">
                        <x-input-label value="2nd Prize" />
                        <div class="grid grid-cols-2 gap-3 mt-1">
                            <div>
                                <x-text-input name="second_prize" type="text"
                                            class="block w-full"
                                            :value="old('second_prize', $secondPrize?->name)"
                                            placeholder="Prize name" required />
                                <x-input-error :messages="$errors->get('second_prize')" class="mt-1" />
                            </div>
                            <div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">₱</span>
                                    <x-text-input name="second_prize_amount" type="number"
                                                step="0.01" min="0"
                                                class="block w-full pl-7"
                                                :value="old('second_prize_amount', $secondPrize?->prize)"
                                                placeholder="Amount (optional)" />
                                </div>
                                <x-input-error :messages="$errors->get('second_prize_amount')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- 3rd prize --}}
                    <div class="mb-4">
                        <x-input-label value="3rd Prize" />
                        <div class="grid grid-cols-2 gap-3 mt-1">
                            <div>
                                <x-text-input name="third_prize" type="text"
                                            class="block w-full"
                                            :value="old('third_prize', $thirdPrize?->name)"
                                            placeholder="Prize name" required />
                                <x-input-error :messages="$errors->get('third_prize')" class="mt-1" />
                            </div>
                            <div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">₱</span>
                                    <x-text-input name="third_prize_amount" type="number"
                                                step="0.01" min="0"
                                                class="block w-full pl-7"
                                                :value="old('third_prize_amount', $thirdPrize?->prize)"
                                                placeholder="Amount (optional)" />
                                </div>
                                <x-input-error :messages="$errors->get('third_prize_amount')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Consolation prizes --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <x-input-label for="consolation_count" value="Consolation Count" />
                            <x-text-input id="consolation_count" name="consolation_count"
                                        type="number" min="0" max="1000"
                                        class="mt-1 block w-full"
                                        :value="old('consolation_count', $consolationCount)" />
                            <x-input-error :messages="$errors->get('consolation_count')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="consolation_name" value="Consolation Prize Name" />
                            <x-text-input id="consolation_name" name="consolation_name"
                                        type="text" class="mt-1 block w-full"
                                        :value="old('consolation_name', $consolationPrize
                                            ? preg_replace('/ #\d+$/', '', $consolationPrize->name)
                                            : 'Consolation Prize')" />
                            <x-input-error :messages="$errors->get('consolation_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="consolation_amount" value="Consolation Prize Amount" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">₱</span>
                                <x-text-input id="consolation_amount" name="consolation_amount"
                                            type="number" step="0.01" min="0"
                                            class="block w-full pl-7"
                                            :value="old('consolation_amount', $consolationPrize?->prize)"
                                            placeholder="Amount (optional)" />
                            </div>
                            <x-input-error :messages="$errors->get('consolation_amount')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('raffle.show', $raffle) }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        <x-primary-button>Update Raffle</x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>