<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:income,expense,transfer'],
            'wallet_id' => [
                'nullable',
                'integer',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        $query = Transaction::with([
            'wallet',
            'category',
            'destinationWallet',
        ])
            ->where('user_id', $userId);

        // Pencarian berdasarkan keterangan atau nomor referensi
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Filter jenis transaksi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter dompet
        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        // Filter tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        // Filter tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Ringkasan hanya menghitung transaksi yang sudah tercatat
        $summaryQuery = clone $query;

        $summary = [
            'income' => (clone $summaryQuery)
                ->where('type', 'income')
                ->where('status', 'posted')
                ->sum('amount'),

            'expense' => (clone $summaryQuery)
                ->where('type', 'expense')
                ->where('status', 'posted')
                ->sum('amount'),
        ];

        $summary['balance'] = $summary['income'] - $summary['expense'];

        $transactions = $query
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10);

        $transactions->appends($request->query());

        $wallets = Wallet::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('transactions.index', compact(
            'transactions',
            'wallets',
            'summary'
        ));
    }


    public function create(Request $request)
    {
        $userId = $request->user()->id;

        $wallets = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::whereIn('type', ['income', 'expense'])
            ->where('is_active', true)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->orderBy('name')
            ->get();

        return view('transactions.create', compact(
            'wallets',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId)
                    ->where('is_active', true),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where(function ($query) use ($userId) {
                        $query->whereNull('user_id')
                            ->orWhere('user_id', $userId);
                    }),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        $category = Category::where('id', $validated['category_id'])
            ->where('type', $validated['type'])
            ->where('is_active', true)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->first();

        if (!$category) {
            return back()
                ->withErrors([
                    'category_id' => 'Kategori tidak sesuai dengan jenis transaksi.',
                ])
                ->withInput();
        }

        Transaction::create([
            'user_id' => $userId,
            'wallet_id' => $validated['wallet_id'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => 'posted',
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }


    public function edit(Request $request, Transaction $transaction)
    {
        abort_unless(
            (int) $transaction->user_id === (int) $request->user()->id,
            404
        );

        if (
            $transaction->status === 'cancelled' ||
            !in_array($transaction->type, ['income', 'expense'])
        ) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Transaksi ini tidak dapat diedit.');
        }

        $userId = $request->user()->id;

        $wallets = Wallet::where('user_id', $userId)
            ->where(function ($query) use ($transaction) {
                $query->where('is_active', true)
                    ->orWhere('id', $transaction->wallet_id);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::where('type', $transaction->type)
            ->where(function ($query) use ($userId, $transaction) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->where(function ($query) use ($transaction) {
                $query->where('is_active', true)
                    ->orWhere('id', $transaction->category_id);
            })
            ->orderBy('name')
            ->get();

        return view('transactions.edit', compact(
            'transaction',
            'wallets',
            'categories'
        ));
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort_unless(
            (int) $transaction->user_id === (int) $request->user()->id,
            404
        );

        if (
            $transaction->status === 'cancelled' ||
            !in_array($transaction->type, ['income', 'expense'])
        ) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Transaksi ini tidak dapat diedit.');
        }

        $userId = $request->user()->id;

        $validated = $request->validate([
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where(function ($query) use ($userId) {
                        $query->whereNull('user_id')
                            ->orWhere('user_id', $userId);
                    }),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        $wallet = Wallet::where('user_id', $userId)
            ->where('id', $validated['wallet_id'])
            ->first();

        if (!$wallet || (!$wallet->is_active &&
            (int) $wallet->id !== (int) $transaction->wallet_id)) {
            return back()
                ->withErrors(['wallet_id' => 'Dompet tidak aktif atau tidak valid.'])
                ->withInput();
        }

        $category = Category::where('id', $validated['category_id'])
            ->where('type', $transaction->type)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->where(function ($query) use ($transaction) {
                $query->where('is_active', true)
                    ->orWhere('id', $transaction->category_id);
            })
            ->first();

        if (!$category) {
            return back()
                ->withErrors([
                    'category_id' => 'Kategori tidak sesuai atau tidak aktif.',
                ])
                ->withInput();
        }

        $transaction->update([
            'wallet_id' => $validated['wallet_id'],
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        abort_unless(
            (int) $transaction->user_id === (int) $request->user()->id,
            404
        );

        if (
            !in_array($transaction->type, ['income', 'expense']) ||
            !in_array($transaction->status, ['posted', 'pending'])
        ) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Transaksi tidak dapat dibatalkan.');
        }

        $transaction->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }


    public function createTransfer(Request $request)
    {
        $wallets = Wallet::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('transactions.transfer', compact('wallets'));
    }

    public function storeTransfer(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'wallet_id' => [
                'required',
                'different:destination_wallet_id',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId)
                    ->where('is_active', true),
            ],
            'destination_wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId)
                    ->where('is_active', true),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $userId) {

            $walletIds = [
                (int) $validated['wallet_id'],
                (int) $validated['destination_wallet_id'],
            ];

            sort($walletIds);

            $lockedWallets = Wallet::where('user_id', $userId)
                ->whereIn('id', $walletIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $sourceWallet = $lockedWallets->get(
                (int) $validated['wallet_id']
            );

            $destinationWallet = $lockedWallets->get(
                (int) $validated['destination_wallet_id']
            );

            if (
                !$sourceWallet || !$destinationWallet ||
                !$sourceWallet->is_active || !$destinationWallet->is_active
            ) {
                throw ValidationException::withMessages([
                    'wallet_id' => 'Dompet tidak valid atau sudah nonaktif.',
                ]);
            }

            // Hitung saldo aktual dompet asal
            $income = $sourceWallet->transactions()
                ->where('type', 'income')
                ->where('status', 'posted')
                ->sum('amount');

            $expense = $sourceWallet->transactions()
                ->where('type', 'expense')
                ->where('status', 'posted')
                ->sum('amount');

            $transferOut = $sourceWallet->transactions()
                ->where('type', 'transfer')
                ->where('status', 'posted')
                ->sum('amount');

            $transferIn = $sourceWallet->destinationTransactions()
                ->where('type', 'transfer')
                ->where('status', 'posted')
                ->sum('amount');

            $balance = (float) $sourceWallet->initial_balance
                + (float) $income
                - (float) $expense
                - (float) $transferOut
                + (float) $transferIn;

            if ((float) $validated['amount'] > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo dompet asal tidak mencukupi.',
                ]);
            }

            Transaction::create([
                'user_id' => $userId,
                'wallet_id' => $sourceWallet->id,
                'destination_wallet_id' => $destinationWallet->id,
                'type' => 'transfer',
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'status' => 'posted',
            ]);
        });

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transfer antar-dompet berhasil dicatat.');
    }
}
