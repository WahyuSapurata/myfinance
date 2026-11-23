<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Models\ReminderNotification;
use App\Models\Setting;
use Illuminate\Console\Command;

class CheckReminders extends Command
{
    protected $signature = 'reminders:check';

    protected $description = 'Memeriksa pengingat jatuh tempo dan membuat notifikasi';

    public function handle()
    {
        $disabledUsers = Setting::where(
            'key',
            'reminder_notification_enabled'
        )
            ->where('value', '0')
            ->pluck('user_id');

        $now = now();

        $reminders = Reminder::whereNotIn('user_id', $disabledUsers)
            ->where('status', 'pending')
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

                $this->newLine();
                $this->info('NOTIFIKASI BARU');
                $this->line('Judul       : ' . $notification->title);
                $this->line('Pengingat   : ' . $reminder->title);
                $this->line('Jatuh Tempo : ' . $reminder->due_date->format('d-m-Y'));
                $this->line('Pesan       : ' . $notification->message);
                $this->line('Status      : Belum dibaca');

                $this->line('----------------------------------------');
            } else {
                $skipped++;

                $this->line(
                    "Notifikasi sudah tersedia: {$reminder->title}"
                );
            }
        }

        $this->newLine();
        $this->info('PEMERIKSAAN PENGINGAT SELESAI');
        $this->line('Total pengingat jatuh tempo : ' . $reminders->count());
        $this->line('Notifikasi baru dibuat      : ' . $created);
        $this->line('Notifikasi dilewati         : ' . $skipped);

        return self::SUCCESS;
    }
}
