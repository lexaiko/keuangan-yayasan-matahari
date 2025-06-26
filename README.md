# ☀️ Sistem Informasi Manajemen Keuangan Yayasan Matahari

Sistem informasi ini dibangun dengan Laravel 11 & FilamentPHP untuk mendukung **transparansi, efisiensi, dan akuntabilitas keuangan** pada Yayasan Matahari — sebuah yayasan edukasi dan sosial yang berlokasi di Banyuwangi.

Dikembangkan sebagai fondasi profesional menggunakan **Kaido Kit**: starter kit FilamentPHP dengan fitur siap pakai untuk pengelolaan keuangan berbasis web.

![GitHub stars](https://img.shields.io/github/stars/lexaiko/Sistem-Informasi-Manajemen-Keuangan-Yayasan?style=flat-square)
![PHP Version](https://img.shields.io/badge/PHP-8.2-blue?style=flat-square&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-11.0-red?style=flat-square&logo=laravel)
![Filament Version](https://img.shields.io/badge/Filament-3.2-purple?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)

---

## 🎯 Tujuan Sistem

- 📊 Membantu staf yayasan mencatat transaksi pemasukan & pengeluaran secara sistematis  
- 🔐 Meningkatkan keamanan & akuntabilitas laporan keuangan  
- 🌱 Mendukung pengambilan keputusan yayasan berbasis data real-time  
- 📤 Menyediakan fitur ekspor laporan ke Excel / PDF untuk keperluan audit & pelaporan  

---

## ✨ Fitur Unggulan

### 🔐 Keamanan & Manajemen Pengguna

- Login & Register
- Manajemen user berbasis peran (Admin, Staf)
- Otorisasi berbasis **Role & Permission** (Filament Shield)
- 2FA & Social Login *(opsional)*

### 📒 Modul Keuangan

- 📥 **Pemasukan**: pembayaran siswa, Sumbangan, Iuran, kas karyawan.
- 📤 **Pengeluaran**: Operasional, Gaji, Kegiatan Sosial, Edukasi
- 📁 **Kategori Dinamis**
- 📈 **Laporan Bulanan**: Real-time & ekspor ke PDF/Excel
- 🔎 Filter transaksi berdasarkan waktu & kategori

### ⚙️ Setting & Ekstensi

- Panel pengaturan dinamis (Filament Settings)
- API-ready dengan autentikasi
- Media Library untuk upload dokumen pendukung
- Impersonasi akun (debug/staff assist)

---

## 🚀 Instalasi

```bash
git clone https://github.com/lexaiko/Sistem-Informasi-Manajemen-Keuangan-Yayasan.git
cd Sistem-Informasi-Manajemen-Keuangan-Yayasan

composer install
npm install && npm run dev

cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
php artisan migrate --seed

# Setup roles
php artisan shield:generate --all
php artisan shield:super-admin

php artisan serve
```
## 🐳 Jalankan dengan Laravel Sail (Docker)
```bash
composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan shield:generate --all
./vendor/bin/sail artisan shield:super-admin
```

---
