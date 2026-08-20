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
    function formatPercentValue($value): string { $value=cleanRupiahValue($value); $displayValue=$value <=1 ? ($value *
    100) : $value; if (floor($displayValue)==$displayValue) { return number_format($displayValue, 0, ',' , '.' ) . '%' ;
    } return number_format($displayValue, 2, ',' , '.' ) . '%' ; } function firstFilledValue($rows, string $key,
    $default='-' ) { foreach ($rows as $row) { $value=$row[$key] ?? null; if ($value !==null && $value !=='' ) { return
    $value; } } return $default; } function formatInvoiceColumnValue(string $column, $value): string { if
    ($column==='provinsi' ) { return ( $value===null || $value==='' || $value==='0' ) ? '-' : (string) $value; } if
    ($column==='iuran_anggota' ) { return 'Rp ' . number_format( cleanRupiahValue($value), 0, ',' , '.' ); } return
    formatPercentValue($value); } /* |-------------------------------------------------------------------------- | Data
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
    | jumlah_pendapatan di sini hanya diperlakukan sebagai
    | JUMLAH FASILITAS PINJAMAN.
    |
    | Tidak menghitung jumlah_tagihan per item.
    |
    | Jumlah tagihan akan dihitung SEKALI setelah grouping
    | berdasarkan penempatan kerja.
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
    | Simpan Data
    |--------------------------------------------------------------------------
    */

    $item['penempatan_kerja'] =
    $item['penempatan_kerja']
    ?? '-';

    $item['jumlah_pendapatan'] =
    $jumlahPendapatan;

    $item['biaya_admin_persen'] =
    $biayaAdminRate;

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
    | PERBAIKAN UTAMA ADA DI SINI.
    |
    | Jangan:
    |
    | $group->sum('jumlah_tagihan')
    |
    | karena bisa menyebabkan tagihan yang sudah dihitung sebelumnya
    | dijumlahkan kembali.
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

    $jumlahTagihan = roundUpToThousand(
    $jumlahPendapatan
    + $biayaAdmin
    );


    /*
    |--------------------------------------------------------------------------
    | Biaya TOMI
    |--------------------------------------------------------------------------
    */

    $biayaTomi = roundUpToThousand(
    $jumlahPendapatan
    * rateToMultiplier($feeTomiRate)
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 1
    |--------------------------------------------------------------------------
    */

    $biayaReferal1 = roundUpToThousand(
    $jumlahPendapatan
    * rateToMultiplier($profesionalFee1Rate)
    );


    /*
    |--------------------------------------------------------------------------
    | Referral 2
    |--------------------------------------------------------------------------
    */

    $biayaReferal2 = roundUpToThousand(
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
        |-------------------------------------------------------------------------- */ $biayaTomi=roundUpToThousand(
        $totalJumlahPendapatan * rateToMultiplier($feeTomiRate) ); /*
        |-------------------------------------------------------------------------- | Referral 1
        |-------------------------------------------------------------------------- */ $biayaReferal1=roundUpToThousand(
        $totalJumlahPendapatan * rateToMultiplier($profesionalFee1Rate) ); /*
        |-------------------------------------------------------------------------- | Referral 2
        |-------------------------------------------------------------------------- */ $biayaReferal2=roundUpToThousand(
        $biayaTomi * rateToMultiplier($profesionalFee2Rate) ); /*
        |-------------------------------------------------------------------------- | JUMLAH TAGIHAN
        |-------------------------------------------------------------------------- */
        $totalJumlahTagihan=roundUpToThousand( $totalJumlahPendapatan + $biayaAdmin ); /*
        |-------------------------------------------------------------------------- | Jumlah Fee
        |-------------------------------------------------------------------------- */ $totalJumlahFee=$biayaTomi +
        $biayaReferal1 + $biayaReferal2; /* |--------------------------------------------------------------------------
        | Buat 1 Baris Fallback |-------------------------------------------------------------------------- */
        $itemsByPenempatanKerja=collect([ [ 'penempatan_kerja'=>
        $invoice['penempatan_kerja']
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
                    margin-top: 20px;
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

                        Berikut rincian tagihan biaya TomiGaji pada perusahaan

                        <b>
                            {{ $invoice['nama_perusahaan'] ?? '-' }}
                        </b>

                        yang telah ditransfer pada tanggal

                        <b>

                            {{
                            !empty($invoice['tanggal_bukti_transfer'])
                            ? \Carbon\Carbon::parse(
                            $invoice['tanggal_bukti_transfer']
                            )->translatedFormat('d F Y')
                            : '-'
                            }}

                        </b>

                        oleh Koperasi Simpan Pinjam Dumi Digital Teknologi,
                        sebagai berikut:

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

                                {{
                                !empty(
                                $invoice[
                                'tanggal_bukti_transfer'
                                ]
                                )
                                ? \Carbon\Carbon::parse(
                                $invoice[
                                'tanggal_bukti_transfer'
                                ]
                                )->translatedFormat('d F Y')
                                : '-'
                                }}

                            </b>

                        </td>


                        <td>

                            <b>
                                {{
                                $invoice[
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


                            Rp {{
                            number_format(
                            $item[
                            'jumlah_tagihan'
                            ]
                            ?? 0,
                            0,
                            ',',
                            '.'
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

                            Rp {{
                            number_format(
                            round(
                            $item[
                            'biaya_tomi'
                            ]
                            ?? 0
                            ),
                            0,
                            ',',
                            '.'
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

                            Rp {{
                            number_format(
                            round(
                            $item[
                            'biaya_referal_1'
                            ]
                            ?? 0
                            ),
                            0,
                            ',',
                            '.'
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

                            Rp {{
                            number_format(
                            round(
                            $item[
                            'biaya_referal_2'
                            ]
                            ?? 0
                            ),
                            0,
                            ',',
                            '.'
                            )
                            }}

                        </td>

                        @endif


                        <td align="right" class="nowrap">

                            Rp {{
                            number_format(
                            round(
                            $item[
                            'jumlah_fee'
                            ]
                            ?? 0
                            ),
                            0,
                            ',',
                            '.'
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

                                Rp {{
                                number_format(
                                $totalJumlahTagihan
                                ?? 0,
                                0,
                                ',',
                                '.'
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

                                Rp {{
                                number_format(
                                round(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_tomi'
                                )
                                ),
                                0,
                                ',',
                                '.'
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

                                Rp {{
                                number_format(
                                round(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_referal_1'
                                )
                                ),
                                0,
                                ',',
                                '.'
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

                                Rp {{
                                number_format(
                                round(
                                $itemsByPenempatanKerja
                                ->sum(
                                'biaya_referal_2'
                                )
                                ),
                                0,
                                ',',
                                '.'
                                )
                                }}

                            </b>

                        </td>

                        @endif


                        <td align="right" class="nowrap">

                            <b>

                                Rp {{
                                number_format(
                                round(
                                $totalJumlahFee
                                ?? 0
                                ),
                                0,
                                ',',
                                '.'
                                )
                                }}

                            </b>

                        </td>

                    </tr>

                </tfoot>

            </table>



            <div class="clearfix">


                <div class="float-right total-section" style="width: 350px;">

                    <table class="summary-table">

                        <tr>

                            <td class="total-title">

                                JUMLAH TOTAL BIAYA

                            </td>


                            <td align="right" class="total-amount">

                                Rp {{
                                number_format(
                                round(
                                $totalJumlahFee
                                ?? 0
                                ),
                                0,
                                ',',
                                '.'
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


                    <div class="bank-info total-title">

                        <table>

                            <tr>

                                <td width="100">
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

                                <td width="100">
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

                                <td width="100">
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