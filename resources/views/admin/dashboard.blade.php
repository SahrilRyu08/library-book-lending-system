@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="moco-page-title">Dashboard</div>
<div class="moco-page-sub">Ringkasan aktivitas perpustakaan hari ini</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="moco-stat-card">
            <div class="stat-num">{{ $bookCount }}</div>
            <div class="stat-lbl">Total Buku</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="moco-stat-card">
            <div class="stat-num">{{ $activeLoan }}</div>
            <div class="stat-lbl">Sedang Dipinjam</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="moco-stat-card">
            <div class="stat-num" style="color:var(--moco-warn);">{{ $lateLoan }}</div>
            <div class="stat-lbl">Terlambat</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="moco-stat-card">
            <div class="stat-num">{{ $memberCount }}</div>
            <div class="stat-lbl">Anggota</div>
        </div>
    </div>
</div>

{{-- Buku Terpopuler --}}
<div class="moco-eyebrow mb-2">Buku Paling Sering Dipinjam</div>
<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul Buku</th>
                <th>Kategori</th>
                <th>Jumlah Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($popularBooks as $i => $book)
                <tr>
                    <td style="color:var(--moco-blue);font-weight:600;">{{ $i + 1 }}</td>
                    <td><strong>{{ $book->judul }}</strong></td>
                    <td>{{ $book->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $book->total_dipinjam }}x</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
