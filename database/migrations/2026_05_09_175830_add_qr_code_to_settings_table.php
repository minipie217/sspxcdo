<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add QR code settings for each payment method
        $now    = now();
        $groups = ['bdo', 'bpi', 'metrobank', 'unionbank', 'gcash', 'maya', 'other'];

        foreach ($groups as $group) {
            DB::table('settings')->insert([
                'key'        => "{$group}_qr_code",
                'value'      => null,
                'label'      => strtoupper($group) . ' QR Code',
                'group'      => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $groups = ['bdo', 'bpi', 'metrobank', 'unionbank', 'gcash', 'maya', 'other'];
        foreach ($groups as $group) {
            DB::table('settings')->where('key', "{$group}_qr_code")->delete();
        }
    }
};