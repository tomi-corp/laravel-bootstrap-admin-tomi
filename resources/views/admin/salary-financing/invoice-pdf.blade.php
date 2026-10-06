@php
/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

function cleanRupiahValue($value)
{
if (is_null($value) || $value === '') {
return 0;
}

if (is_numeric($value)) {
return (float) $value;
}

$value = str_replace(['Rp', 'rp', ' '], '', (string) $value);

if (str_contains($value, ',') && str_contains($value, '.')) {
$value = str_replace('.', '', $value);
$value = str_replace(',', '.', $value);
} elseif (str_contains($value, ',')) {
$value = str_replace(',', '.', $value);
} elseif (substr_count($value, '.') > 1) {
$value = str_replace('.', '', $value);
}

return is_numeric($value) ? (float) $value : 0;
}

function roundUpToThousand($value): float
{
$value = cleanRupiahValue($value);

if ($value <= 0) { return 0; } return ceil($value / 1000) * 1000; } function rateToMultiplier($value): float {
    $value=cleanRupiahValue($value); if ($value <=0) { return 0; } return $value <=1 ? $value : ($value / 100); }
    function formatRupiahInvoice($value): string { $value=round(cleanRupiahValue($value)); return 'Rp ' . number_format($value, 0, ',' , '.' ); }
    function formatPercentValue($value): string { $value=cleanRupiahValue($value); $displayValue=$value <=1 ? ($value *
    100) : $value; if (floor($displayValue)==$displayValue) { return number_format($displayValue, 0, ',' , '.' ) . '%' ;
    } return number_format($displayValue, 2, ',' , '.' ) . '%' ; } function firstFilledValue($rows, string $key,
    $default='-' ) { foreach ($rows as $row) { $value=$row[$key] ?? null; if ($value !==null && $value !=='' ) { return
    $value; } } return $default; } function formatInvoiceColumnValue(string $column, $value): string { if
    ($column==='provinsi' ) { return ( $value===null || $value==='' || $value==='0' ) ? '-' : (string) $value; } if
    ($column==='iuran_anggota' ) { return 'Rp ' . number_format( cleanRupiahValue($value), 0, ',' , '.' ); } return
    formatPercentValue($value); } function salaryInvoicePeriodMonth($items, $invoice): string { $tanggalPencairan =
    firstFilledValue( $items, 'tanggal_pencairan', $invoice['tanggal_pencairan'] ?? $invoice['tanggal_pembayaran'] ??
    $invoice['tanggal_transfer'] ?? $invoice['tanggal_pengajuan'] ?? $invoice['tanggal_bukti_transfer'] ??
    $invoice['created_at'] ?? null ); if (!empty($tanggalPencairan) && $tanggalPencairan !== '-') { try { return
    \Carbon\Carbon::parse($tanggalPencairan)->translatedFormat('F'); } catch (\Throwable $e) { } } return !empty(
    $invoice['created_at'] ?? null ) ? \Carbon\Carbon::parse($invoice['created_at'])->translatedFormat('F') : '-'; } /* |-------------------------------------------------------------------------- | Data
    Awal |-------------------------------------------------------------------------- */ $items=$items ?? [];
    $selectedColumns=$selectedColumns ?? [ 'rate' , 'asuransi' , 'provinsi' , 'biaya_admin' , 'biaya_tomi'
    , 'biaya_referal_1' , 'biaya_referal_2' , 'ksp' , 'iuran_anggota' , ]; $invoiceColumnLabels=[ 'rate'=> 'Rate (%)',
    'asuransi' => 'Asuransi (%)',
    'provinsi' => 'Provinsi',
    'biaya_admin' => 'Biaya Admin (%)',
    'biaya_tomi' => 'Fee TOMI',
    'biaya_referal_1' => 'Referal 1',
    'biaya_referal_2' => 'Referal 2',
    'ksp' => 'KSP (%)',
    'iuran_anggota' => 'Iuran Anggota',
    ];

    $selectedColumns = collect($selectedColumns)
    ->filter(function ($column) use ($invoiceColumnLabels) {
    return array_key_exists(
    $column,
    $invoiceColumnLabels
    );
    })
    ->values()
    ->all();


    /*
    |--------------------------------------------------------------------------
    | Pisahkan Column Config dan Fee
    |--------------------------------------------------------------------------
    */

    $feeColumnKeys = [
    'biaya_tomi',
    'biaya_referal_1',
    'biaya_referal_2',
    ];

    $selectedFeeColumns = collect($selectedColumns)
    ->filter(function ($column) use ($feeColumnKeys) {
    return in_array(
    $column,
    $feeColumnKeys,
    true
    );
    })
    ->values()
    ->all();

    $selectedConfigColumns = collect($selectedColumns)
    ->reject(function ($column) use ($feeColumnKeys) {
    return in_array(
    $column,
    $feeColumnKeys,
    true
    );
    })
    ->values()
    ->all();


    /*
    |--------------------------------------------------------------------------
    | Nomor Invoice
    |--------------------------------------------------------------------------
    */

    $nomorInvoice = $invoice['nomor_invoice']
    ?? (
    'TG/'
    . date(
    'Ym',
    strtotime(
    $invoice['created_at'] ?? now()
    )
    )
    . '/'
    . ($invoice['id'] ?? '-')
    );


    /*
    |--------------------------------------------------------------------------
    | Normalisasi Data Item
    |--------------------------------------------------------------------------
    |
    | PENTING:
    |
    | jumlah_pendapatan di sini diperlakukan sebagai
    | JUMLAH FASILITAS PINJAMAN per item.
    |
    | Biaya admin dihitung serta di-roundup per item. Jumlah
    | tagihan memakai nilai fasilitas + biaya admin tanpa roundup.
    | Setelah itu baru dijumlahkan
    | untuk baris penempatan kerja dan total keseluruhan.
    |
    |--------------------------------------------------------------------------
    */

    $items = collect($items)
    ->map(function ($item) use ($invoice) {

    /*
    |--------------------------------------------------------------------------
    | Jumlah Fasilitas Pinjaman
    |--------------------------------------------------------------------------
    */

    $jumlahPendapatan = cleanRupiahValue(
    $item['jumlah_pendapatan']
    ?? $item['jumlah_pinjaman']
    ?? $item['total_pinjaman']
    ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | Biaya Admin Rate
    |--------------------------------------------------------------------------
    */

    $biayaAdminRate = cleanRupiahValue(
    $item['biaya_admin_rate']
    ?? $item['persen_biaya_admin']
    ?? $invoice['biaya_admin_rate']
    ?? 2.5
    );


    /*
    |--------------------------------------------------------------------------
    | Fee TOMI
    |--------------------------------------------------------------------------
    */

    $feeTomiRate = cleanRupiahValue(
    $item['fee_tomi']
    ?? $invoice['fee_tomi']
    ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 1
    |--------------------------------------------------------------------------
    */

    $profesionalFee1Rate = cleanRupiahValue(
    $item['profesional_fee_1']
    ?? $item['professional_fee_1']
    ?? $invoice['profesional_fee_1']
    ?? $invoice['professional_fee_1']
    ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 2
    |--------------------------------------------------------------------------
    */

    $profesionalFee2Rate = cleanRupiahValue(
    $item['profesional_fee_2']
    ?? $item['professional_fee_2']
    ?? $invoice['profesional_fee_2']
    ?? $invoice['professional_fee_2']
    ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | Biaya Admin dan Jumlah Tagihan Per Item
    |--------------------------------------------------------------------------
    |
    | Biaya admin item di-roundup dulu, lalu ditambahkan dengan
    | jumlah fasilitas item tanpa roundup jumlah tagihan.
    |
    |--------------------------------------------------------------------------
    */

    $biayaAdmin = roundUpToThousand(
    $jumlahPendapatan
    * rateToMultiplier($biayaAdminRate)
    );

    $jumlahTagihan = round(
    $jumlahPendapatan
    + $biayaAdmin
    );


    /*
    |--------------------------------------------------------------------------
    | Simpan Data
    |--------------------------------------------------------------------------
    */

    $item['penempatan_kerja'] =
    $item['penempatan_kerja']
    ?? $item['penempatan']
    ?? $item['lokasi_kerja']
    ?? $item['cabang']
    ?? '-';

    $item['nama_perusahaan'] =
    $item['nama_perusahaan']
    ?? $item['company_name']
    ?? $item['perusahaan']
    ?? $invoice['nama_perusahaan']
    ?? '-';

    $item['tanggal_pencairan'] =
    $item['tanggal_pencairan']
    ?? $item['tanggal_pembayaran']
    ?? $item['tanggal_transfer']
    ?? $item['tanggal_pengajuan']
    ?? null;

    $item['jumlah_pendapatan'] =
    $jumlahPendapatan;

    $item['biaya_admin_persen'] =
    $biayaAdminRate;

    $item['biaya_admin'] =
    $biayaAdmin;

    $item['jumlah_tagihan'] =
    $jumlahTagihan;

    $item['rate'] =
    cleanRupiahValue(
    $item['rate']
    ?? $invoice['rate']
    ?? 0
    );

    $item['asuransi'] =
    cleanRupiahValue(
    $item['asuransi']
    ?? $invoice['asuransi']
    ?? 0
    );

    $item['provinsi'] =
    $item['provinsi']
    ?? $invoice['provinsi']
    ?? '-';

    $item['fee_tomi'] =
    $feeTomiRate;

    $item['profesional_fee_1'] =
    $profesionalFee1Rate;

    $item['profesional_fee_2'] =
    $profesionalFee2Rate;

    $item['ksp'] =
    cleanRupiahValue(
    $item['ksp']
    ?? $invoice['ksp']
    ?? 0
    );

    $item['iuran_anggota'] =
    cleanRupiahValue(
    $item['iuran_anggota']
    ?? $invoice['iuran_anggota']
    ?? 0
    );

    return $item;
    })
    ->values();


    /*
    |--------------------------------------------------------------------------
    | Grouping Berdasarkan Penempatan Kerja
    |--------------------------------------------------------------------------
    |
    | Biaya admin dan jumlah tagihan sudah dihitung per item.
    | Saat grouping, nilai tersebut dijumlahkan apa adanya agar
    | rounding setiap item tetap ikut terbawa ke total.
    |
    |--------------------------------------------------------------------------
    */

    $itemsByPenempatanKerja = $items
    ->groupBy(function ($item) {

    return !empty($item['penempatan_kerja'])
    ? $item['penempatan_kerja']
    : '-';

    })
    ->map(function ($group, $penempatanKerja) {

    /*
    |--------------------------------------------------------------------------
    | Total Fasilitas Pinjaman
    |--------------------------------------------------------------------------
    */

    $jumlahPendapatan = cleanRupiahValue(
    $group->sum('jumlah_pendapatan')
    );


    /*
    |--------------------------------------------------------------------------
    | Ambil Rate
    |--------------------------------------------------------------------------
    */

    $biayaAdminRate = cleanRupiahValue(
    firstFilledValue(
    $group,
    'biaya_admin_persen',
    0
    )
    );

    $feeTomiRate = cleanRupiahValue(
    firstFilledValue(
    $group,
    'fee_tomi',
    0
    )
    );

    $profesionalFee1Rate = cleanRupiahValue(
    firstFilledValue(
    $group,
    'profesional_fee_1',
    0
    )
    );

    $profesionalFee2Rate = cleanRupiahValue(
    firstFilledValue(
    $group,
    'profesional_fee_2',
    0
    )
    );


    /*
    |--------------------------------------------------------------------------
    | Biaya Admin
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Fasilitas = Rp 100.000.000
    | Admin = 2,5%
    |
    | Biaya Admin =
    | 100.000.000 × 2,5%
    | = 2.500.000
    |
    |--------------------------------------------------------------------------
    */

    $biayaAdmin = roundUpToThousand(
    $jumlahPendapatan
    * rateToMultiplier($biayaAdminRate)
    );


    /*
    |--------------------------------------------------------------------------
    | JUMLAH TAGIHAN PINJAMAN TOMIGAJI
    |--------------------------------------------------------------------------
    |
    | JUMLAH FASILITAS + BIAYA ADMIN
    |
    | Hanya dihitung SEKALI di sini.
    |
    |--------------------------------------------------------------------------
    */

    $jumlahTagihan = round(
    $jumlahPendapatan
    + $biayaAdmin
    );


    /*
    |--------------------------------------------------------------------------
    | Override Dengan Total Item
    |--------------------------------------------------------------------------
    |
    | Aturan invoice: biaya admin di-roundup per item, lalu jumlah
    | tagihan item tanpa roundup dijumlahkan untuk total grup.
    |
    |--------------------------------------------------------------------------
    */

    $biayaAdmin = cleanRupiahValue(
    $group->sum('biaya_admin')
    );

    $jumlahTagihan = cleanRupiahValue(
    $group->sum('jumlah_tagihan')
    );


    /*
    |--------------------------------------------------------------------------
    | Biaya TOMI
    |--------------------------------------------------------------------------
    */

    $biayaTomi = round(
    $jumlahPendapatan
    * rateToMultiplier($feeTomiRate)
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 1
    |--------------------------------------------------------------------------
    */

    $biayaReferal1 = round(
    $jumlahPendapatan
    * rateToMultiplier($profesionalFee1Rate)
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 2
    |--------------------------------------------------------------------------
    */

    $biayaReferal2 = round(
    $biayaTomi
    * rateToMultiplier($profesionalFee2Rate)
    );


    /*
    |--------------------------------------------------------------------------
    | Total Fee
    |--------------------------------------------------------------------------
    */

    $jumlahFee =
    $biayaTomi
    + $biayaReferal1
    + $biayaReferal2;


    return [

    'tanggal_pencairan' =>
    firstFilledValue(
    $group,
    'tanggal_pencairan',
    null
    ),

    'nama_perusahaan' =>
    firstFilledValue(
    $group,
    'nama_perusahaan',
    '-'
    ),

    'penempatan_kerja' =>
    $penempatanKerja,

    'rate' =>
    firstFilledValue(
    $group,
    'rate',
    0
    ),

    'asuransi' =>
    firstFilledValue(
    $group,
    'asuransi',
    0
    ),

    'provinsi' =>
    firstFilledValue(
    $group,
    'provinsi',
    '-'
    ),

    'biaya_admin_persen' =>
    $biayaAdminRate,

    'fee_tomi' =>
    $feeTomiRate,

    'profesional_fee_1' =>
    $profesionalFee1Rate,

    'profesional_fee_2' =>
    $profesionalFee2Rate,

    'biaya_tomi' =>
    $biayaTomi,

    'biaya_referal_1' =>
    $biayaReferal1,

    'biaya_referal_2' =>
    $biayaReferal2,

    'jumlah_total_biaya' =>
    $jumlahFee,

    'ksp' =>
    firstFilledValue(
    $group,
    'ksp',
    0
    ),

    'iuran_anggota' =>
    firstFilledValue(
    $group,
    'iuran_anggota',
    0
    ),

    /*
    |--------------------------------------------------------------
    | Jumlah Fasilitas
    |--------------------------------------------------------------
    */

    'jumlah_pendapatan' =>
    $jumlahPendapatan,

    /*
    |--------------------------------------------------------------
    | Biaya Admin
    |--------------------------------------------------------------
    */

    'biaya_admin' =>
    $biayaAdmin,

    /*
    |--------------------------------------------------------------
    | Jumlah Tagihan
    |--------------------------------------------------------------
    */

    'jumlah_tagihan' =>
    $jumlahTagihan,

    /*
    |--------------------------------------------------------------
    | Total Fee
    |--------------------------------------------------------------
    */

    'jumlah_fee' =>
    $jumlahFee,
    ];

    })
    ->values();


    /*
    |--------------------------------------------------------------------------
    | Total Keseluruhan
    |--------------------------------------------------------------------------
    */

    $totalJumlahPendapatan =
    $itemsByPenempatanKerja
    ->sum('jumlah_pendapatan');

    $totalJumlahTagihan =
    $itemsByPenempatanKerja
    ->sum('jumlah_tagihan');

    $totalJumlahFee =
    $itemsByPenempatanKerja
    ->sum('jumlah_fee');


    /*
    |--------------------------------------------------------------------------
    | Fallback Jika Items Kosong
    |--------------------------------------------------------------------------
    */

    if ($totalJumlahPendapatan <= 0) { /* |-------------------------------------------------------------------------- |
        Fasilitas Pinjaman |-------------------------------------------------------------------------- */
        $totalJumlahPendapatan=cleanRupiahValue( $invoice['total_pinjaman'] ?? $invoice['jumlah_pendapatan'] ?? 0 ); /*
        |-------------------------------------------------------------------------- | Admin Rate
        |-------------------------------------------------------------------------- */ $biayaAdminRate=cleanRupiahValue(
        $invoice['biaya_admin_rate'] ?? 2.5 ); /*
        |-------------------------------------------------------------------------- | Admin
        |-------------------------------------------------------------------------- */ $biayaAdmin=roundUpToThousand(
        $totalJumlahPendapatan * rateToMultiplier($biayaAdminRate) ); /*
        |-------------------------------------------------------------------------- | Fee
        |-------------------------------------------------------------------------- */ $feeTomiRate=cleanRupiahValue(
        $invoice['fee_tomi'] ?? 0 ); $profesionalFee1Rate=cleanRupiahValue( $invoice['profesional_fee_1'] ??
        $invoice['professional_fee_1'] ?? 0 ); $profesionalFee2Rate=cleanRupiahValue( $invoice['profesional_fee_2'] ??
        $invoice['professional_fee_2'] ?? 0 ); /*
        |-------------------------------------------------------------------------- | Biaya TOMI
        |-------------------------------------------------------------------------- */ $biayaTomi=round(
        $totalJumlahPendapatan * rateToMultiplier($feeTomiRate) ); /*
        |-------------------------------------------------------------------------- | Referral 1
        |-------------------------------------------------------------------------- */ $biayaReferal1=round(
        $totalJumlahPendapatan * rateToMultiplier($profesionalFee1Rate) ); /*
        |-------------------------------------------------------------------------- | Referral 2
        |-------------------------------------------------------------------------- */ $biayaReferal2=round(
        $biayaTomi * rateToMultiplier($profesionalFee2Rate) ); /*
        |-------------------------------------------------------------------------- | JUMLAH TAGIHAN
        |-------------------------------------------------------------------------- */
        $totalJumlahTagihan=round( $totalJumlahPendapatan + $biayaAdmin ); /*
        |-------------------------------------------------------------------------- | Jumlah Fee
        |-------------------------------------------------------------------------- */ $totalJumlahFee=$biayaTomi +
        $biayaReferal1 + $biayaReferal2; /* |--------------------------------------------------------------------------
        | Buat 1 Baris Fallback |-------------------------------------------------------------------------- */
        $itemsByPenempatanKerja=collect([ [ 'penempatan_kerja'=>
        $invoice['penempatan_kerja']
        ?? '-',

        'tanggal_pencairan' =>
        $invoice['tanggal_pencairan']
        ?? $invoice['tanggal_pembayaran']
        ?? $invoice['tanggal_transfer']
        ?? $invoice['tanggal_pengajuan']
        ?? $invoice['tanggal_bukti_transfer']
        ?? null,

        'nama_perusahaan' =>
        $invoice['nama_perusahaan']
        ?? '-',

        'rate' =>
        cleanRupiahValue(
        $invoice['rate']
        ?? 0
        ),

        'asuransi' =>
        cleanRupiahValue(
        $invoice['asuransi']
        ?? 0
        ),

        'provinsi' =>
        $invoice['provinsi']
        ?? '-',

        'biaya_admin_persen' =>
        $biayaAdminRate,

        'fee_tomi' =>
        $feeTomiRate,

        'profesional_fee_1' =>
        $profesionalFee1Rate,

        'profesional_fee_2' =>
        $profesionalFee2Rate,

        'biaya_tomi' =>
        $biayaTomi,

        'biaya_referal_1' =>
        $biayaReferal1,

        'biaya_referal_2' =>
        $biayaReferal2,

        'jumlah_total_biaya' =>
        $totalJumlahFee,

        'ksp' =>
        cleanRupiahValue(
        $invoice['ksp']
        ?? 0
        ),

        'iuran_anggota' =>
        cleanRupiahValue(
        $invoice['iuran_anggota']
        ?? 0
        ),

        'jumlah_pendapatan' =>
        $totalJumlahPendapatan,

        'biaya_admin' =>
        $biayaAdmin,

        'jumlah_tagihan' =>
        $totalJumlahTagihan,

        'jumlah_fee' =>
        $totalJumlahFee,
        ]
        ]);
        }

        $periodeInvoice = salaryInvoicePeriodMonth($itemsByPenempatanKerja, $invoice);

        @endphp

        <!DOCTYPE html>

        <html lang="id">

        <head>

            <meta charset="utf-8">

            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

            <title>
                Invoice TomiGaji -
                {{ $invoice['nama_perusahaan'] ?? '-' }}
            </title>


            <style>
                @page {
                    size: legal landscape;
                    margin: 30px;
                }

                body {
                    font-family: 'Helvetica', 'Arial', sans-serif;
                    font-size: 10px;
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
                    margin-top: 14px;
                }

                table.main-table th {
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    padding: 7px;
                    text-align: left;
                    font-size: 8px;
                    text-transform: uppercase;
                }

                table.main-table td {
                    border: 1px solid #dee2e6;
                    padding: 7px;
                    vertical-align: top;
                    font-size: 8px;
                }

                table.main-table tfoot td {
                    background-color: #f1f3f5;
                    font-weight: bold;
                }

                .total-section {
                    margin-top: 10px;
                    padding: 10px 12px;
                    background-color: #f8f9fa;
                    border-radius: 5px;
                    page-break-inside: avoid;
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
                    margin-top: 6px;
                    font-size: 8.5px;
                    color: #666;
                    line-height: 1.2;
                    page-break-inside: avoid;
                }

                .note-section p {
                    margin: 4px 0;
                }

                .bank-info {
                    margin-top: 2px;
                    padding: 4px 6px;
                    border: 1px dashed #ccc;
                    display: inline-block;
                    font-size: 8.5px;
                    font-weight: bold;
                    line-height: 1.15;
                    page-break-inside: avoid;
                }

                .bank-info table {
                    border-collapse: collapse;
                }

                .bank-info td {
                    padding: 0 3px;
                    line-height: 1.15;
                }

                .footer-section {
                    margin-top: 8px;
                    page-break-inside: avoid;
                }

                .summary-table {
                    width: 350px;
                    border-collapse: collapse;
                }

                .summary-table td {
                    padding: 4px 0;
                }

                .nowrap {
                    white-space: nowrap;
                }

                .text-right {
                    text-align: right;
                }

                .text-center {
                    text-align: center;
                }
            </style>

        </head>


        <body>


            <div class="header clearfix">

                <div class="float-left">

                    <img src="{{ public_path('assets/images/logo.jpg') }}" class="logo" alt="Logo">

                </div>


                <div class="float-right">

                    <h1 class="title">
                        INVOICE TOMIGAJI
                    </h1>

                    <div style="
                text-align: right;
                margin-top: 5px;
            ">

                        <div>

                            <b>No. Invoice:</b>

                            {{ $nomorInvoice }}

                        </div>


                        <div style="margin-top: 3px;">

                            <b>
                                Tanggal Invoice:
                            </b>

                            {{
                            !empty($invoice['created_at'])
                            ? \Carbon\Carbon::parse(
                            $invoice['created_at']
                            )->translatedFormat('d F Y')
                            : '-'
                            }}

                        </div>

                    </div>

                </div>

            </div>



            <div class="info-section clearfix">

                <div class="float-left">

                    <p>

                        <b>
                            Halo, Koperasi Simpan Pinjam Dumi Digital Teknologi
                        </b>

                    </p>

                    <p>

                        Berikut adalah rincian tagihan biaya atas penggunaan fasilitas TomiGaji pada aplikasi platform
                        digital Tomi yang diberikan kepada

                        <b>
                            {{ $invoice['nama_perusahaan'] ?? '-' }}
                        </b>,

                        periode

                        <b>
                            {{ $periodeInvoice }}
                        </b>;

                    </p>

                </div>

            </div>



            <p>

                <b>
                    {{ $invoice['nama_perusahaan'] ?? '-' }}
                </b>

            </p>



            <table class="main-table">

                <thead>

                    <tr>

                        <th style="text-align:center;" width="30">
                            No.
                        </th>


                        <th style="text-align:center;">
                            Tanggal Pencairan
                        </th>


                        <th style="text-align:center;">
                            Nama Perusahaan
                        </th>


                        <th style="text-align:center;">
                            Penempatan Kerja
                        </th>


                        @foreach(
                        $selectedConfigColumns as $column
                        )

                        <th style="text-align:center;">

                            {{
                            $invoiceColumnLabels[
                            $column
                            ]
                            }}

                        </th>

                        @endforeach


                        <th style="
                text-align:center;
                width:120px;
            ">

                            Jumlah Fasilitas
                            <br>
                            Pinjaman TomiGaji

                        </th>


                        <th style="
                text-align:center;
                width:120px;
            ">

                            Jumlah Tagihan
                            <br>
                            Pinjaman TomiGaji

                        </th>


                        @if(
                        in_array(
                        'biaya_tomi',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <th style="
                    text-align:center;
                    width:110px;
                ">

                            Biaya TOMI
                            <br>

                            <strong>
                                (A)
                            </strong>

                        </th>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_1',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <th style="
                    text-align:center;
                    width:110px;
                ">

                            Biaya Referal 1
                            <br>

                            <strong>
                                (B)
                            </strong>

                        </th>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_2',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <th style="
                    text-align:center;
                    width:110px;
                ">

                            Biaya Referal 2
                            <br>

                            <strong>
                                (C)
                            </strong>

                        </th>

                        @endif


                        <th style="
                text-align:center;
                width:120px;
            ">

                            Jumlah Total Biaya

                            <br>

                            <strong>
                                (A + B + C)
                            </strong>

                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse(
                    $itemsByPenempatanKerja as $item
                    )

                    <tr>

                        <td align="center">

                            {{ $loop->iteration }}

                        </td>


                        <td class="nowrap">

                            <b>

                                @php
                                $tanggalPencairan = $item['tanggal_pencairan']
                                ?? $invoice['tanggal_pencairan']
                                ?? $invoice['tanggal_pembayaran']
                                ?? $invoice['tanggal_transfer']
                                ?? $invoice['tanggal_pengajuan']
                                ?? $invoice['tanggal_bukti_transfer']
                                ?? null;
                                @endphp

                                {{
                                !empty($tanggalPencairan) && $tanggalPencairan !== '-'
                                ? \Carbon\Carbon::parse(
                                $tanggalPencairan
                                )->translatedFormat('d F Y')
                                : '-'
                                }}

                            </b>

                        </td>


                        <td>

                            <b>
                                {{
                                $item[
                                'nama_perusahaan'
                                ]
                                ?? $invoice[
                                'nama_perusahaan'
                                ]
                                ?? '-'
                                }}
                            </b>

                        </td>


                        <td align="left">

                            {{
                            $item[
                            'penempatan_kerja'
                            ]
                            ?? '-'
                            }}

                        </td>


                        @foreach(
                        $selectedConfigColumns as $column
                        )

                        @php

                        $valueKey =
                        $column === 'biaya_admin'
                        ? 'biaya_admin_persen'
                        : $column;

                        @endphp


                        <td align="{{
                        $column === 'provinsi'
                            ? 'left'
                            : 'right'
                    }}" class="nowrap">

                            {{
                            formatInvoiceColumnValue(
                            $column,
                            $item[
                            $valueKey
                            ]
                            ?? null
                            )
                            }}

                        </td>

                        @endforeach


                        <td align="right" class="nowrap">

                            Rp {{
                            number_format(
                            $item[
                            'jumlah_pendapatan'
                            ]
                            ?? 0,
                            0,
                            ',',
                            '.'
                            )
                            }}

                        </td>


                        <td align="right" class="nowrap">


                            {{
                            formatRupiahInvoice(
                            $item[
                            'jumlah_tagihan'
                            ]
                            ?? 0
                            )
                            }}


                        </td>


                        @if(
                        in_array(
                        'biaya_tomi',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            {{
                            formatRupiahInvoice(
                            $item[
                            'biaya_tomi'
                            ]
                            ?? 0
                            )
                            }}

                        </td>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_1',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            {{
                            formatRupiahInvoice(
                            $item[
                            'biaya_referal_1'
                            ]
                            ?? 0
                            )
                            }}

                        </td>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_2',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            {{
                            formatRupiahInvoice(
                            $item[
                            'biaya_referal_2'
                            ]
                            ?? 0
                            )
                            }}

                        </td>

                        @endif


                        <td align="right" class="nowrap">

                            {{
                            formatRupiahInvoice(
                            $item[
                            'jumlah_fee'
                            ]
                            ?? 0
                            )
                            }}

                        </td>

                    </tr>


                    @empty


                    <tr>

                        <td colspan="{{
                    7
                    + count(
                        $selectedConfigColumns
                    )
                    + count(
                        $selectedFeeColumns
                    )
                }}" align="center">

                            Tidak ada data

                        </td>

                    </tr>


                    @endforelse


                </tbody>



                <tfoot>

                    <tr>

                        <td colspan="{{
                4
                + count(
                    $selectedConfigColumns
                )
            }}" align="center">

                            <b>
                                TOTAL
                            </b>

                        </td>


                        <td align="right" class="nowrap">

                            <b>

                                Rp {{
                                number_format(
                                $totalJumlahPendapatan
                                ?? 0,
                                0,
                                ',',
                                '.'
                                )
                                }}

                            </b>

                        </td>


                        <td align="right" class="nowrap">

                            <b>

                                {{
                                formatRupiahInvoice(
                                $totalJumlahTagihan
                                ?? 0
                                )
                                }}

                            </b>

                        </td>


                        @if(
                        in_array(
                        'biaya_tomi',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            <b>

                                {{
                                formatRupiahInvoice(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_tomi'
                                )
                                )
                                }}

                            </b>

                        </td>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_1',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            <b>

                                {{
                                formatRupiahInvoice(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_referal_1'
                                )
                                )
                                }}

                            </b>

                        </td>

                        @endif


                        @if(
                        in_array(
                        'biaya_referal_2',
                        $selectedFeeColumns,
                        true
                        )
                        )

                        <td align="right" class="nowrap">

                            <b>

                                {{
                                formatRupiahInvoice(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_referal_2'
                                )
                                )
                                }}

                            </b>

                        </td>

                        @endif


                        <td align="right" class="nowrap">

                            <b>

                                {{
                                formatRupiahInvoice(
                                $totalJumlahFee
                                ?? 0
                                )
                                }}

                            </b>

                        </td>

                    </tr>

                </tfoot>

            </table>



            <div class="clearfix footer-section">


                <div class="float-right total-section" style="width: 350px;">

                    <table class="summary-table">

                        <tr>

                            <td class="total-title">

                                JUMLAH TOTAL BIAYA

                            </td>


                            <td align="right" class="total-amount">

                                {{
                                formatRupiahInvoice(
                                $totalJumlahFee
                                ?? 0
                                )
                                }}

                            </td>

                        </tr>

                    </table>

                </div>



                <div class="float-left note-section" style="width: 400px;">

                    <b>
                        Catatan Pembayaran:
                    </b>


                    @php

                    $tanggalJatuhTempo =
                    !empty(
                    $invoice['created_at']
                    )
                    ? \Carbon\Carbon::parse(
                    $invoice['created_at']
                    )
                    ->addDays(37)
                    ->translatedFormat(
                    'd F Y'
                    )
                    : '-';

                    @endphp


                    <p>

                        Harap segera melakukan pembayaran
                        paling lambat tanggal

                        <b>
                            {{ $tanggalJatuhTempo }}
                        </b>

                        ke rekening berikut:

                    </p>


                    <div class="bank-info">

                        <table>

                            <tr>

                                <td width="70">
                                    Bank
                                </td>

                                <td>
                                    :
                                </td>

                                <td>
                                    <b>
                                        BANK BRI
                                    </b>
                                </td>

                            </tr>


                            <tr>

                                <td width="70">
                                    No. Rekening
                                </td>

                                <td>
                                    :
                                </td>

                                <td>
                                    <b>
                                        042501000000305
                                    </b>
                                </td>

                            </tr>


                            <tr>

                                <td width="70">
                                    Atas Nama
                                </td>

                                <td>
                                    :
                                </td>

                                <td>
                                    <b>
                                        PT TOKO MIKRO INDONESIA
                                    </b>
                                </td>

                            </tr>

                        </table>

                    </div>


                    <p>

                        <i>
                            *Mohon lampirkan bukti transfer saat konfirmasi.
                        </i>

                    </p>

                </div>

            </div>


        </body>

        </html>
