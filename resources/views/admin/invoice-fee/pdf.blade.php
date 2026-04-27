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
                <b>No. Invoice:</b> {{ 'INV/' . date('Ym') . '/' . ($invoice->id ?? '-') }}
            </div>
        </div>
    </div>

    <div class="info-section clearfix">
        <div class="float-left">
            <p><b>Hallo, Koperasi Simpan Pinjam Dumi Digital Teknologi</b></p>
            <p>
                Berikut rincian tagihan fee atas layanan pelanggan Fasilitas Pinjaman e-Kasbon pada perusahaan yang
                telah bekerjasama dengan <b>Koperasi Simpan Pinjam Dumi Digital Teknologi</b> untuk periode
                <b>
                    {{ !empty($invoice->created_at) ? '6 ' .
                    \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('F Y') : '-' }}
                    -
                    {{ !empty($invoice->created_at) ? '23 ' .
                    \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('F Y') : '-' }},
                </b>
                sebagai berikut :
            </p>
        </div>
    </div>

    <p>
        <b>{{ $invoice->nama_perusahaan ?? '-' }}</b>
    </p>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Tanggal Pengajuan</th>
                <th>Nama <br> NIK</th>
                <th align="right">Penempatan Kerja</th>
                <th align="right">Jumlah Pinjaman</th>
                <th align="right">Jumlah Tagihan</th>
                <th align="right">Fee Tomi</th>
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
                    <b>{{ $item['nama'] ?? '-' }}</b><br>
                    <small>{{ $item['no_ktp'] ?? '-' }}</small>
                </td>
                <td align="right">
                    {{ $item['penempatan_kerja'] }}
                </td>
                <td align="right">Rp {{ number_format($item['jumlah_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($item['jumlah_tagihan'] ?? 0, 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($item['fee_tomi'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" align="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix">
        <div class="float-right total-section" style="width: 350px;">
            <table class="summary-table">
                <tr>
                    <td class="total-title">TOTAL JUMLAH FEE</td>
                    <td align="right" class="total-amount">
                        Rp {{ number_format($totalJumlahFee ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="float-left note-section" style="width: 400px;">
            <b>Catatan Pembayaran:</b>
            <p>Harap segera melakukan pembayaran paling lambat tanggal 27 setiap bulannya ke rekening berikut:</p>
            <div class="bank-info">
                <b>BANK BRI</b><br>
                Atas Nama : <b>PT. TOKO MIKRO INDONESIA</b><br>
                No. Rekening : <b>2011113338</b>
            </div>
            <p><i>*Mohon lampirkan bukti transfer saat konfirmasi.</i></p>
        </div>
    </div>

</body>

</html>