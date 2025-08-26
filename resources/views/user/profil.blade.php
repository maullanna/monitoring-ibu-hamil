@extends('layouts.user')

@section('title', 'Profil')
@section('content')
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Informasi Profil
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.profil.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ Auth::user()->nama_lengkap }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik" value="{{ Auth::user()->pasien->nik ?? '' }}" maxlength="20">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ Auth::user()->pasien->tanggal_lahir ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ Auth::user()->pasien->alamat ?? '' }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usia_kehamilan_minggu" class="form-label">Usia Kehamilan (Minggu)</label>
                            <input type="number" class="form-control" id="usia_kehamilan_minggu" name="usia_kehamilan_minggu" value="{{ Auth::user()->pasien->usia_kehamilan_minggu ?? '' }}" min="1" max="42">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="target_minum_ml" class="form-label">Target Minum Harian (ml)</label>
                            <input type="number" class="form-control" id="target_minum_ml" name="target_minum_ml" value="{{ Auth::user()->pasien->target_minum_ml ?? 2000 }}" min="1000" max="5000" step="100">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-qrcode me-2"></i>QR Code Personal
                </h5>
            </div>
            <div class="card-body">
                @if(Auth::user()->qr_code)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . Auth::user()->qr_code) }}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                    </div>
                    <p class="text-muted small">QR Code ini berisi informasi identitas Anda dan dapat digunakan untuk integrasi dengan IoT botol minum</p>
                    <a href="{{ asset('storage/' . Auth::user()->qr_code) }}" download class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-2"></i>Download QR Code
                    </a>
                @else
                    <div class="mb-3">
                        <i class="fas fa-qrcode fa-5x text-muted"></i>
                    </div>
                    <p class="text-muted">QR Code belum tersedia</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Akun
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status Akun:</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tanggal Daftar:</span>
                    <span>{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Role:</span>
                    <span class="badge bg-info">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 