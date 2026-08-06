<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Instruksi Reset Password - Beasiswa Sidoarjo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #1e293b;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 40px 0;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background-color: #059669;
            padding: 30px 40px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .email-body {
            padding: 36px 40px;
        }
        .greeting {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .info-text {
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 24px;
        }
        .password-box {
            background-color: #ecfdf5;
            border: 2.5px solid #059669;
            border-radius: 12px;
            padding: 24px 20px;
            text-align: center;
            margin-bottom: 24px;
        }
        .password-label {
            font-size: 13px;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .password-code {
            display: inline-block;
            background-color: #ffffff;
            border: 2px solid #047857;
            border-radius: 8px;
            padding: 10px 24px;
            font-family: 'Consolas', 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 900;
            color: #064e3b;
            letter-spacing: 4px;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .btn-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-login {
            display: inline-block;
            background-color: #059669;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Header Logo & Title -->
            <div class="email-header">
                <h1>Beasiswa Pemkab Sidoarjo</h1>
                <p>Pemerintah Kabupaten Sidoarjo</p>
            </div>

            <!-- Email Body -->
            <div class="email-body">
                <div class="greeting">Halo, Yth. Pemohon Beasiswa</div>
                <div class="info-text">
                    Permintaan untuk melakukan reset kata sandi (password) akun Anda pada portal <strong>Beasiswa Sidoarjo</strong> telah berhasil diproses. Berikut adalah password baru sementara milik Anda:
                </div>

                <!-- Password Box -->
                <div class="password-box">
                    <div class="password-label">🔑 PASSWORD BARU SEMENTARA ANDA</div>
                    <div class="password-code">
                        <strong style="font-weight: 900; font-size: 32px; color: #064e3b; letter-spacing: 4px;">{{ $newPassword }}</strong>
                    </div>
                </div>

                <!-- Notice / Security Warning -->
                <div className="warning-box">
                    <strong>Penting Demi Keamanan:</strong> Mohon untuk segera login dan mengganti kata sandi ini dengan password pribadi Anda melalui menu Pengaturan Profil setelah masuk.
                </div>

                <!-- CTA Button -->
                <div className="btn-container">
                    <a href="http://localhost:5173/login" className="btn-login" target="_blank">
                        Login Ke Akun Beasiswa &rarr;
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div className="email-footer">
                <strong>Portal Resmi Beasiswa Pemerintah Kabupaten Sidoarjo</strong><br>
                Diskominfo Kabupaten Sidoarjo &bull; Jl. Jenderal Sudirman No. 50, Sidoarjo<br>
                <span style="font-size: 11px; opacity: 0.8;">Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</span>
            </div>
        </div>
    </div>
</body>
</html>
