# Pembagian Tugas Proyek Peminjaman Buku Perpustakaan

Halo teman-teman 👋

Supaya pengerjaan proyek **Peminjaman Buku Perpustakaan** bisa berjalan paralel dan mengurangi conflict Git, saya sudah membagi tugas berdasarkan requirement yang ada di slide.

---

## Langkah Awal

Silakan update branch `develop` terlebih dahulu:

```bash
git checkout develop
git pull origin develop
```

Lalu buat branch sesuai tugas masing-masing:

```bash
git checkout -b nama-branch
```

Contoh:

```bash
git checkout -b feat/book-management
```

---

# 1️⃣ Authentication & Authorization

**Branch:** `feat/authentication-authorization`

## Tugas

- Login
- Register
- Logout
- Role Admin
- Role Member
- Middleware Role
- Authorization

## Checklist

- [ ] Login Page
- [ ] Register Page
- [ ] Logout
- [ ] Session Authentication
- [ ] Middleware Auth
- [ ] Middleware Role
- [ ] Validasi Login
- [ ] Validasi Register

## Checkout

```bash
git checkout develop
git pull origin develop
git checkout -b feat/authentication-authorization
```

---

# 2️⃣ Category Management

**Branch:** `feat/category-management`

## Tugas

- CRUD Kategori Buku

## Checklist

- [ ] List Category
- [ ] Create Category
- [ ] Edit Category
- [ ] Delete Category
- [ ] Validation
- [ ] Pagination

## Checkout

```bash
git checkout develop
git pull origin develop
git checkout -b feat/category-management
```

---

# 3️⃣ Book Management

**Branch:** `feat/book-management`

## Tugas

- CRUD Buku
- Upload Foto Buku

## Checklist

- [ ] List Buku
- [ ] Create Buku
- [ ] Edit Buku
- [ ] Delete Buku
- [ ] Upload Cover
- [ ] Validation
- [ ] Relasi Category

## Checkout

```bash
git checkout develop
git pull origin develop
git checkout -b feat/book-management
```

---

# 4️⃣ Borrow & Return Book

**Branch:** `feat/borrow-return`

## Tugas

- Peminjaman Buku
- Pengembalian Buku
- Riwayat Peminjaman

## Checklist

- [ ] Form Peminjaman
- [ ] Simpan Peminjaman
- [ ] Pengurangan Stok
- [ ] Pengembalian Buku
- [ ] Penambahan Stok
- [ ] Riwayat Peminjaman
- [ ] Validasi Stok

## Checkout

```bash
git checkout develop
git pull origin develop
git checkout -b feat/borrow-return
```

---

# 5️⃣ Report & Notification

**Branch:** `feat/report-notification`

## Tugas

- Laporan Peminjaman
- Notifikasi Jatuh Tempo

## Checklist

- [ ] Dashboard Ringkasan
- [ ] Laporan Peminjaman
- [ ] Filter Laporan
- [ ] Notifikasi H-3 Jatuh Tempo
- [ ] Statistik Peminjaman

## Checkout

```bash
git checkout develop
git pull origin develop
git checkout -b feat/report-notification
```

---

# Aturan Git

## ✅ Selalu pull develop sebelum mulai kerja

```bash
git checkout develop
git pull origin develop
```

## ✅ Commit dengan pesan yang jelas

Contoh:

```bash
git commit -m "feat: add category CRUD"
```

## ✅ Push ke branch masing-masing

```bash
git push origin nama-branch
```

## ❌ Larangan

- Jangan push langsung ke `main`
- Jangan mengubah migration yang sudah dibuat pada branch setup
- Jangan merge sendiri ke `develop`
- Wajib membuat Pull Request terlebih dahulu

---

# Flow Branch

```text
main
 │
develop
 ├── feat/authentication-authorization
 ├── feat/category-management
 ├── feat/book-management
 ├── feat/borrow-return
 └── feat/report-notification
```

---

# Proses Pengumpulan

1. Kerjakan fitur pada branch masing-masing.
2. Commit secara berkala dengan pesan yang jelas.
3. Push ke repository.
4. Buat Pull Request ke `develop`.
5. Tunggu review sebelum merge.
6. Informasikan di grup jika fitur sudah selesai.

🚀 Semangat teman-teman, semoga pengerjaannya lancar dan minim conflict Git.
