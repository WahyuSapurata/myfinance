
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title', 150);
            $table->decimal('amount', 15, 2)->nullable();

            $table->date('due_date');
            $table->time('reminder_time')->nullable();

            $table->enum('repeat_type', [
                'none',
                'daily',
                'weekly',
                'monthly',
                'yearly'
            ])->default('none');

            $table->enum('status', [
                'pending',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
