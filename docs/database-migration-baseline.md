# Baseline dan Deployment Migration

## Kondisi baseline saat ini

Database existing dapat memiliki tabel yang sudah lengkap, tetapi tabel `migrations` masih mencatat nama migration lama. Dalam kondisi tersebut, `php artisan migrate` akan mencoba menjalankan ulang migration `create_*` dan dapat gagal karena tabel sudah ada.

Baseline hanya dilakukan satu kali pada database existing setelah backup. Baseline mencatat migration yang struktur tabelnya sudah tersedia sebagai batch `0`; baseline tidak membuat, menghapus, atau mengubah tabel bisnis.

Migration update yang belum pernah dijalankan tidak boleh ikut dibaseline. Migration tersebut harus tetap berstatus `Pending` agar dijalankan oleh Laravel.

## Prosedur database existing

1. Backup database.
2. Deploy source code dan jalankan `composer install --no-dev --optimize-autoloader`.
3. Jalankan `php artisan migrate:status`.
4. Baseline hanya migration lama yang tabelnya sudah ada.
5. Pastikan migration update tetap `Pending`.
6. Jalankan:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
```

Jangan gunakan `migrate:fresh` atau `migrate:refresh` pada database existing.

## Validasi

Setelah deployment, jalankan:

```bash
php artisan migrate:status
```

Semua migration harus berstatus `Ran` dan tidak boleh ada migration `Pending` yang tidak disengaja.
