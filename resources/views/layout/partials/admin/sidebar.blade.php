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

    $normalizeMenuName = fn ($name) => strtolower(trim((string) $name));
    $tomiDanaMenu = $allMenus->first(fn ($menu) => $normalizeMenuName($menu->name) === 'tomi dana');

    if (! $tomiDanaMenu) {
        $tomiDanaMenu = (object) [
            'id' => -1010,
            'parent_id' => null,
            'project_id' => null,
            'feature_id' => null,
            'name' => 'Tomi Dana',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'route' => null,
            'sort_order' => 97,
            'status' => 'show',
        ];

        $allMenus = $allMenus->push($tomiDanaMenu)->values();
    }

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
        if (in_array($menu->route, ['admin.disbursements.index', 'admin.repayments.index'], true)) {
            $menu->parent_id = $tomiDanaMenu->id;
            $menu->project_id = $tomiDanaMenu->project_id;
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

        if (in_array('Tomi Dana', $names, true)) {
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
            echo '<ul class="nav-second-level" aria-expanded="false">';

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
