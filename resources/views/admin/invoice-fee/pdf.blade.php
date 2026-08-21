@php
function cleanRupiahValue($value) {
if (is_null($value) || $value === '') {
return 0;
}

// Kalau nilainya sudah angka, langsung return
if (is_numeric($value)) {
return (float) $value;
}

// Bersihkan format seperti: Rp 1.000.000
$value = str_replace(['Rp', 'rp', ' ', '.'], '', $value);

// Kalau ada koma desimal, ubah ke titik
$value = str_replace(',', '.', $value);

return is_numeric($value) ? (float) $value : 0;
}

function parseInvoiceDateValue($value) {
if (empty($value) || $value === '-') {
return null;
}

try {
return \Carbon\Carbon::parse($value);
} catch (\Throwable $e) {
return null;
}
}

function invoiceFeePeriodText($items, $invoice, string $productName): string {
$dates = collect($items)
->map(fn($item) => parseInvoiceDateValue($item['tanggal_pengajuan'] ?? null))
->filter()
->values();

$fallbackDate = parseInvoiceDateValue($invoice->created_at ?? null) ?? now();

if ($dates->isEmpty()) {
$dates = collect([$fallbackDate]);
}

if ($productName === 'TomiDana') {
$sortedDates = $dates->sortBy(fn($date) => $date->timestamp)->values();
$startDate = $sortedDates->first()->copy()->startOfMonth();
$endDate = $sortedDates->last()->copy()->endOfMonth();

return $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
}

return $dates->first()->translatedFormat('F');
}

$totalJumlahPinjaman = collect($items)->sum(function ($item) {
return cleanRupiahValue($item['jumlah_pinjaman'] ?? 0);
});

$totalJumlahTagihan = collect($items)->sum(function ($item) {
return cleanRupiahValue($item['jumlah_tagihan'] ?? 0);
});

$totalFeeTomi = collect($items)->sum(function ($item) {
return cleanRupiahValue($item['fee_tomi'] ?? $item['fee'] ?? $item['biaya_tomi'] ?? 0);
});

$productName = $productName ?? 'TomiDana';
$periodeInvoice = invoiceFeePeriodText($items, $invoice, $productName);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice Fee Tomi - {{ $invoice->nama_perusahaan ?? '-' }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 30px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
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
            padding: 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        table.main-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
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
    </style>
</head>

<body>

    <div class="header clearfix">
        <div class="float-left">
            <img src="{{ public_path('assets/images/logo.jpg') }}" class="logo" alt="Logo">
        </div>

        <div class="float-right">
            <h1 class="title">INVOICE TAGIHAN</h1>

            <div style="text-align: right; margin-top: 5px;">
                <div>
                    <b>No. Invoice:</b> {{ 'INV/' . date('Ym', strtotime($invoice->created_at ?? now())) . '/' .
                    ($invoice->id ?? '-') }}
                </div>
                <div style="margin-top: 3px;">
                    <b>Tanggal Invoice:</b>
                    {{ !empty($invoice->created_at)
                    ? \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y')
                    : '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="info-section clearfix">
        <div class="float-left">
            <p><b>Halo, Koperasi Simpan Pinjam Dumi Digital Teknologi</b></p>
            <p>
                Berikut adalah rincian tagihan biaya atas penggunaan fasilitas {{ $productName }} pada aplikasi platform
                digital Tomi yang diberikan kepada <b>{{ $invoice->nama_perusahaan ?? '-' }}</b>, periode
                <b>{{ $periodeInvoice }}</b>;
            </p>
        </div>
    </div>

    <p>
        <b>{{ $invoice->nama_perusahaan ?? '-' }}</b>
    </p>

    <table class="main-table">
        <thead>
            <tr>
                <th style="text-align:center;" width="30">No.</th>
                <th style="text-align:center;">Tanggal Pengajuan</th>
                <th style="text-align:center;">Nama <br> NIK</th>
                <th style="text-align:center;">Penempatan Kerja</th>
                <th style="text-align:center; width:125px;">Jumlah Pinjaman</th>
                <th style="text-align:center; width:125px;">Jumlah Tagihan</th>
                <th style="text-align:center; width:125px;">Jumlah Fee</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td align="center">{{ $item['no'] ?? '-' }}</td>

                <td>
                    <b>{{ $item['tanggal_pengajuan'] ?? '-' }}</b>
                </td>

                <td>
                    <b>{{ strtoupper($item['nama']) ?? '-' }}</b><br>
                    <small>
                        {{ $item['no_ktp'] ?? '-' }}
                    </small>
                </td>

                <td align="left">
                    {{ $item['penempatan_kerja'] ?? '-' }}
                </td>

                <td align="right">
                    Rp {{ number_format($item['jumlah_pinjaman'] ?? 0, 0, ',', '.') }}
                </td>

                <td align="right">
                    Rp {{ number_format($item['jumlah_tagihan'] ?? 0, 0, ',', '.') }}
                </td>

                <td align="right">
                    Rp {{ number_format($item['fee_tomi'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" align="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="center">
                    <b>TOTAL</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalJumlahPinjaman ?? 0, 0, ',', '.') }}</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalJumlahTagihan ?? 0, 0, ',', '.') }}</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalFeeTomi ?? 0, 0, ',', '.') }}</b>
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
            $tanggalJatuhTempo = !empty($invoice->created_at)
            ? \Carbon\Carbon::parse($invoice->created_at)->addDays(3)->translatedFormat('d F Y')
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
            <p><i>*Mohon lampirkan bukti transfer saat konfirmasi.</i></p>
        </div>
    </div>

</body>

</html>
