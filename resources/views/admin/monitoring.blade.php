@extends('layouts.admin')

@section('title', 'Monitoring Dehidrasi')
@section('page-title', 'Monitoring Dehidrasi')
@section('breadcrumb', 'Monitoring Dehidrasi')

@section('content')
<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Filter Data</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.monitoring') }}" class="row">
                    <div class="col-md-3">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="Kurang" {{ request('status') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                            <option value="Cukup" {{ request('status') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                            <option value="Berlebihan" {{ request('status') == 'Berlebihan' ? 'selected' : '' }}>Berlebihan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.monitoring') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Export Data</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.monitoring.export', ['format' => 'excel'] + request()->query()) }}" 
                           class="btn btn-success btn-block">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.monitoring.export', ['format' => 'csv'] + request()->query()) }}" 
                           class="btn btn-info btn-block">
                            <i class="fas fa-file-csv me-2"></i>Export CSV
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.monitoring.export', ['format' => 'pdf'] + request()->query()) }}" 
                           class="btn btn-danger btn-block">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning btn-block" onclick="printTable()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Monitoring Dehidrasi Semua Pasien</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="monitoringTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal</th>
                                <th>Jumlah Minum</th>
                                <th>Target</th>
                                <th>Pencapaian</th>
                                <th>Status</th>
                                <th>Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monitoringData as $monitoring)
                            <tr>
                                <td>{{ $monitoring->id }}</td>
                                <td>{{ $monitoring->pasien->user->nama_lengkap }}</td>
                                <td>{{ $monitoring->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $monitoring->jumlah_minum_ml }} ml</td>
                                <td>{{ $monitoring->pasien->target_minum_ml ?? 2000 }} ml</td>
                                <td>
                                    @php
                                        $target = $monitoring->pasien->target_minum_ml ?? 2000;
                                        $percentage = min(100, ($monitoring->jumlah_minum_ml / $target) * 100);
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 80 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($monitoring->status == 'Kurang')
                                        <span class="badge badge-danger">{{ $monitoring->status }}</span>
                                    @elseif($monitoring->status == 'Cukup')
                                        <span class="badge badge-success">{{ $monitoring->status }}</span>
                                    @elseif($monitoring->status == 'Berlebihan')
                                        <span class="badge badge-warning">{{ $monitoring->status }}</span>
                                    @else
                                        <span class="badge badge-secondary">Belum dinilai</span>
                                    @endif
                                </td>
                                <td>{{ $monitoring->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik Status Hidrasi</h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rata-rata Asupan Air per Pasien</h3>
            </div>
            <div class="card-body">
                <canvas id="averageChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pasien dengan Asupan Air Rendah (Hari Ini)</h3>
            </div>
            <div class="card-body">
                @php
                    $today = date('Y-m-d');
                    $lowIntakeUsers = \App\Models\MonitoringDehidrasi::with(['pasien.user'])
                        ->where('tanggal', $today)
                        ->get()
                        ->filter(function($monitoring) {
                            $target = $monitoring->pasien->target_minum_ml ?? 2000;
                            return $monitoring->jumlah_minum_ml < ($target * 0.8);
                        });
                @endphp
                
                @if($lowIntakeUsers->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Ada {{ $lowIntakeUsers->count() }} pasien dengan asupan air rendah hari ini
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Pasien</th>
                                    <th>Asupan Hari Ini</th>
                                    <th>Target</th>
                                    <th>Persentase</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowIntakeUsers as $monitoring)
                                <tr>
                                    <td>{{ $monitoring->pasien->user->nama_lengkap }}</td>
                                    <td>{{ $monitoring->jumlah_minum_ml }} ml</td>
                                    <td>{{ $monitoring->pasien->target_minum_ml ?? 2000 }} ml</td>
                                    <td>
                                        @php
                                            $target = $monitoring->pasien->target_minum_ml ?? 2000;
                                            $percentage = min(100, ($monitoring->jumlah_minum_ml / $target) * 100);
                                        @endphp
                                        <span class="text-danger">{{ number_format($percentage, 1) }}%</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="sendNotification({{ $monitoring->pasien->user->id }})">
                                            <i class="fas fa-bell"></i> Kirim Notifikasi
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Semua pasien sudah mencapai target asupan air hari ini
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#monitoringTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Kurang', 'Cukup', 'Berlebihan', 'Belum Dinilai'],
            datasets: [{
                data: [
                    {{ \App\Models\MonitoringDehidrasi::where('status', 'Kurang')->count() }},
                    {{ \App\Models\MonitoringDehidrasi::where('status', 'Cukup')->count() }},
                    {{ \App\Models\MonitoringDehidrasi::where('status', 'Berlebihan')->count() }},
                    {{ \App\Models\MonitoringDehidrasi::whereNull('status')->count() }}
                ],
                backgroundColor: ['#dc3545', '#28a745', '#ffc107', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Average Chart
    const averageCtx = document.getElementById('averageChart').getContext('2d');
    new Chart(averageCtx, {
        type: 'bar',
        data: {
            labels: ['Rata-rata Asupan'],
            datasets: [{
                label: 'ml',
                data: [{{ \App\Models\MonitoringDehidrasi::avg('jumlah_minum_ml') ?? 0 }}],
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
                    beginAtZero: true
                }
            }
        }
    });
});

function sendNotification(userId) {
    if (confirm('Kirim notifikasi pengingat untuk minum air?')) {
        // Redirect to notification page or send via AJAX
        window.location.href = '{{ route("admin.notifikasi") }}?user_id=' + userId;
    }
}
</script>
@endsection 