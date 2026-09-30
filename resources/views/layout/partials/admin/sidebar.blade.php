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

    $renderMenuItem = function ($menu) use (&$renderMenuItem, $menusByParent, $menuUrl, $hasStatusColumn, $statusLabels) {
        $children = $menusByParent->get($menu->id, collect());
        $hasChildren = $children->isNotEmpty();
        $icon = $menu->icon ?: 'fa-solid fa-circle';
        $status = $hasStatusColumn ? ($menu->status ?? 'show') : 'show';
        $isAvailable = $status === 'show';

        if ($hasChildren) {
            echo '<li>';
            echo '<a href="javascript: void(0);">';
            echo '<i class="'.e($icon).'"></i>';
            echo '<span> '.e($menu->name).' </span>';
            echo '<span class="menu-arrow"></span>';
            echo '</a>';
            echo '<ul class="nav-second-level" aria-expanded="false">';

            foreach ($children as $child) {
                $renderMenuItem($child);
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

        if (filled($menu->icon)) {
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
