@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <a href="{{ route('transactions.index') }}" class="text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Transaksi
            </a>

            <h3 class="fw-bold mt-2 mb-1">Edit Transaksi</h3>
            <p class="text-muted mb-0">
                Perbarui informasi transaksi Anda.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali data yang dimasukkan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <div class="mb-3">
                    <span class="badge {{ $transaction->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                        {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                    </span>
                </div>

                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Dompet</label>

                            <select name="wallet_id" class="form-select" required>
                                <option value="">Pilih Dompet</option>

                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}"
                                        {{ old('wallet_id', $transaction->wallet_id) == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }}
                                        {{ !$wallet->is_active ? '(Nonaktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kategori</label>

                            <select name="category_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jumlah (Rp)</label>

                            <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                value="{{ old('amount', $transaction->amount) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Transaksi</label>

                            <input type="date" name="transaction_date" class="form-control"
                                value="{{ old('transaction_date', \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Referensi</label>

                            <input type="text" name="reference_number" class="form-control"
                                value="{{ old('reference_number', $transaction->reference_number) }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>

                            <textarea name="description" class="form-control" rows="3">{{ old('description', $transaction->description) }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light">
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
@endsection
