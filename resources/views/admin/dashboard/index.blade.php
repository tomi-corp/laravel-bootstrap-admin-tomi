@extends('layout.app')

@section('title', 'Dashboard Tagihan Pemberi Pinjaman')

@php
    $formatRupiah = static fn ($value) => 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
    $formatNumber = static fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.');
    $summary = $summary ?? [];
    $statusSummary = $statusSummary ?? [];
    $actionInvoices = $actionInvoices ?? [];
    $invoices = $invoices ?? [];
    $lenders = $lenders ?? [];
    $selectedInvoice = $selectedInvoice ?? null;
    $statusStyles = [
        'draft' => ['label' => 'Draf', 'class' => 'status-muted', 'dot' => 'muted'],
        'menunggu' => ['label' => 'Menunggu Penagihan', 'class' => 'status-blue', 'dot' => 'blue'],
        'mulai_jatuh_tempo' => ['label' => 'Mulai Jatuh Tempo', 'class' => 'status-yellow', 'dot' => 'yellow'],
        'jatuh_tempo' => ['label' => 'Jatuh Tempo', 'class' => 'status-red', 'dot' => 'red'],
        'lunas' => ['label' => 'Sudah Terbayar', 'class' => 'status-green', 'dot' => 'green'],
    ];
    $summaryCards = [
        ['label' => 'Total Pemberi Pinjaman', 'value' => $formatNumber($summary['total_lenders'] ?? 0), 'icon' => 'account-group-outline'],
        ['label' => 'Total Invoice Bulan Ini', 'value' => $formatNumber($summary['total_invoices'] ?? 0), 'icon' => 'file-document-outline'],
        ['label' => 'Total Biaya Layanan', 'value' => $formatRupiah($summary['service_fee'] ?? 0), 'icon' => 'layers-outline'],
        ['label' => 'Total Biaya Admin', 'value' => $formatRupiah($summary['admin_fee'] ?? 0), 'icon' => 'cash-multiple'],
        ['label' => 'Total Tagihan', 'value' => $formatRupiah($summary['total_billing'] ?? 0), 'icon' => 'calculator-variant-outline'],
        ['label' => 'Sudah Terbayar', 'value' => $formatNumber($summary['paid_count'] ?? 0), 'amount' => $formatRupiah($summary['paid_amount'] ?? 0), 'icon' => 'check-circle-outline', 'tone' => 'green'],
        ['label' => 'Mulai Jatuh Tempo', 'value' => $formatNumber($summary['due_count'] ?? 0), 'amount' => $formatRupiah($summary['due_amount'] ?? 0), 'icon' => 'clock-alert-outline', 'tone' => 'red'],
    ];
@endphp

