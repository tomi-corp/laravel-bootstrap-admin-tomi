<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoiceNumber }}</title>
    <style>
        @page { size: A4 landscape; margin: 28px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #343a40; font-family: Arial, sans-serif; font-size: 11px; }
        .toolbar { margin: 0 0 18px; width: 100%; text-align: right; }
        .toolbar a { display: inline-block; border-radius: 4px; color: #fff; margin-left: 6px; padding: 9px 14px; text-decoration: none; }
        .toolbar .download { background: #dc3545; }
        .toolbar .back { background: #6c757d; }
        .invoice { width: 100%; margin: 0; }
        .header { border-bottom: 2px solid #343a40; margin-bottom: 20px; padding-bottom: 12px; }
        .clearfix::after { clear: both; content: ''; display: table; }
        .left { float: left; }
        .right { float: right; text-align: right; }
        .logo { height: 45px; }
        h1 { color: #007bff; font-size: 22px; margin: 0 0 5px; }
        .muted { color: #6c757d; }
        .meta { margin-bottom: 20px; }
        .meta-item { display: inline-block; vertical-align: top; width: 24%; }
        .meta-label { color: #6c757d; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .meta-value { font-weight: bold; margin-top: 4px; }
        .introduction { line-height: 1.6; margin-bottom: 20px; }
        .introduction p { margin: 0 0 8px; }
        .introduction .invoice-detail { margin: 3px 0; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #f1f3f5; color: #495057; font-size: 9px; text-align: left; text-transform: uppercase; }
        th, td { border: 1px solid #cfd4da; padding: 7px; }
        .number { text-align: right; }
        .payment-summary { margin-top: 18px; }
        .payment-info { float: left; line-height: 1.6; width: calc(100% - 320px); }
        .bank-details { border-collapse: collapse; margin-top: 4px; width: auto; }
        .bank-details th, .bank-details td { background: transparent; border: 0; font-size: 11px; padding: 0 6px 0 0; text-transform: none; vertical-align: top; }
        .bank-details th { min-width: 105px; text-align: right; }
        .bank-details .separator { padding-right: 8px; text-align: center; width: 18px; }
        .summary { margin-left: auto; margin-top: 0; width: 300px; }
        .summary th, .summary td { border: 0; padding: 4px 0; }
        .summary th { background: transparent; font-size: 11px; text-transform: none; }
        .status { border-radius: 3px; color: #fff; display: inline-block; font-size: 9px; padding: 4px 7px; }
        .status-paid { background: #28a745; }
        .status-pending { background: #ffc107; color: #212529; }
        .note { border-top: 1px solid #dee2e6; color: #6c757d; margin-top: 25px; padding-top: 10px; }
        @media print {
            .toolbar { display: none; }
            .invoice { max-width: none; }
        }
    </style>
</head>
<body>
    @if (! $isPdf)
        <div class="toolbar">
            <a href="{{ route('admin.tomi-dana.repayments.invoice.pdf', $invoice->id) }}" class="download">Download PDF</a>
            <a href="{{ route('admin.tomi-dana.repayments') }}" class="back">Kembali</a>
        </div>
    @endif

    <div class="invoice">
        <div class="header clearfix">
            <div class="left">
                <img src="{{ $isPdf ? public_path('assets/images/logo.jpg') : asset('assets/images/logo.jpg') }}" class="logo" alt="Logo TOMI">
            </div>
            <div class="right">
                <h1>INVOICE TOMIDANA</h1>
                <div><strong>Nomor Invoice:</strong> {{ $invoiceNumber }}</div>
                <div><strong>Tanggal:</strong> {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="introduction">
            <p>Hallo, {{ strtoupper($company->nama ?? '-') }}</p>
            <p>Berikut rincian tagihan angsuran atas pinjaman payroll karyawan {{ strtoupper($company->nama ?? '-') }} untuk periode {{ $invoicePeriod->format('M-Y') }}</p>
            <p class="invoice-detail"><strong>No. Invoice:</strong> {{ $invoiceNumber }}</p>
            <p class="invoice-detail"><strong>Lampiran Pinjaman ID Perusahaan:</strong> {{ $company->id_code_perusahaan ?? '-' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal Pinjaman</th>
                    <th>Nama</th>
                    <th class="number">Jumlah Pinjaman</th>
                    <th class="number">Jumlah Angsuran</th>
                    <th class="number">Jumlah yang Diterima</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pinjaman as $loan)
                    @php
                        $tanggalPinjaman = $loan->tanggal_pinjaman ?: $loan->created_at;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $tanggalPinjaman ? \Carbon\Carbon::parse($tanggalPinjaman)->format('d/m/Y') : '-' }}<br>
                            <small>{{ $loan->id_pinjaman ?? ('TD-' . $loan->id) }}</small>
                        </td>
                        <td>
                            {{ strtoupper($loan->data_karyawan?->nama_karyawan ?? $loan->user?->name ?? '-') }}<br>
                            <small>NIP: {{ $loan->data_karyawan?->nip ?? '-' }}</small>
                        </td>
                        <td class="number">Rp. {{ number_format((float) ($loan->nominal ?? 0), 0, ',', '.') }}</td>
                        <td class="number">Rp. {{ number_format((float) ($loan->total_angsuran_perbulan ?? 0), 0, ',', '.') }}</td>
                        <td class="number">Rp. {{ number_format((float) ($loan->jumlah_yang_diterima ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada pinjaman pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="payment-summary clearfix">
            <div class="payment-info">
                <div><strong>Noted:</strong> Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal 28 {{ $invoicePeriod->format('M-Y') }} ke rekening berikut:</div>
                <table class="bank-details">
                    <tr>
                        <th>BANK</th>
                        <td class="separator">:</td>
                        <td>BNI</td>
                    </tr>
                    <tr>
                        <th>Atas Nama</th>
                        <td class="separator">:</td>
                        <td>KOPERASI SIMPAN PINJAM DUMI DIGITAL TEKNOLOGI</td>
                    </tr>
                    <tr>
                        <th>No Rekening</th>
                        <td class="separator">:</td>
                        <td>2011113338</td>
                    </tr>
                </table>
            </div>

            <table class="summary">
                <tr>
                    <th>Jumlah Pinjaman</th>
                    <td class="number">{{ number_format($jumlahPinjaman, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Tagihan</th>
                    <td class="number"><strong>Rp. {{ number_format($totalTagihan, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <th>Status Invoice</th>
                    <td class="number">
                        <span class="status {{ strtolower((string) $invoice->status) === 'lunas' ? 'status-paid' : 'status-pending' }}">
                            {{ strtolower((string) $invoice->status) === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="note">Dokumen ini dibuat secara otomatis oleh sistem TomiDana.</div>
    </div>
</body>
</html>
