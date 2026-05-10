<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')->insert([
            'key'        => 'raffle_created',
            'label'      => 'New Raffle Announcement',
            'subject'    => 'New Raffle: {raffle_title}',
            'body'       => '<p>Hello <strong>{name}</strong>,</p><p>A new raffle is now available!</p><p><strong>{raffle_title}</strong></p><p>{raffle_description}</p><p>Ticket Price: <strong>₱{ticket_price}</strong></p><p>Draw Date: <strong>{draw_date}</strong></p><p>Reserve your ticket now before they run out!</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')->where('key', 'raffle_created')->delete();
    }
};