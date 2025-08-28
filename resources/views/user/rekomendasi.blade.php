@extends('layouts.user')

@section('title', 'Rekomendasi Personal')
@section('page-title', '🎯 Rekomendasi Personal')
@section('breadcrumb', 'Target minum dinamis berdasarkan kondisi personal Anda')

@section('content')
<style>
/* Custom styles untuk kontras yang lebih baik */
.card-header {
    border-bottom: 2px solid rgba(255,255,255,0.2) !important;
}

.form-label {
    color: #2c3e50 !important;
    font-weight: 600 !important;
}

.form-control, .form-select {
    border-color: #bdc3c7 !important;
    background-color: #ffffff !important;
}

.form-control:focus, .form-select:focus {
    border-color: #3498db !important;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25) !important;
}

.text-muted {
    color: #6c757d !important;
}

.alert-info {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
    color: #1565c0 !important;
}

.table td {
    vertical-align: middle !important;
}

.btn {
    font-weight: 600 !important;
}

.jadwal-item {
    background-color: #ffffff !important;
    border: 1px solid #dee2e6 !important;
    transition: all 0.3s ease;
}

.jadwal-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    border-color: #adb5bd !important;
}
</style>

<!-- Rekomendasi Hari Ini -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-target fa-2x me-3"></i>
                    <div>
                        <h3 class="mb-0 fw-bold">Target Minum Hari Ini</h3>
                        <p class="mb-0" style="opacity: 0.9;">Target yang disesuaikan dengan kondisi Anda</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center p-4">
                            <div class="display-4 text-primary mb-2 fw-bold" id="target-dinamis">
                                {{ $rekomendasiHariIni ? $rekomendasiHariIni->target_dinamis : ($pasien->target_minum_ml ?? 2000) }} ml
                            </div>
                            <div class="text-dark fw-semibold">Target Dinamis</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center p-4">
                            <div class="display-4 text-secondary mb-2 fw-bold">
                                {{ $rekomendasiHariIni ? $rekomendasiHariIni->target_standar : 2000 }} ml
                            </div>
                            <div class="text-dark fw-semibold">Target Standar</div>
                        </div>
                    </div>
                </div>
                
                @if($rekomendasiHariIni && $rekomendasiHariIni->alasan_rekomendasi)
                <div class="alert alert-info mt-3">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Alasan Penyesuaian Target:</h6>
                    <p class="mb-0 fw-medium">{{ $rekomendasiHariIni->alasan_rekomendasi }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Data Personal untuk Rekomendasi -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-user-edit me-2"></i>Data Personal
                </h5>
            </div>
            <div class="card-body">
                <form id="personalDataForm">
                    @csrf
                    <div class="mb-3">
                        <label for="berat_badan" class="form-label fw-semibold text-dark">Berat Badan (kg)</label>
                        <input type="number" class="form-control" id="berat_badan" name="berat_badan" 
                               value="{{ $pasien->berat_badan }}" step="0.1" min="30" max="200">
                        <div class="form-text text-muted">Berat badan mempengaruhi kebutuhan hidrasi</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tinggi_badan" class="form-label fw-semibold text-dark">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" id="tinggi_badan" name="tinggi_badan" 
                               value="{{ $pasien->tinggi_badan }}" step="0.1" min="100" max="250">
                        <div class="form-text text-muted">Untuk perhitungan BMI dan kebutuhan nutrisi</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="aktivitas_fisik" class="form-label fw-semibold text-dark">Aktivitas Fisik</label>
                        <select class="form-select" id="aktivitas_fisik" name="aktivitas_fisik" required>
                            <option value="rendah" {{ $pasien->aktivitas_fisik == 'rendah' ? 'selected' : '' }}>Rendah (sedentary)</option>
                            <option value="sedang" {{ $pasien->aktivitas_fisik == 'sedang' ? 'selected' : '' }}>Sedang (moderate)</option>
                            <option value="tinggi" {{ $pasien->aktivitas_fisik == 'tinggi' ? 'selected' : '' }}>Tinggi (active)</option>
                        </select>
                        <div class="form-text text-muted">Aktivitas fisik mempengaruhi kebutuhan air</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lokasi_kota" class="form-label fw-semibold text-dark">Kota Lokasi</label>
                        <input type="text" class="form-control" id="lokasi_kota" name="lokasi_kota" 
                               value="{{ $pasien->lokasi_kota }}" placeholder="Contoh: Jakarta, Surabaya">
                        <div class="form-text text-muted">Untuk data cuaca dan penyesuaian target</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fas fa-save me-2"></i>Update Data Personal
                    </button>
                </form>
                
                @if($pasien->bmi)
                <div class="mt-3 p-3 rounded" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                    <h6 class="fw-bold text-dark"><i class="fas fa-calculator me-2"></i>BMI Anda:</h6>
                    <div class="display-6 text-info fw-bold">{{ $pasien->bmi }}</div>
                    <small class="text-dark fw-medium">
                        @if($pasien->bmi < 18.5)
                            Berat badan kurang
                        @elseif($pasien->bmi < 25)
                            Berat badan normal
                        @elseif($pasien->bmi < 30)
                            Berat badan berlebih
                        @else
                            Obesitas
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-bell me-2"></i>Jadwal Notifikasi
                </h5>
            </div>
            <div class="card-body">
                <div id="jadwalContainer">
                    @foreach($jadwalNotifikasi as $index => $jadwal)
                    <div class="jadwal-item mb-3 p-3 border rounded" data-index="{{ $index }}" style="background-color: #fff; border-color: #dee2e6 !important;">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Waktu</label>
                                <input type="time" class="form-control waktu-notifikasi" 
                                       value="{{ $jadwal->waktu_notifikasi->format('H:i') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Trigger</label>
                                <select class="form-select jenis-trigger" required>
                                    <option value="waktu" {{ $jadwal->jenis_trigger == 'waktu' ? 'selected' : '' }}>Waktu</option>
                                    <option value="volume" {{ $jadwal->jenis_trigger == 'volume' ? 'selected' : '' }}>Volume</option>
                                    <option value="kombinasi" {{ $jadwal->jenis_trigger == 'kombinasi' ? 'selected' : '' }}>Kombinasi</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input is-active" type="checkbox" 
                                           {{ $jadwal->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium">Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Threshold Volume (ml)</label>
                                <input type="number" class="form-control volume-threshold" 
                                       value="{{ $jadwal->volume_threshold }}" 
                                       placeholder="1500" min="100" max="5000">
                                <div class="form-text text-muted">Untuk trigger volume</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Pesan</label>
                                <textarea class="form-control pesan-notifikasi" rows="2" required>{{ $jadwal->pesan_notifikasi }}</textarea>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm mt-2 hapus-jadwal fw-semibold">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </div>
                    @endforeach
                </div>
                
                <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="tambahJadwal">
                    <i class="fas fa-plus me-1"></i>Tambah Jadwal
                </button>
                
                <button type="button" class="btn btn-success w-100 mt-3 fw-semibold" id="simpanJadwal">
                    <i class="fas fa-save me-2"></i>Simpan Jadwal Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Rekomendasi dan Grafik Target (2 Grid) -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-chart-line me-2"></i>Statistik Rekomendasi
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold text-dark mb-3">Minggu Ini</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold text-dark">Rata-rata Target:</td>
                                        <td class="text-end fw-bold text-primary">
                                            <span id="avg-target-minggu">
                                                {{ $rekomendasiMingguIni->avg('target_dinamis') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-dark">Faktor Cuaca:</td>
                                        <td class="text-end fw-bold text-info">
                                            <span id="avg-cuaca-minggu">
                                                {{ $rekomendasiMingguIni->avg('faktor_cuaca') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-dark">Faktor Aktivitas:</td>
                                        <td class="text-end fw-bold text-success">
                                            <span id="avg-aktivitas-minggu">
                                                {{ $rekomendasiMingguIni->avg('faktor_aktivitas') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <h6 class="fw-bold text-dark mb-3 mt-4">Bulan Ini</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold text-dark">Rata-rata Target:</td>
                                        <td class="text-end fw-bold text-primary">
                                            <span id="avg-target-bulan">
                                                {{ $rekomendasiBulanIni->avg('target_dinamis') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-dark">Faktor Cuaca:</td>
                                        <td class="text-end fw-bold text-info">
                                            <span id="avg-cuaca-bulan">
                                                {{ $rekomendasiBulanIni->avg('faktor_cuaca') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-dark">Faktor Aktivitas:</td>
                                        <td class="text-end fw-bold text-success">
                                            <span id="avg-aktivitas-bulan">
                                                {{ $rekomendasiBulanIni->avg('faktor_aktivitas') ?? 0 }}
                                            </span> ml
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-chart-bar me-2"></i>Grafik Target Dinamis (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="rekomendasiChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let rekomendasiChart;

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    initializeChart();
    setupEventListeners();
});

function initializeChart() {
    const ctx = document.getElementById('rekomendasiChart').getContext('2d');
    
    rekomendasiChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Target Dinamis (ml)',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Target Standar (ml)',
                data: [],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderDash: [5, 5],
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Perbandingan Target Dinamis vs Standar'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Volume (ml)'
                    }
                }
            }
        }
    });
    
    // Load chart data
    loadChartData();
}

function loadChartData() {
    fetch('/user/rekomendasi/recommendations?periode=week')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                updateChart(data.data);
            }
        })
        .catch(error => console.error('Error loading chart data:', error));
}

function updateChart(data) {
    const labels = data.map(item => {
        const date = new Date(item.tanggal);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    });
    
    const targetDinamis = data.map(item => item.target_dinamis);
    const targetStandar = data.map(item => item.target_standar);
    
    rekomendasiChart.data.labels = labels;
    rekomendasiChart.data.datasets[0].data = targetDinamis;
    rekomendasiChart.data.datasets[1].data = targetStandar;
    rekomendasiChart.update();
}

function setupEventListeners() {
    // Personal data form
    document.getElementById('personalDataForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updatePersonalData();
    });
    
    // Jadwal notifikasi
    document.getElementById('tambahJadwal').addEventListener('click', tambahJadwal);
    document.getElementById('simpanJadwal').addEventListener('click', simpanJadwal);
    
    // Delete jadwal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('hapus-jadwal')) {
            e.target.closest('.jadwal-item').remove();
        }
    });
}

