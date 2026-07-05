<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('title', 'Admin') — MOCO
    </title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">
    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    {{-- CSS --}}
    <link href="{{ asset('css/moco.css') }}"
          rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $notifications = $notifications ?? collect();
    $unreadNotifCount = $unreadNotifCount ?? 0;
@endphp
{{-- ===================== --}}
{{-- TOPBAR ADMIN --}}
{{-- ===================== --}}
<nav class="moco-navbar d-flex justify-content-between align-items-center sticky-top">
    {{-- Logo --}}
    <a href="{{ route('admin.dashboard') }}"
       class="navbar-brand text-decoration-none d-flex align-items-center gap-2">
        <span class="moco-mark">M</span>
        <span
            style="
                font-weight:700;
                font-size:18px;
                color:var(--moco-slate);
                letter-spacing:-.02em;">MOCO</span>
        <span class="moco-admin-badge">ADMIN</span>
    </a>
    {{-- RIGHT MENU --}}
    <div class="d-flex align-items-center gap-3">
        {{-- ===================== --}}
        {{-- NOTIFICATION --}}
        {{-- ===================== --}}
        <div class="position-relative">
            <button
                class="btn border-0 bg-transparent p-0 moco-bell"
                id="adminNotifToggle"
                type="button">
                <i class="bi bi-bell fs-5"></i>
                @if($unreadNotifCount > 0)
                    <span class="moco-bell-dot"></span>
                @endif
            </button>
            {{-- PANEL --}}
            <div
                class="moco-notif-panel"
                id="adminNotifPanel">
                <div class="notif-head d-flex justify-content-between align-items-center">
                    <span>Notifikasi</span>
                    @if($unreadNotifCount > 0)
                        <span class="badge-moco-late">{{ $unreadNotifCount }}Baru</span>
                    @endif
                </div>
                @forelse($notifications as $notification)
{{--                    <a href="{{ $notification->data['action_url'] ?? '#' }}"--}}
{{--                        class="text-decoration-none text-dark">--}}
                        <div class="moco-notif-item {{ $notification->read_at ? '' : 'unread' }}">
                            <div class="notif-ic">
                                @switch($notification->data['type'] ?? '')
                                    @case('loan_request')
                                        <i class="bi bi-book text-primary"></i>
                                        @break
                                    @case('due')
                                        <i class="bi bi-clock-history text-warning"></i>
                                        @break
                                    @case('late')
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        @break
                                    @case('returned')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        @break
                                    @case('stock')
                                        <i class="bi bi-box-seam text-warning"></i>
                                        @break
                                    @default
                                        <i class="bi bi-bell-fill"></i>
                                @endswitch
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                                </div>
                                <div class="notif-txt">
                                    {{ $notification->data['message'] ?? '-' }}
                                </div>
                                <div class="notif-time">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
{{--                    </a>--}}
                @empty
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-bell-slash fs-3"></i>
                        <div class="mt-2">Tidak ada notifikasi</div>
                    </div>
                @endforelse
                <div class="border-top text-center p-2">
                    <a href="{{ route('admin.notifications.index') }}"
                        class="text-decoration-none fw-semibold">Lihat Semua</a>
                </div>
            </div>
        </div>
        {{-- ===================== --}}
        {{-- USER --}}
        {{-- ===================== --}}
        <div class="dropdown">
            <button class="btn p-0 nav-user border-0 bg-transparent dropdown-toggle"
                data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5"></i>
                {{ Auth::user()?->nama ?? 'Admin Demo' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST"
                        action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="dropdown-item text-danger">
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ========================================= --}}
{{-- ADMIN BODY --}}
{{-- ========================================= --}}
<div class="moco-admin-wrap">
    {{-- ===================== --}}
    {{-- SIDEBAR --}}
    {{-- ===================== --}}
    <aside class="moco-sidebar">
        <a
            href="{{ route('admin.dashboard') }}"
            class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            Dashboard
        </a>
        <a
            href="{{ route('admin.books.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i>
            Kelola Buku
        </a>
        <a
            href="{{ route('admin.categories.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>
            Kategori
        </a>
        <a
            href="{{ route('admin.loans.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">

            <i class="bi bi-arrow-left-right"></i>

            Peminjaman

        </a>

        <a
            href="{{ route('admin.returns.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">

            <i class="bi bi-arrow-return-left"></i>

            Pengembalian

        </a>

        <a
            href="{{ route('admin.reports.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">

            <i class="bi bi-bar-chart-line"></i>

            Laporan

        </a>

    </aside>


    {{-- ===================== --}}
    {{-- MAIN CONTENT --}}
    {{-- ===================== --}}

    <main class="moco-main">


        {{-- SUCCESS --}}

        @if(session('success'))

            <div
                class="alert alert-success alert-dismissible fade show mb-4">

                <i class="bi bi-check-circle-fill me-2"></i>

                {{ session('success') }}

                <button
                    class="btn-close"
                    data-bs-dismiss="alert">

                </button>

            </div>

        @endif


        {{-- ERROR --}}

        @if(session('error'))

            <div
                class="alert alert-warning alert-dismissible fade show mb-4">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                {{ session('error') }}

                <button
                    class="btn-close"
                    data-bs-dismiss="alert">

                </button>

            </div>

        @endif


        {{-- VALIDATION ERROR --}}

        @if($errors->any())

            <div class="alert alert-danger">

                <div class="fw-semibold mb-2">

                    Terjadi kesalahan.

                </div>

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>

                            {{ $error }}

                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- PAGE CONTENT --}}

        @yield('content')


    </main>

</div>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Notification Dropdown
        |--------------------------------------------------------------------------
        */

        const toggle = document.getElementById('adminNotifToggle');

        const panel = document.getElementById('adminNotifPanel');

        if (toggle && panel) {

            toggle.addEventListener('click', function (e) {

                e.stopPropagation();

                panel.classList.toggle('show');
            });
            panel.addEventListener('click', function (e) {
                e.stopPropagation();
            });
            document.addEventListener('click', function () {
                panel.classList.remove('show');
            });
        }
        /*
        |--------------------------------------------------------------------------
        | Mark Notification Read
        |--------------------------------------------------------------------------
        */
        document.querySelectorAll('.notification-link')
            .forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.dataset.url;
                    const action = this.dataset.read;
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        }
                    })
                        .finally(() => {
                            window.location.href = url;
                        });
                });

            });

    });

</script>

{{-- Reusable Confirm Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--moco-line);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-600" id="confirmModalTitle">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmModalBody" class="moco-note"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-moco-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-moco" id="confirmModalConfirmBtn">Ya</button>
            </div>
        </div>
    </div>
</div>

<script>
    let confirmCallback = null;
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    function showConfirmModal(title, body, callback) {
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalBody').textContent = body;
        confirmCallback = callback;
        confirmModal.show();
    }
    document.getElementById('confirmModalConfirmBtn').addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
        confirmModal.hide();
    });
</script>

@stack('scripts')

</body>

</html>
