# Implementasi Sistem Peminjaman Buku - File Summary

## 📂 File yang Dibuat/Dimodifikasi

### 1️⃣ Model (app/Models/)

#### ✅ Peminjaman.php (BUAT)
- Model utama untuk tabel peminjaman
- Methods penting:
  - `calculateDenda()` - Hitung denda Rp 500/hari
  - `getDaysRemaining()` - Hitung sisa hari
  - `isOverdue()` - Cek apakah terlambat
  - `isNearDueDate()` - Cek jika H-3
  - Scopes: `pending()`, `active()`, `overdue()`, `completed()`
- Relationships: `belongsTo(User)`, `hasMany(PeminjamanDetail)`

#### ✅ PeminjamanDetail.php (BUAT)
- Model untuk detail buku dalam setiap peminjaman
- Relationships: `belongsTo(Peminjaman)`, `belongsTo(Buku)`

#### ✅ Buku.php (BUAT)
- Model tabel buku
- Relationships: `belongsTo(Kategori)`, `hasMany(PeminjamanDetail)`

#### ✅ Kategori.php (BUAT)
- Model untuk kategori buku
- Relationships: `hasMany(Buku)`

#### ✅ User.php (MODIFIKASI)
- Tambah relationships:
  - `peminjaman()` - Semua peminjaman user
  - `approvals()` - Peminjaman yang diapprove user (admin)
- Tambah method: `isAdmin()`

---

### 2️⃣ Controller (app/Http/Controllers/)

#### ✅ Admin/LoanController.php (MODIFIKASI)
```
Methods:
- index() - List pending + active loans dengan filter & search
- show($id) - Detail loan + form approve/reject
- approve($id) - Approve peminjaman (status: pending→dipinjam)
- reject($id) - Reject peminjaman (delete request)
```

#### ✅ Admin/ReturnController.php (MODIFIKASI)
```
Methods:
- index() - List returned loans dengan denda
- store() - Process return + calculate fine (Rp 500/hari)
```

#### ✅ Member/LoanController.php (MODIFIKASI)
```
Methods:
- index() - Show active loans untuk member
- create() - Form untuk request peminjaman baru
- store() - Submit request (status: pending, tunggu approval)
- history() - List completed loans
- show() - Detail loan
```

---

### 3️⃣ Middleware (app/Http/Middleware/)

#### ✅ AdminMiddleware.php (BUAT)
- Protect admin routes
- Check: `auth()->check()` && (`email == admin@moco.app` OR `role == admin`)
- Redirect unauthorized ke member area

---

### 4️⃣ Migration (database/migrations/)

#### ✅ 2026_06_21_112228_create_peminjaman_table.php (MODIFIKASI)
```sql
TAMBAH fields:
- approved_at (timestamp, nullable)
- approved_by (FK users, nullable)
UBAH:
- status enum: tambah 'pending' status
```

---

### 5️⃣ Seeder (database/seeders/)

#### ✅ DatabaseSeeder.php (MODIFIKASI)
- Create 1 Admin user (admin@moco.app)
- Create 3 Member users (john@, jane@, budi@example.com)
- Create 3 Kategori (Fiksi, Non-Fiksi, Pengembangan Diri)
- Create 8 Buku dengan stok
- Create 5 Sample peminjaman:
  1. Pending - Waiting approval
  2. Dipinjam - Ongoing (3 hari lagi)
  3. Dipinjam - Overdue (8 hari terlambat)
  4. Selesai - Dengan denda Rp 1.500
  5. Selesai - Tanpa denda (tepat waktu)

---

### 6️⃣ Views (resources/views/)

#### Admin Loans
✅ `admin/loans/index.blade.php` (MODIFIKASI)
- Tabs: Menunggu Approval | Peminjaman Aktif
- Table dengan columns: Anggota, Buku, Tanggal, Status, Aksi
- Inline approve/reject buttons untuk pending loans
- Filter: Status, Search

✅ `admin/loans/show.blade.php` (MODIFIKASI)
- Detail anggota & peminjaman
- Status badge dengan info lengkap
- Daftar buku yang dipinjam
- Untuk pending: Approve + Reject buttons
- Untuk dipinjam: Return button + Modal return
- Estimasi denda otomatis

#### Admin Returns
✅ `admin/returns/index.blade.php` (MODIFIKASI)
- List pengembalian buku
- Show denda Rp 500/hari
- Filter: Tepat Waktu | Terlambat
- Search: Anggota / Buku

#### Member Loans
✅ `member/loans/index.blade.php` (MODIFIKASI)
- Quota bar: Berapa buku sedang dipinjam (max 3)
- Button: "Pinjam Buku" (if quota < 3)
- Table: Active loans dengan sisa hari
- Link: Lihat riwayat peminjaman

✅ `member/loans/create.blade.php` (BUAT)
- Form peminjaman baru
- Select buku dari katalog
- Input jumlah (default: 1)
- Info: Syarat & ketentuan (7 hari, Rp 500/hari)
- Submit: Ajukan Peminjaman

✅ `member/loans/history.blade.php` (MODIFIKASI)
- Table: Riwayat peminjaman yang selesai
- Show denda jika ada
- Paginated

