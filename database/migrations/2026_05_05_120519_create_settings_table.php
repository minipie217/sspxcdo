<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('group')->default('general');
            $table->timestamps();

            $table->index('group');
        });

        // Seed default settings
        $now = now();
        DB::table('settings')->insert([
            // General
            [
                'key'        => 'reservation_minutes',
                'value'      => '30',
                'label'      => 'Reservation Time (minutes)',
                'group'      => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // BDO
            [
                'key'        => 'bdo_account_name',
                'value'      => null,
                'label'      => 'BDO Account Name',
                'group'      => 'bdo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'bdo_account_number',
                'value'      => null,
                'label'      => 'BDO Account Number',
                'group'      => 'bdo',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // BPI
            [
                'key'        => 'bpi_account_name',
                'value'      => null,
                'label'      => 'BPI Account Name',
                'group'      => 'bpi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'bpi_account_number',
                'value'      => null,
                'label'      => 'BPI Account Number',
                'group'      => 'bpi',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Metrobank
            [
                'key'        => 'metrobank_account_name',
                'value'      => null,
                'label'      => 'Metrobank Account Name',
                'group'      => 'metrobank',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'metrobank_account_number',
                'value'      => null,
                'label'      => 'Metrobank Account Number',
                'group'      => 'metrobank',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // UnionBank
            [
                'key'        => 'unionbank_account_name',
                'value'      => null,
                'label'      => 'UnionBank Account Name',
                'group'      => 'unionbank',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'unionbank_account_number',
                'value'      => null,
                'label'      => 'UnionBank Account Number',
                'group'      => 'unionbank',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // GCash
            [
                'key'        => 'gcash_name',
                'value'      => null,
                'label'      => 'GCash Name',
                'group'      => 'gcash',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'gcash_number',
                'value'      => null,
                'label'      => 'GCash Number',
                'group'      => 'gcash',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Maya
            [
                'key'        => 'maya_name',
                'value'      => null,
                'label'      => 'Maya Name',
                'group'      => 'maya',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'maya_number',
                'value'      => null,
                'label'      => 'Maya Number',
                'group'      => 'maya',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Other
            [
                'key'        => 'other_payment_label',
                'value'      => null,
                'label'      => 'Other Payment Label',
                'group'      => 'other',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'other_payment_details',
                'value'      => null,
                'label'      => 'Other Payment Details',
                'group'      => 'other',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};