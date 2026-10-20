
# TOMI V2

Aplikasi backend Laravel untuk KSP Tomi versi 2.

## Ringkasan

TOMI V2 adalah aplikasi web berbasis Laravel 11 yang dikembangkan untuk kebutuhan KSP Tomi. Aplikasi ini menyediakan modul manajemen pengguna, pinjaman, pembayaran cicilan, ekspor/import data, laporan PDF/Excel, dan API pendukung.

## Fitur Utama

- Dashboard admin dan manajemen pengguna
- Modul pinjaman, payroll loan, dan gadget installment
- Export data ke Excel dan PDF
- Login JWT untuk API
- Google 2FA dan notifikasi FCM
- Generate QR code, dokumen Word, dan browser screenshot
- Routing terpisah untuk admin, seller, partner, dan API

## Teknologi

- PHP 8.2
- Laravel 11
- Vite + Laravel Vite Plugin
- Composer
- Node.js / npm
- PHPUnit untuk pengujian

## Struktur Proyek

- `app/` – kode aplikasi utama
- `bootstrap/` – bootstrap dan cache framework
- `config/` – konfigurasi aplikasi dan layanan
- `database/` – migrasi, seeders, dan factory
- `public/` – entry point aplikasi
- `resources/` – views, assets, dan file frontend
- `routes/` – definisi routing aplikasi
- `storage/` – cache, sesi, dan file runtime
- `tests/` – unit dan feature test

## Persiapan Pengembangan

1. Install dependensi PHP:
   ```bash
   composer install
   ```
2. Install dependensi Node:
   ```bash
   npm install
   ```
3. Buat file environment:
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Set konfigurasi database, mail, dan layanan lain di `.env`.
6. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```
7. Jalankan build frontend:
   ```bash
   npm run dev
   ```

## Variabel Environment

Semua variabel yang dibaca aplikasi tersedia di [`.env.example`](.env.example)
dengan nilai rahasia dan nilai spesifik server dikosongkan. Penjelasan setiap
kelompok variabel tersedia di [`docs/environment.md`](docs/environment.md).

Salin template tersebut menjadi `.env`, isi konfigurasi sesuai lingkungan, lalu
jalankan `php artisan key:generate`. Jangan menaruh credential pada repository
atau pada variabel frontend `VITE_`.

## Perintah Umum

- Jalankan server Laravel:
  ```bash
  php artisan serve
  ```
- Build frontend produksi:
  ```bash
  npm run build
  ```
- Jalankan migrasi:
  ```bash
  php artisan migrate
  ```
- Clear config cache:
  ```bash
  php artisan config:clear
  ```
- Jalankan test:
  ```bash
  ./vendor/bin/phpunit
  ```

## Catatan Penting

- Jangan commit file `.env`, `.env.production`, atau `.env.stagging` ke repositori.
- Gunakan `.env.example` sebagai template konfigurasi.
- Jika menjalankan di lingkungan produksi, pastikan konfigurasi `APP_ENV`, `APP_DEBUG`, dan `APP_URL` sudah diatur dengan benar.

## Kontributor

Berikut kontributor yang tercatat pada repositori ini:

- imamrovid9
- [Reza Saputra](https://github.com/rezasaputra4250)
- [Muhammad Yusvan Fadhillah](https://github.com/yusvanfadhil)

Terima kasih atas kontribusi dan kerja samanya.

## Lisensi

Lisensi MIT.
