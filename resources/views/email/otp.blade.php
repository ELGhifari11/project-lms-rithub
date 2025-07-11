<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Kode Verifikasi OTP</title>

    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #374151;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .header {
            background-color: #4f46e5;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .content {
            padding: 40px 20px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .otp-container {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
            font-family: 'Courier New', monospace;
            letter-spacing: 8px;
            margin: 10px 0;
            display: inline-block;
            background-color: #ffffff;
            padding: 15px 25px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .validity {
            font-size: 14px;
            color: #ef4444;
            font-weight: 500;
            margin-top: 15px;
        }

        .instructions {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }

        .instructions h3 {
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .instructions p {
            color: #1e40af;
            font-size: 14px;
            margin: 5px 0;
        }

        .security-note {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .security-note h4 {
            color: #dc2626;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .security-note p {
            color: #7f1d1d;
            font-size: 14px;
            margin: 5px 0;
        }

        .footer {
            background-color: #f9fafb;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }

        .app-name {
            color: #4f46e5;
            font-weight: 600;
        }

        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }

            .header {
                padding: 20px 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content {
                padding: 30px 15px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
                padding: 12px 20px;
            }

            .greeting {
                font-size: 16px;
            }

            .message {
                font-size: 15px;
            }
        }

        @media only screen and (max-width: 480px) {
            .otp-code {
                font-size: 24px;
                letter-spacing: 2px;
                padding: 10px 15px;
            }

            .otp-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Laravel') }}</h1>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="greeting">
                Halo!
            </div>

            <div class="message">
                Anda telah meminta kode verifikasi untuk mengakses akun Anda.
                Gunakan kode OTP di bawah ini untuk menyelesaikan proses verifikasi.
            </div>

            <!-- OTP Code Section -->
            <div class="otp-container">
                <div class="otp-label">
                    Kode Verifikasi Anda
                </div>
                <div class="otp-code">
                    {{ $otp }}
                </div>
                <div class="validity">
                    Berlaku selama 10 menit
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>Cara Menggunakan:</h3>
                <p>1. Masukkan kode di atas pada halaman verifikasi</p>
                <p>2. Kode hanya berlaku untuk satu kali penggunaan</p>
                <p>3. Jika kode kedaluwarsa, silakan minta kode baru</p>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <h4>⚠️ Penting untuk Keamanan</h4>
                <p>• Jangan bagikan kode ini kepada siapa pun</p>
                <p>• Tim {{ config('app.name', 'Laravel') }} tidak akan pernah meminta kode OTP melalui telepon atau email</p>
                <p>• Jika Anda tidak meminta kode ini, abaikan email ini atau hubungi dukungan pelanggan</p>
            </div>

            <div class="message">
                Jika Anda mengalami kesulitan, jangan ragu untuk menghubungi tim dukungan kami.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Email ini dikirim oleh <span class="app-name">{{ config('app.name', 'Laravel') }}</span>
            </p>
            <p>
                © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Semua hak dilindungi.
            </p>
            <p style="margin-top: 15px;">
                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
