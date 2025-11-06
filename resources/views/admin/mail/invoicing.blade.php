<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
@php
    use Carbon\Carbon;
    $startDate = Carbon::parse($data['invoice']['period']);
    $dueDate = Carbon::parse($data['invoice']['period'])->addDays(3);
    $total = $data['invoice']['perusahaan']['pinjamanBerjalan']->sum('total_angsuran_perbulan');
@endphp

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice Bulanan - {{ $data['invoice']['perusahaan']['nama'] }}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #f6f8fa;
            font-family: 'Poppins', Arial, sans-serif;
            color: #333333;
            line-height: 1.5;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
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
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .email-wrapper {
            padding: 20px 10px;
        }

        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .content {
            padding: 30px 30px;
            /* Padding utama diperbesar */
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            max-height: 60px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .intro {
            font-size: 15px;
            color: #555555;
            margin-bottom: 22px;
        }

        .highlight-box {
            background-color: #f8fafc;
            border-left: 4px solid #007bff;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 4px;
        }

        .payment-details {
            font-size: 15px;
            margin: 28px 0;
        }

        .payment-details p {
            margin: 10px 0;
        }

        .amount {
            font-size: 20px;
            font-weight: 700;
            color: #d32f2f;
            margin: 18px 0;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff !important;
            padding: 11px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none !important;
            margin: 12px 0 18px;
        }

        .footer-note {
            font-size: 13px;
            color: #777777;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px solid #eeeeee;
            line-height: 1.6;
        }

        @media only screen and (max-width: 620px) {
            .content {
                padding: 24px 20px !important;
            }

            .highlight-box,
            .payment-details {
                padding: 14px 16px !important;
                margin: 20px 0 !important;
            }

            .button {
                padding: 10px 20px !important;
                margin: 12px 0 !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <table class="container" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content">
                    <div class="logo">
                        <img src="{{ $message->embed(public_path('assets/images/logo.jpg')) }}" alt="PT. TOMI"
                            width="200" />
                    </div>

                    <p class="greeting">Halo {{ $data['invoice']['perusahaan']['nama'] }},</p>

                    <p class="intro">
                        Terima kasih telah menggunakan layanan kami. Berikut adalah invoice bulanan Anda untuk periode:
                    </p>

                    <div class="highlight-box">
                        <p><strong>Periode Tagihan:</strong><br />
                            {{ $startDate->format('F Y') }} </p>
                    </div>

                    <p class="intro">
                        Silakan akses detail lengkap invoice melalui tautan berikut:
                    </p>

                    <a href="{{ $data['link'] }}" class="button">Lihat Invoice</a>

                    <div class="payment-details">
                        <p><strong>Batas Waktu Pembayaran maksimum:</strong><br />
                            {{ $dueDate->format('d F Y') }} </p>

                        <p><strong>Rekening Pembayaran kepada pihak lender:</strong></p>
                        <div class="logo">
                            <img src="{{ $message->embed(public_path('assets/images/' . $data['invoice']['perusahaan']['lender']['logo'])) }}"
                                alt="PT. TOMI" width="100" />
                        </div>
                        <p>
                            Bank: <strong>{{ $data['rekening']['bank']['code_bank'] }}</strong><br />
                            Atas Nama: <strong>{{ $data['rekening']['nama_rekening'] }}</strong><br />
                            Nomor Rekening: <strong>{{ $data['rekening']['no_rekening'] }}</strong>
                        </p>

                        <div class="amount">
                            Total Tagihan: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <p class="intro">
                        Untuk pertanyaan atau bantuan lebih lanjut, jangan ragu menghubungi kami melalui:
                        <br>
                        <a href="https://wa.me/6281111181888">Customer Service – Tomi (WhatsApp)</a>
                    </p>

                    <div class="footer-note">
                        <p>Terima kasih atas kepercayaan Anda kepada PT. TOMI. Kami senantiasa berkomitmen memberikan
                            layanan terbaik.</p>
                        <p>Salam hormat,<br /><strong>Tim PT. TOMI</strong></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
