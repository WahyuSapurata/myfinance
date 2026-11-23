@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <a href="{{ route('savings-goals.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Target Tabungan
            </a>

            <h4 class="fw-bold mt-3 mb-1">Tambah Target Tabungan</h4>
            <p class="text-muted mb-0">
                Tentukan tujuan keuangan yang ingin kamu capai.
            </p>
        </div>

        <div class="row">
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <form action="{{ route('savings-goals.store') }}" method="POST">

                            @csrf

                            {{-- Nama Target --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nama Target <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Contoh: Beli Laptop Baru" required>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nominal Target --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nominal Target <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>

                                    <input type="number" name="target_amount"
                                        class="form-control @error('target_amount') is-invalid @enderror"
                                        value="{{ old('target_amount') }}" min="1" step="0.01"
                                        placeholder="10000000" required>
                                </div>

                                @error('target_amount')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tanggal Target --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Target
                                </label>

                                <input type="date" name="target_date"
                                    class="form-control @error('target_date') is-invalid @enderror"
                                    value="{{ old('target_date') }}">

                                @error('target_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Deskripsi
                                </label>

                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Ceritakan tujuan tabunganmu...">{{ old('description') }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Ikon --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ikon Bootstrap
                                </label>

                                <select name="icon" class="form-select">
                                    <option value="bi-piggy-bank" @selected(old('icon', 'bi-piggy-bank') === 'bi-piggy-bank')>
                                        Celengan
                                    </option>

                                    <option value="bi-laptop" @selected(old('icon') === 'bi-laptop')>
                                        Laptop
                                    </option>

                                    <option value="bi-car-front" @selected(old('icon') === 'bi-car-front')>
                                        Mobil
                                    </option>

                                    <option value="bi-house" @selected(old('icon') === 'bi-house')>
                                        Rumah
                                    </option>

                                    <option value="bi-airplane" @selected(old('icon') === 'bi-airplane')>
                                        Liburan
                                    </option>

                                    <option value="bi-mortarboard" @selected(old('icon') === 'bi-mortarboard')>
                                        Pendidikan
                                    </option>

                                    <option value="bi-heart-pulse" @selected(old('icon') === 'bi-heart-pulse')>
                                        Kesehatan
                                    </option>

                                    <option value="bi-gift" @selected(old('icon') === 'bi-gift')>
                                        Hadiah
                                    </option>
                                </select>
                            </div>

                            {{-- Warna --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Warna Target
                                </label>

                                <input type="color" name="color" class="form-control form-control-color"
                                    value="{{ old('color', '#0d6efd') }}">
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex justify-content-end gap-2">

                                <a href="{{ route('savings-goals.index') }}" class="btn btn-light">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    Simpan Target
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
