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
$nomorInvoice = $invoice['nomor_invoice'] ?? ('SF/' . date('Ym', strtotime($invoice['created_at'] ?? now())) . '/' . ($invoice['id'] ?? '-'));

$totalJumlahPendapatan = collect($items)->sum(function ($item) {
    return cleanRupiahValue($item['jumlah_pendapatan'] ?? $item['jumlah_pinjaman'] ?? 0);
});

$totalBiayaAdmin = collect($items)->sum(function ($item) {
    return cleanRupiahValue($item['biaya_admin'] ?? $item['total_fee'] ?? 0);
});

$totalJumlahTagihan = collect($items)->sum(function ($item) {
    return cleanRupiahValue($item['total_tagihan'] ?? $item['jumlah_tagihan'] ?? 0);
});

$totalDibayarkan = collect($items)->sum(function ($item) {
    return cleanRupiahValue($item['total_dibayarkan'] ?? $item['total_tagihan'] ?? 0);
});

if ($totalJumlahPendapatan <= 0) {
    $totalJumlahPendapatan = cleanRupiahValue($invoice['total_pinjaman'] ?? 0);
}

if ($totalBiayaAdmin <= 0) {
    $totalBiayaAdmin = cleanRupiahValue($invoice['biaya_admin'] ?? 0);
}

if ($totalJumlahTagihan <= 0) {
    $totalJumlahTagihan = cleanRupiahValue($invoice['total_tagihan'] ?? 0);
}

if ($totalDibayarkan <= 0) {
    $totalDibayarkan = $totalJumlahTagihan;
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
            <p><b>Halo, {{ $invoice['nama_perusahaan'] ?? '-' }}</b></p>
            <p>
                Berikut rincian tagihan Salary Financing berdasarkan data karyawan yang telah diunggah melalui file
                <b>{{ $invoice['filename'] ?? '-' }}</b>
                pada tanggal
                <b>
                    {{ !empty($invoice['created_at'])
                        ? \Carbon\Carbon::parse($invoice['created_at'])->translatedFormat('d F Y')
                        : '-' }}
                </b>,
                sebagai berikut:
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
                <th style="text-align:center;">Tanggal Pengajuan</th>
                <th style="text-align:center;">Nama <br> NIK / NIP</th>
                <th style="text-align:center;">Penempatan Kerja</th>
                <th style="text-align:center;">Rekening</th>
                <th style="text-align:center; width:120px;">Jumlah Pendapatan</th>
                <th style="text-align:center; width:110px;">Biaya Admin</th>
                <th style="text-align:center; width:120px;">Total Tagihan</th>
                <th style="text-align:center; width:120px;">Total Dibayarkan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td align="center">{{ $item['no'] ?? $loop->iteration }}</td>

                <td class="nowrap">
                    <b>{{ $item['tanggal_pengajuan'] ?? '-' }}</b>
                </td>

                <td>
                    <b>{{ strtoupper($item['nama'] ?? '-') }}</b><br>
                    <small>
                        NIK: {{ $item['nik'] ?? '-' }}<br>
                        NIP: {{ $item['nip'] ?? '-' }}
                    </small>
                </td>

                <td align="left">
                    {{ $item['penempatan_kerja'] ?? '-' }}
                </td>

                <td>
                    <b>{{ $item['nama_bank'] ?? '-' }}</b><br>
                    <small>
                        {{ $item['no_rekening'] ?? '-' }}<br>
                        {{ $item['nama_pemilik_rekening'] ?? '-' }}
                    </small>
                </td>

                <td align="right" class="nowrap">
                    Rp {{ number_format($item['jumlah_pendapatan'] ?? 0, 0, ',', '.') }}
                </td>

                <td align="right" class="nowrap">
                    Rp {{ number_format($item['biaya_admin'] ?? 0, 0, ',', '.') }}
                </td>

                <td align="right" class="nowrap">
                    Rp {{ number_format($item['total_tagihan'] ?? 0, 0, ',', '.') }}
                </td>

                <td align="right" class="nowrap">
                    Rp {{ number_format($item['total_dibayarkan'] ?? $item['total_tagihan'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" align="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="center">
                    <b>TOTAL</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalJumlahPendapatan ?? 0, 0, ',', '.') }}</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalBiayaAdmin ?? 0, 0, ',', '.') }}</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalJumlahTagihan ?? 0, 0, ',', '.') }}</b>
                </td>

                <td align="right">
                    <b>Rp {{ number_format($totalDibayarkan ?? 0, 0, ',', '.') }}</b>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="clearfix">
        <div class="float-right total-section" style="width: 350px;">
            <table class="summary-table">
                <tr>
                    <td class="total-title">TOTAL TAGIHAN</td>
                    <td align="right" class="total-amount">
                        Rp {{ number_format($totalJumlahTagihan ?? 0, 0, ',', '.') }}
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
            <p><i>*Mohon lampirkan bukti transfer saat konfirmasi.</i></p>
        </div>
    </div>

</body>

</html>
