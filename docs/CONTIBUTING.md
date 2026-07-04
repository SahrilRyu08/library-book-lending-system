# Pembagian Tugas — Moco (Perpustakaan Digital) (5 Developer)

> **Proyek:** Peminjaman Buku Perpustakaan (Laravel 12 + Bootstrap 5 + MySQL).
> **Konteks:** Route lengkap, Controller/Service masih **dummy data** → ganti ke MySQL sesuai ERD + lengkapi fitur sesuai rubrik.
> **Workflow Git:** `main` → `develop` → `feature/*`. PR ke `develop`.
> **Role:** enum DB = `admin` / `anggota` (anggota = "member" di brief).

---

## Ringkasan Pembagian

| # | Branch | PIC | Cakupan |
|---|--------|-----|---------|
| 1 | `feature/auth-foundation` | **Dev 1** | Auth & authz, migration, model+relasi, seeder+factory, scaffold notif |
| 2 | `feature/admin-book-category` | Dev 2 | CRUD Buku + upload foto & Kategori (Admin) |
| 3 | `feature/member-catalog` | Dev 3 | Daftar buku + keranjang pinjam (Anggota) |
| 4 | `feature/loan-system` | Dev 4 | Peminjaman header-detail, cegah stok habis, batas pinjam, denda |
| 5 | `feature/returns-reports-dashboard` | Dev 5 | Pengembalian, Laporan, Notifikasi H-3, Dashboard |

**Urutan:** Branch 1 merge `develop` duluan → Branch 2 → 3/4/5 paralel.

---

## INDEKS ENDPOINT (sumber kebenaran tunggal)

Legend: 🟢 = sudah ada di `routes/web.php` · 🔵 = endpoint **baru**, perlu ditambahkan.

### Auth
| St | Method | URI | Route name | Controller@method | Branch |
|---|---|---|---|---|---|
| 🟢 | GET | `/` | — | redirect ke `login` | 1 |
| 🟢 | GET | `/login` | `login` | `AuthController@showLogin` | 1 |
| 🟢 | POST | `/login` | `login.post` | `AuthController@login` | 1 |
| 🟢 | GET | `/register` | `register` | `AuthController@showRegister` | 1 |
| 🟢 | POST | `/register` | `register.post` | `AuthController@register` | 1 |
| 🟢 | POST | `/logout` | `logout` | `AuthController@logout` | 1 |

### Member (prefix `/member`, name `member.`)
| St | Method | URI | Route name | Controller@method | Branch |
|---|---|---|---|---|---|
| 🟢 | GET | `/member/books` | `member.books.index` | `Member\BookController@index` | 3 |
| 🟢 | GET | `/member/books/{id}` | `member.books.show` | `Member\BookController@show` | 3 |
| 🔵 | GET | `/member/cart` | `member.cart.index` | `Member\CartController@index` | 3 |
| 🔵 | POST | `/member/cart` | `member.cart.store` | `Member\CartController@store` | 3 |
| 🔵 | DELETE | `/member/cart/{bukuId}` | `member.cart.destroy` | `Member\CartController@destroy` | 3 |
| 🟢 | GET | `/member/loans` | `member.loans.index` | `Member\LoanController@index` | 4 |
| 🟢 | POST | `/member/loans` | `member.loans.store` | `Member\LoanController@store` | 4 |
| 🟢 | GET | `/member/loans/history` | `member.loans.history` | `Member\LoanController@history` | 4 |
| 🟢 | GET | `/member/loans/{id}` | `member.loans.show` | `Member\LoanController@show` | 4 |
| 🔵 | GET | `/member/notifications` | `member.notifications.index` | `Member\NotificationController@index` | 5 |
| 🔵 | POST | `/member/notifications/{id}/read` | `member.notifications.read` | `Member\NotificationController@markRead` | 5 |

