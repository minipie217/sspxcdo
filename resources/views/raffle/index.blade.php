<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Raffles
            </h2>
            @if (Auth::guard('web')->check())
                <a href="{{ route('raffle.create') }}">
                    <x-primary-button>New Raffle</x-primary-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded">
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
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($raffles as $raffle)
                            @if ($raffle->status->value !== 'draft' || Auth::guard('web')->check())
                                <tr>
                                    <td class="px-6 py-4 font-medium">{{ $raffle->title }}</td>
                                    <td class="px-6 py-4">{{ $raffle->draw_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">{{ number_format($raffle->total_tickets) }}</td>
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                                            'bg-gray-100 text-gray-600'   => $raffle->status->value === 'draft',
                                            'bg-blue-100 text-blue-700'   => $raffle->status->value === 'generating',
                                            'bg-green-100 text-green-700' => $raffle->status->value === 'active',
                                            'bg-red-100 text-red-700'     => $raffle->status->value === 'closed',
                                        ])>
                                            {{ $raffle->status->value }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('raffle.show', $raffle) }}"
                                           class="text-indigo-600 hover:underline">View</a>

                                        @if (Auth::guard('web')->check())
                                            <a href="{{ route('raffle.edit', $raffle) }}"
                                               class="text-yellow-600 hover:underline">Edit</a>
                                            <form method="POST"
                                                  action="{{ route('raffle.destroy', $raffle) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('Delete this raffle?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                                    No raffles yet.
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