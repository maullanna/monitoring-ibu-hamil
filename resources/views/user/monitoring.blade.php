@extends('layouts.user')

@section('title', 'Monitoring')
@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Input Data Minum Hari Ini
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('user.monitoring.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah_minum_ml" class="form-label">Jumlah Minum (ml)</label>
                            <input type="number" class="form-control" id="jumlah_minum_ml" name="jumlah_minum_ml" min="0" max="5000" step="50" required>
                            <div class="form-text">Masukkan jumlah air yang sudah diminum hari ini</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Grafik Asupan Air Minum (1 Bulan ke Depan)
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Periode: {{ date('d/m/Y') }} - {{ \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y') }}
                    </small>
                </div>
                <div class="monitoring-chart-container">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-target me-2"></i>Target & Pencapaian
                </h5>
            </div>
            <div class="card-body text-center">
                @php
                $pasien = Auth::user()->pasien;
                $today = $pasien ? \App\Models\MonitoringDehidrasi::where('pasien_id', $pasien->id)
                ->where('tanggal', date('Y-m-d'))
                ->first() : null;
                $target = $pasien ? $pasien->target_minum_ml : 2000;
                $todayIntake = $today ? $today->jumlah_minum_ml : 0;
                $percentage = $target > 0 ? min(100, ($todayIntake / $target) * 100) : 0;
                
                // Hitung rata-rata minum bulan ini
                $monthlyAverage = $pasien ? \App\Models\MonitoringDehidrasi::where('pasien_id', $pasien->id)
                ->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'))
                ->avg('jumlah_minum_ml') : 0;
                @endphp

                <div class="mb-3">
                    <h3 class="text-primary">{{ $todayIntake }} ml</h3>
                    <p class="text-muted">Minum hari ini</p>
                </div>

                <div class="mb-3">
                    <h4 class="text-success">{{ $target }} ml</h4>
                    <p class="text-muted">Target harian</p>
                </div>

                <div class="mb-3">
                    <h5 class="text-info">{{ number_format($monthlyAverage, 0) }} ml</h5>
                    <p class="text-muted">Rata-rata bulan ini</p>
                </div>

                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 80 ? 'warning' : 'danger') }}"
                        role="progressbar"
                        style="width: {{ $percentage }}%">
                        {{ number_format($percentage, 1) }}%
                    </div>
                </div>

                @if($percentage >= 100)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>Target tercapai! 🎉
                </div>
                @elseif($percentage >= 80)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hampir tercapai, lanjutkan!
                </div>
                @else
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>Masih jauh dari target
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Statistik Status Hidrasi
                </h5>
            </div>
            <div class="card-body">
                @php
                $pasien = Auth::user()->pasien;
                $statusStats = $pasien ? $pasien->monitoringDehidrasi()
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray() : [];
                
                $totalDays = array_sum($statusStats);
                @endphp

                @if($totalDays > 0)
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ $statusStats['Cukup'] ?? 0 }}</h4>
                                <small class="text-muted">Cukup</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalDays > 0 ? (($statusStats['Cukup'] ?? 0) / $totalDays) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-warning mb-1">{{ $statusStats['Berlebihan'] ?? 0 }}</h4>
                                <small class="text-muted">Berlebihan</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $totalDays > 0 ? (($statusStats['Berlebihan'] ?? 0) / $totalDays) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-danger mb-1">{{ $statusStats['Kurang'] ?? 0 }}</h4>
                                <small class="text-muted">Kurang</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $totalDays > 0 ? (($statusStats['Kurang'] ?? 0) / $totalDays) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">Total {{ $totalDays }} hari monitoring</small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data untuk statistik</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tint me-2"></i>Rata-rata Asupan Air per Pasien
                </h5>
            </div>
            <div class="card-body">
                @php
                $pasien = Auth::user()->pasien;
                $weeklyAverage = $pasien ? $pasien->monitoringDehidrasi()
                    ->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()])
                    ->avg('jumlah_minum_ml') : 0;
                
                $monthlyAverage = $pasien ? $pasien->monitoringDehidrasi()
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->avg('jumlah_minum_ml') : 0;
                
                $overallAverage = $pasien ? $pasien->monitoringDehidrasi()
                    ->avg('jumlah_minum_ml') : 0;
                @endphp

                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h5 class="text-primary mb-1">{{ number_format($weeklyAverage, 0) }}</h5>
                            <small class="text-muted">Minggu Ini</small>
                            <div class="text-muted">ml</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h5 class="text-info mb-1">{{ number_format($monthlyAverage, 0) }}</h5>
                            <small class="text-muted">Bulan Ini</small>
                            <div class="text-muted">ml</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h5 class="text-success mb-1">{{ number_format($overallAverage, 0) }}</h5>
                            <small class="text-muted">Keseluruhan</small>
                            <div class="text-muted">ml</div>
                        </div>
                    </div>
                </div>

                @if($pasien && $pasien->target_minum_ml > 0)
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Target Harian:</span>
                            <strong>{{ $pasien->target_minum_ml }} ml</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted">Pencapaian Rata-rata:</span>
                            <strong class="text-{{ $overallAverage >= $pasien->target_minum_ml ? 'success' : 'warning' }}">
                                {{ number_format(($overallAverage / $pasien->target_minum_ml) * 100, 1) }}%
                            </strong>
                        </div>
                    </div>
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
                    <i class="fas fa-history me-2"></i>Riwayat Monitoring
                </h5>
            </div>
            <div class="card-body">
                @php
                $pasien = Auth::user()->pasien;
                $monitoringHistory = $pasien ? $pasien->monitoringDehidrasi()->orderBy('tanggal', 'desc')->paginate(10) : collect();
                @endphp

                @if($monitoringHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah Minum</th>
                                <th>Status</th>
                                <th>Target</th>
                                <th>Pencapaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monitoringHistory as $monitoring)
                            <tr>
                                <td>{{ $monitoring->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $monitoring->jumlah_minum_ml }} ml</td>
                                <td>
                                    @if($monitoring->status == 'Kurang')
                                    <span class="badge bg-danger">{{ $monitoring->status }}</span>
                                    @elseif($monitoring->status == 'Cukup')
                                    <span class="badge bg-success">{{ $monitoring->status }}</span>
                                    @elseif($monitoring->status == 'Berlebihan')
                                    <span class="badge bg-warning">{{ $monitoring->status }}</span>
                                    @else
                                    <span class="badge bg-secondary">Belum dinilai</span>
                                    @endif
                                </td>
                                <td>{{ $target }} ml</td>
                                <td>
                                    @php
                                    $dayPercentage = min(100, ($monitoring->jumlah_minum_ml / $target) * 100);
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $dayPercentage >= 100 ? 'success' : ($dayPercentage >= 80 ? 'warning' : 'danger') }}"
                                            role="progressbar"
                                            style="width: {{ $dayPercentage }}%">
                                            {{ number_format($dayPercentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $monitoringHistory->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data monitoring</p>
                    <p class="text-muted">Mulai input data minum Anda hari ini!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller Laravel
        const chartData = @json($chartData ?? ['labels' => [], 'data' => []]);
        
        // Monthly chart dengan data real dari database
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Asupan Air (ml)',
                    data: chartData.data,
                    backgroundColor: chartData.data.map(value => {
                        // Warna berdasarkan nilai: hijau untuk target tercapai, kuning untuk hampir, merah untuk kurang
                        if (value === 0) return 'rgba(200, 200, 200, 0.6)'; // Abu-abu untuk data kosong
                        if (value >= 2000) return 'rgba(75, 192, 192, 0.6)'; // Hijau untuk target tercapai
                        if (value >= 1600) return 'rgba(255, 205, 86, 0.6)'; // Kuning untuk hampir target
                        return 'rgba(255, 99, 132, 0.6)'; // Merah untuk kurang
                    }),
                    borderColor: chartData.data.map(value => {
                        if (value === 0) return 'rgba(200, 200, 200, 1)';
                        if (value >= 2000) return 'rgba(75, 192, 192, 1)';
                        if (value >= 1600) return 'rgba(255, 205, 86, 1)';
                        return 'rgba(255, 99, 132, 1)';
                    }),
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 3000,
                        ticks: {
                            callback: function(value) {
                                return value + ' ml';
                            },
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)',
                            lineWidth: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: true,
                            maxTicksLimit: 20,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            lineWidth: 0.5
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                if (value === 0) {
                                    return 'Tidak ada data';
                                }
                                return 'Asupan Air: ' + value + ' ml';
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Refresh chart setelah submit form
        document.querySelector('form[action*="monitoring"]').addEventListener('submit', function() {
            setTimeout(function() {
                fetch('{{ route("user.monitoring.chart-data") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.labels && data.data) {
                            chart.data.labels = data.labels;
                            chart.data.datasets[0].data = data.data;
                            chart.data.datasets[0].backgroundColor = data.data.map(value => {
                                if (value === 0) return 'rgba(200, 200, 200, 0.6)';
                                if (value >= 2000) return 'rgba(75, 192, 192, 0.6)';
                                if (value >= 1600) return 'rgba(255, 205, 86, 0.6)';
                                return 'rgba(255, 99, 132, 0.6)';
                            });
                            chart.data.datasets[0].borderColor = data.data.map(value => {
                                if (value === 0) return 'rgba(200, 200, 200, 1)';
                                if (value >= 2000) return 'rgba(255, 205, 86, 1)';
                                return 'rgba(255, 99, 132, 1)';
                            });
                            chart.update('none');
                        }
                    })
                    .catch(error => {
                        console.log('Error refreshing chart:', error);
                    });
            }, 1000); // Refresh setelah 1 detik
        });

        // Auto-refresh data setiap 5 menit untuk data real-time dari IoT
        setInterval(function() {
            fetch('{{ route("user.monitoring.chart-data") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.labels && data.data) {
                        // Update chart dengan data terbaru
                        chart.data.labels = data.labels;
                        chart.data.datasets[0].data = data.data;
                        chart.data.datasets[0].backgroundColor = data.data.map(value => {
                            if (value === 0) return 'rgba(200, 200, 200, 0.6)';
                            if (value >= 2000) return 'rgba(75, 192, 192, 0.6)';
                            if (value >= 1600) return 'rgba(255, 205, 86, 0.6)';
                            return 'rgba(255, 99, 132, 0.6)';
                        });
                        chart.data.datasets[0].borderColor = data.data.map(value => {
                            if (value === 0) return 'rgba(200, 200, 200, 1)';
                            if (value >= 2000) return 'rgba(75, 192, 192, 1)';
                            if (value >= 1600) return 'rgba(255, 205, 86, 1)';
                            return 'rgba(255, 99, 132, 1)';
                        });
                        chart.update('none'); // Update tanpa animasi untuk performa
                    }
                })
                .catch(error => {
                    console.log('Error fetching chart data:', error);
                });
        }, 300000); // 5 menit = 300000 ms
    });
</script>
@endsection