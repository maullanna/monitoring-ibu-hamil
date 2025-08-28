@extends('layouts.user')

@section('title', 'Notifikasi')
@section('page-title', '📱 Pusat Notifikasi')
@section('breadcrumb', 'Kelola semua notifikasi penting kesehatan Anda dan bayi')

@section('content')
<!-- Notification Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <i class="fas fa-bell fa-2x mb-2"></i>
                <h4>{{ $notifications->total() }}</h4>
                <p class="mb-0">Total Notifikasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <i class="fas fa-envelope fa-2x mb-2"></i>
                <h4>{{ $notifications->where('is_read', false)->count() }}</h4>
                <p class="mb-0">Belum Dibaca</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $notifications->where('is_read', true)->count() }}</h4>
                <p class="mb-0">Sudah Dibaca</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4>{{ $notifications->where('prioritas', 'urgent')->count() }}</h4>
                <p class="mb-0">Urgent</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Notification Content -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Daftar Notifikasi
                </h5>
                <div>
                    <button type="button" class="btn btn-light btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">Status</th>
                                    <th width="25%">Judul</th>
                                    <th width="35%">Pesan</th>
                                    <th width="15%">Prioritas</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                <tr class="notification-row {{ $notification->is_read ? 'table-light' : 'table-warning' }}" 
                                    data-notification-id="{{ $notification->id }}">
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-check me-1"></i>Dibaca
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="fas fa-envelope me-1"></i>Baru
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <strong>{{ $notification->judul }}</strong>
                                            @if($notification->tipe)
                                                <span class="badge bg-{{ $notification->tipe == 'info' ? 'info' : ($notification->tipe == 'warning' ? 'warning' : ($notification->tipe == 'success' ? 'success' : 'danger')) }} ms-2">
                                                    {{ ucfirst($notification->tipe) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="notification-message">
                                            {{ Str::limit($notification->pesan, 80) }}
                                            @if(strlen($notification->pesan) > 80)
                                                <button class="btn btn-link btn-sm p-0 ms-1" onclick="showFullMessage({{ $notification->id }})">
                                                    Baca selengkapnya
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($notification->prioritas == 'urgent')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Urgent
                                            </span>
                                        @elseif($notification->prioritas == 'high')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-arrow-up me-1"></i>High
                                            </span>
                                        @elseif($notification->prioritas == 'normal')
                                            <span class="badge bg-info">
                                                <i class="fas fa-minus me-1"></i>Normal
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-arrow-down me-1"></i>Low
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$notification->is_read)
                                                <button type="button" class="btn btn-outline-success" onclick="markAsRead({{ $notification->id }})" title="Tandai Dibaca">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="btn btn-outline-primary" title="Lihat Detail">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-outline-info" onclick="showNotificationDetail({{ $notification->id }})" title="Detail Lengkap">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteNotification({{ $notification->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Tidak Ada Notifikasi</h4>
                        <p class="text-muted">Anda belum memiliki notifikasi apapun. Admin akan mengirim notifikasi penting untuk kesehatan Anda.</p>
                        <div class="mt-3">
                            <i class="fas fa-heart text-danger me-2"></i>
                            <small class="text-muted">Sistem akan memberitahu Anda tentang tips kesehatan, pengingat minum air, dan informasi penting lainnya.</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Notification Detail Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="notificationTitle">
                    <i class="fas fa-bell me-2"></i>Detail Notifikasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="notificationContent" class="mb-3"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Notifikasi</h6>
                                <div class="mb-2">
                                    <small class="text-muted">Status:</small><br>
                                    <span id="notificationStatus"></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Tipe:</small><br>
                                    <span id="notificationType"></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Prioritas:</small><br>
                                    <span id="notificationPriority"></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Waktu:</small><br>
                                    <span id="notificationTime"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-primary" id="notificationAction" style="display: none;">
                    <i class="fas fa-external-link-alt me-1"></i>Lihat Detail
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mark notification as read
function markAsRead(id) {
    fetch(`/user/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI without reload
            const row = document.querySelector(`[data-notification-id="${id}"]`);
            if (row) {
                // Update status badge
                const statusCell = row.querySelector('td:first-child');
                statusCell.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-check me-1"></i>Dibaca</span>';
                
                // Update row class
                row.classList.remove('table-warning');
                row.classList.add('table-light');
                
                // Update action buttons
                const actionCell = row.querySelector('td:last-child');
                const markAsReadBtn = actionCell.querySelector('.btn-outline-success');
                if (markAsReadBtn) {
                    markAsReadBtn.remove();
                }
                
                // Update stats
                updateNotificationStats();
            }
        }
    });
}

// Mark all notifications as read
function markAllAsRead() {
    if (confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
        fetch('/user/notifikasi/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Delete notification
function deleteNotification(id) {
    if (confirm('Hapus notifikasi ini?')) {
        fetch(`/user/notifikasi/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row from table
                const row = document.querySelector(`[data-notification-id="${id}"]`);
                if (row) {
                    row.remove();
                    updateNotificationStats();
                }
            }
        });
    }
}

// Show notification detail
function showNotificationDetail(id) {
    // Find notification data from the row
    const row = document.querySelector(`[data-notification-id="${id}"]`);
    if (!row) return;
    
    const title = row.querySelector('td:nth-child(2) strong').textContent;
    const message = row.querySelector('.notification-message').textContent.replace('Baca selengkapnya', '').trim();
    const status = row.querySelector('td:first-child .badge').textContent.trim();
    const type = row.querySelector('td:nth-child(2) .badge').textContent.trim();
    const priority = row.querySelector('td:nth-child(4) .badge').textContent.trim();
    const time = row.querySelector('td:nth-child(5) small').textContent.trim();
    
    // Update modal content
    document.getElementById('notificationTitle').textContent = title;
    document.getElementById('notificationContent').innerHTML = `<p class="lead">${message}</p>`;
    document.getElementById('notificationStatus').innerHTML = `<span class="badge bg-secondary">${status}</span>`;
    document.getElementById('notificationType').innerHTML = `<span class="badge bg-info">${type}</span>`;
    document.getElementById('notificationPriority').innerHTML = `<span class="badge bg-warning">${priority}</span>`;
    document.getElementById('notificationTime').textContent = time;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('notificationModal')).show();
}

// Show full message
function showFullMessage(id) {
    // This would typically fetch the full message from the server
    // For now, we'll show it in the detail modal
    showNotificationDetail(id);
}

// Update notification stats
function updateNotificationStats() {
    // Update the stats cards
    const unreadCount = document.querySelectorAll('.notification-row.table-warning').length;
    const totalCount = document.querySelectorAll('.notification-row').length;
    const readCount = totalCount - unreadCount;
    
    // Update stats in the cards (if they exist)
    const unreadCard = document.querySelector('.bg-warning h4');
    const readCard = document.querySelector('.bg-success h4');
    const totalCard = document.querySelector('.bg-primary h4');
    
    if (unreadCard) unreadCard.textContent = unreadCount;
    if (readCard) readCard.textContent = readCount;
    if (totalCard) totalCard.textContent = totalCount;
}
</script>
@endsection
