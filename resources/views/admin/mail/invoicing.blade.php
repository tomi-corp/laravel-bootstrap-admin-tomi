<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
        /* Basic styles, these will be inlined by a pre-processor like Juice */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: 'Poppins', Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        td,
        th {
            padding: 0;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        /* Add specific styles for your layout, these will be inlined */
        .container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .logo img {
            max-height: 40px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .invoice-info p {
            margin: 0;
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .total-angsuran {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 600;
        }

        .total-angsuran strong {
            font-size: 24px;
            color: #007bff;
            margin-left: 10px;
        }

        .noted-section {
            font-size: 13px;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .noted-section strong {
            color: #333;
        }

        /* Fallback for email clients that don't support media queries well */
        @media only screen and (max-width: 620px) {
            .container {
                padding: 15px !important;
            }

            .header-title {
                font-size: 20px !important;
            }

            .data-table th,
            .data-table td {
                padding: 8px 10px !important;
                font-size: 13px !important;
            }

            .total-angsuran strong {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Poppins', Arial, sans-serif; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <center>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
            style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <tr>
                <td style="padding: 20px 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" class="container"
                        style="background-color: #ffffff; margin: 0 auto; padding: 20px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 600px; width: 100%;">
                        <tr>
                            <td style="padding: 20px 20px;">
                                <img src="{{ url('assets/images/ksp.png') }}" alt="Tomi" width="200"
                                    height="100" data-image-whitelisted="" class="CToWUd" data-bit="iit">
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 20px 20px;">
                                <p>Hallo {{ $data['invoice']['perusahaan']['nama'] }},
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 20px 20px;">
                                Semoga hari Anda menyenangkan!
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 0px 20px;">
                                Kami harap Anda menikmati layanan kami. Berikut adalah invoice terbaru Anda
                                untuk
                                layanan yang telah diberikan:
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 20px 20px;">
                                Untuk Periode:
                                {{ Carbon::parse($data['invoice']['perusahaan']['jatuh_tempo'])->subDays(5)->subMonth(1)->format('Y-m-d') }}
                                -
                                {{ Carbon::parse($data['invoice']['perusahaan']['jatuh_tempo'])->subDays(5)->format('Y-m-d') }}
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 0px 20px;">
                                Detail lengkapnya bisa Anda lihat pada link invoice ini <a
                                    href="{{ $data['link'] }}">Invoicing List</a>.
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 0px 20px;">
                                Pembayaran dapat
                                dilakukan
                                sebelum
                                {{ Carbon::parse($data['invoice']['perusahaan']['jatuh_tempo'])->addDays(3)->format('Y-m-d') }}
                                (Payment date + H+3).
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 20px 20px;">
                                Anda bisa melakukan pembayaran via transfer ke:
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 0px 20px;">
                                Tim PT.KSP DDT
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 0px 20px;">
                                Bank: {{ $data['rekening']['bank']['code_bank'] }}
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 0px 20px;">
                                Atas Nama:
                                {{ $data['rekening']['atas_nama'] }}
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 0px 20px;">
                                No. Rekening:
                                {{ $data['rekening']['nomor_rekening'] }}
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 0px 20px;">
                                Jumlah yang Harus Dibayar: Rp
                                {{ number_format($data['invoice']['perusahaan']['pinjamanBerjalan']->sum('total_angsuran_perbulan')) }}
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 20px 20px;">
                                Ada pertanyaan? Jangan sungkan untuk membalas email ini atau hubungi kami di <a href="https://wa.me/6281111181888">CS Tomi</a>. Kami siap membantu!
                            </td>
                        <tr>

                        <tr>
                            <td style="padding: 20px 20px;">
                                Terima kasih banyak!
                            </td>
                        <tr>
                        <tr>
                            <td style="padding: 20px 20px;">
                                Salam
                            </td>
                        <tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
