<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $books = Buku::whereIn('id', array_keys($cart))->get();
        return view('member.cart.index', compact('cart', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $buku = Buku::findOrFail($request->buku_id);

        if ($buku->tersedia < $request->jumlah) {
            return back()->with('error', 'Stok buku tidak mencukupi.');
        }

        $cart = session()->get('cart', []);
        $totalItems = array_sum($cart);

        if (($totalItems + $request->jumlah) > config('library.max_buku_per_pinjam')) {
            return back()->with('error', 'Mencapai batas maksimal peminjaman.');
        }

        $cart[$request->buku_id] = ($cart[$request->buku_id] ?? 0) + $request->jumlah;
        session()->put('cart', $cart);

        return redirect()->route('member.cart.index')->with('success', 'Buku ditambahkan ke keranjang.');
    }

    public function destroy($bukuId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$bukuId])) {
            unset($cart[$bukuId]);
            session()->put('cart', $cart);
        }
        return redirect()->route('member.cart.index')->with('success', 'Buku dihapus.');
    }
}
