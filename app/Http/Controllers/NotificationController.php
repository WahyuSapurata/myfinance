<?php

namespace App\Http\Controllers;

use App\Models\ReminderNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = ReminderNotification::where('user_id', auth()->id())
            ->with('reminder')
            ->latest()
            ->paginate(10);

        $unreadCount = ReminderNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    public function markAsRead(ReminderNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        if (!$notification->read_at) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead()
    {
        ReminderNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }
}
