<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Archived Raffles
            </h2>
            <a href="{{ route('raffle.index') }}"
               class="text-sm text-indigo-600 hover:underline">
                ← Back to Raffles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Draw Date</th>
                            <th class="px-6 py-3">Tickets</th>
                            <th class="px-6 py-3">Archived</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($raffles as $raffle)
                            <tr class="bg-gray-50 opacity-75">
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    {{ $raffle->title }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $raffle->draw_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ number_format($raffle->total_tickets) }}
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-xs">
                                    {{ $raffle->deleted_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST"
                                          action="{{ route('raffle.restore', $raffle->id) }}"
                                          class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-indigo-600 hover:underline text-sm">
                                            Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    No archived raffles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $raffles->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>