<!DOCTYPE html>
<html lang="id">
@php
    use Carbon\Carbon;

    $logoPath = public_path('assets/images/logo.jpg');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoPath))
        : '';

    $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
    $periodStart = Carbon::parse($invoice->period)->firstOfMonth()->format('Y-m-d');
    $periodEnd = Carbon::parse($invoice->period)->endOfMonth()->format('Y-m-d');
    $lenderName = strtoupper($invoice->lender->nama ?? '-');
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Biaya Aplikasi</title>

    <style>
        @page { margin: 14px 16px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
        p { margin: 0 0 6px 0; line-height: 1.35; }
        h6 { margin: 0 0 4px 0; font-size: 10.5px; font-weight: bold; }
        small { font-size: 8.5px; }
        .logo { height: 34px; }
        .mt-3 { margin-top: 9px; }
        .mt-4 { margin-top: 12px; }
        .pt-5 { padding-top: 18px; }
        .clearfix { width: 100%; clear: both; }
        .intro-text { font-size: 9.5px; }
        .section-subtitle { margin: 0 0 6px 0; font-size: 9.5px; font-weight: normal; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { border-top: 1px solid #000; border-bottom: 1px solid #000; }
        tbody tr { border-bottom: 1px solid #000; }
        th, td { padding: 5px 3px; vertical-align: middle; font-size: 8px; line-height: 1.25; word-wrap: break-word; }
        th { text-align: center; font-weight: bold; }
        tr.total-row { border-top: 1px solid #000; }
        .amount { text-align: right; white-space: nowrap; }
        .number-cell { text-align: center; }

        .summary-box { width: 260px; float: right; margin-top: 12px; }
        .summary-box p { margin: 0 0 4px 0; }
        .summary-box h3 { margin: 0; font-size: 16px; font-weight: bold; }
        .note-box { width: 55%; margin-top: 18px; }
    </style>
</head>

<body>
    <div class="mt-4">
        @if ($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif

        <div class="mt-3">
            <p><b>HALLO, {{ $lenderName }}</b></p>
            <p class="intro-text">
                Berikut adalah rincian tagihan biaya atas penggunaan fasilitas TomiDana pada aplikasi platform
                digital Tomi yang diberikan kepada <b>{{ $lenderName }}</b>, periode
                <b>{{ $periodStart }} - {{ $periodEnd }}</b>;
                <br>
                <b>NO. INVOICE : {{ strtoupper($invoice->no_invoice ?? '-') }}</b>
            </p>
        </div>

        <div class="mt-4">
            <h6>LAMPIRAN PINJAMAN</h6>
            <h6 class="section-subtitle">ID LENDER : <b>{{ strtoupper($invoice->lender->id_code_lender ?? '-') }}</b></h6>

            <table>
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>TANGGAL PINJAMAN</th>
                        <th>NIP</th>
                        <th>NOMINAL PINJAMAN</th>
                        @foreach ($columns as $label)
                            <th>{{ $label }}</th>
                        @endforeach
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        <tr>
                            <td class="number-cell">{{ $index + 1 }}</td>
                            <td>
                                <b>{{ $row['tanggal'] }}</b><br>
                                {{ strtoupper($row['id_pinjaman'] ?? '-') }}
                            </td>
                            <td>
                                <b>{{ strtoupper($row['nama_karyawan']) }}</b><br>
                                <small>{{ strtoupper($row['perusahaan']) }}</small><br>
                                {{ $row['nip'] }}
                            </td>
                            <td class="amount">{{ $rupiah($row['nominal']) }}</td>
                            @foreach (array_keys($columns) as $key)
                                <td class="amount">{{ $rupiah($row['fees'][$key] ?? 0) }}</td>
                            @endforeach
                            <td class="amount">{{ $rupiah($row['subtotal']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 5 }}" class="number-cell">TIDAK ADA PINJAMAN PADA PERIODE INI</td>
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td colspan="4" class="number-cell"><b>TOTAL</b></td>
                        @foreach (array_keys($columns) as $key)
                            <td class="amount"><b>{{ $rupiah($totals[$key] ?? 0) }}</b></td>
                        @endforeach
                        <td class="amount"><b>{{ $rupiah($grandTotal) }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="summary-box">
            <p><b>TOTAL :</b></p>
            <h3>{{ $rupiah($grandTotal) }}</h3>
        </div>

        <div class="clearfix"></div>

        <div class="note-box pt-5">
            <h6>NOTED:</h6>
            <small>
                Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal 5 ke rekening bank berikut:
                <br>
                BANK : {{ strtoupper($rekening->bank->code_bank ?? '-') }}
                <br>
                ATAS NAMA : {{ strtoupper($rekening->atas_nama ?? '-') }}
                <br>
                NO REKENING : {{ $rekening->nomor_rekening ?? '-' }}
            </small>
        </div>
    </div>
</body>

</html>
