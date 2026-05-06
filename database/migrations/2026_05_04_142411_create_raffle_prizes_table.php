<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raffle_prizes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('raffle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');  // first, second, third, consolation
            $table->unsignedTinyInteger('position')->nullable(); // 1, 2, 3 — null for consolation
            $table->string('name');

            $table->timestamps();

            // Indexes
            $table->index(['raffle_id', 'type']);
            $table->index(['raffle_id', 'position']);
        });

        DB::statement("
            ALTER TABLE raffle_prizes
            ADD CONSTRAINT raffle_prizes_type_check
            CHECK (type IN ('first', 'second', 'third', 'consolation'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('raffle_prizes');
    }
};