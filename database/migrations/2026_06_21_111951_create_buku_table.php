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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->string('judul', 75);
            $table->string('penulis', 50);
            $table->string('penerbit', 30);
            $table->year('tahun_terbit');
            $table->string('isbn', 20)->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
