# Verihubs WhatsApp OTP

Dokumen ini merangkum pemakaian Verihubs di aplikasi TOMI. Jangan menulis nilai asli `APP-ID`, `API-Key`, atau credential Verihubs di dokumentasi, commit message, issue, chat, atau screenshot.

## Ringkasan

Verihubs dipakai untuk mengirim kode OTP melalui WhatsApp.

| Area | Keterangan |
| --- | --- |
| Fungsi utama | Kirim OTP WhatsApp |
| Helper utama | `app/Helpers/helpers.php` |
| Fungsi | `sendOTPWhatsapp($nohp, $code)` |
| Endpoint | `POST /v2/whatsapp/otp/send` |
| Autentikasi | Header `APP-ID` dan `API-Key` |

## Lokasi Kode

| File | Fungsi |
| --- | --- |
| `app/Helpers/helpers.php` | Helper pengiriman OTP WhatsApp |
| `config/services.php` | Mapping konfigurasi Verihubs |
| `app/Http/Controllers/User/AuthController.php` | OTP registrasi/login/verifikasi user |
| `app/Http/Controllers/User/PinjamanController.php` | OTP proses pinjaman |
| `app/Http/Controllers/User/GadgetInstallmentController.php` | OTP gadget installment |

## Konfigurasi

Variabel environment:

```text
VERIHUBS_APP_ID
VERIHUBS_API_KEY
VERIHUBS_BASE_URL
VERIHUBS_OTP_TEMPLATE
VERIHUBS_LANG_CODE
VERIHUBS_TIME_LIMIT
```

Mapping Laravel:

```php
config('services.verihubs.app_id')
config('services.verihubs.api_key')
config('services.verihubs.base_url')
config('services.verihubs.otp_path')
config('services.verihubs.otp_template')
config('services.verihubs.lang_code')
config('services.verihubs.time_limit')
```

Catatan:

- Base URL disimpan di `.env`.
- Path OTP disimpan di `config/services.php`.
- Helper tidak seharusnya menyimpan credential asli.

## Endpoint

Base URL umum:

```text
https://api.verihubs.com
```

Path OTP:

```text
/v2/whatsapp/otp/send
```

URL lengkap:

```text
POST https://api.verihubs.com/v2/whatsapp/otp/send
```

Header:

```text
APP-ID
API-Key
Content-Type: application/json
Accept: application/json
```

Payload:

```text
msisdn
otp
time_limit
template_name
lang_code
```

Template yang tampak dipakai:

```text
otp_tomi
```

## Alur Pengiriman OTP

1. Proses aplikasi membuat kode OTP.
2. Kode memanggil `sendOTPWhatsapp($nomorWhatsapp, $kodeOtp)`.
3. Helper membuat payload Verihubs.
4. Aplikasi mengirim request `POST` ke endpoint OTP WhatsApp Verihubs.
5. Verihubs mengirim pesan WhatsApp ke nomor tujuan.
6. Response Verihubs dikembalikan ke proses pemanggil.

## Environment

Konfigurasi mengikuti env masing-masing:

| Environment | Sumber |
| --- | --- |
| Local/develop | `.env` |
| Staging | `.env.stagging` atau env server staging |
| Production | `.env.production` atau env server production |

Setiap environment dapat memakai credential Verihubs berbeda selama `VERIHUBS_*` diset dengan benar.

## Risiko

- `VERIHUBS_APP_ID` atau `VERIHUBS_API_KEY` kosong membuat OTP gagal dikirim.
- Template yang salah dapat membuat request ditolak provider.
- Token/credential bocor dapat disalahgunakan untuk mengirim OTP.

## Rekomendasi

- Pastikan semua `VERIHUBS_*` tersedia di local, staging, dan production.
- Simpan credential hanya di `.env` atau secret manager.
- Setelah mengubah env, jalankan `php artisan config:clear` atau rebuild config cache.

## Catatan Keamanan

Jangan menyalin nilai asli `APP-ID`, `API-Key`, atau credential Verihubs lain ke dokumentasi, chat, issue, screenshot, commit message, atau log publik.
