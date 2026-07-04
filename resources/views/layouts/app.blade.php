<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MOCO') — Perpustakaan Digital</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- MOCO Custom CSS --}}
    <link href="{{ asset('css/moco.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

{{-- ===== NAVBAR MEMBER ===== --}}
<nav class="moco-navbar d-flex align-items-center justify-content-between sticky-top">

    {{-- Brand --}}
    <a href="{{ route('member.books.index') }}" class="navbar-brand text-decoration-none">
        <span class="moco-mark">M</span>
        MOCO
    </a>

        {{-- Nav Links --}}
            <div class="d-flex align-items-center">
                <a href="{{ route('member.books.index') }}"
                class="nav-link {{ request()->routeIs('member.books.*') ? 'active' : '' }}">
                    Katalog
                </a>
                <a href="{{ route('member.loans.index') }}"
                class="nav-link {{ request()->routeIs('member.loans.index') ? 'active' : '' }}">
                    Peminjaman Saya
                </a>
                <a href="{{ route('member.loans.history') }}"
                class="nav-link {{ request()->routeIs('member.loans.history') ? 'active' : '' }}">
                    Riwayat
                </a>

                {{-- TAMBAHAN MENU KERANJANG --}}
                <a href="{{ route('member.cart.index') }}"
                   class="nav-link {{ request()->routeIs('member.cart.*') ? 'active' : '' }} position-relative">
                    Keranjang
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>
        {{-- Bell --}}
        <div class="moco-bell position-relative" id="notifToggle">
            <i class="bi bi-bell fs-5"></i>
            @if(isset($unreadNotifCount) && $unreadNotifCount > 0)
                <span class="moco-bell-dot"></span>
            @endif

            {{-- Notif Dropdown --}}
            <div class="moco-notif-panel" id="notifPanel">
                <div class="notif-head d-flex justify-content-between align-items-center">
                    Notifikasi
                    @if(isset($unreadNotifCount) && $unreadNotifCount > 0)
                        <span class="badge-moco-late">{{ $unreadNotifCount }} Baru</span>
                    @endif
                </div>
                @forelse($notifications ?? [] as $notif)
                    <div class="moco-notif-item {{ $notif->read_at ? '' : 'unread' }}">
                        <div class="notif-ic {{ $notif->read_at ? 'read' : '' }}">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div>
                            <div class="notif-txt">{{ $notif->data['message'] ?? '-' }}</div>
                            <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center moco-note">Tidak ada notifikasi.</div>
                @endforelse
            </div>
        </div>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <button class="btn p-0 nav-user border-0 bg-transparent dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5"></i>
                {{ Auth::user()?->nama ?? 'Guest' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ===== MAIN CONTENT ===== --}}
<main class="py-4 px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Notif toggle --}}
<script>
    const toggle = document.getElementById('notifToggle');
    const panel  = document.getElementById('notifPanel');
    if (toggle && panel) {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('show');
        });
        document.addEventListener('click', () => panel.classList.remove('show'));
    }
</script>

@stack('scripts')
</body>
</html>
