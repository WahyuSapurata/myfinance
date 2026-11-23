@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Profil Gaji</h4>
                <p class="text-muted mb-0">Perbarui informasi profil gaji Anda.</p>
            </div>

            <a href="{{ route('salary-profiles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('salary-profiles.update', $salaryProfile->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Profil Gaji</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $salaryProfile->name) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sumber Pemasukan</label>
                            <select name="income_source_id" class="form-select" required>
                                <option value="">Pilih sumber pemasukan</option>

                                @foreach ($incomeSources as $source)
                                    <option value="{{ $source->id }}"
                                        {{ old('income_source_id', $salaryProfile->income_source_id) == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Gaji (Rp)</label>
                            <input type="number" name="amount" class="form-control"
                                value="{{ old('amount', $salaryProfile->amount) }}" min="1" step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dompet Penerima</label>
                            <select name="wallet_id" class="form-select" required>
                                <option value="">Pilih dompet</option>

                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}"
                                        {{ old('wallet_id', $salaryProfile->wallet_id) == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Gajian</label>
                            <input type="number" name="pay_day" class="form-control"
                                value="{{ old('pay_day', $salaryProfile->pay_day) }}" min="1" max="31"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', $salaryProfile->start_date?->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Berakhir</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ old('end_date', $salaryProfile->end_date?->format('Y-m-d')) }}">
                            <small class="text-muted">
                                Kosongkan jika pekerjaan masih berlangsung.
                            </small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $salaryProfile->notes) }}</textarea>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('salary-profiles.index') }}" class="btn btn-light">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
