@php
$logoPath = public_path('assets/images/logo.jpg');
$logoSrc = file_exists($logoPath)
    ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
    : '';

$invoiceDate = $invoice['finance_invoice_term_date'] ?? $invoice['created_at'] ?? now();
$periodDate = !empty($invoiceDate) ? \Carbon\Carbon::parse($invoiceDate) : now();
$periodText = $periodDate->translatedFormat('F');

function termInvoiceRupiah($value): string
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $invoice['nomor_invoice'] ?? 'Invoice Termin TomiInvoice' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
        }

        .clearfix::after {
            clear: both;
            content: "";
            display: table;
        }

        .header {
            border-bottom: 2px solid #222;
            margin-bottom: 20px;
            padding-bottom: 12px;
        }

        .logo {
            height: 46px;
        }

        .title {
            float: right;
            font-size: 19px;
            font-weight: bold;
            line-height: 1.4;
            text-align: right;
        }

        .meta {
            margin-top: 4px;
            font-size: 10px;
            font-weight: normal;
        }

        .intro {
            line-height: 1.55;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            vertical-align: middle;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .label-table td {
            border: 0;
            padding: 4px 0;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .total-row td {
            background: #f7f7f7;
            font-size: 12px;
            font-weight: bold;
        }

        .note {
            margin-top: 20px;
            font-size: 10px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        @if ($logoSrc)
        <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif

        <div class="title">
            INVOICE FEE TOMIINVOICE
            <div class="meta">
                No. Invoice: {{ $invoice['nomor_invoice'] ?? '-' }}<br>
                Tanggal Invoice:
                {{ !empty($invoiceDate) ? \Carbon\Carbon::parse($invoiceDate)->translatedFormat('d F Y') : '-' }}
            </div>
        </div>
    </div>

    <div class="intro">
        <p><b>Halo, Koperasi Simpan Pinjam Dumi Digital Teknologi</b></p>
        <p>
            Berikut adalah rincian tagihan biaya atas penggunaan fasilitas TomiInvoice pada aplikasi platform digital
            Tomi yang diberikan kepada <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>, periode
            <b>{{ $periodText }}</b>;
        </p>
    </div>

    <table class="label-table">
        <tr>
            <td width="145">Client</td>
            <td width="12">:</td>
            <td><b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b></td>
        </tr>
        <tr>
            <td>Vendor</td>
            <td>:</td>
            <td>{{ $invoice['vendor_name'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Purchase Order</td>
            <td>:</td>
            <td>{{ $invoice['purchase_order_number'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>Termin</td>
            <td>:</td>
            <td>Termin {{ $invoice['term'] ?? '-' }} ({{ number_format($invoice['term_percentage'] ?? 0, 2, ',', '.') }}%)</td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th width="40">No.</th>
                <th>Uraian</th>
                <th width="135">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>Nilai Pembiayaan Disetujui</td>
                <td class="amount">{{ termInvoiceRupiah($invoice['approved_financing_amount'] ?? 0) }}</td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td>Nominal Termin {{ $invoice['term'] ?? '-' }}</td>
                <td class="amount">{{ termInvoiceRupiah($invoice['term_amount'] ?? 0) }}</td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td>Rate TomiInvoice ({{ number_format($invoice['rate'] ?? 0, 2, ',', '.') }}%)</td>
                <td class="amount">{{ termInvoiceRupiah(($invoice['term_amount'] ?? 0) * (($invoice['rate'] ?? 0) / 100)) }}</td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td>Admin Fee Termin</td>
                <td class="amount">{{ termInvoiceRupiah($invoice['admin_fee'] ?? 0) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2">TOTAL INVOICE FEE</td>
                <td class="amount">{{ termInvoiceRupiah($invoice['total_fee'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="note">
        <b>Catatan:</b><br>
        Invoice ini dibuat berdasarkan data financing request invoice dan konfigurasi termin pembayaran vendor.
    </div>
</body>

</html>
