<!DOCTYPE html>
<html lang="en">
@php
use Carbon\Carbon;

$xxx = 1;
$totalRate = 0;
$totalFeeTomi = 0;
$totalBiayaLayanan = 0;
$totalBiayaAdmin = 0;
$totalKeseluruhan = 0;

$selectedColumns = $selectedColumns ?? [];
$logoPath = public_path('assets/images/logo.jpg');
$logoSrc = file_exists($logoPath)
    ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
    : '';

/*
|--------------------------------------------------------------------------
| Selected Columns
|--------------------------------------------------------------------------
*/
$showTanggalPinjaman = in_array('tanggal_pinjaman', $selectedColumns);
$showNip = in_array('nip', $selectedColumns);
$showNominalPinjaman = in_array('nominal_pinjaman', $selectedColumns);
$showRate = in_array('rate', $selectedColumns);
$showFeeTomi = in_array('fee_tomi', $selectedColumns);
$showBiayaLayanan = in_array('biaya_layanan', $selectedColumns);
$showBiayaAdmin = in_array('biaya_admin', $selectedColumns);
$showTotal = in_array('total', $selectedColumns);

/*
|--------------------------------------------------------------------------
| Rate
|--------------------------------------------------------------------------
| Fee TOMI = 1% dari nominal pinjaman
|--------------------------------------------------------------------------
*/
$ratePersen = 1;

/*
|--------------------------------------------------------------------------
| Batas Perubahan Skema Biaya
|--------------------------------------------------------------------------
| Sebelum 17 April:
| - Fee TOMI 1%
| - Biaya Layanan 20.000
|
| Mulai 17 April dan setelahnya:
| - Biaya Admin 1%
| - Biaya Layanan 10.000
|--------------------------------------------------------------------------
*/
$tahunInvoice = Carbon::parse($historyInvoicingLenderPending->period ?? now())->year;
$batasTanggalApril = Carbon::create($tahunInvoice, 4, 17)->startOfDay();

/*
|--------------------------------------------------------------------------
| Colspan Total Row
|--------------------------------------------------------------------------
| Kolom sebelum kolom Rate:
| No, Tanggal Pinjaman, NIP, Nominal Pinjaman
|--------------------------------------------------------------------------
*/
$jumlahKolomSebelumRate = 1;

if ($showTanggalPinjaman) {
$jumlahKolomSebelumRate++;
}

if ($showNip) {
$jumlahKolomSebelumRate++;
}

