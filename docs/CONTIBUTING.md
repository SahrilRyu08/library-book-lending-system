# Contributing — MOCO

Panduan kerja tim untuk pengembangan **MOCO** (Laravel 12 · Bootstrap 5 · MySQL). Dokumen ini wajib dibaca semua anggota sebelum mulai ngoding. Tujuannya satu: **lima orang bisa kerja paralel tanpa saling tabrakan.**

---

## 1. Sebelum mulai

### Prasyarat
- PHP 8.2+, Composer, MySQL 8, Node.js (untuk aset Bootstrap), Git.

### Setup lokal (sekali di awal)
```bash
git clone <repo-url> pustaka-kita
cd pustaka-kita
git checkout develop          # SELALU kerja dari develop, bukan main

composer install
cp .env.example .env
php artisan key:generate
```

Atur `.env`:
```env
DB_DATABASE=pustaka_kita
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=sync          # dev: notifikasi jalan inline
MAIL_MAILER=log                # dev: email masuk ke storage/logs/laravel.log
PERPUS_NOTIF_EMAIL=true
```

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

> ⚠️ Pastikan `bootstrap/app.php` sudah punya alias middleware `admin` (bagian dari commit fondasi). Kalau halaman admin error 403/route, cek alias ini dulu.

Akun hasil seeder (password semua `password`): `admin@perpus.app` (admin), `john@email.com` (anggota).

---

## 2. Model branching

```
main      ← rilis stabil saja. Tidak ada yang push langsung.
develop   ← integrasi. Semua fitur di-merge ke sini lewat PR.
feat/*    ← branch kerja tiap developer (fork dari develop).
```

- `main` dan `develop` **diproteksi**: tidak ada direct push, wajib lewat Pull Request + minimal 1 review.
- Fondasi (kontrak: schema, model, enum, interface, event) + auth/authz sudah ada di `develop`. Jangan mulai sebelum ini ter-merge.

---

## 3. Tim & kepemilikan file

Aturan inti conflict-free: **satu file hanya dimiliki satu branch.** Jangan menyentuh file di baris orang lain.

| Developer | Branch | File yang dimiliki | Stub yang diganti |
|---|---|---|---|
| Dev 1 | `feat/katalog` | `KatalogController`, `routes/katalog.php`, view `member/katalog`, `member/buku-detail` | `katalog.index` |
| **Ryu (lead)** | `feat/peminjaman` | `PeminjamanController`, isi `PeminjamanService::pinjam`, `routes/peminjaman.php`, view `member/peminjaman`, `PeminjamanSeeder` | — |
| Dev 3 | `feat/admin-buku` | `BukuController`, `KategoriController`, form requests, `routes/admin-buku.php`, view `admin/buku`, `admin/kategori`, `admin/dashboard`, `Buku/KategoriSeeder` | `admin.dashboard` |
| Dev 4 | `feat/admin-pinjam` | isi `PengembalianService::kembalikan`, `PeminjamanAdminController`, `LaporanController`, `routes/admin-pinjam.php`, view `admin/peminjaman`, `admin/laporan` | — |
| Dev 5 | `feat/notifikasi` | `Notifications/*`, `Listeners/KirimNotifPengembalian`, command reminder, `routes/console.php` | — |

**File milik bersama (FROZEN — jangan diedit tanpa kesepakatan tim):**
`config/perpus.php`, semua Enum, semua migration yang sudah merge, model di `app/Models`, interface di `app/Contracts`, event di `app/Events`, `routes/web.php`, `bootstrap/app.php`, `DatabaseSeeder.php`, layout di `resources/views/layouts`.

### Rincian tugas tiap developer

Pembagiannya **vertical slice**: tiap orang memegang satu fitur utuh dari controller sampai view, dengan file yang tidak beririsan. Berikut detail tugas dan tanggung jawab masing-masing.

#### Dev 1 — `feat/katalog` · Katalog & detail buku
**Fokus:** sisi anggota untuk menjelajah koleksi. Read-only — tidak menulis ke database, jadi paling aman dikerjakan paralel.

Tugas:
- [ ] Halaman katalog: grid buku, pencarian judul, filter kategori, pagination
- [ ] Halaman detail buku: data bibliografi, cover, ketersediaan "X dari Y tersedia"
- [ ] Tombol Pinjam yang mengarah ke route milik Dev 2 (`pinjam.store`) — sepakati nama route di awal
- [ ] Ganti stub `katalog.index` jadi controller nyata

Layar mock / FR: 03 Katalog, 04 Detail Buku · FR-04, FR-05
Bergantung pada: model `Buku`, `Kategori`, accessor `Buku::tersedia` (fondasi)

#### Ryu (lead) — `feat/peminjaman` · Aksi pinjam & riwayat
**Fokus:** logika inti peminjaman + halaman pinjaman milik anggota. Sekaligus berperan menjaga kontrak FROZEN dan me-review PR tim.

