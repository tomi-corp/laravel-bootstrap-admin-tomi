{{-- Left Sidebar Start --}}
@php
    $admin = Auth::guard('admin')->user();
    $roleType = $admin?->type;
    $limitedProductRole = in_array($roleType, ['pic', 'staff']);

    $menuColumns = \Illuminate\Support\Facades\Schema::getColumnListing('menus');
    $hasStatusColumn = in_array('status', $menuColumns, true);
    $hasIsActiveColumn = in_array('is_active', $menuColumns, true);

    $menusQuery = \Illuminate\Support\Facades\DB::table('menus')
        ->leftJoin('projects', 'projects.id', '=', 'menus.project_id')
        ->whereNull('menus.deleted_at')
        ->where('projects.code', 'internal')
        ->select('menus.*')
        ->orderBy('menus.parent_id')
        ->orderBy('menus.sort_order')
        ->orderBy('menus.name');

    if ($hasStatusColumn) {
        $menusQuery->where('menus.status', '!=', 'hidden');
    } elseif ($hasIsActiveColumn) {
        $menusQuery->where('menus.is_active', true);
    }

    $allMenus = $menusQuery->get();

    if (! $allMenus->contains('route', 'admin.roles.index') && \Illuminate\Support\Facades\Route::has('admin.roles.index')) {
        $managementMenu = $allMenus->firstWhere('name', 'Management');
        $fallbackRoleMenu = (object) [
            'id' => -1001,
            'parent_id' => $managementMenu?->id,
            'project_id' => $managementMenu?->project_id,
            'feature_id' => null,
            'name' => 'Peran',
            'icon' => 'fa-solid fa-user-shield',
            'route' => 'admin.roles.index',
            'sort_order' => 95,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackRoleMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    if (! $allMenus->contains('route', 'admin.role-menu-permissions.index') && \Illuminate\Support\Facades\Route::has('admin.role-menu-permissions.index')) {
        $managementMenu = $allMenus->firstWhere('name', 'Management');
        $fallbackRoleMenuPermissionMenu = (object) [
            'id' => -1002,
            'parent_id' => $managementMenu?->id,
            'project_id' => $managementMenu?->project_id,
            'feature_id' => null,
            'name' => 'Hak Akses Menu',
            'icon' => 'fa-solid fa-key',
            'route' => 'admin.role-menu-permissions.index',
            'sort_order' => 96,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackRoleMenuPermissionMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    $normalizeMenuName = function ($name) {
        $name = str_replace("\xc2\xa0", ' ', (string) $name);
        $name = preg_replace('/\s+/u', ' ', trim($name));

        return str_replace(' ', '', strtolower($name));
    };

    $tomiDanaMenu = $allMenus->first(fn ($menu) => $normalizeMenuName($menu->name) === 'tomidana');

    if (! $tomiDanaMenu) {
        $tomiDanaMenu = (object) [
            'id' => -1010,
            'parent_id' => null,
            'project_id' => null,
            'feature_id' => null,
            'name' => 'TomiDana',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'route' => null,
            'sort_order' => 97,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($tomiDanaMenu)->values();
    } else {
        $tomiDanaMenu->name = 'TomiDana';
    }

    $duplicateTomiDanaIds = $allMenus
        ->filter(fn ($menu) => $normalizeMenuName($menu->name) === 'tomidana' && $menu->id !== $tomiDanaMenu->id)
        ->pluck('id')
        ->all();

    if ($duplicateTomiDanaIds !== []) {
        $allMenus->each(function ($menu) use ($duplicateTomiDanaIds, $tomiDanaMenu) {
            if (in_array($menu->parent_id, $duplicateTomiDanaIds, true)) {
                $menu->parent_id = $tomiDanaMenu->id;
            }
        });

        $allMenus = $allMenus
            ->reject(fn ($menu) => in_array($menu->id, $duplicateTomiDanaIds, true))
            ->values();
    }

    $tomiDanaConfigurationMenu = $allMenus->first(
        fn ($menu) => $menu->parent_id === $tomiDanaMenu->id && $normalizeMenuName($menu->name) === 'konfigurasi'
    );

    if (! $tomiDanaConfigurationMenu) {
        $tomiDanaConfigurationMenu = (object) [
            'id' => -1011,
            'parent_id' => $tomiDanaMenu->id,
            'project_id' => $tomiDanaMenu->project_id,
            'feature_id' => null,
            'name' => 'Konfigurasi',
            'icon' => 'fa-solid fa-gear',
            'route' => null,
            'sort_order' => 95,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($tomiDanaConfigurationMenu)->values();
    } else {
        $tomiDanaConfigurationMenu->name = 'Konfigurasi';
        $tomiDanaConfigurationMenu->route = null;
        $tomiDanaConfigurationMenu->sort_order = 95;
    }

    $allMenus = $allMenus
        ->reject(fn ($menu) => $menu->id !== $tomiDanaConfigurationMenu->id && in_array($menu->route, [
            'admin.product-configurations.tenor.companies',
            'admin.product-configurations.dsr.companies',
        ], true))
        ->values();

    if (! $allMenus->contains('route', 'admin.disbursements.index') && \Illuminate\Support\Facades\Route::has('admin.disbursements.index')) {
        $fallbackDisbursementMenu = (object) [
            'id' => -1003,
            'parent_id' => $tomiDanaMenu->id,
            'project_id' => $tomiDanaMenu->project_id,
            'feature_id' => null,
            'name' => 'Pencairan Dana',
            'icon' => 'fa-solid fa-money-bill-transfer',
            'route' => 'admin.disbursements.index',
            'sort_order' => 97,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackDisbursementMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    if (\Illuminate\Support\Facades\Route::has('admin.product-configurations.tenor.companies')) {
        $fallbackTenorMenu = (object) [
            'id' => -1005,
            'parent_id' => $tomiDanaConfigurationMenu->id,
            'project_id' => $tomiDanaMenu->project_id,
            'feature_id' => null,
            'name' => 'Tenor',
            'icon' => 'fa-solid fa-sliders',
            'route' => 'admin.product-configurations.tenor.companies',
            'sort_order' => 1,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackTenorMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    if (\Illuminate\Support\Facades\Route::has('admin.product-configurations.dsr.companies')) {
        $fallbackDsrMenu = (object) [
            'id' => -1006,
            'parent_id' => $tomiDanaConfigurationMenu->id,
            'project_id' => $tomiDanaMenu->project_id,
            'feature_id' => null,
            'name' => 'DSR',
            'icon' => 'fa-solid fa-percent',
            'route' => 'admin.product-configurations.dsr.companies',
            'sort_order' => 2,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackDsrMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    if (! $allMenus->contains('route', 'admin.repayments.index') && \Illuminate\Support\Facades\Route::has('admin.repayments.index')) {
        $fallbackRepaymentMenu = (object) [
            'id' => -1004,
            'parent_id' => $tomiDanaMenu->id,
            'project_id' => $tomiDanaMenu->project_id,
            'feature_id' => null,
            'name' => 'Pembayaran',
            'icon' => 'fa-solid fa-receipt',
            'route' => 'admin.repayments.index',
            'sort_order' => 98,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($fallbackRepaymentMenu)->sortBy([
            ['parent_id', 'asc'],
            ['sort_order', 'asc'],
            ['name', 'asc'],
        ])->values();
    }

    $allMenus->each(function ($menu) use ($tomiDanaMenu) {
        if (in_array($menu->route, [
            'admin.disbursements.index',
            'admin.repayments.index',
        ], true)) {
            $menu->parent_id = $tomiDanaMenu->id;
            $menu->project_id = $tomiDanaMenu->project_id;
        }
    });

    $allMenus->each(function ($menu) use ($tomiDanaConfigurationMenu, $tomiDanaMenu) {
        if (in_array($menu->route, [
            'admin.product-configurations.tenor.companies',
            'admin.product-configurations.dsr.companies',
        ], true)) {
            $menu->parent_id = $tomiDanaConfigurationMenu->id;
            $menu->project_id = $tomiDanaMenu->project_id;
            $menu->name = $menu->route === 'admin.product-configurations.tenor.companies' ? 'Tenor' : 'DSR';
        }
    });

    $allMenus = $allMenus->sortBy([
        ['parent_id', 'asc'],
        ['sort_order', 'asc'],
        ['name', 'asc'],
    ])->values();

    if ($limitedProductRole) {
        $limitedHiddenRoots = ['PPOB', 'Daily', 'Mobile', 'Management'];
        $hiddenRootIds = $allMenus
            ->whereIn('name', $limitedHiddenRoots)
            ->pluck('id')
            ->all();

        $collectDescendants = function ($parentIds) use (&$collectDescendants, $allMenus) {
            $children = $allMenus->whereIn('parent_id', $parentIds)->pluck('id')->all();

            if ($children === []) {
                return [];
            }

            return array_merge($children, $collectDescendants($children));
        };

        $hiddenIds = array_merge($hiddenRootIds, $collectDescendants($hiddenRootIds));
        $allMenus = $allMenus->reject(fn ($menu) => in_array($menu->id, $hiddenIds, true))->values();
    }

    $menusByParent = $allMenus->groupBy(fn ($menu) => $menu->parent_id ?: 0);
    $menusById = $allMenus->keyBy('id');

    $productSlugFor = function ($menu) use ($menusById) {
        $names = [];
        $cursor = $menu;

        while ($cursor) {
            $names[] = $cursor->name;
            $cursor = $cursor->parent_id ? $menusById->get($cursor->parent_id) : null;
        }

        if (in_array('Tomi Dana Plus', $names, true)) {
            return 'tomi-dana-plus';
        }

        if (in_array('Tomi Dana', $names, true) || in_array('TomiDana', $names, true)) {
            return 'tomi-dana';
        }

        return null;
    };

    $menuUrl = function ($menu) use ($productSlugFor) {
        if (blank($menu->route) || ! \Illuminate\Support\Facades\Route::has($menu->route)) {
            return 'javascript: void(0);';
        }

        $params = [];

        if (in_array($menu->route, [
            'admin.product-configurations.tenor.companies',
            'admin.product-configurations.dsr.companies',
        ], true)) {
            $productSlug = $productSlugFor($menu);

            if (! $productSlug) {
                return 'javascript: void(0);';
            }

            $params[] = $productSlug;
        }

        return route($menu->route, $params);
    };

    $statusLabels = [
        'show' => 'tersedia',
        'develop' => 'sedang dikembangkan',
        'maintenance' => 'sedang maintenance',
        'coming_soon' => 'segera tersedia',
        'disabled' => 'dinonaktifkan',
    ];

    $renderMenuItem = function ($menu, int $level = 0) use (&$renderMenuItem, $menusByParent, $menuUrl, $hasStatusColumn, $statusLabels) {
        $children = $menusByParent->get($menu->id, collect());
        $hasChildren = $children->isNotEmpty();
        $icon = $menu->icon ?: 'fa-solid fa-circle';
        $status = $hasStatusColumn ? ($menu->status ?? 'show') : 'show';
        $isAvailable = $status === 'show';

        if ($hasChildren) {
            echo '<li>';
            echo '<a href="javascript: void(0);">';
            if ($level === 0) {
                echo '<i class="'.e($icon).'"></i>';
            }
            echo '<span> '.e($menu->name).' </span>';
            echo '<span class="menu-arrow"></span>';
            echo '</a>';
            $submenuClass = $level === 0 ? 'nav-second-level' : 'nav-third-level';
            echo '<ul class="'.$submenuClass.'" aria-expanded="false">';

            foreach ($children as $child) {
                $renderMenuItem($child, $level + 1);
            }

            echo '</ul>';
            echo '</li>';
            return;
        }

        echo '<li>';
        if ($isAvailable) {
            echo '<a href="'.e($menuUrl($menu)).'">';
        } else {
            $statusLabel = $statusLabels[$status] ?? str_replace('_', ' ', $status);
            echo '<a href="javascript: void(0);" class="js-sidebar-status-alert" data-menu-name="'.e($menu->name).'" data-status-label="'.e($statusLabel).'">';
        }

        if ($level === 0 && filled($menu->icon)) {
            echo '<i class="'.e($menu->icon).'"></i>';
            echo '<span> '.e($menu->name).' </span>';
        } else {
            echo e($menu->name);
        }

        echo '</a>';
        echo '</li>';
    };
@endphp

<div class="left-side-menu">
    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                @foreach ($menusByParent->get(0, collect()) as $menu)
                    @php
                        $children = $menusByParent->get($menu->id, collect());
                        $isCategory = blank($menu->icon) && blank($menu->route);
                    @endphp

                    @if ($isCategory)
                        @foreach ($children as $child)
                            @php $renderMenuItem($child); @endphp
                        @endforeach
                    @else
                        @php $renderMenuItem($menu); @endphp
                    @endif
                @endforeach
            </ul>
        </div>

        <div class="clearfix"></div>
    </div>
</div>

@push('js')
<script>
    $(document).on('click', '.js-sidebar-status-alert', function () {
        var menuName = $(this).data('menu-name');
        var statusLabel = $(this).data('status-label');
        var message = menuName + ' ' + statusLabel + '.';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: menuName,
                text: message,
                confirmButtonText: 'OK'
            });
            return;
        }

        alert(message);
    });
</script>
@endpush
