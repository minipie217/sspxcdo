<?php

namespace App\Services;

use App\Enums\RaffleStatus;
use App\Jobs\GenerateRaffleTickets;
use App\Models\Raffle;
use App\Models\RafflePrize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RaffleService
{
    public function createRaffle(array $data): Raffle
    {
        return DB::transaction(function () use ($data) {

            $raffle = Raffle::create([
                'created_by'    => Auth::guard('web')->id(),
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'ticket_price'  => $data['ticket_price'],
                'total_tickets' => $data['total_tickets'],
                'start_date'    => $data['start_date'] ?? null,
                'end_date'      => $data['end_date'] ?? null,
                'draw_date'     => $data['draw_date'],
                'status'        => $data['status'],
            ]);

            $this->createPrizes($raffle, $data);

            if ($raffle->status === RaffleStatus::Active) {
                $raffle->update(['status' => RaffleStatus::Generating]);

                GenerateRaffleTickets::dispatch(
                    $raffle,
                    strtoupper(trim($data['ticket_prefix'] ?? '')),
                    max((int) $data['ticket_digits'], strlen((string) $data['total_tickets']))
                );
            }

            return $raffle;
        });
    }

    public function updateRaffle(Raffle $raffle, array $data): Raffle
    {
        return DB::transaction(function () use ($raffle, $data) {

            $wasNotActive    = $raffle->status !== RaffleStatus::Active;
            $isNowActive     = $data['status'] === 'active';
            $newTotalTickets = (int) $data['total_tickets'];

            // delete() not forceDelete() — RafflePrize has no SoftDeletes
            $raffle->prizes()->delete();

            $raffle->update([
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'ticket_price'  => $data['ticket_price'],
                'total_tickets' => $newTotalTickets,
                'start_date'    => $data['start_date'] ?? null,
                'end_date'      => $data['end_date'] ?? null,
                'draw_date'     => $data['draw_date'],
                'status'        => $data['status'],
            ]);

            $this->createPrizes($raffle, $data);

            $currentTicketCount = $raffle->tickets()->count();
            $needsMoreTickets   = $newTotalTickets > $currentTicketCount;

            $prefix = strtoupper(trim($data['ticket_prefix'] ?? ''));
            $digits = max((int) ($data['ticket_digits'] ?? 4), strlen((string) $newTotalTickets));

            // Case 1 — just activated, no tickets yet
            if ($wasNotActive && $isNowActive && $currentTicketCount === 0) {
                $raffle->update(['status' => RaffleStatus::Generating]);
                GenerateRaffleTickets::dispatch($raffle, $prefix, $digits);

            // Case 2 — already active, total tickets increased
            } elseif ($isNowActive && $needsMoreTickets && $currentTicketCount > 0) {
                GenerateRaffleTickets::dispatch($raffle, $prefix, $digits);
            }

            return $raffle->fresh();
        });
    }

    public function deleteRaffle(Raffle $raffle): void
    {
        DB::transaction(function () use ($raffle) {
            $raffle->tickets()->delete();
            $raffle->prizes()->delete();
            $raffle->delete();
        });
    }

    private function createPrizes(Raffle $raffle, array $data): void
    {
        // Main prizes
        foreach ([
            ['type' => 'first',  'position' => 1, 'key' => 'first_prize',  'amount_key' => 'first_prize_amount'],
            ['type' => 'second', 'position' => 2, 'key' => 'second_prize', 'amount_key' => 'second_prize_amount'],
            ['type' => 'third',  'position' => 3, 'key' => 'third_prize',  'amount_key' => 'third_prize_amount'],
        ] as $prize) {
            RafflePrize::create([
                'raffle_id' => $raffle->id,
                'type'      => $prize['type'],
                'position'  => $prize['position'],
                'name'      => $data[$prize['key']],
                'prize'     => $data[$prize['amount_key']] ?? null,
            ]);
        }

        // Consolation prizes
        $consolationCount  = (int) ($data['consolation_count'] ?? 0);
        $consolationName   = trim($data['consolation_name'] ?? '') ?: 'Consolation Prize';
        $consolationAmount = $data['consolation_amount'] ?? null;

        if ($consolationCount > 0) {
            $now    = now();
            $buffer = [];

            for ($i = 1; $i <= $consolationCount; $i++) {
                $buffer[] = [
                    'raffle_id'  => $raffle->id,
                    'type'       => 'consolation',
                    'position'   => null,
                    'name'       => "{$consolationName} #{$i}",
                    'prize'      => $consolationAmount,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            RafflePrize::insert($buffer);
        }
    }
}