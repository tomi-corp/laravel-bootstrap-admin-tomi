<!DOCTYPE html>
<html lang="id">
@php
    $logoPath = public_path('assets/images/logo.jpg');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoPath))
        : '';

    $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
    $companyName = strtoupper($company->nama ?? '-');
    $positionLabels = \App\Models\CompanyFeatureReferral::POSITIONS;
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Fee Referal</title>

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

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { border-top: 1px solid #000; border-bottom: 1px solid #000; }
        tbody tr { border-bottom: 1px solid #000; }
        th, td { padding: 5px 4px; vertical-align: middle; font-size: 8.5px; line-height: 1.25; word-wrap: break-word; }
        th { text-align: center; font-weight: bold; }
        tr.total-row { border-top: 1px solid #000; }
        .amount { text-align: right; white-space: nowrap; }
        .number-cell { text-align: center; }
        .referral-table td { border: none; padding: 2px 4px; }
        .referral-table tr { border: none; }

        .summary-box { width: 260px; margin: 12px 0 0 auto; page-break-inside: avoid; }
        .summary-box p { margin: 0 0 4px 0; }
        .summary-box h3 { margin: 0; font-size: 16px; font-weight: bold; }
    </style>
</head>

<body>
    <div class="mt-4">
        @if ($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif

        <div class="mt-3">
            <p class="intro-text">
                Berikut adalah rincian fee referal atas pinjaman TomiDana karyawan <b>{{ $companyName }}</b>
                yang dicairkan pada periode
                <b>{{ $periodStart->format('Y-m-d') }} - {{ $periodStart->copy()->endOfMonth()->format('Y-m-d') }}</b>;
                <br>
                <b>NO. INVOICE : {{ $invoiceNumber }}</b>
            </p>
        </div>

        <div class="mt-3">
            <h6>REFERRAL</h6>
            @if ($referrals->isEmpty())
                <small>Belum ada referral yang ditautkan ke perusahaan ini untuk TomiDana.</small>
            @else
                <table class="referral-table">
                    @foreach ($referrals as $link)
                        <tr>
                            <td style="width: 90px"><b>{{ strtoupper($positionLabels[$link->position] ?? '-') }}</b></td>
                            <td>
                                {{ strtoupper($link->referral?->name ?? '-') }}
                                · {{ strtoupper($link->referral?->bank?->code_bank ?? '-') }}
                                {{ $link->referral?->bank_account_number ?? '-' }}
                                a.n. {{ strtoupper($link->referral?->bank_account_name ?? '-') }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <div class="mt-4">
            <h6>LAMPIRAN PINJAMAN</h6>
            <table>
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>TANGGAL PENCAIRAN</th>
                        <th>KARYAWAN</th>
                        <th>NOMINAL PINJAMAN</th>
                        @foreach ($columns as $label)
                            <th>FEE {{ $label }}</th>
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
            <p><b>TOTAL FEE REFERAL :</b></p>
            <h3>{{ $rupiah($grandTotal) }}</h3>
        </div>

        <div class="clearfix"></div>
    </div>
</body>

</html>
