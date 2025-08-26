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
                    <i class="fas fa-chart-bar me-2"></i>Grafik Asupan Air Minum (30 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
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
                @endphp

                <div class="mb-3">
                    <h3 class="text-primary">{{ $todayIntake }} ml</h3>
                    <p class="text-muted">Minum hari ini</p>
                </div>

                <div class="mb-3">
                    <h4 class="text-success">{{ $target }} ml</h4>
                    <p class="text-muted">Target harian</p>
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
        // Monthly chart
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],
                datasets: [{
                    label: 'Asupan Air (ml)',
                    data: [1800, 2100, 1950, 2200, 1900, 2000, 1850, 2300, 2100, 1950, 2200, 1900, 2000, 1850, 2300, 2100, 1950, 2200, 1900, 2000, 1850, 2300, 2100, 1950, 2200, 1900, 2000, 1850, 2300, 2100],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
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