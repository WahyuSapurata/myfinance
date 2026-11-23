@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">Laporan Keuangan</h4>
            <p class="text-muted mb-0">
                Ringkasan pemasukan dan pengeluaran keuangan Anda.
            </p>
        </div>

        {{-- Filter Periode --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>

                    <div class="col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i>
                            Tampilkan
                        </button>

                        <a href="{{ route('reports.export.excel', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]) }}"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i>
                            Export Excel
                        </a>

                        <a href="{{ route('reports.export.pdf', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]) }}"
                            class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                        </a>

                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Pemasukan</div>
                        <h4 class="fw-bold text-success mt-2">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Pengeluaran</div>
                        <h4 class="fw-bold text-danger mt-2">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Selisih Pemasukan dan Pengeluaran</div>
                        <h4 class="fw-bold mt-2 {{ $balance >= 0 ? 'text-primary' : 'text-danger' }}">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Grafik Pemasukan dan Pengeluaran</h5>
            </div>

            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="monthlyReportChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Laporan Kategori --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Ringkasan per Kategori</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Jumlah Transaksi</th>
                                <th class="text-end">Total Nominal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($categoryReports as $report)
                                <tr>
                                    <td>
                                        {{ $report->category?->name ?? 'Tanpa Kategori' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $report->total_transactions }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($report->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Belum ada data kategori pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Daftar Transaksi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Daftar Transaksi</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Dompet</th>
                                <th>Tipe</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transactionList as $transaction)
                                <tr>
                                    <td>
                                        {{ $transaction->transaction_date->format('d-m-Y') }}
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        {{ $transaction->category?->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $transaction->wallet?->name ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($transaction->type === 'income')
                                            <span class="badge bg-success">Pemasukan</span>
                                        @elseif($transaction->type === 'expense')
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactionList->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const monthlyReportData = @json($monthlyReports);

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];

        const labels = monthlyReportData.map(item => {
            const [year, month] = item.period.split('-');

            return monthNames[parseInt(month, 10) - 1] + ' ' + year;
        });

        const incomeData = monthlyReportData.map(item =>
            Number(item.total_income)
        );

        const expenseData = monthlyReportData.map(item =>
            Number(item.total_expense)
        );

        const chartElement = document.getElementById('monthlyReportChart');

        if (chartElement) {
            new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Pemasukan',
                            data: incomeData,
                            backgroundColor: '#198754',
                            borderRadius: 5
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenseData,
                            backgroundColor: '#dc3545',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' +
                                        new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' +
                                        new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