function updatePersonalData() {
    const formData = new FormData(document.getElementById('personalDataForm'));
    
    fetch('/user/rekomendasi/personal-data', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update target display
            document.getElementById('target-dinamis').textContent = data.data.target_baru + ' ml';
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            
            // Reload chart
            loadChartData();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan sistem'
        });
    });
}

function tambahJadwal() {
    const container = document.getElementById('jadwalContainer');
    const index = container.children.length;
    
    const jadwalHtml = `
        <div class="jadwal-item mb-3 p-3 border rounded" data-index="${index}">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Waktu</label>
                    <input type="time" class="form-control waktu-notifikasi" value="08:00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trigger</label>
                    <select class="form-select jenis-trigger" required>
                        <option value="waktu">Waktu</option>
                        <option value="volume">Volume</option>
                        <option value="kombinasi">Kombinasi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input is-active" type="checkbox" checked>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Threshold Volume (ml)</label>
                    <input type="number" class="form-control volume-threshold" value="1500" placeholder="1500" min="100" max="5000">
                    <div class="form-text">Untuk trigger volume</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pesan</label>
                    <textarea class="form-control pesan-notifikasi" rows="2" required>Waktunya minum air!</textarea>
                </div>
            </div>
            
            <button type="button" class="btn btn-outline-danger btn-sm mt-2 hapus-jadwal">
                <i class="fas fa-trash me-1"></i>Hapus
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', jadwalHtml);
}

function simpanJadwal() {
    const jadwalItems = document.querySelectorAll('.jadwal-item');
    const jadwal = [];
    
    jadwalItems.forEach(item => {
        const index = item.dataset.index;
        jadwal.push({
            id: null,
            waktu_notifikasi: item.querySelector('.waktu-notifikasi').value,
            jenis_trigger: item.querySelector('.jenis-trigger').value,
            volume_threshold: item.querySelector('.volume-threshold').value || null,
            pesan_notifikasi: item.querySelector('.pesan-notifikasi').value,
            is_active: item.querySelector('.is-active').checked
        });
    });
    
    fetch('/user/rekomendasi/notification-schedule', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ jadwal })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan sistem'
        });
    });
}
</script>
@endsection
