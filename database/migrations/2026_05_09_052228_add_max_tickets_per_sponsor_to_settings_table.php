<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            'key'        => 'max_tickets_per_sponsor',
            'value'      => '5',
            'label'      => 'Maximum Tickets Per Sponsor',
            'group'      => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'max_tickets_per_sponsor')->delete();
    }
};