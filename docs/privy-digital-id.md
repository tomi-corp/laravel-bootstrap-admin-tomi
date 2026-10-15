# Rencana Integrasi Privy Digital ID

Status: rencana implementasi, belum diterapkan.

Branch: `feature/privy-digital-id-integration`, dibuat dari `local`.

## Acuan dan keputusan

Panduan teknis: `Privy_Digital_ID_v2_API_Documentation_v1.5.pdf`, versi dokumen 1.5, terbit 5 Oktober 2026, API versi 1.0.0. Dokumen sumber tidak disalin ke repo. Ketentuan teknis panduan menjadi referensi kontrak API; cakupan pekerjaan mengikuti keputusan proyek di bawah ini.

Integrasi lama dijelaskan pada [privy.md](privy.md).

Keputusan yang sudah disepakati:

- Digital ID digunakan untuk verifikasi identitas, terpisah dari tanda tangan digital lama.
- Tahap pertama tersedia dari profil aplikasi mobile. Pekerjaan repo ini mencakup backend Laravel dan kontrak integrasi bagi tim mobile, bukan kode aplikasi mobile.
- Gunakan paket Identity untuk mencocokkan NIK dan tanggal lahir dengan profil TOMI. Hasil Privy tidak menimpa profil secara otomatis.
- Migrasi tabel sesi khusus diperbolehkan untuk kepemilikan sesi, pemulihan, dan pencegahan pemrosesan ganda.
- Aktivasi dilakukan bertahap. Pengguna lama tidak diwajibkan verifikasi ulang.
- Simpan bukti minimum; jangan menyimpan JWT, exchange code, foto, URL foto, atau salinan lengkap identitas.
- Tahap ini tidak mengubah syarat pengajuan pinjaman/gawai, alur tanda tangan, maupun biaya PSE lama.

## Perbedaan dengan integrasi lama

Integrasi lama memakai OAuth, signature request, dan konfigurasi tabel `privies`. Digital ID memakai API key, sesi widget, dan hasil berupa JWT bertanda tangan Ed25519.

Digital ID tidak menyediakan `enterprise_token_privy` yang digunakan oleh alur tanda tangan lama. Karena itu, hasil Digital ID tidak boleh langsung mengubah `verified_privy`, `id_privy`, `enterprise_token_privy`, token registrasi lama, atau menambahkan biaya sertifikat pada `privy_pse`.

Perbaikan integrasi lama, termasuk pengecekan status dan callback registrasinya, menjadi pekerjaan terpisah. Endpoint Digital ID harus memiliki controller dan layanan sendiri agar kontrak lama tetap kompatibel.

## Alur verifikasi

1. Pengguna login dan memilih verifikasi Digital ID dari profil mobile.
2. Backend memeriksa aktivasi fitur, kelengkapan NIK/tanggal lahir, serta sesi aktif milik pengguna.
3. Backend melanjutkan sesi yang masih berlaku atau membuat sesi melalui Privy.
4. Mobile membuka `session_url` dalam WebView. Privy menjalankan identifikasi, liveness, pencocokan wajah, OTP, dan consent.
5. Saat menerima `privy:success`, mobile segera mengirim exchange code ke backend.
6. Backend menukar kode, memverifikasi JWT, mendekripsi identitas, lalu mencocokkan NIK dan tanggal lahir.
7. Callback Privy dapat mengirim hasil yang sama secara independen. Kedua jalur memakai pemroses yang sama dan dideduplicasi berdasarkan `session_id`.
8. Profil menampilkan status hasil backend. Event mobile saja tidak membuktikan keberhasilan verifikasi.

## Konfigurasi dan client API

Konfigurasi Digital ID dipisahkan dari tabel credential Privy lama. Nama variabel yang direncanakan:

```text
PRIVY_DIGITAL_ID_ENABLED
PRIVY_DIGITAL_ID_API_URL
PRIVY_DIGITAL_ID_WIDGET_ORIGIN
PRIVY_DIGITAL_ID_MERCHANT_ID
PRIVY_DIGITAL_ID_API_KEY
PRIVY_DIGITAL_ID_PRIVATE_KEY_PATH
PRIVY_DIGITAL_ID_IDENTITY_HMAC_KEY
```

Tambahkan nama variabel tanpa secret asli pada `.env.example`. Private key dan HMAC key disimpan sebagai secret backend; file private key berada di luar direktori publik dan tidak masuk Git. HMAC key harus stabil selama bukti pencocokan masih digunakan; rotasinya memerlukan penanganan invalidasi bukti lama.

