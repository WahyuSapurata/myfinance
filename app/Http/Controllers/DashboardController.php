<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Reminder;
use App\Models\ReminderNotification;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // Total saldo awal seluruh dompet
        $initialBalance = Wallet::where('user_id', $userId)
            ->sum('initial_balance');

        // Total pemasukan sepanjang waktu
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        // Total pengeluaran sepanjang waktu
        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

        // Transfer masuk ke dompet milik pengguna
        $transferIn = Transaction::where('user_id', $userId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->whereNotNull('destination_wallet_id')
            ->whereHas('destinationWallet', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');

        // Transfer keluar dari dompet pengguna
        $transferOut = Transaction::where('user_id', $userId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->whereHas('wallet', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');

        $totalBalance = $initialBalance
            + $totalIncome
            - $totalExpense
            + $transferIn
            - $transferOut;

        // Pemasukan bulan ini
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Pengeluaran bulan ini
        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyBalance = $monthlyIncome - $monthlyExpense;

        // Grafik enam bulan terakhir
        $chartStart = now()->startOfMonth()->subMonths(5);

        $chartTransactions = Transaction::where('user_id', $userId)
            ->where('status', 'posted')
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [
                $chartStart->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, type, SUM(amount) as total')
            ->groupBy('year', 'month', 'type')
            ->get();

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $chartStart->copy()->addMonths($i);
            $key = $date->year . '-' . $date->month;

            $chartLabels[] = $date->translatedFormat('M Y');

            $income = $chartTransactions
                ->where('year', $date->year)
                ->where('month', $date->month)
                ->where('type', 'income')
                ->sum('total');

            $expense = $chartTransactions
                ->where('year', $date->year)
                ->where('month', $date->month)
                ->where('type', 'expense')
                ->sum('total');

            $chartIncome[] = (float) $income;
            $chartExpense[] = (float) $expense;
        }

        // Lima transaksi terbaru
        $recentTransactions = Transaction::with([
            'wallet',
            'category',
            'destinationWallet'
        ])
            ->where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Target tabungan aktif beserta total kontribusinya
        $savingsGoals = SavingsGoal::withSum('contributions', 'amount')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->limit(5)
            ->get();

        // Jumlah notifikasi yang belum dibaca
        $unreadNotifications = ReminderNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        // Jumlah pengingat yang sudah jatuh tempo
        $overdueReminders = Reminder::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        // Lima notifikasi terbaru
        $recentNotifications = ReminderNotification::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyBalance',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'recentTransactions',
            'savingsGoals',
            'unreadNotifications',
            'overdueReminders',
            'recentNotifications',
        ));
    }
}
