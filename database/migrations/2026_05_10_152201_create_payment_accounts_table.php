<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type');        // bdo, bpi, gcash, maya, other etc.
            $table->string('label');       // display name e.g. "BDO Savings"
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('qr_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};