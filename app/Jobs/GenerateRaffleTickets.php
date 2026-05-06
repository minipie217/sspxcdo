<?php

namespace App\Jobs;

use App\Enums\RaffleStatus;
use App\Enums\TicketStatus;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateRaffleTickets
{
    use Dispatchable;

    public function __construct(
        public Raffle $raffle,
        public string $prefix,
        public int    $digits
    ) {}

    public function handle(): void
    {
        // Count existing tickets — starts from where it left off
        $existing = Ticket::where('raffle_id', $this->raffle->id)->count();

        if ($existing >= $this->raffle->total_tickets) {
            $this->raffle->update(['status' => RaffleStatus::Active]);
            return;
        }

        $now       = now();
        $buffer    = [];
        $batchSize = 1000;

        // Starts from $existing + 1 — never duplicates
        for ($i = $existing + 1; $i <= $this->raffle->total_tickets; $i++) {
            $buffer[] = [
                'raffle_id'     => $this->raffle->id,
                'ticket_number' => $this->prefix . str_pad($i, $this->digits, '0', STR_PAD_LEFT),
                'status'        => TicketStatus::Available,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            if (count($buffer) === $batchSize) {
                Ticket::insertOrIgnore($buffer);
                $buffer = [];
            }
        }

        if ($buffer) {
            Ticket::insertOrIgnore($buffer);
        }

        $this->raffle->update(['status' => RaffleStatus::Active]);
    }
}