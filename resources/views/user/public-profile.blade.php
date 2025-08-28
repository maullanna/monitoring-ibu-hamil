<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil {{ $user->nama_lengkap }} - Monitoring Ibu Hamil</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
            position: relative;
            z-index: 1;
        }
        
        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            position: relative;
            z-index: 1;
        }
        
        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .profile-role {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }
        
        .qr-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .qr-info i {
            font-size: 2rem;
            color: #4e73df;
            margin-bottom: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: rgba(255,255,255,0.8);
        }
        
        .footer a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: white;
        }
        
        @media (max-width: 576px) {
            .profile-card {
                margin: 10px;
                border-radius: 15px;
            }
            
            .profile-header {
                padding: 30px 20px;
            }
            
            .profile-body {
                padding: 20px;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                @if($pasien->foto)
                    <img src="{{ asset('storage/' . $pasien->foto) }}" alt="Foto Profil" class="profile-photo">
                @else
                    <div class="profile-avatar">
                        {{ substr($user->nama_lengkap, 0, 1) }}
                    </div>
                @endif
                <div class="profile-name">{{ $user->nama_lengkap }}</div>
                <div class="profile-role">Ibu Hamil</div>
            </div>
            
            <!-- Profile Body -->
            <div class="profile-body">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value">{{ $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('d/m/Y') : 'Belum diisi' }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $pasien->alamat ?: 'Belum diisi' }}</div>
                    </div>
                </div>
                
                                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-baby"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Usia Kehamilan</div>
                            <div class="info-value">{{ $pasien->usia_kehamilan_minggu ? $pasien->usia_kehamilan_minggu . ' minggu' : 'Belum diisi' }}</div>
                        </div>
                    </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Target Minum Harian</div>
                        <div class="info-value">{{ $pasien->target_minum_ml }} ml</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Tanggal Registrasi</div>
                        <div class="info-value">{{ $pasien->tanggal_dibuat->format('d/m/Y') }}</div>
                    </div>
                </div>
                
                <!-- QR Code Info -->
                <div class="qr-info">
                    <i class="fas fa-qrcode"></i>
                    <h6>Profile ini diakses via QR Code</h6>
                    <p class="text-muted mb-0">Scan QR code untuk melihat profil lengkap</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Monitoring Ibu Hamil. Dibuat dengan ❤️ untuk kesehatan ibu dan bayi.</p>
            <p><small>Generated: {{ now()->format('d/m/Y H:i:s') }}</small></p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
