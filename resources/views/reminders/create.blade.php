@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Tambah Pengingat</h4>
                <p class="text-muted mb-0">
                    Buat pengingat untuk tagihan dan pembayaran Anda.
                </p>
            </div>

            <a href="{{ route('reminders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <form action="{{ route('reminders.store') }}" method="POST">
                            @csrf

                            {{-- Judul --}}
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">
                                    Nama Pengingat <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="Contoh: Bayar listrik bulanan" required>

                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nominal --}}
                            <div class="mb-3">
                                <label for="amount" class="form-label fw-semibold">
                                    Nominal Pembayaran
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>

                                    <input type="number" name="amount" id="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="Contoh: 250000" min="0"
                                        step="0.01">

                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <small class="text-muted">
                                    Kosongkan jika nominal belum diketahui.
                                </small>
                            </div>

                            {{-- Tanggal dan Waktu --}}
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label fw-semibold">
                                        Tanggal Jatuh Tempo
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="date" name="due_date" id="due_date"
                                        class="form-control @error('due_date') is-invalid @enderror"
                                        value="{{ old('due_date') }}" required>

                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="reminder_time" class="form-label fw-semibold">
                                        Waktu Pengingat
                                    </label>

                                    <input type="time" name="reminder_time" id="reminder_time"
                                        class="form-control @error('reminder_time') is-invalid @enderror"
                                        value="{{ old('reminder_time') }}">

                                    @error('reminder_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Pengulangan --}}
                            <div class="mb-3">
                                <label for="repeat_type" class="form-label fw-semibold">
                                    Pengulangan <span class="text-danger">*</span>
                                </label>

                                <select name="repeat_type" id="repeat_type"
                                    class="form-select @error('repeat_type') is-invalid @enderror" required>
                                    <option value="none" @selected(old('repeat_type', 'none') == 'none')>
                                        Tidak berulang
                                    </option>

                                    <option value="daily" @selected(old('repeat_type') == 'daily')>
                                        Setiap hari
                                    </option>

                                    <option value="weekly" @selected(old('repeat_type') == 'weekly')>
                                        Setiap minggu
                                    </option>

                                    <option value="monthly" @selected(old('repeat_type') == 'monthly')>
                                        Setiap bulan
                                    </option>

                                    <option value="yearly" @selected(old('repeat_type') == 'yearly')>
                                        Setiap tahun
                                    </option>
                                </select>

                                @error('repeat_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold">
                                    Keterangan
                                </label>

                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Tambahkan keterangan jika diperlukan...">{{ old('description') }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('reminders.index') }}" class="btn btn-light">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    Simpan Pengingat
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

            {{-- Informasi --}}
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Informasi Pengingat
                        </h6>

                        <div class="mb-3">
                            <div class="fw-semibold">Tidak berulang</div>
                            <small class="text-muted">
                                Pengingat hanya berlaku untuk satu tanggal.
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold">Harian</div>
                            <small class="text-muted">
                                Pengingat berulang setiap hari.
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold">Mingguan</div>
                            <small class="text-muted">
                                Pengingat berulang setiap minggu.
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold">Bulanan</div>
                            <small class="text-muted">
                                Pengingat berulang setiap bulan.
                            </small>
                        </div>

                        <div>
                            <div class="fw-semibold">Tahunan</div>
                            <small class="text-muted">
                                Pengingat berulang setiap tahun.
                            </small>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
