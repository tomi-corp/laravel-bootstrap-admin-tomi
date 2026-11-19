@extends('layout.shell')
@section('title', 'Dashboard')
@section('crumbs', 'Internal')
@section('page-title', 'Dashboard')

{{--
    Tampilan awal dashboard internal. Semua angka di bawah masih data contoh (statis)
    dan akan diganti data asli setelah endpoint-nya tersedia.
--}}
@php
    $num = fn ($value) => number_format($value, 0, ',', '.');
    $rp = fn ($value) => 'Rp. '.number_format($value, 0, ',', '.');

    $stats = [
        ['label' => 'Total Perusahaan Aktif', 'value' => $num(128), 'delta' => '12%', 'good' => true, 'sub' => 'dari bulan lalu', 'icon' => 'bank', 'tone' => 'var(--s1)'],
        ['label' => 'Total Pengajuan Hari Ini', 'value' => $num(42), 'delta' => '35%', 'good' => true, 'sub' => 'dari kemarin', 'icon' => 'table', 'tone' => 'var(--s3)'],
        ['label' => 'Transaksi Bulan Ini', 'value' => $rp(1240000000), 'delta' => '18%', 'good' => true, 'sub' => 'dari bulan lalu', 'icon' => 'bolt', 'tone' => 'var(--s4)'],
        ['label' => 'Tugas Perlu Tindakan', 'value' => $num(22), 'delta' => '19%', 'good' => false, 'sub' => 'dari kemarin', 'icon' => 'check', 'tone' => 'var(--crit-fill)'],
    ];

    $alerts = [
        ['value' => 12, 'label' => 'Menunggu Verifikasi', 'icon' => 'table', 'cls' => 'crit'],
        ['value' => 5, 'label' => 'Mendekati Jatuh Tempo', 'icon' => 'clock', 'cls' => 'warn'],
        ['value' => 3, 'label' => 'Data Perlu Revisi', 'icon' => 'info', 'cls' => 'info'],
    ];

    $products = [
        ['name' => 'TomiDana', 'slot' => 1, 'total' => 245, 'pending' => 36, 'approved' => 186],
        ['name' => 'TomiGaji', 'slot' => 3, 'total' => 189, 'pending' => 21, 'approved' => 158],
        ['name' => 'TomiGawai', 'slot' => 4, 'total' => 98, 'pending' => 14, 'approved' => 76],
        ['name' => 'TomiInvoice', 'slot' => 2, 'total' => 76, 'pending' => 11, 'approved' => 60],
    ];

    $operations = [
        ['label' => 'Pengajuan Baru', 'value' => 42, 'sub' => 'pengajuan masuk', 'icon' => 'table', 'cls' => 'info'],
        ['label' => 'Verifikasi Selesai', 'value' => 28, 'sub' => 'pengajuan diverifikasi', 'icon' => 'check', 'cls' => 'good'],
        ['label' => 'Pembayaran Berhasil', 'value' => 18, 'sub' => 'transaksi berhasil', 'icon' => 'bolt', 'cls' => 'info'],
        ['label' => 'MoU Aktif', 'value' => 6, 'sub' => 'perusahaan baru', 'icon' => 'users', 'cls' => 'warn'],
    ];

    $distribution = [
        ['name' => 'TomiDana', 'slot' => 1, 'value' => 245, 'status' => 'Aktif', 'cls' => 'good'],
        ['name' => 'TomiGaji', 'slot' => 3, 'value' => 189, 'status' => 'Aktif', 'cls' => 'good'],
        ['name' => 'TomiGawai', 'slot' => 4, 'value' => 98, 'status' => 'Aktif', 'cls' => 'good'],
        ['name' => 'TomiInvoice', 'slot' => 2, 'value' => 76, 'status' => 'Normal', 'cls' => 'info'],
    ];
    $maxDistribution = max(array_column($distribution, 'value'));

    $activities = [
        ['time' => '10:24', 'user' => 'Andi Prasetyo', 'hue' => 210, 'activity' => 'Mengajukan pinjaman perusahaan', 'product' => 'TomiDana', 'status' => 'Pending', 'cls' => 'warn'],
        ['time' => '09:18', 'user' => 'Maya Nurhasanah', 'hue' => 280, 'activity' => 'Verifikasi data pengajuan', 'product' => 'TomiGaji', 'status' => 'Disetujui', 'cls' => 'good'],
        ['time' => '08:56', 'user' => 'Budi Santoso', 'hue' => 160, 'activity' => 'Upload dokumen tambahan', 'product' => 'TomiGawai', 'status' => 'Perlu Revisi', 'cls' => 'crit'],
        ['time' => '08:32', 'user' => 'Dewi Rahayu', 'hue' => 25, 'activity' => 'Pembayaran berhasil', 'product' => 'TomiInvoice', 'status' => 'Selesai', 'cls' => 'good'],
        ['time' => '08:12', 'user' => 'Rizky Darmawan', 'hue' => 190, 'activity' => 'Mengajukan MoU kerjasama', 'product' => 'TomiDana', 'status' => 'Pending', 'cls' => 'warn'],
    ];
    $initials = fn ($name) => collect(explode(' ', $name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
@endphp

@section('content')
    <div class="-mt-1 mb-5 flex flex-wrap items-center gap-3">
        <p class="section-sub">Ringkasan aktivitas dan performa seluruh produk TOMI dalam satu tampilan</p>
        <a href="{{ route('admin.dashboard.export-report') }}" class="ui-btn primary ml-auto" aria-label="Export report dashboard">
            <svg class="i !h-4 !w-4"><use href="#i-table"/></svg>Export Report
        </a>
    </div>

    {{-- ===================== Statistik utama ===================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $i => $stat)
            <section class="panel panel-pad fade-in flex items-start gap-4" style="--i: {{ $i }}">
                <span class="grid h-12 w-12 flex-none place-items-center rounded-[12px]"
                      style="color: {{ $stat['tone'] }}; background: color-mix(in srgb, {{ $stat['tone'] }} 12%, transparent)">
                    <svg class="i !h-6 !w-6"><use href="#i-{{ $stat['icon'] }}"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="kpi-label">{{ $stat['label'] }}</div>
                    <div class="figure-lg num mt-1 truncate">{{ $stat['value'] }}</div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px]">
                        <span class="badge {{ $stat['good'] ? 'good' : 'crit' }}">&uarr; {{ $stat['delta'] }}</span>
                        <span class="text-muted">{{ $stat['sub'] }}</span>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- ===================== Perlu perhatian ===================== --}}
    <section class="panel mt-4 grid gap-3 p-4 fade-in lg:grid-cols-[minmax(0,1.1fr)_repeat(3,minmax(0,1fr))] lg:items-center"
             style="--i: 4; border-color: color-mix(in srgb, var(--crit-fill) 25%, var(--line)); background: color-mix(in srgb, var(--crit-bg) 55%, var(--surface))">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 flex-none place-items-center rounded-full" style="background: var(--crit-fill); color: #fff">
                <svg class="i !h-5 !w-5"><use href="#i-alert"/></svg>
            </span>
            <div class="min-w-0">
                <div class="text-[16px] font-semibold" style="color: var(--crit)">Perlu Perhatian</div>
                <div class="text-[12.5px] text-ink-2">Terdapat beberapa hal yang memerlukan tindakan Anda</div>
            </div>
        </div>
        @foreach ($alerts as $alert)
            <a href="#" class="panel flex items-center gap-3 px-4 py-3 transition hover:border-[color:var(--brand)]">
                <span class="badge {{ $alert['cls'] }} !h-9 !w-9 flex-none justify-center !rounded-[10px] !p-0">
                    <svg class="i !h-[18px] !w-[18px]"><use href="#i-{{ $alert['icon'] }}"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="num text-[18px] font-semibold leading-tight">{{ $num($alert['value']) }}</div>
                    <div class="truncate text-[12.5px] text-ink-2">{{ $alert['label'] }}</div>
                </div>
                <svg class="i !h-4 !w-4 text-muted"><use href="#i-chevron"/></svg>
            </a>
        @endforeach
    </section>

    {{-- ===================== Ringkasan produk ===================== --}}
    <section class="panel panel-pad mt-4 fade-in" style="--i: 5">
        <div class="panel-title">Ringkasan Produk</div>
        <div class="panel-sub">Performa utama dari seluruh produk TOMI</div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($products as $product)
                <a href="#" class="tile panel block">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] text-[13px] font-bold text-white" style="background: var(--s{{ $product['slot'] }})">{{ mb_substr($product['name'], 4, 1) }}</span>
                        <span class="text-[14px] font-semibold">{{ $product['name'] }}</span>
                        <svg class="i ml-auto !h-4 !w-4" style="color: var(--brand-ink)"><use href="#i-chevron"/></svg>
                    </div>
                    <div class="mt-4 grid grid-cols-3 divide-x divide-[color:var(--line)] text-center">
                        @foreach (['total' => 'Total Pengajuan', 'pending' => 'Pending', 'approved' => 'Disetujui'] as $key => $label)
                            <div class="px-1">
                                <div class="num text-[17px] font-semibold">{{ $num($product[$key]) }}</div>
                                <div class="text-[11.5px] text-muted">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)]">
        {{-- ===================== Operasional hari ini ===================== --}}
        <section class="panel panel-pad fade-in" style="--i: 6">
            <div class="panel-title">Ringkasan Operasional Hari Ini</div>
            <div class="panel-sub">Informasi utama aktivitas operasional hari ini</div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                @foreach ($operations as $operation)
                    <div class="rounded-[12px] p-4" style="background: var(--surface-2)">
                        <div class="flex items-center gap-2.5">
                            <span class="badge {{ $operation['cls'] }} !h-9 !w-9 flex-none justify-center !rounded-[10px] !p-0">
                                <svg class="i !h-[18px] !w-[18px]"><use href="#i-{{ $operation['icon'] }}"/></svg>
                            </span>
                            <span class="text-[12.5px] font-medium text-ink-2">{{ $operation['label'] }}</span>
                        </div>
                        <div class="figure-lg num mt-3">{{ $num($operation['value']) }}</div>
                        <div class="mt-0.5 text-[11.5px] text-muted">{{ $operation['sub'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ===================== Distribusi produk ===================== --}}
        <section class="panel panel-pad fade-in" style="--i: 7">
            <div class="flex items-start gap-3">
                <div>
                    <div class="panel-title">Distribusi Produk</div>
                    <div class="panel-sub">Jumlah aktivitas setiap produk hari ini</div>
                </div>
                <a href="#" class="link ml-auto inline-flex items-center gap-1 text-[12.5px] font-medium">Lihat Semua<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="tbl">
                    <thead><tr><th>No</th><th>Produk</th><th>Jumlah Aktivitas</th><th class="r">Status</th></tr></thead>
                    <tbody>
                        @foreach ($distribution as $i => $item)
                            <tr>
                                <td class="num text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-[3px]" style="background: var(--s{{ $item['slot'] }})"></span>
                                        <span class="whitespace-nowrap">{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td class="min-w-[140px]">
                                    <div class="flex items-center gap-2.5">
                                        <span class="num w-8 font-semibold">{{ $num($item['value']) }}</span>
                                        <div class="track flex-1"><span style="width: {{ round($item['value'] / $maxDistribution * 100) }}%; background: var(--s{{ $item['slot'] }})"></span></div>
                                    </div>
                                </td>
                                <td class="r"><span class="badge {{ $item['cls'] }}">{{ $item['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- ===================== Aktivitas terbaru ===================== --}}
    <section class="panel panel-pad mt-4 fade-in" style="--i: 8">
        <div class="flex items-start gap-3">
            <div>
                <div class="panel-title">Aktivitas / Tugas Terbaru</div>
                <div class="panel-sub">Daftar aktivitas dan tugas yang perlu Anda ketahui</div>
            </div>
            <a href="#" class="link ml-auto inline-flex items-center gap-1 text-[12.5px] font-medium">Lihat Semua<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="tbl">
                <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Produk</th><th>Status</th><th class="r">Aksi</th></tr></thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            <td class="num whitespace-nowrap text-ink-2">{{ $activity['time'] }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <span class="avatar !h-7 !w-7 !rounded-full !text-[10.5px]" style="--h: {{ $activity['hue'] }}">{{ mb_strtoupper($initials($activity['user'])) }}</span>
                                    <span class="whitespace-nowrap">{{ mb_strtoupper($activity['user']) }}</span>
                                </div>
                            </td>
                            <td class="min-w-[200px]">{{ $activity['activity'] }}</td>
                            <td class="whitespace-nowrap">{{ $activity['product'] }}</td>
                            <td><span class="badge {{ $activity['cls'] }}">{{ $activity['status'] }}</span></td>
                            <td class="r"><a href="#" class="ui-btn !h-7 !px-3 !text-[12px]">Lihat</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
