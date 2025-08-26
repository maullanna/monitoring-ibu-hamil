@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi')
@section('page-title', 'Pengaturan Aplikasi')
@section('breadcrumb', 'Pengaturan Aplikasi')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Umum Aplikasi</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @php
                $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::first();
                @endphp



                <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="nama_aplikasi">Nama Aplikasi</label>
                        <input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi"
                            value="{{ $pengaturan->nama_aplikasi ?? 'Monitoring Ibu Hamil' }}" required>
                        <small class="form-text text-muted">Nama yang akan ditampilkan di header dan title aplikasi</small>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Aplikasi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ $pengaturan->deskripsi ?? '' }}</textarea>
                        <small class="form-text text-muted">Deskripsi singkat tentang aplikasi</small>
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo Aplikasi</label>
                        @if($pengaturan && $pengaturan->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo Aplikasi" class="img-thumbnail" style="max-width: 200px;">
                            <p class="text-muted small">Logo saat ini</p>
                        </div>
                        @endif
                        <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">Upload logo baru (format: PNG, JPG, JPEG. Max: 2MB)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Sistem</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-server"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Versi Laravel</span>
                        <span class="info-box-number">{{ app()->version() }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Database</span>
                        <span class="info-box-number">MySQL</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total User</span>
                        <span class="info-box-number">{{ \App\Models\User::count() }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Monitoring</span>
                        <span class="info-box-number">{{ \App\Models\MonitoringDehidrasi::count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Tambahan</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Mode Aplikasi</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="maintenanceMode" disabled>
                        <label class="custom-control-label" for="maintenanceMode">Mode Maintenance</label>
                    </div>
                    <small class="form-text text-muted">Fitur ini akan diaktifkan nanti</small>
                </div>

                <div class="form-group">
                    <label>Notifikasi Email</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="emailNotification" disabled>
                        <label class="custom-control-label" for="emailNotification">Aktifkan Notifikasi Email</label>
                    </div>
                    <small class="text-muted">Fitur ini akan diaktifkan nanti</small>
                </div>

                <div class="form-group">
                    <label>Backup Otomatis</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="autoBackup" disabled>
                        <label class="custom-control-label" for="autoBackup">Backup Harian Otomatis</label>
                    </div>
                    <small class="text-muted">Fitur ini akan diaktifkan nanti</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Perubahan Pengaturan</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Admin</th>
                                <th>Perubahan</th>
                                <th>Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $pengaturan ? $pengaturan->updated_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>System</td>
                                <td>Pengaturan terakhir diupdate</td>
                                <td>{{ $pengaturan ? $pengaturan->nama_aplikasi : 'Default' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Preview logo before upload
        $('#logo').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if ($('#logoPreview').length === 0) {
                        $('#logo').after('<div class="mt-2"><img id="logoPreview" src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;"></div>');
                    } else {
                        $('#logoPreview').attr('src', e.target.result);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection