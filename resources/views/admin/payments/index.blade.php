<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            {{-- Pending payments --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="font-semibold text-gray-700">
                        Pending Payments
                        <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                            {{ $pending->total() }}
                        </span>
                    </h3>
                </div>

                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Ticket</th>
                            <th class="px-4 py-3">Raffle</th>
                            <th class="px-4 py-3">Sponsor</th>
                            <th class="px-4 py-3">Proof Type</th>
                            <th class="px-4 py-3">Submitted</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pending as $payment)
                            <tr>
                                <td class="px-4 py-3 font-mono font-semibold">
                                    {{ $payment->ticket->ticket_number }}
                                </td>
                                <td class="px-4 py-3">{{ $payment->ticket->raffle->title }}</td>
                                <td class="px-4 py-3">{{ $payment->sponsor->fullName() }}</td>
                                <td class="px-4 py-3 capitalize">{{ $payment->proof_type->value }}</td>
                                <td class="px-4 py-3">{{ $payment->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.payments.show', $payment) }}"
                                       class="text-indigo-600 hover:underline">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                    No pending payments.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $pending->links() }}</div>
            </div>

            {{-- Recent confirmed/rejected --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="font-semibold text-gray-700">Recent Decisions</h3>
                </div>

                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Ticket</th>
                            <th class="px-4 py-3">Raffle</th>
                            <th class="px-4 py-3">Sponsor</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Confirmed By</th>
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
                                <td class="px-4 py-3">{{ $payment->confirmedBy?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $payment->confirmed_at?->format('M d, Y H:i') }}</td>
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
</x-app-layout>