<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('salary_period', 7)
                ->nullable()
                ->after('salary_profile_id');

            $table->unique(
                ['salary_profile_id', 'salary_period'],
                'transactions_salary_profile_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(
                'transactions_salary_profile_period_unique'
            );

            $table->dropColumn('salary_period');
        });
    }
};
