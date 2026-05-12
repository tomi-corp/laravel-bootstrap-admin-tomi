@php
function cleanRupiahValue($value) {
if (is_null($value) || $value === '') {
return 0;
}

if (is_numeric($value)) {
return (float) $value;
}

$value = str_replace(['Rp', 'rp', ' ', '.'], '', $value);
$value = str_replace(',', '.', $value);

return is_numeric($value) ? (float) $value : 0;
}

$items = $items ?? [];

$nomorInvoice = $invoice['nomor_invoice'] ?? (
'SF/' . date('Ym', strtotime($invoice['created_at'] ?? now())) . '/' . ($invoice['id'] ?? '-')
);

/*
|--------------------------------------------------------------------------
| Rumus
|--------------------------------------------------------------------------
| Jumlah Tagihan = Jumlah Pendapatan + 2,5%
| Jumlah Fee = Jumlah Pendapatan * 1%
|--------------------------------------------------------------------------
*/

$items = collect($items)->map(function ($item) {
$jumlahPendapatan = cleanRupiahValue(
$item['jumlah_pendapatan']
?? $item['jumlah_pinjaman']
?? $item['total_pinjaman']
?? 0
);

$jumlahTagihan = $jumlahPendapatan + ($jumlahPendapatan * 0.025);
$jumlahFee = $jumlahPendapatan * 0.01;

$item['penempatan_kerja'] = $item['penempatan_kerja'] ?? '-';
$item['jumlah_pendapatan'] = $jumlahPendapatan;
$item['jumlah_tagihan'] = $jumlahTagihan;
$item['jumlah_fee'] = $jumlahFee;

return $item;
})->values();

/*
|--------------------------------------------------------------------------
| Grouping berdasarkan Penempatan Kerja
|--------------------------------------------------------------------------
*/

$itemsByPenempatanKerja = $items
->groupBy(function ($item) {
return !empty($item['penempatan_kerja'])
? $item['penempatan_kerja']
: '-';
})
->map(function ($group, $penempatanKerja) {
return [
'penempatan_kerja' => $penempatanKerja,
'jumlah_pendapatan' => $group->sum('jumlah_pendapatan'),
'jumlah_tagihan' => $group->sum('jumlah_tagihan'),
'jumlah_fee' => $group->sum('jumlah_fee'),
];
})
->values();

/*
|--------------------------------------------------------------------------
| Total Keseluruhan
|--------------------------------------------------------------------------
*/

$totalJumlahPendapatan = $itemsByPenempatanKerja->sum('jumlah_pendapatan');
$totalJumlahTagihan = $itemsByPenempatanKerja->sum('jumlah_tagihan');
$totalJumlahFee = $itemsByPenempatanKerja->sum('jumlah_fee');

/*
|--------------------------------------------------------------------------
| Fallback kalau items kosong
|--------------------------------------------------------------------------
*/

