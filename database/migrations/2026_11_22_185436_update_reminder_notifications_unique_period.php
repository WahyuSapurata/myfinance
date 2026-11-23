
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom hanya jika belum tersedia.
        if (!Schema::hasColumn('reminder_notifications', 'due_date')) {
            Schema::table('reminder_notifications', function (Blueprint $table) {
                $table->date('due_date')->after('reminder_id');
            });
        }

        // Buat indeks baru terlebih dahulu.
        Schema::table('reminder_notifications', function (Blueprint $table) {
            $table->unique(
                ['reminder_id', 'due_date'],
                'reminder_notifications_reminder_due_unique'
            );
        });

        // Setelah indeks baru tersedia, hapus indeks lama.
        Schema::table('reminder_notifications', function (Blueprint $table) {
            $table->dropUnique(
                'reminder_notifications_reminder_id_user_id_unique'
            );
        });
    }

    public function down(): void
    {
        // Buat kembali indeks lama terlebih dahulu.
        Schema::table('reminder_notifications', function (Blueprint $table) {
            $table->unique(
                ['reminder_id', 'user_id'],
                'reminder_notifications_reminder_id_user_id_unique'
            );
        });

        // Hapus indeks baru.
        Schema::table('reminder_notifications', function (Blueprint $table) {
            $table->dropUnique(
                'reminder_notifications_reminder_due_unique'
            );
        });

        Schema::table('reminder_notifications', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