| Lingkungan | API | Origin widget |
| --- | --- | --- |
| Sandbox | `https://stg-api-digitalid.privy.id` | `https://stg-digitalid.privy.id` |
| Production | `https://api-digitalid.privy.id` | `https://digitalid.privy.id` |

| Operasi Privy | Request |
| --- | --- |
| Membuat sesi | `POST /v1/sessions`, tanpa body, header `X-API-Key` |
| Menukar kode | `POST /v1/sessions/exchange`, JSON `{"code":"..."}`, header `X-API-Key` |
| Mengambil kunci publik | `GET /.well-known/jwks.json`, tanpa API key |
| Pemeriksaan layanan | `GET /healthz`, HTTP 200 menandakan sehat; body tidak dijadikan kontrak |

Client wajib memakai HTTPS, verifikasi TLS, dan timeout. Jangan mengulang exchange otomatis karena kode hanya dapat dipakai sekali. Kegagalan create-session dapat dicoba kembali sebagai permintaan baru; Privy tidak menjamin idempotency POST.

## Penyimpanan dan konsistensi

Rencanakan tabel `privy_digital_id_sessions` dengan data berikut:

- Relasi user, environment, merchant ID, dan `session_id` unik.
- `session_url` terenkripsi, `expires_at`, serta timestamp standar.
- Status pemrosesan lokal, kode hasil, channel penerimaan, dan `verified_at`.
- PrivyID terenkripsi serta fingerprint HMAC NIK dan tanggal lahir yang berhasil dicocokkan.

Jangan menyimpan payload JWT atau identitas lengkap sebagai kolom audit. Model tidak boleh menampilkan atribut sensitif melalui serialisasi umum.

Gunakan penguncian per user saat membuat sesi untuk menghindari beberapa sesi aktif akibat request bersamaan. Pakai transaksi dan row lock ketika menerima hasil untuk memastikan callback dan exchange hanya menyelesaikan satu sesi sekali.

Simpan URL sesi hanya selama diperlukan untuk resume; hapus setelah sesi selesai atau kedaluwarsa. Jadwalkan pembersihan URL sesi yang expired meskipun pengguna tidak kembali membuka aplikasi.

Sesi yang kedaluwarsa tidak boleh dilanjutkan pada widget. Expiry lokal bukan bukti bahwa tidak pernah ada hasil valid: hasil yang datang belakangan tetap harus melewati validasi JWT, kepemilikan, dan deduplikasi sebelum diputuskan.

## Validasi JWT dan pencocokan identitas

Kedua jalur hasil menggunakan satu layanan validasi dan penyimpanan:

1. Pastikan event hasil adalah `identity.verified`.
2. Batasi algoritma JWT ke `EdDSA`, dengan kunci `OKP`/`Ed25519` sesuai `kid` dari JWKS milik endpoint yang dikonfigurasi server.
3. Verifikasi signature, issuer `https://digitalid.privy.id/`, audience merchant ID, dan masa berlaku token. Jangan menentukan issuer hanya dari hostname sandbox.
4. Cocokkan session ID pada JWT, envelope, dan sesi yang tersimpan. Untuk exchange, sesi juga harus dimiliki pengguna login.
5. Setelah JWT valid, dekripsi `data.my_identity` memakai private key RSA-4096 dengan padding PKCS#1 v1.5 melalui OpenSSL.
6. Parse hasil JSON secara ketat. NIK harus berupa string 16 digit; tanggal lahir dibandingkan sebagai `YYYY-MM-DD`.
7. Cocokkan dengan profil TOMI terkini di dalam transaksi penyelesaian. Jangan mengesahkan berdasarkan profil lama yang berubah saat widget masih berjalan.

Hasil lokal:

| Status | Makna |
| --- | --- |
| `pending` | Belum ada hasil backend yang disahkan |
| `verified` | JWT valid, identitas berhasil didekripsi, NIK dan tanggal lahir cocok |
| `identity_mismatch` | Identitas Privy berbeda dari profil TOMI |
| `identity_incomplete` | Hasil tidak memuat field identitas wajib untuk pencocokan |
| `expired` | Sesi tidak dapat dilanjutkan berdasarkan expiry yang tersimpan |

JWT tidak valid tidak boleh mengubah status menjadi verified. Error dari event mobile hanya dipakai untuk UI; jangan menjadikannya otoritas identitas atau menimpa hasil verified.

Jika NIK atau tanggal lahir profil berubah setelah verifikasi, fingerprint lama tidak lagi memenuhi pencocokan. Ringkasan profil harus menunjukkan kebutuhan verifikasi ulang.

