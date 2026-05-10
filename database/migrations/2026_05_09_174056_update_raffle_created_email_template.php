<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')
            ->where('key', 'raffle_created')
            ->update([
                'subject' => 'New Raffle: {raffle_title}',
                'body'    => '
<p>Hello <strong>{name}</strong>,</p>
<p>A new raffle is now available!</p>
<p><strong>{raffle_title}</strong></p>
<p>{raffle_description}</p>
<p>Ticket Price: <strong>₱{ticket_price}</strong></p>
<p>Draw Date: <strong>{draw_date}</strong></p>
<p>Reserve your ticket now before they run out!</p>
<br>
<p style="text-align:center;">
    <a href="{raffle_url}"
       style="display:inline-block;padding:12px 28px;background-color:#4f46e5;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;font-size:14px;">
        View Raffle → {raffle_title}
    </a>
</p>',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('key', 'raffle_created')
            ->update([
                'body'       => '<p>Hello <strong>{name}</strong>,</p>',
                'updated_at' => now(),
            ]);
    }
};