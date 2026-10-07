# Google reCAPTCHA / CAPTCHA

Dokumen ini merangkum pemakaian Google reCAPTCHA di aplikasi TOMI. Jangan menulis nilai asli secret key CAPTCHA di dokumentasi, commit message, issue, chat, atau screenshot.

## Ringkasan

Google reCAPTCHA dipakai untuk memastikan request form publik berasal dari manusia, bukan bot.

| Area | Keterangan |
| --- | --- |
| Fungsi utama | Proteksi form pendaftaran e-kasbon |
| Field request | `g-recaptcha-response` |
| Secret backend | `SECRET_G_CAPTCHA` |
| Endpoint Google | `https://www.google.com/recaptcha/api/siteverify` |

## Lokasi Kode

| File | Fungsi |
| --- | --- |
| `app/Http/Requests/Public/Ekasbon/DaftarEkasbonRequest.php` | Validasi field reCAPTCHA wajib ada |
| `app/Http/Controllers/Admin/DashboardController.php` | Verifikasi token ke Google |
| `config/services.php` | Mapping secret reCAPTCHA |

## Konfigurasi

Variabel environment:

```text
SECRET_G_CAPTCHA
```

Mapping Laravel:

```php
'recaptcha' => [
    'secret' => env('SECRET_G_CAPTCHA'),
],
```

Catatan saat ini:

- `config/services.php` sudah menyediakan mapping `recaptcha.secret`.
- Beberapa kode masih membaca langsung `env('SECRET_G_CAPTCHA')`.
- Frontend membutuhkan site key, tetapi backend yang aktif mengecek secret key.

## Alur Verifikasi

1. User mengisi form pendaftaran e-kasbon.
2. Frontend mendapatkan token reCAPTCHA dari Google.
3. Token dikirim ke backend sebagai `g-recaptcha-response`.
4. Request divalidasi oleh `DaftarEkasbonRequest`.
5. Backend mengirim token, secret key, dan IP user ke Google `siteverify`.
6. Jika Google mengembalikan `success = true`, proses dilanjutkan.
7. Jika gagal, request ditolak dengan pesan verifikasi keamanan gagal.

## Endpoint Google

```text
POST https://www.google.com/recaptcha/api/siteverify
```

Payload:

```text
secret   : secret key dari SECRET_G_CAPTCHA
response : token dari g-recaptcha-response
remoteip : IP address user/request
```

## Environment

Konfigurasi mengikuti env masing-masing:

| Environment | Sumber |
| --- | --- |
| Local/develop | `.env` |
| Staging | `.env.stagging` atau env server staging |
| Production | `.env.production` atau env server production |

Jika setiap environment memakai secret key berbeda, masing-masing environment akan memverifikasi ke konfigurasi reCAPTCHA yang berbeda.

## Variabel Terkait Yang Pernah Ada

```text
SECRET_G_CAPTCHA
GEETEST_KEY
SITE_KEY
H_CAPTCHA_SECRET
```

Catatan: dari kode backend yang dicek, verifikasi Google reCAPTCHA aktif memakai `SECRET_G_CAPTCHA`.

## Risiko

- `SECRET_G_CAPTCHA` kosong atau salah membuat verifikasi gagal.
- Secret key bocor dapat disalahgunakan pihak lain.
- Membaca `env()` langsung di controller dapat bermasalah jika config cache aktif.

## Rekomendasi

- Gunakan `config('services.recaptcha.secret')` di controller.
- Pastikan `.env.example` hanya berisi nama variabel, bukan nilai asli.
- Setelah mengubah env, jalankan `php artisan config:clear` atau rebuild config cache.

## Catatan Keamanan

Jangan menyalin nilai asli `SECRET_G_CAPTCHA` atau secret CAPTCHA lain ke dokumentasi, chat, issue, screenshot, commit message, atau log publik.
