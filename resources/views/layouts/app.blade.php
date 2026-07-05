<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <title>
        @yield('title', 'MOCO')
        — Perpustakaan Digital
    </title>
    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">
    {{-- Google Font --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    {{-- CSS --}}
    <link
        href="{{ asset('css/moco.css') }}"
        rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $notifications = $notifications ?? collect();
    $unreadNotifCount = $unreadNotifCount ?? 0;
@endphp
<nav class="moco-navbar d-flex justify-content-between align-items-center sticky-top">
    <a
        href="{{ route('member.books.index') }}"
        class="navbar-brand text-decoration-none">
        <span class="moco-mark">
            M
        </span>
        MOCO
    </a>
    {{-- MENU --}}
    <div class="d-flex align-items-center">
        <a
            href="{{ route('member.books.index') }}"
            class="nav-link {{ request()->routeIs('member.books.*') ? 'active' : '' }}">
            Katalog
        </a>
        <a
            href="{{ route('member.loans.index') }}"
            class="nav-link {{ request()->routeIs('member.loans.index') ? 'active' : '' }}">
            Peminjaman Saya
        </a>
        <a
            href="{{ route('member.loans.history') }}"
            class="nav-link {{ request()->routeIs('member.loans.history') ? 'active' : '' }}">
            Riwayat
        </a>
    </div>
    {{-- RIGHT MENU --}}
    <div class="d-flex align-items-center gap-3">
        {{-- NOTIFICATION --}}
        <div class="position-relative">
            <button
                class="btn border-0 bg-transparent p-0 moco-bell"
                id="notifToggle"
                type="button">
                <i class="bi bi-bell fs-5"></i>
                @if($unreadNotifCount > 0)
                    <span class="moco-bell-dot"></span>
                @endif
            </button>
            <div
                class="moco-notif-panel"
                id="notifPanel">
                <div class="notif-head d-flex justify-content-between align-items-center">
                    <span>
                        Notifikasi
                    </span>
                    @if($unreadNotifCount)
                        <span class="badge-moco-late">
                            {{ $unreadNotifCount }}
                            Baru
                        </span>
                    @endif
                </div>
                @forelse($notifications as $notif)
{{--                    <a--}}
{{--                        href="{{ $notif->data['action_url'] ?? '#' }}"--}}
{{--                        class="text-decoration-none text-dark">--}}
                        <div
                            class="moco-notif-item {{ $notif->read_at ? '' : 'unread' }}">
                            <div class="notif-ic">
                                @switch($notif->data['type'] ?? '')
                                    @case('due')
                                        <i class="bi bi-clock-history text-warning"></i>
                                        @break
                                    @case('late')
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        @break
                                    @case('returned')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        @break
                                    @default
                                        <i class="bi bi-bell-fill"></i>
                                @endswitch
                            </div>
                            <div>
                                <div class="fw-semibold">
                                    {{ $notif->data['title'] ?? 'Notifikasi' }}
                                </div>
                                <div class="notif-txt">
                                    {{ $notif->data['message'] ?? '-' }}
                                </div>
                                <div class="notif-time">
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
{{--                    </a>--}}
                @empty
                    <div class="p-4 text-center text-muted">
                        Belum ada notifikasi.
                    </div>
                @endforelse
                <div class="border-top p-2 text-center">
                    <a
                        href="{{ route('member.notifications.index') }}"
                        class="text-decoration-none">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
        {{-- Keranjang --}}
        <a href="{{ route('member.cart.index') }}"
        class="text-decoration-none"
        style="color:var(--moco-text-soft);">
            <i class="bi bi-basket fs-5"></i>
            @php $cartCount = array_sum(session('cart', [])); @endphp
            @if($cartCount > 0)
                <span class="moco-bell-dot"
                    style="background:var(--moco-blue);width:16px;height:16px;
                            font-size:9px;display:inline-flex;align-items:center;
                            justify-content:center;color:#fff;border-radius:50%;
                            position:relative;top:-8px;left:-6px;">
                    {{ $cartCount }}
                </span>
            @endif
        </a>

        {{-- USER --}}
        <div class="dropdown">
            <button
                class="btn border-0 bg-transparent dropdown-toggle"
                data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                {{ auth()->user()->nama ?? 'Guest' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form
                        method="POST"
                        action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="dropdown-item text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggle = document.getElementById('notifToggle');
    const panel = document.getElementById('notifPanel');
    if(toggle && panel){
        toggle.addEventListener('click',function(e){
            e.stopPropagation();
            panel.classList.toggle('show');
        });
        panel.addEventListener('click',function(e){
            e.stopPropagation();
        });
        document.addEventListener('click',function(){
            panel.classList.remove('show');
        });
    }
</script>
@stack('scripts')
</body>
</html>
