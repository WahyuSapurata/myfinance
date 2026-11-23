@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Notifikasi</h4>
                <p class="text-muted mb-0">
                    Daftar pemberitahuan pengingat keuangan Anda.
                </p>
            </div>

            <div class="d-flex gap-2 align-items-center">
                @if ($unreadCount > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-check2-all"></i>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif

                <button type="button" id="enable-browser-notification" class="btn btn-outline-primary">
                    <i class="bi bi-bell"></i>
                    Aktifkan Notifikasi Browser
                </button>
            </div>
        </div>


        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Notifikasi</p>
                            <h3 class="fw-bold mb-0">
                                {{ $totalNotifications }}
                            </h3>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Belum Dibaca</p>
                            <h3 class="fw-bold text-danger mb-0">
                                {{ $unreadCount }}
                            </h3>
                        </div>
                        <div class="fs-1 text-danger">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Sudah Dibaca</p>
                            <h3 class="fw-bold text-success mb-0">
                                {{ $readCount }}
                            </h3>
                        </div>
                        <div class="fs-1 text-success">
                            <i class="bi bi-envelope-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="{{ route('notifications.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari judul atau pesan..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>
                                    Belum Dibaca
                                </option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                                    Sudah Dibaca
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>

                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <hr>

                @forelse($notifications as $notification)
                    <div
                        class="d-flex justify-content-between align-items-start border-bottom py-3
                    {{ $notification->read_at ? '' : 'bg-light rounded px-3' }}">

                        <div class="d-flex gap-3">
                            <div class="fs-4 text-primary">
                                <i class="bi bi-bell{{ $notification->read_at ? '' : '-fill' }}"></i>
                            </div>

                            <div>
                                <h6 class="mb-1">
                                    {{ $notification->title }}

                                    @if (!$notification->read_at)
                                        <span class="badge bg-primary ms-1">Baru</span>
                                    @endif
                                </h6>

                                <p class="text-muted mb-1">
                                    {{ $notification->message }}
                                </p>

                                <small class="text-muted">
                                    Jatuh tempo:
                                    {{ \Carbon\Carbon::parse($notification->due_date)->format('d-m-Y') }}
                                    &bull;
                                    {{ $notification->created_at->format('d-m-Y H:i') }}
                                </small>
                            </div>
                        </div>


                        <div class="d-flex gap-2 align-items-center">

                            @if (!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check2"></i>
                                        Dibaca
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Sudah Dibaca</span>
                            @endif

                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash display-4 text-muted"></i>
                        <h5 class="mt-3">Belum Ada Notifikasi</h5>
                        <p class="text-muted mb-0">
                            Notifikasi pengingat akan muncul ketika sudah jatuh tempo.
                        </p>
                    </div>
                @endforelse

                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('enable-browser-notification');

            if (!button) return;

            function updateButton() {
                if (!('Notification' in window)) {
                    button.disabled = true;
                    button.textContent = 'Browser Tidak Mendukung';
                    return;
                }

                if (Notification.permission === 'granted') {
                    button.innerHTML = '<i class="bi bi-check-circle-fill"></i> Notifikasi Aktif';
                    button.disabled = true;
                } else if (Notification.permission === 'denied') {
                    button.innerHTML = '<i class="bi bi-x-circle"></i> Izin Notifikasi Ditolak';
                    button.disabled = true;
                }
            }

            button.addEventListener('click', async function() {
                if (!('Notification' in window)) {
                    alert('Browser Anda tidak mendukung notifikasi.');
                    return;
                }

                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    new Notification('MyFinance', {
                        body: 'Notifikasi browser berhasil diaktifkan!',
                        icon: '/favicon.ico'
                    });
                } else {
                    alert('Izin notifikasi belum diberikan.');
                }

                updateButton();
            });

            updateButton();
        });
    </script>
@endpush
