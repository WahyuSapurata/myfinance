<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule): void
    {
        // Memproses gaji otomatis setiap hari pukul 00:05
        $schedule->command('salary:process')
            ->dailyAt('00:05')
            ->withoutOverlapping();

        // Memeriksa pengingat setiap menit
        $schedule->command('reminders:check')
            ->everyMinute()
            ->withoutOverlapping();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
