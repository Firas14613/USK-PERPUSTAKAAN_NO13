# Product Requirements Document
## Sistem Informasi Perpustakaan — SMKN 1 Purwokerto

> **Versi:** 1.0.0 | **Tanggal:** April 2026 | **Status:** Draft Review Internal
>
> **Tech Stack:** Laravel 12 · Filament 3 (Admin) · Laravel Breeze (Siswa) · MySQL 8.0+

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Latar Belakang & Tujuan](#2-latar-belakang--tujuan)
3. [Arsitektur & Tech Stack](#3-arsitektur--tech-stack)
4. [Database Design](#4-database-design)
5. [Alur Sistem (System Flow)](#5-alur-sistem-system-flow)
6. [Spesifikasi Panel Admin (Filament 3)](#6-spesifikasi-panel-admin-filament-3)
7. [Spesifikasi Panel Siswa (Breeze)](#7-spesifikasi-panel-siswa-breeze)
8. [Business Logic & Rules](#8-business-logic--rules)
9. [Middleware & Keamanan](#9-middleware--keamanan)
10. [Ringkasan Semua Halaman](#10-ringkasan-semua-halaman)
11. [Setup & Instalasi](#11-setup--instalasi)
12. [Acceptance Criteria](#12-acceptance-criteria)

---

## 1. Ringkasan Eksekutif

Sistem Informasi Perpustakaan SMKN 1 Purwokerto adalah aplikasi web multi-role yang mengelola seluruh proses operasional perpustakaan secara digital — dari pengelolaan koleksi buku, peminjaman berbasis mekanisme **request → approve**, pengembalian dengan kalkulasi denda otomatis, hingga laporan data peminjaman.

| Aspek | Detail |
|---|---|
| Nama Proyek | Sistem Informasi Perpustakaan SMKN 1 Purwokerto |
| Framework Backend | Laravel 12 |
| Panel Admin | Filament 3 |
| Panel Siswa | Laravel Breeze (Blade + Tailwind CSS) |
| Database | MySQL 8.0+ |
| PHP Version | PHP 8.2+ |
| Node.js | Node.js 18+ (asset build) |
| Tipe Aplikasi | Web Application (Multi-role: Admin & Siswa) |

---

## 2. Latar Belakang & Tujuan

### 2.1 Latar Belakang

Perpustakaan SMKN 1 Purwokerto masih mengelola proses peminjaman dan pengembalian buku secara manual. Masalah utama yang dihadapi:

- Pencatatan peminjaman masih dilakukan secara manual (buku tulis / spreadsheet)
- Tidak ada mekanisme otomatis untuk menghitung keterlambatan dan denda
- Siswa tidak dapat melihat ketersediaan buku secara real-time
- Admin kesulitan membuat laporan rekap peminjaman
- Tidak ada sistem autentikasi yang memisahkan hak akses admin dan siswa

### 2.2 Tujuan Proyek

- Membangun sistem informasi perpustakaan berbasis web yang terintegrasi
- Menyediakan dua panel terpisah: **Panel Admin** (Filament 3) dan **Panel Siswa** (Breeze)
- Mengotomatisasi perhitungan denda dengan opsi override manual oleh admin
- Menyediakan fitur request peminjaman oleh siswa yang disetujui admin
- Menyajikan laporan peminjaman yang dapat difilter dan dicari secara real-time

### 2.3 Ruang Lingkup

| Modul | Deskripsi |
|---|---|
| Auth | Login multi-role, redirect berdasarkan role |
| Manajemen User | CRUD admin & siswa |
| Manajemen Buku | CRUD buku & kategori, upload cover |
| Peminjaman | Request siswa → Approve/Tolak admin |
| Pengembalian | Proses admin, denda otomatis + override |
| Laporan | Tabel web dengan filter & search |
| Dashboard | Statistik ringkas per role |

---

## 3. Arsitektur & Tech Stack

### 3.1 Overview Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT BROWSER                          │
├──────────────────────┬──────────────────────────────────────┤
│   /admin/*           │   /dashboard, /koleksi-buku, dll     │
│   Panel Admin        │   Panel Siswa                        │
│   Filament 3         │   Laravel Breeze (Blade)             │
│   (Livewire + Alpine)│   (Tailwind CSS)                     │
├──────────────────────┴──────────────────────────────────────┤
│               Laravel 12 — Backend / Routing                │
│         Business Logic · Validation · Policies              │
├─────────────────────────────────────────────────────────────┤
│                 MySQL 8.0+ Database                         │
│   users · siswa · buku · kategori_buku                      │
│   peminjaman · pengembalian                                  │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Struktur Direktori Laravel

```
app/
├── Models/
│   ├── User.php
│   ├── Siswa.php
│   ├── Buku.php
│   ├── KategoriBuku.php
│   ├── Peminjaman.php
│   └── Pengembalian.php
├── Filament/
│   ├── Resources/
│   │   ├── BukuResource.php
│   │   ├── SiswaResource.php
│   │   ├── PeminjamanResource.php
│   │   ├── PengembalianResource.php
│   │   └── KategoriBukuResource.php
│   ├── Widgets/
│   │   ├── StatsOverviewWidget.php
│   │   └── PeminjamanChartWidget.php
│   └── Pages/
│       └── LaporanPage.php
├── Http/
│   ├── Controllers/
│   │   ├── SiswaDashboardController.php
│   │   ├── KoleksiBukuController.php
│   │   ├── PeminjamanSiswaController.php
│   │   ├── RiwayatPinjamController.php
│   │   └── ProfilSiswaController.php
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── SiswaMiddleware.php
└── Policies/
    └── PeminjamanPolicy.php

database/
├── migrations/
└── seeders/

resources/views/
├── siswa/
│   ├── dashboard.blade.php
│   ├── koleksi-buku.blade.php
│   ├── riwayat-pinjam.blade.php
│   └── profil.blade.php
└── components/
```

---

## 4. Database Design

### 4.1 Tabel: `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `username` | VARCHAR(255) UNIQUE | Username login |
| `email` | VARCHAR(255) UNIQUE | Email pengguna |
| `password` | VARCHAR(255) | Password (bcrypt) |
| `role` | ENUM('admin','siswa') | Peran pengguna |
| `nama_lengkap` | VARCHAR(255) | Nama lengkap |
| `alamat` | TEXT NULLABLE | Alamat domisili |
| `telepon` | VARCHAR(20) NULLABLE | Nomor telepon |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

```sql
CREATE INDEX idx_users_role ON users(role);
CREATE UNIQUE INDEX idx_users_username ON users(username);
```

### 4.2 Tabel: `siswa`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `user_id` | BIGINT UNSIGNED FK | FK → users.id |
| `nis` | VARCHAR(20) UNIQUE | Nomor Induk Siswa |
| `kelas` | VARCHAR(20) | Kelas (misal: XII PPLG 2) |
| `jurusan` | VARCHAR(100) NULLABLE | Jurusan siswa |
| `tanggal_lahir` | DATE NULLABLE | Tanggal lahir |
| `status` | ENUM('aktif','lulus','keluar') | Status, default: 'aktif' |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

```sql
CREATE UNIQUE INDEX idx_siswa_nis ON siswa(nis);
CREATE INDEX idx_siswa_status ON siswa(status);
```

### 4.3 Tabel: `kategori_buku`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `nama_kategori` | VARCHAR(100) | Nama kategori |
| `deskripsi` | TEXT NULLABLE | Deskripsi kategori |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

### 4.4 Tabel: `buku`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `kode_buku` | VARCHAR(50) UNIQUE | Kode unik (misal: BK001) |
| `judul` | VARCHAR(255) | Judul buku |
| `penulis` | VARCHAR(255) | Nama penulis |
| `penerbit` | VARCHAR(255) | Nama penerbit |
| `tahun_terbit` | YEAR | Tahun terbit |
| `kategori_id` | BIGINT UNSIGNED FK | FK → kategori_buku.id |
| `isbn` | VARCHAR(20) NULLABLE | ISBN |
| `jumlah_halaman` | INT NULLABLE | Jumlah halaman |
| `deskripsi` | TEXT NULLABLE | Sinopsis buku |
| `stok` | INT DEFAULT 0 | Stok tersedia |
| `lokasi_rak` | VARCHAR(50) NULLABLE | Lokasi rak (misal: A1-01) |
| `cover_image` | VARCHAR(255) NULLABLE | Path file cover |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

```sql
CREATE INDEX idx_buku_kategori ON buku(kategori_id);
CREATE UNIQUE INDEX idx_buku_kode ON buku(kode_buku);
```

### 4.5 Tabel: `peminjaman`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `kode_peminjaman` | VARCHAR(20) UNIQUE | Kode unik (PMJ-XXXXXXXX) |
| `siswa_id` | BIGINT UNSIGNED FK | FK → siswa.id |
| `buku_id` | BIGINT UNSIGNED FK | FK → buku.id |
| `admin_id` | BIGINT UNSIGNED FK | FK → users.id (admin) |
| `tanggal_pinjam` | DATE | Tanggal mulai dipinjam |
| `tanggal_kembali` | DATE NULLABLE | Tanggal aktual kembali |
| `batas_pengembalian` | DATE | Batas harus dikembalikan |
| `status` | ENUM('pending','dipinjam','dikembalikan','terlambat','hilang','ditolak') | Status transaksi |
| `denda` | DECIMAL(10,2) NULLABLE | Nominal denda |
| `catatan` | TEXT NULLABLE | Catatan tambahan |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

```sql
CREATE INDEX idx_peminjaman_status ON peminjaman(status);
CREATE INDEX idx_peminjaman_tanggal ON peminjaman(tanggal_pinjam);
CREATE UNIQUE INDEX idx_peminjaman_kode ON peminjaman(kode_peminjaman);
```

> **Catatan:** Status `pending` dan `ditolak` ditambahkan untuk mendukung alur **request → approve**.

### 4.6 Tabel: `pengembalian`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED AI PK | Primary key |
| `peminjaman_id` | BIGINT UNSIGNED FK | FK → peminjaman.id |
| `admin_id` | BIGINT UNSIGNED FK | FK → users.id (admin) |
| `tanggal_kembali_aktual` | DATE | Tanggal aktual kembali |
| `keterlambatan` | INT | Jumlah hari terlambat (0 = tepat waktu) |
| `denda_per_hari` | DECIMAL(10,2) | Tarif dari konfigurasi |
| `denda_otomatis` | DECIMAL(10,2) | keterlambatan × denda_per_hari |
| `denda_dibayar` | DECIMAL(10,2) NULLABLE | Final (bisa di-override admin) |
| `kondisi_buku` | ENUM('baik','rusak','hilang') | Kondisi fisik buku |
| `catatan` | TEXT NULLABLE | Catatan admin |
| `created_at/updated_at` | TIMESTAMP | Timestamps Laravel |

### 4.7 Relasi Eloquent

| Model | Relasi | Target | Method | Keterangan |
|---|---|---|---|---|
| User | hasOne | Siswa | `siswa()` | Satu user siswa → satu record siswa |
| User | hasMany | Peminjaman | `peminjamanAdmin()` | Admin memproses banyak peminjaman |
| Siswa | belongsTo | User | `user()` | Inverse User→Siswa |
| Siswa | hasMany | Peminjaman | `peminjaman()` | Satu siswa → banyak peminjaman |
| Buku | hasMany | Peminjaman | `peminjaman()` | Satu buku dipinjam berkali-kali |
| Buku | belongsTo | KategoriBuku | `kategori()` | Setiap buku masuk satu kategori |
| KategoriBuku | hasMany | Buku | `buku()` | Satu kategori → banyak buku |
| Peminjaman | hasMany | Pengembalian | `pengembalian()` | Satu peminjaman → banyak record pengembalian |
| Peminjaman | belongsTo | Siswa | `siswa()` | Inverse Siswa→Peminjaman |
| Peminjaman | belongsTo | Buku | `buku()` | Inverse Buku→Peminjaman |
| Peminjaman | belongsTo | User | `admin()` | Admin yang memproses |
| Pengembalian | belongsTo | Peminjaman | `peminjaman()` | Inverse Peminjaman→Pengembalian |
| Pengembalian | belongsTo | User | `admin()` | Admin yang memproses pengembalian |

---

## 5. Alur Sistem (System Flow)

> Lihat diagram flowchart visual di bawah ini.

### 5.1 Alur Autentikasi

| Langkah | Aktor | Aksi | Hasil |
|---|---|---|---|
| 1 | User | Akses `/login` | Form login ditampilkan |
| 2 | User | Input kredensial, klik Login | Sistem verifikasi |
| 3a | Sistem | Role = `admin` | Redirect ke `/admin` |
| 3b | Sistem | Role = `siswa` | Redirect ke `/dashboard` |
| 4 | Sistem | Role tidak sesuai route | 403 atau redirect |
| 5 | User | Klik Logout | Session dihapus, redirect `/login` |

### 5.2 Alur Peminjaman (Request → Approve)

| Langkah | Aktor | Aksi | Status |
|---|---|---|---|
| 1 | Siswa | Login, buka Koleksi Buku | — |
| 2 | Siswa | Pilih buku (stok > 0), klik PINJAM | — |
| 3 | Sistem | Buat record, generate kode PMJ-XXXXXXXX | `pending` |
| 4 | Sistem | Stok buku **belum** dikurangi | `pending` |
| 5 | Admin | Buka Peminjaman, lihat daftar pending | `pending` |
| 6a ✅ | Admin | Klik Approve | `dipinjam` |
| — | Sistem | Stok -1, tanggal_pinjam = hari ini, batas = +7 hari | `dipinjam` |
| 6b ❌ | Admin | Klik Tolak, isi catatan alasan | `ditolak` |
| — | Sistem | Status → ditolak, stok tidak berubah | `ditolak` |
| 7 | Siswa | Cek status di Riwayat Peminjaman | `dipinjam`/`ditolak` |

### 5.3 Alur Pengembalian & Denda

| Langkah | Aktor | Aksi | Catatan |
|---|---|---|---|
| 1 | Admin | Buka menu Peminjaman | — |
| 2 | Admin | Cari transaksi, klik Kembalikan | Hanya status `dipinjam`/`terlambat` |
| 3 | Sistem | Hitung keterlambatan = tgl_aktual − batas_kembali | — |
| 4 | Sistem | Denda otomatis = keterlambatan × denda_per_hari | Dari konfigurasi `.env` |
| 5 | Admin | Form tampil dengan denda otomatis terisi | Admin bisa override |
| 6a | Admin | Simpan tanpa ubah denda | `denda_dibayar` = otomatis |
| 6b | Admin | Ubah `denda_dibayar`, isi catatan wajib | Override denda |
| 7 | Admin | Pilih kondisi buku, klik Simpan | — |
| 8 | Sistem | Record pengembalian dibuat, status → `dikembalikan` | — |
| 9 | Sistem | Stok +1 (kecuali kondisi `hilang`) | Hilang = stok tidak bertambah |

---

## 6. Spesifikasi Panel Admin (Filament 3)

Akses via `/admin`, dilindungi middleware `AdminMiddleware`. Semua route memverifikasi `role = 'admin'`.

### 6.1 Dashboard Admin — `/admin`

**Filament Widgets:**

| Widget | Data | Tipe |
|---|---|---|
| Total Buku | `COUNT(buku)` | StatsOverviewWidget |
| Total Siswa Aktif | `COUNT(siswa WHERE status='aktif')` | StatsOverviewWidget |
| Total Peminjaman | `COUNT(peminjaman)` | StatsOverviewWidget |
| Peminjaman Pending | `COUNT(peminjaman WHERE status='pending')` | StatsOverviewWidget |
| Peminjaman Terlambat | `COUNT(peminjaman WHERE status='terlambat')` | StatsOverviewWidget |
| Grafik Peminjaman | Chart per bulan (12 bulan terakhir) | ChartWidget (Bar/Line) |

**Sidebar Filament:** Dashboard · Buku · Siswa · Peminjaman · Pengembalian · Laporan · Logout

### 6.2 Manajemen Buku — `/admin/buku`

**Resource:** `BukuResource`

**List:** Tabel dengan kolom cover thumbnail, judul, kode buku, penulis, kategori (badge), info terbit, stok & rak, aksi (Edit/Hapus). Filter kategori, search global.

**Form Tambah/Edit:**

| Field | Tipe Input | Validasi |
|---|---|---|
| Kode Buku | TextInput | required, unique, max:50 |
| Judul | TextInput | required, max:255 |
| Penulis | TextInput | required, max:255 |
| Kategori | Select | required |
| Penerbit | TextInput | required, max:255 |
| Tahun Terbit | TextInput | required, digits:4 |
| ISBN | TextInput | nullable, max:20 |
| Jumlah Halaman | TextInput (number) | nullable, integer, min:1 |
| Stok | TextInput (number) | required, integer, min:0 |
| Lokasi Rak | TextInput | nullable, max:50 |
| Cover Buku | FileUpload (image) | nullable, image, max:2MB |
| Deskripsi | Textarea | nullable |

### 6.3 Manajemen Siswa — `/admin/siswa`

**Resource:** `SiswaResource`

Form dibagi dua section: **Data Akun** (username, email, password, nama, alamat, telepon) dan **Data Siswa** (NIS, kelas, jurusan, tanggal lahir, status). Keduanya disimpan dalam satu database transaction.

### 6.4 Manajemen Peminjaman — `/admin/peminjaman`

**Resource:** `PeminjamanResource`

**Badge warna status:**
- `pending` → abu-abu
- `dipinjam` → kuning
- `dikembalikan` → hijau
- `terlambat` → merah
- `ditolak` → merah muda
- `hilang` → merah tua

**Aksi per status:**
- `pending`: tombol **Approve** (hijau) + **Tolak** (merah)
- `dipinjam`/`terlambat`: tombol **Kembalikan** (biru)

### 6.5 Manajemen Pengembalian — `/admin/pengembalian`

**Form Proses Pengembalian:**

| Field | Tipe | Keterangan |
|---|---|---|
| Tanggal Kembali Aktual | DatePicker | Default: hari ini |
| Keterlambatan (hari) | TextInput (readonly) | Dihitung otomatis |
| Denda Per Hari | TextInput (readonly) | Dari konfigurasi |
| Denda Otomatis | TextInput (readonly) | keterlambatan × tarif |
| Denda Dibayar | TextInput (editable) | Default = otomatis, bisa di-override |
| Kondisi Buku | Select | baik / rusak / hilang |
| Catatan | Textarea | Wajib jika override denda |

### 6.6 Laporan Peminjaman — `/admin/laporan`

**Statistik Cards:** Total Pinjaman · Total Terlambat

**Filter:** Search teks (buku/siswa) + dropdown status + tombol Filter & Reset

**Tabel:** Kode (link detail) · Nama Siswa · Buku · Tgl Kembali · Status (badge)

---

## 7. Spesifikasi Panel Siswa (Laravel Breeze)

Diakses dengan route `/dashboard` dst., dilindungi middleware `auth` + `SiswaMiddleware`. Tampilan menggunakan Blade + Tailwind CSS.

### 7.1 Dashboard Siswa — `/dashboard`

- Greeting: *"Selamat datang, [Nama Siswa]!"*
- Tiga shortcut card: **Koleksi Buku** · **Riwayat Pinjam** · **Profil Saya**
- Sidebar: Dashboard · Koleksi Buku · Riwayat Pinjam · Logout
- Nama siswa di pojok kanan atas

### 7.2 Koleksi Buku — `/koleksi-buku`

- Filter kategori (dropdown kanan atas)
- Grid kartu buku (responsive 2–4 kolom)
- Setiap kartu menampilkan: cover, badge ketersediaan, judul, penulis, kategori, tombol **PINJAM**
- Tombol PINJAM **disabled** jika stok = 0 atau sudah ada peminjaman aktif buku tersebut
- Klik PINJAM → buat record `pending` + flash notifikasi

### 7.3 Riwayat Peminjaman — `/riwayat-pinjam`

Tabel: Kode · Buku · Tgl Pinjam · Status (badge)

Data hanya milik siswa yang sedang login, diurutkan terbaru di atas.

### 7.4 Profil Siswa — `/profil`

- Edit data akun: nama, email, alamat, telepon
- Data siswa read-only: NIS, kelas, jurusan, tanggal lahir, status
- Form ubah password: password lama + baru + konfirmasi

### 7.5 Halaman Auth

| Halaman | Route | Keterangan |
|---|---|---|
| Login | `GET /login` | Form email/username + password |
| Logout | `POST /logout` | Hapus session, redirect `/login` |

> Registrasi mandiri oleh siswa **tidak tersedia**. Akun siswa hanya dibuat oleh admin.

---

## 8. Business Logic & Rules

### 8.1 Aturan Peminjaman

- Hanya buku dengan stok > 0 yang bisa di-request
- Siswa tidak bisa request buku yang sama jika masih ada status `pending` atau `dipinjam`
- Satu siswa bisa meminjam lebih dari satu judul buku sekaligus
- Kode peminjaman: `PMJ-` + 8 karakter random alphanumeric uppercase
- Default batas pengembalian = tanggal approve + 7 hari (konfigurasikan via `BATAS_HARI_PINJAM`)
- Stok buku **hanya dikurangi setelah admin Approve** (bukan saat request)

### 8.2 Aturan Pengembalian & Denda

- Denda otomatis = `keterlambatan_hari × DENDA_PER_HARI`
- Admin dapat override `denda_dibayar` ke nilai berapa pun (termasuk Rp 0)
- Jika override: field `catatan` **wajib** diisi
- Kondisi `hilang`: status → `hilang`, stok **tidak** bertambah
- Kondisi `rusak`: status → `dikembalikan`, stok +1
- Kondisi `baik`: status → `dikembalikan`, stok +1

### 8.3 Aturan Buku

- Kode buku tidak dapat diubah jika sudah ada riwayat peminjaman
- Buku tidak dapat dihapus jika ada peminjaman aktif (pending/dipinjam)
- Penghapusan buku ber-riwayat menggunakan **soft delete**
- Upload cover: JPG/PNG, maks 2MB, disimpan di `storage/app/public/covers`

### 8.4 Konfigurasi Sistem (`.env`)

| Parameter | Default | Keterangan |
|---|---|---|
| `DENDA_PER_HARI` | `1000` | Tarif denda per hari (rupiah) |
| `BATAS_HARI_PINJAM` | `7` | Durasi peminjaman default |
| `MAX_PINJAM_SISWA` | `3` | Maks buku dipinjam sekaligus |
| `STORAGE_COVER` | `public/covers` | Direktori cover buku |

---

## 9. Middleware & Keamanan

### 9.1 Daftar Middleware

| Middleware | Berlaku Pada | Fungsi |
|---|---|---|
| `auth` | Semua route siswa & admin | Redirect `/login` jika belum auth |
| `AdminMiddleware` | Semua `/admin/*` | Block jika `role != admin` |
| `SiswaMiddleware` | Semua route siswa | Block jika `role != siswa` |
| Filament Auth | Panel `/admin` | Cek auth + role untuk Filament |

### 9.2 Proteksi Data

- Password di-hash menggunakan **bcrypt** (Laravel default)
- CSRF protection aktif untuk semua form POST
- Validasi input server-side menggunakan **Laravel Form Request Validation**
- File upload divalidasi tipe MIME dan ukuran di server side
- Siswa hanya dapat melihat data peminjaman **miliknya sendiri** (policy/scope)
- `admin_id` selalu diambil dari `auth()->id()`, tidak dari input form

---

## 10. Ringkasan Semua Halaman

### 10.1 Panel Admin (Filament 3)

| No | Halaman | Route | Filament Class | Aksi |
|---|---|---|---|---|
| 1 | Dashboard Admin | `/admin` | `AdminDashboard` | Statistik & grafik |
| 2 | List Buku | `/admin/buku` | `BukuResource` | List, cari, filter |
| 3 | Tambah Buku | `/admin/buku/create` | `BukuResource (Create)` | Form buku baru |
| 4 | Edit Buku | `/admin/buku/{id}/edit` | `BukuResource (Edit)` | Ubah data buku |
| 5 | Detail Buku | `/admin/buku/{id}` | `BukuResource (View)` | Lihat detail |
| 6 | Manajemen Kategori | `/admin/kategori-buku` | `KategoriBukuResource` | CRUD kategori |
| 7 | List Siswa | `/admin/siswa` | `SiswaResource` | List, cari, filter |
| 8 | Tambah Siswa | `/admin/siswa/create` | `SiswaResource (Create)` | Buat akun + siswa |
| 9 | Edit Siswa | `/admin/siswa/{id}/edit` | `SiswaResource (Edit)` | Ubah data siswa |
| 10 | List Peminjaman | `/admin/peminjaman` | `PeminjamanResource` | Approve/Tolak/Kembalikan |
| 11 | Detail Peminjaman | `/admin/peminjaman/{id}` | `PeminjamanResource (View)` | Detail + proses |
| 12 | List Pengembalian | `/admin/pengembalian` | `PengembalianResource` | Riwayat pengembalian |
| 13 | Laporan Peminjaman | `/admin/laporan` | `LaporanPage` | Filter & laporan tabel |

### 10.2 Panel Siswa (Laravel Breeze)

| No | Halaman | Route | Controller@Method | View |
|---|---|---|---|---|
| 1 | Login | `GET /login` | `AuthenticatedSessionController@create` | `auth/login` |
| 2 | Dashboard Siswa | `GET /dashboard` | `SiswaDashboardController@index` | `siswa/dashboard` |
| 3 | Koleksi Buku | `GET /koleksi-buku` | `KoleksiBukuController@index` | `siswa/koleksi-buku` |
| 4 | Request Pinjam | `POST /pinjam` | `PeminjamanSiswaController@store` | — (redirect) |
| 5 | Riwayat Peminjaman | `GET /riwayat-pinjam` | `RiwayatPinjamController@index` | `siswa/riwayat-pinjam` |
| 6 | Profil Siswa | `GET /profil` | `ProfilSiswaController@edit` | `siswa/profil` |
| 7 | Update Profil | `POST /profil` | `ProfilSiswaController@update` | — (redirect) |
| 8 | Update Password | `POST /password` | `PasswordController@update` | — (redirect) |
| 9 | Logout | `POST /logout` | `AuthenticatedSessionController@destroy` | — (redirect) |

---

## 11. Setup & Instalasi

### 11.1 Prasyarat

- PHP >= 8.2 dengan ekstensi: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer >= 2.x
- Node.js >= 18.x dan npm >= 9.x
- MySQL >= 8.0 atau MariaDB >= 10.6
- Apache (mod_rewrite) atau Nginx

### 11.2 Langkah Instalasi

```bash
# 1. Clone & masuk direktori
git clone <repo-url>
cd sistem-perpustakaan

# 2. Install dependencies PHP
composer install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env — isi kredensial database & konfigurasi denda
# DB_DATABASE=perpustakaan
# DB_USERNAME=root
# DB_PASSWORD=secret
# DENDA_PER_HARI=1000
# BATAS_HARI_PINJAM=7

# 5. Migrasi & seed database
php artisan migrate
php artisan db:seed

# 6. Storage link untuk upload cover
php artisan storage:link

# 7. Install & build assets
npm install
npm run build

# 8. Jalankan server
php artisan serve
```

### 11.3 Akun Default (Seeder)

| Role | Email | Username | Password |
|---|---|---|---|
| Admin | `admin@perpus.sch.id` | `admin` | `password` |
| Siswa | `siswa1@perpus.sch.id` | `siswa1` | `password` |

---

## 12. Acceptance Criteria

| Modul | Kriteria | ✓ |
|---|---|---|
| Auth | Login admin → redirect `/admin` | □ |
| Auth | Login siswa → redirect `/dashboard` | □ |
| Auth | Akses `/admin` tanpa login → redirect `/login` | □ |
| Auth | Akses `/dashboard` dengan role admin → 403/redirect | □ |
| Buku | CRUD buku berfungsi dengan validasi lengkap | □ |
| Buku | Upload cover tersimpan dan tampil di list | □ |
| Buku | Filter dan search buku berfungsi | □ |
| Siswa | Tambah siswa buat record `users` DAN `siswa` dalam satu transaksi | □ |
| Peminjaman | Siswa klik PINJAM → status `pending` | □ |
| Peminjaman | Admin Approve → status `dipinjam`, stok -1 | □ |
| Peminjaman | Admin Tolak → status `ditolak`, stok tidak berubah | □ |
| Peminjaman | Filter status peminjaman berfungsi | □ |
| Pengembalian | Denda otomatis = hari_terlambat × tarif | □ |
| Pengembalian | Admin dapat override `denda_dibayar` | □ |
| Pengembalian | Stok +1 setelah dikembalikan (kecuali hilang) | □ |
| Laporan | Filter dan search laporan berfungsi | □ |
| Laporan | Statistik total pinjaman & terlambat akurat | □ |
| Portal Siswa | Koleksi tampil dengan filter kategori | □ |
| Portal Siswa | Tombol PINJAM disabled jika stok = 0 | □ |
| Portal Siswa | Riwayat hanya tampilkan peminjaman milik siswa login | □ |

---

*Dokumen ini disiapkan untuk keperluan USK 2026 — SMKN 1 Purwokerto | Versi 1.0.0 | April 2026*
