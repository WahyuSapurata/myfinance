<?php

namespace App\Http\Controllers;

use App\Models\ReminderNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $userId = auth()->id();

        // Statistik seluruh notifikasi pengguna
        $totalNotifications = ReminderNotification::where('user_id', $userId)
            ->count();

        $unreadCount = ReminderNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $readCount = ReminderNotification::where('user_id', $userId)
            ->whereNotNull('read_at')
            ->count();

        // Query daftar notifikasi
        $query = ReminderNotification::where('user_id', $userId)
            ->with('reminder');

        if ($request->status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->status === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $notifications = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('notifications.index', compact(
            'notifications',
            'totalNotifications',
            'unreadCount',
            'readCount'
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

    public function destroy(ReminderNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function unreadCount()
    {
        $count = ReminderNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
