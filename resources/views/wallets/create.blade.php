@extends('layouts.app')

@section('title', 'Tambah Dompet')
@section('page-title', 'Tambah Dompet')

@section('content')

    <div class="mb-4">
        <a href="{{ route('wallets.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Kembali ke Dompet
        </a>
        <h4 class="fw-bold mt-3 mb-1">Tambah Dompet Baru</h4>
        <p class="text-secondary">
            Masukkan informasi dompet atau rekening yang ingin ditambahkan.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <form action="{{ route('wallets.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nama Dompet
                            </label>

                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: BCA, Kas Tunai, GoPay" required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Jenis Dompet
                            </label>

                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Pilih jenis dompet</option>
                                <option value="cash" @selected(old('type') == 'cash')>
                                    Uang Tunai
                                </option>
                                <option value="bank" @selected(old('type') == 'bank')>
                                    Rekening Bank
                                </option>
                                <option value="ewallet" @selected(old('type') == 'ewallet')>
                                    E-Wallet
                                </option>
                                <option value="savings" @selected(old('type') == 'savings')>
                                    Tabungan
                                </option>
                                <option value="other" @selected(old('type') == 'other')>
                                    Lainnya
                                </option>
                            </select>

                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Saldo Awal
                            </label>

                            <input type="number" name="initial_balance"
                                class="form-control @error('initial_balance') is-invalid @enderror"
                                value="{{ old('initial_balance', 0) }}" min="0" step="0.01" required>

                            @error('initial_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-secondary">
                                Masukkan saldo yang sudah tersedia saat dompet dibuat.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nomor Rekening (Opsional)
                            </label>

                            <input type="text" name="account_number"
                                class="form-control @error('account_number') is-invalid @enderror"
                                value="{{ old('account_number') }}" placeholder="Nomor rekening atau identitas dompet">

                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('wallets.index') }}" class="btn btn-light">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan Dompet
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
