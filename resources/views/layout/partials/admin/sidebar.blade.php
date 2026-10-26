{{-- Left Sidebar Start --}}
@php
$admin = Auth::guard('admin')->user();
$roleType = $admin?->type;
$accountCategory = $admin?->account_category ?? 'internal';
$isInternalAccount = $accountCategory === 'internal';
$isLenderAccount = $accountCategory === 'lender';
$isCompanyAccount = $accountCategory === 'company';

// Role yang hanya boleh melihat e-Kasbon dan Salary Financing
$limitedProductRole = in_array($roleType, ['pic', 'staff']);
@endphp

<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigasi</li>

                <li>
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Dasbor </span>
                    </a>
                </li>

                <li class="menu-title">Produk</li>

                {{-- PPOB hanya tampil selain pic dan staff --}}
                @if ($isInternalAccount && !$limitedProductRole)
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

                {{-- Perusahaan dan Lender dipakai lintas produk --}}
                @if ($isInternalAccount || $isCompanyAccount)
                <li>
                    <a href="{{ route('admin.ksp.index') }}">
                        <i class="dripicons-store"></i>
                        <span> Perusahaan </span>
                    </a>
                </li>
                @endif

                @if ($isInternalAccount)
                <li>
                    <a href="{{ route('admin.lender.index') }}">
                        <i class="dripicons-user-group"></i>
                        <span> Pemberi Pinjaman </span>
                    </a>
                </li>
                @endif

                @if ($isLenderAccount)
                <li>
                    <a href="javascript: void(0);" onclick="alert('Menu sedang dalam pengembangan.')">
                        <i class="dripicons-network-3"></i>
                        <span>TomiDana</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" onclick="alert('Menu sedang dalam pengembangan.')">
                        <i class="dripicons-network-3"></i>
                        <span>TomiDana Plus</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" onclick="alert('Menu sedang dalam pengembangan.')">
                        <i class="dripicons-device-mobile"></i>
                        <span>TomiGawai</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" onclick="alert('Menu sedang dalam pengembangan.')">
                        <i class="dripicons-wallet"></i>
                        <span>TomiGaji</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" onclick="alert('Menu sedang dalam pengembangan.')">
                        <i class="dripicons-document"></i>
                        <span>TomiInvoice</span>
                    </a>
                </li>
                @endif

                {{-- Tomi Dana tampil sesuai kategori organisasi akun --}}
                @if ($isInternalAccount || $isCompanyAccount)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-network-3"></i>
                        <span> Tomi Dana </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        @if ($isInternalAccount)
                        {{-- Menu disembunyikan sementara, bukan dihapus. --}}
                        {{--
                        <li>
                            <a href="{{ route('admin.cost.index') }}">Biaya</a>
                        </li>
                        --}}
                        <li>
                            <a href="{{ route('admin.ksp.index') }}">Perusahaan</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.lender.index') }}">Pemberi Pinjaman</a>
                        </li>
                        @endif

                        @if ($isInternalAccount || $isCompanyAccount)
                        {{-- Menu disembunyikan sementara, bukan dihapus. --}}
                        {{--
                        <li>
                            <a href="{{ route('admin.ksp.invoicing.index') }}">Daftar Tagihan Perusahaan</a>
                        </li>
                        --}}
                        @endif

                        @if ($isInternalAccount)
                        <li>
                            <a href="{{ route('admin.lender.invoicing.index') }}">Tagihan Pemberi Pinjaman</a>
                        </li>
                        @endif

                        @if ($isInternalAccount)
                        {{-- Menu disembunyikan sementara, bukan dihapus. --}}
                        {{--
                        <li>
                            <a href="{{ route('admin.generate.invoice.index') }}">
                                <span> Buat Tagihan </span>
                            </a>
                        </li>
                        --}}
                        <li>
                            <a href="{{ route('admin.ksp.konfigurasi.index') }}">
                                Konfigurasi
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pembayaran Angsuran sedang dalam pengembangan.')">
                                Pembayaran Angsuran
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pencairan sedang dalam pengembangan.')">
                                Pencairan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- TomiDana Plus tampil sesuai kategori organisasi akun --}}
                @if ($isInternalAccount || $isCompanyAccount)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-network-3"></i>
                        <span> TomiDana Plus </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Perusahaan TomiDana Plus sedang dalam pengembangan.')">
                                Perusahaan
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pemberi Pinjaman TomiDana Plus sedang dalam pengembangan.')">
                                Pemberi Pinjaman
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Konfigurasi TomiDana Plus sedang dalam pengembangan.')">
                                Konfigurasi
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pembayaran Angsuran TomiDana Plus sedang dalam pengembangan.')">
                                Pembayaran Angsuran
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pencairan TomiDana Plus sedang dalam pengembangan.')">
                                Pencairan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- TomiGaji --}}
                @if ($isInternalAccount || $isCompanyAccount)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-wallet"></i>
                        <span>TomiGaji</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.salary-financing.companies') }}">Perusahaan</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.salary-financing.lenders') }}">Pemberi Pinjaman</a>
                        </li>
                        @if ($isInternalAccount)
                        <li>
                            <a href="{{ route('admin.salary-financing.configuration') }}">
                                Konfigurasi
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pembayaran Angsuran sedang dalam pengembangan.')">
                                Pembayaran Angsuran
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pencairan sedang dalam pengembangan.')">
                                Pencairan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- TomiGawai --}}
                @if ($isInternalAccount || $isCompanyAccount)
                @if ($isInternalAccount)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-device-mobile"></i>
                        <span>TomiGawai</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.ksp.index') }}">Perusahaan</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.lender.index') }}">Pemberi Pinjaman</a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu konfigurasi sedang dalam pengembangan.')">
                                Konfigurasi
                            </a>
                        </li>
                    </ul>
                </li>
                @else
                <li>
                    <a href="{{ route('admin.gadget-installment.index') }}">
                        <i class="dripicons-device-mobile"></i>
                        <span>TomiGawai</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- TomiInvoice --}}
                @if ($isInternalAccount || $isCompanyAccount)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-document"></i>
                        <span>TomiInvoice</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level nav" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.invoice-financing.companies') }}">Perusahaan</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoice-financing.lenders') }}">Pemberi Pinjaman</a>
                        </li>
                        @if ($isInternalAccount)
                        <li>
                            <a href="{{ route('admin.invoice-financing.configuration') }}">
                                Konfigurasi
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pembayaran Angsuran sedang dalam pengembangan.')">
                                Pembayaran Angsuran
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);" onclick="alert('Menu Pencairan sedang dalam pengembangan.')">
                                Pencairan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Menu di bawah ini hanya tampil selain pic dan staff --}}
                @if ($isInternalAccount && !$limitedProductRole)

                <!-- <li>
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
                </li> -->

                <li class="menu-title">Harian</li>

                <li>
                    <a href="{{ route('admin.daily.index') }}">
                        <i class="dripicons-brightness-low"></i>
                        <span> Presensi Harian </span>
                    </a>
                </li>

                <li class="menu-title">Seluler</li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-view-apps"></i>
                        <span> Seluler </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.menumobile.index') }}"> Menu Seluler</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.ota.index') }}"> Pembaruan OTA Seluler</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sendnotif.index') }}"> Kirim Notifikasi</a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title">Manajemen</li>

                <li>
                    <a href="{{ route('admin.manage.admin.index') }}">
                        <i class="dripicons-user-group"></i>
                        <span> Kelola Admin </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.manage.pic-lender.index') }}">
                        <i class="dripicons-user-id"></i>
                        <span> PIC Pemberi Pinjaman </span>
                    </a>
                </li>

                @endif

                @if ($isInternalAccount)
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
                                Tagihan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
