<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Fee TomiDana - {{ $invoice->periode }}</title>
    <style>
        @page { size: A4 landscape; margin: 28px; }
        body { font-family: Arial, sans-serif; color: #333; font-size: 11px; }
        .clearfix::after { content: ''; clear: both; display: table; }
        .left { float: left; }
        .right { float: right; text-align: right; }
        .logo { height: 45px; }
        h1 { color: #007bff; font-size: 20px; margin: 0 0 8px; }
        .header { border-bottom: 2px solid #444; margin-bottom: 18px; padding-bottom: 10px; }
        .info { margin-bottom: 18px; line-height: 1.6; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #f1f3f5; text-transform: uppercase; }
        th, td { border: 1px solid #cfd4da; padding: 7px; }
        .number { text-align: right; }
        .summary { margin-top: 18px; margin-left: auto; width: 320px; }
        .summary td { border: 0; padding: 5px; }
        .total { font-size: 14px; font-weight: bold; color: #198754; }
        .note { margin-top: 22px; color: #666; font-size: 10px; }
        .toolbar { margin: 0 auto 18px; max-width: 1120px; text-align: right; }
        .toolbar a { display: inline-block; padding: 9px 14px; color: #fff; text-decoration: none; border-radius: 4px; margin-left: 6px; }
        .download { background: #dc3545; }
        .back { background: #6c757d; }
        .invoice { max-width: 1120px; margin: 0 auto; }
        @media print { .toolbar { display: none; } .invoice { max-width: none; } }
    </style>
</head>
<body>
    @if (! $isPdf)
        <div class="toolbar">
            <a href="{{ route('admin.tomi-dana.lender.pinjaman', ['lender_id' => request('lender_id')]) }}" class="back">Kembali</a>
            <a href="{{ $downloadUrl }}" class="download">Download PDF</a>
        </div>
    @endif

    <div class="invoice">
    <div class="header clearfix">
        <div class="left">
            <img src="{{ $isPdf ? public_path('assets/images/logo.jpg') : asset('assets/images/logo.jpg') }}" class="logo" alt="Logo TOMI">
        </div>
        <div class="right">
            <h1>INVOICE FEE TOMIDANA</h1>
            <div><strong>No. Invoice:</strong> {{ $invoice->nomor }}</div>
            <div><strong>Periode:</strong> {{ $invoice->periode }}</div>
        </div>
    </div>

    <div class="info">
        <div><strong>Lender:</strong> {{ strtoupper($invoice->lender) }}</div>
        <div>Rincian biaya layanan pinjaman TomiDana pada periode {{ $invoice->periode }}.</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="35">No.</th>
                <th>Tanggal Pengajuan</th>
                <th>Nama Peminjam</th>
                <th>Perusahaan</th>
                <th class="number">Jumlah Pinjaman</th>
                @foreach ($selectedFields as $field)
                    <th class="number">{{ $fieldLabels[$field] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['no'] }}</td>
                    <td>{{ $item['tanggal_pengajuan'] }}</td>
                    <td>{{ strtoupper($item['nama']) }}</td>
                    <td>{{ strtoupper($item['perusahaan']) }}</td>
                    <td class="number">Rp. {{ number_format($item['jumlah_pinjaman'], 0, ',', '.') }}</td>
                    @foreach ($selectedFields as $field)
                        <td class="number">Rp. {{ number_format($item['fee_components'][$field] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ 5 + count($selectedFields) }}" style="text-align:center;">Tidak ada data pinjaman pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if (count($selectedFields) > 0)
            <tfoot>
                <tr class="total">
                    <td colspan="5">Total Komponen Fee</td>
                    @foreach ($selectedFields as $field)
                        <td class="number">Rp. {{ number_format($fieldTotals[$field] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>

    <table class="summary">
        <tr class="total">
            <td>Total Pinjaman</td>
            <td class="number">Rp. {{ number_format($summary['total_pinjaman'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="note">Dokumen ini dibuat secara otomatis oleh sistem TomiDana.</div>
    </div>
</body>
</html>
