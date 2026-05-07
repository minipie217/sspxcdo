<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Notifications\ReservationExpiredNotification;
use Illuminate\Console\Command;

class ReleaseExpiredTickets extends Command
{
    protected $signature   = 'tickets:release-expired';
    protected $description = 'Release reserved tickets whose reservation window has lapsed';

    public function handle(): void
    {
        $expired = Ticket::with('sponsor')
            ->where('status', TicketStatus::Reserved)
            ->where('reserved_until', '<', now())
            ->get();

        foreach ($expired as $ticket) {
            // Notify sponsor before releasing
            if ($ticket->sponsor) {
                $ticket->sponsor->notify(new ReservationExpiredNotification($ticket));
            }

            // Delete any pending payments for this ticket
            // so it disappears from the admin pending list
            TicketPayment::where('ticket_id', $ticket->id)
                ->where('status', 'pending')
                ->delete();

            $ticket->update([
                'status'         => TicketStatus::Available,
                'sponsor_id'     => null,
                'reserved_until' => null,
                'holder_first_name' => null,
                'holder_last_name' => null,
            ]);            
        }
        $released = Ticket::where('status', TicketStatus::Reserved)
            ->where('reserved_until', '<', now())
            ->update([
                'status'         => TicketStatus::Available,
                'sponsor_id'     => null,
                'reserved_until' => null,
                'updated_at'     => now(),
            ]);

        $this->info("Released {$released} expired ticket(s).");
    }
}