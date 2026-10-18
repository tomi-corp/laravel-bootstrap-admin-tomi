# Strategi Branch dan Pull Request TOMI

Status: Draft untuk diskusi

## Tujuan

Dokumen ini mendefinisikan cara kerja Git untuk TOMI agar setiap perubahan:

- dikerjakan pada branch terpisah;
- melewati gerbang integrasi yang jelas;
- memiliki Pull Request yang rapi dan mudah dipahami;
- dapat dilacak dari pekerjaan awal sampai production;
- memiliki bukti testing, risiko, dan rencana rollback.

Dokumen ini adalah pedoman proses. Konfigurasi branch protection, CI/CD, dan template GitHub akan diterapkan setelah aturan ini disepakati.

## Alur branch utama

```text
feature/*, fix/*, bugfix/*, refactor/*, chore/*
                         |
                         v
                       local
                         |
                         v
                      develop
                         |
                         v
                      staging
                         |
                         v
                       main
```

`main` digunakan sebagai nama branch production. Branch `master` tidak digunakan sebagai branch utama baru.

### Fungsi setiap branch

| Branch | Fungsi | Sumber perubahan | Tujuan |
|---|---|---|---|
| `local` | Gerbang integrasi awal | Branch kerja | `develop` |
| `develop` | Pengembangan bersama | `local` | `staging` |
| `staging` | QA, UAT, dan release candidate | `develop` | `main` |
| `main` | Production | `staging` atau hotfix | Deployment production |

`local` di sini adalah branch integrasi awal. Ia berbeda dari environment lokal di komputer developer.

## Aturan branch utama

- Tidak boleh melakukan direct push ke `local`, `develop`, `staging`, atau `main`.
- Semua perubahan masuk melalui Pull Request.
- Branch utama harus menggunakan branch protection.
- Pull Request wajib memiliki reviewer yang sesuai.
- CI wajib lulus sebelum Pull Request dapat di-merge.
- Branch kerja dibuat dari `local` untuk pekerjaan normal.
- Branch kerja dihapus setelah berhasil di-merge.
- `main` hanya menerima perubahan yang sudah divalidasi di `staging`.
- Force push ke branch utama dilarang.
- Perubahan tidak boleh mencampur pekerjaan yang tidak berhubungan.

## Jenis branch kerja

Gunakan jenis branch berikut sesuai tujuan perubahan:

| Prefix | Penggunaan | Contoh |
|---|---|---|
| `feature/` | Fitur baru | `feature/TOMI-123-login-gateway` |
| `fix/` | Perbaikan masalah non-kritis | `fix/TOMI-124-login-validation` |
| `bugfix/` | Perbaikan bug yang sudah teridentifikasi | `bugfix/TOMI-125-session-redirect` |
| `hotfix/` | Perbaikan kritis di production | `hotfix/TOMI-126-production-login` |
| `refactor/` | Perubahan struktur tanpa mengubah tujuan bisnis | `refactor/TOMI-127-auth-module` |
| `chore/` | Maintenance atau tooling | `chore/TOMI-128-update-dependency` |
| `docs/` | Dokumentasi | `docs/TOMI-129-branching-guide` |
| `test/` | Penambahan atau perubahan test | `test/TOMI-130-authentication` |

Format nama branch:

```text
<type>/<ticket>-<deskripsi-singkat>
```

Gunakan huruf kecil, tanda hubung, dan nama yang menjelaskan tujuan perubahan.

## Alur kerja normal

### 1. Membuat branch kerja

Branch kerja dibuat dari `local`:

```bash
git switch local
git pull origin local
git switch -c feature/TOMI-123-login-gateway
```

### 2. Mengembangkan perubahan

```bash
git add <file>
git commit -m "feat: add centralized login gateway"
```

Commit harus menjelaskan perubahan yang dilakukan dan tidak boleh memuat secret.

### 3. Push branch kerja

```bash
git push -u origin feature/TOMI-123-login-gateway
```

### 4. Membuat Pull Request ke `local`

Pull Request harus menjelaskan scope, implementasi, testing, risiko, dan rollback plan.

### 5. Promosi antar-gerbang

Setelah perubahan memenuhi syarat pada setiap tahap:

```text
feature/* → local → develop → staging → main
```

Setiap promosi dilakukan melalui Pull Request baru atau release Pull Request yang sesuai. Tidak boleh melewati branch secara langsung.

## Standar judul Pull Request

Gunakan format:

```text
[TYPE] deskripsi singkat perubahan
```

Contoh:

```text
[FEATURE] Add centralized login gateway
[FIX] Fix company dashboard redirect
[BUGFIX] Prevent unauthorized lender access
[REFACTOR] Separate organization access layer
[HOTFIX] Fix production login failure
```

## Template isi Pull Request

Setiap Pull Request harus memiliki bagian berikut:

