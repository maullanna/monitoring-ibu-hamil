@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kirim Notifikasi Baru</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.notifikasi.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">Pilih Pasien</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">Pilih Pasien</option>
                            @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_lengkap }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="judul">Judul Notifikasi</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Notifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Notifikasi</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="notifikasiTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pasien</th>
                                <th>Judul</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Waktu Kirim</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Notifikasi::with('user')->latest()->get() as $notifikasi)
                            <tr>
                                <td>{{ $notifikasi->id }}</td>
                                <td>{{ $notifikasi->user->nama_lengkap }}</td>
                                <td>{{ $notifikasi->judul }}</td>
                                <td>{{ Str::limit($notifikasi->pesan, 50) }}</td>
                                <td>
                                    @if($notifikasi->is_read)
                                        <span class="badge badge-success">Sudah Dibaca</span>
                                    @else
                                        <span class="badge badge-warning">Belum Dibaca</span>
                                    @endif
                                </td>
                                <td>{{ $notifikasi->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $notifikasi->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteNotifikasi({{ $notifikasi->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
@foreach(\App\Models\Notifikasi::with('user')->get() as $notifikasi)
<div class="modal fade" id="detailModal{{ $notifikasi->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Notifikasi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><td><strong>Pasien:</strong></td><td>{{ $notifikasi->user->nama_lengkap }}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>{{ $notifikasi->user->email }}</td></tr>
                    <tr><td><strong>Judul:</strong></td><td>{{ $notifikasi->judul }}</td></tr>
                    <tr><td><strong>Pesan:</strong></td><td>{{ $notifikasi->pesan }}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>
                        @if($notifikasi->is_read)
                            <span class="badge badge-success">Sudah Dibaca</span>
                        @else
                            <span class="badge badge-warning">Belum Dibaca</span>
                        @endif
                    </td></tr>
                    <tr><td><strong>Waktu Kirim:</strong></td><td>{{ $notifikasi->created_at->format('d/m/Y H:i:s') }}</td></tr>
                </table>
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
    $('#notifikasiTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#notifikasiTable_wrapper .col-md-6:eq(0)');
});

function deleteNotifikasi(id) {
    if (confirm('Yakin ingin menghapus notifikasi ini?')) {
        // Implement delete functionality
        alert('Fitur delete akan diimplementasikan nanti');
    }
}
</script>
@endsection 