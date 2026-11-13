@extends('layout.app')

@section('title', 'Dashboard Tagihan Pemberi Pinjaman')

@php
$formatRupiah = static fn ($value) => 'Rp. ' . number_format((float) ($value ?? 0), 0, ',', '.');
$formatNumber = static fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.');

$summary = $summary ?? [];
$invoices = $invoices ?? [];
$lenders = $lenders ?? [];
$selectedInvoice = $selectedInvoice ?? null;

$summaryCards = [
['label' => 'TOTAL BIAYA LAYANAN', 'value' => $formatRupiah($summary['service_fee'] ?? 0), 'icon' => 'fa fa-layer-group', 'color' => 'text-primary'],
['label' => 'TOTAL BIAYA ADMIN', 'value' => $formatRupiah($summary['admin_fee'] ?? 0), 'icon' => 'fa fa-money-bill', 'color' => 'text-primary'],
['label' => 'TOTAL TAGIHAN', 'value' => $formatRupiah($summary['total_billing'] ?? 0), 'icon' => 'fa fa-calculator', 'color' => 'text-primary'],
['label' => 'SUDAH TERBAYAR', 'value' => $formatNumber($summary['paid_count'] ?? 0), 'amount' => $formatRupiah($summary['paid_amount'] ?? 0), 'icon' => 'fa fa-check', 'color' => 'text-success'],
['label' => 'MULAI JATUH TEMPO', 'value' => $formatNumber($summary['due_count'] ?? 0), 'amount' => $formatRupiah($summary['due_amount'] ?? 0), 'icon' => 'fa fa-clock', 'color' => 'text-danger'],
];

