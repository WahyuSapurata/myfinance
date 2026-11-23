<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\TransactionsReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Periode laporan
        $startDate = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        $endDate = $request->input(
            'end_date',
            now()->endOfMonth()->toDateString()
        );

        // Validasi tanggal
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        // Query transaksi
        $transactions = Transaction::where('user_id', $userId)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [
                $startDate,
                $endDate,
            ]);

        // Ringkasan pemasukan
        $totalIncome = (clone $transactions)
            ->where('type', 'income')
            ->sum('amount');

        // Ringkasan pengeluaran
        $totalExpense = (clone $transactions)
            ->where('type', 'expense')
            ->sum('amount');

        // Transfer tidak dimasukkan sebagai pemasukan/pengeluaran
        $balance = $totalIncome - $totalExpense;

        // Grafik pemasukan dan pengeluaran per bulan dan tahun
        $monthlyReports = (clone $transactions)
            ->selectRaw("
        DATE_FORMAT(transaction_date, '%Y-%m') as period,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    ")
            ->whereIn('type', ['income', 'expense'])
            ->groupByRaw("DATE_FORMAT(transaction_date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(transaction_date, '%Y-%m')")
            ->get();

        // Ringkasan per kategori
        $categoryReports = (clone $transactions)
            ->select(
                'category_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->whereIn('type', ['income', 'expense'])
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        // Daftar transaksi
        $transactionList = (clone $transactions)
            ->with(['category', 'wallet'])
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'balance',
            'categoryReports',
            'transactionList',
            'monthlyReports'
        ));
    }

    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        return Excel::download(
            new TransactionsReportExport(
                $validated['start_date'],
                $validated['end_date'],
                auth()->id()
            ),
            'laporan-keuangan-' .
                $validated['start_date'] . '-' .
                $validated['end_date'] . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        $userId = auth()->id();

        $transactions = Transaction::where('user_id', $userId)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [
                $validated['start_date'],
                $validated['end_date'],
            ])
            ->with(['category', 'wallet'])
            ->orderBy('transaction_date')
            ->get();

        $totalIncome = $transactions
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = $transactions
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'validated'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-keuangan-' .
                $validated['start_date'] . '-' .
                $validated['end_date'] . '.pdf'
        );
    }


    public function cashFlow(Request $request)
    {
        $userId = auth()->id();

        $startDate = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        $endDate = $request->input(
            'end_date',
            now()->endOfMonth()->toDateString()
        );

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        // Transaksi sebelum periode laporan
        $previousTransactions = Transaction::where('user_id', $userId)
            ->where('status', 'posted')
            ->whereDate('transaction_date', '<', $startDate);

        $previousIncome = (clone $previousTransactions)
            ->where('type', 'income')
            ->sum('amount');

        $previousExpense = (clone $previousTransactions)
            ->where('type', 'expense')
            ->sum('amount');

        $openingBalance = $previousIncome - $previousExpense;

        // Transaksi selama periode laporan
        $transactions = Transaction::where('user_id', $userId)
            ->where('status', 'posted')
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with(['category', 'wallet'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $totalIncome = $transactions
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = $transactions
            ->where('type', 'expense')
            ->sum('amount');

        $netCashFlow = $totalIncome - $totalExpense;

        // Saldo berjalan dimulai dari saldo awal
        $runningBalance = $openingBalance;

        foreach ($transactions as $transaction) {
            if ($transaction->type === 'income') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }

            $transaction->running_balance = $runningBalance;
        }

        $closingBalance = $runningBalance;

        return view('reports.cash-flow', compact(
            'startDate',
            'endDate',
            'transactions',
            'totalIncome',
            'totalExpense',
            'netCashFlow',
            'openingBalance',
            'closingBalance'
        ));
    }
}
