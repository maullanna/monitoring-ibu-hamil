<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Dehidrasi Ibu Hamil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .btn-hero {
            background: white;
            color: #667eea;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 10px;
            transition: all 0.3s;
        }
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .feature-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-baby text-primary me-2"></i>
                <strong>Monitoring Ibu Hamil</strong>
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">Login</a>
                <a class="nav-link btn btn-primary text-white px-3" href="{{ route('register') }}">Daftar</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Monitoring Dehidrasi Ibu Hamil</h1>
            <p>Sistem cerdas untuk memantau asupan air minum ibu hamil dengan teknologi IoT</p>
            <div>
                <a href="{{ route('register') }}" class="btn btn-hero">
                    <i class="fas fa-user-plus me-2"></i> Mulai Sekarang
                </a>
                <a href="#features" class="btn btn-hero btn-outline-light">
                    <i class="fas fa-info-circle me-2"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="feature-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">Fitur Utama</h2>
                <p class="lead text-muted">Sistem monitoring yang lengkap dan mudah digunakan</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <h4>Monitoring Dehidrasi</h4>
                        <p class="text-muted">Pantau asupan air minum harian dengan data real-time dari IoT botol minum</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Notifikasi Cerdas</h4>
                        <p class="text-muted">Dapatkan peringatan otomatis ketika asupan air minum kurang dari target</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analisis Data</h4>
                        <p class="text-muted">Lihat tren dan statistik asupan air minum dalam bentuk grafik yang informatif</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h4>QR Code Personal</h4>
                        <p class="text-muted">Setiap user memiliki QR code unik untuk identifikasi dan integrasi IoT</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Akses Mobile</h4>
                        <p class="text-muted">Akses sistem dari mana saja melalui smartphone atau tablet</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Keamanan Data</h4>
                        <p class="text-muted">Data pribadi tersimpan dengan aman dan hanya dapat diakses oleh user yang bersangkutan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-baby me-2"></i>Monitoring Ibu Hamil</h5>
                    <p>Sistem monitoring dehidrasi yang dirancang khusus untuk kesehatan ibu hamil</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2025 Monitoring Ibu Hamil. All rights reserved.</p>
                    <p>Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk kesehatan ibu hamil</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