$statusStyles = [
'draft' => ['label' => 'DRAF', 'class' => 'badge-secondary'],
'menunggu' => ['label' => 'MENUNGGU PENAGIHAN', 'class' => 'badge-primary'],
'mulai_jatuh_tempo' => ['label' => 'MULAI JATUH TEMPO', 'class' => 'badge-warning'],
'jatuh_tempo' => ['label' => 'JATUH TEMPO', 'class' => 'badge-danger'],
'lunas' => ['label' => 'SUDAH TERBAYAR', 'class' => 'badge-success'],
];
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">TomiDana</a></li>
                    <li class="breadcrumb-item active">Tagihan Pemberi Pinjaman</li>
                </ol>
            </div>
            <h4 class="page-title">Dashboard Tagihan Pemberi Pinjaman</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="row">
            @foreach ($summaryCards as $card)
            <div class="col-xl col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ $card['label'] }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $card['value'] }}</div>
                                @if (isset($card['amount']))
                                <small class="text-muted">{{ $card['amount'] }}</small>
                                @endif
                            </div>
                            <div class="col-auto">
                                <i class="{{ $card['icon'] }} fa-2x {{ $card['color'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row align-items-end">
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="invoiceMonth">Periode Bulan</label>
                                <select class="form-control" id="invoiceMonth" name="month">
                                    <option value="" @selected(!request()->filled('month'))>SEMUA BULAN</option>
                                    @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @selected((int) request('month')===$month)>{{
                                        strtoupper(\Carbon\Carbon::create()->month($month)->translatedFormat('F')) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="invoiceYear">Tahun</label>
                                <select class="form-control" id="invoiceYear" name="year">
                                    <option value="" @selected(!request()->filled('year'))>SEMUA TAHUN</option>
                                    @foreach (range(now()->year - 2, now()->year + 1) as $year)
                                    <option value="{{ $year }}" @selected((int) request('year')===$year)>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label for="invoiceLender">Pemberi Pinjaman</label>
                                <select class="form-control" id="invoiceLender" name="lender_id">
                                    <option value="">SEMUA PEMBERI PINJAMAN</option>
                                    @foreach ($lenders as $lender)
                                    <option value="{{ data_get($lender, 'id') }}" @selected((string) request('lender_id')===(string) data_get($lender, 'id'))>{{ strtoupper(data_get($lender, 'nama', '-')) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label for="invoiceStatus">Status Invoice</label>
                                <select class="form-control" id="invoiceStatus" name="status">
                                    <option value="">SEMUA STATUS</option>
                                    @foreach ($statusStyles as $key => $style)
                                    <option value="{{ $key }}" @selected(request('status')===$key)>{{ $style['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">
                                    <i class="mdi mdi-filter-variant"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="tableLenderInvoices" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Pemberi Pinjaman</th>
                            <th>Periode</th>
                            <th>Jumlah Transaksi</th>
                            <th>Biaya Layanan</th>
                            <th>Biaya Admin</th>
                            <th>Total Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Download</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                        @php($style = $statusStyles[data_get($invoice, 'status', 'draft')] ?? $statusStyles['draft'])
                        <tr class="{{ $selectedInvoice && data_get($selectedInvoice, 'id') == data_get($invoice, 'id') ? 'table-active' : '' }}">
                            <td>{{ strtoupper(data_get($invoice, 'invoice_number', '-')) }}</td>
                            <td>{{ strtoupper(data_get($invoice, 'lender_name', '-')) }}</td>
                            <td>{{ strtoupper(data_get($invoice, 'period_label', '-')) }}</td>
                            <td>{{ $formatNumber(data_get($invoice, 'transaction_count', 0)) }}</td>
                            <td>{{ $formatRupiah(data_get($invoice, 'service_fee', 0)) }}</td>
                            <td>{{ $formatRupiah(data_get($invoice, 'admin_fee', 0)) }}</td>
                            <td>{{ $formatRupiah(data_get($invoice, 'amount', 0)) }}</td>
                            <td>{{ strtoupper(data_get($invoice, 'due_date', '-')) }}</td>
                            <td><span class="badge {{ $style['class'] }}">{{ $style['label'] }}</span></td>
                            <td>
                                @if (data_get($invoice, 'download_url'))
                                <a class="btn btn-sm btn-info download-invoice" href="#"
                                    data-url="{{ data_get($invoice, 'download_url') }}" title="Download Invoice"
                                    aria-label="Download Invoice"><i class="mdi mdi-download"></i></a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php($proofUrl = data_get($invoice, 'status') === 'lunas' ? data_get($invoice, 'payment_proof_url') : null)
                                <button type="button"
                                    class="btn btn-sm {{ $proofUrl ? 'btn-success' : 'btn-primary' }} edit-invoice-payment"
                                    title="{{ $proofUrl ? 'Detail pembayaran invoice' : 'Upload bukti pembayaran invoice' }}"
                                    data-invoice-id="{{ data_get($invoice, 'id') }}"
                                    data-invoice-number="{{ data_get($invoice, 'invoice_number') }}"
                                    data-payment-date="{{ data_get($invoice, 'payment_date') }}"
                                    data-proof-url="{{ $proofUrl }}">
                                    <i class="mdi {{ $proofUrl ? 'mdi-eye' : 'mdi-upload' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- modal pilih kolom download --}}
<div class="modal fade" id="downloadInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Kolom Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                @foreach ([
                'rate' => 'Biaya Rate',
                'asuransi' => 'Biaya Asuransi',
                'provisi' => 'Biaya Provisi',
                'biaya_admin' => 'Biaya Admin',
                'fee_tomi' => 'Biaya Fee TOMI',
                'biaya_layanan' => 'Biaya Layanan',
                'referral_1' => 'Biaya Profesional Fee 1',
                'referral_2' => 'Biaya Profesional Fee 2',
                'ksp' => 'Biaya Lender',
                ] as $field => $label)
                <div class="form-check mb-2">
                    <input class="form-check-input invoice-column" type="checkbox" value="{{ $field }}"
                        id="invoiceColumn{{ ucfirst($field) }}" checked>
                    <label class="form-check-label" for="invoiceColumn{{ ucfirst($field) }}">{{ $label }}</label>
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batal</button>
                <a href="#" id="confirmDownloadInvoice" target="_blank"
                    class="btn btn-primary waves-effect waves-light">Download Invoice</a>
            </div>
        </div>
    </div>
</div>

{{-- modal pembayaran --}}
<div class="modal fade" id="invoicePaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="invoicePaymentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran Invoice Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
                    <div class="form-group" id="paymentProofUpload">
                        <label for="paymentProof">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti_transfer" id="paymentProof"
                            accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="form-text text-muted">Format JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                    </div>
                    <div class="form-group d-none" id="paymentProofView">
                        <label>Bukti Transfer</label>
                        <div>
                            <a href="#" id="paymentProofLink" target="_blank" rel="noopener"
                                class="btn btn-success btn-block waves-effect waves-light">
                                <i class="mdi mdi-eye"></i> Lihat Bukti Transfer</a>
                        </div>
                    </div>
                    <div class="form-check" id="paymentDeclarationGroup">
                        <input type="checkbox" class="form-check-input" name="pernyataan" value="1"
                            id="paymentDeclaration" required>
                        <label class="form-check-label" for="paymentDeclaration">Saya menyatakan bahwa invoicing
                            terhadap lender ini telah terbayarkan</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitInvoicePayment"
                        disabled>
                        <i class="mdi mdi-content-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        const exportColumns = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $('#tableLenderInvoices').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: true,
                    title: 'Tagihan Pemberi Pinjaman',
                    exportOptions: { columns: exportColumns },
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: true,
                    title: 'Tagihan Pemberi Pinjaman',
                    exportOptions: { columns: exportColumns },
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: true,
                    title: 'Tagihan Pemberi Pinjaman',
                    exportOptions: { columns: exportColumns },
                }
            ],
            lengthMenu: [10, 50, 100, 500],
            processing: true,
            responsive: false,
            scrollX: true,
            order: [[2, 'desc']],
            columnDefs: [{ orderable: false, targets: [9, 10] }],
            language: {
                emptyTable: 'Belum ada invoice tagihan lender',
                zeroRecords: 'Invoice tidak ditemukan',
            }
        });

        $(document).on('click', '.download-invoice', function (event) {
            event.preventDefault();
            $('#confirmDownloadInvoice').attr('href', $(this).data('url'));
            $('#downloadInvoiceModal').modal('show');
        });

        $('#confirmDownloadInvoice').on('click', function () {
            const invoiceUrl = new URL($(this).attr('href'), window.location.origin);
            invoiceUrl.searchParams.delete('fields[]');
            $('.invoice-column:checked').each(function () {
                invoiceUrl.searchParams.append('fields[]', $(this).val());
            });
            $(this).attr('href', invoiceUrl.toString());
        });

        $(document).on('click', '.edit-invoice-payment', function () {
            const button = $(this);
            $('#invoicePaymentForm')[0].reset();
            $('#paymentInvoiceId').val(button.data('invoice-id'));
            $('#paymentInvoiceNumber').val(button.data('invoice-number'));
            $('#paymentDate').val(button.data('payment-date') || '');

            const proofUrl = button.data('proof-url') || '';
            const isDetail = proofUrl !== '';
            $('#invoicePaymentModal .modal-title').text(isDetail ? 'Detail Pembayaran Invoice Lender' : 'Pembayaran Invoice Lender');
            $('#paymentDate').prop('readonly', isDetail);
            $('#paymentProofUpload').toggleClass('d-none', isDetail);
            $('#paymentProof').prop({ disabled: isDetail, required: !isDetail });
            $('#paymentProofView').toggleClass('d-none', !isDetail);
            $('#paymentProofLink').attr('href', proofUrl || '#');
            $('#paymentDeclarationGroup').toggleClass('d-none', isDetail);
            $('#paymentDeclaration').prop({ disabled: isDetail, required: !isDetail });
            $('#submitInvoicePayment').toggleClass('d-none', isDetail).prop('disabled', true);

            $('#invoicePaymentModal').modal('show');
        });

        $('#paymentDeclaration').on('change', function () {
            $('#submitInvoicePayment').prop('disabled', !this.checked);
        });

        $('#invoicePaymentForm').on('submit', function (event) {
            event.preventDefault();

            const submitButton = $('#submitInvoicePayment');
            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');
            submitButton.prop('disabled', true);

            $.ajax({
                url: '{{ route('admin.tomi' . '-dana.lender.invoice.payment') }}',
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
                    submitButton.prop('disabled', !$('#paymentDeclaration').is(':checked'));
                }
            });
        });
    });
</script>
@endpush
