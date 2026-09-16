@extends('layout.app')

@section('title', 'Detail TomiInvoice')

@push('css')
<style>
    .tomi-invoice-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #detailTable {
        width: 100% !important;
        min-width: 1850px;
        white-space: nowrap;
    }

    #detailTable th,
    #detailTable td {
        white-space: nowrap !important;
        vertical-align: middle;
    }

    #detailTable td:nth-child(4) {
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .text-nowrap {
        white-space: nowrap !important;
    }

    .term-cell {
        min-width: 220px;
        white-space: normal !important;
    }

    .term-cell .term-amount {
        font-weight: 700;
        line-height: 1.45;
        margin-bottom: 2px;
    }

    .term-cell .term-line {
        line-height: 1.45;
        margin-bottom: 2px;
    }

    .term-cell .btn {
        margin-top: 4px;
    }

    .recap-term-cell {
        min-width: 230px;
        white-space: normal !important;
    }

    #detailTable .btn,
    #detailTable .badge {
        display: block;
        width: 100%;
    }

    #detailTable .badge {
        padding: 6px 8px;
        white-space: normal;
    }

    .invoice-mode-menu .nav-link {
        min-width: 150px;
        text-align: center;
    }

    .referral-upload-action {
        display: none !important;
    }

    .invoice-download-action {
        display: block !important;
    }

    body.invoice-mode-upload-referral .invoice-download-action {
        display: none !important;
    }

    body.invoice-mode-upload-referral .referral-upload-action {
        display: block !important;
    }

    .referral-recap-row {
        display: none;
    }

    body.invoice-mode-recap-referral .invoice-detail-row {
        display: none;
    }

    body.invoice-mode-recap-referral .referral-recap-row {
        display: table-row;
    }
</style>
@endpush

