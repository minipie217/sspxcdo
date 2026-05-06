<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Sponsor extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'otp',
        'otp_expires_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'otp',
        'remember_token',
    ];

    protected $casts = [
        'otp_expires_at'    => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Relations

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Presentation

    public function fullName(): string
    {
        return trim("{$this->title} {$this->first_name} {$this->last_name}");
    }
}