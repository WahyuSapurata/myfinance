@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Transaksi</h3>
                <p class="text-muted mb-0">
                    Kelola dan pantau seluruh transaksi keuangan Anda.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('transactions.transfer') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Transfer
                </a>

                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Transaksi
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Ringkasan Transaksi --}}
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Pemasukan</div>
                        <h4 class="fw-bold text-success mb-0">
                            Rp {{ number_format($summary['income'], 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Pengeluaran</div>
                        <h4 class="fw-bold text-danger mb-0">
                            Rp {{ number_format($summary['expense'], 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Selisih</div>
                        <h4 class="fw-bold mb-0 {{ $summary['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            Rp {{ number_format($summary['balance'], 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter dan Pencarian --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <form action="{{ route('transactions.index') }}" method="GET">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Keterangan atau nomor referensi" value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Jenis</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>
                                    Pemasukan
                                </option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>
                                    Pengeluaran
                                </option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>
                                    Transfer
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Dompet</label>
                            <select name="wallet_id" class="form-select">
                                <option value="">Semua Dompet</option>

                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}"
                                        {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Terapkan Filter
                            </button>

                            <a href="{{ route('transactions.index') }}" class="btn btn-light">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Daftar Transaksi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Riwayat Transaksi</h5>
                    <span class="text-muted small">
                        {{ $transactions->total() }} transaksi ditemukan
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Kategori</th>
                                <th>Dompet</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $transaction->description ?: '-' }}
                                        </div>

                                        @if ($transaction->reference_number)
                                            <small class="text-muted">
                                                Ref: {{ $transaction->reference_number }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $transaction->category->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $transaction->wallet->name ?? '-' }}

                                        @if ($transaction->type === 'transfer' && $transaction->destinationWallet)
                                            <div class="small text-muted">
                                                Ke: {{ $transaction->destinationWallet->name }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($transaction->type === 'income')
                                            <span class="badge bg-success">Pemasukan</span>
                                        @elseif($transaction->type === 'expense')
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        @else
                                            <span class="badge bg-info">Transfer</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($transaction->status === 'posted')
                                            <span class="badge bg-success">Tercatat</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Dibatalkan</span>
                                        @endif
                                    </td>

                                    <td
                                        class="text-end fw-semibold
                                    {{ $transaction->type === 'income' ? 'text-success' : ($transaction->type === 'expense' ? 'text-danger' : '') }}">

                                        {{ $transaction->type === 'income' ? '+' : ($transaction->type === 'expense' ? '-' : '') }}
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="text-center">
                                        @if (in_array($transaction->type, ['income', 'expense']) && $transaction->status !== 'cancelled')
                                            <div class="d-flex justify-content-center gap-1">

                                                <a href="{{ route('transactions.edit', $transaction->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('transactions.cancel', $transaction->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')">

                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Batalkan">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-receipt fs-1 text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">
                                            Tidak ada transaksi yang ditemukan.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
