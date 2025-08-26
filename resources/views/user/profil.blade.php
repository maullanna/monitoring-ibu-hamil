@extends('layouts.user')

@section('title', 'Profil')
@section('page-title', 'Profil & QR Code')
@section('breadcrumb', 'Kelola informasi profil dan QR Code identitas')

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

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.profil.update') }}" enctype="multipart/form-data">
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
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ Auth::user()->pasien->tanggal_lahir ?? '' }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="usia_kehamilan_minggu" class="form-label">Usia Kehamilan (Minggu)</label>
                            <input type="number" class="form-control" id="usia_kehamilan_minggu" name="usia_kehamilan_minggu" value="{{ Auth::user()->pasien->usia_kehamilan_minggu ?? '' }}" min="1" max="42">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ Auth::user()->pasien->alamat ?? '' }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target_minum_ml" class="form-label">Target Minum Harian (ml)</label>
                            <input type="number" class="form-control" id="target_minum_ml" name="target_minum_ml" value="{{ Auth::user()->pasien->target_minum_ml ?? 2000 }}" min="1000" max="5000" step="100">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-text">Upload foto profil Anda (JPG, PNG, maksimal 2MB)</div>
                                                    @if(Auth::user()->pasien && Auth::user()->pasien->foto)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . Auth::user()->pasien->foto) }}" alt="Foto Profil" class="img-thumbnail" style="max-width: 150px;">
                                    <div class="mt-1">
                                        <small class="text-muted">Foto saat ini</small>
                                    </div>
                                </div>
                            @endif
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
                <!-- QR Code Display -->
                <div class="qr-code-container mb-3">
                    <div class="qr-code-svg-container" id="qrCodeContainer">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat QR Code...</p>
                        </div>
                    </div>
                </div>
                
                <h6>QR Code Profil</h6>
                <p class="text-muted small">QR Code ini berisi informasi identitas lengkap Anda</p>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>QR Code Siap!</strong> Sekarang QR code bisa di-scan dengan Google Lens dan semua scanner lainnya. 
                    Setelah scan akan langsung membuka halaman profil lengkap dengan foto.
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('user.qr-code.download') }}" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    <button onclick="printQRCode()" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
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

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="printContent">
                    <h4 class="mb-3">QR Code Profil Ibu Hamil</h4>
                    <div class="qr-code-svg-container" id="printQrCodeContainer">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p><strong>Nama:</strong> {{ Auth::user()->nama_lengkap }}</p>

                        <p><strong>Usia Kehamilan:</strong> {{ Auth::user()->pasien->usia_kehamilan_minggu ? Auth::user()->pasien->usia_kehamilan_minggu . ' minggu' : 'Belum diisi' }}</p>
                        <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printContent()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load QR Code when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadQRCode();
    });

    // Function to load QR Code
    function loadQRCode() {
        const qrContainer = document.getElementById('qrCodeContainer');
        const printQrContainer = document.getElementById('printQrCodeContainer');
        
        if (qrContainer) {
            fetch('{{ route("user.qr-code.generate") }}')
                .then(response => response.text())
                .then(svgContent => {
                    qrContainer.innerHTML = svgContent;
                })
                .catch(error => {
                    console.error('Error loading QR Code:', error);
                    qrContainer.innerHTML = `
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Gagal memuat QR Code</p>
                            <button onclick="loadQRCode()" class="btn btn-sm btn-outline-primary">Coba Lagi</button>
                        </div>
                    `;
                });
        }
        
        if (printQrContainer) {
            fetch('{{ route("user.qr-code.generate") }}')
                .then(response => response.text())
                .then(svgContent => {
                    printQrContainer.innerHTML = svgContent;
                })
                .catch(error => {
                    console.error('Error loading print QR Code:', error);
                    printQrContainer.innerHTML = `
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Gagal memuat QR Code</p>
                        </div>
                    `;
                });
        }
    }

    function printQRCode() {
        try {
            // Show print modal
            const printModalElement = document.getElementById('printModal');
            if (printModalElement) {
                const printModal = new bootstrap.Modal(printModalElement);
                printModal.show();
                
                // Load QR Code for print if not loaded yet
                setTimeout(() => {
                    const printQrContainer = document.getElementById('printQrCodeContainer');
                    if (printQrContainer && printQrContainer.querySelector('.spinner-border')) {
                        loadQRCode();
                    }
                }, 100);
            } else {
                console.error('Print modal element not found');
                alert('Modal print tidak ditemukan');
            }
        } catch (error) {
            console.error('Error showing print modal:', error);
            alert('Error menampilkan modal print: ' + error.message);
        }
    }

    function printContent() {
        try {
            const printContent = document.getElementById('printContent');
            if (!printContent) {
                console.error('Print content element not found');
                alert('Konten print tidak ditemukan');
                return;
            }

            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print QR Code</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .qr-code-svg-container { text-align: center; margin: 20px 0; }
                        .qr-code-svg-container svg { max-width: 300px; height: auto; }
                        .info { margin: 20px 0; }
                        .info p { margin: 10px 0; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                    <div class="no-print" style="text-align: center; margin-top: 20px;">
                        <button onclick="window.print()">Print</button>
                        <button onclick="window.close()">Tutup</button>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            
        } catch (error) {
            console.error('Error printing content:', error);
            alert('Error saat print: ' + error.message);
        }
    }

    // Auto-refresh QR code setiap 5 menit untuk memastikan data terbaru
    setInterval(function() {
        const qrContainer = document.querySelector('.qr-code-svg-container');
        if (qrContainer) {
            fetch('{{ route("user.qr-code.generate") }}?' + new Date().getTime())
                .then(response => response.text())
                .then(svgContent => {
                    qrContainer.innerHTML = svgContent;
                })
                .catch(error => {
                    console.log('Error refreshing QR code:', error);
                });
        }
    }, 300000); // 5 menit
</script>

<style>
    .qr-code-container {
        padding: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        margin: 15px 0;
    }

    .qr-code-svg-container {
        display: flex;
        justify-content: center;
        align-items: center;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .qr-code-svg-container svg {
        max-width: 200px;
        height: auto;
    }

    @media print {
        .btn, .modal-footer, .card-header {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .qr-code-container {
            background: white !important;
        }
    }
</style>
@endsection 