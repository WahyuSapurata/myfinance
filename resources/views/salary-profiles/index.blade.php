@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Profil Gaji Tetap</h4>
                <p class="text-muted mb-0">
                    Kelola informasi gaji bulanan dan dompet penerima.
                </p>
            </div>

            <a href="{{ route('salary-profiles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Profil Gaji
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Ringkasan --}}

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total Profil Gaji</div>
                        <h4 class="fw-bold mb-0">
                            {{ number_format($totalProfiles, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Profil Gaji Aktif</div>
                        <h4 class="fw-bold mb-0">
                            {{ number_format($activeProfiles, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total Gaji Aktif per Bulan</div>
                        <h4 class="fw-bold text-success mb-0">
                            Rp {{ number_format($totalActiveSalary, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Profil --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Profil</th>
                                <th>Sumber Pemasukan</th>
                                <th>Nominal</th>
                                <th>Dompet</th>
                                <th>Tanggal Gajian</th>
                                <th>Status</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($salaryProfiles as $profile)
                                <tr>
                                    <td>
                                        {{ $salaryProfiles->firstItem() + $loop->index }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $profile->name }}
                                    </td>

                                    <td>
                                        {{ $profile->incomeSource->name ?? '-' }}
                                    </td>

                                    <td class="fw-semibold text-success">
                                        Rp {{ number_format($profile->amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $profile->wallet->name ?? '-' }}
                                    </td>

                                    <td>
                                        Tanggal {{ $profile->pay_day }}
                                    </td>

                                    <td>
                                        @if ($profile->is_active)
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="{{ route('salary-profiles.edit', $profile->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form action="{{ route('salary-profiles.toggle-status', $profile->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="btn btn-sm {{ $profile->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    title="{{ $profile->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i
                                                        class="bi {{ $profile->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
                                        Belum ada profil gaji.
                                        <div class="mt-2">
                                            <a href="{{ route('salary-profiles.create') }}">
                                                Tambahkan profil gaji pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $salaryProfiles->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
