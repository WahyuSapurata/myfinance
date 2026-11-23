@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Riwayat Pengingat</h4>
                <p class="text-muted mb-0">{{ $reminder->title }}</p>
            </div>

            <a href="{{ route('reminders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <small class="text-muted">Nominal Pengingat</small>
                        <h5 class="mb-0">
                            {{ $reminder->amount !== null ? 'Rp ' . number_format($reminder->amount, 0, ',', '.') : '-' }}
                        </h5>
                    </div>

                    <div class="col-md-4 mb-2">
                        <small class="text-muted">Jadwal Berikutnya</small>
                        <h5 class="mb-0">
                            {{ \Carbon\Carbon::parse($reminder->due_date)->format('d M Y') }}
                        </h5>
                    </div>

                    <div class="col-md-4 mb-2">
                        <small class="text-muted">Pengulangan</small>
                        <h5 class="mb-0">
                            {{ ucfirst($reminder->repeat_type) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Daftar Penyelesaian</strong>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th>Waktu Diselesaikan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($histories as $index => $history)
                                <tr>
                                    <td>
                                        {{ $histories->firstItem() + $index }}
                                    </td>

                                    <td>
                                        {{ $history->due_date->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $history->amount !== null ? 'Rp ' . number_format($history->amount, 0, ',', '.') : '-' }}
                                    </td>

                                    <td>
                                        {{ $history->completed_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada riwayat penyelesaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
