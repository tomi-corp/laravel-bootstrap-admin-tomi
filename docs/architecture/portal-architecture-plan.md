# Rencana Arsitektur Portal TOMI

Status: Draft

## Tujuan

Membangun satu aplikasi TOMI yang dapat mendukung banyak jenis pengguna dan organisasi, tanpa membuat sistem autentikasi terpisah untuk setiap jenis portal.

Portal awal yang direncanakan:

- Internal / back-office
- Lender
- Perusahaan

Arsitektur harus dapat diperluas untuk tipe lain pada masa depan, misalnya investor, auditor, aggregator, atau jenis organisasi lain.

## Keputusan arsitektur

TOMI menggunakan pendekatan modular monolith dengan komponen berikut:

```text
Central Identity
        +
Organization / Workspace
        +
Membership
        +
Role & Permission
        +
Modular Business Domains
```

`lender` dan `perusahaan` diperlakukan sebagai tipe organisasi atau workspace, bukan sebagai sistem login yang sepenuhnya terpisah.

BTA tidak termasuk dalam arsitektur TOMI ini dan diabaikan dari rancangan portal baru.

## Alur login

```text
/login
  |
  v
Validasi user
  |
  +-- satu akses  ------> dashboard yang sesuai
  |
  +-- banyak akses -----> pilih workspace/portal
                              |
                              v
                       validasi membership
                              |
                              v
                       dashboard yang sesuai
```

Rute portal yang direncanakan:

```text
/backoffice
/lender
/perusahaan
```

Penentuan akses harus dilakukan di server berdasarkan membership dan permission. Pengguna tidak boleh mendapatkan akses hanya dengan mengganti URL atau memilih tipe portal dari sisi browser.

## Model domain yang disarankan

Secara konseptual, struktur data terdiri dari:

```text
users
organizations
organization_types
memberships
roles
permissions
role_permissions
membership_roles
```

### User

Mewakili identitas login seseorang. Satu user dapat memiliki akses ke beberapa organisasi atau workspace.

### Organization

Mewakili entitas yang menggunakan TOMI, misalnya lender atau perusahaan.

### Organization type

Menyimpan jenis organisasi. Contoh awal:

- `internal`
- `lender`
- `perusahaan`

Jenis baru sebaiknya dapat ditambahkan tanpa membuat ulang sistem autentikasi.

### Membership

Menghubungkan user dengan organization dan menentukan status aksesnya. Contoh:

```text
Budi
  - Admin TOMI
  - Member Lender A
  - Operator Perusahaan B
```

### Role dan permission

Role berlaku dalam konteks organization atau workspace tertentu. Permission digunakan untuk membatasi kemampuan secara lebih rinci daripada sekadar tipe organisasi.

## Pembagian modul aplikasi

Struktur konseptual yang disarankan:

```text
app/
├── Domain/
│   ├── Identity/
│   ├── Organization/
│   ├── AccessControl/
│   ├── Backoffice/
│   ├── Lender/
│   ├── Perusahaan/
│   └── Shared/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
└── Models/
```

Modul `Identity`, `Organization`, dan `AccessControl` menjadi fondasi bersama. Fitur bisnis khusus ditempatkan di modul domain masing-masing.

## Penamaan perusahaan

Istilah bisnis yang digunakan ke depan adalah `perusahaan`, bukan `partner`.

Target penamaan:

- URL: `/perusahaan`
- Label UI: `Perusahaan`
- Modul: `Perusahaan`
- Route name: `perusahaan.*`

Nama internal lama seperti guard `partner` atau tabel `partners` dapat dipertahankan sementara selama masa transisi untuk menghindari perubahan berisiko. Penggantian internal dilakukan setelah dependensi dan data lama dipetakan.

## Kondisi project saat ini

Komponen yang sudah tersedia:

- Back-office menggunakan prefix `/backoffice` dan guard `admin`.
- Area perusahaan saat ini menggunakan prefix `/partner` dan guard `partner`.
- Area lender belum menjadi portal login mandiri; sebagian fitur lender masih berada di area back-office.
- Konfigurasi autentikasi saat ini memiliki beberapa guard terpisah.

Kondisi tersebut menjadi baseline migrasi, bukan target akhir arsitektur.

## Tahapan implementasi

### Tahap 1 — Finalisasi domain dan akses

- Tentukan arti setiap organization type.
- Tentukan apakah satu email boleh memiliki beberapa membership.
- Tentukan role awal untuk internal, lender, dan perusahaan.
- Identifikasi data yang boleh dilihat lintas organisasi.

### Tahap 2 — Identity dan workspace

- Rancang sumber identitas bersama.
- Rancang membership user ke organization.
- Rancang pemilihan workspace setelah login.
- Tetapkan middleware untuk autentikasi, workspace, dan permission.

### Tahap 3 — Portal gateway

- Sediakan login terpusat di `/login`.
- Arahkan user ke portal berdasarkan aksesnya.
- Tampilkan pilihan workspace jika user memiliki lebih dari satu akses.
- Pertahankan redirect lama selama masa transisi bila diperlukan.

### Tahap 4 — Portal perusahaan

- Perkenalkan `/perusahaan` sebagai URL resmi.
- Ubah label UI dari Partner menjadi Perusahaan.
- Pertahankan kompatibilitas `/partner` untuk sementara.
- Pastikan data perusahaan dibatasi oleh membership perusahaan yang aktif.

### Tahap 5 — Portal lender

- Tetapkan model organization lender.
- Buat area `/lender`.
- Pindahkan fitur lender secara bertahap dari back-office sesuai kebutuhan bisnis.
- Pastikan lender hanya dapat melihat data miliknya sendiri.

### Tahap 6 — Penguatan keamanan dan pengujian

- Tambahkan rate limit dan proteksi brute-force.
- Terapkan two-factor authentication untuk area berisiko tinggi.
- Tambahkan audit log untuk aktivitas penting.
- Uji isolasi data antar-organization.
- Uji akses lintas portal, logout, session, dan redirect.

## Prinsip keamanan

- Jangan mempercayai tipe portal dari input client.
- Semua data bisnis harus memiliki pembatasan organization/workspace.
- Role harus diperiksa server-side pada setiap operasi sensitif.
- Jangan membuat guard baru hanya karena ada organization type baru.
- Hindari menampilkan informasi yang membocorkan apakah akun tertentu terdaftar.
- Jangan mencatat password, token, atau credential ke log.

## Kriteria keberhasilan

- Satu login gateway dapat menangani beberapa portal.
- User dapat memiliki lebih dari satu workspace bila diizinkan.
- Lender dan perusahaan tidak dapat melihat data organisasi lain.
- Penambahan organization type baru tidak membutuhkan sistem autentikasi baru.
- Back-office tetap memiliki kontrol internal dan two-factor authentication sesuai kebutuhan.
- URL dan tampilan menggunakan istilah `perusahaan`, bukan `partner`.
- Modul bisnis dapat dikembangkan tanpa mencampur aturan domain yang berbeda.

## Keputusan yang masih perlu dikonfirmasi

- Apakah satu user boleh memiliki beberapa organization?
- Apa nama role awal untuk lender dan perusahaan?
- Apakah internal TOMI akan dimodelkan sebagai organization khusus?
- Apakah lender dan perusahaan menggunakan kredensial login yang sama?
- Fitur lender mana yang harus dipindahkan dari back-office ke portal lender?
- Berapa lama URL `/partner` perlu dipertahankan sebagai kompatibilitas?

