@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Keranjang Peminjaman</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($books->isEmpty())
        <div class="text-center py-5">
            <p>Keranjang Anda kosong.</p>
            <a href="{{ route('member.books.index') }}" class="btn btn-primary">Kembali ke Katalog</a>
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr>
                    <td>{{ $book->judul }}</td>
                    <td>{{ $cart[$book->id] }}</td>
                    <td>
                        <form action="{{ route('member.cart.destroy', $book->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('member.loans.store') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Ajukan Peminjaman</button>
        </form>
    @endif
</div>
@endsection
