@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Target Tabungan</h4>
                <p class="text-muted mb-0">Perbarui informasi target tabungan.</p>
            </div>

            <a href="{{ route('savings-goals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <form action="{{ route('savings-goals.update', $savingsGoal->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Target</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $savingsGoal->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nominal Target (Rp)</label>
                        <input type="number" name="target_amount"
                            class="form-control @error('target_amount') is-invalid @enderror"
                            value="{{ old('target_amount', $savingsGoal->target_amount) }}" min="0.01" step="0.01"
                            required>
                        @error('target_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Target</label>
                        <input type="date" name="target_date" class="form-control"
                            value="{{ old('target_date', $savingsGoal->target_date ? \Carbon\Carbon::parse($savingsGoal->target_date)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $savingsGoal->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ikon Bootstrap</label>
                        <input type="text" name="icon" class="form-control"
                            value="{{ old('icon', $savingsGoal->icon) }}" placeholder="bi-phone">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Warna</label>
                        <input type="color" name="color" class="form-control form-control-color"
                            value="{{ old('color', $savingsGoal->color ?: '#198754') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('savings-goals.index') }}" class="btn btn-light">
                        Batal
                    </a>

                </form>
            </div>
        </div>

    </div>
@endsection
