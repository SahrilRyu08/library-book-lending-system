<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel peminjaman — header transaksi peminjaman buku
     * Satu peminjaman bisa punya banyak buku (lihat tabel peminjaman_detail)
     */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_pinjam');
            $table->date('jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->decimal('denda', 10, 2)->default(0);

            // menunggu = belum dikonfirmasi admin
            // dipinjam = sudah dikonfirmasi, buku di tangan anggota
            // selesai  = sudah dikembalikan tepat waktu
            // terlambat = sudah dikembalikan tapi lewat jatuh tempo
            $table->enum('status', ['menunggu', 'dipinjam', 'selesai', 'terlambat'])->default('menunggu');

            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['jatuh_tempo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
