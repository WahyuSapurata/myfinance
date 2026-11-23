@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Pengingat</h4>
                <p class="text-muted mb-0">
                    Perbarui informasi pengingat pembayaran.
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

                        <form action="{{ route('reminders.update', $reminder->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Nama Pengingat --}}
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">
                                    Nama Pengingat <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $reminder->title) }}" placeholder="Contoh: Bayar listrik bulanan"
                                    required>

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
                                        value="{{ old('amount', $reminder->amount) }}" placeholder="Contoh: 250000"
                                        min="0" step="0.01">

                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                        value="{{ old('due_date', \Carbon\Carbon::parse($reminder->due_date)->format('Y-m-d')) }}"
                                        required>

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
                                        value="{{ old('reminder_time', $reminder->reminder_time ? substr($reminder->reminder_time, 0, 5) : '') }}">

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
                                    <option value="none" @selected(old('repeat_type', $reminder->repeat_type) == 'none')>
                                        Tidak berulang
                                    </option>

                                    <option value="daily" @selected(old('repeat_type', $reminder->repeat_type) == 'daily')>
                                        Setiap hari
                                    </option>

                                    <option value="weekly" @selected(old('repeat_type', $reminder->repeat_type) == 'weekly')>
                                        Setiap minggu
                                    </option>

                                    <option value="monthly" @selected(old('repeat_type', $reminder->repeat_type) == 'monthly')>
                                        Setiap bulan
                                    </option>

                                    <option value="yearly" @selected(old('repeat_type', $reminder->repeat_type) == 'yearly')>
                                        Setiap tahun
                                    </option>
                                </select>

                                @error('repeat_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold">
                                    Keterangan
                                </label>

                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Tambahkan keterangan...">{{ old('description', $reminder->description) }}</textarea>

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
                                    Simpan Perubahan
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

                        <p class="text-muted small">
                            Perubahan informasi akan langsung disimpan pada
                            pengingat yang dipilih.
                        </p>

                        <hr>

                        <div class="mb-2">
                            <small class="text-muted">Status Saat Ini</small>

                            <div>
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
                            </div>
                        </div>

                        <div>
                            <small class="text-muted">Dibuat Pada</small>
                            <div class="fw-semibold">
                                {{ $reminder->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
