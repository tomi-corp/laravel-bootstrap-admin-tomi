# Privy

Dokumen ini merangkum pemakaian Privy di aplikasi TOMI. Jangan menulis nilai asli URL internal, username, password, token, client secret, atau credential Privy di dokumentasi, commit message, issue, chat, atau screenshot.

## Ringkasan

Privy dipakai untuk dua proses utama:

| Proses | Keterangan |
| --- | --- |
| Verifikasi identitas | User mengirim data KTP, selfie, NIK, nama, tanggal lahir, email, dan nomor HP |
| Tanda tangan digital | Aplikasi mengirim dokumen ke Privy untuk ditandatangani |

Konfigurasi Privy yang aktif saat ini diambil dari database, bukan dari `.env`.

```text
tabel: privies
model: App\Models\privy
cara ambil: privy::first()
```

## Lokasi Kode

| File | Fungsi |
| --- | --- |
| `app/Helpers/helpers.php` | Login Privy, signature, cek status, tanda tangan dokumen |
| `app/Http/Controllers/User/PrivyController.php` | Register dan resend Privy |
| `app/Http/Controllers/Guest/CallbackController.php` | Callback register Privy |
| `app/Http/Controllers/User/PinjamanController.php` | Pemakaian Privy pada proses pinjaman |
| `app/Http/Controllers/User/GadgetInstallmentController.php` | Pemakaian Privy pada gadget installment |
| `routes/web.php` | Route callback |
| `routes/api.php` | Route API user |

## Sumber Konfigurasi Aktif

Tabel database:

```text
privies
```

Field yang dipakai:

| Field | Fungsi |
| --- | --- |
| `url` | Base URL API Privy |
| `username` | `client_id` dan identity signature |
| `password` | `client_secret` dan HMAC secret signature |
| `token` | Access token Privy |
| `expired` | Masa berlaku access token |

Catatan:

- `.env`, `.env.stagging`, dan `.env.production` dapat memiliki `PRIVY_URL`.
- Selama kode masih memakai `$privy->url`, perubahan `PRIVY_URL` tidak mengubah endpoint aktif.
- Perbedaan environment mengikuti isi tabel `privies` di database masing-masing environment.

## Data Privy Per User

Data proses Privy disimpan di tabel `users`.

| Field | Keterangan |
| --- | --- |
| `id_privy` | ID user di Privy |
| `enterprise_token_privy` | Token enterprise dari Privy untuk user |
| `reference_number_privy` | Nomor referensi proses register |
| `register_token_privy` | Token register dari Privy |
| `reason_privy` | Alasan penolakan jika verifikasi gagal |
| `resend_privy` | Penanda apakah user boleh kirim ulang data |
| `update_data_privy` | Penanda apakah user sudah update data |
| `verified_privy` | Status verifikasi user, biasanya `y` atau `n` |

## Endpoint Privy

Semua endpoint memakai base URL dari `privies.url`.

```text
/oauth2/api/v1/token
/web/api/v2/register
/web/api/v2/register/resend
/web/api/v2/register/status
/web/api/v2/doc-signing
/web/api/v2/doc-signing/status
```

Callback dari Privy ke TOMI:

```text
POST /callback/register/privy
```

## Alur Login Privy

Helper:

```text
app/Helpers/helpers.php
fungsi: loginPrivy()
```

Alur:

1. Aplikasi mengambil data pertama dari tabel `privies`.
2. Jika token masih berlaku, token lama dipakai.
3. Jika token expired, aplikasi meminta token baru ke `/oauth2/api/v1/token`.
4. Token baru dan waktu expired disimpan kembali ke database.

## Alur Register / Verifikasi User

Controller:

```text
app/Http/Controllers/User/PrivyController.php
fungsi: registerPrivy()
```

Data yang dikirim:

```text
reference_number
channel_id
email
phone
nik
name
dob
selfie
identity/KTP
```

File selfie dan KTP diubah ke base64 sebelum dikirim.

Kemungkinan status:

| Status | Dampak |
| --- | --- |
| `waiting_verification` | Simpan `register_token_privy` dan `reference_number_privy` |
| `verified` / `registered` | Set `verified_privy = y` |
| `rejected` | Set `verified_privy = n`, simpan alasan dan status resend |

## Alur Resend Register

Controller:

```text
app/Http/Controllers/User/PrivyController.php
fungsi: resendRegister()
```

Syarat yang dicek:

```text
NIK ada
nama lengkap ada
selfie ada
KTP ada
kartu keluarga ada
update_data_privy bernilai true
```

Endpoint:

```text
/web/api/v2/register/resend
```

## Alur Callback

Controller:

```text
app/Http/Controllers/Guest/CallbackController.php
fungsi: privyRegisterCallback()
```

Cara mencari user:

```php
User::where('register_token_privy', $req->register_token)->first();
```

Jika status `rejected`, aplikasi menyimpan alasan penolakan dan status resend.

Jika bukan `rejected`, aplikasi menyimpan:

```text
id_privy
enterprise_token_privy
verified_privy = y
reason_privy = null
resend_privy = null
```

Aplikasi juga membuat catatan biaya PSE Privy lewat model `app/Models/privy_pse.php`.

## Alur Tanda Tangan Dokumen

Helper:

```text
app/Helpers/helpers.php
fungsi: ttdPK()
```

Endpoint:

```text
/web/api/v2/doc-signing
```

Data penting:

```text
reference_number
channel_id
document
doc_owner
recipients
```

Dokumen diubah ke base64 sebelum dikirim.

## Alur Cek Status Dokumen

Helper:

```text
app/Helpers/helpers.php
fungsi: getDocumentPK()
```

Endpoint:

```text
/web/api/v2/doc-signing/status
```

Data:

```text
reference_number
channel_id
document_token
info
```

## Signature

Request ke Privy memakai access token dan signature.

Helper:

```text
app/Helpers/helpers.php
fungsi: signature()
```

Signature dibuat dari:

```text
request body
username dari tabel privies
password dari tabel privies
method request
timestamp
```

Header umum:

```text
timestamp
Authorization: Bearer token
Signature
Request-ID
Content-Type
Accept
```

## Environment

Sumber aktif per environment adalah database masing-masing environment.

| Environment | Sumber aktif |
| --- | --- |
| Local/develop | Tabel `privies` database local/develop |
| Staging | Tabel `privies` database staging |
| Production | Tabel `privies` database production |

`PRIVY_URL` di `.env*` saat ini lebih tepat dianggap legacy atau belum aktif selama kode masih memakai `$privy->url`.

## Risiko

- Mengubah `PRIVY_URL` di `.env` tidak berdampak jika database `privies.url` tidak berubah.
- Credential di tabel `privies` bocor dapat membuka akses ke Privy.
- Token expired yang gagal diperbarui akan membuat proses Privy gagal.
- Callback bergantung pada kecocokan `register_token_privy`.

## Rekomendasi

- Pilih satu sumber konfigurasi: database atau `.env`.
- Jika tetap database, beri catatan bahwa `PRIVY_URL` di `.env` tidak aktif.
- Jika pindah ke `.env`, pindahkan URL dan credential statis ke config Laravel.
- Jangan menampilkan credential Privy di log atau response API.

## Catatan Keamanan

Jangan menyalin nilai asli `url`, `username`, `password`, `token`, client secret, atau credential Privy lain ke dokumentasi, chat, issue, screenshot, commit message, atau log publik.
