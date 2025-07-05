<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #e91e63, #667eea);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                background-position: 200% 0;
            }

            50% {
                background-position: -200% 0;
            }
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            animation: bounceIn 0.8s ease-out;
        }

        .success-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .error-icon {
            background: linear-gradient(135deg, #e91e63, #ad1457);
            color: white;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            animation: slideInUp 0.6s ease-out 0.3s both;
        }

        .success-title {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-title {
            background: linear-gradient(135deg, #e91e63, #ad1457);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            animation: slideInUp 0.6s ease-out 0.5s both;
        }

        .details {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
            animation: slideInUp 0.6s ease-out 0.7s both;
        }

        .error-details {
            background: #fdf2f8;
            border-left-color: #e91e63;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }

        .detail-value {
            color: #666;
            font-size: 14px;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }

            .title {
                font-size: 24px;
            }

            .icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="floating-shapes">
            <div class="shape">✓</div>
            <div class="shape">●</div>
            <div class="shape">▲</div>
        </div>

        <div class="icon success-icon">
            ✓
        </div>

        <h1 class="title success-title">Verifikasi Berhasil!</h1>

        <p class="message">
            Akun Anda telah berhasil diverifikasi. Selamat! Anda sekarang dapat menggunakan semua fitur yang tersedia.
        </p>

        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">Terverifikasi</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Waktu Verifikasi</span>
                <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</body>

</html>
