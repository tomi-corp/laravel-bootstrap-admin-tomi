<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                id="sidebarToggle"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-600 lg:hidden"
                aria-label="Toggle sidebar"
            >
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <p class="text-sm font-semibold text-slate-950">@yield('page-title', 'Lender')</p>
                <p class="hidden text-xs text-slate-500 sm:block">@yield('page-subtitle', 'Portal operasional lender')</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:bg-slate-100"
                title="Notifikasi"
            >
                <i class="fa-regular fa-bell"></i>
            </button>
            <div class="hidden items-center gap-3 rounded-md border border-slate-200 px-3 py-2 sm:flex">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700">
                    <i class="fa-regular fa-user"></i>
                </div>
                <div>
                    <p class="text-sm font-medium leading-none text-slate-950">Lender</p>
                    <p class="mt-1 text-xs leading-none text-slate-500">User</p>
                </div>
            </div>
        </div>
    </div>
</header>
