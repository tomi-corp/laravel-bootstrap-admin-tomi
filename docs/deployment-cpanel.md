# Deploy otomatis ke cPanel

Workflow `.github/workflows/deploy.yml` men-deploy aplikasi ke server cPanel lewat SSH:

- `develop` → environment `development` (otomatis)
- `staging` → environment `staging` (otomatis)
- `main` → environment `production` (otomatis setelah di-approve)

Setiap deploy menjalankan langkah berikut:

1. Build aset Vite (`npm ci && npm run build`) di runner GitHub. Server tidak perlu Node.js.
2. SSH ke server: `artisan down`, lalu `git fetch` + `git reset --hard origin/<branch>`.
3. Upload `public/build` ke server dengan `rsync`.
4. `composer install --no-dev`, `artisan migrate --force`, `storage:link`, cache config/route/view, lalu `artisan up`.
5. Jika ada langkah yang gagal, `artisan up` tetap dijalankan supaya situs tidak terkunci di maintenance mode, dan job ditandai gagal.

> `git reset --hard` membuang perubahan yang diedit langsung di server. Jangan edit kode di server; semua perubahan lewat Git.
> File yang tidak di-track Git (`.env`, `storage/`, `vendor/`, `public/build`) tidak tersentuh oleh reset.

Lakukan setup di bawah **sekali per server** (development, staging, production).

## 1. Siapkan server (via SSH / Terminal cPanel)

1. Cek PHP CLI dan Composer:
   ```bash
   php -v          # minimal 8.2
   composer -V
   ```
   Kalau `php -v` bukan 8.2+, cari binary yang sesuai, misalnya `/opt/cpanel/ea-php82/root/usr/bin/php`, lalu isi variable `PHP_BIN` (lihat langkah 4). Kalau composer tidak ada di PATH, isi `COMPOSER_BIN` dengan path lengkapnya, misalnya `/opt/cpanel/composer/bin/composer`.

2. Buat **deploy key** supaya server bisa `git fetch` dari repo private (read-only):
   ```bash
   ssh-keygen -t ed25519 -f ~/.ssh/github_tomiv2 -N "" -C "cpanel-tomiv2"
   cat ~/.ssh/github_tomiv2.pub
   cat >> ~/.ssh/config <<'EOF'
   Host github-tomiv2
     HostName github.com
     User git
     IdentityFile ~/.ssh/github_tomiv2
     IdentitiesOnly yes
   EOF
   ```
   Tempel public key-nya di GitHub: repo → `Settings` → `Deploy keys` → `Add deploy key`. Biarkan **Allow write access** tidak dicentang.

3. Clone repo di luar `public_html` dan pakai path **absolut**:
   ```bash
   mkdir -p ~/apps && cd ~/apps
   git clone -b develop git@github-tomiv2:tomi-corp/laravel-bootstrap-admin-tomi.git tomiv2   # develop/staging/main sesuai server
   cd tomiv2
   cp .env.example .env    # lalu isi .env untuk server ini
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan storage:link
   chmod -R ug+rwX storage bootstrap/cache
   pwd                     # contoh: /home/namauser/apps/tomiv2  → isi secret DEPLOY_PATH
   ```

4. Arahkan document root domain/subdomain ke folder `public`:
   - cPanel → `Domains` → domain/subdomain → `Document Root` = `apps/tomiv2/public`, **atau**
   - untuk domain utama yang document root-nya terkunci di `public_html`: pindahkan `public_html` ke cadangan, lalu `ln -s ~/apps/tomiv2/public ~/public_html`.

5. Matikan auto-deploy lain di server (cPanel Git Version Control, cron `git pull`, webhook). Lihat `.github/DEPLOYMENT_PROTECTION.md`.

## 2. Key SSH untuk GitHub Actions

Key ini dipakai GitHub Actions untuk masuk ke server. Buat di komputer lokal, terpisah dari deploy key:

```bash
ssh-keygen -t ed25519 -f gha_tomiv2 -N "" -C "github-actions-tomiv2"
```

1. cPanel → `SSH Access` → `Manage SSH Keys` → `Import Key`. Tempel isi `gha_tomiv2.pub`, lalu **Authorize**.
2. Ambil fingerprint host server (port SSH cPanel bisa 22, 21098, dsb.):
   ```bash
   ssh-keyscan -p <port> <host>
   ```
3. Hapus file `gha_tomiv2` dari komputer setelah isinya dimasukkan ke secret.

## 3. GitHub Environment, secrets, dan variables

Repo → `Settings` → `Environments`, lalu buat `development`, `staging`, `production`. Aturan approval dan branch ada di `.github/DEPLOYMENT_PROTECTION.md`; untuk `production` wajib required reviewer.

Isi di **masing-masing environment**:

| Jenis    | Nama              | Isi                                                              |
|----------|-------------------|------------------------------------------------------------------|
| Secret   | `SSH_HOST`        | Host/IP server cPanel                                            |
| Secret   | `SSH_PORT`        | Port SSH (kosong = 22)                                           |
| Secret   | `SSH_USER`        | Username cPanel                                                  |
| Secret   | `SSH_PRIVATE_KEY` | Isi file `gha_tomiv2` (private key, termasuk baris BEGIN/END)    |
| Secret   | `SSH_KNOWN_HOSTS` | Output `ssh-keyscan` di langkah 2                                |
| Secret   | `DEPLOY_PATH`     | Path absolut repo di server, misalnya `/home/namauser/apps/tomiv2` |
| Variable | `PHP_BIN`         | Opsional, default `php`                                          |
| Variable | `COMPOSER_BIN`    | Opsional, default `composer`                                     |

> Selama secrets suatu environment belum diisi, deploy ke environment itu akan gagal dengan pesan "Secret ... belum diisi".

## 4. Uji pertama

1. Tab `Actions` → `Deploy ke cPanel` → `Run workflow` dari branch `develop`, target `development`.
2. Pastikan job hijau, lalu buka situsnya dan cek aset CSS/JS termuat.
3. Merge PR ke `staging`: deploy jalan otomatis.
4. Merge PR ke `main`: job `Deploy main ke production` menunggu approval. Klik **Review deployments** → **Approve and deploy**.

## Troubleshooting

- **`Permission denied (publickey)` saat SSH dari Actions**: public key belum di-Authorize di cPanel, atau `SSH_USER`/`SSH_PORT` salah.
- **`Host key verification failed`**: isi ulang `SSH_KNOWN_HOSTS` dengan `ssh-keyscan -p <port> <host>`.
- **`git fetch` gagal di server**: deploy key belum ditambahkan, atau remote `origin` tidak memakai host `github-tomiv2`. Cek dengan `git remote -v`, lalu perbaiki dengan `git remote set-url origin git@github-tomiv2:tomi-corp/laravel-bootstrap-admin-tomi.git`.
- **`composer: command not found` / versi PHP salah**: isi variable `COMPOSER_BIN` / `PHP_BIN` dengan path lengkap.
- **Situs tertahan di maintenance mode**: jalankan `php artisan up` di server, lalu cek log job yang gagal.
