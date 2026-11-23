<?php

namespace App\Http\Controllers;

use App\Models\GoalContribution;
use App\Models\SavingsGoal;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = SavingsGoal::where('user_id', $userId)
            ->where('is_archived', false);

        // Pencarian nama target
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter status target
        if (
            $request->filled('status') &&
            in_array($request->status, ['active', 'completed', 'cancelled'])
        ) {
            $query->where('status', $request->status);
        }

        $goals = $query
            ->with([
                'contributions' => function ($query) {
                    $query->whereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->where('status', 'posted');
                    });
                }
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $goals->getCollection()->transform(function ($goal) {
            $goal->total_saved = $goal->contributions->sum('amount');

            $goal->progress = $goal->target_amount > 0
                ? min(100, ($goal->total_saved / $goal->target_amount) * 100)
                : 0;

            return $goal;
        });

        $totalTarget = SavingsGoal::where('user_id', $userId)
            ->where('is_archived', false)
            ->sum('target_amount');

        $totalSaved = \App\Models\GoalContribution::whereHas(
            'savingsGoal',
            function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('is_archived', false);
            }
        )
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'posted');
            })
            ->sum('amount');

        return view('savings-goals.index', compact(
            'goals',
            'totalTarget',
            'totalSaved'
        ));
    }

    public function create()
    {
        return view('savings-goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'target_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
        ]);

        $request->user()->savingsGoals()->create($validated);

        return redirect()
            ->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil ditambahkan.');
    }


    public function createContribution(Request $request, SavingsGoal $savingsGoal)
    {
        abort_unless(
            $savingsGoal->user_id === $request->user()->id,
            404
        );

        $wallets = Wallet::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('savings-goals.contribute', compact(
            'savingsGoal',
            'wallets'
        ));
    }

    public function storeContribution(Request $request, SavingsGoal $savingsGoal)
    {
        abort_unless(
            $savingsGoal->user_id === $request->user()->id,
            404
        );

        $validated = $request->validate([
            'wallet_id' => [
                'required',
                'integer',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $request->user()->id)
                    ->where('is_active', true),
            ],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:9999999999999.99'],
            'contribution_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $savingsGoal, $validated) {

            $goal = SavingsGoal::where('user_id', $request->user()->id)
                ->whereKey($savingsGoal->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($goal->status !== 'active') {
                throw ValidationException::withMessages([
                    'amount' => 'Target tabungan ini sudah tidak aktif.',
                ]);
            }

            $wallet = Wallet::where('user_id', $request->user()->id)
                ->where('is_active', true)
                ->whereKey($validated['wallet_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Hitung saldo dompet saat ini.
            $income = $wallet->transactions()
                ->where('type', 'income')
                ->where('status', 'posted')
                ->sum('amount');

            $expense = $wallet->transactions()
                ->where('type', 'expense')
                ->where('status', 'posted')
                ->sum('amount');

            $transferOut = $wallet->transactions()
                ->where('type', 'transfer')
                ->where('status', 'posted')
                ->sum('amount');

            $transferIn = $wallet->destinationTransactions()
                ->where('type', 'transfer')
                ->where('status', 'posted')
                ->sum('amount');

            $balance = (float) $wallet->initial_balance
                + (float) $income
                - (float) $expense
                - (float) $transferOut
                + (float) $transferIn;

            $amount = (float) $validated['amount'];

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo dompet tidak mencukupi untuk setoran ini.',
                ]);
            }

            // Ambil kategori pengeluaran global atau milik pengguna.
            $category = \App\Models\Category::where('type', 'expense')
                ->where('name', 'Tabungan')
                ->where('is_active', true)
                ->where(function ($query) use ($request) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', $request->user()->id);
                })
                ->orderByRaw('user_id IS NOT NULL DESC')
                ->first();

            if (!$category) {
                throw ValidationException::withMessages([
                    'amount' => 'Kategori pengeluaran Tabungan belum tersedia. Tambahkan kategori tersebut terlebih dahulu.',
                ]);
            }

            // Buat transaksi pengeluaran.
            $transaction = $request->user()->transactions()->create([
                'wallet_id' => $wallet->id,
                'category_id' => $category->id,
                'type' => 'expense',
                'amount' => $amount,
                'transaction_date' => $validated['contribution_date'],
                'description' => 'Setoran tabungan: ' . $goal->name,
                'status' => 'posted',
            ]);

            // Hubungkan transaksi dengan target tabungan.
            GoalContribution::create([
                'savings_goal_id' => $goal->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'contribution_date' => $validated['contribution_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('savings-goals.index')
            ->with('success', 'Setoran tabungan berhasil dicatat.');
    }


    public function history(SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        $contributions = $savingsGoal->contributions()
            ->with('transaction.wallet')
            ->latest('contribution_date')
            ->latest('id')
            ->paginate(10);

        $totalSaved = $savingsGoal->contributions()
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'posted');
            })
            ->sum('amount');

        return view('savings-goals.history', compact(
            'savingsGoal',
            'contributions',
            'totalSaved'
        ));
    }


    public function cancelContribution(
        SavingsGoal $savingsGoal,
        GoalContribution $contribution
    ) {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        abort_if(
            $contribution->savings_goal_id !== $savingsGoal->id,
            404
        );

        DB::transaction(function () use ($savingsGoal, $contribution) {

            $goal = SavingsGoal::where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($savingsGoal->id);

            $contribution = GoalContribution::where(
                'savings_goal_id',
                $goal->id
            )
                ->lockForUpdate()
                ->findOrFail($contribution->id);

            $transaction = $contribution->transaction()
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $transaction->user_id !== auth()->id(),
                403
            );

            abort_if(
                $transaction->type !== 'expense' ||
                    $transaction->status !== 'posted',
                422,
                'Setoran ini sudah dibatalkan atau tidak valid.'
            );

            // Pastikan transaksi memang merupakan setoran tabungan ini.
            abort_if(
                $transaction->description !== 'Setoran tabungan: ' . $goal->name,
                422,
                'Transaksi tidak sesuai dengan setoran tabungan.'
            );

            // Ubah status transaksi, jangan menghapus data.
            $transaction->status = 'cancelled';
            $transaction->save();
        });

        return redirect()
            ->route('savings-goals.history', $savingsGoal->id)
            ->with('success', 'Setoran tabungan berhasil dibatalkan.');
    }

    public function edit(SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        return view('savings-goals.edit', compact('savingsGoal'));
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
                'max:9999999999999.99'
            ],
            'target_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $savingsGoal->update($validated);

        return redirect()
            ->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function complete(SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        if ($savingsGoal->status !== 'active') {
            return redirect()
                ->route('savings-goals.index')
                ->with('error', 'Target tabungan ini tidak berstatus aktif.');
        }

        $totalSaved = $savingsGoal->contributions()
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'posted');
            })
            ->sum('amount');

        if ($totalSaved < $savingsGoal->target_amount) {
            return redirect()
                ->route('savings-goals.index')
                ->with('error', 'Target belum tercapai. Tidak dapat diselesaikan.');
        }

        $savingsGoal->update([
            'status' => 'completed',
        ]);

        return redirect()
            ->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil diselesaikan.');
    }

    public function archive(SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        if ($savingsGoal->is_archived) {
            return back()->with('error', 'Target sudah diarsipkan.');
        }

        $savingsGoal->update([
            'is_archived' => true,
        ]);

        return redirect()
            ->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil diarsipkan.');
    }

    public function archived()
    {
        $goals = SavingsGoal::where('user_id', auth()->id())
            ->where('is_archived', true)
            ->latest()
            ->paginate(10);

        return view('savings-goals.archived', compact('goals'));
    }

    public function restore(SavingsGoal $savingsGoal)
    {
        abort_if($savingsGoal->user_id !== auth()->id(), 403);

        if (!$savingsGoal->is_archived) {
            return back()->with('error', 'Target ini tidak berada dalam arsip.');
        }

        $savingsGoal->update([
            'is_archived' => false,
        ]);

        return redirect()
            ->route('savings-goals.archived')
            ->with('success', 'Target berhasil dipulihkan.');
    }
}
