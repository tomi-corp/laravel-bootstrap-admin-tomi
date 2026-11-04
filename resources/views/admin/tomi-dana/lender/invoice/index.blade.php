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

    $summaryCards = [
        ['label' => 'Total Pemberi Pinjaman', 'value' => $formatNumber($summary['total_lenders'] ?? 0), 'icon' => 'account-group-outline', 'tone' => 'blue'],
        ['label' => 'Total Invoice Bulan Ini', 'value' => $formatNumber($summary['total_invoices'] ?? 0), 'icon' => 'file-document-outline', 'tone' => 'blue'],
        ['label' => 'Total Biaya Layanan', 'value' => $formatRupiah($summary['service_fee'] ?? 0), 'icon' => 'layers-outline', 'tone' => 'blue'],
        ['label' => 'Total Biaya Admin', 'value' => $formatRupiah($summary['admin_fee'] ?? 0), 'icon' => 'cash-multiple', 'tone' => 'blue'],
        ['label' => 'Total Tagihan', 'value' => $formatRupiah($summary['total_billing'] ?? 0), 'icon' => 'calculator-variant-outline', 'tone' => 'blue'],
        ['label' => 'Sudah Terbayar', 'value' => $formatNumber($summary['paid_count'] ?? 0), 'amount' => $formatRupiah($summary['paid_amount'] ?? 0), 'icon' => 'check-circle-outline', 'tone' => 'green'],
        ['label' => 'Mulai Jatuh Tempo', 'value' => $formatNumber($summary['due_count'] ?? 0), 'amount' => $formatRupiah($summary['due_amount'] ?? 0), 'icon' => 'clock-alert-outline', 'tone' => 'red'],
    ];

    $statusStyles = [
        'draft' => ['label' => 'Draf', 'class' => 'status-muted', 'dot' => 'muted'],
        'menunggu' => ['label' => 'Menunggu Penagihan', 'class' => 'status-blue', 'dot' => 'blue'],
        'mulai_jatuh_tempo' => ['label' => 'Mulai Jatuh Tempo', 'class' => 'status-yellow', 'dot' => 'yellow'],
        'jatuh_tempo' => ['label' => 'Jatuh Tempo', 'class' => 'status-red', 'dot' => 'red'],
        'lunas' => ['label' => 'Sudah Terbayar', 'class' => 'status-green', 'dot' => 'green'],
    ];
@endphp