Cache JWKS selama satu jam dan refresh sekali jika `kid` tidak dikenal atau signature gagal. Tolak hasil jika verifikasi ulang tetap gagal. Jangan mengikuti URL kunci yang diberikan oleh header token.

Gunakan `firebase/php-jwt` yang sudah tersedia dalam lockfile dan nyatakan sebagai dependency langsung. Wajibkan ekstensi Sodium untuk Ed25519 dan OpenSSL untuk RSA. Referensi library: <https://github.com/googleapis/php-jwt>.

## Kontrak API TOMI

Prefix endpoint mengikuti proyek: `/v1/api`.

| Method dan path | Fungsi |
| --- | --- |
| `POST /privy/digital-id/sessions` | Membuat atau melanjutkan sesi milik pengguna login |
| `POST /privy/digital-id/sessions/{session_id}/exchange` | Menerima `code`, memeriksa kepemilikan sesi, menukar kode, dan memproses JWT |
| `GET /privy/digital-id/sessions/{session_id}` | Membaca status lokal milik pengguna login |
| `POST /privy/digital-id/callback` | Menerima hasil dari Privy tanpa autentikasi user; keaslian hasil dibuktikan oleh JWT |

Respons create/resume memuat `session_id`, `session_url`, `expires_at`, dan `status`. URL hanya diberikan kepada pemilik sesi untuk membuka widget. Semua respons sesi menggunakan `Cache-Control: no-store`.

Respons status tidak memuat URL sesi, JWT, exchange code, identitas, atau credential. Gunakan respons API yang konsisten dengan konvensi proyek dan kode error yang dapat dibaca mobile.

Endpoint pengguna memakai autentikasi API TOMI, pemeriksaan kepemilikan, dan rate limit. Callback ditempatkan pada middleware API tanpa CSRF web, dengan validasi bentuk request dan batas ukuran payload.

Callback memverifikasi JWT dan menyimpan hasil minimum sebelum mengembalikan HTTP 200. Target waktu respons di bawah 5 detik; kontrak Privy membatasi waktu respons maksimum 60 detik. Pekerjaan tambahan yang lambat harus dijalankan sesudahnya melalui queue. Pengiriman ulang hasil valid yang sudah diproses tetap memperoleh HTTP 200 tanpa efek bisnis tambahan.

Tambahkan ringkasan `digital_id` pada respons profil: `status`, `verified_at`, dan `can_start`. Pertahankan field Privy lama untuk kompatibilitas aplikasi yang belum diperbarui.

## Kontrak WebView mobile

- Buka `session_url` langsung dalam WebView; validasi origin URL terhadap konfigurasi backend sebelum diberikan ke aplikasi.
- Android menyediakan interface `PrivyAndroid.postMessage(json)`; iOS menyediakan shim `ReactNativeWebView.postMessage(json)`.
- Batasi navigasi dan asal pesan bridge ke origin widget yang diizinkan. Cocokkan session ID event jika tersedia.
- Tangani `privy:ready`, `privy:success`, `privy:cancel`, dan `privy:error`. Nilai reason dan verification method diperlakukan sebagai opaque.
- Pada success, kirim exchange code segera ke backend. Jangan memakai token atau `sections` preview untuk keputusan bisnis dan jangan mencatat payload bridge mentah.
- Siapkan izin kamera Android/iOS dan HTTPS. Kamera liveness hanya membutuhkan video, bukan mikrofon.
- Tangani izin kamera ditolak, penolakan permanen dengan tautan pengaturan aplikasi, pembatalan consent, aplikasi ditutup, session expired, serta session blocked.
- Jika aplikasi dibuka kembali, minta resume dari backend; gunakan sesi yang belum expired. Kelanjutan langkah persis di dalam widget tidak dijamin.
- Jika exchange timeout, baca status lokal terlebih dahulu. Jangan mengulang exchange code otomatis. Jika belum ada hasil valid, tawarkan memeriksa kembali atau memulai sesi baru secara eksplisit.
- Setelah session blocked, arahkan pengguna ke bantuan; jangan membuat sesi baru otomatis karena aturan retry harus mengikuti kebijakan Privy.
- Entry point mobile mengikuti pedoman tombol Privy pada dokumen sumber; asset resmi dan pengaturan widget disiapkan bersama tim mobile/Privy.

## Batasan dan pemulihan