Tugas:
- [ ] Implementasi `PeminjamanService::pinjam`: `DB::transaction` + `lockForUpdate`, validasi kuota lalu stok, buat `peminjaman` + `peminjaman_detail`, set `jatuh_tempo`
- [ ] `PeminjamanController::store` (memproses tombol Pinjam)
- [ ] Tangani `PeminjamanException` (kuota penuh, stok habis) jadi pesan sesuai mock
- [ ] Halaman "Peminjaman Saya & Riwayat": pinjaman aktif + riwayat, badge status/telat
- [ ] `PeminjamanSeeder` dengan data contoh sesuai mock

Layar mock / FR: 05 Peminjaman Saya · FR-06, FR-07
Bergantung pada: `PeminjamanServiceContract`, model, `config/perpus.php`

#### Dev 3 — `feat/admin-buku` · Manajemen koleksi & dashboard
**Fokus:** semua CRUD koleksi dan dashboard admin.

Tugas:
- [ ] CRUD Buku: list, tambah, edit, hapus, upload cover
- [ ] CRUD Kategori
- [ ] Form request validasi (`StoreBukuRequest`, `StoreKategoriRequest`)
- [ ] Dashboard: total buku, pinjaman aktif, buku terpopuler
- [ ] Seeder kategori + buku
- [ ] Ganti stub `admin.dashboard` jadi controller nyata

Layar mock / FR: 06 Dashboard, 07 Kelola Buku, 07B Kelola Kategori, 08 Form Buku · FR-09, FR-10, FR-11
Bergantung pada: model `Buku`, `Kategori`, enum

#### Dev 4 — `feat/admin-pinjam` · Pengembalian, denda & laporan
**Fokus:** sisi admin untuk konfirmasi pengembalian dan pelaporan.

Tugas:
- [ ] Implementasi `PengembalianService::kembalikan`: hitung hari telat, denda, set `status` + `tanggal_kembali`, lalu `PeminjamanDikembalikan::dispatch(...)`
- [ ] `PeminjamanAdminController`: daftar semua peminjaman + filter, tombol Kembalikan
- [ ] `LaporanController`: laporan per rentang tanggal + export

Layar mock / FR: 09 Kelola Peminjaman & Pengembalian, 10 Laporan · FR-08, FR-12
Bergantung pada: `PengembalianServiceContract`, model, event `PeminjamanDikembalikan` (fondasi)
Koordinasi: cukup dispatch event — **tidak** memanggil kode notifikasi (itu urusan Dev 5)

#### Dev 5 — `feat/notifikasi` · Notifikasi & pengingat
**Fokus:** seluruh kanal notifikasi, terdecouple dari modul lain lewat event.

Tugas:
- [ ] Notifikasi `JatuhTempoMendekat` (channel database + mail, `toMail`)
- [ ] Notifikasi `PengembalianDikonfirmasi`
- [ ] Listener `KirimNotifPengembalian` yang menangani event `PeminjamanDikembalikan`
- [ ] Command `perpus:kirim-pengingat` (query H-3) + jadwal di `routes/console.php`
- [ ] Endpoint tandai-dibaca + tampilan dropdown notifikasi

Layar mock / FR: 05B Dropdown Notifikasi · FR-13, FR-14
Bergantung pada: model `Peminjaman`, event `PeminjamanDikembalikan`, `config/perpus.php`
Koordinasi: hanya mendengarkan event — **tidak** perlu menunggu Dev 4 selesai

### Peta ketergantungan singkat

- Semua orang bergantung ke **fondasi** (model, enum, interface, event) — bukan ke kode satu sama lain.
- **Dev 1 → Dev 2 (Ryu):** lewat kesepakatan nama route `pinjam.store`.
- **Dev 4 → Dev 5:** lewat event `PeminjamanDikembalikan` (Dev 4 dispatch, Dev 5 listen).
- Tidak ada ketergantungan lain — itulah kenapa kelimanya bisa jalan paralel penuh.

---

## 4. Aturan emas (wajib)

1. **Fork dari `develop`, bukan dari branch teman.** Selalu mulai dari kontrak yang stabil.
2. **Migration yang sudah merge bersifat read-only.** Mau ubah kolom? Bikin migration baru (`php artisan make:migration add_x_to_y_table`). Jangan pernah edit migration lama — itu merusak DB semua orang.
3. **Program to interface.** Panggil service lewat interface (`PeminjamanServiceContract`), bukan kelas konkret. Implementasi boleh belum jadi — pakai fake/mock.
4. **Jangan sentuh file milik developer lain.** Kalau butuh perubahan di file bersama (model/route/seeder), ajukan ke lead lewat PR kecil terpisah; jangan diam-diam edit di branch fiturmu.
5. **Integrasi lintas modul lewat event, bukan panggilan langsung.** (Lihat bagian 7.)
6. **PR kecil dan sering.** Jangan numpuk seminggu lalu merge sekaligus.
7. **Rebase ke `develop` sebelum buka PR**, supaya konflik (kalau ada) selesai di sisimu, bukan di reviewer.

---

## 5. Alur kerja harian

