<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            'key'        => 'recent_updates_limit',
            'value'      => '10',
            'label'      => 'Recent Updates — Number of items to show',
            'group'      => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'recent_updates_limit')->delete();
    }
};