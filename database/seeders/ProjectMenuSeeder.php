<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectMenuSeeder extends Seeder
{
    private array $menuColumns = [];

    public function run(): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasTable('menus')) {
            return;
        }

        $this->menuColumns = Schema::getColumnListing('menus');

        DB::transaction(function () {
            $internalProjectId = $this->project('Internal', 'internal', 'Menu internal admin TOMI');
            $companyProjectId = $this->project('Perusahaan', 'perusahaan', 'Menu proyek perusahaan');
            $lenderProjectId = $this->project('Lender', 'lender', 'Menu proyek lender');

            $this->seedInternalMenus($internalProjectId);
            $this->seedCompanyMenus($companyProjectId);
            $this->seedLenderMenus($lenderProjectId);
        });
    }

    private function seedInternalMenus(int $projectId): void
    {
        $dashboard = $this->menu($projectId, null, 'Dashboard', 'admin.dashboard.index', 1, 'fa-solid fa-gauge-high');
        $master = $this->menu($projectId, null, 'Master Data', null, 10, 'fa-solid fa-database');
        $tomiDana = $this->menu($projectId, null, 'TomiDana', null, 20, 'fa-solid fa-hand-holding-dollar');
        $konfigurasi = $this->menu($projectId, $tomiDana, 'Konfigurasi', null, 2, 'fa-solid fa-gear');
        $tomiGaji = $this->menu($projectId, null, 'TomiGaji', null, 30, 'fa-solid fa-money-check-dollar');
        $tomiGawai = $this->menu($projectId, null, 'TomiGawai', null, 40, 'fa-solid fa-mobile-screen-button');
        $tomiInvoice = $this->menu($projectId, null, 'TomiInvoice', null, 50, 'fa-solid fa-file-invoice-dollar');
        $management = $this->menu($projectId, null, 'Management', null, 90, 'fa-solid fa-sliders');

        $this->menu($projectId, $master, 'Perusahaan', 'admin.companies.index', 1, 'fa-solid fa-building');
        $this->menu($projectId, $master, 'Lender', 'admin.lender.index', 2, 'fa-solid fa-landmark');
        $this->menu($projectId, $master, 'PIC Lender', 'admin.manage.pic-lender.index', 3, 'fa-solid fa-user-tie');
        $this->menu($projectId, $master, 'Referral', 'admin.referral.index', 4, 'fa-solid fa-user-group');

        $this->menu($projectId, $tomiDana, 'Perusahaan', 'admin.ksp.index', 1, 'fa-solid fa-building');
        $this->menu($projectId, $konfigurasi, 'Tenor', 'admin.product-configurations.tenor.companies', 1, 'fa-solid fa-sliders');
        $this->menu($projectId, $konfigurasi, 'DSR', 'admin.product-configurations.dsr.companies', 2, 'fa-solid fa-percent');
        $this->menu($projectId, $tomiDana, 'Generate Invoice', 'admin.generate.invoice.index', 3, 'fa-solid fa-file-invoice');

        $this->menu($projectId, $tomiGaji, 'Perusahaan', 'admin.salary-financing.companies', 1, 'fa-solid fa-building');
        $this->menu($projectId, $tomiGaji, 'Konfigurasi', 'admin.salary-financing.index', 2, 'fa-solid fa-gear');
        $this->menu($projectId, $tomiGawai, 'Konfigurasi', 'admin.gadget-installment.index', 1, 'fa-solid fa-gear');
        $this->menu($projectId, $tomiInvoice, 'Transaksi', 'admin.invoice-financing.index', 1, 'fa-solid fa-file-lines');
        $this->menu($projectId, $tomiInvoice, 'Mitra', 'admin.invoice-financing.lenders', 2, 'fa-solid fa-handshake');
        $this->menu($projectId, $tomiInvoice, 'Pelanggan', 'admin.invoice-financing.companies', 3, 'fa-solid fa-building-user');

        $this->menu($projectId, $management, 'Menu', 'admin.menus.index', 1, 'fa-solid fa-bars');
        $this->menu($projectId, $management, 'Proyek', 'admin.projects.index', 2, 'fa-solid fa-layer-group');
        $this->menu($projectId, $management, 'Peran', 'admin.roles.index', 3, 'fa-solid fa-user-shield');
        $this->menu($projectId, $management, 'Hak Akses Menu', 'admin.role-menu-permissions.index', 4, 'fa-solid fa-key');
        $this->menu($projectId, $management, 'Admin', 'admin.manage.admin.index', 5, 'fa-solid fa-users-gear');

        unset($dashboard);
    }

    private function seedCompanyMenus(int $projectId): void
    {
        $tomiDana = $this->menu($projectId, null, 'TomiDana', null, 10, 'fa-solid fa-hand-holding-dollar');
        $konfigurasi = $this->menu($projectId, $tomiDana, 'Konfigurasi', null, 2, 'fa-solid fa-gear');

        $this->menu($projectId, null, 'Dashboard', 'admin.dashboard.index', 1, 'fa-solid fa-gauge-high');
        $this->menu($projectId, $tomiDana, 'Karyawan', 'admin.ksp.index', 1, 'fa-solid fa-users');
        $this->menu($projectId, $konfigurasi, 'Tenor', 'admin.product-configurations.tenor.companies', 1, 'fa-solid fa-sliders');
        $this->menu($projectId, $konfigurasi, 'DSR', 'admin.product-configurations.dsr.companies', 2, 'fa-solid fa-percent');
    }

    private function seedLenderMenus(int $projectId): void
    {
        $tomiDana = $this->menu($projectId, null, 'TomiDana', null, 10, 'fa-solid fa-hand-holding-dollar');
        $konfigurasi = $this->menu($projectId, $tomiDana, 'Konfigurasi', null, 2, 'fa-solid fa-gear');
        $tomiGaji = $this->menu($projectId, null, 'TomiGaji', null, 20, 'fa-solid fa-money-check-dollar');
        $tomiGawai = $this->menu($projectId, null, 'TomiGawai', null, 30, 'fa-solid fa-mobile-screen-button');
        $tomiInvoice = $this->menu($projectId, null, 'TomiInvoice', null, 40, 'fa-solid fa-file-invoice-dollar');

        $this->menu($projectId, null, 'Dashboard', 'admin.dashboard.index', 1, 'fa-solid fa-gauge-high');
        $this->menu($projectId, $tomiDana, 'Perusahaan', 'admin.ksp.index', 1, 'fa-solid fa-building');
        $this->menu($projectId, $konfigurasi, 'Tenor', 'admin.product-configurations.tenor.companies', 1, 'fa-solid fa-sliders');
        $this->menu($projectId, $konfigurasi, 'DSR', 'admin.product-configurations.dsr.companies', 2, 'fa-solid fa-percent');
        $this->menu($projectId, $tomiDana, 'Pencairan Dana', 'admin.disbursements.index', 3, 'fa-solid fa-money-bill-transfer');
        $this->menu($projectId, $tomiDana, 'Pengembalian Dana', 'admin.repayments.index', 4, 'fa-solid fa-receipt');
        $this->menu($projectId, $tomiDana, 'Generate Invoice', 'admin.generate.invoice.index', 5, 'fa-solid fa-file-invoice');

        $this->menu($projectId, $tomiGaji, 'Perusahaan', 'admin.salary-financing.companies', 1, 'fa-solid fa-building');
        $this->menu($projectId, $tomiGaji, 'Konfigurasi', 'admin.salary-financing.index', 2, 'fa-solid fa-gear');
        $this->menu($projectId, $tomiGawai, 'Perusahaan', null, 1, 'fa-solid fa-building');
        $this->menu($projectId, $tomiGawai, 'Konfigurasi', 'admin.gadget-installment.index', 2, 'fa-solid fa-gear');
        $this->menu($projectId, $tomiInvoice, 'Transaksi', 'admin.invoice-financing.index', 1, 'fa-solid fa-file-lines');
        $this->menu($projectId, $tomiInvoice, 'Konfigurasi', null, 2, 'fa-solid fa-gear');
        $invoiceConfiguration = $this->findMenuId($projectId, $tomiInvoice, 'Konfigurasi', null);
        $this->menu($projectId, $invoiceConfiguration, 'Mitra', 'admin.invoice-financing.lenders', 1, 'fa-solid fa-handshake');
        $this->menu($projectId, $invoiceConfiguration, 'Pelanggan', 'admin.invoice-financing.companies', 2, 'fa-solid fa-building-user');
    }

    private function project(string $name, string $code, string $description): int
    {
        $now = now();
        $project = DB::table('projects')->where('code', $code)->first();
        $payload = [
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($project) {
            DB::table('projects')->where('id', $project->id)->update($payload);

            return (int) $project->id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table('projects')->insertGetId($payload);
    }

    private function menu(int $projectId, int|string|null $parentId, string $name, ?string $route, int $sortOrder, ?string $icon = null): int
    {
        $now = now();
        $menu = $this->findMenu($projectId, $parentId, $name, $route);
        $payload = $this->menuPayload($projectId, $parentId, $name, $route, $sortOrder, $icon, $now);

        if ($menu) {
            DB::table('menus')->where('id', $menu->id)->update($payload);

            return (int) $menu->id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table('menus')->insertGetId($payload);
    }

    private function findMenuId(int $projectId, int|string|null $parentId, string $name, ?string $route): ?int
    {
        $menu = $this->findMenu($projectId, $parentId, $name, $route);

        return $menu ? (int) $menu->id : null;
    }

    private function findMenu(int $projectId, int|string|null $parentId, string $name, ?string $route): ?object
    {
        return DB::table('menus')
            ->where('project_id', $projectId)
            ->where('parent_id', $parentId)
            ->where('name', $name)
            ->when($route, fn ($query) => $query->where('route', $route), fn ($query) => $query->whereNull('route'))
            ->first();
    }

    private function menuPayload(
        int $projectId,
        int|string|null $parentId,
        string $name,
        ?string $route,
        int $sortOrder,
        ?string $icon,
        mixed $now
    ): array {
        $payload = [
            'parent_id' => $parentId,
            'project_id' => $projectId,
            'name' => $name,
            'icon' => $icon,
            'route' => $route,
            'sort_order' => $sortOrder,
            'updated_at' => $now,
        ];

        if (in_array('status', $this->menuColumns, true)) {
            $payload['status'] = 'show';
        }

        if (in_array('is_active', $this->menuColumns, true)) {
            $payload['is_active'] = true;
        }

        return array_intersect_key($payload, array_flip($this->menuColumns));
    }
}
