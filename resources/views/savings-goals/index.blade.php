@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Target Tabungan</h4>
                <p class="text-muted mb-0">
                    Pantau dan kelola target keuanganmu.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('savings-goals.archived') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-archive me-1"></i>
                    Arsip
                </a>

                <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Buat Target Tabungan
                </a>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="row g-3 mb-4">

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-bullseye fs-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Total Target</p>
                                <h5 class="fw-bold mb-0">
                                    Rp {{ number_format($totalTarget, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-piggy-bank fs-4 text-success"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Total Terkumpul</p>
                                <h5 class="fw-bold mb-0">
                                    Rp {{ number_format($totalSaved, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <form action="{{ route('savings-goals.index') }}" method="GET">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Cari Target</label>

                            <input type="text" name="search" class="form-control" placeholder="Masukkan nama target..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Filter Status</label>

                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>

                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                    Selesai
                                </option>

                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                    Dibatalkan
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">

                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-search"></i>
                                    Cari
                                </button>

                                <a href="{{ route('savings-goals.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>

                            </div>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        {{-- Daftar Target --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Daftar Target Tabungan</h6>
            </div>

            <div class="card-body">

                @forelse($goals as $goal)
                    <div class="border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">

                                <div class="rounded p-3 me-3" style="background-color: {{ $goal->color ?? '#e9ecef' }}20;">
                                    <i class="bi {{ $goal->icon ?? 'bi-piggy-bank' }} fs-4"
                                        style="color: {{ $goal->color ?? '#6c757d' }};">
                                    </i>
                                </div>

                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ $goal->name }}
                                    </h6>

                                    @if ($goal->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($goal->status === 'active')
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($goal->status) }}</span>
                                    @endif

                                    <small class="text-muted">
                                        @if ($goal->target_date)
                                            Target:
                                            {{ \Carbon\Carbon::parse($goal->target_date)->format('d M Y') }}
                                        @else
                                            Tanggal target belum ditentukan
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <span class="badge bg-primary">
                                {{ number_format($goal->progress, 1) }}%
                            </span>
                        </div>

                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $goal->progress }}%;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <small class="text-success fw-semibold">
                                Rp {{ number_format($goal->total_saved, 0, ',', '.') }}
                                terkumpul
                            </small>

                            <small class="text-muted">
                                Target:
                                Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                            </small>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('savings-goals.contribute', $goal->id) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-circle me-1"></i>
                                Setor Tabungan
                            </a>

                            <a href="{{ route('savings-goals.edit', $goal->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Target
                            </a>

                            <a href="{{ route('savings-goals.history', $goal->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-clock-history me-1"></i>
                                Riwayat Setoran
                            </a>

                            @if ($goal->status === 'active' && $goal->progress >= 100)
                                <form action="{{ route('savings-goals.complete', $goal->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Selesaikan target tabungan ini?');">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Selesaikan Target
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('savings-goals.archive', $goal->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Arsipkan target tabungan ini?');">

                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-archive me-1"></i>
                                    Arsipkan
                                </button>
                            </form>
                        </div>

                        @if ($goal->description)
                            <p class="text-muted small mt-3 mb-0">
                                {{ $goal->description }}
                            </p>
                        @endif

                    </div>

                @empty

                    <div class="text-center py-5">
                        <i class="bi bi-piggy-bank display-4 text-muted"></i>

                        <h6 class="fw-bold mt-3">
                            Belum Ada Target Tabungan
                        </h6>

                        <p class="text-muted">
                            Mulai buat target tabungan pertamamu.
                        </p>

                        <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">
                            Buat Target Tabungan
                        </a>
                    </div>
                @endforelse

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $goals->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
