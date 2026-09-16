<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Rekap Referal TomiInvoice</title>
    <style>
        @page {
            margin: 28px 34px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2933;
        }

        .header {
            width: 100%;
            margin-bottom: 18px;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .brand-subtitle {
            font-size: 10px;
            color: #5f6b7a;
            margin-top: 4px;
        }

        .invoice-title {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            color: #111827;
        }

        .invoice-number {
            text-align: right;
            color: #5f6b7a;
            margin-top: 4px;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 16px;
        }

        .info-box {
            border: 1px solid #d7dde5;
            padding: 10px 12px;
            min-height: 86px;
        }

        .box-title {
            font-size: 10px;
            color: #5f6b7a;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }

        .box-line {
            margin-bottom: 4px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table th {
            background: #eef2f7;
            border: 1px solid #cfd7e3;
            padding: 8px 7px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .main-table td {
            border: 1px solid #d7dde5;
            padding: 8px 7px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-table {
            width: 38%;
            margin-left: auto;
            margin-top: 16px;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #cfd7e3;
            padding: 9px 10px;
        }

        .summary-table th {
            background: #eef2f7;
            text-align: left;
        }

        .note {
            margin-top: 18px;
            font-size: 10px;
            color: #5f6b7a;
        }

        .signature {
            width: 230px;
            margin-left: auto;
            margin-top: 34px;
            text-align: center;
        }

        .signature-space {
            height: 54px;
        }
    </style>
</head>

<body>
    @php
        $documentNumber = 'REF-TOMI-' . ($perusahaan['id'] ?? '0') . '-' . str_replace('-', '', $period) . '-T' . $term;
        $today = now()->format('d F Y');
    @endphp

    <table class="header">
        <tr>
            <td>
                <div class="brand">TOMIINVOICE</div>
                <div class="brand-subtitle">PT. TOKO MIKRO INDONESIA</div>
            </td>
            <td>
                <div class="invoice-title">INVOICE TAGIHAN REFERAL</div>
                <div class="invoice-number">No. {{ $documentNumber }}</div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td width="50%" style="padding-right: 8px;">
                <div class="info-box">
                    <div class="box-title">Ditagihkan kepada</div>
                    <div class="box-line"><strong>{{ $perusahaan['nama'] ?? '-' }}</strong></div>
                    <div class="box-line">{{ $perusahaan['address'] ?? '-' }}</div>
                    <div class="box-line">{{ $perusahaan['email'] ?? '-' }}</div>
                    <div class="box-line">{{ $perusahaan['phone'] ?? '-' }}</div>
                </div>
            </td>
            <td width="50%" style="padding-left: 8px;">
                <div class="info-box">
                    <div class="box-title">Detail tagihan</div>
                    <div class="box-line"><strong>Tanggal Invoice:</strong> {{ $today }}</div>
                    <div class="box-line"><strong>Periode:</strong> {{ $periodLabel }}</div>
                    <div class="box-line"><strong>Termin:</strong> {{ $term }}</div>
                    <div class="box-line"><strong>Jenis:</strong> Rekap Referal TomiInvoice</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="35">No</th>
                <th>Pelanggan Mitra</th>
                <th>Nomor Invoice</th>
                <th>No. PO</th>
                <th class="text-right">Nominal Referal</th>
                <th>Status Bukti</th>
                <th>Tanggal Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item['vendor_name'] }}</td>
                <td>{{ $item['nomor_invoice'] }}</td>
                <td>{{ $item['purchase_order_number'] }}</td>
                <td class="text-right">Rp {{ number_format($item['total_referal'], 0, ',', '.') }}</td>
                <td>{{ !empty($item['referral_transfer_proof']) ? 'SUDAH UPLOAD' : 'BELUM UPLOAD' }}</td>
                <td>
                    {{ !empty($item['referral_transfer_date']) ? \Carbon\Carbon::parse($item['referral_transfer_date'])->format('d-m-Y') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data rekap.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <th>Total Tagihan Referal</th>
            <td class="text-right"><strong>Rp {{ number_format($totalReferal, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="note">
        Mohon melakukan pengecekan data tagihan berdasarkan periode dan termin yang tertera pada invoice ini.
    </div>

    <div class="signature">
        <div>Hormat kami,</div>
        <div class="signature-space"></div>
        <div><strong>PT. TOKO MIKRO INDONESIA</strong></div>
    </div>
</body>

</html>
