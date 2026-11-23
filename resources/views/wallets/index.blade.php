@extends('layouts.app')

@section('title', 'Dompet')
@section('page-title', 'Manajemen Dompet')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dompet Saya</h4>
            <p class="text-secondary mb-0">
                Kelola semua rekening dan tempat penyimpanan uang.
            </p>
        </div>

        <a href="{{ route('wallets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Dompet
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($wallets->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-wallet2 fs-1 text-secondary"></i>
                <h5 class="fw-bold mt-3">Belum Ada Dompet</h5>
                <p class="text-secondary">
                    Tambahkan dompet pertama untuk mulai mengelola keuangan.
                </p>
                <a href="{{ route('wallets.create') }}" class="btn btn-primary">
                    Tambah Dompet
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($wallets as $wallet)
                @php
                    $balance =
                        (float) $wallet->initial_balance +
                        (float) ($wallet->income_total ?? 0) -
                        (float) ($wallet->expense_total ?? 0) -
                        (float) ($wallet->transfer_out_total ?? 0) +
                        (float) ($wallet->transfer_in_total ?? 0);

                    $types = [
                        'cash' => 'Uang Tunai',
                        'bank' => 'Rekening Bank',
                        'ewallet' => 'E-Wallet',
                        'savings' => 'Tabungan',
                        'other' => 'Lainnya',
                    ];
                @endphp

                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $types[$wallet->type] ?? 'Lainnya' }}
                                    </span>
                                    <h5 class="fw-bold mt-3 mb-1">
                                        {{ $wallet->name }}
                                    </h5>
                                    <small class="text-secondary">
                                        {{ $wallet->account_number ?: 'Tanpa nomor rekening' }}
                                    </small>
                                </div>

                                <i class="bi bi-wallet2 fs-2 text-primary"></i>
                            </div>

                            <hr>

                            <small class="text-secondary">Saldo saat ini</small>
                            <h4 class="fw-bold mt-1 mb-3">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </h4>

                            <div class="d-flex justify-content-between align-items-center mt-3">

                                @if ($wallet->is_active)
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                @endif

                                <div class="d-flex gap-2">

                                    <a href="{{ route('wallets.edit', $wallet) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <form action="{{ route('wallets.toggle-status', $wallet) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin mengubah status dompet ini?')">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="btn btn-sm {{ $wallet->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">

                                            {{ $wallet->is_active ? 'Nonaktifkan' : 'Aktifkan' }}

                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
