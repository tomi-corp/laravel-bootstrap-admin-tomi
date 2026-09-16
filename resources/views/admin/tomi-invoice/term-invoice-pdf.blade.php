@php
$logoPath = public_path('assets/images/logo.jpg');
$logoSrc = file_exists($logoPath)
? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
: '';

$invoiceDate = $invoice['finance_invoice_term_date'] ?? $invoice['created_at'] ?? now();
$invoiceDateCarbon = !empty($invoiceDate) ? \Carbon\Carbon::parse($invoiceDate) : now();
$periodText = $invoiceDateCarbon->translatedFormat('F');
$dueDate = $invoiceDateCarbon->copy()->addDays(37)->translatedFormat('d F Y');
$termText = 'Termin ' . ($invoice['term'] ?? '-');

$invoiceColumnLabels = [
'vendor' => 'Pelanggan Mitra',
'purchase_order_number' => 'No. PO',
'approved_financing_amount' => 'Nilai Pembiayaan',
'term_amount' => 'Nominal Termin',
'fee_tomi' => 'Biaya TOMI',
'admin_fee' => 'Admin Fee',
'total_fee' => 'Jumlah Total Biaya',
];

$defaultColumns = array_keys($invoiceColumnLabels);
$hasColumnSelection = $selectedColumns !== null;
$selectedColumns = collect($selectedColumns ?? $defaultColumns)
->filter(fn($column) => array_key_exists($column, $invoiceColumnLabels))
->values()
->all();

if (!$hasColumnSelection) {
$selectedColumns = $defaultColumns;
}

$showColumn = fn(string $column): bool => in_array($column, $selectedColumns, true);

