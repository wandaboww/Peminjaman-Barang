# Deployment Guide

## Status

Aplikasi ini sudah disiapkan untuk deployment PHP hosting dengan langkah konfigurasi production yang benar.

## Rekomendasi Utama

- Gunakan **document root** ke folder `public/`
- Pakai `.env` production dengan password admin yang kuat
- Siapkan recovery code admin untuk fitur lupa password
- Aktifkan HTTPS
- Pastikan folder session dan database bisa ditulis server

## File Entry Point

- Jika hosting mendukung custom document root:
  - arahkan ke `public/`
  - entry file yang dipakai: `public/index.php`
- Jika hosting tidak mendukung custom document root:
  - gunakan root proyek sebagai document root
  - proteksi file sensitif sudah dibantu oleh `.htaccess`

## Checklist Production

1. Copy `.env.example` menjadi `.env`
2. Ubah nilai berikut:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
ADMIN_PASSWORD=ganti_dengan_password_yang_kuat
ADMIN_RECOVERY_CODE=ganti_dengan_kode_pemulihan
HTTPS_ONLY=true
DB_DRIVER=sqlite
SQLITE_DATABASE_FILE=database/sim_inventaris.sqlite
```

3. Pastikan file/folder ini writable oleh PHP:
   - `storage/sessions/`
   - `database/`
   - `database/sim_inventaris.sqlite`

## Proteksi yang Sudah Ditambahkan

- CSRF protection untuk request POST
- Session cookie lebih aman (`HttpOnly`, `SameSite=Lax`)
- Security headers dasar
- `.htaccess` untuk memblokir akses langsung ke:
  - `.env`
  - `database/`
  - `storage/`
  - file `.md`
  - file SQLite

## Verifikasi Setelah Upload

1. Buka halaman utama
2. Pastikan login admin berhasil
3. Coba fitur ubah password dari menu `Pengaturan`
4. Coba fitur `Lupa password?` di halaman login memakai recovery code
3. Coba tambah dan hapus 1 user
4. Pastikan file sensitif tidak bisa dibuka langsung:
   - `/.env`
   - `/database/sim_inventaris.sqlite`
   - `/GETTING_STARTED.md`

## Catatan

- Aturan `.htaccess` berlaku untuk Apache / LiteSpeed
- Jika hosting memakai Nginx, aturan blok file sensitif perlu dibuat ulang di konfigurasi server
