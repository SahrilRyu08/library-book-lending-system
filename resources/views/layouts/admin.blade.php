<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — MOCO</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/moco.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

{{-- ===== TOPBAR ADMIN ===== --}}
<nav class="moco-navbar d-flex align-items-center justify-content-between sticky-top">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand text-decoration-none d-flex align-items-center gap-2">
        <span class="moco-mark">M</span>
        <span style="font-weight:700;font-size:18px;color:var(--moco-slate);letter-spacing:-0.02em;">MOCO</span>
        <span class="moco-admin-badge">ADMIN</span>
    </a>

    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-bell fs-5" style="color:var(--moco-text-soft);"></i>
        <div class="dropdown">
            <button class="btn p-0 nav-user border-0 bg-transparent dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5"></i>
                {{ optional(Auth::user())->nama ?? 'Admin Demo' }}
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

{{-- ===== BODY: SIDEBAR + MAIN ===== --}}
<div class="moco-admin-wrap">

    {{-- Sidebar --}}
    <aside class="moco-sidebar">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="{{ route('admin.books.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i> Kelola Buku
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Kategori
        </a>
        <a href="{{ route('admin.loans.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Peminjaman
        </a>
        <a href="{{ route('admin.returns.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-return-left"></i> Pengembalian
        </a>
        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Laporan
        </a>
    </aside>

    {{-- Main Content --}}
    <main class="moco-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
