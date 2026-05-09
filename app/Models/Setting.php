<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'label',
        'group',
        'site_name',
        'logo',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->label ??= str($key)->replace('_', ' ')->title();
        $setting->group ??= 'general';
        $setting->save();
    }
}
