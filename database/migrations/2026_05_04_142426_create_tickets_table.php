<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('raffle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sponsor_id')
                ->nullable()
                ->constrained('sponsors')
                ->nullOnDelete();

            $table->string('holder_first_name')->nullable();
            $table->string('holder_last_name')->nullable();

            $table->string('ticket_number');
            $table->string('status')->default('available');
            $table->timestamp('reserved_until')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['raffle_id', 'status']);
            $table->index(['sponsor_id', 'raffle_id']);
            $table->index(['status', 'reserved_until']);
            $table->index('ticket_number');

            // One ticket number per raffle
            $table->unique(['raffle_id', 'ticket_number']);
        });

        DB::statement("
            ALTER TABLE tickets
            ADD CONSTRAINT tickets_status_check
            CHECK (status IN ('available', 'reserved', 'sold'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};