
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('savings_goal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('transaction_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('amount', 15, 2);
            $table->date('contribution_date');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['savings_goal_id', 'contribution_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_contributions');
    }
};
