# Environment TOMI V2

Dokumen ini menjelaskan variabel environment yang digunakan TOMI V2. Gunakan
`.env.example` sebagai template yang sama untuk lokal, staging, dan produksi.
Salin ke `.env`, lalu isi nilai sesuai server. File `.env`, `.env.production`,
dan `.env.stagging` tidak boleh di-commit karena dapat berisi credential.

## Aturan pengisian

- `APP_KEY`, `JWT_SECRET`, token, password, private key, dan API key wajib diisi
  dari secret manager atau konfigurasi server; jangan menyalin secret antar lingkungan.
- `APP_DEBUG=true` hanya untuk lokal. Gunakan `false` untuk staging dan produksi.
- `APP_ENV` membedakan konfigurasi aplikasi (`local`, `staging`, atau `production`).
- URL, database, bucket, callback, dan credential harus menunjuk ke lingkungan yang sama.
- Nama file lama `.env.stagging` dipertahankan demi kompatibilitas deployment. Untuk
  penamaan baru gunakan `staging`, tetapi jangan mengganti nama file deployment tanpa
  memperbarui pipeline terkait.
- Struktur key pada `.env`, `.env.example`, `.env.production`, dan `.env.stagging`
  harus sama. Jika menambah variabel baru, tambahkan juga ke empat file tersebut.
- Repository saat ini belum memiliki file `.env.staging`; file staging yang aktif
  adalah `.env.stagging`. Jangan membuat salinan baru tanpa memperbarui deployment.

## Kamus variabel

