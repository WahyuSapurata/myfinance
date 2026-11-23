<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - MyFinance</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="d-flex min-vh-100">

        {{-- Sidebar --}}
        <aside class="bg-dark text-white p-3" style="width: 250px; min-width: 250px;">

            <h4 class="fw-bold mb-4">
                <i class="bi bi-wallet2 me-2"></i>
                MyFinance
            </h4>

            <div class="text-uppercase text-secondary small mb-2">
                Menu Utama
            </div>

            <nav class="nav flex-column gap-2">

                <a href="{{ route('dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-grid me-2"></i>
                    Dashboard
                </a>

                <a href="{{ route('transactions.index') }}" class="sidebar-link">
                    <i class="bi bi-arrow-left-right me-2"></i>
                    Transaksi
                </a>

                <a href="{{ route('wallets.index') }}" class="nav-link text-white">
                    <i class="bi bi-wallet me-2"></i>
                    Dompet
                </a>

                <a href="{{ route('income-sources.index') }}" class="nav-link text-white">
                    <i class="bi bi-cash-stack me-2"></i>
                    Gaji & Pemasukan
                </a>

                <a href="{{ route('income-sources.index') }}" class="nav-link text-white">
                    <i class="bi bi-cash-stack me-2"></i>
                    Sumber Pemasukan
                </a>

                <a href="{{ route('salary-profiles.index') }}" class="nav-link text-white">
                    <i class="bi bi-wallet2 me-2"></i>
                    Profil Gaji
                </a>

                <a href="{{ route('savings-goals.index') }}" class="nav-link">
                    <i class="bi bi-piggy-bank me-2"></i>
                    Target Tabungan
                </a>

                <hr class="border-secondary">

                <div class="text-uppercase text-secondary small mb-2">
                    Lainnya
                </div>

                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>
                    Laporan Keuangan
                </a>

                <a href="{{ route('reports.cash-flow') }}"
                    class="nav-link {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack me-2"></i>
                    Laporan Arus Kas
                </a>

                <a href="{{ route('reminders.index') }}"
                    class="nav-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                    <i class="bi bi-bell me-2"></i>
                    Pengingat
                </a>

                <a href="{{ route('notifications.index') }}"
                    class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('notifications.*') ? 'active' : '' }}">

                    <span>
                        <i class="bi bi-bell-fill me-2"></i>
                        Notifikasi
                    </span>

                    @if ($unreadNotifications > 0)
                        <span id="unread-notification-badge" class="badge bg-danger rounded-pill"
                            style="{{ $unreadNotifications > 0 ? '' : 'display: none;' }}">
                            {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('notifications.settings') }}"
                    class="nav-link {{ request()->routeIs('notifications.settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i>
                    Pengaturan Notifikasi
                </a>

            </nav>

            <div class="mt-auto pt-4">
                <hr class="border-secondary">

                <div class="small text-secondary">
                    MyFinance v1.0
                </div>
            </div>

        </aside>

        {{-- Main Content --}}
        <main class="flex-grow-1">

            {{-- Navbar --}}
            <nav class="navbar navbar-expand bg-white border-bottom px-4 py-3">
                <div class="container-fluid">

                    <span class="navbar-brand fw-semibold">
                        @yield('page-title', 'Dashboard')
                    </span>

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">

                            <i class="bi bi-person-circle me-2"></i>
                            {{ Auth::user()->name }}

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>
                                    Profil
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </nav>

            {{-- Page Content --}}
            <div class="p-4">

                @yield('content')

            </div>

        </main>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')

    {{-- Area Toast Notifikasi --}}
    <div id="notification-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    </div>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badge = document.getElementById('unread-notification-badge');
                const toastContainer = document.getElementById('notification-toast-container');

                if (!badge || !toastContainer) {
                    return;
                }

                let previousCount = null;

                function showNotificationToast() {
                    const toast = document.createElement('div');

                    toast.className = 'alert alert-primary shadow border-0';
                    toast.style.minWidth = '280px';
                    toast.style.maxWidth = '350px';

                    toast.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="bi bi-bell-fill me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>Notifikasi Baru</strong>
                    <div class="small">
                        Anda memiliki notifikasi yang belum dibaca.
                    </div>
                    <a href="{{ route('notifications.index') }}"
                        class="small fw-semibold">
                        Lihat Notifikasi
                    </a>
                </div>
                <button type="button" class="btn-close ms-2"
                    aria-label="Tutup"></button>
            </div>
        `;

                    toast.querySelector('.btn-close').addEventListener('click', function() {
                        toast.remove();
                    });

                    toastContainer.appendChild(toast);

                    setTimeout(function() {
                        toast.remove();
                    }, 6000);
                }

                function showBrowserNotification(count) {
                    const browserNotificationEnabled =
                        @json(\App\Models\Setting::getValue('browser_notification_enabled', '1') == '1');

                    if (!browserNotificationEnabled) {
                        return;
                    }

                    if (!('Notification' in window)) {
                        return;
                    }

                    if (Notification.permission !== 'granted') {
                        return;
                    }

                    const notification = new Notification('MyFinance - Notifikasi Baru', {
                        body: count + ' pengingat baru telah jatuh tempo.',
                        icon: '/favicon.ico',
                        tag: 'myfinance-reminder'
                    });

                    notification.onclick = function() {
                        window.focus();
                        window.location.href = "{{ route('notifications.index') }}";
                        notification.close();
                    };
                }

                async function updateUnreadNotifications() {
                    try {
                        const response = await fetch(
                            "{{ route('notifications.unread-count') }}", {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                        if (!response.ok) return;

                        const data = await response.json();
                        const count = Number(data.count) || 0;

                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = count > 0 ? '' : 'none';

                        if (previousCount !== null && count > previousCount) {
                            const newCount = count - previousCount;

                            // Tampilkan pop-up di halaman
                            showNotificationToast(newCount);

                            // Tampilkan notifikasi sistem browser
                            showBrowserNotification(newCount);
                        }

                        previousCount = count;

                    } catch (error) {
                        console.error('Gagal memperbarui notifikasi:', error);
                    }
                }

                updateUnreadNotifications();

                setInterval(updateUnreadNotifications, 10000);
            });
        </script>
    @endauth

</body>

</html>
