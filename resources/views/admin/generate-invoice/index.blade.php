@extends('layout.app')
@section('title', 'Generate Invoice')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="page-title mb-0">Generate Invoice</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                <li class="breadcrumb-item active">Generate Invoice</li>
            </ol>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card widget-flat border-primary shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-file-document-outline text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h6 class="text-muted text-uppercase font-size-14">Total Invoice</h6>
                <h3 class="mt-3 mb-0" id="totalInvoice">0</h3>
                <p class="text-muted mb-0 font-size-13"><span class="text-success me-1">▼</span>Updated today</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card widget-flat border-success shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cash-multiple text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h6 class="text-muted text-uppercase font-size-14">Total Pinjaman</h6>
                <h3 class="mt-3 mb-0" id="totalPinjaman">Rp 0</h3>
                <p class="text-muted mb-0 font-size-13">Dari semua invoice</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card widget-flat border-info shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-wallet-outline text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h6 class="text-muted text-uppercase font-size-14">Total Dibayarkan</h6>
                <h3 class="mt-3 mb-0" id="totalDibayarkan">Rp 0</h3>
                <p class="text-muted mb-0 font-size-13">Sudah terverifikasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card widget-flat border-warning shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-percent text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h6 class="text-muted text-uppercase font-size-14">Fee Tomi</h6>
                <h3 class="mt-3 mb-0" id="feeTomi">Rp 0</h3>
                <p class="text-muted mb-0 font-size-13">Komisi terakumulasi</p>
            </div>
        </div>
    </div>
</div>

