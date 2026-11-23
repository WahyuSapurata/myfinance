<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::where('user_id', $request->user()->id)
            ->withSum([
                'transactions as income_total' => function ($query) {
                    $query->where('type', 'income')
                        ->where('status', 'posted');
                }
            ], 'amount')
            ->withSum([
                'transactions as expense_total' => function ($query) {
                    $query->where('type', 'expense')
                        ->where('status', 'posted');
                }
            ], 'amount')
            ->withSum([
                'transactions as transfer_out_total' => function ($query) {
                    $query->where('type', 'transfer')
                        ->where('status', 'posted');
                }
            ], 'amount')
            ->withSum([
                'destinationTransactions as transfer_in_total' => function ($query) {
                    $query->where('type', 'transfer')
                        ->where('status', 'posted');
                }
            ], 'amount')
            ->orderByDesc('is_active')
            ->latest()
            ->get();

        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:cash,bank,ewallet,savings,other'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $request->user()->wallets()->create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()
            ->route('wallets.index')
            ->with('success', 'Dompet berhasil ditambahkan.');
    }


    public function edit(Request $request, Wallet $wallet)
    {
        abort_unless($wallet->user_id === $request->user()->id, 404);

        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        abort_unless($wallet->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:cash,bank,ewallet,savings,other'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'account_number' => ['nullable', 'string', 'max:50'],
        ]);

        $wallet->update($validated);

        return redirect()
            ->route('wallets.index')
            ->with('success', 'Dompet berhasil diperbarui.');
    }


    public function toggleStatus(Request $request, Wallet $wallet)
    {
        abort_unless($wallet->user_id === $request->user()->id, 404);

        $wallet->update([
            'is_active' => ! $wallet->is_active,
        ]);

        return redirect()
            ->route('wallets.index')
            ->with(
                'success',
                $wallet->is_active
                    ? 'Dompet berhasil diaktifkan.'
                    : 'Dompet berhasil dinonaktifkan.'
            );
    }
}
