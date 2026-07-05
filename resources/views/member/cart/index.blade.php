@extends('layouts.app')
@section('title', 'Keranjang Pinjam')

@section('content')
<div class="moco-page-title">Keranjang Pinjam</div>
<div class="moco-page-sub">Periksa daftar buku sebelum mengajukan peminjaman</div>

{{-- Info kuota --}}
<div class="moco-alert moco-alert-info mb-4">
    <i class="bi bi-info-circle"></i>
    <div>
        Kuota aktif: <strong>{{ $kuotaAktif }} dari {{ $maxPinjam }} buku</strong> sedang dipinjam
        &middot; sisa keranjang: <strong>{{ max($maxPinjam - $kuotaAktif - array_sum($cart), 0) }}</strong> slot
    </div>
</div>

@if(count($cart) > 0)
    <div class="moco-card p-0 mb-4" style="overflow:hidden;">
        <table class="table moco-table mb-0">
            <thead>
                <tr>
                    <th>Buku</th>
                    <th>Kategori</th>
                    <th>Stok Tersedia</th>
                    <th>Jumlah Pinjam</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $bukuId => $jumlah)
                    @php $buku = $books[$bukuId] ?? null; @endphp
                    @if($buku)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $buku->judul }}</div>
                                <div class="moco-note">{{ $buku->penulis }}</div>
                            </td>
                            <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                @if($buku->tersedia > 0)
                                    <span class="badge-moco-stock">{{ $buku->tersedia }} tersedia</span>
                                @else
                                    <span class="badge-moco-out">Stok Habis</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('member.cart.update', $bukuId) }}"
                                      class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number"
                                           name="jumlah"
                                           value="{{ $jumlah }}"
                                           min="1"
                                           max="{{ max($buku->tersedia, 1) }}"
                                           class="form-control moco-input"
                                           style="width:88px;">
                                    <button type="submit" class="btn btn-sm btn-moco-outline">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td>
                                {{-- Hapus dari keranjang --}}
                                <form method="POST"
                                      action="{{ route('member.cart.destroy', $bukuId) }}"
                                      id="deleteCartForm-{{ $bukuId }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-moco-outline"
                                            style="color:var(--moco-text-faint);"
                                            onclick="showConfirmModal('Hapus dari Keranjang', 'Hapus buku ini dari keranjang?', function() {
                                                document.getElementById('deleteCartForm-{{ $bukuId }}').submit();
                                            })">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tombol ajukan peminjaman --}}
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('member.books.index') }}"
           class="btn btn-moco-outline">
            <i class="bi bi-arrow-left"></i> Tambah Buku Lagi
        </a>
        <form method="POST" action="{{ route('member.loans.store') }}">
            @csrf
            <input type="hidden" name="dari_keranjang" value="1">
            <button type="submit" class="btn btn-moco">
                <i class="bi bi-send"></i> Ajukan Peminjaman
            </button>
        </form>
    </div>

@else
    {{-- Keranjang kosong --}}
    <div class="moco-card text-center py-5">
        <div style="font-size:40px;color:var(--moco-text-faint);">
            <i class="bi bi-basket"></i>
        </div>
        <div class="moco-page-title mt-3">Keranjang Kosong</div>
        <div class="moco-page-sub">Belum ada buku yang ditambahkan ke keranjang.</div>
        <a href="{{ route('member.books.index') }}" class="btn btn-moco mt-2">
            Jelajahi Katalog
        </a>
    </div>
@endif

@endsection
