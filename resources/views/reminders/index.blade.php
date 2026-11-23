@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Pengingat Pembayaran</h4>
                <p class="text-muted mb-0">
                    Kelola tagihan dan jadwal pembayaran Anda.
                </p>
            </div>

            <a href="{{ route('reminders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengingat
            </a>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Total Pengingat</p>
                                <h4 class="fw-bold mb-0">
                                    {{ $totalReminders }}
                                </h4>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="bi bi-bell"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Menunggu Pembayaran</p>
                                <h4 class="fw-bold text-warning mb-0">
                                    {{ $pendingReminders }}
                                </h4>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Selesai</p>
                                <h4 class="fw-bold text-success mb-0">
                                    {{ $completedReminders }}
                                </h4>
                            </div>
                            <div class="text-success fs-2">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Search and Filter --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <form action="{{ route('reminders.index') }}" method="GET">
                    <div class="row g-2">

                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama pengingat..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(request('status') == 'pending')>
                                    Menunggu
                                </option>
                                <option value="completed" @selected(request('status') == 'completed')>
                                    Selesai
                                </option>
                                <option value="cancelled" @selected(request('status') == 'cancelled')>
                                    Dibatalkan
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Cari
                            </button>

                            <a href="{{ route('reminders.index') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Reminder Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Pengingat</th>
                                <th>Nominal</th>
                                <th>Jatuh Tempo</th>
                                <th>Pengulangan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($reminders as $reminder)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $reminder->title }}
                                        </div>

                                        @if ($reminder->description)
                                            <small class="text-muted">
                                                {{ $reminder->description }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($reminder->amount !== null)
                                            Rp{{ number_format($reminder->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        <div>
                                            {{ \Carbon\Carbon::parse($reminder->due_date)->format('d/m/Y') }}
                                        </div>

                                        @if ($reminder->reminder_time)
                                            <small class="text-muted">
                                                {{ substr($reminder->reminder_time, 0, 5) }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @switch($reminder->repeat_type)
                                            @case('none')
                                                Sekali
                                            @break

                                            @case('daily')
                                                Harian
                                            @break

                                            @case('weekly')
                                                Mingguan
                                            @break

                                            @case('monthly')
                                                Bulanan
                                            @break

                                            @case('yearly')
                                                Tahunan
                                            @break
                                        @endswitch
                                    </td>

                                    <td>
                                        @if ($reminder->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                Menunggu
                                            </span>
                                        @elseif($reminder->status === 'completed')
                                            <span class="badge bg-success">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Edit --}}
                                            <a href="{{ route('reminders.edit', $reminder->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            {{-- Update Status --}}
                                            @if ($reminder->status === 'pending')
                                                <form action="{{ route('reminders.update-status', $reminder->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="status" value="completed">

                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        title="Tandai selesai">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('reminders.update-status', $reminder->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="status" value="cancelled">

                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                        title="Batalkan">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('reminders.destroy', $reminder->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus pengingat ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('reminders.histories', $reminder) }}"
                                                class="btn btn-sm btn-outline-info" title="Lihat Riwayat">
                                                <i class="bi bi-clock-history"></i> Riwayat
                                            </a>

                                        </div>
                                    </td>
                                </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">
                                                Belum ada pengingat pembayaran.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $reminders->links() }}
                    </div>

                </div>
            </div>

        </div>
    @endsection
