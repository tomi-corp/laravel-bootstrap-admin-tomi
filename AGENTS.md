# Panduan Agen dan Kredensial Proyek TOMI

Dokumen ini menjadi peta utama untuk bekerja di repo TOMI. Fokus utamanya adalah lokasi konfigurasi, aturan keamanan secret, konvensi tampilan data, dan aturan perubahan kode.

Jangan menyalin nilai asli secret, token, password, private key, atau credential pihak ketiga ke dokumentasi, commit message, issue, chat, screenshot, atau log publik.

## Dokumen Terkait

| Dokumen | Isi |
| --- | --- |
| `docs/buylink.md` | Alur Buylink/PPOB, token report user, token leader/komisi |
| `docs/fcm.md` | Firebase Cloud Messaging, project ID, service account JSON |
| `docs/g-captcha.md` | Google reCAPTCHA, secret key, verifikasi form e-kasbon |
| `docs/privy.md` | Privy identity verification, tanda tangan digital, token runtime |
| `docs/verihubs.md` | Verihubs WhatsApp OTP, APP-ID, API-Key, template OTP |

## Ringkasan Lokasi Secret

| Lokasi | Isi | Catatan |
| --- | --- | --- |
| `.env` | Konfigurasi local/developer | Diabaikan Git |
| `.env.production` | Konfigurasi production | Diabaikan Git |
| `.env.stagging` | Konfigurasi staging | Ejaan repo memakai `stagging`; diabaikan Git |
| `.env.example` | Template nama variabel env | Aman dibagikan jika tanpa secret asli |
| `config/services.php` | Mapping service pihak ketiga ke env Laravel | FCM, reCAPTCHA, Buylink, Verihubs, mail, SES/AWS, Slack |
| `config/koperasi.php` | Mapping credential Koperasi API | Dipakai salary financing |
| `storage/app/fcm/*.json` | Service account Firebase | Sangat sensitif; jangan commit |
| Tabel `privies` | Credential dan token runtime Privy | Sumber aktif Privy saat ini |
| `app/Helpers/helpers.php` | Helper umum integrasi pihak ketiga | Cek jika ada credential lama/hardcoded |

## Variabel Env Penting

### Aplikasi dan Enkripsi

```text
APP_KEY
JWT_SECRET
KEYDECRYPTION
SALTDECRYPTION
```

Pemakaian umum:

```text
config/app.php
config/auth.php
app/Helpers/helpers.php
```

### Database, Cache, Queue

```text
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
REDIS_HOST
REDIS_USERNAME
REDIS_PASSWORD
REDIS_PORT
MEMCACHED_USERNAME
MEMCACHED_PASSWORD
```

Pemakaian:

```text
config/database.php
config/cache.php
config/queue.php
```

### Mail / SMTP / Postmark

```text
MAIL_MAILER
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_ENCRYPTION
MAIL_FROM_ADDRESS
MAIL_FROM_NAME
POSTMARK_TOKEN
```

Pemakaian:

```text
config/mail.php
config/services.php
app/Mail/
```

### AWS / S3 / SES / SQS

```text
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION
AWS_BUCKET
AWS_URL
AWS_ENDPOINT
AWS_USE_PATH_STYLE_ENDPOINT
SQS_PREFIX
SQS_QUEUE
SQS_SUFFIX
```

Pemakaian:

```text
config/filesystems.php
config/services.php
config/queue.php
config/cache.php
```

### Firebase Cloud Messaging

```text
FCM_PROJECT_ID
FCM_PROJECT_ID_PROD
storage/app/fcm/fcmDevelopment.json
storage/app/fcm/fcmProduction.json
```

Detail: lihat `docs/fcm.md`.

### reCAPTCHA / CAPTCHA

```text
SECRET_G_CAPTCHA
GEETEST_KEY
SITE_KEY
H_CAPTCHA_SECRET
```

Detail: lihat `docs/g-captcha.md`.

### Buylink / PPOB

```text
BUYLINK_BASE_URL
BUYLINK_USER_TOKEN
BUYLINK_LEADER_TOKEN
```

Detail: lihat `docs/buylink.md`.

### Verihubs WhatsApp OTP

```text
VERIHUBS_APP_ID
VERIHUBS_API_KEY
VERIHUBS_BASE_URL
VERIHUBS_OTP_TEMPLATE
VERIHUBS_LANG_CODE
VERIHUBS_TIME_LIMIT
```

Detail: lihat `docs/verihubs.md`.

### Koperasi API

```text
KOPERASI_API_URL
KOPERASI_API_IDENTITY
KOPERASI_API_PASSWORD
```

Pemakaian:

```text
config/koperasi.php
app/Http/Controllers/Admin/SalaryFinancingController.php
```

### Privy

Privy saat ini aktif memakai database, bukan `.env`.

```text
tabel: privies
field: url, username, password, token, expired
model: App\Models\privy
```

`PRIVY_URL` di `.env*` ada, tetapi belum menjadi sumber aktif selama kode memakai `privy::first()->url`.

Detail: lihat `docs/privy.md`.

## Kredensial Yang Perlu Diwaspadai

