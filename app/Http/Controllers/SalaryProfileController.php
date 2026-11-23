<?php

namespace App\Http\Controllers;

use App\Models\IncomeSource;
use App\Models\SalaryProfile;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaryProfileController extends Controller
{

    public function index(Request $request)
    {
        $query = SalaryProfile::where('user_id', auth()->id())
            ->with(['incomeSource', 'wallet']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('incomeSource', function ($sourceQuery) use ($request) {
                        $sourceQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $totalProfiles = SalaryProfile::where('user_id', auth()->id())
            ->count();

        $activeProfiles = SalaryProfile::where('user_id', auth()->id())
            ->where('is_active', true)
            ->count();

        $totalActiveSalary = SalaryProfile::where('user_id', auth()->id())
            ->where('is_active', true)
            ->sum('amount');

        $salaryProfiles = $query
            ->latest()
            ->paginate(10);

        $salaryProfiles->appends($request->query());

        return view('salary-profiles.index', compact(
            'salaryProfiles',
            'totalProfiles',
            'activeProfiles',
            'totalActiveSalary'
        ));
    }

    public function create(Request $request)
    {
        $userId = $request->user()->id;

        $incomeSources = IncomeSource::where('user_id', $userId)
            ->where('type', 'salary')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $wallets = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('salary-profiles.create', compact(
            'incomeSources',
            'wallets'
        ));
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'income_source_id' => [
                'required',
                Rule::exists('income_sources', 'id')
                    ->where('user_id', $userId)
                    ->where('type', 'salary')
                    ->where('is_active', true),
            ],
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId)
                    ->where('is_active', true),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
                'max:9999999999999.99',
            ],
            'pay_day' => [
                'required',
                'integer',
                'between:1,31',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        SalaryProfile::create([
            'user_id' => $userId,
            'income_source_id' => $validated['income_source_id'],
            'wallet_id' => $validated['wallet_id'],
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'pay_day' => $validated['pay_day'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'Profil gaji berhasil ditambahkan.');
    }

    public function edit(Request $request, SalaryProfile $salaryProfile)
    {
        abort_if(
            $salaryProfile->user_id !== $request->user()->id,
            403
        );

        $userId = $request->user()->id;

        $incomeSources = IncomeSource::where('user_id', $userId)
            ->where('type', 'salary')
            ->orderBy('name')
            ->get();

        $wallets = Wallet::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('salary-profiles.edit', compact(
            'salaryProfile',
            'incomeSources',
            'wallets'
        ));
    }

    public function update(
        Request $request,
        SalaryProfile $salaryProfile
    ) {
        abort_if(
            $salaryProfile->user_id !== $request->user()->id,
            403
        );

        $userId = $request->user()->id;

        $validated = $request->validate([
            'income_source_id' => [
                'required',
                Rule::exists('income_sources', 'id')
                    ->where('user_id', $userId)
                    ->where('type', 'salary'),
            ],
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('user_id', $userId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
                'max:9999999999999.99',
            ],
            'pay_day' => [
                'required',
                'integer',
                'between:1,31',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $salaryProfile->update($validated);

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'Profil gaji berhasil diperbarui.');
    }

    public function toggleStatus(
        Request $request,
        SalaryProfile $salaryProfile
    ) {
        abort_if(
            $salaryProfile->user_id !== $request->user()->id,
            403
        );

        $salaryProfile->update([
            'is_active' => !$salaryProfile->is_active,
        ]);

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'Status profil gaji berhasil diperbarui.');
    }
}