```markdown
## Ringkasan

Jelaskan perubahan utama secara singkat.

## Latar Belakang

Jelaskan masalah atau kebutuhan yang melatarbelakangi perubahan ini.

## Scope Perubahan

### Yang diubah

-
-

### Yang tidak diubah

-
-

## Detail Implementasi

Jelaskan perubahan berdasarkan modul atau file penting.

## Alur Sebelum dan Sesudah

### Sebelum

1.
2.

### Sesudah

1.
2.

## Database

- [ ] Tidak ada perubahan database
- [ ] Ada migration baru
- [ ] Ada perubahan seed
- [ ] Ada perubahan data existing

Jika ada migration, jelaskan urutan dan risiko rollback.

## Security dan Authorization

- Guard atau middleware yang terpengaruh:
- Role/permission yang terpengaruh:
- Potensi risiko keamanan:
- Validasi akses yang dilakukan:

## Testing

### Automated Test

```text
Tuliskan command test dan hasilnya.
```

### Manual Test

- [ ]
- [ ]
- [ ]

## Evidence

Tambahkan screenshot, video, response API, atau bukti lain jika relevan.

## Risiko dan Dampak

- Risiko:
- Modul terdampak:
- Potensi breaking change:
- Mitigasi:

## Rollback Plan

Jelaskan cara membatalkan perubahan jika terjadi masalah.

## Checklist

- [ ] Branch dibuat dari branch yang benar
- [ ] Tidak ada credential atau secret
- [ ] Tidak ada file `.env` yang ikut berubah
- [ ] Test sudah dijalankan
- [ ] Migration sudah diperiksa
- [ ] Authorization sudah diperiksa
- [ ] Evidence sudah ditambahkan bila diperlukan
- [ ] Dokumentasi sudah diperbarui bila diperlukan
- [ ] Tidak ada perubahan di luar scope
```

## Fokus review berdasarkan gerbang

### Branch kerja → `local`

Fokus review:

- correctness;
- keamanan;
- struktur kode;
- unit atau feature test;
- perubahan file dan scope;
- screenshot atau evidence.

### `local` → `develop`

Fokus review:

- integrasi antar-modul;
- konflik dengan pekerjaan lain;
- regression test;
- kesesuaian dengan arsitektur;
- kesiapan untuk development bersama.

### `develop` → `staging`

Fokus review:

- release candidate;
- daftar fitur dan bugfix;
- migration dan seed;
- instruksi deployment;
- skenario QA dan UAT;
- risiko integrasi eksternal.

### `staging` → `main`

Fokus review:

- persetujuan QA/UAT;
- hasil regression test;
- release note;
- backup dan rollback plan;
- dampak production;
- persetujuan deployment.

## Alur hotfix production

Untuk masalah kritis di production:

```text
main
  ↓
hotfix/*
  ↓
main
  ↓
staging
  ↓
develop
  ↓
local
```

Hotfix yang sudah masuk ke `main` harus disinkronkan kembali ke branch pengembangan agar perbaikannya tidak hilang pada release berikutnya.

## Aturan merge

- Gunakan Pull Request, bukan direct push.
- Gunakan squash merge untuk branch kerja kecil agar history tetap rapi.
- Gunakan merge commit atau strategi yang disepakati untuk promosi release antar-gerbang.
- Jangan menggabungkan Pull Request dengan conflict yang belum diselesaikan.
- Jangan menggabungkan Pull Request yang gagal CI tanpa alasan dan persetujuan tertulis.
- Pastikan branch target sudah terbaru sebelum merge.

## CI yang disarankan

Minimal pemeriksaan otomatis:

- validasi syntax PHP;
- code style atau formatter;
- unit dan feature test;
- validasi route;
- pemeriksaan konfigurasi yang relevan;
- pemeriksaan secret sederhana;
- build asset frontend jika diperlukan.

Validasi yang lebih ketat dapat diterapkan pada `staging` dan `main`, termasuk smoke test dan regression test.

## Rencana penerapan

1. Sepakati nama branch utama: `local`, `develop`, `staging`, dan `main`.
2. Pastikan branch tersebut tersedia di remote repository.
3. Aktifkan branch protection.
4. Tambahkan aturan reviewer dan CODEOWNERS bila diperlukan.
5. Tambahkan Pull Request template.
6. Tambahkan CI dasar untuk branch kerja dan `local`.
7. Tambahkan CI release untuk `develop`, `staging`, dan `main`.
8. Dokumentasikan prosedur deployment dan rollback.
9. Evaluasi workflow setelah beberapa release.

## Keputusan yang masih perlu dikonfirmasi

- Apakah `local` akan menjadi branch remote bersama atau hanya branch integrasi sementara?
- Siapa reviewer wajib untuk setiap modul?
- Apakah promosi antar-gerbang memakai Pull Request terpisah?
- Apakah branch kerja dibuat selalu dari `local`, termasuk hotfix?
- Apakah production branch akan bernama `main` secara resmi?
- Test apa yang wajib lulus sebelum promosi ke setiap gerbang?

