@extends('layouts.admin')

@section('title', 'Backup Data')
@section('page-title', 'Backup Data')
@section('breadcrumb', 'Backup Data')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buat Backup Baru</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.backup.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Backup</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Backup data sebelum maintenance sistem" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-database me-2"></i>Buat Backup
                    </button>
                </form>
                
                <div class="mt-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Backup akan menyimpan snapshot data saat ini untuk keamanan dan pemulihan data.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Backup</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="backupTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Admin</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Waktu Backup</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\BackupLog::with('admin')->latest()->get() as $backup)
                            <tr>
                                <td>{{ $backup->id }}</td>
                                <td>{{ $backup->admin->nama_lengkap }}</td>
                                <td>{{ $backup->deskripsi }}</td>
                                <td>
                                    @if($backup->status == 'Sukses')
                                        <span class="badge badge-success">{{ $backup->status }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $backup->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $backup->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $backup->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($backup->status == 'Sukses')
                                        <button type="button" class="btn btn-sm btn-success" onclick="downloadBackup({{ $backup->id }})">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
@foreach(\App\Models\BackupLog::with('admin')->get() as $backup)
<div class="modal fade" id="detailModal{{ $backup->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Backup</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><td><strong>ID Backup:</strong></td><td>{{ $backup->id }}</td></tr>
                    <tr><td><strong>Admin:</strong></td><td>{{ $backup->admin->nama_lengkap }}</td></tr>
                    <tr><td><strong>Email Admin:</strong></td><td>{{ $backup->admin->email }}</td></tr>
                    <tr><td><strong>Deskripsi:</strong></td><td>{{ $backup->deskripsi }}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>
                        @if($backup->status == 'Sukses')
                            <span class="badge badge-success">{{ $backup->status }}</span>
                        @else
                            <span class="badge badge-danger">{{ $backup->status }}</span>
                        @endif
                    </td></tr>
                    <tr><td><strong>Waktu Backup:</strong></td><td>{{ $backup->created_at->format('d/m/Y H:i:s') }}</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                @if($backup->status == 'Sukses')
                    <button type="button" class="btn btn-success" onclick="downloadBackup({{ $backup->id }})">
                        <i class="fas fa-download me-2"></i>Download Backup
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#backupTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#backupTable_wrapper .col-md-6:eq(0)');
});

function downloadBackup(id) {
    if (confirm('Download file backup ini?')) {
        // Implement download functionality
        alert('Fitur download backup akan diimplementasikan nanti');
    }
}
</script>
@endsection 