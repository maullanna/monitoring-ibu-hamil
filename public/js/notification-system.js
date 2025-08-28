// Notification System untuk Monitoring Ibu Hamil
class NotificationSystem {
    constructor() {
        this.isSupported = 'Notification' in window;
        this.permission = 'default';
        this.serviceWorkerRegistration = null;
        this.shownNotifications = new Set(); // Track shown notifications
        this.init();
    }

    async init() {
        if (this.isSupported) {
            this.permission = await this.requestPermission();
            if (this.permission === 'granted') {
                await this.registerServiceWorker();
                this.startNotificationCheck();
            }
        }
        
        // Check for unread notifications immediately on page load
        this.checkUnreadNotifications();
    }

    async requestPermission() {
        if (Notification.permission === 'granted') {
            return 'granted';
        } else if (Notification.permission === 'denied') {
            return 'denied';
        } else {
            const permission = await Notification.requestPermission();
            return permission;
        }
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                this.serviceWorkerRegistration = await navigator.serviceWorker.register('/sw-notification.js');
                console.log('Service Worker registered:', this.serviceWorkerRegistration);
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    startNotificationCheck() {
        // Check notifikasi setiap 30 detik
        setInterval(() => {
            this.checkNewNotifications();
        }, 30000);

        // Check notifikasi saat halaman load
        this.checkNewNotifications();
    }

    // Check unread notifications for popup display
    async checkUnreadNotifications() {
        try {
            const response = await fetch('/user/notifikasi/unread-count');
            const data = await response.json();
            
            if (data.count > 0) {
                // Get latest unread notifications for popup
                const notificationsResponse = await fetch('/user/notifikasi/latest');
                const notifications = await notificationsResponse.json();
                
                // Show popup for each unread notification
                notifications.forEach(notification => {
                    if (!this.shownNotifications.has(notification.id)) {
                        this.showWelcomePopup(notification);
                        this.shownNotifications.add(notification.id);
                    }
                });
            }
        } catch (error) {
            console.error('Error checking unread notifications:', error);
        }
    }

    // Show welcome popup for unread notifications
    showWelcomePopup(notification) {
        const popup = document.createElement('div');
        popup.className = 'welcome-notification-popup';
        popup.innerHTML = `
            <div class="welcome-popup-content">
                <div class="welcome-popup-header ${notification.tipe || 'info'}">
                    <i class="fas fa-bell text-white me-2"></i>
                    <span class="welcome-popup-title">${notification.judul}</span>
                    <button type="button" class="welcome-popup-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="welcome-popup-body">
                    <p>${notification.pesan}</p>
                    <div class="welcome-popup-actions">
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='/user/notifikasi'">
                            <i class="fas fa-eye me-1"></i>Baca Detail
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(popup);

        // Auto remove setelah 15 detik
        setTimeout(() => {
            if (popup.parentElement) {
                popup.remove();
            }
        }, 15000);
    }

    async checkNewNotifications() {
        try {
            const response = await fetch('/user/notifikasi/latest');
            const notifications = await response.json();
            
            notifications.forEach(notification => {
                if (!this.shownNotifications.has(notification.id)) {
                    this.showNotification(notification);
                    this.shownNotifications.add(notification.id);
                }
            });
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    showNotification(notification) {
        if (this.permission === 'granted' && this.serviceWorkerRegistration) {
            // Gunakan Service Worker untuk push notification
            this.serviceWorkerRegistration.showNotification(notification.judul, {
                body: notification.pesan,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                vibrate: [200, 100, 200],
                data: {
                    url: notification.action_url || '/user/notifikasi',
                    notification_id: notification.id
                },
                actions: [
                    {
                        action: 'view',
                        title: 'Lihat',
                        icon: '/favicon.ico'
                    },
                    {
                        action: 'close',
                        title: 'Tutup',
                        icon: '/favicon.ico'
                    }
                ]
            });
        } else {
            // Fallback ke browser notification
            this.showBrowserNotification(notification);
        }
    }

    showBrowserNotification(notification) {
        if (this.permission === 'granted') {
            const browserNotification = new Notification(notification.judul, {
                body: notification.pesan,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                vibrate: [200, 100, 200],
                tag: `notification-${notification.id}`,
                requireInteraction: notification.prioritas === 'urgent'
            });

            browserNotification.onclick = function() {
                window.focus();
                if (notification.action_url) {
                    window.location.href = notification.action_url;
                } else {
                    window.location.href = '/user/notifikasi';
                }
                browserNotification.close();
            };

            // Auto close setelah 10 detik (kecuali urgent)
            if (notification.prioritas !== 'urgent') {
                setTimeout(() => {
                    browserNotification.close();
                }, 10000);
            }
        }
    }

    // Show toast notification di halaman
    showToastNotification(notification) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-notification-${notification.tipe || 'info'}`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong>${notification.judul}</strong>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="toast-body">
                ${notification.pesan}
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove setelah 5 detik
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Request permission dengan UI yang menarik
    async requestPermissionWithUI() {
        if (this.permission === 'default') {
            const modal = this.createPermissionModal();
            document.body.appendChild(modal);
            
            return new Promise((resolve) => {
                modal.querySelector('.btn-grant').onclick = async () => {
                    const permission = await this.requestPermission();
                    modal.remove();
                    resolve(permission);
                };
                
                modal.querySelector('.btn-deny').onclick = () => {
                    modal.remove();
                    resolve('denied');
                };
            });
        }
        return this.permission;
    }

    createPermissionModal() {
        const modal = document.createElement('div');
        modal.className = 'permission-modal';
        modal.innerHTML = `
            <div class="permission-modal-content">
                <div class="permission-modal-header">
                    <i class="fas fa-bell text-primary"></i>
                    <h4>Aktifkan Notifikasi</h4>
                </div>
                <div class="permission-modal-body">
                    <p>Dapatkan notifikasi penting tentang kesehatan Anda dan bayi:</p>
                    <ul>
                        <li>Pengingat minum air</li>
                        <li>Tips kesehatan</li>
                        <li>Jadwal pemeriksaan</li>
                        <li>Update dari dokter</li>
                    </ul>
                </div>
                <div class="permission-modal-footer">
                    <button class="btn btn-primary btn-grant">
                        <i class="fas fa-check me-2"></i>Aktifkan
                    </button>
                    <button class="btn btn-secondary btn-deny">
                        <i class="fas fa-times me-2"></i>Nanti
                    </button>
                </div>
            </div>
        `;
        return modal;
    }
}

// Initialize notification system
document.addEventListener('DOMContentLoaded', function() {
    window.notificationSystem = new NotificationSystem();
});

// Export untuk penggunaan global
window.NotificationSystem = NotificationSystem;
