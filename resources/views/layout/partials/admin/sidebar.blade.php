{{-- Left Sidebar Start --}}
@php
$admin = Auth::guard('admin')->user();
$roleType = $admin?->type;

// Role yang hanya boleh melihat e-Kasbon dan Salary Financing
$limitedProductRole = in_array($roleType, ['pic', 'staff']);
@endphp

<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                <li>
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title">Product</li>

                {{-- PPOB hanya tampil selain pic dan staff --}}
                @if (!$limitedProductRole)
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
                @endif

                {{-- e-Kasbon tetap tampil untuk semua role, termasuk pic dan staff --}}
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-network-3"></i>
                        <span> e-Kasbon </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        <li>
                            <a href="javascript: void(0);" aria-expanded="false">
                                Perusahaan
                                <span class="menu-arrow"></span>
                            </a>

                            <ul class="nav-third-level nav" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.ksp.index') }}">List Perusahaan</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.ksp.invoicing.index') }}">List Invoice Perusahaan</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" aria-expanded="false">
                                Lender
                                <span class="menu-arrow"></span>
                            </a>

                            <ul class="nav-third-level nav" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.lender.index') }}">List Lender</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.lender.invoicing.index') }}">Invoice Lender</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('admin.cost.index') }}">Cost</a>
                        </li>

                        <li>
                            <a href="{{ route('admin.generate.invoice.index') }}">
                                <span> Generate Invoice </span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Salary Financing tetap tampil untuk semua role, termasuk pic dan staff --}}
                <li>
                    <a href="{{ route('admin.salary-financing.index') }}">
                        <i class="dripicons-wallet"></i>
                        <span> Salary Financing </span>
                    </a>
                </li>

                {{-- Menu di bawah ini hanya tampil selain pic dan staff --}}
                @if (!$limitedProductRole)

                <li>
                    <a href="#">
                        <i class="dripicons-briefcase"></i>
                        <span> Pinjaman Regular </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.cicilan-elektronik.index') }}">
                        <i class="dripicons-briefcase"></i>
                        <span> Cicilan Gedget </span>
                    </a>
                </li>

                <li class="menu-title">Daily</li>

                <li>
                    <a href="{{ route('admin.daily.index') }}">
                        <i class="dripicons-brightness-low"></i>
                        <span> Daily Checkin </span>
                    </a>
                </li>

                <li class="menu-title">Mobile</li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-view-apps"></i>
                        <span> Mobile </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.menumobile.index') }}"> Menu Mobile</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.ota.index') }}"> OTA Mobile</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sendnotif.index') }}"> Send Notification</a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title">Manage</li>

                <li>
                    <a href="{{ route('admin.manage.admin.index') }}">
                        <i class="dripicons-user-group"></i>
                        <span> Manage Admin </span>
                    </a>
                </li>

                @endif

                <li class="menu-title">BTA</li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-web"></i>
                        <span> Layanan Website </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.website.invoice.index') }}">
                                Invoice
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="clearfix"></div>
    </div>
</div>