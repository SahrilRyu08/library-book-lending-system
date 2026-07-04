@extends('layouts.admin')
@section('title', 'Peminjaman')

@section('content')
<div class="moco-page-title">Kelola Peminjaman</div>
<div class="moco-page-sub">Persetujuan dan pengelolaan peminjaman buku</div>

{{-- Tab Navigation --}}
<ul class="nav nav-underline mb-4" style="border-bottom: 1px solid var(--moco-line);">
    <li class="nav-item">
        <a class="nav-link {{ request('status') !== 'active' && request('status') !== 'terlambat' && request('status') !== 'mendekati' && request('status') !== 'aman' ? 'active' : '' }}" 
           href="{{ route('admin.loans.index', ['status' => 'pending']) }}">
            <i class="bi bi-hourglass-split"></i> Menunggu Approval
            <span class="badge bg-warning ms-2" id="pendingCount">0</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ !request('status') || request('status') === 'aman' || request('status') === 'mendekati' || request('status') === 'terlambat' ? 'active' : '' }}"
           href="{{ route('admin.loans.index') }}">
            <i class="bi bi-book"></i> Peminjaman Aktif
        </a>
    </li>
</ul>

<form method="GET" action="{{ route('admin.loans.index') }}" class="d-flex gap-2 mb-3">
    <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-white" style="border-color:var(--moco-line);">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control moco-input" placeholder="Cari anggota / buku...">
    </div>
    @if(request('status') !== 'pending')
    <select name="status" class="form-select moco-input" style="max-width:180px;">
        <option value="">Semua</option>
        <option value="aman"     {{ request('status') == 'aman'     ? 'selected' : '' }}>Aman</option>
        <option value="mendekati"{{ request('status') == 'mendekati' ? 'selected' : '' }}>H-3</option>
        <option value="terlambat"{{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
    </select>
    @endif
    <button class="btn btn-moco-outline" type="submit">Filter</button>
</form>

<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table align-middle mb-0">
        <thead>
            <tr>
                <th>Anggota</th>
                <th>Buku</th>
                @if(request('status') !== 'pending')
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Sisa Hari</th>
                @else
                <th>Tgl Permintaan</th>
                @endif
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                @if($loan->status === 'pending')
                    {{-- Pending Loan Row --}}
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $loan->user->name ?? '-' }}</div>
                            <div class="moco-note">{{ $loan->user->email ?? '' }}</div>
                        </td>
                        <td>{{ $loan->detail->first()->buku->judul ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge bg-warning">⏳ Menunggu Approval</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" style="flex:1;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100" title="Setujui">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" style="flex:1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100" 
                                            onclick="return confirm('Tolak permintaan ini?')" title="Tolak">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @else
                    {{-- Active Loan Row --}}
                    @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->jatuh_tempo, false);
                        $isLate   = $daysLeft < 0;
                        $isNear   = $daysLeft >= 0 && $daysLeft <= 3;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $loan->user->name ?? '-' }}</div>
                            <div class="moco-note">{{ $loan->user->email ?? '' }}</div>
                        </td>
                        <td>{{ $loan->detail->first()->buku->judul ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->jatuh_tempo)->format('d M Y') }}</td>
                        <td>
                            @if($isLate)
                                <span style="color:var(--moco-warn);font-weight:700;">
                                    {{ abs((int)$daysLeft) }} hari telat
                                </span>
                            @else
                                <span style="color:var(--moco-blue);font-weight:600;">
                                    {{ (int)$daysLeft }} hari lagi
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($isLate)
                                <span class="badge-moco-late">Terlambat</span>
                            @elseif($isNear)
                                <span class="badge-moco-late">
                                    <i class="bi bi-bell"></i> H-{{ (int)$daysLeft }}
                                </span>
                            @else
                                <span class="badge-moco-active">Dipinjam</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.loans.show', $loan->id) }}"
                               class="btn btn-sm btn-moco-outline">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center moco-note py-4">
                        @if(request('status') === 'pending')
                            Tidak ada permintaan peminjaman yang menunggu.
                        @else
                            Tidak ada peminjaman aktif.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $loans->links() }}</div>
@endsection
