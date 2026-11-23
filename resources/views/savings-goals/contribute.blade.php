@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <a href="{{ route('savings-goals.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Target Tabungan
            </a>

            <h4 class="fw-bold mt-3 mb-1">Setor Tabungan</h4>
            <p class="text-muted mb-0">
                Tambahkan dana ke target tabunganmu.
            </p>
        </div>

        <div class="row">
            <div class="col-lg-8">

                {{-- Informasi Target --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded p-3 me-3"
                                style="background-color: {{ $savingsGoal->color ?? '#0d6efd' }}20;">
                                <i class="bi {{ $savingsGoal->icon ?? 'bi-piggy-bank' }} fs-3"
                                    style="color: {{ $savingsGoal->color ?? '#0d6efd' }};">
                                </i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-1">
                                    {{ $savingsGoal->name }}
                                </h6>

                                <p class="text-muted mb-0 small">
                                    Target:
                                    Rp {{ number_format($savingsGoal->target_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Setoran --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <form action="{{ route('savings-goals.contribute.store', $savingsGoal->id) }}" method="POST">

                            @csrf

                            {{-- Dompet --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Dompet Sumber Dana
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="wallet_id" class="form-select @error('wallet_id') is-invalid @enderror"
                                    required>

                                    <option value="">Pilih Dompet</option>

                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}" @selected(old('wallet_id') == $wallet->id)>
                                            {{ $wallet->name }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('wallet_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nominal --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nominal Setoran
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>

                                    <input type="number" name="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" min="1" step="0.01" placeholder="500000"
                                        required>
                                </div>

                                @error('amount')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tanggal --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Setoran
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date" name="contribution_date"
                                    class="form-control @error('contribution_date') is-invalid @enderror"
                                    value="{{ old('contribution_date', now()->format('Y-m-d')) }}" required>

                                @error('contribution_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Catatan --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Catatan
                                </label>

                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="Contoh: Setoran tabungan bulan November">{{ old('notes') }}</textarea>

                                @error('notes')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex justify-content-end gap-2">

                                <a href="{{ route('savings-goals.index') }}" class="btn btn-light">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-piggy-bank me-1"></i>
                                    Simpan Setoran
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