@push('css')
<style>
    .admin-invoice-dashboard { color: #202a44; }
    .admin-invoice-dashboard .page-heading { align-items: center; display: flex; gap: 14px; margin-bottom: 20px; }
    .admin-invoice-dashboard .page-heading-icon, .admin-invoice-dashboard .metric-icon { align-items: center; background: #e8f0ff; border-radius: 10px; color: #1264e8; display: flex; justify-content: center; }
    .admin-invoice-dashboard .page-heading-icon { font-size: 26px; height: 48px; width: 48px; }
    .admin-invoice-dashboard h1 { color: #101a38; font-size: 25px; font-weight: 700; letter-spacing: -.5px; margin: 0; }
    .admin-invoice-dashboard .page-heading p { color: #697386; font-size: 13px; margin: 3px 0 0; }
    .admin-invoice-dashboard .dashboard-panel { background: #fff; border: 1px solid #e6ebf3; border-radius: 12px; box-shadow: 0 4px 16px rgba(39, 61, 95, .05); }
    .admin-invoice-dashboard .filter-panel { padding: 16px; }
    .admin-invoice-dashboard label { color: #202a44; font-size: 11px; font-weight: 700; margin-bottom: 5px; }
    .admin-invoice-dashboard .form-control { border-color: #dce3ee; border-radius: 8px; color: #27324a; font-size: 12px; height: 38px; }
    .admin-invoice-dashboard .btn-dashboard { border-radius: 8px; font-size: 12px; font-weight: 600; height: 38px; padding: 0 14px; }
    .admin-invoice-dashboard .metric-card { min-height: 102px; padding: 15px; }
    .admin-invoice-dashboard .metric-icon { float: left; font-size: 21px; height: 36px; margin-right: 10px; width: 36px; }
    .admin-invoice-dashboard .metric-label { color: #4c5870; font-size: 11px; line-height: 1.25; min-height: 28px; }
    .admin-invoice-dashboard .metric-value { color: #101a38; font-size: 18px; font-weight: 700; line-height: 1.2; margin-top: 7px; }
    .admin-invoice-dashboard .metric-amount { color: #4c5870; font-size: 10px; margin-top: 3px; }
    .admin-invoice-dashboard .tone-green { background: #f3fbf5; } .admin-invoice-dashboard .tone-green .metric-icon, .admin-invoice-dashboard .tone-green .metric-value { color: #159447; }
    .admin-invoice-dashboard .tone-red { background: #fff7f6; } .admin-invoice-dashboard .tone-red .metric-icon, .admin-invoice-dashboard .tone-red .metric-value { color: #d72e3c; }
    .admin-invoice-dashboard .panel-header { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; gap: 9px; padding: 15px 16px; }
    .admin-invoice-dashboard .panel-header i { color: #1264e8; font-size: 21px; } .admin-invoice-dashboard .panel-header h3 { color: #18213a; font-size: 16px; font-weight: 700; margin: 0; }
    .admin-invoice-dashboard .panel-body { padding: 10px 16px 16px; }
    .admin-invoice-dashboard .status-row { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; gap: 8px; padding: 9px 0; }
    .admin-invoice-dashboard .status-dot { border-radius: 50%; height: 11px; width: 11px; } .status-dot.muted { background: #aab3c4; } .status-dot.blue { background: #1264e8; } .status-dot.yellow { background: #ed9d00; } .status-dot.red { background: #d72e3c; } .status-dot.green { background: #159447; }
    .admin-invoice-dashboard .status-name { flex: 1; font-size: 12px; font-weight: 600; } .admin-invoice-dashboard .status-count, .admin-invoice-dashboard .status-total { font-size: 11px; text-align: right; } .admin-invoice-dashboard .status-total { font-weight: 700; min-width: 115px; }
    .admin-invoice-dashboard .invoice-table { margin-bottom: 0; min-width: 1060px; width: 100%; } .admin-invoice-dashboard th { background: #f2f4f8; border: 0; color: #42506a; font-size: 10px; font-weight: 700; white-space: nowrap; } .admin-invoice-dashboard td { border-color: #edf0f5; color: #27324a; font-size: 11px; vertical-align: middle; }
    .admin-invoice-dashboard .status-pill { border-radius: 999px; display: inline-block; font-size: 10px; font-weight: 600; padding: 5px 8px; white-space: nowrap; } .admin-invoice-dashboard .status-muted { background: #eef0f4; color: #5e687a; } .admin-invoice-dashboard .status-blue { background: #e6efff; color: #1264e8; } .admin-invoice-dashboard .status-yellow { background: #fff3d6; color: #a46a00; } .admin-invoice-dashboard .status-red { background: #ffe5e5; color: #c32635; } .admin-invoice-dashboard .status-green { background: #e4f7e9; color: #16833f; }
    .admin-invoice-dashboard .detail-list > div { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; font-size: 12px; justify-content: space-between; padding: 9px 0; } .admin-invoice-dashboard .detail-list dt { color: #5c667a; font-weight: 400; } .admin-invoice-dashboard .detail-list dd { color: #202a44; font-weight: 600; margin-left: 10px; text-align: right; }
    .admin-invoice-dashboard .detail-total { background: #eaf2ff; border-radius: 8px; color: #1264e8; font-size: 14px; font-weight: 700; margin-top: 10px; padding: 11px; } .admin-invoice-dashboard .empty-state { color: #8a93a5; font-size: 12px; padding: 25px 10px; text-align: center; }
    @media (max-width: 991.98px) { .admin-invoice-dashboard h1 { font-size: 21px; } }
</style>
@endpush

@section('content')
<div class="admin-invoice-dashboard">
    <div class="page-heading"><div class="page-heading-icon"><i class="mdi mdi-calendar-text-outline"></i></div><div><h1>Dashboard Tagihan Pemberi Pinjaman</h1><p>Halaman ini menampilkan tagihan invoice kepada pemberi pinjaman yang terkait dengan biaya layanan bulanan dan biaya admin.</p></div></div>

    <div class="dashboard-panel filter-panel mb-3"><form method="GET" action="{{ url()->current() }}"><div class="form-row align-items-end">
        <div class="form-group col-lg-2 col-md-6 mb-lg-0"><label for="dashboardMonth">Periode Bulan</label><select class="form-control" id="dashboardMonth" name="month"><option value="">Semua Bulan</option>@foreach (range(1, 12) as $month)<option value="{{ $month }}" @selected((int) request('month') === $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>@endforeach</select></div>
        <div class="form-group col-lg-2 col-md-6 mb-lg-0"><label for="dashboardYear">Tahun</label><select class="form-control" id="dashboardYear" name="year"><option value="">Semua Tahun</option>@foreach (range(now()->year - 2, now()->year + 1) as $year)<option value="{{ $year }}" @selected((int) request('year') === $year)>{{ $year }}</option>@endforeach</select></div>
        <div class="form-group col-lg-3 col-md-6 mb-lg-0"><label for="dashboardLender">Pemberi Pinjaman</label><select class="form-control" id="dashboardLender" name="lender_id"><option value="">Semua Pemberi Pinjaman</option>@foreach ($lenders as $lender)<option value="{{ data_get($lender, 'id') }}" @selected((string) request('lender_id') === (string) data_get($lender, 'id'))>{{ strtoupper(data_get($lender, 'nama', '-')) }}</option>@endforeach</select></div>
        <div class="form-group col-lg-2 col-md-6 mb-lg-0"><label for="dashboardStatus">Status Invoice</label><select class="form-control" id="dashboardStatus" name="status"><option value="">Semua Status</option>@foreach ($statusStyles as $key => $style)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $style['label'] }}</option>@endforeach</select></div>
        <div class="col-lg-3 col-md-12"><button class="btn btn-primary btn-dashboard mr-2" type="submit"><i class="mdi mdi-filter-variant mr-1"></i>Terapkan Filter</button><button class="btn btn-outline-primary btn-dashboard" type="button" id="dashboardExport"><i class="mdi mdi-download-outline mr-1"></i>Ekspor</button></div>
    </div></form></div>

    <div class="row mb-1">@foreach ($summaryCards as $card)<div class="col-xl col-lg-3 col-md-6 mb-3"><div class="dashboard-panel metric-card tone-{{ $card['tone'] ?? 'blue' }} h-100 clearfix"><div class="metric-icon"><i class="mdi mdi-{{ $card['icon'] }}"></i></div><div class="metric-label">{{ $card['label'] }}</div><div class="metric-value">{{ $card['value'] }}</div>@if (isset($card['amount']))<div class="metric-amount">{{ $card['amount'] }}</div>@endif</div></div>@endforeach</div>

    <div class="row mb-3"><div class="col-lg-4 mb-3 mb-lg-0"><div class="dashboard-panel h-100"><div class="panel-header"><i class="mdi mdi-file-chart-outline"></i><h3>Ringkasan Tagihan Per Status</h3></div><div class="panel-body">@foreach ($statusStyles as $status => $style) @php $data = $statusSummary[$status] ?? ['count' => 0, 'amount' => 0]; @endphp<div class="status-row"><span class="status-dot {{ $style['dot'] }}"></span><span class="status-name">{{ $style['label'] }}</span><span class="status-count">{{ $formatNumber($data['count']) }} invoice</span><span class="status-total">{{ $formatRupiah($data['amount']) }}</span></div>@endforeach</div></div></div>
        <div class="col-lg-8"><div class="dashboard-panel h-100"><div class="panel-header"><i class="mdi mdi-bell-alert-outline"></i><h3>Tagihan yang Perlu Tindakan</h3></div><div class="table-responsive"><table class="table invoice-table mb-0"><thead><tr><th>Pemberi Pinjaman</th><th>No. Invoice</th><th>Jatuh Tempo</th><th>Nominal Tagihan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>@forelse ($actionInvoices as $invoice) @php $style = $statusStyles[data_get($invoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp<tr><td>{{ strtoupper(data_get($invoice, 'lender_name', '-')) }}</td><td>{{ data_get($invoice, 'invoice_number', '-') }}</td><td>{{ data_get($invoice, 'due_date', '-') }}</td><td>{{ $formatRupiah(data_get($invoice, 'amount', 0)) }}</td><td><span class="status-pill {{ $style['class'] }}">{{ $style['label'] }}</span></td><td>@if (data_get($invoice, 'download_url'))<a class="btn btn-primary btn-sm" href="{{ data_get($invoice, 'download_url') }}"><i class="mdi mdi-download-outline"></i></a>@endif</td></tr>@empty<tr><td class="empty-state" colspan="6">Tidak ada tagihan yang perlu ditindaklanjuti.</td></tr>@endforelse</tbody></table></div></div></div>
    </div>

    <div class="row"><div class="col-lg-9 mb-3 mb-lg-0"><div class="dashboard-panel"><div class="panel-header"><i class="mdi mdi-file-table-outline"></i><h3>Daftar Invoice Tagihan Lender</h3></div><div class="table-responsive"><table class="table invoice-table mb-0"><thead><tr><th>No. Invoice</th><th>Pemberi Pinjaman</th><th>Periode</th><th>Jumlah Transaksi</th><th>Biaya Layanan</th><th>Biaya Admin</th><th>Total Tagihan</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead><tbody>@forelse ($invoices as $invoice) @php $style = $statusStyles[data_get($invoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp<tr><td>{{ data_get($invoice, 'invoice_number', '-') }}</td><td>{{ strtoupper(data_get($invoice, 'lender_name', '-')) }}</td><td>{{ data_get($invoice, 'period_label', '-') }}</td><td>{{ $formatNumber(data_get($invoice, 'transaction_count', 0)) }}</td><td>{{ $formatRupiah(data_get($invoice, 'service_fee', 0)) }}</td><td>{{ $formatRupiah(data_get($invoice, 'admin_fee', 0)) }}</td><td>{{ $formatRupiah(data_get($invoice, 'amount', 0)) }}</td><td>{{ data_get($invoice, 'due_date', '-') }}</td><td><span class="status-pill {{ $style['class'] }}">{{ $style['label'] }}</span></td><td>@if (data_get($invoice, 'download_url'))<a class="btn btn-outline-primary btn-sm" href="{{ data_get($invoice, 'download_url') }}"><i class="mdi mdi-download-outline"></i></a>@else<span class="text-muted">-</span>@endif</td></tr>@empty<tr><td class="empty-state" colspan="10">Belum ada invoice tagihan lender.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-lg-3"><div class="dashboard-panel h-100"><div class="panel-header"><i class="mdi mdi-file-document-outline"></i><h3>Rincian Tagihan</h3></div><div class="panel-body">@if ($selectedInvoice)<dl class="detail-list"><div><dt>Pemberi Pinjaman</dt><dd>{{ strtoupper(data_get($selectedInvoice, 'lender_name', '-')) }}</dd></div><div><dt>No. Invoice</dt><dd>{{ data_get($selectedInvoice, 'invoice_number', '-') }}</dd></div><div><dt>Periode</dt><dd>{{ data_get($selectedInvoice, 'period_label', '-') }}</dd></div><div><dt>Jumlah Transaksi</dt><dd>{{ $formatNumber(data_get($selectedInvoice, 'transaction_count', 0)) }}</dd></div><div><dt>Total Nilai Transaksi</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'transaction_value', 0)) }}</dd></div><div><dt>Biaya Layanan</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'service_fee', 0)) }}</dd></div><div><dt>Biaya Admin</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'admin_fee', 0)) }}</dd></div></dl><div class="detail-total d-flex justify-content-between"><span>Total Tagihan</span><span>{{ $formatRupiah(data_get($selectedInvoice, 'amount', 0)) }}</span></div>@php $selectedStyle = $statusStyles[data_get($selectedInvoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp<div class="d-flex justify-content-between align-items-center mt-3"><span>Status Pembayaran</span><span class="status-pill {{ $selectedStyle['class'] }}">{{ $selectedStyle['label'] }}</span></div>@if (data_get($selectedInvoice, 'download_url'))<a class="btn btn-primary btn-block mt-3" href="{{ data_get($selectedInvoice, 'download_url') }}"><i class="mdi mdi-download-outline mr-1"></i>Download Invoice</a>@endif @else<div class="empty-state">Pilih invoice untuk melihat rinciannya.</div>@endif</div></div></div>
    </div>
</div>
@endsection

@push('js')
<script>$(function () { $('#dashboardExport').on('click', function () { window.print(); }); });</script>
@endpush
