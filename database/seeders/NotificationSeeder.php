<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\User;
use App\Notifications\JatuhTempoNotification;
use App\Notifications\KeterlambatanNotification;
use App\Notifications\PeminjamanBerhasilNotification;
use App\Notifications\PengembalianNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Jalankan UserSeeder dulu sebelum NotificationSeeder.');
            return;
        }

        foreach ($users as $user) {
            if ($user->role === 'anggota') {
                $this->seedNotifikasiAnggota($user);
            } else {
                $this->seedNotifikasiAdmin($user);
            }
        }

        $this->command->info('NotificationSeeder selesai: notifikasi dummy dibuat untuk semua user.');
    }

    /**
     * Anggota: dapat notifikasi terkait peminjamannya sendiri kalau ada,
     * atau data generik kalau belum punya peminjaman.
     */
    protected function seedNotifikasiAnggota(User $user): void
    {
        $peminjaman = Peminjaman::where('user_id', $user->id)->first();

        // 1. Notifikasi lama, SUDAH dibaca (dibuat 5 hari lalu, dibaca 4 hari lalu)
        $this->insert(
            user: $user,
            type: PeminjamanBerhasilNotification::class,
            data: [
                'type'          => 'loan_request',
                'title'         => 'Peminjaman Berhasil',
                'message'       => 'Peminjaman buku berhasil diproses.',
                'action_url'    => $peminjaman ? route('member.loans.show', $peminjaman->id) : route('member.loans.index'),
                'peminjaman_id' => $peminjaman?->id,
            ],
            createdAt: now()->subDays(5),
            readAt: now()->subDays(4)
        );

        // 2. Reminder H-3, SUDAH dibaca
        $this->insert(
            user: $user,
            type: JatuhTempoNotification::class,
            data: [
                'type'          => 'due',
                'title'         => 'Segera Jatuh Tempo',
                'message'       => 'Buku yang kamu pinjam jatuh tempo dalam 3 hari.',
                'action_url'    => $peminjaman ? route('member.loans.show', $peminjaman->id) : route('member.loans.index'),
                'peminjaman_id' => $peminjaman?->id,
            ],
            createdAt: now()->subDays(3),
            readAt: now()->subDays(2)
        );

        // 3. Notifikasi terlambat, BELUM dibaca (paling relevan buat badge "Baru")
        $this->insert(
            user: $user,
            type: KeterlambatanNotification::class,
            data: [
                'type'          => 'late',
                'title'         => 'Peminjaman Terlambat',
                'message'       => 'Buku yang kamu pinjam sudah melewati batas pengembalian.',
                'action_url'    => $peminjaman ? route('member.loans.show', $peminjaman->id) : route('member.loans.index'),
                'peminjaman_id' => $peminjaman?->id,
            ],
            createdAt: now()->subHours(6),
            readAt: null
        );

        // 4. Notifikasi pengembalian, BELUM dibaca (paling baru)
        $this->insert(
            user: $user,
            type: PengembalianNotification::class,
            data: [
                'type'          => 'returned',
                'title'         => 'Pengembalian Berhasil',
                'message'       => 'Buku berhasil dikembalikan.',
                'action_url'    => route('member.loans.history'),
                'peminjaman_id' => $peminjaman?->id,
            ],
            createdAt: now()->subMinutes(30),
            readAt: null
        );
    }

    /**
     * Admin: notifikasi generik seputar operasional (stok, peminjaman masuk, dst).
     * Silakan sesuaikan tipe 'stock' / 'loan_request' dengan Notification class
     * admin kamu sendiri kalau sudah dibuat.
     */
    protected function seedNotifikasiAdmin(User $user): void
    {
        $this->insert(
            user: $user,
            type: 'App\\Notifications\\StokMenipisNotification', // ganti sesuai class asli kalau sudah ada
            data: [
                'type'    => 'stock',
                'title'   => 'Stok Buku Menipis',
                'message' => 'Beberapa judul buku stoknya di bawah 3.',
            ],
            createdAt: now()->subDays(2),
            readAt: now()->subDays(1)
        );

        $this->insert(
            user: $user,
            type: 'App\\Notifications\\PeminjamanBaruNotification', // ganti sesuai class asli kalau sudah ada
            data: [
                'type'    => 'loan_request',
                'title'   => 'Ada Peminjaman Baru',
                'message' => 'Seorang anggota baru saja meminjam buku.',
            ],
            createdAt: now()->subHours(3),
            readAt: null
        );

        $this->insert(
            user: $user,
            type: 'App\\Notifications\\PengembalianTerlambatNotification', // ganti sesuai class asli kalau sudah ada
            data: [
                'type'    => 'late',
                'title'   => 'Ada Peminjaman Terlambat',
                'message' => 'Ada anggota yang belum mengembalikan buku tepat waktu.',
            ],
            createdAt: now()->subMinutes(45),
            readAt: null
        );
    }

    /**
     * Insert satu baris notifikasi langsung ke tabel `notifications`,
     * TANPA lewat notify() supaya tidak ikut mengirim email saat seeding.
     */
    protected function insert(User $user, string $type, array $data, $createdAt, $readAt = null): void
    {
        DB::table('notifications')->insert([
            'id'              => (string) Str::uuid(),
            'type'            => $type,
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => json_encode($data),
            'read_at'         => $readAt,
            'created_at'      => $createdAt,
            'updated_at'      => $createdAt,
        ]);
    }
}
