<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password Lender</title>
</head>
<body style="margin:0;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;">
                            <h1 style="margin:0;font-size:24px;line-height:1.3;">Reset Password Lender</h1>
                            <p style="margin:8px 0 0;font-size:14px;line-height:1.6;">TOMI Lender Portal</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.7;">
                                Kami menerima permintaan reset password untuk akun lender Anda.
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.7;">
                                Klik tombol berikut untuk membuat password baru. Link ini berlaku selama 15 menit.
                            </p>
                            <p style="margin:0 0 28px;text-align:center;">
                                <a href="{{ route('lender.reset', $data['token']) }}" style="display:inline-block;border-radius:6px;padding:12px 22px;text-decoration:none;font-size:14px;font-weight:700;">
                                    Reset Password
                                </a>
                            </p>
                            <p style="margin:0 0 16px;font-size:13px;line-height:1.7;">
                                Jika tombol tidak bisa dibuka, salin link berikut ke browser:
                            </p>
                            <p style="margin:0 0 24px;font-size:12px;line-height:1.6;word-break:break-all;">
                                {{ route('lender.reset', $data['token']) }}
                            </p>
                            <p style="margin:0;font-size:13px;line-height:1.7;">
                                Abaikan email ini jika Anda tidak meminta reset password.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;font-size:12px;line-height:1.6;">
                            Hormat kami,<br>
                            <strong>PT TOMI</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
