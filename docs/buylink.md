# Buylink / PPOB

Dokumen ini merangkum pemakaian Buylink di aplikasi TOMI. Jangan menulis nilai asli token/API key di dokumentasi, commit message, issue, chat, atau screenshot.

## Ringkasan

Buylink dipakai untuk membaca data transaksi PPOB, baik prepaid, postpaid, maupun ringkasan komisi leader.

| Area | Keterangan |
| --- | --- |
| Fungsi utama | Laporan transaksi PPOB dan komisi leader |
| Sisi admin | Ringkasan dan tabel transaksi prepaid/postpaid |
| Sisi user | Riwayat PPOB user lewat API |
| Autentikasi | Header `Authorization: Bearer ...` |
| Konfigurasi aktif | `.env` -> `config/services.php` |

## Lokasi Kode

| File | Fungsi |
| --- | --- |
| `app/Http/Controllers/Admin/PpobController.php` | Dashboard dan laporan PPOB admin |
| `app/Http/Controllers/User/HistoryController.php` | Riwayat PPOB user |
| `config/services.php` | Mapping konfigurasi Buylink |

## Konfigurasi

Variabel environment yang dipakai:

```text
BUYLINK_BASE_URL
BUYLINK_USER_TOKEN
BUYLINK_LEADER_TOKEN
```

Mapping Laravel:

```php
config('services.buylink.base_url')
config('services.buylink.user_token')
config('services.buylink.leader_token')
```

Catatan:

- `BUYLINK_USER_TOKEN` dipakai untuk laporan transaksi user/prepaid/postpaid.
- `BUYLINK_LEADER_TOKEN` dipakai untuk laporan leader atau komisi.
- Nilai token asli wajib tetap berada di `.env` atau secret manager, bukan di controller.

## Endpoint

Base URL:

```text
https://mitra.buylink.id
```

Endpoint transaksi user:

```text
GET /api/user/report/transaction
GET /api/user/report/transaction/summary
GET /api/user/report/postpaid/transaction
GET /api/user/report/postpaid/transaction/summary
```

Endpoint leader/komisi:

```text
GET /api/user/leader/transaction/summary
```

Parameter yang umum dikirim:

```text
start_date
end_date
status
type
customer_phone
product_name
ref_id
customer_name
payment_method
page
```

## Alur Admin

1. Admin membuka halaman laporan PPOB.
2. Aplikasi menyusun filter tanggal, produk, status, dan pagination.
3. Aplikasi mengirim request `GET` ke Buylink.
4. Buylink mengembalikan JSON.
5. Aplikasi menampilkan ringkasan atau tabel transaksi.

Route admin yang terkait:

```text
GET  /ppob/transaction/komisi
GET  /ppob/transaction/prepaid
GET  /ppob/transaction/postpaid
POST /ppob/transaction/komisi/summary/all
POST /ppob/transaction/summary/prepaid
POST /ppob/transaction/summary/postpad
POST /ppob/transacion/prepaid/table
POST /ppob/transacion/postpaid/table
```

Catatan: nama route `postpad` dan `transacion` mengikuti kondisi kode saat ini.

## Alur User

Controller:

```text
app/Http/Controllers/User/HistoryController.php
```

Route API:

```text
POST /history
```

Buylink dipakai saat request memiliki:

```text
path = ppob
```

Alur:

1. Aplikasi mengambil user yang sedang login.
2. Nomor HP user dinormalisasi ke format lokal.
3. Aplikasi meminta data prepaid dan postpaid ke Buylink.
4. Data digabung dan diurutkan dari transaksi terbaru.
5. Response dikembalikan ke aplikasi user.

## Environment

Konfigurasi mengikuti env masing-masing:

| Environment | Sumber |
| --- | --- |
| Local/develop | `.env` |
| Staging | `.env.stagging` atau env server staging |
| Production | `.env.production` atau env server production |

Setiap environment dapat memakai token berbeda selama `BUYLINK_*` diset dengan benar.

## Risiko

- Token kosong atau salah membuat request ke Buylink gagal.
- Token yang bocor dapat dipakai pihak lain untuk membaca data transaksi.
- Perubahan nama route typo dapat memengaruhi integrasi frontend jika sudah terlanjur dipakai.

## Rekomendasi

- Pastikan `BUYLINK_BASE_URL`, `BUYLINK_USER_TOKEN`, dan `BUYLINK_LEADER_TOKEN` ada di semua environment.
- Jangan hardcode token di controller.
- Setelah mengubah env di Laravel, jalankan `php artisan config:clear` atau rebuild config cache.

## Catatan Keamanan

Jangan menyalin nilai asli Bearer token Buylink ke dokumentasi, chat, issue, screenshot, commit message, atau log publik.
