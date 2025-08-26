@extends('layouts.user')

@section('title', 'Dashboard User')
@section('page-title', 'Dashboard Monitoring')
@section('breadcrumb', 'Pantau kesehatan Anda dengan sistem monitoring dehidrasi yang cerdas')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
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
        <div class="card text-center">
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
        <div class="card text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-bell fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">Notifikasi</h5>
                <h2 class="text-warning">{{ Auth::user()->notifikasi()->where('is_read', false)->count() }}</h2>
                <p class="card-text">Pesan belum dibaca</p>
            </div>
        </div>
    </div>
</div>

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

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Tips Kesehatan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <i class="fas fa-glass-water fa-2x text-info mb-2"></i>
                            <h6>Minum Air Secara Teratur</h6>
                            <small class="text-muted">Minum air setiap 1-2 jam untuk menjaga tubuh tetap terhidrasi</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <i class="fas fa-apple-alt fa-2x text-success mb-2"></i>
                            <h6>Konsumsi Buah dan Sayur</h6>
                            <small class="text-muted">Buah dan sayur mengandung air yang membantu hidrasi tubuh</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                            <h6>Istirahat yang Cukup</h6>
                            <small class="text-muted">Istirahat membantu tubuh memproses cairan dengan lebih baik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample data for chart (replace with real data later)
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
@endsection