@push('css')
<style>
    .invoice-dashboard { color: #202a44; }
    .invoice-dashboard .page-heading { align-items: center; display: flex; gap: 14px; margin-bottom: 20px; }
    .invoice-dashboard .page-heading-icon { align-items: center; background: #e8f0ff; border-radius: 12px; color: #1264e8; display: flex; font-size: 26px; height: 48px; justify-content: center; width: 48px; }
    .invoice-dashboard .page-heading h1 { font-size: 25px; font-weight: 700; letter-spacing: -.5px; margin: 0; }
    .invoice-dashboard .page-heading p { color: #697386; font-size: 13px; margin: 3px 0 0; }
    .invoice-dashboard .dashboard-panel { background: #fff; border: 1px solid #e6ebf3; border-radius: 12px; box-shadow: 0 4px 16px rgba(39, 61, 95, .05); }
    .invoice-dashboard .filter-panel { padding: 16px; }
    .invoice-dashboard .filter-panel .form-group { min-width: 0; }
    .invoice-dashboard .filter-panel label { color: #202a44; font-size: 11px; font-weight: 700; margin-bottom: 5px; }
    .invoice-dashboard .filter-panel .form-control { border-color: #dce3ee; border-radius: 8px; color: #27324a; font-size: 12px; height: 38px; }
    .invoice-dashboard .btn-dashboard { border-radius: 8px; font-size: 12px; font-weight: 600; height: 38px; padding: 0 14px; }
    .invoice-dashboard .filter-actions { align-items: flex-end; gap: 8px; min-width: 0; }
    .invoice-dashboard .filter-actions .btn { flex: 1 1 0; margin-right: 0 !important; min-width: 0; white-space: nowrap; }
    .invoice-dashboard .btn-primary { background: #1264e8; border-color: #1264e8; }
    .invoice-dashboard .metric-card { min-height: 102px; padding: 15px; }
    .invoice-dashboard .metric-card .metric-icon { align-items: center; background: #edf4ff; border-radius: 10px; color: #1264e8; display: flex; float: left; font-size: 21px; height: 36px; justify-content: center; margin-right: 10px; width: 36px; }
    .invoice-dashboard .metric-card .metric-label { color: #4c5870; font-size: 11px; line-height: 1.25; min-height: 28px; }
    .invoice-dashboard .metric-card .metric-value { color: #101a38; font-size: 19px; font-weight: 700; line-height: 1.2; margin-top: 7px; }
    .invoice-dashboard .metric-card .metric-amount { color: #4c5870; font-size: 10px; margin-top: 3px; }
    .invoice-dashboard .metric-card.tone-green { background: #f3fbf5; }
    .invoice-dashboard .metric-card.tone-green .metric-icon { background: #e1f5e6; color: #159447; }
    .invoice-dashboard .metric-card.tone-green .metric-value { color: #159447; }
    .invoice-dashboard .metric-card.tone-red { background: #fff7f6; }
    .invoice-dashboard .metric-card.tone-red .metric-icon { background: #ffe8e5; color: #d72e3c; }
    .invoice-dashboard .metric-card.tone-red .metric-value { color: #d72e3c; }
    .invoice-dashboard .panel-header { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; gap: 9px; padding: 15px 16px; }
    .invoice-dashboard .panel-header i { color: #1264e8; font-size: 21px; }
    .invoice-dashboard .panel-header h3 { color: #18213a; font-size: 16px; font-weight: 700; margin: 0; }
    .invoice-dashboard .panel-body { padding: 10px 16px 16px; }
    .invoice-dashboard .status-row { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; gap: 8px; padding: 9px 0; }
    .invoice-dashboard .status-row:last-child { border-bottom: 0; }
    .invoice-dashboard .status-dot { border-radius: 50%; height: 11px; width: 11px; }
    .invoice-dashboard .status-dot.muted { background: #aab3c4; }
    .invoice-dashboard .status-dot.blue { background: #1264e8; }
    .invoice-dashboard .status-dot.yellow { background: #ed9d00; }
    .invoice-dashboard .status-dot.red { background: #d72e3c; }
    .invoice-dashboard .status-dot.green { background: #159447; }
    .invoice-dashboard .status-name { flex: 1; font-size: 12px; font-weight: 600; }
    .invoice-dashboard .status-count, .invoice-dashboard .status-total { font-size: 11px; text-align: right; }
    .invoice-dashboard .status-total { font-weight: 700; min-width: 115px; }
    .invoice-dashboard .invoice-table { margin-bottom: 0; min-width: 1180px; width: 100% !important; }
    .invoice-dashboard .invoice-table th { background: #f2f4f8; border: 0; color: #42506a; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .invoice-dashboard .invoice-table td { border-color: #edf0f5; color: #27324a; font-size: 11px; vertical-align: middle; }
    .invoice-dashboard .invoice-table tr.is-selected { background: #f1f6ff; box-shadow: inset 3px 0 #1264e8; }
    .invoice-dashboard .status-pill { border-radius: 999px; display: inline-block; font-size: 10px; font-weight: 600; padding: 5px 8px; white-space: nowrap; }
    .invoice-dashboard .status-muted { background: #eef0f4; color: #5e687a; }
    .invoice-dashboard .status-blue { background: #e6efff; color: #1264e8; }
    .invoice-dashboard .status-yellow { background: #fff3d6; color: #a46a00; }
    .invoice-dashboard .status-red { background: #ffe5e5; color: #c32635; }
    .invoice-dashboard .status-green { background: #e4f7e9; color: #16833f; }
    .invoice-dashboard .detail-list { margin: 0; }
    .invoice-dashboard .detail-list > div { align-items: center; border-bottom: 1px solid #edf0f5; display: flex; font-size: 12px; justify-content: space-between; padding: 9px 0; }
    .invoice-dashboard .detail-list dt { color: #5c667a; font-weight: 400; margin: 0; }
    .invoice-dashboard .detail-list dd { color: #202a44; font-weight: 600; margin: 0 0 0 12px; text-align: right; }
    .invoice-dashboard .detail-total { background: #eaf2ff; border-radius: 8px; color: #1264e8; font-size: 14px; font-weight: 700; margin-top: 10px; padding: 11px; }
    .invoice-dashboard .detail-status { margin-top: 14px; }
    .invoice-dashboard .empty-state { color: #8a93a5; font-size: 12px; padding: 25px 10px; text-align: center; }
    .invoice-dashboard .action-table { margin-bottom: 0; width: 100%; }
    .invoice-dashboard .action-table th { background: #f2f4f8; border: 0; color: #42506a; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .invoice-dashboard .action-table td { border-color: #edf0f5; color: #27324a; font-size: 11px; vertical-align: middle; }
    .invoice-dashboard .detail-action { background: #1264e8; border-color: #1264e8; border-radius: 8px; color: #fff; font-size: 11px; font-weight: 600; padding: 7px 10px; white-space: nowrap; }
    .invoice-dashboard .detail-action:hover { background: #0b55cb; border-color: #0b55cb; color: #fff; }
    .invoice-dashboard .detail-download { border-radius: 8px; font-size: 12px; font-weight: 600; padding: 10px 12px; width: 100%; }
    .invoice-dashboard .summary-grid { margin-bottom: 16px; }
    .invoice-dashboard .summary-grid > [class*="col-"] { margin-bottom: 16px; }
    .invoice-dashboard .table-scroll { margin: 16px; max-width: calc(100% - 32px); overflow: visible; }
    @media (max-width: 991.98px) {
        .invoice-dashboard .filter-actions { margin-top: 12px; }
        .invoice-dashboard .page-heading h1 { font-size: 21px; }
    }
</style>
@endpush

@section('content')
<div class="invoice-dashboard">
    <div class="page-heading">
        <div class="page-heading-icon"><i class="mdi mdi-calendar-text-outline"></i></div>
        <div>
            <h1>Dashboard Tagihan Pemberi Pinjaman</h1>
            <p>Halaman ini menampilkan tagihan invoice kepada pemberi pinjaman yang terkait dengan biaya layanan bulanan dan biaya admin.</p>
        </div>
    </div>

    <div class="dashboard-panel filter-panel mb-3">
        <form method="GET" action="{{ url()->current() }}">
            <div class="form-row align-items-end">
                <div class="form-group col-lg-2 col-md-6 mb-lg-0">
                    <label for="invoiceMonth">Periode Bulan</label>
                    <select class="form-control" id="invoiceMonth" name="month">
                        <option value="" @selected(!request()->filled('month'))>Semua Bulan</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" @selected((int) request('month') === $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-6 mb-lg-0">
                    <label for="invoiceYear">Tahun</label>
                    <select class="form-control" id="invoiceYear" name="year">
                        <option value="" @selected(!request()->filled('year'))>Semua Tahun</option>
                        @foreach (range(now()->year - 2, now()->year + 1) as $year)
                            <option value="{{ $year }}" @selected((int) request('year') === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6 mb-lg-0">
                    <label for="invoiceLender">Pemberi Pinjaman</label>
                    <select class="form-control" id="invoiceLender" name="lender_id">
                        <option value="">Semua Pemberi Pinjaman</option>
                        @foreach ($lenders as $lender)
                            <option value="{{ data_get($lender, 'id') }}" @selected((string) request('lender_id') === (string) data_get($lender, 'id'))>{{ strtoupper(data_get($lender, 'nama', '-')) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-6 mb-lg-0">
                    <label for="invoiceStatus">Status Invoice</label>
                    <select class="form-control" id="invoiceStatus" name="status">
                        <option value="">Semua Status</option>
                        @foreach ($statusStyles as $key => $style)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $style['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-12 filter-actions d-flex flex-wrap">
                    <button class="btn btn-primary btn-dashboard mr-2 mb-2 mb-lg-0" type="submit"><i class="mdi mdi-filter-variant mr-1"></i>Terapkan Filter</button>
                    <button class="btn btn-outline-primary btn-dashboard mr-2 mb-2 mb-lg-0" type="button" id="exportInvoice"><i class="mdi mdi-download-outline mr-1"></i>Ekspor</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row mb-3">
        @foreach ($summaryCards as $card)
            <div class="col-xl col-lg-3 col-md-6 mb-3 mb-xl-0">
                <div class="dashboard-panel metric-card tone-{{ $card['tone'] }} h-100 clearfix">
                    <div class="metric-icon"><i class="mdi mdi-{{ $card['icon'] }}"></i></div>
                    <div class="metric-label">{{ $card['label'] }}</div>
                    <div class="metric-value">{{ $card['value'] }}</div>
                    @if (isset($card['amount']))
                        <div class="metric-amount">{{ $card['amount'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="row summary-grid">
        <div class="col-lg-4">
            <div class="dashboard-panel h-100">
                <div class="panel-header"><i class="mdi mdi-file-chart-outline"></i><h3>Ringkasan Tagihan Per Status</h3></div>
                <div class="panel-body">
                    @foreach ($statusStyles as $status => $style)
                        @php $statusData = $statusSummary[$status] ?? ['count' => 0, 'amount' => 0]; @endphp
                        <div class="status-row">
                            <span class="status-dot {{ $style['dot'] }}"></span>
                            <span class="status-name">{{ $style['label'] }}</span>
                            <span class="status-count">{{ $formatNumber($statusData['count']) }} invoice</span>
                            <span class="status-total">{{ $formatRupiah($statusData['amount']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="dashboard-panel h-100">
                <div class="panel-header"><i class="mdi mdi-bell-alert-outline"></i><h3>Tagihan yang Perlu Tindakan</h3></div>
                <div class="panel-body p-0">
                    <div class="table-responsive">
                        <table class="table action-table">
                            <thead><tr><th>Pemberi Pinjaman</th><th>No. Invoice</th><th>Jatuh Tempo</th><th>Nominal Tagihan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse ($actionInvoices as $invoice)
                                    @php $style = $statusStyles[data_get($invoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp
                                    <tr>
                                        <td>{{ strtoupper(data_get($invoice, 'lender_name', '-')) }}</td>
                                        <td>{{ data_get($invoice, 'invoice_number', '-') }}</td>
                                        <td>{{ data_get($invoice, 'due_date', '-') }}</td>
                                        <td>{{ $formatRupiah(data_get($invoice, 'amount', 0)) }}</td>
                                        <td><span class="status-pill {{ $style['class'] }}"><i class="mdi mdi-clock-outline mr-1"></i>{{ $style['label'] }}</span></td>
                                        <td>
                                            @if (data_get($invoice, 'download_url'))
                                                <a class="detail-action" href="{{ data_get($invoice, 'download_url') }}"><i class="mdi mdi-download-outline mr-1"></i>Download Invoice</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="empty-state" colspan="6">Tidak ada tagihan yang perlu ditindaklanjuti.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9 mb-3 mb-lg-0">
            <div class="dashboard-panel">
                <div class="panel-header"><i class="mdi mdi-file-table-outline"></i><h3>Daftar Invoice Tagihan Lender</h3></div>
                <div class="panel-body p-0">
                    <div class="table-scroll">
                        <table id="tableLenderInvoices" class="table invoice-table" style="width:100%">
                            <thead><tr><th>No. Invoice</th><th>Pemberi Pinjaman</th><th>Periode</th><th>Jumlah Transaksi</th><th>Biaya Layanan</th><th>Biaya Admin</th><th>Total Tagihan</th><th>Jatuh Tempo</th><th>Status</th><th>Download</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    @php $style = $statusStyles[data_get($invoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp
                                    <tr class="{{ $selectedInvoice && data_get($selectedInvoice, 'id') == data_get($invoice, 'id') ? 'is-selected' : '' }}">
                                        <td>{{ data_get($invoice, 'invoice_number', '-') }}</td>
                                        <td>{{ strtoupper(data_get($invoice, 'lender_name', '-')) }}</td>
                                        <td>{{ data_get($invoice, 'period_label', '-') }}</td>
                                        <td>{{ $formatNumber(data_get($invoice, 'transaction_count', 0)) }}</td>
                                        <td>{{ $formatRupiah(data_get($invoice, 'service_fee', 0)) }}</td>
                                        <td>{{ $formatRupiah(data_get($invoice, 'admin_fee', 0)) }}</td>
                                        <td>{{ $formatRupiah(data_get($invoice, 'amount', 0)) }}</td>
                                        <td>{{ data_get($invoice, 'due_date', '-') }}</td>
                                        <td><span class="status-pill {{ $style['class'] }}">{{ $style['label'] }}</span></td>
                                        <td>
                                            @if (data_get($invoice, 'download_url'))
                                                <a class="btn btn-outline-primary btn-sm" href="{{ data_get($invoice, 'download_url') }}" title="Download Invoice" aria-label="Download Invoice"><i class="mdi mdi-download-outline"></i></a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm edit-invoice-payment" title="Catat pembayaran invoice"
                                                data-invoice-id="{{ data_get($invoice, 'id') }}"
                                                data-invoice-number="{{ data_get($invoice, 'invoice_number') }}"
                                                data-payment-date="{{ data_get($invoice, 'payment_date') }}">
                                                <i class="mdi mdi-pencil-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="dashboard-panel h-100">
                <div class="panel-header"><i class="mdi mdi-file-document-outline"></i><h3>Rincian Tagihan</h3></div>
                <div class="panel-body">
                    @if ($selectedInvoice)
                        <dl class="detail-list">
                            <div><dt>Pemberi Pinjaman</dt><dd>{{ strtoupper(data_get($selectedInvoice, 'lender_name', '-')) }}</dd></div>
                            <div><dt>No. Invoice</dt><dd>{{ data_get($selectedInvoice, 'invoice_number', '-') }}</dd></div>
                            <div><dt>Periode</dt><dd>{{ data_get($selectedInvoice, 'period_label', '-') }}</dd></div>
                            <div><dt><i class="mdi mdi-counter mr-1"></i>Jumlah Transaksi</dt><dd>{{ $formatNumber(data_get($selectedInvoice, 'transaction_count', 0)) }}</dd></div>
                            <div><dt><i class="mdi mdi-chart-bar mr-1"></i>Total Nilai Transaksi</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'transaction_value', 0)) }}</dd></div>
                            <div><dt><i class="mdi mdi-layers-outline mr-1"></i>Biaya Layanan</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'service_fee', 0)) }}</dd></div>
                            <div><dt><i class="mdi mdi-cash-multiple mr-1"></i>Biaya Admin</dt><dd>{{ $formatRupiah(data_get($selectedInvoice, 'admin_fee', 0)) }}</dd></div>
                        </dl>
                        <div class="detail-total d-flex justify-content-between"><span>Total Tagihan</span><span>{{ $formatRupiah(data_get($selectedInvoice, 'amount', 0)) }}</span></div>
                        @php $selectedStyle = $statusStyles[data_get($selectedInvoice, 'status', 'draft')] ?? $statusStyles['draft']; @endphp
                        <div class="detail-status d-flex justify-content-between align-items-center"><span>Status Pembayaran</span><span class="status-pill {{ $selectedStyle['class'] }}">{{ $selectedStyle['label'] }}</span></div>
                        @if (data_get($selectedInvoice, 'download_url'))
                            <a class="btn btn-primary detail-download mt-3" href="{{ data_get($selectedInvoice, 'download_url') }}"><i class="mdi mdi-download-outline mr-1"></i>Download Invoice</a>
                        @endif
                    @else
                        <div class="empty-state">Pilih invoice untuk melihat rinciannya.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="invoicePaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="invoicePaymentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran Invoice Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="paymentInvoiceId">
                    <div class="form-group">
                        <label>No. Invoice</label>
                        <input type="text" class="form-control" id="paymentInvoiceNumber" readonly>
                    </div>
                    <div class="form-group">
                        <label for="paymentDate">Waktu Pembayaran <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="tanggal" id="paymentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="paymentProof">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti_transfer" id="paymentProof" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="form-text text-muted">Format JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="pernyataan" value="1" id="paymentDeclaration" required>
                        <label class="form-check-label" for="paymentDeclaration">Saya menyatakan bahwa invoicing terhadap lender ini telah terbayarkan</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitInvoicePayment"><i class="mdi mdi-content-save-outline mr-1"></i>Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        $('#tableLenderInvoices').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            pageLength: 8,
            lengthMenu: [[8, 15, 25, 50], [8, 15, 25, 50]],
            order: [[2, 'desc']],
            dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-2"<"col-md-6"i><"col-md-6"p>>',
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ invoice',
                infoEmpty: 'Belum ada invoice tagihan lender',
                emptyTable: 'Belum ada invoice tagihan lender',
                zeroRecords: 'Invoice tidak ditemukan',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
            }
        });

        $('#exportInvoice').on('click', function () {
            window.print();
        });

        $('.edit-invoice-payment').on('click', function () {
            const button = $(this);
            $('#invoicePaymentForm')[0].reset();
            $('#paymentInvoiceId').val(button.data('invoice-id'));
            $('#paymentInvoiceNumber').val(button.data('invoice-number'));
            $('#paymentDate').val(button.data('payment-date') || '');
            $('#invoicePaymentModal').modal('show');
        });

        $('#invoicePaymentForm').on('submit', function (event) {
            event.preventDefault();

            const submitButton = $('#submitInvoicePayment');
            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');
            submitButton.prop('disabled', true);

            $.ajax({
                url: '{{ route('admin.lender.invoicing.pending.update') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (window.toastr) toastr.success(response.message);
                    $('#invoicePaymentModal').modal('hide');
                    window.location.reload();
                },
                error: function (xhr) {
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    const message = errors.tanggal?.[0] || errors.bukti_transfer?.[0] || errors.pernyataan?.[0] || response.message || 'Pembayaran invoice gagal disimpan.';
                    if (window.toastr) toastr.error(message);
                },
                complete: function () {
                    submitButton.prop('disabled', false);
                }
            });
        });

    });
</script>
@endpush
