@extends('layout.app')

@section('title', 'Dashboard')

@php
    $dashboardData = $dashboardData ?? [];
    $register = $register ?? [];
    $formatNumber = static fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.');
    $formatRupiahShort = static function ($value) {
        $value = (float) ($value ?? 0);
        if ($value >= 1000000000) return 'Rp ' . rtrim(rtrim(number_format($value / 1000000000, 2, ',', '.'), '0'), ',') . ' M';
        if ($value >= 1000000) return 'Rp ' . rtrim(rtrim(number_format($value / 1000000, 2, ',', '.'), '0'), ',') . ' Jt';
        return 'Rp ' . number_format($value, 0, ',', '.');
    };
    $activeCompanies = $dashboardData['activeCompanies'] ?? 0;
    $todayApplications = $dashboardData['todayApplications'] ?? 0;
    $monthlyTransactions = $dashboardData['monthlyTransactions'] ?? 0;
    $attentionItems = $dashboardData['attentionItems'] ?? [];
    $productStats = $dashboardData['productStats'] ?? [];
    $operationalItems = $dashboardData['operationalItems'] ?? [];
@endphp

@push('css')
<style>
    .main-dashboard { color: #17233d; margin: -8px -4px 0; }
    .main-dashboard .dashboard-header { align-items: center; display: flex; justify-content: space-between; margin-bottom: 22px; }
    .main-dashboard .dashboard-header h1 { color: #111b35; font-size: 28px; font-weight: 700; letter-spacing: -.7px; margin: 0; }
    .main-dashboard .dashboard-header p { color: #748198; font-size: 13px; margin: 2px 0 0; }
    .main-dashboard .search-box { align-items: center; background: #fff; border: 1px solid #e4eaf2; border-radius: 9px; color: #8090a8; display: flex; font-size: 12px; gap: 9px; padding: 10px 13px; width: 300px; }
    .main-dashboard .search-box kbd { background: #f1f4f8; border: 0; border-radius: 4px; color: #64748b; font-size: 10px; margin-left: auto; padding: 3px 6px; }
    .main-dashboard .dashboard-card { background: #fff; border: 1px solid #e8edf4; border-radius: 12px; box-shadow: 0 4px 16px rgba(45, 66, 102, .05); }
    .main-dashboard .metric-card { min-height: 106px; padding: 16px; }
    .main-dashboard .metric-icon { align-items: center; background: #edf5ff; border-radius: 11px; color: #146fe8; display: flex; float: left; font-size: 23px; height: 46px; justify-content: center; margin-right: 12px; width: 46px; }
    .main-dashboard .metric-label { color: #4f5e77; font-size: 12px; line-height: 1.2; margin: 5px 0 5px; }
    .main-dashboard .metric-value { color: #101a38; font-size: 25px; font-weight: 700; line-height: 1; }
    .main-dashboard .metric-change { color: #36a56d; font-size: 11px; margin-top: 8px; }
    .main-dashboard .metric-change span { background: #e4f8ee; border-radius: 5px; font-weight: 700; margin-right: 7px; padding: 3px 6px; }
    .main-dashboard .metric-change.red { color: #df4e58; } .main-dashboard .metric-change.red span { background: #ffebed; }
    .main-dashboard .attention-panel { align-items: center; background: linear-gradient(100deg, #fff7f7, #fffdf9); border-color: #f4dfe2; display: flex; gap: 20px; padding: 13px 18px; }
    .main-dashboard .attention-title { align-items: center; color: #a91e2a; display: flex; font-size: 17px; font-weight: 700; gap: 9px; min-width: 285px; }
    .main-dashboard .attention-title i { color: #ee3d4e; font-size: 27px; }
    .main-dashboard .attention-title small { color: #748198; display: block; font-size: 11px; font-weight: 400; margin-top: 3px; }
    .main-dashboard .attention-items { display: flex; flex: 1; gap: 12px; }
    .main-dashboard .attention-item { align-items: center; border: 1px solid #e7edf4; border-radius: 10px; display: flex; flex: 1; gap: 10px; min-height: 58px; padding: 9px 12px; }
    .main-dashboard .attention-item i { border-radius: 8px; font-size: 22px; padding: 7px; } .main-dashboard .attention-item strong { display: block; font-size: 19px; line-height: 1; } .main-dashboard .attention-item small { color: #5f6e85; font-size: 11px; }
    .main-dashboard .attention-red i { background: #ffebed; color: #e14a55; } .main-dashboard .attention-yellow i { background: #fff4dc; color: #d9940b; } .main-dashboard .attention-blue i { background: #e8f2ff; color: #1470df; }
    .main-dashboard .section-heading { align-items: center; display: flex; gap: 9px; margin-bottom: 12px; } .main-dashboard .section-heading i { color: #1470df; font-size: 21px; } .main-dashboard .section-heading h2 { font-size: 17px; font-weight: 700; margin: 0; } .main-dashboard .section-heading p { color: #8490a4; font-size: 11px; margin: 2px 0 0; }
    .main-dashboard .section-heading a { color: #1470df; font-size: 11px; font-weight: 600; margin-left: auto; }
    .main-dashboard .product-card { padding: 12px 14px 10px; } .main-dashboard .product-title { align-items: center; display: flex; font-size: 13px; font-weight: 700; gap: 9px; } .main-dashboard .product-icon { border-radius: 8px; color: #fff; font-size: 18px; padding: 7px; } .main-dashboard .product-blue { background: #2b83e9; } .main-dashboard .product-purple { background: #804ce3; } .main-dashboard .product-green { background: #18aa73; } .main-dashboard .product-orange { background: #f3941b; } .main-dashboard .product-arrow { color: #1470df; margin-left: auto; }
    .main-dashboard .product-stats { display: flex; margin-top: 13px; } .main-dashboard .product-stat { flex: 1; text-align: center; } .main-dashboard .product-stat + .product-stat { border-left: 1px solid #e5eaf1; } .main-dashboard .product-stat strong { display: block; font-size: 17px; } .main-dashboard .product-stat small { color: #7a879c; font-size: 10px; }
    .main-dashboard .sub-card { padding: 15px; } .main-dashboard .sub-card .section-heading { margin-bottom: 12px; }
    .main-dashboard .operation-grid { display: grid; gap: 10px; grid-template-columns: repeat(4, 1fr); } .main-dashboard .operation-item { border-radius: 10px; min-height: 96px; padding: 11px; } .main-dashboard .operation-item i { border-radius: 8px; display: inline-block; font-size: 20px; padding: 7px; } .main-dashboard .operation-item strong { display: block; font-size: 21px; margin-top: 7px; } .main-dashboard .operation-item small { color: #6f7e95; font-size: 10px; } .main-dashboard .op-blue { background: #f0f7ff; } .main-dashboard .op-blue i { background: #dceeff; color: #267bdc; } .main-dashboard .op-green { background: #effbf6; } .main-dashboard .op-green i { background: #d7f6e7; color: #15a26a; } .main-dashboard .op-purple { background: #f6f2ff; } .main-dashboard .op-purple i { background: #e7ddff; color: #7a4bdd; } .main-dashboard .op-yellow { background: #fff9e9; } .main-dashboard .op-yellow i { background: #fff0c9; color: #d78d00; }
    .main-dashboard .distribution-table, .main-dashboard .activity-table { margin: 0; width: 100%; } .main-dashboard .distribution-table th, .main-dashboard .activity-table th { background: #f4f7fb; border: 0; color: #6a7890; font-size: 10px; font-weight: 600; } .main-dashboard .distribution-table td, .main-dashboard .activity-table td { border-color: #edf1f6; color: #34435d; font-size: 11px; vertical-align: middle; } .main-dashboard .distribution-table .product-dot { border-radius: 5px; color: #fff; display: inline-block; font-size: 11px; margin-right: 7px; padding: 4px; }
    .main-dashboard .badge-active, .main-dashboard .badge-normal, .main-dashboard .badge-pending { border-radius: 999px; font-size: 10px; padding: 4px 9px; } .main-dashboard .badge-active { background: #dff7eb; color: #168a59; } .main-dashboard .badge-normal { background: #e4f1ff; color: #2675c9; } .main-dashboard .badge-pending { background: #fff1d2; color: #b27600; }
    .main-dashboard .avatar { align-items: center; background: #2076dc; border-radius: 50%; color: #fff; display: inline-flex; font-size: 10px; font-weight: 700; height: 24px; justify-content: center; margin-right: 7px; width: 24px; } .main-dashboard .activity-action { border: 1px solid #2a82e2; border-radius: 6px; color: #1670ce; font-size: 10px; padding: 3px 10px; }
    @media (max-width: 1199px) { .main-dashboard .attention-panel { align-items: stretch; flex-direction: column; gap: 10px; } .main-dashboard .attention-title { min-width: 0; } } @media (max-width: 767px) { .main-dashboard .dashboard-header { align-items: flex-start; flex-direction: column; gap: 12px; } .main-dashboard .search-box { width: 100%; } .main-dashboard .attention-items, .main-dashboard .operation-grid { grid-template-columns: 1fr 1fr; display: grid; } }
</style>
@endpush

@section('content')
<div class="main-dashboard">
    <div class="dashboard-header"><div><h1>Dashboard</h1><p>Ringkasan aktivitas dan performa seluruh produk TOMI dalam satu tampilan</p></div><div class="search-box"><i class="mdi mdi-magnify"></i><span>Cari perusahaan, pengguna, atau transaksi...</span><kbd>⌘ K</kbd></div></div>

    <div class="row mb-2">@foreach ([['label' => 'Total Perusahaan Aktif', 'value' => $formatNumber($activeCompanies), 'icon' => 'mdi-office-building-outline', 'change' => '↑ 12%', 'caption' => 'dari bulan lalu'], ['label' => 'Total Pengajuan Hari Ini', 'value' => $formatNumber($todayApplications), 'icon' => 'mdi-file-document-outline', 'change' => '↑ 35%', 'caption' => 'dari kemarin'], ['label' => 'Transaksi Bulan Ini', 'value' => $formatRupiahShort($monthlyTransactions), 'icon' => 'mdi-chart-bar', 'change' => '↑ 18%', 'caption' => 'dari bulan lalu'], ['label' => 'Tugas Perlu Tindakan', 'value' => $formatNumber($dashboardData['attentionTotal'] ?? 0), 'icon' => 'mdi-calendar-alert-outline', 'change' => '↑ 19%', 'caption' => 'dari kemarin', 'red' => true]] as $metric)<div class="col-xl-3 col-md-6 mb-3"><div class="dashboard-card metric-card clearfix"><div class="metric-icon"><i class="mdi {{ $metric['icon'] }}"></i></div><div class="metric-label">{{ $metric['label'] }}</div><div class="metric-value">{{ $metric['value'] }}</div><div class="metric-change {{ !empty($metric['red']) ? 'red' : '' }}"><span>{{ $metric['change'] }}</span>{{ $metric['caption'] }}</div></div></div>@endforeach</div>

    <div class="dashboard-card attention-panel mb-3"><div class="attention-title"><i class="mdi mdi-alert"></i><div>Perlu Perhatian<small>Terdapat beberapa hal yang memerlukan tindakan Anda</small></div></div><div class="attention-items">@foreach ($attentionItems as $item)<div class="attention-item {{ $item['class'] }}"><i class="mdi {{ $item['icon'] }}"></i><div><strong>{{ $formatNumber($item['count']) }}</strong><small>{{ $item['label'] }}</small></div></div>@endforeach</div></div>

    <div class="dashboard-card sub-card mb-3"><div class="section-heading"><i class="mdi mdi-chart-bar"></i><div><h2>Ringkasan Produk</h2><p>Performa utama dari seluruh produk TOMI</p></div></div><div class="row">@foreach ($productStats as $product)<div class="col-xl-3 col-md-6 mb-2"><div class="dashboard-card product-card h-100"><div class="product-title"><span class="product-icon {{ $product['class'] }}"><i class="mdi {{ $product['icon'] }}"></i></span>{{ $product['name'] }}<i class="mdi mdi-chevron-right product-arrow"></i></div><div class="product-stats"><div class="product-stat"><strong>{{ $formatNumber($product['total']) }}</strong><small>Total Pengajuan</small></div><div class="product-stat"><strong>{{ $formatNumber($product['pending']) }}</strong><small>Pending</small></div><div class="product-stat"><strong>{{ $formatNumber($product['approved']) }}</strong><small>Disetujui</small></div></div></div></div>@endforeach</div></div>

    <div class="row mb-3"><div class="col-lg-8 mb-3 mb-lg-0"><div class="dashboard-card sub-card h-100"><div class="section-heading"><i class="mdi mdi-calendar-today"></i><div><h2>Ringkasan Operasional Hari Ini</h2><p>Informasi utama aktivitas operasional hari ini</p></div></div><div class="operation-grid">@foreach ($operationalItems as $item)<div class="operation-item {{ $item['class'] }}"><i class="mdi {{ $item['icon'] }}"></i><strong>{{ $formatNumber($item['value']) }}</strong><small>{{ $item['label'] }}<br>{{ $item['caption'] }}</small></div>@endforeach</div></div></div><div class="col-lg-4"><div class="dashboard-card sub-card h-100"><div class="section-heading"><i class="mdi mdi-cube-outline"></i><div><h2>Distribusi Produk</h2><p>Jumlah aktivitas setiap produk</p></div><a href="#">Lihat Semua <i class="mdi mdi-arrow-right"></i></a></div><table class="table distribution-table"><thead><tr><th>#</th><th>Produk</th><th>Jumlah Aktivitas</th><th>Status</th></tr></thead><tbody>@foreach ($productStats as $index => $product)<tr><td>{{ $index + 1 }}</td><td><span class="product-dot {{ $product['class'] }}"><i class="mdi {{ $product['icon'] }}"></i></span>{{ $product['name'] }}</td><td><strong>{{ $formatNumber($product['total']) }}</strong></td><td><span class="{{ $product['total'] > 0 ? 'badge-active' : 'badge-normal' }}">{{ $product['total'] > 0 ? 'Aktif' : 'Normal' }}</span></td></tr>@endforeach</tbody></table></div></div></div>

    <div class="dashboard-card sub-card"><div class="section-heading"><i class="mdi mdi-file-document-outline"></i><div><h2>Aktivitas / Tugas Terbaru</h2><p>Daftar aktivitas dan tugas yang perlu Anda ketahui</p></div><a href="#">Lihat Semua <i class="mdi mdi-arrow-right"></i></a></div><div class="table-responsive"><table class="table activity-table"><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Produk</th><th>Status</th><th>Aksi</th></tr></thead><tbody>@forelse (($register['activiti'] ?? []) as $activity)<tr><td>{{ $activity['created_at'] }}</td><td><span class="avatar">{{ strtoupper(substr($activity['name'], 0, 2)) }}</span>{{ $activity['name'] }}</td><td>{{ $activity['type'] === 'Registrasi' ? 'Pengguna baru terdaftar' : 'Mengajukan pinjaman perusahaan' }}</td><td>{{ $activity['type'] === 'Registrasi' ? 'TomiDana' : 'TomiDana' }}</td><td><span class="{{ $activity['type'] === 'Registrasi' ? 'badge-active' : 'badge-pending' }}">{{ $activity['type'] === 'Registrasi' ? 'Selesai' : 'Pending' }}</span></td><td><a class="activity-action" href="#">Lihat</a></td></tr>@empty<tr><td colspan="6" class="text-center text-muted">Belum ada aktivitas terbaru.</td></tr>@endforelse</tbody></table></div></div>
</div>
@endsection