✅ `member/loans/show.blade.php` (BUAT)
- Detail status peminjaman (pending/dipinjam/selesai)
- Alert: Status pending/terlambat/selesai
- Info: Tanggal pinjam, jatuh tempo, kembali, denda
- Daftar buku yang dipinjam

---

### 7️⃣ Routes (routes/web.php)

#### MODIFIKASI:
```
Member Routes
- GET    /member/loans/create ← NEW
- Auth middleware added

Admin Routes
- Middleware 'admin' added to all routes
- POST   /admin/loans/{id}/approve ← NEW
- DELETE /admin/loans/{id}/reject ← NEW
```

---

### 8️⃣ Documentation (docs/)

#### ✅ SISTEM-PEMINJAMAN.md (BUAT)
- Gambaran umum sistem
- Status workflow
- Role & akses (Admin vs Member)
- Workflow lengkap
- Database schema
- UI components
- Tips & troubleshooting

#### ✅ QUICK-START.md (BUAT)
- Setup & instalasi
- Login credentials
- Testing workflow (5 test cases)
- File-file penting
- Debug tips
- Production checklist

---

## 🔑 Key Features Implemented

### ✅ Peminjaman dengan Approval
- Member ajukan request (status: pending)
- Hanya ADMIN yang bisa approve/reject
- Setelah approve: status → dipinjam, tanggal terisi otomatis
- Periode: 7 hari

### ✅ Perhitungan Denda Otomatis
- Tarif: Rp 500 per hari keterlambatan
- Dihitung saat pengembalian diproses
- Formula: (tgl_kembali - jatuh_tempo) × 500
- Contoh: 3 hari terlambat = Rp 1.500

### ✅ Batasan Peminjaman
- Max 3 buku per anggota (concurrent)
- Tidak bisa pinjam buku yang sudah di-request
- Validation di setiap tahap

### ✅ Admin Authorization
- Middleware AdminMiddleware
- Check email: admin@moco.app
- Hanya admin yang access `/admin/*`

### ✅ Status Flow
```
pending → dipinjam → selesai
(menunggu) (7 hari) (+ denda jika terlambat)
```

---

## 📊 Database Structure

### Tabel: peminjaman
| Column | Type | Notes |
|--------|------|-------|
| id | PK | - |
| user_id | FK | Member yang pinjam |
| tanggal_pinjam | date | Ketika disetujui |
| jatuh_tempo | date | Tanggal harus kembali (pinjam + 7) |
| tanggal_kembali | date | Actual return date (nullable) |
| denda | int | Denda Rp (default 0) |
| status | enum | pending\|dipinjam\|selesai |
| approved_at | timestamp | Kapan approval (nullable) |
| approved_by | FK | Admin yang approve (nullable) |

### Tabel: peminjaman_detail
| Column | Type | Notes |
|--------|------|-------|
| id | PK | - |
| peminjaman_id | FK | Link ke peminjaman |
| buku_id | FK | Buku yang dipinjam |
| jumlah | int | Qty (default 1) |

### Tabel: buku
| Column | Type | Notes |
|--------|------|-------|
| id | PK | - |
| kategori_id | FK | - |
| judul | string | Nama buku |
| penulis | string | Pengarang |
| penerbit | string | Publisher |
| tahun_terbit | int | Tahun terbit |
| isbn | string | ISBN |
| stok | int | Jumlah copy |

---

## 🎯 User Journey

### Member
```
1. Login → /member/loans
2. Lihat active loans & kuota
3. Klik "Pinjam Buku" → /member/loans/create
4. Pilih buku, submit → Status: PENDING
5. Tunggu admin approve
6. Setelah approve → Status: DIPINJAM (lihat di active loans)
7. Baca buku selama 7 hari
8. Admin kembalikan → Status: SELESAI (+ denda jika terlambat)
9. Lihat riwayat di /member/loans/history
```

### Admin
```
1. Login → /admin/loans
2. Lihat tab "Menunggi Approval"
3. Review request dari member
4. Klik "Lihat" → /admin/loans/{id}
5. Approve atau Tolak
6. Lihat peminjaman aktif di tab "Peminjaman Aktif"
7. Cek status: Aman, H-3, Terlambat
8. Proses pengembalian → Denda dihitung otomatis
9. Lihat riwayat di /admin/returns
```

---

## ⚠️ Important Notes

1. **Email Hardcoding** - Admin cek: `email == 'admin@moco.app'`
   - Production: Gunakan role column + migration
   
2. **Denda Calculation** - Rp 500/hari (bukan Rp 1.000 seperti UI lama)
   - Formula: `(return_date - due_date) × 500`

3. **Default Password** - Semua user: `password`
   - Seeder sudah bcrypt()

4. **Status Cannot Reverse** - pending → dipinjam → selesai
   - Tidak bisa ubah balik

5. **Auth Middleware** - Member routes protected dengan 'auth'
   - Admin routes protected dengan 'admin' middleware

---

## 🧪 Testing

Run migrations:
```bash
php artisan migrate
php artisan db:seed
```

Access:
- Admin: admin@moco.app / password
- Member: john@example.com / password

See `docs/QUICK-START.md` untuk test flow lengkap.

---

**Last Updated**: 2026-07-04
**Version**: 1.0 - Fully Implemented