if ($showNominalPinjaman) {
$jumlahKolomSebelumRate++;
}
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Fee Tomi + Layanan</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 14px 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
            margin-top: 6px;
        }

        .mt-3 {
            margin-top: 9px;
        }

        .mt-4 {
            margin-top: 12px;
        }

        .pt-4 {
            padding-top: 12px;
        }

        .pt-5 {
            padding-top: 18px;
        }

        .m-0 {
            margin: 0;
        }

        .text-muted,
        .text-right,
        .text-center {
            color: #000;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .heading-title {
            margin: 0 0 6px 0;
            font-size: 13px;
            font-weight: bold;
            color: #000;
        }

        .intro-text {
            font-size: 9.5px;
            line-height: 1.35;
            color: #000;
        }

        .invoice-text {
            font-size: 9.5px;
            font-weight: bold;
            color: #000;
        }

        .section-title {
            margin: 0 0 3px 0;
            font-size: 10.5px;
            font-weight: bold;
            color: #000;
        }

        .section-subtitle {
            margin: 0 0 6px 0;
            font-size: 9.5px;
            font-weight: normal;
            color: #000;
        }

        h3,
        h4,
        h5,
        h6,
        p,
        b,
        strong,
        small,
        span,
        div,
        td,
        th {
            color: #000;
        }

        h4 {
            margin: 0 0 6px 0;
            font-size: 14px;
            font-weight: bold;
        }

        h5 {
            margin: 0 0 6px 0;
            font-size: 12px;
            font-weight: bold;
        }

        h6 {
            margin: 0 0 4px 0;
            font-size: 10.5px;
            font-weight: bold;
        }

        p {
            margin: 0 0 6px 0;
            line-height: 1.35;
        }

        small {
            font-size: 8.5px;
        }

        .logo {
            height: 34px;
        }

        .table-responsive {
            width: 100%;
            margin-top: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
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
            padding: 5px 4px;
            vertical-align: middle;
            border-left: none;
            border-right: none;
            color: #000;
            font-size: 8.7px;
            line-height: 1.25;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        th {
            text-align: center;
            vertical-align: middle;
            font-size: 8.5px;
            font-weight: bold;
            background: none;
        }

        tbody tr.total-row {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .col-no {
            width: 4%;
        }

        .col-date {
            width: 13%;
        }

        .col-nip {
            width: 22%;
        }

        .col-nominal {
            width: 13%;
        }

        .col-rate {
            width: 11%;
        }

        .col-fee {
            width: 11%;
        }

        .col-service {
            width: 12%;
        }

        .col-admin {
            width: 12%;
        }

        .col-total {
            width: 13%;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
            font-size: 8.5px;
        }

        .number-cell {
            text-align: center;
        }

        .summary-box {
            width: 260px;
            float: right;
            margin-top: 12px;
            color: #000;
        }

        .summary-box p {
            margin: 0 0 4px 0;
            color: #000;
        }

        .summary-box h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .note-box {
            width: 55%;
            margin-top: 18px;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="card-box">

            <div class="clearfix">
                <div class="float-left">
                    @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" class="logo">
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <p>
                    <b>Hallo, {{ $historyInvoicingLenderPending->lender->nama ?? '-' }}</b>
                </p>

                <p class="text-muted intro-text">
                    Berikut adalah rincian tagihan biaya atas penggunaan fasilitas TomiDana pada aplikasi platform
                    digital Tomi yang diberikan kepada
                    <b>{{ $historyInvoicingLenderPending->lender->nama ?? '-' }}</b>, periode
                    <b>{{ Carbon::parse($historyInvoicingLenderPending->period)->firstOfMonth()->format('Y-m-d') }}
                        -
                        {{ Carbon::parse($historyInvoicingLenderPending->period)->endOfMonth()->format('Y-m-d') }}</b>;

                    <br>

                    <span class="invoice-text">
                        <b>No. Invoice : {{ $historyInvoicingLenderPending->no_invoice ?? '-' }}</b>
                    </span>
                </p>
            </div>

            <div class="mt-4">
                <h6 class="section-title">Lampiran Pinjaman</h6>

                <h6 class="section-subtitle">
                    ID Lender :
                    <b>{{ $historyInvoicingLenderPending->lender->id_code_lender ?? '-' }}</b>
                </h6>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-no">No.</th>

                                @if ($showTanggalPinjaman)
                                <th class="col-date">Tanggal Pinjaman</th>
                                @endif

                                @if ($showNip)
                                <th class="col-nip">NIP</th>
                                @endif

                                @if ($showNominalPinjaman)
                                <th class="col-nominal">Nominal Pinjaman</th>
                                @endif

                                @if ($showRate)
                                <th class="col-rate">Rate</th>
                                @endif

                                @if ($showFeeTomi)
                                <th class="col-fee">Fee TOMI</th>
                                @endif

                                @if ($showBiayaLayanan)
                                <th class="col-service">Biaya Layanan</th>
                                @endif

                                @if ($showBiayaAdmin)
                                <th class="col-admin">Biaya Admin</th>
                                @endif

                                @if ($showTotal)
                                <th class="col-total">Total</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($historyInvoicingLenderPending->lender->pinjaman as $item)
                            @php
                            $nominalPinjaman = $item->nominal ?? 0;
                            $tanggalPinjaman = Carbon::parse($item->created_at)->startOfDay();

                            /*
                            |--------------------------------------------------------------------------
                            | Skema Biaya Berdasarkan Tanggal Pinjaman
                            |--------------------------------------------------------------------------
                            | Sebelum 17 April:
                            | - Fee TOMI = 1% dari nominal pinjaman
                            | - Biaya Layanan = 20.000
                            | - Biaya Admin = 0
                            |
                            | Mulai 17 April dan setelahnya:
                            | - Fee TOMI = 0
                            | - Biaya Layanan = 10.000
                            | - Biaya Admin = 1% dari nominal pinjaman
                            |--------------------------------------------------------------------------
                            */
                            $isSebelumTanggal17April = $tanggalPinjaman->lt($batasTanggalApril);

                            if ($isSebelumTanggal17April) {
                            $rate = $nominalPinjaman * ($ratePersen / 100);
                            $feeTomi = $rate;
                            $biayaLayanan = 20000;
                            $biayaAdmin = 0;
                            } else {
                            $rate = 0;
                            $feeTomi = 0;
                            $biayaLayanan = 10000;
                            $biayaAdmin = $nominalPinjaman * 0.01;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Subtotal Dinamis
                            |--------------------------------------------------------------------------
                            | Subtotal hanya menghitung kolom yang tampil.
                            | Kalau kolom tidak tampil, nilainya tidak ikut dihitung.
                            |--------------------------------------------------------------------------
                            */
                            $subtotal = 0;

                            if ($showRate) {
                            $subtotal += $rate;
                            }

                            if ($showFeeTomi) {
                            $subtotal += $feeTomi;
                            }

                            if ($showBiayaLayanan) {
                            $subtotal += $biayaLayanan;
                            }

                            if ($showBiayaAdmin) {
                            $subtotal += $biayaAdmin;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Total Per Kolom Dinamis
                            |--------------------------------------------------------------------------
                            | Total setiap kolom hanya dihitung jika kolomnya tampil.
                            |--------------------------------------------------------------------------
                            */
                            if ($showRate) {
                            $totalRate += $rate;
                            }

                            if ($showFeeTomi) {
                            $totalFeeTomi += $feeTomi;
                            }

                            if ($showBiayaLayanan) {
                            $totalBiayaLayanan += $biayaLayanan;
                            }

                            if ($showBiayaAdmin) {
                            $totalBiayaAdmin += $biayaAdmin;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Total Keseluruhan Dinamis
                            |--------------------------------------------------------------------------
                            | Total keseluruhan hanya menjumlahkan subtotal yang tampil.
                            |--------------------------------------------------------------------------
                            */
                            if ($showTotal) {
                            $totalKeseluruhan += $subtotal;
                            }
                            @endphp

                            <tr>
                                <td class="number-cell">{{ $xxx }}</td>

                                @if ($showTanggalPinjaman)
                                <td>
                                    <b>{{ $tanggalPinjaman->format('d-m-Y') }}</b>
                                    <br>
                                    {{ $item->id_pinjaman ?? '-' }}
                                </td>
                                @endif

                                @if ($showNip)
                                <td>
                                    <b>{{ $item->data_karyawan->nama_karyawan ?? '-' }}</b>
                                    <br>
                                    <small>{{ $item->perusahaan->nama ?? '-' }}</small>
                                    <br>
                                    {{ $item->data_karyawan->nip ?? '-' }}
                                </td>
                                @endif

                                @if ($showNominalPinjaman)
                                <td class="amount">
                                    Rp {{ number_format($nominalPinjaman, 0, ',', '.') }}
                                </td>
                                @endif

                                @if ($showRate)
                                <td class="amount">
                                    Rp {{ number_format($rate, 0, ',', '.') }}
                                </td>
                                @endif

                                @if ($showFeeTomi)
                                <td class="amount">
                                    Rp {{ number_format($feeTomi, 0, ',', '.') }}
                                </td>
                                @endif

                                @if ($showBiayaLayanan)
                                <td class="amount">
                                    Rp {{ number_format($biayaLayanan, 0, ',', '.') }}
                                </td>
                                @endif

                                @if ($showBiayaAdmin)
                                <td class="amount">
                                    Rp {{ number_format($biayaAdmin, 0, ',', '.') }}
                                </td>
                                @endif

                                @if ($showTotal)
                                <td class="amount">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                @endif
                            </tr>

                            @php
                            $xxx++;
                            @endphp
                            @endforeach

                            <tr class="total-row">
                                <td colspan="{{ $jumlahKolomSebelumRate }}" class="number-cell">
                                    <b>Total</b>
                                </td>

                                @if ($showRate)
                                <td class="amount">
                                    <b>Rp {{ number_format($totalRate, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if ($showFeeTomi)
                                <td class="amount">
                                    <b>Rp {{ number_format($totalFeeTomi, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if ($showBiayaLayanan)
                                <td class="amount">
                                    <b>Rp {{ number_format($totalBiayaLayanan, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if ($showBiayaAdmin)
                                <td class="amount">
                                    <b>Rp {{ number_format($totalBiayaAdmin, 0, ',', '.') }}</b>
                                </td>
                                @endif

                                @if ($showTotal)
                                <td class="amount">
                                    <b>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</b>
                                </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($showTotal)
            <div class="summary-box">
                <p><b>Total :</b></p>
                <h3>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</h3>
            </div>
            @endif

            <div class="clearfix"></div>

            <div class="note-box pt-5">
                <h6>Noted:</h6>

                <small class="text-muted">
                    Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal
                    5 ke rekening bank berikut:
                    <br>

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
