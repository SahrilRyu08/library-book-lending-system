@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Keranjang Peminjaman</h3>

    @if($books->isEmpty())
        <div class="text-center py-5">
            <p>Keranjang Anda kosong.</p>
            <a href="{{ route('member.books.index') }}" class="btn btn-primary">Kembali ke Katalog</a>
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr>
                    <td><img src="{{ asset($book->cover) }}" width="50" alt="Cover"></td>
                    <td>{{ $book->judul }}</td>
                    <td>{{ $cart[$book->id] }}</td>
                    <td>
                        <form action="{{ route('member.cart.destroy', $book->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-end">
            <a href="{{ route('member.loans.index') }}" class="btn btn-success">Ajukan Peminjaman</a>
        </div>
    @endif
</div>
@endsection
