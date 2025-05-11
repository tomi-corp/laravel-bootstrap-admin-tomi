{{-- Left Sidebar Start --}}
<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                <li>
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="dripicons-meter"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title">Product</li>


                <li>
                    <a href="{{ route('admin.ppob.index') }}">
                        <i class=" dripicons-document"></i>
                        <span> PPOB </span>
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