{{-- Action Section --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-upload me-2 text-primary"></i>Generate Invoice
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    {{-- Download Template --}}
                    <div class="col-lg-12">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold mb-2">📥 Butuh Template?</h6>
                            <p class="text-muted small mb-3">Download format Excel yang sesuai untuk upload data
                                invoice.</p>
                            <a href="{{ route('admin.generate.invoice.download') }}"
                                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2"
                                id="btnDownloadTemplate">
                                <i class="mdi mdi-download"></i>
                                Download Template Excel
                            </a>
                            <small class="text-muted d-block mt-2 text-center">Format: .xlsx • Max 10MB</small>
                        </div>
                    </div>
                    <!-- 
                        {{-- Upload & Generate --}}
                        <div class="col-lg-7">
                            <form id="formGenerateInvoice" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="invoiceFile" class="form-label fw-bold">📤 Upload File Excel</label>
                                    <div class="input-group">
                                        <input type="file"
                                            class="form-control @error('invoiceFile') is-invalid @enderror" id="invoiceFile"
                                            name="invoiceFile" accept=".xlsx,.xls" required>
                                        <label class="input-group-text" for="invoiceFile">
                                            <i class="mdi mdi-folder-open"></i>
                                        </label>
                                    </div>
                                    @error('invoiceFile')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Hanya file Excel (.xlsx, .xls) • Max 10MB</small>
                                </div>

                                {{-- Progress Bar (Hidden by default) --}}
                                <div id="progressContainer" class="mb-3 d-none">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted" id="progressText">Memproses file...</small>
                                        <small class="text-muted" id="progressPercent">0%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            id="progressBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 flex-wrap float-right">
                                    <button type="submit" class="btn btn-primary btn-sm px-4 mr-4" id="btnGenerate">
                                        <i class="mdi mdi-play-circle me-1"></i>Generate Invoice
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReset">
                                        <i class="mdi mdi-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </form> -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- History Table --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-history me-2 text-primary"></i>Riwayat Generate Invoice
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableGenerateInvoice" class="table table-hover align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Perusahaan</th>
                                <th class="text-end">Total Peminjam</th>
                                <th class="text-end">Total Pinjaman</th>
                                <th class="text-end">Dibayarkan</th>
                                <th class="text-end">Fee Tomi</th>
                                <th>Tanggal</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded via DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="mdi mdi-check-circle text-success me-2"></i>
            <strong class="me-auto">Success</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Invoice berhasil digenerate!
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {

        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };

        function formatRupiah(angka) {
            if (angka === null || angka === undefined || angka === '') {
                return 'Rp. 0';
            }

            const number = parseInt(angka, 10);
            if (isNaN(number)) {
                return 'Rp. 0';
            }

            return 'Rp. ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        const table = $('#tableGenerateInvoice').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [{
                extend: 'excel',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                header: true,
                title: 'List Invoice Generate',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                },
            },
            {
                extend: 'copy',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                header: true,
                title: 'List Invoice Generate',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                },
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                header: true,
                title: 'List Invoice Generate',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                },
            }
            ],
            "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                type: "POST",
                url: '{!! route('admin.generate.invoice.table') !!}',
                data: function (xx) {
                    const finalData = $.extend({}, xx, dataTable);

                    console.log('DataTables raw xx:', xx);
                    console.log('dataTable tambahan:', dataTable);
                    console.log('Final data yang dikirim:', finalData);

                    return finalData;
                },
                beforeSend: function (xhr) {
                    console.log('Request akan dikirim ke:', '{!! route('admin.generate.invoice.table') !!}');
                },
                dataSrc: function (response) {
                    console.log('Response dari server:', response);
                    return response.data;
                },
                error: function (xhr, error, thrown) {
                    console.log('XHR object:', xhr);
                    console.log('Status:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    console.log('Error:', error);
                    console.log('Thrown:', thrown);
                }
            },

            columns: [{
                data: 'nama_perusahaan'
            }, {
                data: 'jumlah_peminjam'
            }, {
                data: 'total_pinjaman'
            }, {
                data: 'total_dibayarkan'
            }, {
                data: 'total_fee'
            }, {
                data: 'datetime'
            }, {
                data: 'action'
            }]
        });

        // Helper Format Rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

        // Inisialisasi ajaxForm pada formGenerateInvoice
        $('#formGenerateInvoice').ajaxForm({
            url: '{{ route('admin.generate.invoice.generate') }}', // Menggunakan route yang Anda minta
            type: 'POST',
            beforeSend: function () {
                // Logika awal sesuai code pertama Anda
                $('#errUploadData').text(''); // Jika ada elemen error text
                $('#btnGenerate').attr("disabled", true);
                $('#btnGenerate').html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Memproses...');

                // Tampilkan dan reset progress bar
                $('#progressContainer').removeClass('d-none');
                $('#progressBar').css("width", '0%').attr("aria-valuenow", 0);
                $('#progressPercent').text('0%');
            },
            uploadProgress: function (event, position, total, percentComplete) {
                // Update progress bar saat upload berlangsung
                var percentage = percentComplete;
                $('#progressBar').css("width", percentage + '%').attr("aria-valuenow", percentage);
                $('#progressPercent').text(percentage + '%');
            },
            error: function (xhr, status, error) {
                // Reset UI jika gagal (Logika error dari code Anda)
                $('#invoiceFile').val('');
                $('#progressContainer').addClass('d-none');
                $('#btnGenerate').attr("disabled", false);
                $('#btnGenerate').html('<i class="mdi mdi-play-circle me-1"></i>Generate Invoice');

                // Menampilkan pesan error
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                    .message : 'Terjadi kesalahan sistem.';
                toastr.error(msg);
            },
            success: function (xhr) {
                // Logika sukses sesuai code pertama Anda
                // table.ajax.reload(); // Ganti ke instance table Anda
                if (window.invoiceTable) {
                    window.invoiceTable.ajax.reload(null, false);
                }

                $('#invoiceFile').val('');
                $('#progressContainer').addClass('d-none');
                $('#btnGenerate').attr("disabled", false);
                $('#btnGenerate').html('<i class="mdi mdi-play-circle me-1"></i>Generate Invoice');

                // Update statistik kartu (Total Pinjaman, dll)
                refreshStats();

                // Notifikasi sukses
                toastr.success(xhr.message || 'Invoice berhasil digenerate!');
            }
        });

        // Fungsi untuk refresh data statistik di atas
        function refreshStats() {
            $.get('{{ route('admin.generate.invoice.stats') }}', function (stats) {
                $('#totalInvoice').text(stats.total_invoice);
                $('#totalPinjaman').text(formatRupiah(stats.total_pinjaman));
                $('#totalDibayarkan').text(formatRupiah(stats.total_dibayarkan));
                $('#feeTomi').text(formatRupiah(stats.fee_tomi));
            });
        }

        // Tombol Reset
        $('#btnReset').on('click', function () {
            $('#formGenerateInvoice')[0].reset();
            $('#progressContainer').addClass('d-none');
            $('#btnGenerate').attr("disabled", false);
        });

        // Load statistik saat pertama kali halaman dibuka
        refreshStats();
    });
</script>
@endpush