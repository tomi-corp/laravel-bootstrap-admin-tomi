<html>

<head>
    <meta charset="UTF-8">
    <title>Pengingat Jatuh Tempo Pembayaran</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f5f7; font-family:'Lato', Helvetica, Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">

                    <tr>
                        <td bgcolor="#ffffff" align="center"
                            style="padding:40px 20px 20px 20px; border-radius:4px 4px 0px 0px; color:#111111;">
                            <img src="{{ url('assets/images/logo.jpg') }}" alt="TOMI" width="200" height="100"
                                style="display:block; margin-bottom:10px;">
                            <h1 style="font-size:36px; font-weight:400; margin:0;">Pengingat Pembayaran Pinjaman</h1>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#ffffff" align="left"
                            style="padding:20px 30px; color:#555555; font-size:16px; line-height:25px;">
                            <p style="margin:0;">Hai <strong>{{ $data['nama_lengkap'] }}</strong>,</p>
                            <p style="margin-top:10px;">
                                Kami ingin mengingatkan bahwa pembayaran pinjaman <strong>Payroll</strong> Anda akan
                                <strong>jatuh tempo pada tanggal {{ $data['jatuh_tempo'] }}</strong>.
                            </p>
                            <p>
                                Berikut detail pinjaman Anda:
                            </p>
                            <table cellpadding="6" cellspacing="0"
                                style="background-color:#f9fafc; border-radius:6px; width:100%; font-size:15px;">
                                <tr>
                                    <td>ID Pinjaman</td>
                                    <td><strong>{{ $data['no_pinjaman'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Tagihan</td>
                                    <td><strong>Rp {{ number_format($data['jumlah_tagihan'], 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Jatuh Tempo</td>
                                    <td><strong>{{ $data['jatuh_tempo'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Angsuran Ke </td>
                                    <td><strong>{{ $data['angsuranKe'] }} / {{ $data['totalAngsuran'] }}</strong></td>
                                </tr>
                            </table>
                            <p style="margin-top:20px;">
                                Pembayaran pinjaman ini akan secara otomatis dipotong dari <strong>Take Home Pay
                                    (THP)</strong> Anda pada periode penggajian bulan ini.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#ffffff" align="left"
                            style="padding:0 30px 20px 30px; color:#666666; font-size:14px; line-height:25px;">
                            <p>Jika Anda memiliki pertanyaan terkait pemotongan THP atas pinjaman payroll anda, silahkan
                                menghubungi PIC payroll pada divisi HC/GA perusahaan anda.</p>
                            <p style="margin:0;">Terima kasih telah menjadi bagian dari <strong>TOMI</strong>.</p>
                            <p>Salam hangat,<br><b>Tim Support TOMI</b></p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#f4f4f4" align="center" style="padding:10px; color:#aaaaaa; font-size:12px;">
                            © {{ date('Y') }} PT TOKO MIKRO INDONESIA. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
