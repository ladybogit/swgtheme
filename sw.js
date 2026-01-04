/**
 * Service Worker for PWA Support
 * Provides offline functionality and caching
 */

const CACHE_VERSION = 'swgtheme-v1.0.0';
const CACHE_STATIC = `${CACHE_VERSION}-static`;
const CACHE_DYNAMIC = `${CACHE_VERSION}-dynamic`;
const CACHE_IMAGES = `${CACHE_VERSION}-images`;

// Assets to cache on install
const STATIC_ASSETS = [
	'/',
	'/offline.html',
	'/wp-content/themes/swgtheme/css/main.css',
	'/wp-content/themes/swgtheme/css/bootstrap.min.css',
	'/wp-content/themes/swgtheme/js/bootstrap.bundle.min.js',
	'/wp-content/themes/swgtheme/js/theme-features.js',
];

// Install Event - Cache static assets
self.addEventListener('install', (event) => {
	console.log('[Service Worker] Installing...');
	
	event.waitUntil(
		caches.open(CACHE_STATIC)
			.then((cache) => {
				console.log('[Service Worker] Caching static assets');
				return cache.addAll(STATIC_ASSETS);
			})
			.catch((error) => {
				console.error('[Service Worker] Cache failed:', error);
			})
	);
	
	self.skipWaiting();
});

// Activate Event - Clean up old caches
self.addEventListener('activate', (event) => {
	console.log('[Service Worker] Activating...');
	
	event.waitUntil(
		caches.keys().then((cacheNames) => {
			return Promise.all(
				cacheNames.map((cacheName) => {
					if (cacheName.startsWith('swgtheme-') && cacheName !== CACHE_STATIC && cacheName !== CACHE_DYNAMIC && cacheName !== CACHE_IMAGES) {
						console.log('[Service Worker] Deleting old cache:', cacheName);
						return caches.delete(cacheName);
					}
				})
			);
		})
	);
	
	return self.clients.claim();
});

// Fetch Event - Network first, then cache
self.addEventListener('fetch', (event) => {
	const { request } = event;
	const url = new URL(request.url);
	
	// Skip cross-origin requests
	if (url.origin !== location.origin) {
		return;
	}
	
	// Skip admin and login pages
	if (url.pathname.includes('/wp-admin') || url.pathname.includes('/wp-login')) {
		return;
	}
	
	// Handle different request types
	if (request.destination === 'image') {
		event.respondWith(cacheFirstStrategy(request, CACHE_IMAGES));
	} else if (request.destination === 'script' || request.destination === 'style') {
		event.respondWith(cacheFirstStrategy(request, CACHE_STATIC));
	} else {
		event.respondWith(networkFirstStrategy(request));
	}
});

// Network First Strategy (for HTML pages)
async function networkFirstStrategy(request) {
	try {
		const response = await fetch(request);
		
		if (response.ok) {
			const cache = await caches.open(CACHE_DYNAMIC);
			cache.put(request, response.clone());
		}
		
		return response;
	} catch (error) {
		const cachedResponse = await caches.match(request);
		
		if (cachedResponse) {
			return cachedResponse;
		}
		
		// Return offline page for navigation requests
		if (request.mode === 'navigate') {
			return caches.match('/offline.html');
		}
		
		return new Response('Offline', {
			status: 503,
			statusText: 'Service Unavailable'
		});
	}
}

// Cache First Strategy (for images, CSS, JS)
async function cacheFirstStrategy(request, cacheName) {
	const cachedResponse = await caches.match(request);
	
	if (cachedResponse) {
		return cachedResponse;
	}
	
	try {
		const response = await fetch(request);
		
		if (response.ok) {
			const cache = await caches.open(cacheName);
			cache.put(request, response.clone());
		}
		
		return response;
	} catch (error) {
		console.error('[Service Worker] Fetch failed:', error);
		return new Response('Failed to fetch', { status: 503 });
	}
}

// Background Sync for offline actions
self.addEventListener('sync', (event) => {
	console.log('[Service Worker] Background sync:', event.tag);
	
	if (event.tag === 'sync-comments') {
		event.waitUntil(syncComments());
	}
});

async function syncComments() {
	// Placeholder for syncing offline comments
	console.log('[Service Worker] Syncing offline comments...');
}

// Push Notifications
self.addEventListener('push', (event) => {
	const data = event.data ? event.data.json() : {};
	
	const title = data.title || 'Lords of the Outer Rim';
	const options = {
		body: data.body || 'New update available',
		icon: '/wp-content/themes/swgtheme/images/icon-192x192.png',
		badge: '/wp-content/themes/swgtheme/images/icon-96x96.png',
		vibrate: [200, 100, 200],
		tag: data.tag || 'notification',
		data: data,
		actions: [
			{
				action: 'view',
				title: 'View',
				icon: '/wp-content/themes/swgtheme/images/icon-96x96.png'
			},
			{
				action: 'close',
				title: 'Close'
			}
		]
	};
	
	event.waitUntil(
		self.registration.showNotification(title, options)
	);
});

// Notification Click
self.addEventListener('notificationclick', (event) => {
	event.notification.close();
	
	if (event.action === 'view' || !event.action) {
		const urlToOpen = event.notification.data?.url || '/';
		
		event.waitUntil(
			clients.matchAll({ type: 'window', includeUncontrolled: true })
				.then((windowClients) => {
					// Check if there's already a window open
					for (let client of windowClients) {
						if (client.url === urlToOpen && 'focus' in client) {
							return client.focus();
						}
					}
					
					// Open new window
					if (clients.openWindow) {
						return clients.openWindow(urlToOpen);
					}
				})
		);
	}
});

// Message Event - Handle messages from main thread
self.addEventListener('message', (event) => {
	if (event.data && event.data.type === 'SKIP_WAITING') {
		self.skipWaiting();
	}
	
	if (event.data && event.data.type === 'CACHE_URLS') {
		const urls = event.data.urls || [];
		event.waitUntil(
			caches.open(CACHE_DYNAMIC)
				.then(cache => cache.addAll(urls))
		);
	}
});
