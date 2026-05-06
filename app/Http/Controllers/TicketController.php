<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReserveTicketRequest;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService) {}

    // Show all tickets for a raffle — with reserve button for sponsors
    public function index(Raffle $raffle)
    {
        $status  = request('status');
        $tickets = $raffle->tickets()
            ->with('sponsor')
            ->when($status, fn($q) => $q->where('status', $status))
            ->paginate(20)
            ->withQueryString();

        return view('ticket.index', compact('raffle', 'tickets', 'status'));
    }

    // Show single ticket + reserve form
    public function show(Raffle $raffle, Ticket $ticket)
    {
        return view('ticket.show', compact('raffle', 'ticket'));
    }

    // Reserve a ticket
    public function reserve(ReserveTicketRequest $request, Raffle $raffle, Ticket $ticket)
    {
        $sponsor  = Auth::guard('sponsor')->user();
        $useOther = $request->boolean('use_other_name');

        $reserved = $this->ticketService->reserve(
            ticket:    $ticket,
            sponsorId: $sponsor->id,
            useOther:  $useOther,
            firstName: $request->holder_first_name,
            lastName:  $request->holder_last_name,
        );

        if (! $reserved) {
            return back()->with('error', 'This ticket is no longer available.');
        }

        return redirect()
            ->route('ticket.index', $raffle)
            ->with('success', "Ticket {$ticket->ticket_number} reserved for 10 minutes!");
    }

    // Cancel a reservation
    public function cancel(Raffle $raffle, Ticket $ticket)
    {
        $sponsor = Auth::guard('sponsor')->user();

        $cancelled = $this->ticketService->cancel($ticket, $sponsor->id);

        if (! $cancelled) {
            return back()->with('error', 'Unable to cancel this reservation.');
        }

        return redirect()
            ->route('ticket.index', $raffle)
            ->with('success', "Reservation for ticket {$ticket->ticket_number} has been cancelled.");
    }
}