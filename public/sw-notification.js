// Service Worker untuk Push Notifications
self.addEventListener('push', function(event) {
    if (event.data) {
        const data = event.data.json();
        
        const options = {
            body: data.pesan,
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            vibrate: [200, 100, 200],
            data: {
                url: data.action_url || '/user/notifikasi',
                notification_id: data.id
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
        };

        event.waitUntil(
            self.registration.showNotification(data.judul, options)
        );
    }
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

// Handle notification close
self.addEventListener('notificationclose', function(event) {
    console.log('Notification closed:', event.notification.data);
});
