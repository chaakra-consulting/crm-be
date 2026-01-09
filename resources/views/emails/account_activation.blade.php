<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Akun</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f7fa; padding:20px">

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" style="background:#ffffff; padding:30px; border-radius:8px">

                    <tr>
                        <td>
                            <h2 style="margin-top:0">Halo {{ $user->name }}</h2>

                            <p>
                                Anda telah terdaftar di sistem <strong>Chaakra CRM</strong>.
                            </p>

                            <p>
                                Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda:
                            </p>

                            <!-- BUTTON -->
                            <table cellspacing="0" cellpadding="0" style="margin:30px 0">
                                <tr>
                                    <td align="center">
                                        <a
                                            href="{{ $activationUrl }}"
                                            target="_blank"
                                            style="
                                                background:#2563eb;
                                                color:#ffffff;
                                                padding:14px 28px;
                                                text-decoration:none;
                                                border-radius:6px;
                                                font-weight:bold;
                                                display:inline-block;
                                            "
                                        >
                                            Aktifkan Akun
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px; color:#555">
                                Jika tombol tidak berfungsi, salin dan buka link berikut di browser:
                            </p>

                            <p style="font-size:13px; word-break:break-all">
                                {{ $activationUrl }}
                            </p>

                            <br>

                            <p>
                                Salam,<br>
                                <strong>Chaakra Consulting</strong>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