```bash
# mulai branch (sekali)
git checkout develop
git pull origin develop
git checkout -b feat/katalog        # sesuai branch-mu

# selama kerja: commit kecil-kecil
git add .
git commit -m "feat(katalog): tambah pencarian judul"

# sebelum push / buka PR: sinkron dulu dengan develop
git fetch origin
git rebase origin/develop           # selesaikan konflik di sini kalau ada
git push origin feat/katalog
```

Lalu buka **Pull Request ke `develop`** di GitHub, isi checklist (bagian 10), minta 1 reviewer. Setelah di-approve dan CI hijau, **Squash and merge**.

---

## 6. Konvensi commit

Pakai format Conventional Commits: `tipe(scope): deskripsi singkat`.

| Tipe | Untuk |
|---|---|
| `feat` | fitur baru |
| `fix` | perbaikan bug |
| `refactor` | ubah struktur tanpa ubah perilaku |
| `test` | tambah/ubah test |
| `chore` | konfigurasi, dependency, hal non-kode |
| `docs` | dokumentasi |

Contoh: `feat(peminjaman): validasi kuota sebelum simpan`, `fix(admin-pinjam): perbaiki hitung denda saat tepat waktu`.

---

## 7. Integrasi antar-modul

Modul tidak boleh saling memanggil kode konkret. Gunakan dua mekanisme dari fondasi:

**Interface (untuk memanggil service):**
```php
public function store(PeminjamanServiceContract $service, Buku $buku) {
    $service->pinjam(auth()->user(), $buku);
}
```

**Event (untuk memicu aksi modul lain):** modul pengembalian tidak tahu apa-apa soal notifikasi — ia cukup dispatch event; modul notifikasi yang mendengarkan.
```php
// di PengembalianService (Dev 4) — tidak menyebut notifikasi sama sekali
PeminjamanDikembalikan::dispatch($peminjaman);

// di Listeners/KirimNotifPengembalian (Dev 5) — terpisah, auto-discovered
public function handle(PeminjamanDikembalikan $event): void {
    $event->peminjaman->user->notify(new PengembalianDikonfirmasi($event->peminjaman));
}
```

Kalau butuh data dari entitas, ambil dari **model fondasi** (`Buku`, `Peminjaman`, `User`) — bukan dari kode modul lain.

---

## 8. Pengujian

- Tulis test untuk logika inti: alur pinjam (kuota & stok), kalkulasi denda, transisi status.
- Modul boleh diuji **terisolasi** dengan mock kontrak — tidak perlu menunggu implementasi modul lain selesai:
```php
$this->mock(PeminjamanServiceContract::class)
     ->shouldReceive('pinjam')->once()
     ->andReturn(new Peminjaman(['status' => StatusPeminjaman::Dipinjam]));
```
- Untuk yang memicu event, pakai `Event::fake()` lalu `Event::assertDispatched(...)`.
- Jalankan sebelum push:
```bash
php artisan test
```
- **CI menjalankan `php artisan test` di setiap PR ke `develop`.** PR dengan test gagal tidak boleh di-merge.

---

## 9. Definition of Done

Sebuah PR dianggap selesai jika:
- [ ] Fitur berjalan sesuai mock UI / kebutuhan (FR terkait di BRD).
- [ ] Tidak mengedit migration lama, file bersama, atau file milik developer lain.
- [ ] Ada test untuk logika baru yang relevan, dan `php artisan test` hijau.
- [ ] Sudah di-rebase ke `develop` terbaru, tanpa konflik.
- [ ] Stub route (kalau ada) sudah diganti implementasi nyata.
- [ ] Di-review dan di-approve minimal 1 anggota tim.

---

## 10. Checklist PR

Salin ke deskripsi setiap Pull Request:

```markdown
## Apa yang dikerjakan
- ...

## Checklist
- [ ] Branch dari develop & sudah rebase
- [ ] Hanya menyentuh file milik branch ini
- [ ] Tidak mengedit migration yang sudah merge
- [ ] `php artisan test` hijau di lokal
- [ ] Integrasi lintas modul lewat interface/event (bukan panggilan langsung)
- [ ] Screenshot UI dilampirkan (untuk perubahan tampilan)
```

---

## 11. Gaya kode

- Ikuti **PSR-12** dan konvensi Laravel. Jalankan `./vendor/bin/pint` sebelum commit kalau tersedia.
- **Controller tipis, logika di service.** Jangan taruh aturan bisnis di controller atau view.
- Penamaan: kelas `PascalCase`, method/variabel `camelCase`, tabel & kolom domain `snake_case` Bahasa Indonesia (`tanggal_pinjam`, `jatuh_tempo`) sesuai skema.
- Jangan hardcode angka bisnis (lama pinjam, tarif denda, kuota) — ambil dari `config('perpus.*')`.
- Komentar secukupnya; kode yang jelas lebih baik daripada komentar yang banyak.

---

Pertanyaan soal kepemilikan file atau konflik kontrak? Tanyakan ke **lead (Ryu)** sebelum mengedit apa pun yang berstatus FROZEN.