| Area | Lokasi | Catatan |
| --- | --- | --- |
| Buylink | `PpobController`, `HistoryController`, `config/services.php` | Pastikan token dibaca dari env/config |
| Verihubs | `app/Helpers/helpers.php`, `config/services.php` | Pastikan APP-ID/API-Key tidak hardcoded |
| Privy | Tabel `privies`, helper/controller Privy | Jangan log token, username, password |
| FCM | `storage/app/fcm/*.json` | Service account JSON adalah secret |
| reCAPTCHA | `DashboardController`, `config/services.php` | Sebaiknya pakai config, bukan `env()` langsung |

## Perintah Cepat Audit Secret

Cari pola credential:

```powershell
rg -n "API-Key|Authorization|Bearer|SECRET|TOKEN|PASSWORD|CLIENT_ID|CLIENT_SECRET|APP-ID|withBasicAuth|env\(" app config routes resources database
```

Cari pemakaian `env()`:

```powershell
rg -n "env\(" app config routes resources database
```

Cari file env:

```powershell
Get-ChildItem -Force .env*
```

## Konvensi Tampilan Data

- Field teks di UI ditampilkan huruf besar, kecuali email.
- Email tetap bentuk asli/lowercase agar mudah dibaca dan tidak mengubah makna.
- Rupiah memakai format `Rp. 1.000.000.000`.
- Angka biasa memakai format `1.000.000.000`.
- Jangan tampilkan desimal kecuali kebutuhan bisnis secara eksplisit meminta desimal.

## Cara Kerja Perubahan Iteratif

- Perlakukan permintaan terbaru user sebagai arahan aktif.
- Jika user meminta revisi berulang pada fitur yang sama, lanjutkan dari kondisi terakhir.
- Jangan mengembalikan ke versi lama kecuali user eksplisit meminta revert.
- Untuk revisi UI bertahap, pertahankan bagian yang tidak disentuh permintaan terbaru.
- Jika instruksi ambigu, pilih perubahan paling kecil dan mudah direvisi.
- Setelah menyelesaikan setiap perubahan yang diminta, segera commit perubahan tersebut agar tidak menumpuk dengan perubahan berikutnya.
- Commit hanya file yang terkait dengan perubahan yang baru diselesaikan; jangan ikut commit perubahan pengguna lain atau file yang berisi secret.

## Aturan Migrasi Database

- Jangan membuat migrasi baru kecuali user eksplisit meminta migrasi, perubahan struktur database, tabel, kolom, index, atau foreign key.
- Jika user hanya meminta perubahan model, controller, request, resource, view, route, atau validasi, ubah layer tersebut saja.
- Jika perubahan membutuhkan kolom/tabel yang belum ada, jelaskan kebutuhan migrasinya dan tunggu konfirmasi.
- Jika user memberi dump SQL, pakai hanya sebagai referensi kecuali user meminta migrasi.

## Catatan Keamanan

- Jangan commit `.env`, `.env.production`, atau `.env.stagging`.
- Jangan menaruh token baru langsung di controller/helper.
- Jangan menampilkan secret di log, screenshot, issue, chat, atau commit message.
- Jika menemukan token hardcoded aktif, pindahkan ke env/config lalu rotate di dashboard provider.
- Setelah mengubah env di Laravel, jalankan `php artisan config:clear` atau rebuild config cache.
- Jika membagikan daftar kebutuhan env ke tim, perbarui `.env.example` dengan nama variabel saja tanpa nilai secret asli.

## Aturan Branch dan Commit

- Setiap pekerjaan fitur, perubahan perilaku, perbaikan bug, refactor, atau perubahan konfigurasi harus dimulai dengan membuat branch baru dari branch dasar yang sedang ditentukan. Jangan mengerjakan perubahan langsung di `main`, `master`, atau branch fitur lain yang sudah ada.
- Gunakan nama branch yang singkat dan deskriptif dengan pola seperti `feature/nama-fitur`, `fix/nama-bug`, `refactor/nama-area`, atau `chore/nama-pekerjaan`.
- Sebelum membuat branch, periksa status dan branch aktif menggunakan `git status` serta pastikan perubahan milik pengguna tidak ikut terbawa atau tertimpa. Jangan menghapus, mereset, atau meng-commit perubahan yang tidak terkait tanpa instruksi eksplisit.
- Commit setiap proses atau unit kerja yang bermakna setelah perubahan tersebut selesai dan diverifikasi. Hindari menumpuk seluruh pekerjaan hingga akhir dalam satu commit besar.
- Gunakan pesan commit yang jelas dan konsisten, misalnya `feat: tambah fitur`, `fix: perbaiki validasi`, atau `test: tambah pengujian`.
- Sebelum setiap commit, tinjau `git diff` dan `git status`, jalankan validasi atau test yang relevan, lalu commit hanya file yang terkait dengan unit kerja tersebut.
- Setelah pekerjaan selesai, pastikan seluruh perubahan fitur tercatat dalam commit di branch baru. Jangan melakukan merge, push, atau menghapus branch tanpa instruksi pengguna.