if ($totalJumlahPendapatan <= 0) { $totalJumlahPendapatan=cleanRupiahValue( $invoice['total_pinjaman'] ??
    $invoice['jumlah_pendapatan'] ?? 0 ); $totalJumlahTagihan=$totalJumlahPendapatan + ($totalJumlahPendapatan * 0.025);
    $totalJumlahFee=$totalJumlahPendapatan * 0.01; $itemsByPenempatanKerja=collect([ [ 'penempatan_kerja'=>
    $invoice['penempatan_kerja'] ?? '-',
    'jumlah_pendapatan' => $totalJumlahPendapatan,
    'jumlah_tagihan' => $totalJumlahTagihan,
    'jumlah_fee' => $totalJumlahFee,
    ]
    ]);
    }
    @endphp

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Invoice Salary Financing - {{ $invoice['nama_perusahaan'] ?? '-' }}</title>

        <style>
            @page {
                size: a4 landscape;
                margin: 30px;
            }

            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                font-size: 11px;
                color: #333;
            }

            .clearfix::after {
                content: "";
                clear: both;
                display: table;
            }

            .float-left {
                float: left;
            }

            .float-right {
                float: right;
            }

            .header {
                margin-bottom: 20px;
                border-bottom: 2px solid #444;
                padding-bottom: 10px;
            }

            .logo {
                height: 50px;
            }

            .title {
                font-size: 20px;
                font-weight: bold;
                margin: 0;
                color: #007bff;
            }

            .info-section {
                margin-bottom: 20px;
            }

            table.main-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table.main-table th {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 7px;
                text-align: left;
                font-size: 10px;
                text-transform: uppercase;
            }

            table.main-table td {
                border: 1px solid #dee2e6;
                padding: 7px;
                vertical-align: top;
                font-size: 10px;
            }

            table.main-table tfoot td {
                background-color: #f1f3f5;
                font-weight: bold;
            }

            .total-section {
                margin-top: 20px;
                padding: 15px;
                background-color: #f8f9fa;
                border-radius: 5px;
            }

            .total-title {
                font-size: 14px;
                font-weight: bold;
            }

            .total-amount {
                font-size: 18px;
                color: #28a745;
                font-weight: bold;
            }

            .note-section {
                margin-top: 30px;
                font-size: 10px;
                color: #666;
            }

            .bank-info {
                margin-top: 5px;
                padding: 10px;
                border: 1px dashed #ccc;
                display: inline-block;
            }

            .summary-table {
                width: 350px;
                border-collapse: collapse;
            }

            .summary-table td {
                padding: 6px 0;
            }

            .nowrap {
                white-space: nowrap;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }
        </style>
    </head>

    <body>

        <div class="header clearfix">
            <div class="float-left">
                <img src="{{ public_path('assets/images/logo.jpg') }}" class="logo" alt="Logo">
            </div>

            <div class="float-right">
                <h1 class="title">INVOICE SALARY FINANCING</h1>

                <div style="text-align: right; margin-top: 5px;">
                    <div>
                        <b>No. Invoice:</b> {{ $nomorInvoice }}
                    </div>

                    <div style="margin-top: 3px;">
                        <b>Tanggal Invoice:</b>
                        {{ !empty($invoice['created_at'])
                        ? \Carbon\Carbon::parse($invoice['created_at'])->translatedFormat('d F Y')
                        : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section clearfix">
            <div class="float-left">
                <p>
                    <b>Halo, Koperasi Simpan Pinjam Dumi Digital Teknologi</b>
                </p>

                <p>
                    Berikut rincian tagihan fee Salary Financing pada perusahaan
                    <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>
                    yang telah ditransfer pada tanggal
                    <b>
                        {{ !empty($invoice['tanggal_bukti_transfer'])
                        ? \Carbon\Carbon::parse($invoice['tanggal_bukti_transfer'])->translatedFormat('d F Y')
                        : '-' }}
                    </b>
                    oleh Koperasi Simpan Pinjam Dumi Digital Teknologi, sebagai berikut:
                </p>
            </div>
        </div>

        <p>
            <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>
        </p>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="text-align:center;" width="30">No.</th>
                    <th style="text-align:center;">Tanggal Pencairan</th>
                    <th style="text-align:center;">Nama Perusahaan</th>
                    <th style="text-align:center;">Penempatan Kerja</th>
                    <th style="text-align:center; width:120px;">Jumlah Pendapatan</th>
                    <th style="text-align:center; width:120px;">Jumlah Tagihan</th>
                    <th style="text-align:center; width:120px;">Jumlah Fee</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($itemsByPenempatanKerja as $item)
                <tr>
                    <td align="center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="nowrap">
                        <b>
                            {{ !empty($invoice['tanggal_bukti_transfer'])
                            ? \Carbon\Carbon::parse($invoice['tanggal_bukti_transfer'])->translatedFormat('d F Y')
                            : '-' }}
                        </b>
                    </td>

                    <td>
                        <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>
                    </td>

                    <td align="left">
                        {{ $item['penempatan_kerja'] ?? '-' }}
                    </td>

                    <td align="right" class="nowrap">
                        Rp {{ number_format($item['jumlah_pendapatan'] ?? 0, 0, ',', '.') }}
                    </td>

                    <td align="right" class="nowrap">
                        Rp {{ number_format($item['jumlah_tagihan'] ?? 0, 0, ',', '.') }}
                    </td>

                    <td align="right" class="nowrap">
                        Rp {{ number_format($item['jumlah_fee'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" align="center">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" align="center">
                        <b>TOTAL</b>
                    </td>

                    <td align="right" class="nowrap">
                        <b>Rp {{ number_format($totalJumlahPendapatan ?? 0, 0, ',', '.') }}</b>
                    </td>

                    <td align="right" class="nowrap">
                        <b>Rp {{ number_format($totalJumlahTagihan ?? 0, 0, ',', '.') }}</b>
                    </td>

                    <td align="right" class="nowrap">
                        <b>Rp {{ number_format($totalJumlahFee ?? 0, 0, ',', '.') }}</b>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="clearfix">
            <div class="float-right total-section" style="width: 350px;">
                <table class="summary-table">
                    <tr>
                        <td class="total-title">TOTAL FEE</td>
                        <td align="right" class="total-amount">
                            Rp {{ number_format($totalJumlahFee ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="float-left note-section" style="width: 400px;">
                <b>Catatan Pembayaran:</b>

                @php
                $tanggalJatuhTempo = !empty($invoice['created_at'])
                ? \Carbon\Carbon::parse($invoice['created_at'])->addDays(3)->translatedFormat('d F Y')
                : '-';
                @endphp

                <p>
                    Harap segera melakukan pembayaran paling lambat tanggal
                    <b>{{ $tanggalJatuhTempo }}</b>
                    ke rekening berikut:
                </p>

                <div class="bank-info">
                    <b>BANK BRI</b><br>
                    Atas Nama : <b>PT. TOKO MIKRO INDONESIA</b><br>
                    No. Rekening : <b>042501000000305</b>
                </div>

                <p>
                    <i>*Mohon lampirkan bukti transfer saat konfirmasi.</i>
                </p>
            </div>
        </div>

    </body>

    </html>