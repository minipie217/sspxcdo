<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RafflePrize extends Model
{
    protected $fillable = [
        'raffle_id',
        'type',
        'position',
        'name',
        'prize',
    ];

    protected $casts = [
        'prize' => 'decimal:2',
    ];

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }
}