<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * Admin DashboardController - DUMMY untuk preview UI
 */
class DashboardController extends Controller
{
    public function index()
    {
        $popularBooks = collect([
            ['judul' => 'Laskar Pelangi',  'kategori' => 'Fiksi',             'total' => 42],
            ['judul' => 'Atomic Habits',   'kategori' => 'Pengembangan Diri', 'total' => 35],
            ['judul' => 'Bumi Manusia',    'kategori' => 'Fiksi',             'total' => 29],
            ['judul' => 'Sapiens',         'kategori' => 'Non-Fiksi',         'total' => 21],
            ['judul' => 'Deep Work',       'kategori' => 'Pengembangan Diri', 'total' => 18],
        ])->map(function ($d) {
            $b = new \stdClass();
            $b->judul          = $d['judul'];
            $b->total_dipinjam = $d['total'];
            $kat = new \stdClass();
            $kat->nama_kategori = $d['kategori'];
            $b->kategori = $kat;
            return $b;
        });

        return view('admin.dashboard', [
            'totalBuku'      => 128,
            'sedangDipinjam' => 24,
            'terlambat'      => 5,
            'totalAnggota'   => 60,
            'popularBooks'   => $popularBooks,
        ]);
    }
}
