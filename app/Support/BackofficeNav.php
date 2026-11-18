<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Support\Facades\Route;

/**
 * Menu sidebar backoffice. Setiap produk punya turunan: dasbor produk lalu halaman operasionalnya.
 *
 * Isi menu = semua menu sidebar lama (tidak ada halaman yang hilang dari menu), dengan judul Bahasa Indonesia.
 * Item `internal` mengikuti aturan sidebar lama: tidak tampil untuk akun bertipe pic/staff. Item yang route-nya
 * belum ada di aplikasi ini disembunyikan (`Route::has`), jadi menu bisa ditambah bertahap.
 */
class BackofficeNav
{
    /** Tipe akun yang di sidebar lama hanya melihat menu produk (lihat sidebar lama: $limitedProductRole). */
    private const LIMITED_TYPES = ['pic', 'staff'];

    public static function build(?Admin $user): array
    {
        if (! $user) {
            return [];
        }
        $internal = ! in_array($user->type, self::LIMITED_TYPES, true);

        // [judul, route, internal]
        $groups = [
            ['type' => 'link', 'label' => 'Dasbor utama', 'icon' => 'home', 'route' => 'admin.insight.index'],
            ['type' => 'link', 'label' => 'Dasbor lama', 'icon' => 'table', 'route' => 'admin.dashboard.index'],
            ['type' => 'label', 'label' => 'Produk'],
            self::group('ppob', 'PPOB', 'bolt', [
                ['Komisi', 'admin.ppob.komisi', true],
                ['Transaksi Prabayar', 'admin.ppob.prepaid', true],
                ['Transaksi Pasca Bayar', 'admin.ppob.postpaid', true],
            ]),
            // halaman turunan perusahaan (karyawan, konfigurasi, pengajuan) path-nya tidak diawali /ksp/pinjaman
            ['type' => 'link', 'label' => 'Perusahaan', 'icon' => 'table', 'route' => 'admin.ksp.index',
                'also' => ['/backoffice/ksp/list/karyawan', '/backoffice/ksp/konfigurasi', '/backoffice/ksp/pengajuan']],
            ['type' => 'link', 'label' => 'Pemberi Pinjaman', 'icon' => 'bank', 'route' => 'admin.lender.index'],
            self::product('tomidana', 'Tomi Dana', 1, [
                ['Daftar Tagihan Perusahaan', 'admin.ksp.invoicing.index', false],
                ['Buat Tagihan', 'admin.generate.invoice.index', false],
                ['Persetujuan Biaya Aplikasi', 'admin.lender.invoicing.index', false],
                ['Biaya', 'admin.cost.index', false],
            ]),
            self::product('tomigaji', 'TomiGaji', 2, [
                ['Perusahaan', 'admin.salary-financing.companies', false],
                ['Pemberi Pinjaman', 'admin.salary-financing.lenders', false],
                ['Pemantauan', 'admin.salary-financing.index', false],
            ]),
            self::product('tomiinvoice', 'TomiInvoice', 3, [
                ['Perusahaan', 'admin.invoice-financing.companies', false],
                ['Pemberi Pinjaman', 'admin.invoice-financing.lenders', false],
                ['Pemantauan', 'admin.invoice-financing.index', false],
            ]),
            self::product('tomigawai', 'TomiGawai', 4, [
                ['Pemantauan', 'admin.gadget-installment.index', false],
            ]),
            ['type' => 'label', 'label' => 'Harian'],
            ['type' => 'link', 'label' => 'Presensi Harian', 'icon' => 'calendar', 'route' => 'admin.daily.index', 'internal' => true],
            ['type' => 'label', 'label' => 'Seluler'],
            self::group('seluler', 'Seluler', 'phone', [
                ['Menu Seluler', 'admin.menumobile.index', true],
                ['Pembaruan OTA Seluler', 'admin.ota.index', true],
                ['Kirim Notifikasi', 'admin.sendnotif.index', true],
            ]),
            ['type' => 'label', 'label' => 'Manajemen'],
            ['type' => 'link', 'label' => 'Kelola Admin', 'icon' => 'shield', 'route' => 'admin.manage.admin.index', 'internal' => true],
            ['type' => 'link', 'label' => 'PIC Pemberi Pinjaman', 'icon' => 'users', 'route' => 'admin.manage.pic-lender.index', 'internal' => true],
            ['type' => 'link', 'label' => 'Pengaturan', 'icon' => 'gear', 'route' => 'admin.profile.index'],
            ['type' => 'label', 'label' => 'BTA'],
            self::group('website', 'Layanan Website', 'info', [
                ['Tagihan', 'admin.website.invoice.index', false],
            ]),
        ];

        $visible = function (array $item) use ($internal) {
            if (! empty($item['internal']) && ! $internal) {
                return false;
            }

            return empty($item['route']) || Route::has($item['route']);
        };

        $out = [];
        foreach ($groups as $g) {
            if ($g['type'] === 'product') {
                $g['children'] = array_values(array_filter($g['children'], $visible));
                $hasDashboard = $g['dashboard'] !== null && Route::has($g['dashboard']['route']);
                if (! $hasDashboard && $g['children'] === []) {
                    continue;
                }
                $out[] = $g;
            } elseif ($g['type'] === 'label' || $visible($g)) {
                $out[] = $g;
            }
        }

        // label tanpa isi di bawahnya tidak ditampilkan
        $out = array_values(array_filter($out, function ($g, $i) use ($out) {
            return $g['type'] !== 'label' || (isset($out[$i + 1]) && $out[$i + 1]['type'] !== 'label');
        }, ARRAY_FILTER_USE_BOTH));

        return self::markActive($out);
    }

