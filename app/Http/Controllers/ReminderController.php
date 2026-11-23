<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ReminderHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReminderController extends Controller
{
    /**
     * Menampilkan daftar pengingat.
     */
    public function index(Request $request)
    {
        $query = Reminder::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reminders = $query
            ->orderBy('due_date')
            ->orderBy('reminder_time')
            ->paginate(10);

        $reminders->appends($request->query());

        $totalReminders = Reminder::where('user_id', auth()->id())->count();

        $pendingReminders = Reminder::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        $completedReminders = Reminder::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->count();

        return view('reminders.index', compact(
            'reminders',
            'totalReminders',
            'pendingReminders',
            'completedReminders'
        ));
    }

    /**
     * Form tambah pengingat.
     */
    public function create()
    {
        return view('reminders.create');
    }

    /**
     * Menyimpan pengingat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'due_date' => ['required', 'date'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'repeat_type' => [
                'required',
                Rule::in(['none', 'daily', 'weekly', 'monthly', 'yearly']),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Reminder::create($validated);

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Pengingat berhasil ditambahkan.');
    }

    /**
     * Form edit pengingat.
     */
    public function edit(Reminder $reminder)
    {
        abort_unless($reminder->user_id === auth()->id(), 403);

        return view('reminders.edit', compact('reminder'));
    }

    /**
     * Memperbarui pengingat.
     */
    public function update(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'due_date' => ['required', 'date'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'repeat_type' => [
                'required',
                Rule::in(['none', 'daily', 'weekly', 'monthly', 'yearly']),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $reminder->update($validated);

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Pengingat berhasil diperbarui.');
    }

    /**
     * Mengubah status pengingat.
     */
    public function updateStatus(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
        ]);

        $result = DB::transaction(function () use ($reminder, $validated) {
            $lockedReminder = Reminder::whereKey($reminder->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedReminder->user_id === auth()->id(), 403);

            $newStatus = $validated['status'];

            // Jika pengingat ditandai selesai
            if ($newStatus === 'completed') {
                // Hindari pencatatan riwayat ganda
                if ($lockedReminder->status === 'completed') {
                    return 'already_completed';
                }

                // Pengingat yang dibatalkan tidak dapat diselesaikan
                if ($lockedReminder->status === 'cancelled') {
                    return 'cancelled';
                }

                // Simpan riwayat penyelesaian
                ReminderHistory::create([
                    'reminder_id' => $lockedReminder->id,
                    'due_date' => Carbon::parse($lockedReminder->due_date)->toDateString(),
                    'amount' => $lockedReminder->amount,
                    'completed_at' => now(),
                ]);

                // Jika pengingat hanya sekali
                if ($lockedReminder->repeat_type === 'none') {
                    $lockedReminder->status = 'completed';
                    $lockedReminder->save();

                    return 'completed';
                }

                // Hitung jadwal berikutnya
                $nextDueDate = Carbon::parse($lockedReminder->due_date);

                switch ($lockedReminder->repeat_type) {
                    case 'daily':
                        $nextDueDate->addDay();
                        break;

                    case 'weekly':
                        $nextDueDate->addWeek();
                        break;

                    case 'monthly':
                        $nextDueDate->addMonthNoOverflow();
                        break;

                    case 'yearly':
                        $nextDueDate->addYearNoOverflow();
                        break;
                }

                // Perbarui tanggal dan pertahankan status pending
                $lockedReminder->due_date = $nextDueDate->toDateString();
                $lockedReminder->status = 'pending';
                $lockedReminder->save();

                return 'recurring';
            }

            // Untuk perubahan status selain completed
            $lockedReminder->status = $newStatus;
            $lockedReminder->save();

            return 'updated';
        });

        if ($result === 'already_completed') {
            return back()->with('info', 'Pengingat ini sudah pernah diselesaikan.');
        }

        if ($result === 'cancelled') {
            return back()->with('error', 'Pengingat yang dibatalkan tidak dapat diselesaikan.');
        }

        if ($result === 'recurring') {
            return back()->with('success', 'Pengingat selesai. Jadwal berikutnya berhasil dibuat.');
        }

        if ($result === 'completed') {
            return back()->with('success', 'Pengingat berhasil diselesaikan.');
        }

        return back()->with('success', 'Status pengingat berhasil diperbarui.');
    }

    /**
     * Menghapus pengingat.
     */
    public function destroy(Reminder $reminder)
    {
        abort_unless($reminder->user_id === auth()->id(), 403);

        $reminder->delete();

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Pengingat berhasil dihapus.');
    }

    public function histories(Reminder $reminder)
    {
        abort_unless($reminder->user_id === auth()->id(), 403);

        $histories = $reminder->histories()
            ->orderByDesc('completed_at')
            ->paginate(10);

        return view('reminders.histories', compact(
            'reminder',
            'histories'
        ));
    }
}
