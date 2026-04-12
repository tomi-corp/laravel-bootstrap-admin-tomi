<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice Fee Tomi - {{ $invoice->nama_perusahaan }}</title>
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
            {{-- Gunakan public_path jika gambar ada di public/assets --}}
            <img src="{{ public_path('assets/images/logo.jpg') }}" class="logo" alt="Logo">
        </div>
        <div class="float-right">
            <h1 class="title">INVOICE TAGIHAN</h1>
            <div style="text-align: right; margin-top: 5px;">
                <b>No. Invoice:</b> {{ 'INV/' . date('Ym') . '/' . $invoice->id }}
            </div>
        </div>
    </div>

    <div class="info-section clearfix">
        <div class="float-left">
            <p><b>Hallo, {{ $invoice->nama_perusahaan }}</b></p>
            <p>Berikut rincian tagihan aplikasi layanan pelanggan digital Pinjaman Payroll TOMI pada KSPDDT pada
                perusahaan<b> {{ $invoice->nama_perusahaan }}</b>
                untuk periode <b>{{ date('F Y', strtotime($invoice->created_at)) }} </b></p>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Tanggal Pinjaman</th>
                <!-- <th>Nama<br> NIP/ NIK</th> -->
                <th>Nama <br> NIK</th>
                <th align="right">Jumlah Pinjaman</th>
                <th align="right">Fee TOMI</th>
                <th align="right">Biaya Layanan</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalNominal = 0;
            $totalFeeTomi = 0;
            $totalLayanan = 0;
            $grandTotal = 0;
            @endphp

            @foreach ($items as $item)
            <tr>
                <td align="center">{{ $item['no'] }}</td>
                <td>
                    <b>{{ $item['tanggal_pengajuan'] }}</b>
                </td>
                <td>
                    <b>{{ $item['nama'] }}</b><br>
                    <!-- <small>{{ $item['no_ktp'] }}/
                            {{ $item['nip'] }}</small> -->
                    <small>{{ $item['no_ktp'] }}</small>
                </td>
                <td align="right">Rp {{ number_format($item['jumlah_pinjaman'], 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($item['fee_tomi'], 0, ',', '.') }}</td>
                <td align="right">Rp 0</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <div class="clearfix">
        <div class="float-right total-section" style="width: 350px;">
            <table width="100%">
                <tr>
                    <td class="total-title">FEE TOMI + BIAYA LAYANAN</td>
                    <td align="right" class="total-amount">Rp
                        {{ number_format($summary['total_fee'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="float-left note-section" style="width: 400px;">
            <b>Catatan Pembayaran:</b>
            <p>Harap segera melakukan pembayaran paling lambat tanggal 5 setiap bulannya ke rekening berikut:</p>
            <div class="bank-info">
                <b>BANK {{ $rekening->bank->code_bank ?? 'MANDIRI' }}</b><br>
                Atas Nama : {{ $rekening->atas_nama ?? 'PT. TOMI DIGITAL INDONESIA' }}<br>
                No. Rekening : <b>{{ $rekening->nomor_rekening ?? '1234567890' }}</b>
            </div>
            <p><i>*Mohon lampirkan bukti transfer saat konfirmasi.</i></p>
        </div>
    </div>

</body>

</html>