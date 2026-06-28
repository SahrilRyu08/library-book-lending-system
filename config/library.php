<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Aturan Perpustakaan MOCO
    |--------------------------------------------------------------------------
    | Konstanta ini dipakai di seluruh aplikasi untuk mengatur aturan bisnis.
    | Ubah nilai di sini jika aturan perpustakaan berubah.
    */

    // Denda per hari keterlambatan pengembalian (dalam Rupiah)
    // Dipakai oleh Dev 4 (LoanService::hitungDenda) dan Dev 5 (pengembalian)
    'tarif_denda_per_hari' => 1000,

    // Maksimal durasi peminjaman dalam hari sejak tanggal pinjam
    // Dipakai oleh Dev 4 saat menghitung jatuh_tempo
    'max_hari_pinjam'      => 7,

    // Maksimal jumlah buku yang boleh dipinjam per anggota sekaligus
    // Dipakai oleh Dev 3 (tampil di UI) dan Dev 4 (validasi store)
    'max_buku_per_pinjam'  => 3,

    // Notifikasi H-berapa sebelum jatuh tempo dikirim ke anggota
    // Dipakai oleh Dev 5 (command loans:check-due)
    'notif_h_minus'        => 3,
];
