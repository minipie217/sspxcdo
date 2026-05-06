<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->decimal('ticket_price', 10, 2);
            $table->unsignedInteger('total_tickets');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('draw_date');

            // string + check constraint — TicketStatus enum is source of truth
            $table->string('status')->default('draft');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('draw_date');
            $table->index('created_by');
        });

        DB::statement("
            ALTER TABLE raffles
            ADD CONSTRAINT raffles_status_check
            CHECK (status IN ('draft', 'active', 'closed', 'generating'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};