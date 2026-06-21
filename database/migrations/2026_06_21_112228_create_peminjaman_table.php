<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained('users');
            $table->date('tanggal_pinjam');
            $table->date('jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->unsignedInteger('denda')->default(0);
            $table->enum('status',['dipinjam','selesai','terlambat'])->default('dipinjam');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['jatuh_tempo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
