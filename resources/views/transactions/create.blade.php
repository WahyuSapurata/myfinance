@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <a href="{{ route('transactions.index') }}" class="text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Transaksi
            </a>

            <h3 class="fw-bold mt-2 mb-1">Tambah Transaksi</h3>
            <p class="text-muted mb-0">
                Catat pemasukan atau pengeluaran keuangan Anda.
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

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Transaksi</label>

                            <select name="type" id="transactionType" class="form-select" required>
                                <option value="">Pilih Jenis Transaksi</option>
                                <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>
                                    Pemasukan
                                </option>
                                <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>
                                    Pengeluaran
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Dompet</label>

                            <select name="wallet_id" class="form-select" required>
                                <option value="">Pilih Dompet</option>

                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}"
                                        {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($wallets->isEmpty())
                                <small class="text-danger">
                                    Belum ada dompet aktif. Silakan buat dompet terlebih dahulu.
                                </small>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kategori</label>

                            <select name="category_id" id="categorySelect" class="form-select" required>
                                <option value="">Pilih Kategori</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jumlah (Rp)</label>

                            <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                placeholder="Contoh: 500000" value="{{ old('amount') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Transaksi</label>

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

                            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Gaji bulanan November">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Simpan Transaksi
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('transactionType');
            const categorySelect = document.getElementById('categorySelect');

            function filterCategories() {
                const selectedType = typeSelect.value;
                const options = categorySelect.querySelectorAll('option[data-type]');

                let selectedOptionStillValid = false;

                options.forEach(function(option) {
                    const visible = option.dataset.type === selectedType;

                    option.hidden = !visible;

                    if (visible && option.value === categorySelect.value) {
                        selectedOptionStillValid = true;
                    }
                });

                if (!selectedOptionStillValid) {
                    categorySelect.value = '';
                }
            }

            typeSelect.addEventListener('change', filterCategories);

            filterCategories();
        });
    </script>
@endpush
