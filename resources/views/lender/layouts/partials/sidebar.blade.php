@php
    $menus = [
        [
            'label' => 'Dashboard',
            'icon' => 'fa-solid fa-gauge-high',
            'route' => 'lender.dashboard',
            'active' => request()->routeIs('lender.dashboard'),
            'development' => false,
        ],
        [
            'label' => 'List Perusahaan',
            'icon' => 'fa-solid fa-building',
            'route' => 'lender.companies.index',
            'active' => request()->routeIs('lender.companies.*'),
            'development' => false,
        ],
        [
            'label' => 'TomiDana',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'active' => request()->routeIs('lender.tomi-dana.*') || request()->routeIs('lender.loans.*') || request()->routeIs('lender.disbursements.*') || request()->routeIs('lender.repayments.*') || request()->routeIs('lender.configurations.*') || request()->routeIs('lender.loan-amounts.*'),
            'children' => [
                [
                    'label' => 'Konfigurasi',
                    'icon' => 'fa-solid fa-sliders',
                    'route' => 'lender.configurations.index',
                    'active' => request()->routeIs('lender.configurations.*'),
                ],
                [
                    'label' => 'Nominal Pinjaman',
                    'icon' => 'fa-solid fa-money-bill-wave',
                    'route' => 'lender.loan-amounts.index',
                    'active' => request()->routeIs('lender.loan-amounts.*'),
                ],
                [
                    'label' => 'Pencairan Dana',
                    'icon' => 'fa-solid fa-money-bill-transfer',
                    'route' => 'lender.disbursements.index',
                    'active' => request()->routeIs('lender.disbursements.*'),
                ],
                [
                    'label' => 'Pengembalian Dana',
                    'icon' => 'fa-solid fa-rotate-left',
                    'route' => 'lender.repayments.index',
                    'active' => request()->routeIs('lender.repayments.*'),
                ],
            ],
        ],
        [
            'label' => 'TomiDana',
            'suffix' => '+',
            'icon' => 'fa-solid fa-circle-plus',
            'development' => true,
        ],
        [
            'label' => 'TomiGawai',
            'icon' => 'fa-solid fa-mobile-screen-button',
            'development' => true,
        ],
        [
            'label' => 'TomiEmas',
            'icon' => 'fa-solid fa-coins',
            'development' => true,
        ],
        [
            'label' => 'TomiGaji',
            'icon' => 'fa-solid fa-money-check-dollar',
            'development' => true,
        ],
        [
            'label' => 'TomiInvoice',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'development' => true,
        ],
        [
            'label' => 'TomiHaji',
            'icon' => 'fa-solid fa-kaaba',
            'development' => true,
        ],
        [
            'label' => 'TomiUmroh',
            'icon' => 'fa-solid fa-mosque',
            'development' => true,
        ],
        [
            'label' => 'TomiMoto',
            'icon' => 'fa-solid fa-motorcycle',
            'development' => true,
        ],
        [
            'label' => 'TomiAuto',
            'icon' => 'fa-solid fa-car-side',
            'development' => true,
        ],
        [
            'label' => 'PPOB',
            'icon' => 'fa-solid fa-receipt',
            'development' => true,
        ],
    ];
@endphp

<aside
    id="lenderSidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-black bg-black transition-all lg:translate-x-0"
>
    <div class="flex h-14 shrink-0 items-center gap-3 border-b border-white/20 px-4">
        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-white/20 text-white shadow-sm shadow-slate-950/20">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div class="sidebar-label">
            <p class="text-sm font-semibold text-white">TOMI Lender</p>
            <p class="text-xs text-white/80">Portal Pendanaan</p>
        </div>
    </div>

    <nav class="min-h-0 flex-1 space-y-0.5 overflow-visible px-2 py-2">
        @foreach ($menus as $menu)
            @if (isset($menu['children']))
                @php
                    $submenuId = 'submenu-'.\Illuminate\Support\Str::slug($menu['label']);
                @endphp
                <div>
                    <button
                        type="button"
                        class="js-submenu-toggle flex w-full items-center justify-between rounded-md px-3 py-1.5 text-sm font-medium text-white transition hover:bg-white/15"
                        data-target="{{ $submenuId }}"
                        aria-expanded="{{ $menu['active'] ? 'true' : 'false' }}"
                    >
                        <span class="flex items-center gap-3">
                            <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                            <span class="sidebar-label">{{ $menu['label'] }}</span>
                        </span>
                        <i class="sidebar-label fa-solid fa-chevron-down text-xs text-white/80 transition-transform {{ $menu['active'] ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div id="{{ $submenuId }}" class="sidebar-submenu {{ $menu['active'] ? '' : 'hidden' }} mt-0.5 space-y-0.5 pl-7">
                        @foreach ($menu['children'] as $child)
                            <a
                                href="{{ route($child['route']) }}"
                                class="flex items-center gap-3 rounded-md px-3 py-1.5 text-xs font-medium transition {{ $child['active'] ? 'bg-white/25 text-white shadow-sm shadow-slate-950/10' : 'text-slate-600 hover:bg-white/15 hover:text-white' }}"
                            >
                                <i class="{{ $child['icon'] }} w-4 text-center"></i>
                                <span class="sidebar-label">{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a
                    href="{{ ($menu['development'] ?? false) ? '#' : route($menu['route']) }}"
                    class="{{ ($menu['development'] ?? false) ? 'js-development-menu' : '' }} flex items-center gap-3 rounded-md px-3 py-1.5 text-sm font-medium transition {{ ($menu['active'] ?? false) ? 'bg-white/25 text-white shadow-sm shadow-slate-950/10' : 'text-white hover:bg-white/15 hover:text-white' }}"
                    data-menu-label="{{ $menu['label'] }}{{ $menu['suffix'] ?? '' }}"
                >
                    <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                    <span class="sidebar-label">
                        {{ $menu['label'] }}@isset($menu['suffix'])<sup class="text-[10px] leading-none">{{ $menu['suffix'] }}</sup>@endisset
                    </span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>

<div
    id="sidebarBackdrop"
    class="fixed inset-0 z-30 hidden bg-slate-950/40 lg:hidden"
></div>

@push('scripts')
    <script>
        $('.js-development-menu').on('click', function (event) {
            event.preventDefault();

            AppAlert.fire({
                icon: 'info',
                title: 'Sedang dalam pengembangan',
                text: `${$(this).data('menu-label')} belum tersedia saat ini.`,
                                confirmButtonText: 'Mengerti',
            });
        });

        $('.js-submenu-toggle').on('click', function () {
            const targetId = $(this).data('target');
            const target = $('#' + targetId);
            const icon = $(this).find('.fa-chevron-down');
            const isOpen = !target.hasClass('hidden');

            target.toggleClass('hidden', isOpen);
            icon.toggleClass('rotate-180', !isOpen);
            $(this).attr('aria-expanded', isOpen ? 'false' : 'true');
        });
    </script>
@endpush

