{{-- Sidebar Kiri Seller --}}

<div class="left-side-menu">

    <div class="slimscroll-menu">

        <div id="sidebar-menu">

            <ul class="metismenu" id="side-menu">

                {{-- DASHBOARD --}}
                <li class="menu-title">Menu Seller</li>

                <li>
                    <a href="{{ route('seller.dashboard') }}">
                        <i class="dripicons-meter"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                {{-- PRODUK --}}
                <li class="menu-title">Produk</li>

                {{-- CICILAN GADGET --}}
                <li>
                    <a href="javascript:void(0);">
                        <i class="dripicons-device-mobile"></i>
                        <span> Cicilan Gadget </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level">

                        <li>
                            <a href="{{ route('seller.gadget-installment.index') }}">
                                Daftar Produk
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                Tambah Produk
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- MANAJEMEN --}}
                <li class="menu-title">Manajemen</li>

                <li>
                    <a href="#">
                        <i class="dripicons-user"></i>
                        <span> Profil Toko </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="dripicons-cart"></i>
                        <span> Pesanan </span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="dripicons-graph-bar"></i>
                        <span> Laporan </span>
                    </a>
                </li>

                {{-- AKUN --}}
                <li class="menu-title">Akun</li>

                <li>
                    <form action="{{ route('seller.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-start w-100 ps-0">
                            <i class="dripicons-power"></i>
                            Keluar
                        </button>
                    </form>
                </li>

            </ul>

        </div>

        <div class="clearfix"></div>

    </div>

</div>