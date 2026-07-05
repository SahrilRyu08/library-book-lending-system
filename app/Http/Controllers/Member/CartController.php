<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
        {
            $cart = session()->get('cart', []);
            $books = [];

            foreach ($cart as $id => $jumlah) {
                $book = Buku::find($id);
                if ($book) {
                    $book->jumlah_pesan = $jumlah;
                    $books[] = $book;
                }
            }

            return view('member.cart.index', [
                'books' => collect($books),
                'cart' => $cart
            ]);
        }

    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (array_key_exists($request->buku_id, $cart)) {
            return back()->with('error', 'Buku ini sudah ada di keranjang.');
        }

        $buku = Buku::findOrFail($request->buku_id);

        $stokTerpakai = DB::table('peminjaman_detail')
            ->join('peminjaman', 'peminjaman_detail.peminjaman_id', '=', 'peminjaman.id')
            ->where('peminjaman_detail.buku_id', $request->buku_id)
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->sum('peminjaman_detail.jumlah');

        $sisaStok = $buku->stok - $stokTerpakai;

        if ($request->jumlah > $sisaStok) {
            return back()->with('error', 'Stok buku tidak mencukupi. Sisa stok tersedia: ' . $sisaStok);
        }

        $cart[$request->buku_id] = $request->jumlah;
        session()->put('cart', $cart);

        return redirect()->route('member.cart.index')->with('success', 'Buku berhasil ditambahkan ke keranjang.');
    }

    public function destroy($bukuId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$bukuId])) {
            unset($cart[$bukuId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('member.cart.index')->with('success', 'Buku berhasil dihapus dari keranjang.');
    }
}
