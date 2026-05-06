<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\ProofType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPayment extends Model
{
    protected $fillable = [
        'ticket_id',
        'sponsor_id',
        'proof_type',
        'proof_value',
        'status',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'proof_type'   => ProofType::class,
        'status'       => PaymentStatus::class,
        'confirmed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}