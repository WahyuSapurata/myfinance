@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    @php
        $formatRupiah = fn($amount) => 'Rp ' . number_format((float) $amount, 0, ',', '.');
    @endphp

    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            Selamat Datang, {{ Auth::user()->name }}!
        </h4>
        <p class="text-secondary mb-0">
            Berikut ringkasan kondisi keuangan Anda.
        </p>
    </div>

    {{-- Ringkasan Keuangan --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Total Saldo</span>
                        <i class="bi bi-wallet2 text-primary fs-4"></i>
                    </div>
                    <h4 class="fw-bold mt-3 mb-1">
                        {{ $formatRupiah($totalBalance) }}
                    </h4>
                    <small class="text-secondary">Seluruh dompet</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Pemasukan</span>
                        <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-success mt-3 mb-1">
                        {{ $formatRupiah($monthlyIncome) }}
                    </h4>
                    <small class="text-secondary">Bulan ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Pengeluaran</span>
                        <i class="bi bi-arrow-up-circle text-danger fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-danger mt-3 mb-1">
                        {{ $formatRupiah($monthlyExpense) }}
                    </h4>
                    <small class="text-secondary">Bulan ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Sisa Uang</span>
                        <i class="bi bi-piggy-bank text-warning fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-primary mt-3 mb-1">
                        {{ $formatRupiah($monthlyBalance) }}
                    </h4>
                    <small class="text-secondary">Pemasukan - Pengeluaran</small>
                </div>
            </div>
        </div>

    </div>


    {{-- Ringkasan Notifikasi --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary">Notifikasi Belum Dibaca</span>
                            <h3 class="fw-bold mt-2 mb-1">
                                {{ $unreadNotifications }}
                            </h3>
                            <small class="text-secondary">
                                Notifikasi yang perlu diperiksa
                            </small>
                        </div>
                        <i class="bi bi-bell text-primary fs-1"></i>
                    </div>

                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                        Lihat Notifikasi
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary">Pengingat Terlambat</span>
                            <h3 class="fw-bold text-danger mt-2 mb-1">
                                {{ $overdueReminders }}
                            </h3>
                            <small class="text-secondary">
                                Pengingat yang melewati tanggal jatuh tempo
                            </small>
                        </div>
                        <i class="bi bi-calendar-x text-danger fs-1"></i>
                    </div>

                    <a href="{{ route('reminders.index') }}" class="btn btn-sm btn-outline-danger mt-3">
                        Lihat Pengingat
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Notifikasi Terbaru --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Notifikasi Terbaru</h5>

                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>

            @forelse ($recentNotifications as $notification)
                <div class="d-flex align-items-start justify-content-between border-bottom py-3">

                    <div class="d-flex align-items-start">
                        <i class="bi bi-bell-fill text-primary me-3 mt-1"></i>

                        <div>
                            <div class="fw-semibold">
                                {{ $notification->title }}
                            </div>

                            <div class="text-secondary small">
                                {{ $notification->message }}
                            </div>

                            <small class="text-secondary">
                                {{ $notification->created_at->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>

                    @if (!$notification->read_at)
                        <span class="badge bg-primary">Baru</span>
                    @else
                        <span class="badge bg-secondary">Dibaca</span>
                    @endif

                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-bell-slash fs-1 text-secondary"></i>
                    <p class="text-secondary mt-2 mb-0">
                        Belum ada notifikasi.
                    </p>
                </div>
            @endforelse

        </div>
    </div>

    {{-- Grafik dan Target Tabungan --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Grafik Keuangan</h5>
                    <canvas id="financeChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Target Tabungan</h5>

                    @forelse ($savingsGoals as $goal)
                        @php
                            $saved = (float) ($goal->contributions_sum_amount ?? 0);
                            $target = (float) $goal->target_amount;
                            $percentage = $target > 0 ? min(100, ($saved / $target) * 100) : 0;
                        @endphp

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">{{ $goal->name }}</span>
                                <span class="text-secondary">
                                    {{ number_format($percentage, 0) }}%
                                </span>
                            </div>

                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                                </div>
                            </div>

                            <small class="text-secondary">
                                {{ $formatRupiah($saved) }}
                                / {{ $formatRupiah($target) }}
                            </small>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-piggy-bank fs-1 text-secondary"></i>
                            <p class="text-secondary mt-2 mb-0">
                                Belum ada target tabungan.
                            </p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Transaksi Terbaru</h5>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Kategori</th>
                            <th>Dompet</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    {{ $transaction->transaction_date ? $transaction->transaction_date->format('d M Y') : '-' }}
                                </td>

                                <td>
                                    {{ $transaction->description ?: '-' }}
                                </td>

                                <td>
                                    @if ($transaction->type === 'income')
                                        <span class="badge bg-success-subtle text-success">
                                            {{ $transaction->category?->name ?? 'Pemasukan' }}
                                        </span>
                                    @elseif ($transaction->type === 'expense')
                                        <span class="badge bg-danger-subtle text-danger">
                                            {{ $transaction->category?->name ?? 'Pengeluaran' }}
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary">
                                            Transfer
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $transaction->wallet?->name ?? '-' }}

                                    @if ($transaction->type === 'transfer')
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        {{ $transaction->destinationWallet?->name ?? '-' }}
                                    @endif
                                </td>

                                <td
                                    class="text-end fw-semibold
                                {{ $transaction->type === 'income'
                                    ? 'text-success'
                                    : ($transaction->type === 'expense'
                                        ? 'text-danger'
                                        : 'text-primary') }}">

                                    {{ $transaction->type === 'income' ? '+' : ($transaction->type === 'expense' ? '-' : '') }}

                                    {{ $formatRupiah($transaction->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    Belum ada transaksi. Silakan mulai mencatat keuangan Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('financeChart');

        new Chart(ctx, {
            type: 'bar',

            data: {
                labels: @json($chartLabels),

                datasets: [{
                        label: 'Pemasukan',
                        data: @json($chartIncome),
                        backgroundColor: '#22c55e',
                        borderRadius: 5
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($chartExpense),
                        backgroundColor: '#ef4444',
                        borderRadius: 5
                    }
                ]
            },

            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
