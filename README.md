# Monitoring Ibu Hamil - Sistem Monitoring Dehidrasi dengan QR Code

Sistem monitoring kesehatan ibu hamil yang terintegrasi dengan IoT untuk memantau asupan air minum harian dengan fitur QR Code untuk identifikasi cepat.

## 🚀 Fitur Utama

### 👥 User Management
- **Registrasi Ibu Hamil**: Sistem registrasi dengan validasi data
- **Login/Logout**: Autentikasi aman dengan role-based access
- **Profil Lengkap**: Data pribadi, foto profil, dan informasi kehamilan

### 📱 Monitoring Dehidrasi
- **Input Data Minum**: Pencatatan asupan air minum harian
- **Grafik Real-time**: Visualisasi data 30 hari terakhir dari database IoT
- **Target & Pencapaian**: Monitoring target minum harian dengan progress bar
- **Riwayat Monitoring**: Data historis dengan pagination

### 🔍 QR Code System
- **QR Code Profil**: Berisi informasi lengkap ibu hamil
- **Data Terenkripsi**: Informasi profil dalam format JSON yang mudah di-scan
- **Download & Print**: Fitur download dan print QR code
- **Auto-refresh**: Data otomatis ter-update setiap 5 menit

### 🏥 Admin Panel
- **Dashboard Admin**: Overview sistem monitoring
- **Manajemen Pasien**: CRUD data pasien ibu hamil
- **Monitoring Data**: Lihat semua data monitoring
- **Notifikasi**: Sistem notifikasi untuk admin
- **Backup Data**: Backup dan restore database
- **Pengaturan Aplikasi**: Konfigurasi sistem

### 🌐 IoT Integration
- **API Endpoints**: REST API untuk integrasi IoT device
- **Real-time Data**: Update data monitoring secara real-time
- **Device Management**: Manajemen device IoT per pasien

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Chart.js
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **File Storage**: Laravel Storage
- **Authentication**: Laravel Sanctum

## 📋 Requirements

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM
- Laragon/XAMPP (untuk development)

## 🚀 Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd monitoring-ibu-hamil
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
```bash
# Edit .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring-ibu-hamil
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Migration & Seeding
```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage Setup
```bash
php artisan storage:link
```

### 7. Build Assets
```bash
npm run build
```

### 8. Run Application
```bash
php artisan serve
```

## 🔐 Default Accounts

### Admin
- **Email**: admin@example.com
- **Password**: password

### User (Ibu Hamil)
- **Email**: user@example.com
- **Password**: password

## 📱 QR Code System

### Struktur Data QR Code
QR Code berisi informasi profil dalam format JSON:

```json
{
    "nama_lengkap": "Nama Ibu Hamil",
    "email": "email@example.com",
    "nik": "1234567890123456",
    "tanggal_lahir": "01/01/1990",
    "alamat": "Alamat lengkap",
    "usia_kehamilan": "20 minggu",
    "target_minum": "2000 ml",
    "tanggal_registrasi": "26/08/2025",
    "qr_generated_at": "26/08/2025 13:30:00"
}
```

### Cara Penggunaan QR Code
1. **Lihat QR Code**: Menu "QR Code Profil" di sidebar
2. **Download**: Tombol download untuk simpan QR code
3. **Print**: Fitur print untuk cetak QR code
4. **Scan**: Gunakan aplikasi QR code scanner untuk baca data

## 🌐 API Endpoints

### IoT Integration
```
POST /api/iot/monitoring
GET  /api/iot/monitoring/{pasien_id}/chart
```

### User Monitoring
```
GET  /user/monitoring/chart-data
GET  /user/qr-code/generate
GET  /user/qr-code/download
```

## 📊 Database Structure

### Tabel Utama
- **users**: Data user (admin & ibu hamil)
- **pasien**: Data lengkap ibu hamil
- **monitoring_dehidrasi**: Data monitoring harian
- **notifikasi**: Sistem notifikasi
- **pengaturan_aplikasi**: Konfigurasi sistem

## 🔧 Customization

### Menambah Field Baru
1. Buat migration untuk field baru
2. Update model dan controller
3. Update view dan form
4. Update QR code data structure

### Mengubah Target Minum
1. Edit field `target_minum_ml` di tabel pasien
2. Update logic di MonitoringController
3. Update view progress bar

## 🚨 Troubleshooting

### QR Code Tidak Muncul
- Pastikan package `simple-qrcode` terinstall
- Check route `user.qr-code.generate`
- Verify storage link sudah dibuat

### Foto Tidak Upload
- Check folder permissions `storage/app/public`
- Verify `storage:link` sudah dijalankan
- Check file size limit (max 2MB)

### Grafik Tidak Update
- Check API endpoint `/user/monitoring/chart-data`
- Verify data monitoring ada di database
- Check JavaScript console untuk error

## 📝 License

Project ini dibuat untuk keperluan monitoring kesehatan ibu hamil. Silakan gunakan sesuai kebutuhan.

## 👨‍💻 Developer

Dikembangkan dengan ❤️ menggunakan Laravel Framework

---

**Note**: Pastikan untuk selalu backup database sebelum melakukan update atau perubahan besar pada sistem.