    /**
     * Tandai menu yang sedang dibuka. Halaman turunan (detail, konfigurasi per perusahaan, dst.) ikut
     * menandai menu induknya lewat awalan path terpanjang (atau awalan tambahan `also`). Untuk halaman
     * turunan, `exact` = false sehingga judul header tetap memakai judul halamannya sendiri.
     */
    private static function markActive(array $nav): array
    {
        $path = '/' . trim(request()->path(), '/');
        $best = null;
        $bestLen = -1;
        $exact = false;
        $consider = function (string $route, int $gi, ?int $ci, array $also = []) use ($path, &$best, &$bestLen, &$exact) {
            $p = '/' . trim((string) parse_url(route($route, [], false), PHP_URL_PATH), '/');
            $isExact = request()->routeIs($route) || $path === $p;
            $len = $isExact ? PHP_INT_MAX : -1;
            foreach (array_merge([$p], $also) as $prefix) {
                if (! $isExact && str_starts_with($path, rtrim($prefix, '/') . '/')) {
                    $len = max($len, strlen($prefix));
                }
            }
            if ($len > $bestLen) {
                $best = [$gi, $ci];
                $bestLen = $len;
                $exact = $isExact;
            }
        };

        // Dasbor produk ditandai lewat grupnya, bukan lewat "Dasbor utama".
        $onProductDashboard = request()->routeIs('admin.insight.product');
        foreach ($onProductDashboard ? [] : $nav as $gi => $g) {
            if ($g['type'] === 'link') {
                $consider($g['route'], $gi, null, $g['also'] ?? []);
            } elseif ($g['type'] === 'product') {
                foreach ($g['children'] as $ci => $c) {
                    $consider($c['route'], $gi, $ci);
                }
            }
        }

        foreach ($nav as $gi => &$g) {
            if ($g['type'] === 'link') {
                $g['active'] = $best !== null && $best[0] === $gi && $best[1] === null;
                $g['exact'] = $g['active'] && $exact;
            } elseif ($g['type'] === 'product') {
                foreach ($g['children'] as $ci => &$c) {
                    $c['active'] = $best !== null && $best[0] === $gi && $best[1] === $ci;
                    $c['exact'] = $c['active'] && $exact;
                }
                unset($c);
                $g['dashboardActive'] = $g['dashboard'] !== null && $onProductDashboard && request()->route('product') === $g['key'];
                $g['active'] = $g['dashboardActive'] || ($best !== null && $best[0] === $gi && $best[1] !== null);
            }
        }
        unset($g);

        return $nav;
    }

    /**
     * Posisi halaman saat ini di menu: ['group' => 'Tomi Dana', 'label' => 'Biaya', 'exact' => true] atau null.
     * `exact` = false untuk halaman turunan dari menu itu.
     */
    public static function locate(array $nav): ?array
    {
        foreach ($nav as $g) {
            if ($g['type'] === 'link' && ! empty($g['active'])) {
                return ['group' => null, 'label' => $g['label'], 'exact' => $g['exact']];
            }
            if ($g['type'] === 'product') {
                if (! empty($g['dashboardActive'])) {
                    return ['group' => $g['label'], 'label' => 'Dasbor', 'exact' => true];
                }
                foreach ($g['children'] as $c) {
                    if (! empty($c['active'])) {
                        return ['group' => $g['label'], 'label' => $c['label'], 'exact' => $c['exact']];
                    }
                }
            }
        }

        return null;
    }

    /** Grup menu tanpa dasbor produk (mis. PPOB, Seluler). */
    private static function group(string $key, string $name, string $icon, array $children): array
    {
        return [
            'type' => 'product',
            'key' => $key,
            'label' => $name,
            'slot' => null,
            'icon' => $icon,
            'dashboard' => null,
            'children' => self::children($children),
        ];
    }

    private static function product(string $key, string $name, int $slot, array $children): array
    {
        return [
            'type' => 'product',
            'key' => $key,
            'label' => $name,
            'slot' => $slot,
            'icon' => null,
            'dashboard' => ['label' => 'Dasbor', 'route' => 'admin.insight.product', 'params' => ['product' => $key]],
            'children' => self::children($children),
        ];
    }

    private static function children(array $children): array
    {
        return array_map(fn ($c) => ['label' => $c[0], 'route' => $c[1], 'internal' => $c[2]], $children);
    }
}
