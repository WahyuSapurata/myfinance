<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function edit()
    {
        $userId = auth()->id();

        $reminderEnabled = Setting::firstOrCreate(
            [
                'user_id' => $userId,
                'key' => 'reminder_notification_enabled',
            ],
            ['value' => '1']
        )->value;

        $browserEnabled = Setting::firstOrCreate(
            [
                'user_id' => $userId,
                'key' => 'browser_notification_enabled',
            ],
            ['value' => '1']
        )->value;

        return view('notifications.settings', compact(
            'reminderEnabled',
            'browserEnabled'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'reminder_notification_enabled' => ['required', 'boolean'],
            'browser_notification_enabled' => ['required', 'boolean'],
        ]);

        Setting::setValue(
            'reminder_notification_enabled',
            $validated['reminder_notification_enabled']
        );

        Setting::setValue(
            'browser_notification_enabled',
            $validated['browser_notification_enabled']
        );

        return back()->with(
            'success',
            'Pengaturan notifikasi berhasil disimpan.'
        );
    }
}
