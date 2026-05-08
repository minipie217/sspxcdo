<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insert([
            [
                'key'        => 'site_name',
                'value'      => 'My Raffle Site',
                'label'      => 'Website Name',
                'group'      => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'site_logo',
                'value'      => null,
                'label'      => 'Site Logo',
                'group'      => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['site_name', 'site_logo'])->delete();
    }
};