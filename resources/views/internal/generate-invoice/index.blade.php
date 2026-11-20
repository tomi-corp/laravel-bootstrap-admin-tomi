@extends('internal.layouts.app')

@section('title', 'Generate Invoice')

@push('css')
    <style>
        .company-nav-scroll { width: 100%; overflow-x: auto; overflow-y: hidden; white-space: nowrap; padding-bottom: 8px; }
        .company-nav-scroll .nav { display: flex; flex-wrap: nowrap; min-width: max-content; }
        .company-nav-scroll .nav-item { flex: 0 0 auto; margin-right: 6px; }
        .company-nav-scroll .nav-link { white-space: nowrap; }

        #generateInvoiceTable th,
        #generateInvoiceTable td { white-space: nowrap; vertical-align: middle; }
        .table-scroll { clear: both; overflow-x: auto; margin-top: 1rem; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Fitur</a></li>
                        <li class="breadcrumb-item active">Buat Tagihan</li>
                    </ol>
                </div>
                <h4 class="page-title">Generate Invoice</h4>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Upload Invoice --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Upload File Invoice</h4>
                    <p class="text-muted">
                        Silakan upload file Excel dengan format <strong>.xls</strong> atau <strong>.xlsx</strong>
                        untuk proses generate invoice.
                    </p>

                    <form action="{{ route('internal.generate-invoice.store') }}" method="POST" enctype="multipart/form-data" id="generateInvoiceForm">
                        @csrf

                        <div class="form-group">
                            <label for="invoiceFile">File Invoice</label>
                            <input type="file" name="invoiceFile" id="invoiceFile"
                                class="form-control @error('invoiceFile') is-invalid @enderror" accept=".xls,.xlsx">

                            @error('invoiceFile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="btnUploadInvoice">
                                <i class="mdi mdi-upload"></i> Upload & Generate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Petunjuk Upload --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Petunjuk Upload</h4>
                    <ul class="pl-3 mb-0">
                        <li>File harus berformat <strong>.xls</strong> atau <strong>.xlsx</strong></li>
                        <li>Ukuran file maksimal <strong>10 MB</strong></li>
                        <li>Header wajib: <strong>NAMA PERUSAHAAN</strong> (atau TEMPAT BEKERJA), <strong>NAMA</strong>, <strong>JUMLAH PINJAMAN</strong>, <strong>TOTAL DIBAYARKAN</strong>, <strong>TOTAL FEE</strong></li>
                        <li>Satu file boleh berisi beberapa perusahaan; invoice dibuat per perusahaan</li>
                        <li>Pastikan nama perusahaan pada file Excel sudah sesuai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel data hasil import --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Data Invoice yang Sudah Diimport</h4>

                    @if ($companies->count() > 0)
                        <div class="company-nav-scroll mb-3">
                            <ul class="nav nav-pills flex-nowrap">
                                <li class="nav-item">
                                    <a class="nav-link {{ empty($selectedCompany) ? 'active' : '' }}"
                                        href="{{ route('internal.generate-invoice') }}">Semua Perusahaan</a>
                                </li>
                                @foreach ($companies as $company)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $selectedCompany == $company ? 'active' : '' }}"
                                            href="{{ route('internal.generate-invoice', ['nama_perusahaan' => $company]) }}">{{ $company }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table id="generateInvoiceTable" class="table table-hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama Perusahaan</th>
                                <th>Nama File</th>
                                <th>Total Karyawan</th>
                                <th>Total Pendapatan</th>
                                <th>Total Dibayarkan</th>
                                <th>Total Fee</th>
                                <th>Tanggal Import</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->nama_perusahaan ?? '-' }}</td>
                                    <td>{{ $invoice->filename ?? '-' }}</td>
                                    <td>{{ number_format($invoice->jumlah_peminjam ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($invoice->total_pinjaman ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($invoice->total_dibayarkan ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($invoice->total_fee ?? 0, 0, ',', '.') }}</td>
                                    <td data-order="{{ $invoice->created_at?->timestamp }}">{{ $invoice->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('internal.generate-invoice.pdf', $invoice->id) }}"
                                            class="btn btn-sm btn-danger" target="_blank">
                                            <i class="mdi mdi-file-pdf-outline"></i> Download Invoice
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            $('#generateInvoiceTable').DataTable({
                dom: 'lf<"table-scroll"t>ip',
                pageLength: 10,
                order: [[6, 'desc']],
                columnDefs: [{ targets: 7, orderable: false, searchable: false }],
                language: {
                    emptyTable: 'Belum ada data invoice yang diimport',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: { first: 'Awal', last: 'Akhir', next: '›', previous: '‹' }
                }
            });

            // cegah upload ganda saat file besar sedang diproses
            $('#generateInvoiceForm').on('submit', function () {
                $('#btnUploadInvoice').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Memproses...');
            });
        });
    </script>
@endpush
