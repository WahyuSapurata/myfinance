
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->enum('type', [
                'cash',
                'bank',
                'ewallet',
                'savings',
                'other'
            ])->default('cash');

            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->string('account_number', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icon', 50)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
