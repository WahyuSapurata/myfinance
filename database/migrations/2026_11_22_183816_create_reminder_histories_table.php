<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reminder_id')
                ->constrained('reminders')
                ->cascadeOnDelete();

            $table->date('due_date');

            $table->decimal('amount', 15, 2)->nullable();

            $table->timestamp('completed_at');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['reminder_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_histories');
    }
};
