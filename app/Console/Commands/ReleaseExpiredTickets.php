<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
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
            if ($ticket->sponsor) {
                $ticket->sponsor->notify(new ReservationExpiredNotification($ticket));
            }
            $ticket->update([
                'status'         => TicketStatus::Available,
                'sponsor_id'     => null,
                'reserved_until' => null,
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