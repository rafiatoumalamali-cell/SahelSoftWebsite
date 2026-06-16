// Service Worker for SahelSoft PWA
const CACHE_NAME = 'sahelsoft-v2';
const urlsToCache = [
    '/',
    '/public/css/style.css',
    '/public/js/main.js',
    '/public/images/logo.png',
    '/public/uploads/placeholder.jpg',
    '/images/icon-192x192.svg',
    '/images/icon-512x512.svg',
    '/site.webmanifest'
];

// Install event - cache resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // Clone request for network fetch
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    response => {
                        // Check if valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone response for caching
                        const responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(cache => {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    }
                ).catch(() => {
                    // Network failed, try to serve from cache
                    return caches.match(event.request);
                })
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Push notification event
self.addEventListener('push', event => {
    const options = {
        body: event.data.text(),
        icon: '/public/images/logo.png',
        badge: '/public/images/badge.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/public/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/public/images/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('SahelSoft Notification', options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'explore') {
        // Open the app and navigate to relevant page
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    } else if (event.action === 'close') {
        // Just close the notification
        event.notification.close();
    } else {
        // Default action - open app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Background sync for offline actions
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    // Handle offline actions that need to be synced
    return fetch('/api/sync-offline-actions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            timestamp: Date.now()
        })
    });
}

// Periodic background sync
self.addEventListener('periodicsync', event => {
    if (event.tag === 'get-latest-data') {
        event.waitUntil(
            caches.open(CACHE_NAME).then(cache => {
                return fetch('/api/latest-data').then(response => {
                    if (response.ok) {
                        return cache.put('/api/latest-data', response);
                    }
                });
            })
        );
    }
});
