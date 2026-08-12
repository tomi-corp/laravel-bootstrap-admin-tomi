
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

## Variabel Environment Penting

- `APP_NAME`, `APP_ENV`, `APP_URL`
- `APP_KEY`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`
- `VITE_APP_NAME`

> Catatan: proyek ini juga menggunakan variabel tambahan yang mungkin tidak semuanya ada di `.env.example`, seperti `JWT_SECRET`, `KEYDECRYPTION`, `SALTDECRYPTION`, `FCM_SERVER_KEY`, `POSTMARK_TOKEN`, dan layanan pihak ketiga lainnya.

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

## Lisensi

Lisensi MIT.
