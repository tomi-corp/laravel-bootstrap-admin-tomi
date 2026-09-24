<header class="sticky top-0 z-20 border-b border-sky-500 bg-sky-500">
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
            <div class="relative">
                <button
                    type="button"
                    id="lenderUserMenuButton"
                    class="hidden items-center gap-3 rounded-md border border-white/30 px-3 py-2 text-left transition hover:bg-white/15 sm:flex"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium leading-none text-white">{{ auth('lender')->user()?->username ?? 'Lender' }}</p>
                        <p class="mt-1 text-xs leading-none text-white/80">Lender</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-white/80"></i>
                </button>

                <div
                    id="lenderUserMenu"
                    class="absolute right-0 top-full z-30 mt-2 hidden w-56 overflow-hidden rounded-md border border-sky-100 bg-white py-1 text-slate-950 shadow-lg shadow-slate-950/20"
                >
                    <button type="button" class="js-lender-profile-action flex w-full items-center gap-3 px-4 py-2 text-left text-sm hover:bg-sky-50" data-title="Ubah Profile">
                        <i class="fa-regular fa-user w-4 text-center text-sky-700"></i>
                        Ubah Profile
                    </button>
                    <button type="button" class="js-lender-profile-action flex w-full items-center gap-3 px-4 py-2 text-left text-sm hover:bg-sky-50" data-title="Ubah Password">
                        <i class="fa-solid fa-key w-4 text-center text-sky-700"></i>
                        Ubah Password
                    </button>
                    <button type="button" class="js-lender-profile-action flex w-full items-center gap-3 px-4 py-2 text-left text-sm hover:bg-sky-50" data-title="Ubah Email">
                        <i class="fa-regular fa-envelope w-4 text-center text-sky-700"></i>
                        Ubah Email
                    </button>
                    <div class="my-1 border-t border-sky-100"></div>
                    <form action="{{ route('lender.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        const lenderUserMenuButton = document.getElementById('lenderUserMenuButton');
        const lenderUserMenu = document.getElementById('lenderUserMenu');

        lenderUserMenuButton?.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = !lenderUserMenu.classList.contains('hidden');

            lenderUserMenu.classList.toggle('hidden', isOpen);
            lenderUserMenuButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });

        document.addEventListener('click', function (event) {
            if (! lenderUserMenu || lenderUserMenu.classList.contains('hidden')) {
                return;
            }

            if (! lenderUserMenu.contains(event.target) && ! lenderUserMenuButton.contains(event.target)) {
                lenderUserMenu.classList.add('hidden');
                lenderUserMenuButton.setAttribute('aria-expanded', 'false');
            }
        });

        $('.js-lender-profile-action').on('click', function () {
            lenderUserMenu.classList.add('hidden');
            lenderUserMenuButton.setAttribute('aria-expanded', 'false');

            AppAlert.fire({
                icon: 'info',
                title: $(this).data('title'),
                text: 'Fitur ini belum tersedia saat ini.',
                                confirmButtonText: 'Mengerti',
            });
        });
    </script>
@endpush
