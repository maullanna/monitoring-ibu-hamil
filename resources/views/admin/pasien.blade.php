@extends('layouts.admin')

@section('title', 'Manajemen Pasien')
@section('page-title', 'Manajemen Pasien')
@section('breadcrumb', 'Manajemen Pasien')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pasien</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="pasienTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>NIK</th>
                        <th>Usia Kehamilan</th>
                        <th>Target Minum</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\User::with('pasien')->where('role', 'user')->get() as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->nama_lengkap }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->pasien->nik ?? '-' }}</td>
                        <td>
                            @if($user->pasien->usia_kehamilan_minggu)
                                {{ $user->pasien->usia_kehamilan_minggu }} minggu
                            @else
                                <span class="text-muted">Belum diisi</span>
                            @endif
                        </td>
                        <td>{{ $user->pasien->target_minum_ml ?? 2000 }} ml</td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($user->pasien->monitoringDehidrasi()->where('tanggal', date('Y-m-d'))->exists())
                                <span class="badge badge-success">Sudah Input Hari Ini</span>
                            @else
                                <span class="badge badge-warning">Belum Input Hari Ini</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $user->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $user->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modals -->
@foreach(\App\Models\User::with('pasien')->where('role', 'user')->get() as $user)
<div class="modal fade" id="detailModal{{ $user->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pasien: {{ $user->nama_lengkap }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pribadi</h6>
                        <table class="table table-borderless">
                            <tr><td>Nama:</td><td>{{ $user->nama_lengkap }}</td></tr>
                            <tr><td>Email:</td><td>{{ $user->email }}</td></tr>
                            <tr><td>NIK:</td><td>{{ $user->pasien->nik ?? '-' }}</td></tr>
                            <tr><td>Tanggal Lahir:</td><td>{{ $user->pasien->tanggal_lahir ? $user->pasien->tanggal_lahir->format('d/m/Y') : '-' }}</td></tr>
                            <tr><td>Alamat:</td><td>{{ $user->pasien->alamat ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Kehamilan</h6>
                        <table class="table table-borderless">
                            <tr><td>Usia Kehamilan:</td><td>{{ $user->pasien->usia_kehamilan_minggu ?? '-' }} {{ $user->pasien->usia_kehamilan_minggu ? 'minggu' : '' }}</td></tr>
                            <tr><td>Target Minum:</td><td>{{ $user->pasien->target_minum_ml ?? 2000 }} ml</td></tr>
                            <tr><td>Tanggal Daftar:</td><td>{{ $user->created_at->format('d/m/Y') }}</td></tr>
                        </table>
                        
                        @if($user->qr_code)
                        <div class="text-center mt-3">
                            <h6>QR Code</h6>
                            <img src="{{ asset('storage/' . $user->qr_code) }}" alt="QR Code" class="img-fluid" style="max-width: 150px;">
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Riwayat Monitoring Terbaru</h6>
                    @php
                        $recentMonitoring = $user->pasien->monitoringDehidrasi()->latest()->take(5)->get();
                    @endphp
                    
                    @if($recentMonitoring->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jumlah Minum</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMonitoring as $monitoring)
                                    <tr>
                                        <td>{{ $monitoring->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $monitoring->jumlah_minum_ml }} ml</td>
                                        <td>
                                            @if($monitoring->status == 'Kurang')
                                                <span class="badge badge-danger">{{ $monitoring->status }}</span>
                                            @elseif($monitoring->status == 'Cukup')
                                                <span class="badge badge-success">{{ $monitoring->status }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $monitoring->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada data monitoring</p>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#pasienTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#pasienTable_wrapper .col-md-6:eq(0)');
});
</script>
@endsection 