| Kelompok | Variabel | Keterangan |
|---|---|---|
| Aplikasi | `APP_NAME` | Nama aplikasi yang tampil di Laravel dan frontend. |
| Aplikasi | `APP_ENV` | Nama lingkungan aplikasi. |
| Aplikasi | `APP_KEY` | Kunci enkripsi Laravel; buat dengan `php artisan key:generate`. |
| Aplikasi | `APP_DEBUG` | Mengaktifkan detail error; hanya `true` di lokal. |
| Aplikasi | `APP_TIMEZONE` | Zona waktu bisnis aplikasi, misalnya `Asia/Jakarta`. |
| Aplikasi | `APP_URL` | URL dasar aplikasi untuk link, email, dan asset. |
| Aplikasi | `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE` | Bahasa utama, fallback, dan Faker. |
| Aplikasi | `APP_PREVIOUS_KEYS` | Daftar key Laravel lama saat rotasi key. |
| Aplikasi | `APP_MAINTENANCE_DRIVER`, `APP_MAINTENANCE_STORE` | Penyimpanan status maintenance mode. |
| Keamanan | `BCRYPT_ROUNDS` | Cost hashing password BCrypt. |
| Logging | `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL` | Driver, channel gabungan, dan level log. |
| Logging | `LOG_DEPRECATIONS_CHANNEL`, `LOG_DEPRECATIONS_TRACE`, `LOG_DAILY_DAYS` | Pengaturan log deprecation dan retensi log harian. |
| Logging | `LOG_SLACK_WEBHOOK_URL`, `LOG_SLACK_USERNAME`, `LOG_SLACK_EMOJI` | Tujuan log Slack, jika digunakan. |
| Logging | `PAPERTRAIL_URL`, `PAPERTRAIL_PORT`, `LOG_STDERR_FORMATTER` | Pengaturan logging eksternal/format stderr opsional. |
| Database | `DB_CONNECTION`, `DB_URL` | Driver atau URL koneksi database utama. |
| Database | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Endpoint dan credential database utama. |
| Database | `DB_SOCKET`, `DB_CHARSET`, `DB_COLLATION`, `DB_FOREIGN_KEYS`, `MYSQL_ATTR_SSL_CA` | Detail koneksi dan keamanan MySQL. |
| Database | `DB_SECOND_HOST`, `DB_SECOND_PORT`, `DB_SECOND_DATABASE`, `DB_SECOND_USERNAME`, `DB_SECOND_PASSWORD` | Credential database kedua bila integrasi membutuhkannya. |
| Session | `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT` | Driver, masa berlaku (menit), dan enkripsi session. |
| Session | `SESSION_PATH`, `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY`, `SESSION_SAME_SITE`, `SESSION_PARTITIONED_COOKIE` | Atribut keamanan cookie session. |
| Session | `SESSION_EXPIRE_ON_CLOSE`, `SESSION_CONNECTION`, `SESSION_TABLE`, `SESSION_STORE` | Perilaku dan penyimpanan session database/cache. |
| Queue | `QUEUE_CONNECTION` | Driver antrean (`database`, Redis, SQS, dan lainnya). |
| Queue | `DB_QUEUE_*`, `BEANSTALKD_QUEUE*`, `REDIS_QUEUE*` | Detail driver queue database, Beanstalkd, atau Redis. |
| Cache | `CACHE_STORE`, `CACHE_PREFIX`, `DB_CACHE_*`, `DYNAMODB_CACHE_*` | Driver, prefix, dan tabel cache. |
| Redis/Memcached | `REDIS_*`, `MEMCACHED_*` | Endpoint, credential, database, dan koneksi Redis/Memcached. |
| Mail | `MAIL_MAILER`, `MAIL_URL`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION` | Transport dan credential email. |
| Mail | `MAIL_EHLO_DOMAIN`, `MAIL_SENDMAIL_PATH`, `MAIL_LOG_CHANNEL`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Detail SMTP/sendmail dan identitas pengirim. |
| Mail | `POSTMARK_TOKEN`, `POSTMARK_MESSAGE_STREAM_ID` | Credential dan stream Postmark bila digunakan. |
| Storage | `FILESYSTEM_DISK` | Disk default Laravel (`local`, `public`, atau `s3`). |
| Storage | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT` | Credential dan endpoint S3-compatible. |
| Storage | `SQS_PREFIX`, `SQS_QUEUE`, `SQS_SUFFIX` | Detail queue AWS SQS. |
| Auth/OTP | `AUTH_GUARD`, `AUTH_PASSWORD_BROKER`, `AUTH_MODEL`, `AUTH_PASSWORD_RESET_TOKEN_TABLE`, `AUTH_PASSWORD_TIMEOUT` | Default guard, model, reset password, dan timeout. |
| Auth/OTP | `JWT_SECRET` | Secret signing token JWT API. |
| Auth/OTP | `OTP_ENABLED`, `OTP_LIFETIME`, `OTP_KEEP_ALIVE`, `OTP_THROW_EXCEPTION`, `OTP_TOTP_ISSUER` | Perilaku Google 2FA/TOTP. |
| Enkripsi | `KEYDECRYPTION`, `SALTDECRYPTION` | Key dan salt untuk helper enkripsi aplikasi; harus rahasia. |
| PDF | `WKHTML_PDF_BINARY`, `WKHTML_IMG_BINARY` | Path binary wkhtmltopdf dan wkhtmltoimage. |
| Notifikasi | `FCM_SERVER_KEY`, `FCM_PROJECT_ID`, `FCM_SERVER_KEY_PROD`, `FCM_PROJECT_ID_PROD` | Credential Firebase Cloud Messaging per lingkungan. |
| Captcha | `SECRET_G_CAPTCHA` | Secret server Google reCAPTCHA. |
| Koperasi API | `KOPERASI_API_URL`, `KOPERASI_API_IDENTITY`, `KOPERASI_API_PASSWORD` | URL dan credential API internal Koperasi. |
| Verihubs | `VERIHUBS_APP_ID`, `VERIHUBS_API_KEY`, `VERIHUBS_BASE_URL` | Identitas dan endpoint Verihubs. |
| Verihubs | `VERIHUBS_OTP_TEMPLATE`, `VERIHUBS_LANG_CODE`, `VERIHUBS_TIME_LIMIT` | Template, bahasa, dan batas waktu OTP. |
| BuyLink | `BUYLINK_BASE_URL`, `BUYLINK_USER_TOKEN`, `BUYLINK_LEADER_TOKEN` | Endpoint dan token integrasi BuyLink. |
| Privy | `DIGITAL_IDV2_PRIVY_*` | Konfigurasi utama Privy Digital ID v2: environment, issuer, application, client, key, dan merchant. |
| Privy legacy | `PRIVY_DIGITAL_ID_*`, `PRIVY_URL`, `PRIVY_DIGITAL_ID_ENV`, `PRIVY_DIGITAL_ID_ISSUER` | Nama lama yang dibaca sebagai fallback; gunakan nama v2 untuk konfigurasi baru. |
| Privy Sign | `PRIVY_SIGN_USERNAME`, `PRIVY_SIGN_PASSWORD` | Username dan password/secret untuk autentikasi tanda tangan digital. |
| Privy Sign | `PRIVY_SIGN_API_KEY`, `PRIVY_SIGN_SECRET_KEY` | API key dan secret key dari penyedia tanda tangan digital. |
| Privy Sign | `PRIVY_SIGN_CHANNEL_ID` | ID channel yang dikirim pada request tanda tangan. |
| Privy Sign | `PRIVY_SIGN_DOCUMENT_OWNER`, `PRIVY_SIGN_ENTERPRISE_TOKEN` | ID document owner dan enterprise token pemilik dokumen. |
| Privy Sign | `PRIVY_SIGN_BASE_URL` | Base URL API tanda tangan digital. |
| Privy Sign legacy | `PRIVY_SIGN_OWNER_ID`, `PRIVY_SIGN_OWNER_ENTERPRISE_TOKEN` | Nama lama untuk owner; dipertahankan demi kompatibilitas konfigurasi sebelumnya. |
| Slack | `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` | Integrasi Slack opsional. |
| Frontend | `VITE_APP_NAME` | Nama aplikasi yang diekspos ke Vite/browser; jangan menaruh secret pada variabel `VITE_`. |

## Verifikasi setelah mengubah env

```bash
php artisan config:clear
php artisan config:cache
php artisan about
```

Pastikan `APP_ENV`, `APP_DEBUG`, `APP_URL`, koneksi database, mail, storage, dan
endpoint layanan pihak ketiga sesuai dengan lingkungan yang sedang dijalankan.

## Tanda tangan digital

Isi kelompok berikut pada environment yang mengaktifkan tanda tangan digital:

```dotenv
PRIVY_SIGN_USERNAME=
PRIVY_SIGN_PASSWORD=
PRIVY_SIGN_API_KEY=
PRIVY_SIGN_SECRET_KEY=
PRIVY_SIGN_CHANNEL_ID=
PRIVY_SIGN_DOCUMENT_OWNER=
PRIVY_SIGN_ENTERPRISE_TOKEN=
PRIVY_SIGN_BASE_URL=
```

Jangan menaruh nilai tersebut di frontend atau commit ke repository. Integrasi
legacy saat ini masih menggunakan data pada tabel `privies` untuk sebagian alur,
sehingga migrasi pemakaian env ke runtime perlu dilakukan terpisah dan diuji per
environment.
