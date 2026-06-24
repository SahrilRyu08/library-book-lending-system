# MOCO - UI Preview Branch

Branch ini digunakan untuk preview tampilan (UI) aplikasi **MOCO - Library Book Lending System** tanpa koneksi database. Seluruh data yang ditampilkan menggunakan controller dummy sehingga anggota tim dapat melihat dan mengevaluasi desain antarmuka sebelum implementasi backend dilakukan.

---

## Tujuan Branch

* Preview tampilan aplikasi Member dan Admin
* Mempermudah diskusi desain UI


---

## Struktur File

### Routes

```text
routes/
└── web.php
```

### Controllers

```text
app/Http/Controllers/
├── Auth/
│   └── AuthController.php
├── Member/
│   ├── BookController.php
│   └── LoanController.php
└── Admin/
    ├── DashboardController.php
    ├── BookController.php
    ├── CategoryController.php
    ├── LoanController.php
    ├── ReturnController.php
    └── ReportController.php
```

### Views

```text
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── admin.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── member/
│   ├── books/
│   └── loans/
└── admin/
    ├── dashboard/
    ├── books/
    ├── categories/
    ├── loans/
    ├── returns/
    └── reports/
```

### Assets

```text
public/css/
└── moco.css
```

---



## Akun Login Preview

Karena menggunakan controller dummy, password tidak divalidasi.

### Admin

```text
Email    : admin@moco.app
Password : bebas
```

### Member

```text
Email    : selain admin@moco.app
Password : bebas
```

---

## Halaman yang Dapat Dicoba

### Member

| URL             | Halaman                  |
| --------------- | ------------------------ |
| /member/books   | Katalog Buku             |
| /member/books/1 | Detail Buku              |
| /member/books/3 | Detail Buku (Stok Habis) |
| /member/loans   | Peminjaman Saya          |

### Admin

| URL                 | Halaman           |
| ------------------- | ----------------- |
| /admin/dashboard    | Dashboard         |
| /admin/books        | Kelola Buku       |
| /admin/books/create | Tambah Buku       |
| /admin/books/1/edit | Edit Buku         |
| /admin/categories   | Kelola Kategori   |
| /admin/loans        | Daftar Peminjaman |
| /admin/returns      | Pengembalian Buku |
| /admin/reports      | Laporan           |

---

## Catatan Penting

* Semua data pada branch ini bersifat dummy.
* Tidak ada koneksi database.
* Tidak ada proses CRUD yang benar-benar tersimpan.
* Branch ini hanya digunakan untuk evaluasi dan review tampilan.
* Saat implementasi backend, seluruh controller dummy akan diganti dengan controller berbasis Eloquent dan database MySQL.

---


\