@section('content')
@php
$namaPerusahaan = $perusahaan['nama'] ?? '-';
$totalInvoice = $perusahaan['total_invoice'] ?? 0;
$totalPinjaman = $perusahaan['total_pinjaman'] ?? 0;
$totalDibayarkan = $perusahaan['total_dibayarkan'] ?? 0;
$totalFee = $perusahaan['total_fee'] ?? 0;
$details = collect($perusahaan['details'] ?? []);
$totalReferralTerms = $details->sum(fn($item) => collect($item['terms'] ?? [])->count());
$totalReferralUploaded = $details->sum(fn($item) => collect($item['terms'] ?? [])->filter(fn($term) => !empty($term['referral_transfer_proof']))->count());
$totalReferralPending = max(0, $totalReferralTerms - $totalReferralUploaded);
$referralRecapRows = $details
    ->flatMap(function ($item) use ($namaPerusahaan) {
        return collect($item['terms'] ?? [])->map(function ($term) use ($item, $namaPerusahaan) {
            $periodSource = $term['finance_invoice_term_date'] ?? $term['created_at'] ?? $item['created_at'] ?? null;
            $periode = $periodSource ? \Carbon\Carbon::parse($periodSource)->format('F Y') : '-';

            return [
                'nama_perusahaan' => $item['nama_perusahaan'] ?? $namaPerusahaan,
                'vendor_name' => $item['vendor_name'] ?? '-',
                'term' => $term['term'] ?? '-',
                'periode' => $periode,
                'period_key' => $periodSource ? \Carbon\Carbon::parse($periodSource)->format('Y-m') : '-',
                'total_referal' => $term['total_fee'] ?? 0,
                'referral_transfer_date' => $term['referral_transfer_date'] ?? null,
                'referral_transfer_proof' => $term['referral_transfer_proof'] ?? null,
            ];
        });
    })
    ->groupBy(fn($row) => $row['period_key'].'|'.$row['periode'].'|'.$row['nama_perusahaan'])
    ->map(function ($rows) {
        return [
            'nama_perusahaan' => $rows->first()['nama_perusahaan'],
            'periode' => $rows->first()['periode'],
            'period_key' => $rows->first()['period_key'],
            'terms' => collect([1, 2, 3])->mapWithKeys(function ($termNumber) use ($rows) {
                $termRows = $rows->where('term', $termNumber);

                return [
                    $termNumber => [
                        'total_referal' => $termRows->sum('total_referal'),
                        'uploaded' => $termRows->filter(fn($row) => !empty($row['referral_transfer_proof']))->count(),
                        'total' => $termRows->count(),
                    ],
                ];
            }),
            'total_referal' => $rows->sum('total_referal'),
            'uploaded' => $rows->filter(fn($row) => !empty($row['referral_transfer_proof']))->count(),
            'total_terms' => $rows->count(),
        ];
    })
    ->values();
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Detail TomiInvoice - {{ $namaPerusahaan }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-primary rounded-circle mr-3">
                        <i class="mdi mdi-file-document-outline avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Invoice</h5>
                        <h3 class="mb-1">{{ number_format($totalInvoice, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Jumlah invoice perusahaan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-warning rounded-circle mr-3">
                        <i class="mdi mdi-cash-multiple avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Pinjaman</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total nilai pembiayaan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-info rounded-circle mr-3">
                        <i class="mdi mdi-wallet-outline avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Dibayarkan</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalDibayarkan, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total nominal termin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-success rounded-circle mr-3">
                        <i class="mdi mdi-percent avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Fee Tomi</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalFee, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total invoice fee</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2" id="tableTitle">Daftar Invoice</h4>

                    <ul class="nav nav-tabs invoice-mode-menu mb-3" role="tablist" aria-label="Menu detail TomiInvoice">
                        <li class="nav-item">
                            <a href="#" class="nav-link active invoice-mode-btn" data-mode="invoice" role="tab"
                                aria-selected="true">
                                <i class="mdi mdi-table"></i> Invoice
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link invoice-mode-btn" data-mode="upload-referral" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-upload"></i> Upload Bukti Transfer Referal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link invoice-mode-btn" data-mode="recap-referral" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-file-chart"></i> Rekap Referal
                            </a>
                        </li>
                    </ul>

                    <div class="table-responsive tomi-invoice-table-wrapper">
                        <table id="detailTable" class="table table-bordered table-striped table-hover mb-0 nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60" data-invoice-label="No" data-recap-label="No">No</th>
                                    <th data-invoice-label="Mitra" data-recap-label="Periode">Mitra</th>
                                    <th data-invoice-label="Pelanggan Mitra" data-recap-label="Mitra">Pelanggan Mitra</th>
                                    <th data-invoice-label="Nomor Invoice" data-recap-label="Termin 1">Nomor Invoice</th>
                                    <th data-invoice-label="No. PO" data-recap-label="Termin 2">No. PO</th>
                                    <th data-invoice-label="Nilai Pembiayaan" data-recap-label="Termin 3">Nilai Pembiayaan</th>
                                    <th data-invoice-label="Termin 1" data-recap-label="Total Referal">Termin 1</th>
                                    <th data-invoice-label="Termin 2" data-recap-label="Status Bukti">Termin 2</th>
                                    <th data-invoice-label="Termin 3" data-recap-label="Sudah Upload">Termin 3</th>
                                    <th data-invoice-label="Total Termin" data-recap-label="Belum Upload">Total Termin</th>
                                    <th data-invoice-label="Total Invoice Fee" data-recap-label="Status">Total Invoice Fee</th>
                                    <th data-invoice-label="Status" data-recap-label="">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referralRecapRows as $recap)
                                <tr class="referral-recap-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $recap['periode'] }}</td>
                                    <td>{{ $recap['nama_perusahaan'] }}</td>
                                    @for($recapTermNumber = 1; $recapTermNumber <= 3; $recapTermNumber++)
                                    @php
                                        $recapTerm = $recap['terms']->get($recapTermNumber);
                                    @endphp
                                    <td class="recap-term-cell">
                                        @if(($recapTerm['total'] ?? 0) > 0)
                                            @if(($recapTerm['uploaded'] ?? 0) === ($recapTerm['total'] ?? 0))
                                                <span class="badge badge-success">SUDAH UPLOAD</span>
                                            @else
                                                <span class="badge badge-warning">BELUM UPLOAD</span>
                                            @endif
                                            <a href="{{ route('admin.invoice-financing.referral-recap.download', ['id' => $perusahaan['id'], 'period' => $recap['period_key'], 'term' => $recapTermNumber]) }}"
                                                class="btn btn-sm btn-info mt-1" target="_blank" title="Download Rekap Termin {{ $recapTermNumber }}">
                                                <i class="mdi mdi-download"></i> Download Rekap
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endfor
                                    <td>Rp {{ number_format($recap['total_referal'], 0, ',', '.') }}</td>
                                    <td>
                                        @if($recap['uploaded'] === $recap['total_terms'])
                                            <span class="badge badge-success">LENGKAP</span>
                                        @else
                                            <span class="badge badge-warning">BELUM LENGKAP</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($recap['uploaded'], 0, ',', '.') }}</td>
                                    <td>{{ number_format(max(0, $recap['total_terms'] - $recap['uploaded']), 0, ',', '.') }}</td>
                                    <td>
                                        @if($recap['uploaded'] === $recap['total_terms'])
                                            <span class="badge badge-success">LENGKAP</span>
                                        @else
                                            <span class="badge badge-warning">BELUM LENGKAP</span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach

                                <tr class="referral-recap-row">
                                    <td></td>
                                    <td><strong>Total Semua Periode</strong></td>
                                    <td><strong>Semua Mitra</strong></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><strong>Rp {{ number_format($totalFee, 0, ',', '.') }}</strong></td>
                                    <td>-</td>
                                    <td><strong>{{ number_format($totalReferralUploaded, 0, ',', '.') }}</strong></td>
                                    <td><strong>{{ number_format($totalReferralPending, 0, ',', '.') }}</strong></td>
                                    <td>-</td>
                                    <td></td>
                                </tr>

                                @forelse($details as $item)
                                <tr class="invoice-detail-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['nama_perusahaan'] ?? $namaPerusahaan }}</td>
                                    <td>{{ $item['vendor_name'] ?? '-' }}</td>
                                    <td>{{ $item['nomor_invoice'] ?? '-' }}</td>
                                    <td>{{ $item['purchase_order_number'] ?? '-' }}</td>
                                    <td>Rp {{ number_format($item['approved_financing_amount'] ?? 0, 0, ',', '.') }}</td>
                                    @for($termNumber = 1; $termNumber <= 3; $termNumber++)
                                    @php
                                    $term = $item['terms'][$termNumber] ?? null;
                                    @endphp
                                    <td class="term-cell">
                                        @if($term)
                                        <div class="term-amount">
                                            Rp {{ number_format($term['term_amount'] ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="term-line">
                                            Fee: Rp {{ number_format($term['total_fee'] ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="term-line">
                                            Tanggal:
                                            @if(!empty($term['finance_invoice_term_date']))
                                            {{ \Carbon\Carbon::parse($term['finance_invoice_term_date'])->format('d-m-Y') }}
                                            @else
                                            -
                                            @endif
                                        </div>

                                        @if($term['can_download'] ?? false)
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm btn-danger btn-download-invoice invoice-download-action"
                                            data-url="{{ route('admin.invoice-financing.term.download', ['id' => $term['invoice_id'], 'term' => $term['term']]) }}"
                                            title="Download Invoice Termin {{ $termNumber }}">
                                            <i class="mdi mdi-file-pdf-box"></i> Invoice
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary btn-upload-referral referral-upload-action"
                                            data-id="{{ $term['invoice_id'] }}"
                                            data-term="{{ $term['term'] }}"
                                            data-invoice="{{ $term['nomor_invoice'] ?? '-' }}">
                                            <i class="mdi mdi-upload"></i> Upload
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-secondary invoice-download-action" disabled
                                            title="Termin sebelumnya belum selesai diproses">
                                            <i class="mdi mdi-lock"></i> Terkunci
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary btn-upload-referral referral-upload-action"
                                            data-id="{{ $term['invoice_id'] }}"
                                            data-term="{{ $term['term'] }}"
                                            data-invoice="{{ $term['nomor_invoice'] ?? '-' }}">
                                            <i class="mdi mdi-upload"></i> Upload
                                        </button>
                                        @endif

                                        <div class="referral-upload-action mt-1">
                                            @if(!empty($term['referral_transfer_proof']))
                                                <span class="badge badge-success">SUDAH UPLOAD</span>
                                                <small class="text-muted d-block">
                                                    {{ !empty($term['referral_transfer_date']) ? \Carbon\Carbon::parse($term['referral_transfer_date'])->format('d-m-Y') : '-' }}
                                                </small>
                                            @else
                                                <span class="badge badge-warning">BELUM UPLOAD</span>
                                            @endif
                                        </div>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    @endfor
                                    <td>Rp {{ number_format($item['total_dibayarkan'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['total_fee'] ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ strtoupper(str_replace('_', ' ', $item['status'] ?? '-')) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">Belum ada data TomiInvoice.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUploadReferral" tabindex="-1" role="dialog"
    aria-labelledby="modalUploadReferralLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formUploadReferral" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUploadReferralLabel">Upload Bukti Transfer Referal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="text-muted mb-3" id="uploadReferralInvoiceText"></p>

                    <div class="form-group">
                        <label>Tanggal Transfer</label>
                        <input type="date" class="form-control" name="referral_transfer_date"
                            id="referral_transfer_date">
                        <small class="text-danger" id="errReferralTransferDate"></small>
                    </div>

                    <div class="form-group">
                        <label>Bukti Transfer</label>
                        <input type="file" class="form-control" name="referral_transfer_proof"
                            id="referral_transfer_proof" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted d-block">Format: JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                        <small class="text-danger" id="errReferralTransferProof"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="buttonUploadReferral">
                        Upload <div id="loadingUploadReferral"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDownloadInvoice" tabindex="-1" role="dialog"
    aria-labelledby="modalDownloadInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDownloadInvoice" method="GET" target="_blank">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDownloadInvoiceLabel">Pilih Kolom Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="column_selection" value="1">

                    @php
                    $invoiceColumns = [
                    'vendor' => 'Pelanggan Mitra',
                    'purchase_order_number' => 'No. PO',
                    'approved_financing_amount' => 'Nilai Pembiayaan',
                    'term_amount' => 'Nominal Termin',
                    'fee_tomi' => 'Fee TomiInvoice',
                    'admin_fee' => 'Admin Fee Termin',
                    'total_fee' => 'Total Fee',
                    ];
                    @endphp

                    @foreach($invoiceColumns as $value => $label)
                    <div class="form-check mb-2">
                        <input class="form-check-input invoice-column-checkbox" type="checkbox" name="columns[]"
                            value="{{ $value }}" id="tomiInvoiceCol{{ \Illuminate\Support\Str::studly($value) }}"
                            checked>
                        <label class="form-check-label"
                            for="tomiInvoiceCol{{ \Illuminate\Support\Str::studly($value) }}">
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-download"></i> Download PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        let detailDataTable = null;
        let uploadReferralUrl = '';

        if ($('#detailTable').length) {
            if ($.fn.DataTable.isDataTable('#detailTable')) {
                $('#detailTable').DataTable().destroy();
            }

            detailDataTable = $('#detailTable').DataTable({
                processing: true,
                pageLength: 10,
                scrollX: true,
                autoWidth: false,
                ordering: true,
                order: [[3, 'desc']],
                columnDefs: [{
                    targets: '_all',
                    className: 'text-nowrap'
                }],
                language: {
                    emptyTable: "Belum ada data TomiInvoice",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }

        const setInvoiceMode = (mode) => {
            $('.invoice-mode-btn')
                .removeClass('active')
                .attr('aria-selected', 'false');

            $(`.invoice-mode-btn[data-mode="${mode}"]`)
                .addClass('active')
                .attr('aria-selected', 'true');

            $('body').toggleClass('invoice-mode-upload-referral', mode === 'upload-referral');
            $('body').toggleClass('invoice-mode-recap-referral', mode === 'recap-referral');

            if (mode === 'invoice') {
                $('#tableTitle').text('Daftar Invoice');
            } else if (mode === 'upload-referral') {
                $('#tableTitle').text('Upload Bukti Transfer Referal');
            } else {
                $('#tableTitle').text('Rekap Referal');
            }

            $('#detailTable thead th').each(function () {
                const label = mode === 'recap-referral' ? $(this).data('recap-label') : $(this).data('invoice-label');
                $(this).text(label || '');
            });

            if (detailDataTable) {
                detailDataTable.columns.adjust();
                setTimeout(() => {
                    detailDataTable.columns.adjust().draw(false);
                }, 50);
            }
        };

        $(document).on('click', '.invoice-mode-btn', function (event) {
            event.preventDefault();
            setInvoiceMode($(this).data('mode'));
        });

        $(document).on('click', '.btn-download-invoice', function () {
            $('#formDownloadInvoice').attr('action', $(this).data('url'));
            $('.invoice-column-checkbox').prop('checked', true);
            $('#modalDownloadInvoice').modal('show');
        });

        $(document).on('click', '.btn-upload-referral', function () {
            const invoiceId = $(this).data('id');
            const term = $(this).data('term');
            const invoiceNumber = $(this).data('invoice');

            uploadReferralUrl = "{{ route('admin.invoice-financing.referral-transfer-proof.upload', ['id' => '__ID__', 'term' => '__TERM__']) }}"
                .replace('__ID__', invoiceId)
                .replace('__TERM__', term);

            $('#formUploadReferral')[0].reset();
            $('#errReferralTransferDate').text('');
            $('#errReferralTransferProof').text('');
            $('#uploadReferralInvoiceText').text(`Invoice: ${invoiceNumber} | Termin ${term}`);
            $('#modalUploadReferral').modal('show');
        });

        $('#formUploadReferral').on('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            $('#errReferralTransferDate').text('');
            $('#errReferralTransferProof').text('');

            $.ajax({
                url: uploadReferralUrl,
                data: formData,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#buttonUploadReferral').attr('disabled', true);
                    $('#loadingUploadReferral').addClass('spinner-border');
                },
                success: function (response) {
                    toastr.success(response.message);
                    $('#modalUploadReferral').modal('hide');
                    window.location.reload();
                },
                error: function (err) {
                    $('#buttonUploadReferral').attr('disabled', false);
                    $('#loadingUploadReferral').removeClass('spinner-border');

                    if (err.responseJSON && err.responseJSON.errors) {
                        $('#errReferralTransferDate').text(err.responseJSON.errors.referral_transfer_date);
                        $('#errReferralTransferProof').text(err.responseJSON.errors.referral_transfer_proof);
                        return;
                    }

                    toastr.error(err.responseJSON?.message || 'Bukti transfer referal gagal diupload.');
                }
            });
        });
    });
</script>
@endpush
