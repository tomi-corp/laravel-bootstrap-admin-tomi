<!DOCTYPE html>
<html lang="en">
@php
use Carbon\Carbon;

$xxx = 1;
$totalFeeTomi = 0;
$totalBiayaLayanan = 0;
$totalBiayaAdmin = 0;
$totalKeseluruhan = 0;

$selectedColumns = $selectedColumns ?? [];

$showTanggalPinjaman = in_array('tanggal_pinjaman', $selectedColumns);
$showNip = in_array('nip', $selectedColumns);
$showNominalPinjaman = in_array('nominal_pinjaman', $selectedColumns);
$showFeeTomi = in_array('fee_tomi', $selectedColumns);
$showBiayaLayanan = in_array('biaya_layanan', $selectedColumns);
$showBiayaAdmin = in_array('biaya_admin', $selectedColumns);
$showTotal = in_array('total', $selectedColumns);

$jumlahKolomSebelumBiaya = 1;

if ($showTanggalPinjaman) $jumlahKolomSebelumBiaya++;
if ($showNip) $jumlahKolomSebelumBiaya++;
if ($showNominalPinjaman) $jumlahKolomSebelumBiaya++;
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Fee Tomi + Layanan</title>
    <style>
        @page {
            size: landscape;
            margin: 20px 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            width: 100%;
        }

        .card-box {
            width: 100%;
        }

        .clearfix {
            width: 100%;
            clear: both;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .pt-4 {
            padding-top: 16px;
        }

        .pt-5 {
            padding-top: 24px;
        }

        .m-0 {
            margin: 0;
        }

        .text-muted {
            color: #000;
        }

        .text-right {
            text-align: right;
            color: #000;
        }

        .text-center {
            text-align: center;
            color: #000;
        }

        .heading-title {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .intro-text {
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }

        .invoice-text {
            font-size: 11px;
            font-weight: bold;
            color: #000;
        }

        .section-title {
            margin: 0 0 4px 0;
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }

        .section-subtitle {
            margin: 0 0 8px 0;
            font-size: 11px;
            font-weight: normal;
            color: #000;
        }

        h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        h5 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        h6 {
            margin: 0 0 6px 0;
            font-size: 13px;
            font-weight: bold;
            color: #000;
        }

        h3 {
            color: #000;
        }

        p {
            margin: 0 0 8px 0;
            line-height: 1.5;
            color: #000;
        }

        b,
        strong,
        small,
        span,
        div,
        td,
        th {
            color: #000;
        }

        small {
            font-size: 11px;
        }

        .logo {
            height: 40px;
        }

        .table-responsive {
            width: 100%;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            color: #000;
        }

        thead tr {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        tbody tr {
            border-bottom: 1px solid #000;
        }

        th,
        td {
            padding: 10px 8px;
            vertical-align: top;
            border-left: none;
            border-right: none;
            color: #000;
        }

        th {
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            color: #000;
            background: none;
        }

        tbody tr.total-row {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .summary-box {
            width: 320px;
            float: right;
            margin-top: 18px;
            color: #000;
        }

        .summary-box p {
            margin: 0 0 6px 0;
            color: #000;
        }

        .summary-box h3 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #000;
        }

        .note-box {
            width: 55%;
            margin-top: 26px;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="card-box">

            <div class="clearfix">
                <div class="float-left">
                    @php
                    $logoPath = public_path('assets/images/logo.jpg');
                    @endphp

                    @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo" class="logo">
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <p><b>Hallo, {{ $historyInvoicingLenderPending->lender->nama }}</b></p>
                <p class="text-muted intro-text">
                    Berikut rincian tagihan Fasilitas Pinjaman e-Kasbon aplikasi layanan digital TOMI kepada
                    {{ $historyInvoicingLenderPending->lender->nama }} Pada Tanggal
                    <b>{{ Carbon::parse($historyInvoicingLenderPending->period)->firstOfMonth()->format('Y-m-d') }}
                        -
                        {{ Carbon::parse($historyInvoicingLenderPending->period)->endOfMonth()->format('Y-m-d') }}</b>
                    <br>
                    <span class="invoice-text">
                        <b>No. Invoice : {{ $historyInvoicingLenderPending->no_invoice }}</b>
                    </span>
                </p>
            </div>

            <div class="mt-4">
                <h6 class="section-title">Lampiran Pinjaman</h6>
                <h6 class="section-subtitle">
                    ID Lender :
                    <b>{{ $historyInvoicingLenderPending->lender->id_code_lender }}</b>
                </h6>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th width="40">No.</th>

                                @if($showTanggalPinjaman)
                                <th width="130">Tanggal Pinjaman</th>
                                @endif

                                @if($showNip)
                                <th>NIP</th>
                                @endif

                                @if($showNominalPinjaman)
                                <th width="120">Nominal Pinjaman</th>
                                @endif

                                @if($showFeeTomi)
                                <th width="90">Fee TOMI</th>
                                @endif

                                @if($showBiayaLayanan)
                                <th width="110">Biaya Layanan</th>
                                @endif

                                @if($showBiayaAdmin)
                                <th width="110">Biaya Admin</th>
                                @endif

                                @if($showTotal)
                                <th width="120">Total</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historyInvoicingLenderPending->lender->pinjaman as $item)
                            @php
                            $feeTomi = 0;
                            $biayaLayanan = 10000;
                            $biayaAdmin = $item->nominal * 0.01;
                            $subtotal = $feeTomi + $biayaLayanan + $biayaAdmin;

                            $totalFeeTomi += $feeTomi;
                            $totalBiayaLayanan += $biayaLayanan;
                            $totalBiayaAdmin += $biayaAdmin;
                            $totalKeseluruhan += $subtotal;
                            @endphp

                            <tr>
                                <td>{{ $xxx }}</td>

                                @if($showTanggalPinjaman)
                                <td>
                                    <b>{{ Carbon::parse($item->created_at)->format('d-m-Y') }}</b><br>
                                    {{ $item->id_pinjaman }}
                                </td>
                                @endif

                                @if($showNip)
                                <td>
                                    <b>{{ $item->data_karyawan->nama_karyawan ?? '-' }}</b><br>
                                    <small>{{ $item->perusahaan->nama ?? '-' }}</small><br>
                                    {{ $item->data_karyawan->nip ?? '-' }}
                                </td>
                                @endif

                                @if($showNominalPinjaman)
                                <td>
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                @endif

                                @if($showFeeTomi)
                                <td>
                                    Rp {{ number_format($feeTomi, 0, ',', '.') }}
                                </td>
                                @endif

                                @if($showBiayaLayanan)
                                <td>
                                    Rp {{ number_format($biayaLayanan, 0, ',', '.') }}
                                </td>
                                @endif

                                @if($showBiayaAdmin)
                                <td>
                                    Rp {{ number_format($biayaAdmin, 0, ',', '.') }}
                                </td>
                                @endif

                                @if($showTotal)
                                <td>
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                @endif
                            </tr>

                            @php
                            $xxx++;
                            @endphp
                            @endforeach

                            <tr class="total-row">
                                <td colspan="{{ $jumlahKolomSebelumBiaya }}">
                                    <b>Total</b>
                                </td>

                                @if($showFeeTomi)
                                <td>
                                    <b>Rp {{ number_format($totalFeeTomi, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if($showBiayaLayanan)
                                <td>
                                    <b>Rp {{ number_format($totalBiayaLayanan, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if($showBiayaAdmin)
                                <td>
                                    <b>Rp {{ number_format($totalBiayaAdmin, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if($showTotal)
                                <td>
                                    <b>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</b>
                                </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="summary-box">
                <p><b>Total :</b></p>
                <h3>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</h3>
            </div>

            <div class="clearfix"></div>

            <div class="note-box pt-5">
                <h6>Noted:</h6>

                <small class="text-muted">
                    Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal
                    5 ke rekening bank berikut: <br>
                    BANK : {{ $rekening->bank->code_bank ?? '-' }}
                    <br>
                    Atas Nama :
                    {{ $rekening->atas_nama ?? '-' }}
                    <br>
                    No Rekening :
                    {{ $rekening->nomor_rekening ?? '-' }}
                    <br>
                </small>
            </div>
        </div>
    </div>
</body>

</html>