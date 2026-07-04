# Sistem Peminjaman Buku - Dokumentasi Lengkap

## 📚 Gambaran Umum

Sistem peminjaman buku ini dirancang untuk mengelola proses peminjaman dan pengembalian buku di perpustakaan dengan fitur approval dari admin dan penghitungan denda otomatis.

## 🎯 Fitur Utama

### 1. **Proses Peminjaman Berpilih (Approval)**
- Anggota/Member mengajukan permintaan peminjaman buku
- Status awal: **Pending** (menunggu persetujuan admin)
- Hanya **ADMIN** yang dapat menyetujui atau menolak permintaan
- Setelah disetujui: Status berubah menjadi **Dipinjam**

### 2. **Periode Peminjaman: 7 Hari**
- Dihitung sejak tanggal approval dari admin
- Tanggal kembali otomatis: `tanggal_pinjam + 7 hari`
- Anggota dapat melihat sisa hari dalam dashboard

### 3. **Denda Otomatis Rp 500/Hari**
- Dihitung saat pengembalian diproses
- Formula: `(tanggal_kembali - jatuh_tempo) × 500`
- Contoh: 3 hari terlambat = Rp 1.500

### 4. **Batasan Peminjaman: Max 3 Buku**
- Setiap anggota hanya bisa meminjam max 3 buku secara bersamaan
- Tidak bisa mengajukan peminjaman jika sudah 3 buku aktif

## 📋 Status Peminjaman

```
┌─────────┐      ┌────────────┐      ┌─────────┐
│ PENDING │─────▶│   DIPINJAM  │─────▶│ SELESAI │
│ Menunggu│      │  (7 hari)   │      │(+ Denda)│
│ Approval│      │             │      │         │
└─────────┘      └────────────┘      └─────────┘
     │                │                   │
     └── DITOLAK ─────┘                   └── Rp 500/hari
         Dihapus             Hitung Denda    keterlambatan
```

## 👥 Role & Akses

### Admin (admin@moco.app)
- ✅ Melihat semua permintaan peminjaman (Pending)
- ✅ Menyetujui permintaan peminjaman
- ✅ Menolak permintaan peminjaman
- ✅ Melihat daftar peminjaman aktif
- ✅ Memproses pengembalian buku
- ✅ Melihat daftar denda

### Member/Anggota
- ✅ Melihat katalog buku
- ✅ Mengajukan permintaan peminjaman
- ✅ Melihat peminjaman aktif saya
- ✅ Melihat riwayat peminjaman
- ❌ Tidak bisa approve peminjaman

## 🔄 Workflow Lengkap

### Dari Perspektif Member

1. **Buka "Peminjaman Saya"** (`/member/loans`)
   - Lihat kuota: berapa buku sedang dipinjam
   - Lihat peminjaman aktif dengan sisa hari

2. **Klik "Pinjam Buku"** untuk mengajukan peminjaman baru
   - Pilih buku yang ingin dipinjam
   - Masukkan jumlah (default: 1)
   - Lihat syarat & ketentuan
   - Klik "Ajukan Peminjaman"
   - Status: **PENDING** (menunggu approval admin)

3. **Tunggu Admin Approve**
   - Akan muncul di "Peminjaman Saya" setelah disetujui
   - Status: **DIPINJAM**
   - Periode: 7 hari sejak approval

4. **Pengembalian Buku**
   - Admin memproses pengembalian di halaman detail
   - Denda dihitung otomatis jika ada keterlambatan
   - Status: **SELESAI**

5. **Lihat Riwayat**
   - Buka "Riwayat Peminjaman" di menu member
   - Lihat daftar buku yang sudah dikembalikan
   - Lihat jumlah denda yang dibayarkan

### Dari Perspektif Admin

1. **Dashboard Peminjaman** (`/admin/loans`)
   - Tab 1: "Menunggu Approval" - Lihat request baru
   - Tab 2: "Peminjaman Aktif" - Lihat peminjaman yang ongoing

2. **Mengelola Request Peminjaman**
   - Klik "Lihat" pada baris peminjaman pending
   - Lihat detail anggota & buku yang diminta
   - Tombol: **Setujui** atau **Tolak**
   - Jika Setujui: Status → DIPINJAM, tanggal ditetapkan
   - Jika Tolak: Permintaan dihapus

3. **Memproses Pengembalian**
   - Buka detail peminjaman aktif
   - Klik "Proses Pengembalian"
   - Masukkan tanggal kembali
   - Sistem otomatis hitung denda
   - Klik "Konfirmasi Pengembalian"

