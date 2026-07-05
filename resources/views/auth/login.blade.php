<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MOCO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/moco.css') }}" rel="stylesheet">
</head>
<body>
<div class="moco-auth-wrapper">
    <div class="moco-auth-card">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="moco-mark" style="width:36px;height:36px;border-radius:10px;font-size:16px;">M</span>
                <span style="font-weight:700;font-size:22px;color:var(--moco-slate);letter-spacing:-0.02em;">MOCO</span>
            </div>
            <p class="moco-note mb-0">Masuk untuk mulai meminjam buku</p>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="moco-alert moco-alert-warn mb-3">
                <i class="bi bi-exclamation-triangle"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="moco-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control moco-input" placeholder="nama@email.com" required autofocus>
            </div>

            <div class="mb-4">
                <label class="moco-label">Password</label>
                <input type="password" name="password"
                       class="form-control moco-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-moco w-100 py-2">Masuk</button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:13px;">
            Belum punya akun?
            <a href="{{ route('register') }}" style="color:var(--moco-blue);font-weight:600;">Daftar di sini</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
