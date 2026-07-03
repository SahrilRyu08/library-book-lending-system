# Quick Start - Sistem Peminjaman Buku

## ⚡ Setup & Instalasi

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed data dummy
php artisan db:seed
```

### 4. Build Assets
```bash
npm run dev
# atau untuk production:
npm run build
```

### 5. Start Development Server
```bash
php artisan serve
```

Server berjalan di: `http://localhost:8000`

## 👤 Login Credentials

### Admin
- **Email**: admin@moco.app
- **Password**: password
- **Akses**: `/admin/*`

### Member 1
- **Email**: john@example.com
- **Password**: password

### Member 2
- **Email**: jane@example.com
- **Password**: password

### Member 3
- **Email**: budi@example.com
- **Password**: password

## 🧪 Testing Workflow

### Test 1: Member Mengajukan Peminjaman

1. Login sebagai `john@example.com`
2. Buka `/member/loans`
3. Klik **"Pinjam Buku"**
4. Pilih buku "Laskar Pelangi"
5. Klik **"Ajukan Peminjaman"**
6. Lihat status: **PENDING** ✓

### Test 2: Admin Approve Peminjaman

1. Login sebagai `admin@moco.app`
2. Buka `/admin/loans?status=pending`
3. Lihat request dari John Doe
4. Klik **"Lihat"**
5. Klik **"Setujui Peminjaman"**
6. Lihat status berubah menjadi **DIPINJAM** ✓
7. Tanggal pinjam & jatuh tempo otomatis terisi ✓

### Test 3: Member Lihat Peminjaman Aktif

1. Login sebagai `john@example.com`
2. Buka `/member/loans`
3. Lihat "Peminjaman Saya" menampilkan buku yang baru diapprove
4. Lihat sisa hari peminjaman (7 hari) ✓
5. Buka riwayat untuk melihat peminjaman lama ✓

### Test 4: Admin Proses Pengembalian

1. Login sebagai `admin@moco.app`
2. Buka `/admin/loans` → tab "Peminjaman Aktif"
3. Klik **"Lihat"** pada peminjaman John
4. Klik **"Proses Pengembalian"**
5. Masukkan tanggal kembali (misal: terlambat 3 hari)
6. Lihat estimasi denda: **Rp 1.500** (3 × Rp 500)
7. Klik **"Konfirmasi Pengembalian"** ✓
8. Buka `/admin/returns` → lihat denda tercatat ✓

### Test 5: Cek Overdue

1. Login sebagai `admin@moco.app`
2. Buka `/admin/loans` → filter "Terlambat"
3. Lihat peminjaman yang sudah melewati jatuh tempo
4. Lihat "Sisa Hari" menampilkan nilai negatif ✓

## 📝 File-File Penting

| File | Fungsi |
|------|--------|
| `app/Models/Peminjaman.php` | Model peminjaman + methods denda |
| `app/Models/PeminjamanDetail.php` | Model detail buku yang dipinjam |
| `app/Http/Controllers/Admin/LoanController.php` | Controller approval peminjaman |
| `app/Http/Controllers/Admin/ReturnController.php` | Controller pengembalian & denda |
| `app/Http/Controllers/Member/LoanController.php` | Controller request peminjaman |
| `app/Http/Middleware/AdminMiddleware.php` | Middleware admin protection |
| `database/migrations/2026_06_21_112228_...` | Migration peminjaman |
| `database/seeders/DatabaseSeeder.php` | Seeder data testing |

## 🎨 UI Routes

### Member
- `/member/loans` - Daftar peminjaman aktif
- `/member/loans/create` - Form peminjaman
- `/member/loans/history` - Riwayat peminjaman
- `/member/loans/{id}` - Detail peminjaman

### Admin
- `/admin/loans` - Kelola peminjaman (pending + active)
- `/admin/loans/{id}` - Detail + approve/reject
- `/admin/returns` - Daftar pengembalian & denda

## 🔍 Debug Tips

### Cek Status Peminjaman di Database
```bash
# Akses tinker
php artisan tinker

# Lihat semua peminjaman
App\Models\Peminjaman::all()

# Lihat peminjaman pending
App\Models\Peminjaman::where('status', 'pending')->get()

# Lihat peminjaman dengan detail
App\Models\Peminjaman::with(['user', 'detail.buku'])->get()
```

### Reset Database
```bash
# Truncate dan seed ulang
php artisan migrate:fresh --seed
```

### Cek Denda Calculation
```bash
# Di tinker
$loan = App\Models\Peminjaman::find(1);
$loan->calculateDenda(); // Hitung denda
```

## 📌 Catatan Penting

1. **Middleware Admin** - Pastikan auth()->check() bekerja dengan baik
2. **Default Password** - Gunakan "password" untuk semua test user
3. **Tanggal Otomatis** - Jatuh tempo otomatis saat approve (+ 7 hari)
4. **Denda Calculation** - Hanya dihitung saat status berubah ke "selesai"
5. **Status Flow** - pending → dipinjam → selesai (tidak bisa reverse)

## 🚀 Production Checklist

- [ ] Update email authentication (jangan hardcode admin@moco.app)
- [ ] Add proper user role column di database
- [ ] Implement email notifications
- [ ] Add logging untuk approval/rejection
- [ ] Setup payment gateway untuk denda
- [ ] Add inventory management (kurangi stok saat dipinjam)
- [ ] Setup backup database regular
- [ ] Configure email untuk notifikasi reminder

---

**Happy Testing!** 📚✨
