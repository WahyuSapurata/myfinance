@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Riwayat Setoran Tabungan</h4>
                <p class="text-muted mb-0">
                    {{ $savingsGoal->name }}
                </p>
            </div>

            <a href="{{ route('savings-goals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Target Tabungan</small>
                        <h5 class="fw-bold">
                            Rp{{ number_format($savingsGoal->target_amount, 0, ',', '.') }}
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Total Setoran Aktif</small>
                        <h5 class="fw-bold text-success">
                            Rp{{ number_format($totalSaved) }}
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Jumlah Riwayat</small>
                        <h5 class="fw-bold">
                            {{ $contributions->total() }} Setoran
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Daftar Setoran</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Dompet</th>
                                <th>Catatan</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($contributions as $contribution)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($contribution->contribution_date)->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $contribution->transaction?->wallet?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $contribution->notes ?? '-' }}
                                    </td>

                                    <td class="text-end fw-semibold text-success">
                                        Rp{{ number_format($contribution->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($contribution->transaction?->status === 'posted')
                                            <span class="badge bg-success">Berhasil</span>
                                        @elseif ($contribution->transaction?->status === 'cancelled')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak diketahui</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($contribution->transaction?->status === 'posted')
                                            <form
                                                action="{{ route('savings-goals.contribution.cancel', [$savingsGoal->id, $contribution->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin membatalkan setoran ini?');">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada riwayat setoran tabungan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $contributions->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
