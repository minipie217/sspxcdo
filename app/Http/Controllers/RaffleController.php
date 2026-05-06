<?php

namespace App\Http\Controllers;

use App\Enums\RaffleStatus;
use App\Http\Requests\StoreRaffleRequest;
use App\Http\Requests\UpdateRaffleRequest;
use App\Models\Raffle;
use App\Services\RaffleService;
use Illuminate\Support\Facades\Auth;

class RaffleController extends Controller
{
    public function __construct(private RaffleService $raffleService) {}

    public function index()
    {
        $raffles = Raffle::with('prizes')
        ->when(! Auth::guard('web')->check(), fn($q) => $q->where('status', '!=', 'draft'))
        ->latest()
        ->paginate(10);

        return view('raffle.index', compact('raffles'));
    }

    public function create()
    {
        return view('raffle.create');
    }

    public function store(StoreRaffleRequest $request)
    {
        $raffle = $this->raffleService->createRaffle($request->validated());

        return redirect()->route('raffle.show', $raffle)
            ->with('success', $raffle->status->value === 'generating'
                ? 'Raffle created! Tickets are being generated.'
                : 'Raffle created successfully!'
            );
    }

    public function show(Raffle $raffle)
    {
        // Sponsors and guests cannot see draft raffles
        if (! Auth::guard('web')->check() && $raffle->status === RaffleStatus::Draft) {
            abort(404);
        }

        $raffle->load('prizes');

        $status  = request('status');
        $tickets = $raffle->tickets()
            ->with('sponsor')
            ->when($status, fn($q) => $q->where('status', $status))
            ->paginate(20)
            ->withQueryString();

        return view('raffle.show', compact('raffle', 'tickets', 'status'));
    }

    public function edit(Raffle $raffle)
    {
        $raffle->load('prizes');

        return view('raffle.edit', compact('raffle'));
    }

    public function update(UpdateRaffleRequest $request, Raffle $raffle)
    {
        $raffle = $this->raffleService->updateRaffle($raffle, $request->validated());

        return redirect()->route('raffle.show', $raffle)
            ->with('success', 'Raffle updated successfully!');
    }

    public function destroy(Raffle $raffle)
    {
        $this->raffleService->deleteRaffle($raffle);

        return redirect()->route('raffle.index')
            ->with('success', 'Raffle deleted.');
    }
}