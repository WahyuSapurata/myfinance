@extends('layouts.app')

@section('title', 'Laporan Arus Kas')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Laporan Arus Kas</h4>
                <p class="text-muted mb-0">
                    Ringkasan pergerakan uang masuk dan keluar.
                </p>
            </div>

            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Laporan
            </a>
        </div>

        {{-- Filter Periode --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('reports.cash-flow') }}" method="GET">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>


        {{-- Ringkasan --}}
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted">Saldo Awal</div>
                        <h5 class="text-primary mb-0">
                            Rp {{ number_format($openingBalance, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted">Uang Masuk</div>
                        <h5 class="text-success mb-0">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted">Uang Keluar</div>
                        <h5 class="text-danger mb-0">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted">Saldo Akhir</div>
                        <h5 class="text-success mb-0">
                            Rp {{ number_format($closingBalance, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabel Transaksi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Detail Arus Kas</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Dompet</th>
                                <th>Jenis</th>
                                <th class="text-end">Uang Masuk</th>
                                <th class="text-end">Uang Keluar</th>
                                <th class="text-end">Saldo Berjalan</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="table-light fw-semibold">
                                <td colspan="8">Saldo Awal Periode</td>
                                <td class="text-end">
                                    Rp {{ number_format($openingBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                            @forelse($transactions as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}
                                    </td>

                                    <td>{{ $transaction->description }}</td>

                                    <td>
                                        {{ $transaction->category?->name ?? 'Tanpa Kategori' }}
                                    </td>

                                    <td>
                                        {{ $transaction->wallet?->name ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($transaction->type === 'income')
                                            <span class="badge bg-success">Pemasukan</span>
                                        @else
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        @endif
                                    </td>

                                    <td class="text-end text-success">
                                        @if ($transaction->type === 'income')
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end text-danger">
                                        @if ($transaction->type === 'expense')
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($transaction->running_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