- Tidak ada endpoint publik Privy untuk mencari status berdasarkan `session_id`. Endpoint status TOMI hanya membaca hasil lokal.
- Gunakan `expires_at` dari respons create-session, bukan durasi sesi tetap.
- Exchange code sekali pakai dan berumur pendek; jangan mengandalkan kode dapat diterbitkan ulang.
- Callback dapat hilang, berulang, atau datang dengan urutan berbeda. Jangan mengandalkan retry atau exactly-once delivery dari provider.
- Untuk HTTP 429, kurangi frekuensi request; jangan mengarang batas numerik provider.
- Untuk HTTP 410, mulai sesi baru atas tindakan pengguna. Untuk HTTP 423, ikuti jalur bantuan/kebijakan Privy.
- Jika hasil tidak diterima dari callback maupun exchange, jangan menandai verified. Pengguna perlu memulai sesi baru atau menghubungi bantuan.

## Aktivasi dan operasional

1. Siapkan paket Identity dengan NIK dan tanggal lahir, merchant ID, API key per environment, private key, callback, dan konfigurasi native host bersama Privy. Paket serta item data ditentukan saat onboarding, bukan saat membuat sesi.
2. Pastikan konfigurasi consent, kanal OTP, syarat penggunaan, dan kebijakan privasi sesuai kebutuhan TOMI.
3. Implementasikan migrasi, layanan, endpoint, pengujian, dan kontrak mobile. Fitur default nonaktif.
4. Uji sandbox dengan akun internal pada Android dan iOS.
5. Deploy backend dan migrasi melalui prosedur proyek; pasang konfigurasi secret lalu clear/rebuild config cache Laravel.
6. Rilis aplikasi mobile yang mendukung kontrak baru, kemudian aktifkan fitur bertahap per lingkungan/rilis aplikasi.
7. Pantau jumlah sesi, tingkat keberhasilan, mismatch, kegagalan JWT/dekripsi, kegagalan exchange, serta durasi callback.

Rollback menonaktifkan pembuatan sesi baru. Callback dan penyelesaian sesi yang sudah diterbitkan tetap tersedia agar hasil tidak hilang. Jangan menonaktifkan seluruh route callback melalui enable flag pembuatan sesi.

Log hanya metadata operasional seperti session ID, kode hasil, channel, dan durasi. Jangan log API key, private key, HMAC key, session URL, exchange code, JWT, NIK, tanggal lahir, foto, maupun payload request/response mentah. Terapkan redaksi juga pada exception, HTTP tracing, dan monitoring.

## Rencana pengujian

- Unit: signature valid/palsu, token expired, issuer/audience salah, algoritma salah, kid tidak dikenal, refresh JWKS, JWKS gagal, dekripsi gagal, dan payload JSON tidak valid.
- Pencocokan: identitas cocok, NIK berbeda, tanggal lahir berbeda, field wajib hilang, profil berubah saat sesi berlangsung, serta profil berubah setelah verified.
- Feature: autentikasi, isolasi antar-user, create/resume, expiry, request bersamaan, callback tanpa sesi, envelope/JWT/session ID berbeda, callback dan exchange bersamaan, serta duplicate delivery.
- Pemulihan: create timeout, exchange timeout, kode sudah dipakai, 404, 410, 423, dan 429 melalui HTTP fake tanpa memanggil provider.
- Keamanan data: credential dan payload sensitif tidak muncul dalam response profil, serialisasi model, log, atau error; URL sesi dibersihkan setelah selesai/expired.
- Regresi: profil lama, pengajuan pinjaman/gawai, tanda tangan, dan biaya PSE tetap berjalan seperti sebelumnya.
- Sandbox mobile: kamera Android/iOS, izin ditolak, aplikasi ditutup/dibuka, consent ditolak, sesi expired/blocked, verifikasi berhasil, serta callback sebelum/sesudah exchange.

## Kriteria selesai

- Backend hanya mengesahkan identitas dari JWT valid dengan NIK dan tanggal lahir yang cocok dengan profil TOMI terkini.
- Sesi hanya dapat dibuat, dilanjutkan, ditukar, dan dibaca oleh pemiliknya; callback memakai validasi JWT dan korelasi sesi.
- Hasil dari dua channel diproses sekali tanpa biaya atau perubahan status ganda.
- Bukti minimum tersimpan secara aman dan tidak menimpa profil maupun status tanda tangan lama.
- Pengujian backend lulus dan alur sandbox Android/iOS terverifikasi sebelum aktivasi produksi.
- Kontrak API, konfigurasi, penanganan error, dan panduan WebView tersedia untuk tim mobile.

Pembuatan dokumen ini belum menjalankan migrasi, mengubah aplikasi, menghubungi API Privy, atau mengaktifkan fitur.
