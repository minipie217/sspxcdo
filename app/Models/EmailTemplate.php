<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'label',
        'subject',
        'body',
    ];

    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    // Replace placeholders with actual values
    public function render(array $data): array
    {
        $subject = $this->subject;
        $body    = $this->body;

        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
            $body    = str_replace("{{$key}}", $value, $body);
        }

        return compact('subject', 'body');
    }
}