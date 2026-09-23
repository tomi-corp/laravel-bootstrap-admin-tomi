<header class="sticky top-0 z-20 border-b border-primary bg-primary" style="background-color: var(--lender-blue); border-color: var(--lender-blue);">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                id="sidebarToggle"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/30 text-white transition hover:bg-white/15"
                aria-label="Toggle sidebar"
            >
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/30 text-white transition hover:bg-white/15"
                title="Notifikasi"
            >
                <i class="fa-regular fa-bell"></i>
            </button>
            <div class="hidden items-center gap-3 rounded-md border border-white/30 px-3 py-2 sm:flex">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                    <i class="fa-regular fa-user"></i>
                </div>
                <div>
                    <p class="text-sm font-medium leading-none text-white">{{ auth('lender')->user()?->username ?? 'Lender' }}</p>
                    <p class="mt-1 text-xs leading-none text-white/80">Lender</p>
                </div>
            </div>
            <form action="{{ route('lender.logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/30 text-white transition hover:bg-white/15"
                    title="Logout"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>

