/**
 * PWA Initialization Script
 */

(function() {
	'use strict';
	
	// Check for service worker support
	if ('serviceWorker' in navigator) {
		window.addEventListener('load', function() {
			registerServiceWorker();
		});
	}
	
	// Register Service Worker
	async function registerServiceWorker() {
		try {
			// Service Worker at root level for full site control
			const swPath = '/sw.js';
			
			const registration = await navigator.serviceWorker.register(swPath, {
				scope: '/'
			});
			
			console.log('[PWA] Service Worker registered:', registration.scope);
			
			// Check for updates
			registration.addEventListener('updatefound', () => {
				const newWorker = registration.installing;
				
				newWorker.addEventListener('statechange', () => {
					if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
						showUpdateNotification();
					}
				});
			});
			
			// Check for updates every hour
			setInterval(() => {
				registration.update();
			}, 60 * 60 * 1000);
			
		} catch (error) {
			console.warn('[PWA] Service Worker registration failed:', error.message || error);
			// Silently fail - PWA features just won't be available
		}
	}
	
	// Show update notification
	function showUpdateNotification() {
		const notification = document.createElement('div');
		notification.className = 'pwa-update-notification';
		notification.innerHTML = `
			<div class="pwa-update-content">
				<p>A new version is available!</p>
				<button onclick="window.location.reload()" class="btn btn-sm btn-primary">Update Now</button>
				<button onclick="this.closest('.pwa-update-notification').remove()" class="btn btn-sm btn-secondary">Later</button>
			</div>
		`;
		document.body.appendChild(notification);
	}
	
	// Install prompt
	let deferredPrompt;
	
	window.addEventListener('beforeinstallprompt', (e) => {
		e.preventDefault();
		deferredPrompt = e;
		showInstallPromotion();
	});
	
	function showInstallPromotion() {
		const installBanner = document.createElement('div');
		installBanner.className = 'pwa-install-banner';
		installBanner.innerHTML = `
			<div class="pwa-install-content">
				<div class="pwa-install-icon">📱</div>
				<div class="pwa-install-text">
					<strong>Install App</strong>
					<p>Get the full experience by installing our app</p>
				</div>
				<button id="pwa-install-button" class="btn btn-primary btn-sm">Install</button>
				<button id="pwa-dismiss-button" class="btn btn-secondary btn-sm">×</button>
			</div>
		`;
		
		document.body.appendChild(installBanner);
		
		document.getElementById('pwa-install-button').addEventListener('click', async () => {
			if (deferredPrompt) {
				deferredPrompt.prompt();
				const { outcome } = await deferredPrompt.userChoice;
				console.log('[PWA] User choice:', outcome);
				deferredPrompt = null;
				installBanner.remove();
			}
		});
		
		document.getElementById('pwa-dismiss-button').addEventListener('click', () => {
			installBanner.remove();
			localStorage.setItem('pwa-install-dismissed', Date.now());
		});
		
		// Don't show if dismissed recently
		const dismissed = localStorage.getItem('pwa-install-dismissed');
		if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) {
			installBanner.remove();
		}
	}
	
	// Track app installation
	window.addEventListener('appinstalled', () => {
		console.log('[PWA] App installed successfully');
		deferredPrompt = null;
		
		// Track with analytics if available
		if (typeof gtag !== 'undefined') {
			gtag('event', 'app_installed', {
				event_category: 'PWA',
				event_label: 'App Installed'
			});
		}
	});
	
	// Detect if running as PWA
	if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
		document.body.classList.add('pwa-mode');
		console.log('[PWA] Running in standalone mode');
	}
	
})();
