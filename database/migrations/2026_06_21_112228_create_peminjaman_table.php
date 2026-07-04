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
            $table->foreignId('user_id')->constrained('users');

            $table->date('tanggal_pinjam')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->date('tanggal_kembali')->nullable();

            // Loan approval workflow
            $table->enum('status', ['pending', 'dipinjam', 'selesai'])->default('pending');
            $table->unsignedInteger('denda')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

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

