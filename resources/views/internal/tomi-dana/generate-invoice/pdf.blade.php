<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice KSPDDT - {{ $invoice->nama_perusahaan }} {{ 'INV/' . date('Ym') . '/' . $invoice->id }}</title>
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

        .info-table td {
            padding: 2px 0;
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
                    <b>No. Invoice:</b> {{ 'INV/' . date('Ym') . '/' . $invoice->id }}
                </div>

                <div style="margin-top: 3px;">
                    <b>Tanggal:</b> {{ $invoice->created_at ? $invoice->created_at->format('d M Y') : '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="info-section clearfix">
        <div class="float-left">
            <p><b>Halo, {{ $invoice->nama_perusahaan }}</b></p>
            <p>
                Berikut rincian tagihan layanan pelanggan Fasilitas Pinjaman e-Kasbon pada
                perusahaan <b>{{ $invoice->nama_perusahaan }}</b>
                untuk periode
                <b>
                    6 {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('F Y') }}
                    -
                    23 {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('F Y') }},
                </b>
                sebagai berikut :
            </p>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Tanggal Pengajuan</th>
                <th>Nama <br> NIK</th>
                <th>Penempatan Kerja</th>
                <th align="right" style="width: 125px;">Jumlah Pinjaman</th>
                <th align="right" style="width: 125px;">Biaya Admin</th>
                <th align="right" style="width: 125px;">Jumlah Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalJumlahPinjaman = 0;
            $totalBiayaAdmin = 0;
            $totalJumlahTagihan = 0;
            @endphp

            @foreach ($items as $item)
            @php

            $jumlahPinjaman = $item['jumlah_pinjaman'] ?? 0;
            $biayaAdmin = round($jumlahPinjaman * 0.07);
            $jumlahTagihan = $jumlahPinjaman + $biayaAdmin;

            $totalJumlahPinjaman += $jumlahPinjaman;
            $totalBiayaAdmin += $biayaAdmin;
            $totalJumlahTagihan += $jumlahTagihan;
            @endphp

            <tr>
                <td align="center">{{ $item['no'] }}</td>
                <td>
                    <b>
                        {{ !empty($item['tanggal_pengajuan'])
                        ? \Carbon\Carbon::createFromFormat('d/m/Y', $item['tanggal_pengajuan'])->translatedFormat('d F
                        Y')
                        : '-' }}
                    </b>
                </td>
                <td>
                    <b>{{ $item['nama'] }}</b><br>
                    <small>{{ $item['no_ktp'] }}</small>
                </td>
                <td>{{ $item['penempatan_kerja'] ?? '-' }}</td>
                <td align="right">Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($jumlahTagihan, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr>
                <td colspan="4" align="center"><b>Total</b></td>
                <td align="right"><b>Rp {{ number_format($totalJumlahPinjaman, 0, ',', '.') }}</b>
                </td>
                <td align="right"><b>Rp {{ number_format($totalBiayaAdmin, 0, ',', '.') }}</b></td>
                <td align="right"><b>Rp {{ number_format($totalJumlahTagihan, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <div class="clearfix">
        <div class="float-right total-section" style="width: 350px;">
            <table width="100%">
                <tr>
                    <td class="total-title">TOTAL TAGIHAN</td>
                    <td align="right" class="total-amount">
                        Rp {{ number_format($totalJumlahTagihan, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="float-left note-section" style="width: 400px;">
            <b>Catatan Pembayaran:</b>
            <p>Harap segera melakukan pembayaran paling lambat tanggal 12 setiap bulannya ke rekening berikut:</p>
            <div class="bank-info">
                <b>BANK {{ $rekening->bank->code_bank ?? 'BNI' }}</b><br>
                Atas Nama : <b>{{ $rekening->atas_nama ?? 'KOPERASI SIMPAN PINJAM DUMI DIGITAL TEKNOLOGI' }}</b><br>
                No. Rekening : <b>{{ $rekening->nomor_rekening ?? '2011113338' }}</b>
            </div>
            <p><i>*Mohon lampirkan bukti transfer saat konfirmasi.</i></p>
        </div>
    </div>

</body>

</html>