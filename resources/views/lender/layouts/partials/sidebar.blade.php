<div class="left-side-menu">
    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Navigasi Lender</li>
                <li><a href="{{ route('lender.dashboard.index') }}"><i class="dripicons-align-center"></i><span>Dasbor</span></a></li>

                <li class="menu-title">Produk</li>
                @foreach ([
                    ['slug' => 'tomi-dana', 'label' => 'TomiDana', 'icon' => 'dripicons-network-3'],
                    ['slug' => 'tomi-dana-plus', 'label' => 'TomiDana Plus', 'icon' => 'dripicons-network-3'],
                    ['slug' => 'tomi-gawai', 'label' => 'TomiGawai', 'icon' => 'dripicons-device-mobile'],
                    ['slug' => 'tomi-gaji', 'label' => 'TomiGaji', 'icon' => 'dripicons-wallet'],
                    ['slug' => 'tomi-invoice', 'label' => 'TomiInvoice', 'icon' => 'dripicons-document'],
                ] as $product)
                    <li>
                        <a href="{{ route('lender.' . $product['slug'] . '.index') }}">
                            <i class="{{ $product['icon'] }}"></i><span>{{ $product['label'] }}</span>
                        </a>
                    </li>
                @endforeach

                <li class="menu-title">Akun</li>
                <li><a href="{{ route('lender.settings.index') }}"><i class="dripicons-gear"></i><span>Pengaturan</span></a></li>
                <li><a href="{{ route('lender.accounts.index') }}"><i class="dripicons-wallet"></i><span>Rekening</span></a></li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