$termAmount = (float) ($invoice['term_amount'] ?? 0);
$rate = (float) ($invoice['rate'] ?? 0);
$feeTomi = $termAmount * ($rate / 100);
$adminFee = (float) ($invoice['admin_fee'] ?? 0);
$totalFee = (float) ($invoice['total_fee'] ?? ($feeTomi + $adminFee));

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
            size: legal landscape;
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
        }

        .clearfix::after {
            clear: both;
            content: "";
            display: table;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .header {
            border-bottom: 2px solid #444;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo {
            height: 50px;
        }

        .title {
            color: #007bff;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            text-align: right;
        }

        .invoice-meta {
            font-size: 11px;
            line-height: 1.5;
            margin-top: 5px;
            text-align: right;
        }

        .info-section {
            line-height: 1.55;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table.main-table {
            margin-top: 20px;
        }

        table.main-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 11px;
            padding: 8px;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
        }

        table.main-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
        }

        table.main-table tfoot td {
            background-color: #f8f9fa;
        }

        .nowrap {
            white-space: nowrap;
        }

        .total-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 20px;
            padding: 15px;
        }

        .summary-table td {
            padding: 6px 0;
        }

        .total-title {
            font-size: 14px;
            font-weight: bold;
        }

        .total-amount {
            color: #28a745;
            font-size: 18px;
            font-weight: bold;
        }

        .note-section {
            color: #666;
            font-size: 10px;
            line-height: 1.5;
            margin-top: 30px;
        }

        .bank-info {
            border: 1px dashed #ccc;
            display: inline-block;
            margin-top: 5px;
            padding: 10px;
        }

        .bank-info td {
            padding: 2px 4px;
        }

        .bank-info .bank-value {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <div class="float-left">
            @if ($logoSrc)
            <img src="{{ $logoSrc }}" class="logo" alt="Logo">
            @endif
        </div>

        <div class="float-right">
            <h1 class="title">INVOICE TOMIINVOICE</h1>

            <div class="invoice-meta">
                <div>
                    <b>No. Invoice:</b> {{ $invoice['nomor_invoice'] ?? '-' }}
                </div>
                <div>
                    <b>Tanggal Invoice:</b> {{ $invoiceDateCarbon->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="info-section clearfix">
        <div class="float-left">
            <p><b>Halo, Koperasi Simpan Pinjam Dumi Digital Teknologi</b></p>
            <p>
                Berikut adalah rincian tagihan biaya atas penggunaan fasilitas TomiInvoice pada aplikasi platform
                digital Tomi yang diberikan kepada <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>, periode
                <b>{{ $periodText }}</b> untuk <b>{{ $termText }}</b>;
            </p>
        </div>
    </div>

    <p>
        <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b><br>
        {{ $termText }}
    </p>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Tanggal Invoice</th>
                <th>Mitra</th>
                @if($showColumn('vendor'))
                <th>Pelanggan Mitra</th>
                @endif
                @if($showColumn('purchase_order_number'))
                <th>No. PO</th>
                @endif
                @if($showColumn('approved_financing_amount'))
                <th>Nilai Pembiayaan</th>
                @endif
                @if($showColumn('term_amount'))
                <th>Nominal Termin</th>
                @endif
                @if($showColumn('fee_tomi'))
                <th>Biaya TOMI<br><strong>(A)</strong></th>
                @endif
                @if($showColumn('admin_fee'))
                <th>Admin Fee<br><strong>(B)</strong></th>
                @endif
                @if($showColumn('total_fee'))
                <th>Jumlah Total Biaya<br><strong>(A + B)</strong></th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td class="nowrap">
                    <b>{{ $invoiceDateCarbon->translatedFormat('d F Y') }}</b>
                </td>
                <td>
                    <b>{{ $invoice['nama_perusahaan'] ?? '-' }}</b>
                </td>
                @if($showColumn('vendor'))
                <td>{{ $invoice['vendor_name'] ?? '-' }}</td>
                @endif
                @if($showColumn('purchase_order_number'))
                <td>{{ $invoice['purchase_order_number'] ?? '-' }}</td>
                @endif
                @if($showColumn('approved_financing_amount'))
                <td align="right" class="nowrap">{{ termInvoiceRupiah($invoice['approved_financing_amount'] ?? 0) }}
                </td>
                @endif
                @if($showColumn('term_amount'))
                <td align="right" class="nowrap">{{ termInvoiceRupiah($termAmount) }}</td>
                @endif
                @if($showColumn('fee_tomi'))
                <td align="right" class="nowrap">{{ termInvoiceRupiah($feeTomi) }}</td>
                @endif
                @if($showColumn('admin_fee'))
                <td align="right" class="nowrap">{{ termInvoiceRupiah($adminFee) }}</td>
                @endif
                @if($showColumn('total_fee'))
                <td align="right" class="nowrap">{{ termInvoiceRupiah($totalFee) }}</td>
                @endif
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{
                    3
                    + ($showColumn('vendor') ? 1 : 0)
                    + ($showColumn('purchase_order_number') ? 1 : 0)
                }}" align="center">
                    <b>TOTAL</b>
                </td>
                @if($showColumn('approved_financing_amount'))
                <td align="right" class="nowrap">
                    <b>{{ termInvoiceRupiah($invoice['approved_financing_amount'] ?? 0) }}</b>
                </td>
                @endif
                @if($showColumn('term_amount'))
                <td align="right" class="nowrap">
                    <b>{{ termInvoiceRupiah($termAmount) }}</b>
                </td>
                @endif
                @if($showColumn('fee_tomi'))
                <td align="right" class="nowrap">
                    <b>{{ termInvoiceRupiah($feeTomi) }}</b>
                </td>
                @endif
                @if($showColumn('admin_fee'))
                <td align="right" class="nowrap">
                    <b>{{ termInvoiceRupiah($adminFee) }}</b>
                </td>
                @endif
                @if($showColumn('total_fee'))
                <td align="right" class="nowrap">
                    <b>{{ termInvoiceRupiah($totalFee) }}</b>
                </td>
                @endif
            </tr>
        </tfoot>
    </table>

    <div class="clearfix">
        <div class="float-right total-section" style="width: 350px;">
            <table class="summary-table">
                <tr>
                    <td class="total-title">JUMLAH TOTAL BIAYA</td>
                    <td align="right" class="total-amount">{{ termInvoiceRupiah($totalFee) }}</td>
                </tr>
            </table>
        </div>

        <div class="float-left note-section" style="width: 400px;">
            <b>Catatan Pembayaran:</b>

            <p>
                Harap segera melakukan pembayaran paling lambat tanggal
                <b>{{ $dueDate }}</b>
                ke rekening berikut:
            </p>

            <div class="bank-info total-title">
                <table>
                    <tr>
                        <td width="100">Bank</td>
                        <td>:</td>
                        <td class="bank-value"><b>BANK {{ $rekening->bank->code_bank ?? 'BRI' }}</b></td>
                    </tr>
                    <tr>
                        <td width="100">No. Rekening</td>
                        <td>:</td>
                        <td class="bank-value"><b>{{ $rekening->nomor_rekening ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td width="100">Atas Nama</td>
                        <td>:</td>
                        <td class="bank-value"><b>{{ $rekening->atas_nama ?? 'PT TOKO MIKRO INDONESIA' }}</b></td>
                    </tr>
                </table>
            </div>

            <p>
                <i>*Mohon lampirkan bukti transfer saat konfirmasi.</i>
            </p>
        </div>
    </div>
</body>

</html>