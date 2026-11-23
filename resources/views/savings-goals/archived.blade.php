@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Arsip Target Tabungan</h4>
                <p class="text-muted mb-0">
                    Daftar target tabungan yang telah diarsipkan.
                </p>
            </div>

            <a href="{{ route('savings-goals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Target</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($goals as $goal)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $goal->name }}
                                    </td>

                                    <td>
                                        Rp{{ number_format($goal->target_amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if ($goal->status === 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($goal->status === 'cancelled')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge bg-primary">Aktif</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <form action="{{ route('savings-goals.restore', $goal->id) }}" method="POST"
                                            onsubmit="return confirm('Pulihkan target ini?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                                Pulihkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada target yang diarsipkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $goals->links() }}

            </div>
        </div>

    </div>
@endsection
