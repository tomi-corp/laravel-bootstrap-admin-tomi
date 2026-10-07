# Firebase Cloud Messaging (FCM)

Dokumen ini merangkum pemakaian Firebase Cloud Messaging di aplikasi TOMI. Jangan menulis isi service account JSON, private key, access token, atau credential Firebase di dokumentasi, commit message, issue, chat, atau screenshot.

## Ringkasan

FCM dipakai untuk mengirim push notification ke device user atau topic broadcast.

| Area | Keterangan |
| --- | --- |
| Fungsi utama | Push notification |
| Helper utama | `app/Helpers/fcmHelper.php` |
| Fungsi utama | `sendNotification($token, $title, $body)` |
| Mode API | FCM HTTP v1 |
| Credential utama | Service account JSON di `storage/app/fcm/` |

Contoh notifikasi:

- Pengajuan pinjaman.
- Reminder pinjaman pending.
- Reminder pinjaman payroll.
- Broadcast/news dari admin.

## Lokasi Kode

| File | Fungsi |
| --- | --- |
| `app/Helpers/fcmHelper.php` | Mengirim push notification |
| `config/services.php` | Mapping project ID |
| `app/Console/Commands/ReminderPinjamanYangPending.php` | Reminder pinjaman pending |
| `app/Console/Commands/HapusPinjamanPendingCron.php` | Notifikasi/cron pinjaman pending |
| `app/Console/Commands/ReminderPinjamanPayrollCron.php` | Reminder payroll |
| `app/Http/Controllers/Admin/NotificationController.php` | Broadcast/news admin |

## Konfigurasi

Variabel environment:

```text
FCM_PROJECT_ID
FCM_PROJECT_ID_PROD
```

Mapping Laravel:

```php
'fcm' => [
    'project_id' => env('FCM_PROJECT_ID', null),
    'project_id_production' => env('FCM_PROJECT_ID_PROD', null),
],
```

File service account:

```text
storage/app/fcm/fcmDevelopment.json
storage/app/fcm/fcmProduction.json
```

Catatan:

- `.env` hanya menyimpan project ID.
- Credential OAuth Firebase dibaca dari file JSON service account.
- File JSON berisi private key dan client email, sehingga harus diperlakukan seperti password.

## Alur Pengiriman

1. Kode memanggil `sendNotification($token, $title, $body)`.
2. Aplikasi memilih project ID dan file credential berdasarkan environment.
3. Google Client membaca service account JSON.
4. Aplikasi meminta access token dengan scope Firebase Messaging.
5. Aplikasi membuat payload notifikasi.
6. Aplikasi mengirim payload ke FCM HTTP v1.
7. FCM meneruskan notifikasi ke device atau topic.

## Target Notifikasi

| Target | Cara kirim |
| --- | --- |
| Device user | `message.token = token_firebase user` |
| Topic news | `message.topic = news` |

Field token device user:

```text
users.token_firebase
```

Jika parameter `$token` bernilai `news`, notifikasi dikirim ke topic `news`.

## Endpoint

Scope OAuth:

```text
https://www.googleapis.com/auth/firebase.messaging
```

Endpoint FCM HTTP v1:

```text
POST https://fcm.googleapis.com/v1/projects/{projectId}/messages:send
```

`{projectId}` diambil dari `FCM_PROJECT_ID` atau `FCM_PROJECT_ID_PROD`.

## Environment

Kode saat ini membedakan FCM berdasarkan `App::environment()`.

| Environment | Project ID | File credential |
| --- | --- | --- |
| `local` | `FCM_PROJECT_ID` | `storage/app/fcm/fcmDevelopment.json` |
| `production` | `FCM_PROJECT_ID_PROD` | `storage/app/fcm/fcmProduction.json` |
| `staging` | Belum eksplisit | Belum eksplisit |

Catatan: jika `APP_ENV=staging`, kode saat ini belum punya cabang khusus staging.

## Risiko

- `FCM_PROJECT_ID` kosong membuat aplikasi tidak bisa menentukan project Firebase.
- File JSON tidak ada akan menghasilkan pesan credential tidak ditemukan.
- Private key salah atau expired dapat membuat access token gagal dibuat.
- Environment `staging` perlu penanganan khusus jika memakai project Firebase terpisah.

## Rekomendasi

- Tambahkan konfigurasi staging jika staging memakai Firebase sendiri.
- Contoh variabel:

```text
FCM_PROJECT_ID_STAGING
storage/app/fcm/fcmStaging.json
```

- Pertimbangkan mapping yang lebih eksplisit di `config/services.php` agar pemilihan credential tidak hardcoded.

## Catatan Keamanan

Jangan menyalin isi service account JSON, private key, access token, atau credential Firebase ke dokumentasi, chat, issue, screenshot, commit message, atau log publik.
