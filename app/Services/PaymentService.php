<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\User;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function __construct(private SettingService $settingService) {}

    public function reserve(
        Ticket  $ticket,
        int     $sponsorId,
        bool    $useOther,
        ?string $firstName,
        ?string $lastName
    ): bool {
        return DB::transaction(function () use ($ticket, $sponsorId, $useOther, $firstName, $lastName) {

            $minutes = $this->settingService->reservationMinutes();

            // All tickets for this sponsor get the same expiry
            $reservedUntil = now()->addMinutes($minutes);

            $updated = Ticket::where('id', $ticket->id)
                ->where('status', TicketStatus::Available)
                ->update([
                    'status'            => TicketStatus::Reserved,
                    'sponsor_id'        => $sponsorId,
                    'reserved_until'    => $reservedUntil,
                    'holder_first_name' => $useOther ? $firstName : null,
                    'holder_last_name'  => $useOther ? $lastName  : null,
                    'updated_at'        => now(),
                ]);            

            return (bool) $updated;
        });
    }

    public function submitProof(
        Ticket              $ticket,
        int                 $sponsorId,
        string              $proofType,
        string|UploadedFile $proof
    ): TicketPayment {
        return DB::transaction(function () use ($ticket, $sponsorId, $proofType, $proof) {

            $proofValue = $proofType === 'image'
                ? Storage::disk('public')->put('payment_proofs', $proof)
                : $proof;

            $payment = TicketPayment::create([
                'ticket_id'   => $ticket->id,
                'sponsor_id'  => $sponsorId,
                'proof_type'  => $proofType,
                'proof_value' => $proofValue,
                'status'      => PaymentStatus::Pending,
            ]);

            return $payment;
        });
    }

    public function submitProofForAll(
        array               $tickets,
        int                 $sponsorId,
        string              $proofType,
        string|UploadedFile $proof
    ): void {
        DB::transaction(function () use ($tickets, $sponsorId, $proofType, $proof) {

            // Store image once, reuse path for all tickets
            $proofValue = $proofType === 'image'
                ? Storage::disk('public')->put('payment_proofs', $proof)
                : $proof;

            foreach ($tickets as $ticket) {
                TicketPayment::create([
                    'ticket_id'   => $ticket->id,
                    'sponsor_id'  => $sponsorId,
                    'proof_type'  => $proofType,
                    'proof_value' => $proofValue,
                    'status'      => PaymentStatus::Pending,
                ]);
            }

            // Notify all admins once — not per ticket
            $admins = User::all();
            $firstPayment = TicketPayment::where('sponsor_id', $sponsorId)
                ->where('status', PaymentStatus::Pending)
                ->latest()
                ->first();

            Notification::send($admins, new PaymentReceivedNotification($firstPayment));
        });
    }

    public function confirm(TicketPayment $payment, int $adminId): void
    {
        DB::transaction(function () use ($payment, $adminId) {

            $payment->update([
                'status'       => PaymentStatus::Confirmed,
                'confirmed_by' => $adminId,
                'confirmed_at' => now(),
            ]);

            $payment->ticket->update([
                'status' => TicketStatus::Sold,
            ]);

            $payment->sponsor->notify(new PaymentConfirmedNotification($payment));
        });
    }

    public function reject(TicketPayment $payment, int $adminId, ?string $notes = null): void
    {
        DB::transaction(function () use ($payment, $adminId, $notes) {

            $payment->update([
                'status'       => PaymentStatus::Rejected,
                'confirmed_by' => $adminId,
                'confirmed_at' => now(),
                'notes'        => $notes,
            ]);

            // Release ticket immediately on rejection
            $payment->ticket->update([
                'status'            => TicketStatus::Available,
                'sponsor_id'        => null,
                'reserved_until'    => null,
                'holder_first_name' => null,
                'holder_last_name'  => null,
            ]);

            $payment->sponsor->notify(new PaymentRejectedNotification($payment, $notes));
        });
    }
}