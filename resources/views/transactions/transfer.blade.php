@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <a href="{{ route('transactions.index') }}" class="text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Transaksi
            </a>

            <h3 class="fw-bold mt-2 mb-1">Transfer Antar-Dompet</h3>
            <p class="text-muted mb-0">
                Pindahkan saldo dari satu dompet ke dompet lainnya.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Transfer belum berhasil.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($wallets->count() < 2)
            <div class="alert alert-warning">
                Anda membutuhkan minimal dua dompet aktif untuk melakukan transfer.
                <a href="{{ route('wallets.create') }}">Tambah dompet</a>.
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <form action="{{ route('transactions.transfer.store') }}" method="POST">
                        @csrf

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Dompet Asal</label>

                                <select name="wallet_id" class="form-select" required>
                                    <option value="">Pilih Dompet Asal</option>

                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}"
                                            {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                            {{ $wallet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Dompet Tujuan</label>

                                <select name="destination_wallet_id" class="form-select" required>
                                    <option value="">Pilih Dompet Tujuan</option>

                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}"
                                            {{ old('destination_wallet_id') == $wallet->id ? 'selected' : '' }}>
                                            {{ $wallet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Jumlah Transfer (Rp)</label>

                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                    placeholder="Contoh: 500000" value="{{ old('amount') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Transfer</label>

                                <input type="date" name="transaction_date" class="form-control"
                                    value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nomor Referensi</label>

                                <input type="text" name="reference_number" class="form-control" placeholder="Opsional"
                                    value="{{ old('reference_number') }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Keterangan</label>

                                <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Transfer tabungan bulanan">{{ old('description') }}</textarea>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('transactions.index') }}" class="btn btn-light">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-left-right me-1"></i>
                                Simpan Transfer
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        @endif

    </div>
@endsection
