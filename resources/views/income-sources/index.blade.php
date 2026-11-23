@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Sumber Pemasukan</h4>
                <p class="text-muted mb-0">
                    Kelola sumber pemasukan tetap dan tidak tetap.
                </p>
            </div>

            <a href="{{ route('income-sources.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Sumber
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

        {{-- Filter --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('income-sources.index') }}" method="GET">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Cari Sumber</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama sumber pemasukan..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Jenis</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Jenis</option>

                                <option value="salary" @selected(request('type') === 'salary')>
                                    Gaji
                                </option>

                                <option value="freelance" @selected(request('type') === 'freelance')>
                                    Freelance
                                </option>

                                <option value="business" @selected(request('type') === 'business')>
                                    Bisnis
                                </option>

                                <option value="bonus" @selected(request('type') === 'bonus')>
                                    Bonus
                                </option>

                                <option value="investment" @selected(request('type') === 'investment')>
                                    Investasi
                                </option>

                                <option value="other" @selected(request('type') === 'other')>
                                    Lainnya
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="active" @selected(request('status') === 'active')>
                                    Aktif
                                </option>
                                <option value="inactive" @selected(request('status') === 'inactive')>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>
                                Cari
                            </button>
                        </div>

                    </div>

                    @if (request()->hasAny(['search', 'type', 'status']))
                        <div class="mt-3">
                            <a href="{{ route('income-sources.index') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>Nama Sumber</th>
                                <th>Jenis</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th width="190">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($incomeSources as $source)
                                <tr>
                                    <td>
                                        {{ $incomeSources->firstItem() + $loop->index }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $source->name }}
                                    </td>

                                    <td>
                                        @switch($source->type)
                                            @case('salary')
                                                <span class="badge bg-primary">Gaji</span>
                                            @break

                                            @case('freelance')
                                                <span class="badge bg-info text-dark">Freelance</span>
                                            @break

                                            @case('business')
                                                <span class="badge bg-success">Bisnis</span>
                                            @break

                                            @case('bonus')
                                                <span class="badge bg-warning text-dark">Bonus</span>
                                            @break

                                            @case('investment')
                                                <span class="badge bg-secondary">Investasi</span>
                                            @break

                                            @default
                                                <span class="badge bg-light text-dark">Lainnya</span>
                                        @endswitch
                                    </td>

                                    <td>
                                        {{ $source->description ?: '-' }}
                                    </td>

                                    <td>
                                        @if ($source->is_active)
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

                                            <a href="{{ route('income-sources.edit', $source->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form action="{{ route('income-sources.toggle-status', $source->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="btn btn-sm {{ $source->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    title="{{ $source->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i
                                                        class="bi {{ $source->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada sumber pemasukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $incomeSources->links() }}
                    </div>

                </div>
            </div>

        </div>
    @endsection
