@extends('layouts.user')

@section('title', 'Dashboard User')
@section('page-title', 'Dashboard Monitoring')
@section('breadcrumb', 'Pantau kesehatan Anda dengan sistem monitoring dehidrasi yang cerdas')

@section('content')
<!-- Welcome Notification Popup (Hidden, will be shown via JavaScript) -->
<div id="notification-popup" style="display: none;"></div>

<!-- Secondary Info Cards -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-tint fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">Target Minum Hari Ini</h5>
                <h2 class="text-primary">{{ Auth::user()->pasien->target_minum_ml ?? 2000 }} ml</h2>
                <p class="card-text">Jaga tubuh tetap terhidrasi untuk kesehatan Anda dan bayi</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-calendar-alt fa-3x text-success"></i>
                </div>
                <h5 class="card-title">Usia Kehamilan</h5>
                <h2 class="text-success">{{ Auth::user()->pasien->usia_kehamilan_minggu ?? 'Belum diisi' }}</h2>
                <p class="card-text">{{ Auth::user()->pasien->usia_kehamilan_minggu ? 'minggu' : 'Silakan isi di profil' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-chart-line fa-3x text-info"></i>
                </div>
                <h5 class="card-title">Monitoring Harian</h5>
                <h2 class="text-info">{{ Auth::user()->pasien->monitoringDehidrasi()->count() }}</h2>
                <p class="card-text">Catatan asupan air minum harian</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>Grafik Asupan Air Minum (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="waterIntakeChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Riwayat Terbaru
                </h5>
            </div>
            <div class="card-body">
                @php
                $pasien = Auth::user()->pasien;
                $recentMonitoring = $pasien ? $pasien->monitoringDehidrasi()->latest()->take(5)->get() : collect();
                @endphp

                @if($recentMonitoring->count() > 0)
                @foreach($recentMonitoring as $monitoring)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <small class="text-muted">{{ $monitoring->tanggal->format('d/m') }}</small>
                        <div class="fw-bold">{{ $monitoring->jumlah_minum_ml }} ml</div>
                    </div>
                    <span class="badge bg-{{ $monitoring->status == 'Kurang' ? 'danger' : ($monitoring->status == 'Cukup' ? 'success' : 'warning') }}">
                        {{ $monitoring->status ?? 'Belum dinilai' }}
                    </span>
                </div>
                @endforeach
                @else
                <p class="text-muted text-center">Belum ada data monitoring</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 for beautiful popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Show notification popup when user logs in
document.addEventListener('DOMContentLoaded', function() {
    // Delay popup for 1 second after page load
    setTimeout(() => {
        checkAndShowNotifications();
    }, 1000);
});

// Check and show notifications
function checkAndShowNotifications() {
    fetch('/user/notifikasi/unread-count')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                showNotificationPopup(data.count);
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

// Show beautiful notification popup
function showNotificationPopup(count) {
    Swal.fire({
        title: '🔔 Notifikasi Baru!',
        html: `
            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-bell fa-3x text-primary"></i>
                </div>
                <p class="mb-2">Anda memiliki <strong class="text-primary">${count}</strong> notifikasi baru yang belum dibaca.</p>
                <p class="text-muted small">Klik tombol di bawah untuk melihat semua notifikasi</p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Lihat Notifikasi',
        cancelButtonText: 'Nanti Saja',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: true,
        allowEscapeKey: true,
        timer: 15000, // Auto close after 15 seconds
        timerProgressBar: true,
        customClass: {
            popup: 'notification-popup',
            title: 'notification-title',
            htmlContainer: 'notification-content'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to notifications page
            window.location.href = '{{ route("user.notifikasi") }}';
        }
    });
}

// Update notification stats real-time
function updateNotificationStats() {
    fetch('/user/notifikasi/unread-count')
        .then(response => response.json())
        .then(data => {
            // Update unread count in sidebar if exists
            const unreadElement = document.getElementById('unread-notifications');
            if (unreadElement) {
                unreadElement.textContent = data.count;
            }
            
            // Update read count (total - unread)
            const totalElement = document.getElementById('total-notifications');
            const readElement = document.getElementById('read-notifications');
            if (totalElement && readElement) {
                const total = parseInt(totalElement.textContent);
                const unread = data.count;
                readElement.textContent = total - unread;
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Update stats every 30 seconds
setInterval(updateNotificationStats, 30000);

// Update stats on page load
document.addEventListener('DOMContentLoaded', updateNotificationStats);

// Chart initialization
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('waterIntakeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            datasets: [{
                label: 'Asupan Air (ml)',
                data: [1800, 2100, 1950, 2200, 1900, 2000, 1850],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 3000
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<style>
/* Custom styles for notification popup */
.notification-popup {
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}

.notification-title {
    color: #2c3e50 !important;
    font-weight: 700 !important;
}

.notification-content {
    color: #34495e !important;
}

.swal2-popup {
    font-size: 14px !important;
}

.swal2-confirm {
    font-weight: 600 !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
}

.swal2-cancel {
    font-weight: 600 !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
}
</style>
@endsection