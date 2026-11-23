@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Sumber Pemasukan</h4>
                <p class="text-muted mb-0">
                    Perbarui informasi sumber pemasukan.
                </p>
            </div>

            <a href="{{ route('income-sources.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-7">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <form action="{{ route('income-sources.update', $incomeSource->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Nama sumber --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nama Sumber Pemasukan
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $incomeSource->name) }}" placeholder="Contoh: Gaji Bulanan"
                                    required>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Jenis --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Jenis Pemasukan
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>

                                    <option value="">Pilih Jenis</option>

                                    <option value="salary" @selected(old('type', $incomeSource->type) === 'salary')>
                                        Gaji
                                    </option>

                                    <option value="freelance" @selected(old('type', $incomeSource->type) === 'freelance')>
                                        Freelance
                                    </option>

                                    <option value="business" @selected(old('type', $incomeSource->type) === 'business')>
                                        Bisnis
                                    </option>

                                    <option value="bonus" @selected(old('type', $incomeSource->type) === 'bonus')>
                                        Bonus
                                    </option>

                                    <option value="investment" @selected(old('type', $incomeSource->type) === 'investment')>
                                        Investasi
                                    </option>

                                    <option value="other" @selected(old('type', $incomeSource->type) === 'other')>
                                        Lainnya
                                    </option>
                                </select>

                                @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Deskripsi
                                </label>

                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                    placeholder="Keterangan tambahan (opsional)">{{ old('description', $incomeSource->description) }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('income-sources.index') }}" class="btn btn-light">
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
        </div>

    </div>
@endsection
