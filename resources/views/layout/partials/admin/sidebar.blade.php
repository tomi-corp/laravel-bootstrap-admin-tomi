{{-- Left Sidebar Start --}}
<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                <li>
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class=" dripicons-align-center"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title">Product</li>

                {{-- 
                <li>
                    <a href="{{ route('admin.ppob.index') }}">
                        <i class=" dripicons-document"></i>
                        <span> PPOB </span>
                    </a>
                </li> --}}

                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-document"></i>
                        <span> PPOB </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.ppob.komisi') }}">Komisi</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.ppob.prepaid') }}">Transaksi Prabayar</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.ppob.postpaid') }}">Transaksi Pasca Bayar</a>
                        </li>

                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-network-3"></i>
                        <span> Pinjaman Payroll </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.ksp.index') }}">List Perusahaan</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.lender.index') }}">List Lender</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.cost.index') }}">Cost</a>
                        </li>

                    </ul>
                </li>

                {{-- <li>
                    <a href="javascript: void(0);">
                        <i class=" "></i>
                        <span> KSP DUMI </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a href="">List Perusahaan</a></li>
                        <li><a href="{{ route('admin.ksp.konfigurasi') }}">Konfigurasi Pinjaman</a></li>
                    </ul>
                </li> --}}

                <li class="menu-title">Daily</li>

                <li>
                    <a href="{{ route('admin.daily.index') }}">
                        <i class=" dripicons-brightness-low"></i>
                        <span> Daily Checkin </span>
                    </a>
                </li>


                <li class="menu-title">Menu Mobile</li>
                <li>
                    <a href="{{ route('admin.menumobile.index') }}">
                        <i class=" dripicons-view-apps"></i>
                        <span> Menu Mobile </span>
                    </a>
                </li>


                {{-- <li>
                    <a href="{{ route('regis.view') }}">
                        <i class="dripicons-user-group"></i>
                        <span> List Deposan </span>
                    </a>
                </li> --}}

                {{-- <li>
                    <a href="{{ route('bpr.view') }}">
                        <i class=" dripicons-store "></i>
                        <span> BPR Lender</span>
                    </a>
                </li> --}}

                {{-- <li>
                    <a href="{{ route('laporan-keuangan.bpr.view') }}">
                        <i class=" dripicons-document-remove  "></i>
                        <span> Laporan Keuangan </span>
                    </a>
                </li> --}}

                {{-- <li>
                    <a href="{{ route('proses.deposito.bpr.view') }}">
                        <i class=" dripicons-hourglass "></i>
                        <span> Proses Deposito</span>
                    </a>
                </li> --}}
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
