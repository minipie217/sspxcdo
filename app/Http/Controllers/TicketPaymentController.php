<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Notifications\ReservationExpiredNotification;
use App\Services\PaymentService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private SettingService $settingService,
    ) {}

    // -----------------------------------------------------------------------
    // Reserve a ticket inline
    // -----------------------------------------------------------------------

    public function reserve(Request $request, Raffle $raffle, Ticket $ticket)
    {
        $request->validate([
            'use_other_name'    => 'boolean',
            'holder_first_name' => 'required_if:use_other_name,1|nullable|string|max:255',
            'holder_last_name'  => 'required_if:use_other_name,1|nullable|string|max:255',
        ]);

        $sponsor  = Auth::guard('sponsor')->user();
        $useOther = $request->boolean('use_other_name');

        $reserved = $this->paymentService->reserve(
            ticket:    $ticket,
            sponsorId: $sponsor->id,
            useOther:  $useOther,
            firstName: $request->holder_first_name,
            lastName:  $request->holder_last_name,
        );

        if (! $reserved) {
            return redirect()->route('ticket.index', $raffle)
                ->with('error', "Ticket {$ticket->ticket_number} was just taken. Please choose another.");
        }

        return redirect()->route('ticket.index', $raffle)
            ->with('success', "Ticket {$ticket->ticket_number} reserved!");
    }

    // -----------------------------------------------------------------------
    // Show payment page
    // -----------------------------------------------------------------------

    public function showPayment(Raffle $raffle)
    {
        $sponsor = Auth::guard('sponsor')->user();

        $this->releaseExpired($raffle, $sponsor);

        // Only tickets with no pending payment yet
        $tickets = $raffle->tickets()
            ->where('sponsor_id', $sponsor->id)
            ->where('status', 'reserved')
            ->where('reserved_until', '>', now())
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'pending'))
            ->get();

        if ($tickets->isEmpty()) {
            return redirect()->route('ticket.index', $raffle)
                ->with('error', 'No new tickets to pay for. Check your payment status below.');
        }

        $instructions = $this->settingService->paymentInstructions();
        $minutes      = $this->settingService->reservationMinutes();

        return view('ticket.payment', compact('raffle', 'tickets', 'instructions', 'minutes'));
    }

    // -----------------------------------------------------------------------
    // Submit proof of payment
    // -----------------------------------------------------------------------

    public function submitProof(Request $request, Raffle $raffle)
    {
        $request->validate([
            'proof_type'         => 'required|in:image,transaction_number',
            'proof_image'        => 'required_if:proof_type,image|nullable|image|max:5120',
            'transaction_number' => 'required_if:proof_type,transaction_number|nullable|string|max:255',
        ]);

        $sponsor = Auth::guard('sponsor')->user();

        // Only tickets with no pending payment yet
        $tickets = $raffle->tickets()
            ->where('sponsor_id', $sponsor->id)
            ->where('status', 'reserved')
            ->where('reserved_until', '>', now())
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'pending'))
            ->get();

        if ($tickets->isEmpty()) {
            return redirect()->route('ticket.index', $raffle)
                ->with('error', 'No new tickets to submit payment for.');
        }

        $proof = $request->proof_type === 'image'
            ? $request->file('proof_image')
            : $request->transaction_number;

        $this->paymentService->submitProofForAll(
            tickets:   $tickets->all(),
            sponsorId: $sponsor->id,
            proofType: $request->proof_type,
            proof:     $proof,
        );

        return redirect()->route('ticket.index', $raffle)
            ->with('success', "Payment proof submitted for {$tickets->count()} ticket(s)! We will confirm shortly.");
    }

    // -----------------------------------------------------------------------
    // Cancel a reservation
    // -----------------------------------------------------------------------

    public function cancelReservation(Raffle $raffle, Ticket $ticket)
    {
        $sponsor = Auth::guard('sponsor')->user();

        if ($ticket->sponsor_id !== $sponsor->id || $ticket->status->value !== 'reserved') {
            return redirect()->route('ticket.index', $raffle)
                ->with('error', 'Unable to cancel this reservation.');
        }

        $ticket->update([
            'status'            => 'available',
            'sponsor_id'        => null,
            'reserved_until'    => null,
            'holder_first_name' => null,
            'holder_last_name'  => null,
        ]);

        return redirect()->route('ticket.index', $raffle)
            ->with('success', "Reservation for ticket {$ticket->ticket_number} cancelled.");
    }

    // -----------------------------------------------------------------------
    // Private — release expired reservations inline
    // -----------------------------------------------------------------------

    private function releaseExpired(Raffle $raffle, $sponsor): void
    {
        $expired = $raffle->tickets()
            ->where('sponsor_id', $sponsor->id)
            ->where('status', 'reserved')
            ->where('reserved_until', '<', now())
            ->get();

        foreach ($expired as $ticket) {
            TicketPayment::where('ticket_id', $ticket->id)
                ->where('status', 'pending')
                ->delete();

            $ticket->update([
                'status'            => 'available',
                'sponsor_id'        => null,
                'reserved_until'    => null,
                'holder_first_name' => null,
                'holder_last_name'  => null,
            ]);

            $sponsor->notify(new ReservationExpiredNotification($ticket));
        }
    }
}