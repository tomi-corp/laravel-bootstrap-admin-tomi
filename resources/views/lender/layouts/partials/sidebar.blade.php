@php
    $menus = [
        [
            'label' => 'TomiiN',
            'icon' => 'fa-solid fa-house',
            'route' => 'lender.dashboard',
            'active' => request()->routeIs('lender.dashboard'),
            'development' => true,
        ],
        [
            'label' => 'TomiDana',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'route' => 'lender.dashboard.summary',
            'active' => request()->routeIs('lender.dashboard.summary'),
            'development' => false,
        ],
        [
            'label' => 'TomiDana',
            'suffix' => '+',
            'icon' => 'fa-solid fa-circle-plus',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
        [
            'label' => 'TomiGawai',
            'icon' => 'fa-solid fa-mobile-screen-button',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
        [
            'label' => 'TomiEmas',
            'icon' => 'fa-solid fa-coins',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
        [
            'label' => 'TomiGaji',
            'icon' => 'fa-solid fa-money-check-dollar',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
        [
            'label' => 'TomiInvoice',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
        [
            'label' => 'PPOB',
            'icon' => 'fa-solid fa-receipt',
            'route' => 'lender.dashboard.summary',
            'active' => false,
            'development' => true,
        ],
    ];
@endphp

<aside
    id="lenderSidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-primary bg-primary transition-transform lg:translate-x-0"
    style="background-color: #0B8FE8; border-color: #0B8FE8;"
>
    <div class="flex h-16 items-center gap-3 border-b border-white/20 px-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 text-white shadow-sm shadow-primary-950/20">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-white">TOMI Lender</p>
            <p class="text-xs text-white/80">Funding Portal</p>
        </div>
    </div>

    <nav class="space-y-1 px-3 py-4">
        @foreach ($menus as $menu)
            <a
                href="{{ $menu['development'] ? '#' : route($menu['route']) }}"
                class="{{ $menu['development'] ? 'js-development-menu' : '' }} flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ $menu['active'] ? 'bg-white text-primary shadow-sm shadow-primary-950/20' : 'text-white hover:bg-white/15 hover:text-white' }}"
                data-menu-label="{{ $menu['label'] }}{{ $menu['suffix'] ?? '' }}"
            >
                <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                <span>
                    {{ $menu['label'] }}@isset($menu['suffix'])<sup class="text-[10px] leading-none">{{ $menu['suffix'] }}</sup>@endisset
                </span>
            </a>
        @endforeach
    </nav>
</aside>

<div
    id="sidebarBackdrop"
    class="fixed inset-0 z-30 hidden bg-primary-950/40 lg:hidden"
></div>

@push('scripts')
    <script>
        $('.js-development-menu').on('click', function (event) {
            event.preventDefault();

            AppAlert.fire({
                icon: 'info',
                title: 'Sedang dalam pengembangan',
                text: `${$(this).data('menu-label')} belum tersedia saat ini.`,
                confirmButtonColor: '#0B8FE8',
                confirmButtonText: 'Mengerti',
            });
        });
    </script>
@endpush
