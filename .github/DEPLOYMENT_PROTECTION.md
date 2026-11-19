# Perlindungan Deployment Server

Semua deploy ke server cPanel **hanya** lewat workflow `.github/workflows/deploy.yml`. Server tidak boleh meng-update dirinya sendiri, supaya approval production tidak bisa dilewati dan setiap deploy tercatat di tab Actions.

## Aturan utama

| Branch    | Environment GitHub | Kapan deploy                      | Approval |
|-----------|--------------------|-----------------------------------|----------|
| `develop` | `development`      | Otomatis setiap push/merge        | Tidak    |
| `staging` | `staging`          | Otomatis setiap push/merge        | Tidak    |
| `main`    | `production`       | Otomatis, menunggu approval dulu  | **Wajib** |

1. Environment `production` wajib punya required reviewer `@rezasaputra4250` dan batasan deployment branch `main`.
2. Deploy ke tiap environment selalu memakai branch miliknya (development=`develop`, staging=`staging`, production=`main`). Workflow memaksa ini, termasuk saat deploy ulang manual.
3. Server tidak boleh memasang webhook auto-pull dari GitHub.
4. Server tidak boleh menjalankan cron `git pull` otomatis.
5. Fitur auto-deploy bawaan panel (cPanel Git Version Control "Deploy HEAD Commit" otomatis, Ploi, Forge, RunCloud, DeployHQ, dan sejenisnya) harus dimatikan.

## Setting GitHub Environment

Buka `Settings` -> `Environments`, lalu buat `development`, `staging`, dan `production`.

- `production`:
  1. Aktifkan `Required reviewers` dan masukkan `@rezasaputra4250`.
  2. `Deployment branches and tags` -> `Selected branches and tags` -> tambahkan `main`.
- `staging`: `Deployment branches` -> hanya `staging` (tanpa reviewer).
- `development`: `Deployment branches` -> hanya `develop` (tanpa reviewer).

Secrets dan variables per environment dijelaskan di `docs/deployment-cpanel.md`.

## Deploy ulang manual

Tab `Actions` -> `Deploy ke cPanel` -> `Run workflow`, lalu pilih target. Jalankan dari branch yang sama dengan targetnya (misalnya production dari `main`), karena batasan deployment branch environment mengecek branch tempat workflow dijalankan. Production tetap menunggu approval.

## Cek di server

Pastikan tidak ada mekanisme auto-update lain seperti:

- GitHub webhook yang menjalankan `git pull`.
- Cron job yang menjalankan `git pull`.
- cPanel Git Version Control atau service hosting lain yang auto-deploy saat push.

Jika ada, matikan dan biarkan workflow `deploy.yml` yang melakukan deploy.
