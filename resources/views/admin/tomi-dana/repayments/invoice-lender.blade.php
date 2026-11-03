<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoiceNumber }}</title>
    <style>
        @page { size: A4 landscape; margin: 28px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #343a40; font-family: Arial, sans-serif; font-size: 11px; }
        .toolbar { margin: 0 auto 18px; max-width: 760px; text-align: right; }
        .toolbar a { display: inline-block; border-radius: 4px; color: #fff; margin-left: 6px; padding: 9px 14px; text-decoration: none; }
        .toolbar .download { background: #dc3545; }
        .toolbar .back { background: #6c757d; }
        .invoice { max-width: 760px; margin: 0 auto; }
        .header { border-bottom: 2px solid #343a40; margin-bottom: 20px; padding-bottom: 12px; }
        .clearfix::after { clear: both; content: ''; display: table; }
        .left { float: left; }
        .right { float: right; text-align: right; }
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
        .summary { margin-left: auto; margin-top: 18px; width: 300px; }
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
                <h1>INVOICE TOMIDANA</h1>
                <div class="muted">Rincian tagihan pembayaran angsuran</div>
            </div>
            <div class="right">
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

        <div class="meta">
            <div class="meta-item">
                <div class="meta-label">Kode Perusahaan</div>
                <div class="meta-value">{{ strtoupper($company->id_code_perusahaan ?? '-') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Nama Perusahaan</div>
                <div class="meta-value">{{ strtoupper($company->nama ?? '-') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Lender</div>
                <div class="meta-value">{{ strtoupper($company->lender?->nama ?? '-') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Periode / Jatuh Tempo</div>
                <div class="meta-value">{{ $invoicePeriod->format('d/m/Y') }}</div>
            </div>
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

        <div class="note">Dokumen ini dibuat secara otomatis oleh sistem TomiDana.</div>
    </div>
</body>
</html>