### Admin (prefix `/admin`, name `admin.`)
| St | Method | URI | Route name | Controller@method | Branch |
|---|---|---|---|---|---|
| 🟢 | GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | 5 |
| 🟢 | GET | `/admin/books` | `admin.books.index` | `Admin\BookController@index` | 2 |
| 🟢 | GET | `/admin/books/create` | `admin.books.create` | `Admin\BookController@create` | 2 |
| 🟢 | POST | `/admin/books` | `admin.books.store` | `Admin\BookController@store` | 2 |
| 🟢 | GET | `/admin/books/{id}/edit` | `admin.books.edit` | `Admin\BookController@edit` | 2 |
| 🟢 | PUT | `/admin/books/{id}` | `admin.books.update` | `Admin\BookController@update` | 2 |
| 🟢 | DELETE | `/admin/books/{id}` | `admin.books.destroy` | `Admin\BookController@destroy` | 2 |
| 🟢 | GET | `/admin/categories` | `admin.categories.index` | `Admin\CategoryController@index` | 2 |
| 🟢 | POST | `/admin/categories` | `admin.categories.store` | `Admin\CategoryController@store` | 2 |
| 🟢 | PUT | `/admin/categories/{id}` | `admin.categories.update` | `Admin\CategoryController@update` | 2 |
| 🟢 | DELETE | `/admin/categories/{id}` | `admin.categories.destroy` | `Admin\CategoryController@destroy` | 2 |
| 🟢 | GET | `/admin/loans` | `admin.loans.index` | `Admin\LoanController@index` | 4 |
| 🟢 | GET | `/admin/loans/{id}` | `admin.loans.show` | `Admin\LoanController@show` | 4 |
| 🟢 | GET | `/admin/returns` | `admin.returns.index` | `Admin\ReturnController@index` | 5 |
| 🟢 | POST | `/admin/returns` | `admin.returns.store` | `Admin\ReturnController@store` | 5 |
| 🟢 | GET | `/admin/reports` | `admin.reports.index` | `Admin\ReportController@index` | 5 |
| 🔵 | GET | `/admin/reports/export` | `admin.reports.export` | `Admin\ReportController@export` | 5 (opsional) |

> 🔵 **Aturan tambah route:** hanya owner branch yang menambahkan route baru di blok grup miliknya, lalu kabari tim agar tidak konflik saat merge ke `develop`.

---

## Pemetaan FITUR / TANTANGAN / RUBRIK

**Fitur → Branch:** Login&registrasi(1) · Daftar buku(3 anggota/2 admin) · Upload foto(2) · Kelola kategori(2) · Peminjaman(4) · Pengembalian(5) · Riwayat(4) · Laporan(5) · Notifikasi jatuh tempo(5).

**Tantangan → Branch:** 2 role(1) · cegah stok habis(4) · denda otomatis(4 logic+5 eksekusi) · buku terpopuler(5) · notifikasi H-3(5) · batas buku per anggota(4).

**Aspek teknis wajib → bukti:**
| # | Aspek | Branch | Bukti |
|---|---|---|---|
| 1 | MVC | semua | controller tipis, logic di Service/Model |
| 2 | Relasi antar tabel | 1 | hasMany/belongsTo lengkap |
| 3 | Migration | 1 | 6 migration + FK |
| 4 | Seeder & Factory | 1 | Factory tiap model + seeder |
| 5 | Validation | 2,3,4 | Form Request tiap input |
| 6 | Auth & Authz | 1 | login/register + middleware `role` + Policy |
| 7 | Upload file | 2 | upload `cover` buku |

---

## Skema Database (acuan bersama — sesuai ERD)

Dibuat **Branch 1**.

**kategori:** id(PK) · nama_kategori(unique) · deskripsi(text,nullable) · timestamps
**users:** id(PK) · nama(50) · email(unique) · password(255 bcrypt) · role enum(`admin`/`anggota`) · timestamps
**buku:** id(PK) · kategori_id(FK) · judul(75) · penulis(50) · penerbit(30) · tahun_terbit(year) · isbn(unique) · deskripsi(text,nullable) · cover(nullable path) · stok(int, TOTAL eksemplar) · timestamps
**peminjaman:** id(PK) · user_id(FK) · tanggal_pinjam(date) · jatuh_tempo(date) · tanggal_kembali(date,nullable) · denda(int,default 0) · status enum(`dipinjam`/`selesai`/`terlambat`) · timestamps
**peminjaman_detail:** id(PK) · peminjaman_id(FK) · buku_id(FK) · jumlah(int,default 1) · timestamps
**notifications:** id(uuid PK) · type(string) · notifiable(morph→user) · data(json) · read_at(nullable) · timestamps

