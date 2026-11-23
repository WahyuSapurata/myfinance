<?php

namespace App\Http\Controllers;

use App\Models\IncomeSource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeSourceController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomeSource::where('user_id', $request->user()->id);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if (
            $request->filled('type') &&
            in_array($request->type, [
                'salary',
                'freelance',
                'business',
                'bonus',
                'investment',
                'other'
            ])
        ) {
            $query->where('type', $request->type);
        }

        if (
            $request->filled('status') &&
            in_array($request->status, ['active', 'inactive'])
        ) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $incomeSources = $query
            ->latest()
            ->paginate(10);

        $incomeSources->appends($request->query());

        return view('income-sources.index', compact('incomeSources'));
    }

    public function create()
    {
        return view('income-sources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'type' => [
                'required',
                Rule::in([
                    'salary',
                    'freelance',
                    'business',
                    'bonus',
                    'investment',
                    'other'
                ]),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        IncomeSource::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('income-sources.index')
            ->with('success', 'Sumber pemasukan berhasil ditambahkan.');
    }

    public function edit(Request $request, IncomeSource $incomeSource)
    {
        abort_if(
            $incomeSource->user_id !== $request->user()->id,
            403
        );

        return view('income-sources.edit', compact('incomeSource'));
    }

    public function update(
        Request $request,
        IncomeSource $incomeSource
    ) {
        abort_if(
            $incomeSource->user_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'type' => [
                'required',
                Rule::in([
                    'salary',
                    'freelance',
                    'business',
                    'bonus',
                    'investment',
                    'other'
                ]),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $incomeSource->update($validated);

        return redirect()
            ->route('income-sources.index')
            ->with('success', 'Sumber pemasukan berhasil diperbarui.');
    }

    public function toggleStatus(
        Request $request,
        IncomeSource $incomeSource
    ) {
        abort_if(
            $incomeSource->user_id !== $request->user()->id,
            403
        );

        $incomeSource->update([
            'is_active' => !$incomeSource->is_active,
        ]);

        return redirect()
            ->route('income-sources.index')
            ->with('success', 'Status sumber pemasukan berhasil diperbarui.');
    }
}