4. **Lihat Daftar Pengembalian** (`/admin/returns`)
   - Lihat semua peminjaman yang sudah dikembalikan
   - Filter: Tepat Waktu / Terlambat
   - Lihat jumlah denda per anggota

## 📊 Database Schema

### Tabel: peminjaman
```sql
- id (PK)
- user_id (FK ke users) - Anggota yang meminjam
- tanggal_pinjam (date) - Tanggal mulai peminjaman
- jatuh_tempo (date) - Tanggal harus dikembalikan
- tanggal_kembali (date, nullable) - Tanggal actual kembali
- denda (int) - Nilai denda (Rp)
- status (enum) - pending | dipinjam | selesai
- approved_at (timestamp, nullable) - Waktu approval
- approved_by (FK ke users, nullable) - Admin yang approve
- created_at, updated_at
```

### Tabel: peminjaman_detail
```sql
- id (PK)
- peminjaman_id (FK) - Referensi peminjaman
- buku_id (FK) - Buku yang dipinjam
- jumlah (int) - Jumlah copy
- created_at, updated_at
```

### Tabel: buku
```sql
- id (PK)
- kategori_id (FK) - Kategori buku
- judul (string)
- penulis (string)
- penerbit (string)
- tahun_terbit (int)
- isbn (string)
- stok (int) - Jumlah stok
- created_at, updated_at
```

## 🔐 Keamanan & Validasi

### Middleware
- **AdminMiddleware** - Hanya admin@moco.app yang bisa akses `/admin/*`
- **Auth** - Semua routes protected, harus login

### Validasi Business Logic
- ✅ Member tidak bisa pinjam jika sudah 3 buku aktif
- ✅ Member tidak bisa pinjam buku yang sudah di-request
- ✅ Hanya admin yang bisa approve/reject
- ✅ Denda hanya dihitung untuk pengembalian terlambat
- ✅ Tidak bisa reject peminjaman yang sudah approved

## 🧪 Testing Data

Seeder sudah menyediakan data testing:
- 1 Admin (admin@moco.app)
- 3 Member (john@, jane@, budi@example.com)
- 8 Buku dengan 3 kategori
- 5 Sample peminjaman:
  1. Pending - Menunggu approval
  2. Dipinjam - Ongoing (3 hari lagi)
  3. Dipinjam - Overdue (8 hari terlambat)
  4. Selesai - Dengan denda (Rp 1.500)
  5. Selesai - Tanpa denda (tepat waktu)

### Cara Testing

```bash
# Run migration
php artisan migrate

# Seed data
php artisan db:seed

# Login sebagai admin
Email: admin@moco.app
Password: password

# Login sebagai member
Email: john@example.com
Password: password
```

## 📱 UI Components

### Alert Styles
- `moco-alert-info` - Informasi biasa (biru)
- `moco-alert-warning` - Peringatan (orange/merah)
- `moco-alert-success` - Sukses (hijau)

### Badge Styles
- `badge-moco-active` - Dipinjam (biru)
- `badge-moco-late` - Terlambat (merah)
- `badge-moco-done` - Selesai (hijau)
- `badge bg-warning` - Pending (kuning)

### Tables
- `moco-table` - Styling table standard
- `moco-card` - Container card dengan border

## 💡 Tips Penggunaan

1. **Untuk Member yang Baru**
   - Arahkan ke `/member/loans`
   - Klik "Pinjam Buku"
   - Pilih buku dari katalog

2. **Untuk Admin yang Ingin Approve Cepat**
   - Buka `/admin/loans?status=pending`
   - Langsung lihat tab "Menunggu Approval"
   - Klik "Setujui" untuk approve

3. **Untuk Tracking Denda**
   - Buka `/admin/returns?status=terlambat`
   - Filter hanya peminjaman terlambat
   - Lihat detail denda per anggota

## 🐛 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Member tidak bisa pinjam | Cek kuota (max 3), atau tunggu admin approve |
| Admin tidak melihat peminjaman | Pastikan login sebagai admin@moco.app |
| Denda tidak dihitung | Pastikan tanggal kembali > jatuh tempo |
| Tidak bisa approve | Pastikan status masih "pending" |

## 📞 Informasi Kontak

Untuk pertanyaan lebih lanjut tentang sistem peminjaman buku ini, silakan hubungi:
- Admin Perpustakaan: admin@moco.app
- Dokumentasi Teknis: See `doc/TECHNICAL.md`
