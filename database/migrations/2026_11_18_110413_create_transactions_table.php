
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('wallet_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('destination_wallet_id')
                ->nullable()
                ->constrained('wallets')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('income_source_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('salary_profile_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'income',
                'expense',
                'transfer'
            ]);

            $table->decimal('amount', 15, 2);

            $table->date('transaction_date');

            $table->string('description', 255)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('attachment')->nullable();

            $table->enum('status', [
                'posted',
                'pending',
                'cancelled'
            ])->default('posted');

            $table->timestamps();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'type', 'status']);
            $table->index(['wallet_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
