@extends('layouts.app')

@section('title', 'Pengaturan Notifikasi')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">Pengaturan Notifikasi</h4>
            <p class="text-muted mb-0">
                Atur bagaimana MyFinance mengirimkan pemberitahuan kepada Anda.
            </p>
        </div>

        <form action="{{ route('notifications.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-bell me-2"></i>
                        Preferensi Notifikasi
                    </h6>
                </div>

                <div class="card-body">

                    {{-- Notifikasi Internal --}}
                    <div
                        class="d-flex justify-content-between align-items-center
                            py-3 border-bottom">
                        <div class="me-3">
                            <h6 class="mb-1">Notifikasi Pengingat</h6>
                            <p class="text-muted small mb-0">
                                Menerima notifikasi di dalam aplikasi MyFinance
                                ketika pengingat jatuh tempo.
                            </p>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="reminder_notification_enabled" value="0">

                            <input class="form-check-input" type="checkbox" role="switch"
                                id="reminder_notification_enabled" name="reminder_notification_enabled" value="1"
                                {{ $reminderEnabled == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Browser Notification --}}
                    <div class="d-flex justify-content-between align-items-center
                            py-3">
                        <div class="me-3">
                            <h6 class="mb-1">Notifikasi Browser</h6>
                            <p class="text-muted small mb-0">
                                Menampilkan pemberitahuan melalui Chrome
                                ketika ada pengingat baru.
                            </p>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="browser_notification_enabled" value="0">

                            <input class="form-check-input" type="checkbox" role="switch" id="browser_notification_enabled"
                                name="browser_notification_enabled" value="1"
                                {{ $browserEnabled == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Simpan Pengaturan
                </button>
            </div>

        </form>
    </div>
@endsection
