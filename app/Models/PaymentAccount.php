<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'type',
        'label',
        'account_name',
        'account_number',
        'qr_code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Type labels for display
    public static function typeLabels(): array
    {
        return [
            'bdo'       => 'BDO',
            'bpi'       => 'BPI',
            'metrobank' => 'Metrobank',
            'unionbank' => 'UnionBank',
            'gcash'     => 'GCash',
            'maya'      => 'Maya',
            'other'     => 'Other',
        ];
    }

    public function typeLabel(): string
    {
        return static::typeLabels()[$this->type] ?? ucfirst($this->type);
    }

    public function typeIcon(): string
    {
        return match($this->type) {
            'gcash', 'maya' => '📱',
            'other'         => '💳',
            default         => '🏦',
        };
    }
}