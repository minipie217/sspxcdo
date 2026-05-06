<?php

namespace App\Models;

use App\Enums\RaffleStatus;
use App\Enums\TicketStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raffle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'ticket_price',
        'total_tickets',
        'start_date',
        'end_date',
        'draw_date',
        'status',
    ];

    protected $casts = [
        'status'     => RaffleStatus::class,
        'start_date' => 'date',
        'end_date'   => 'date',
        'draw_date'  => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($raffle) {
            $slug = Str::slug($raffle->title);

            $count = self::where('slug', 'like', "{$slug}%")->count();

            $raffle->slug = $count ? "{$slug}-{$count}" : $slug;
        });
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    // Relations

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(RafflePrize::class)->orderBy('position');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function availableTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->where('status', TicketStatus::Available);
    }

    public function reservedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->where('status', TicketStatus::Reserved);
    }

    public function soldTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->where('status', TicketStatus::Sold);
    }
}