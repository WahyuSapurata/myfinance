<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Models\ReminderNotification;
use Illuminate\Console\Command;

class CheckReminders extends Command
{
    protected $signature = 'reminders:check';

    protected $description = 'Memeriksa dan membuat notifikasi pengingat jatuh tempo';

    public function handle()
    {
        $now = now();

        $reminders = Reminder::where('status', 'pending')
            ->whereDate('due_date', '<=', $now->toDateString())
            ->where(function ($query) use ($now) {
                $query->whereNull('reminder_time')
                    ->orWhereTime('reminder_time', '<=', $now->format('H:i:s'));
            })
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($reminders as $reminder) {
            $notification = ReminderNotification::firstOrCreate(
                [
                    'reminder_id' => $reminder->id,
                    'due_date' => $reminder->due_date,
                ],
                [
                    'user_id' => $reminder->user_id,
                    'title' => $reminder->title,
                    'message' => 'Pengingat "' . $reminder->title .
                        '" sudah jatuh tempo.',
                ]
            );

            if ($notification->wasRecentlyCreated) {
                $created++;

                $this->info("Notifikasi dibuat: {$reminder->title}");
            } else {
                $skipped++;

                $this->line("Notifikasi sudah tersedia: {$reminder->title}");
            }
        }

        $this->info("Total pengingat jatuh tempo: {$reminders->count()}");
        $this->info("Notifikasi baru dibuat: {$created}");
        $this->info("Notifikasi dilewati: {$skipped}");

        return self::SUCCESS;
    }
}