**Relasi:** kategori 1–N buku · users 1–N peminjaman · users 1–N notifications · peminjaman 1–N peminjaman_detail · buku 1–N peminjaman_detail

> **Tersedia (bukan kolom):** `stok - Σ jumlah pada peminjaman_detail yg peminjaman.status = 'dipinjam'`. Accessor `Buku::getTersediaAttribute()` (Branch 1).

**config/library.php:** `tarif_denda_per_hari`(1000) · `max_hari_pinjam`(7) · `max_buku_per_pinjam`(3) · `notif_h_minus`(3)
**Denda:** `max(0, hari_telat) x tarif_denda_per_hari`.

---

## 1. `feature/auth-foundation` — Dev 1

**Endpoint (semua 🟢):** `GET /`, `GET/POST /login`, `GET/POST /register`, `POST /logout`

**Implementasi:**
- [ ] `AuthController@showLogin` → view form login
- [ ] `AuthController@login` → Form Request `LoginRequest`, `Auth::attempt`, regenerate session, redirect by role (admin→dashboard, anggota→katalog)
- [ ] `AuthController@showRegister` → view form register
- [ ] `AuthController@register` → `RegisterRequest`, buat user role `anggota`, hash password, auto-login
- [ ] `AuthController@logout` → `Auth::logout` + invalidate session

**Fondasi (dipakai semua):**
- [ ] 6 migration + FK constraint
- [ ] Model + relasi: `Kategori`, `User`(Notifiable), `Buku`, `Peminjaman`, `PeminjamanDetail`
- [ ] Accessor `Buku::getTersediaAttribute()`
- [ ] Factory tiap model + `DatabaseSeeder` (1 admin, anggota, kategori, buku, contoh peminjaman)
- [ ] Middleware `role` + register di `bootstrap/app.php`; Policy untuk aksi admin
- [ ] Proteksi grup route `member`/`admin`
- [ ] `config/library.php`
- [ ] Base class `PeminjamanNotification` (scaffold Dev 5)

**Merge duluan.**

---

## 2. `feature/admin-book-category` — Dev 2

**Endpoint:**
| Method | URI | Controller@method | Implementasi |
|---|---|---|---|
| GET | `/admin/books` | `Admin\BookController@index` | list buku + pagination + filter kategori/judul/ISBN + kolom `tersedia` |
| GET | `/admin/books/create` | `@create` | form tambah buku |
| POST | `/admin/books` | `@store` | `BookRequest` validasi + **upload cover** ke storage; set stok |
| GET | `/admin/books/{id}/edit` | `@edit` | form edit |
| PUT | `/admin/books/{id}` | `@update` | update + ganti cover (hapus lama) |
| DELETE | `/admin/books/{id}` | `@destroy` | cegah hapus kalau ada peminjaman aktif |
| GET | `/admin/categories` | `Admin\CategoryController@index` | list + form inline (create/edit di halaman ini) |
| POST | `/admin/categories` | `@store` | `CategoryRequest`, `nama_kategori` unik |
| PUT | `/admin/categories/{id}` | `@update` | update kategori |
| DELETE | `/admin/categories/{id}` | `@destroy` | cegah hapus kalau masih punya buku |

- [ ] Form Request `BookRequest`: judul(75), penulis(50), penerbit(30), tahun_terbit, isbn unik, stok≥0, kategori_id exists, cover image|max
- [ ] Upload cover ke `storage/app/public`, tampil thumbnail, flash message tiap aksi

**Depend on:** Branch 1.

---

## 3. `feature/member-catalog` — Dev 3

