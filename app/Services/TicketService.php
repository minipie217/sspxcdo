<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function reserve(
        Ticket  $ticket,
        int     $sponsorId,
        bool    $useOther,
        ?string $firstName,
        ?string $lastName
    ): bool {
        return DB::transaction(function () use ($ticket, $sponsorId, $useOther, $firstName, $lastName) {

            $updated = Ticket::where('id', $ticket->id)
                ->where('status', TicketStatus::Available)
                ->update([
                    'status'            => TicketStatus::Reserved,
                    'sponsor_id'        => $sponsorId,
                    'reserved_until'    => now()->addMinutes(10),
                    'holder_first_name' => $useOther ? $firstName : null,
                    'holder_last_name'  => $useOther ? $lastName  : null,
                    'updated_at'        => now(),
                ]);

            return (bool) $updated;
        });
    }

    public function cancel(Ticket $ticket, int $sponsorId): bool
    {
        return DB::transaction(function () use ($ticket, $sponsorId) {

            $updated = Ticket::where('id', $ticket->id)
                ->where('sponsor_id', $sponsorId)
                ->where('status', TicketStatus::Reserved)
                ->update([
                    'status'            => TicketStatus::Available,
                    'sponsor_id'        => null,
                    'reserved_until'    => null,
                    'holder_first_name' => null,
                    'holder_last_name'  => null,
                    'updated_at'        => now(),
                ]);

            return (bool) $updated;
        });
    }
}