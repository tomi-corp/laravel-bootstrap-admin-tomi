@php
$items = collect($items ?? []);

$totalPlafond = $items->sum(function ($item) {
return (float) ($item['plafond'] ?? 0);
});

$totalFeeWebsite = $items->sum(function ($item) {
return ((float) ($item['plafond'] ?? 0)) * 0.0025;
});

$tanggalInvoice = !empty($tahun) && !empty($bulan)
? \Carbon\Carbon::createFromDate(
$tahun,
$bulan,
1
)->translatedFormat('d F Y')
: '-';

$tanggalJatuhTempo = !empty($invoice['created_at'])
? \Carbon\Carbon::parse($invoice['created_at'])->addDays(3)->translatedFormat('d F Y')
: '-';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice Website</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 30px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
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

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.main-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 7px;
            font-size: 10px;
            text-transform: uppercase;
        }

        table.main-table td {
            border: 1px solid #dee2e6;
            padding: 7px;
            font-size: 10px;
            vertical-align: top;
        }

        table.main-table tfoot td {
            background-color: #f1f3f5;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            width: 350px;
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

        .summary-table {
            width: 100%;
        }

        .note-section {
            margin-top: 30px;
            width: 400px;
            font-size: 10px;
            color: #666;
        }

        .bank-info {
            margin-top: 5px;
            padding: 10px;
            border: 1px dashed #ccc;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="header clearfix">
        <div class="float-left">
            <img src="{{ public_path('assets/images/logo.jpg') }}" class="logo" alt="Logo">
        </div>

        <div class="float-right" style="text-align: right;">
            <h1 class="title">LAYANAN WEBSITE</h1>

            <div style="margin-top: 5px;">
                <b>No. Invoice:</b> {{ $invoice }}
            </div>

            <div style="margin-top: 3px;">
                <b>Tanggal Invoice:</b> {{ $tanggalInvoice }}
            </div>
        </div>
    </div>

    <p>
        <b>Halo, Bank Tata Asia</b>
    </p>

    <p>
        Berikut rincian tagihan layanan pengembangan dan implementasi website
        <b>TOMI pada Bank Tata Asia</b>,
        sebagai berikut:
    </p>

    <table class="main-table">
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th>Tanggal Pengajuan Debitur</th>
                <th>Nama Debitur<br>Nama Perusahaan<br>NIP / KARIP</th>
                <th>Tanggal Pencairan Debitur</th>
                <th>Nominal Pinjaman</th>
                <th>Biaya Layanan Website</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($items as $item)
            <tr>
                <td class="text-center">
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ !empty($item['tanggal_pengajuan'])
                    ? \Carbon\Carbon::parse($item['tanggal_pengajuan'])->translatedFormat('d F Y')
                    : '-' }}
                </td>

                <td>
                    {{ $item['nama_lengkap'] ?? '-' }}<br>
                    {{ $item['nama_perusahaan'] ?? '-' }}<br>
                    {{ $item['nip'] ?? '-' }}
                </td>

                <td>
                    {{ !empty($item['mulai_pinjaman'])
                    ? \Carbon\Carbon::parse($item['mulai_pinjaman'])->translatedFormat('d F Y')
                    : '-' }}
                </td>

                <td class="nowrap text-right">
                    Rp {{ number_format($item['plafond'] ?? 0, 0, ',', '.') }}
                </td>

                <td class="nowrap text-right">
                    Rp {{ number_format(($item['plafond'] ?? 0) * 0.0025, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="text-center">
                    TOTAL
                </td>

                <td class="text-right">
                    Rp {{ number_format($totalPlafond, 0, ',', '.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($totalFeeWebsite, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="clearfix">
        <div class="float-right total-section">
            <table class="summary-table">
                <tr>
                    <td class="total-title">TOTAL FEE</td>
                    <td class="total-amount text-right">
                        Rp {{ number_format($totalFeeWebsite, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="float-left note-section">
            <b>Catatan Pembayaran:</b>

            <p>
                Harap segera melakukan pembayaran paling lambat tanggal
                <b>{{ $tanggalJatuhTempo }}</b>
                ke rekening berikut:
            </p>

            <div class="bank-info">
                <b>BANK BRI</b><br>
                Atas Nama: <b>PT. TOKO MIKRO INDONESIA</b><br>
                No. Rekening: <b>042501000000305</b>
            </div>

            <p>
                <i>*Mohon lampirkan bukti transfer saat konfirmasi.</i>
            </p>
        </div>
    </div>

</body>

</html>