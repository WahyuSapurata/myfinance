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
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

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
