<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\TicketPayment;
use App\Models\hasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id',
        'sponsor_id',
        'ticket_number',
        'status',
        'holder_first_name',
        'holder_last_name',
        'reserved_until',
    ];

    protected $casts = [
        'status'         => TicketStatus::class,
        'reserved_until' => 'datetime',
    ];

    // Relations

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    // Presentation

    public function sponsorFullName(): ?string
    {
        return $this->sponsor?->fullName();
    }

    public function holderName(): ?string
    {
        if ($this->holder_first_name) {
            return trim("{$this->holder_first_name} {$this->holder_last_name}");
        }

        return $this->sponsorFullName();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            TicketStatus::Available => 'Available',
            TicketStatus::Reserved  => 'Reserved',
            TicketStatus::Sold      => 'Sold',
        };
    }
    public function payment(): HasOne
    {
        return $this->hasOne(TicketPayment::class);
    }
}