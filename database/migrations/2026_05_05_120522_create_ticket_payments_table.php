<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sponsor_id')
                ->constrained('sponsors')
                ->cascadeOnDelete();

            // Proof of payment
            $table->string('proof_type');  // image | transaction_number
            $table->string('proof_value'); // file path or txn number

            // Status
            $table->string('status')->default('pending');

            // Admin review
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['ticket_id', 'status']);
            $table->index(['sponsor_id', 'status']);
        });

        DB::statement("
            ALTER TABLE ticket_payments
            ADD CONSTRAINT ticket_payments_proof_type_check
            CHECK (proof_type IN ('image', 'transaction_number'))
        ");

        DB::statement("
            ALTER TABLE ticket_payments
            ADD CONSTRAINT ticket_payments_status_check
            CHECK (status IN ('pending', 'confirmed', 'rejected'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_payments');
    }
};