**Endpoint:**
| Method | URI | Controller@method | Implementasi |
|---|---|---|---|
| GET | `/member/books` | `Member\BookController@index` | list buku DB + pagination + search judul/penulis + filter kategori + badge `tersedia` |
| GET | `/member/books/{id}` | `@show` | detail buku + tombol "Tambah ke peminjaman" |
| 🔵 GET | `/member/cart` | `Member\CartController@index` | lihat isi keranjang (session) |
| 🔵 POST | `/member/cart` | `@store` | tambah buku ke keranjang, cek `tersedia` & `max_buku_per_pinjam` |
| 🔵 DELETE | `/member/cart/{bukuId}` | `@destroy` | hapus buku dari keranjang |

- [ ] Keranjang berbasis session; tombol "Ajukan Peminjaman" submit ke `member.loans.store` (Branch 4)
- [ ] Empty state + UI responsif Bootstrap 5

**Depend on:** Branch 1 & 2. Sepakati payload ke Dev 4: array `{buku_id, jumlah}`.

---

## 4. `feature/loan-system` — Dev 4 (header-detail)

**Endpoint:**
| Method | URI | Controller@method | Implementasi |
|---|---|---|---|
| POST | `/member/loans` | `Member\LoanController@store` | dari keranjang → **1 peminjaman + N detail** dalam DB transaction; set tanggal_pinjam, jatuh_tempo, status `dipinjam`; **validasi stok** & **batas pinjam** |
| GET | `/member/loans` | `@index` | peminjaman aktif (`dipinjam`/`terlambat`) milik user + bukunya |
| GET | `/member/loans/history` | `@history` | peminjaman `selesai` (Riwayat) |
| GET | `/member/loans/{id}` | `@show` | detail 1 transaksi + estimasi denda kalau telat |
| GET | `/admin/loans` | `Admin\LoanController@index` | semua peminjaman + filter status + pagination |
| GET | `/admin/loans/{id}` | `@show` | detail transaksi (header + semua detail) |

- [ ] `StoreLoanRequest`: validasi item keranjang, `tersedia >= jumlah` tiap buku
- [ ] **Batas pinjam:** total buku aktif anggota ≤ `max_buku_per_pinjam`
- [ ] **`LoanService`**: `hitungDenda(Peminjaman $p)`, `tandaiTerlambat()` — kontrak disepakati dgn Dev 5

**Depend on:** Branch 1 (model+accessor), Branch 3 (keranjang).

---

## 5. `feature/returns-reports-dashboard` — Dev 5

**Endpoint:**
| Method | URI | Controller@method | Implementasi |
|---|---|---|---|
| GET | `/admin/returns` | `Admin\ReturnController@index` | daftar peminjaman belum kembali (`dipinjam`/`terlambat`) |
| POST | `/admin/returns` | `@store` | set `tanggal_kembali`, hitung `denda` via `LoanService`, set status `selesai`/`terlambat`; stok pulih otomatis |
| GET | `/admin/reports` | `Admin\ReportController@index` | rekap: total peminjaman, **buku terpopuler** (agregasi peminjaman_detail), total denda, jumlah terlambat + filter tanggal |
| GET | `/admin/dashboard` | `Admin\DashboardController@index` | kartu statistik query nyata (buku, anggota, peminjaman aktif, terlambat) |
| 🔵 GET | `/member/notifications` | `Member\NotificationController@index` | daftar notifikasi anggota |
| 🔵 POST | `/member/notifications/{id}/read` | `@markRead` | tandai `read_at` |

- [ ] **Notifikasi H-3:** command `php artisan loans:check-due` (jadwal harian) → kirim ke anggota yg `jatuh_tempo` = hari+`notif_h_minus`; sekalian set `terlambat` yg lewat due
- [ ] Notifikasi saat terlambat & konfirmasi pengembalian via `User->notify(...)`

**Depend on:** Branch 1 (model+scaffold), Branch 4 (`LoanService`).

---

## Aturan Main Tim

1. Commit kecil & jelas: `feat(loan): cegah pinjam saat stok habis`.
2. Tambah/ubah route hanya di blok grup milik sendiri; kabari tim.
3. Jangan ubah migration orang lain tanpa kabar.
4. Stok jangan dikurangi manual — pakai accessor `tersedia`.
5. MVC: controller tipis, logic di Service/Model.
