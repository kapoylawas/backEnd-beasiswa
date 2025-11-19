<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beasiswa Sidoarjo Micro Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #E84C3D;
            --secondary-color: #2C3E50;
            --accent-color: #3498DB;
            --light-color: #ECF0F1;
            --dark-color: #2C3E50;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--primary-color);
        }

        .logo-container {
            margin-bottom: 30px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-icon {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), #ff7b6c);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(232, 76, 61, 0.3);
            margin-bottom: 20px;
            -webkit-animation: spin 8s linear infinite;
            -moz-animation: spin 8s linear infinite;
            animation: spin 8s linear infinite;
        }

        .logo-icon i {
            font-size: 80px;
            color: white;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .tagline {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 500;
        }

        .description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
            max-width: 600px;
        }

        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #d14032;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(232, 76, 61, 0.4);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background: #1a2530;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(44, 62, 80, 0.4);
        }

        .features {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 40px;
            gap: 30px;
            width: 100%;
            max-width: 900px;
        }

        .feature {
            flex: 1;
            min-width: 250px;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .feature-description {
            font-size: 0.95rem;
            color: #666;
        }

        footer {
            margin-top: 50px;
            color: #777;
            font-size: 0.9rem;
            padding: 20px;
        }

        @-moz-keyframes spin {
            100% {
                -moz-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }

            .card {
                padding: 30px 20px;
            }

            .buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }

            .logo-icon {
                width: 150px;
                height: 150px;
            }

            .logo-icon i {
                font-size: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>

            <h1>Beasiswa Sidoarjo Micro Service</h1>
            <p class="tagline">Platform Digital untuk Pengelolaan Beasiswa di Kabupaten Sidoarjo</p>
            <p class="description">Layanan terintegrasi untuk mempermudah proses pendaftaran, seleksi, dan penyaluran
                beasiswa bagi pelajar dan mahasiswa di wilayah Sidoarjo.</p>

            <div class="buttons">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-rocket"></i> Mulai Menggunakan
                </a>
                <a href="https://documenter.getpostman.com/view/5049400/UzQuNQZM#6c99b1c8-3b53-40b8-9c82-eca7ddd99b79"
                    class="btn btn-secondary">
                    <i class="fas fa-book"></i> Lihat Dokumentasi
                </a>
            </div>
        </div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">🚀</div>
                <h3 class="feature-title">Akses Cepat</h3>
                <p class="feature-description">Proses pendaftaran beasiswa yang cepat dan mudah dengan sistem digital
                    terintegrasi.</p>
            </div>

            <div class="feature">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Transparan</h3>
                <p class="feature-description">Sistem seleksi yang terbuka dan transparan dengan kriteria yang jelas.
                </p>
            </div>

            <div class="feature">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">Aman</h3>
                <p class="feature-description">Data pribadi peserta terlindungi dengan sistem keamanan berlapis.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 Beasiswa Sidoarjo Micro Service. Hak Cipta Dilindungi.</p>
    </footer>
</body>

</html>
