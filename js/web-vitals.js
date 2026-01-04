/**
 * Web Vitals Monitoring
 * Tracks Core Web Vitals (LCP, FID, CLS, TTFB, FCP, INP)
 */

(function() {
	'use strict';
	
	// Core Web Vitals thresholds
	const THRESHOLDS = {
		LCP: { good: 2500, poor: 4000 },
		FID: { good: 100, poor: 300 },
		CLS: { good: 0.1, poor: 0.25 },
		TTFB: { good: 800, poor: 1800 },
		FCP: { good: 1800, poor: 3000 },
		INP: { good: 200, poor: 500 }
	};
	
	// Store metrics
	const metrics = {};
	
	// Get rating for a metric
	function getRating(name, value) {
		const threshold = THRESHOLDS[name];
		if (!threshold) return 'unknown';
		
		if (value <= threshold.good) return 'good';
		if (value <= threshold.poor) return 'needs-improvement';
		return 'poor';
	}
	
	// Send metric to analytics
	function sendToAnalytics(metric) {
		const { name, value, rating, delta, id } = metric;
		
		// Store locally
		metrics[name] = metric;
		
		// Console logging
		console.log(`[Web Vitals] ${name}:`, {
			value: Math.round(value),
			rating,
			delta: Math.round(delta)
		});
		
		// Send to Google Analytics if available
		if (typeof gtag !== 'undefined') {
			gtag('event', name, {
				event_category: 'Web Vitals',
				event_label: id,
				value: Math.round(name === 'CLS' ? value * 1000 : value),
				metric_rating: rating,
				non_interaction: true
			});
		}
		
		// Send to WordPress via AJAX
		if (typeof swgTheme !== 'undefined' && swgTheme.ajaxUrl) {
			fetch(swgTheme.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'track_web_vitals',
					nonce: swgTheme.ajaxNonce,
					metric_name: name,
					metric_value: value,
					metric_rating: rating,
					page_url: window.location.href
				})
			}).catch(error => console.error('[Web Vitals] Tracking error:', error));
		}
		
		// Display visual indicator in development
		if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
			displayMetricIndicator(metric);
		}
	}
	
	// Display visual indicator for metrics
	function displayMetricIndicator(metric) {
		const container = getOrCreateContainer();
		const indicator = document.createElement('div');
		indicator.className = `metric-indicator metric-${metric.rating}`;
		indicator.innerHTML = `
			<strong>${metric.name}</strong>
			<span>${Math.round(metric.value)}${metric.name === 'CLS' ? '' : 'ms'}</span>
		`;
		container.appendChild(indicator);
		
		// Remove after 5 seconds
		setTimeout(() => indicator.remove(), 5000);
	}
	
	// Get or create metrics container
	function getOrCreateContainer() {
		let container = document.getElementById('web-vitals-indicators');
		if (!container) {
			container = document.createElement('div');
			container.id = 'web-vitals-indicators';
			container.style.cssText = `
				position: fixed;
				bottom: 20px;
				right: 20px;
				z-index: 999999;
				max-width: 300px;
			`;
			document.body.appendChild(container);
			
			// Add styles
			const style = document.createElement('style');
			style.textContent = `
				.metric-indicator {
					background: rgba(0, 0, 0, 0.9);
					color: white;
					padding: 10px 15px;
					margin-top: 10px;
					border-radius: 5px;
					display: flex;
					justify-content: space-between;
					align-items: center;
					font-size: 14px;
					border-left: 4px solid;
					animation: slideIn 0.3s;
				}
				
				.metric-good { border-color: #0cce6b; }
				.metric-needs-improvement { border-color: #ffa400; }
				.metric-poor { border-color: #ff4e42; }
				
				@keyframes slideIn {
					from {
						transform: translateX(100%);
						opacity: 0;
					}
					to {
						transform: translateX(0);
						opacity: 1;
					}
				}
			`;
			document.head.appendChild(style);
		}
		return container;
	}
	
	// Load web-vitals library
	function loadWebVitals() {
		// Use CDN version with fallback
		const script = document.createElement('script');
		script.src = 'https://unpkg.com/web-vitals@3/dist/web-vitals.iife.js';
		script.async = true;
		script.onload = initWebVitals;
		script.onerror = () => {
			console.warn('[Web Vitals] Failed to load library - tracking disabled');
			// Continue without web vitals tracking
		};
		
		try {
			document.head.appendChild(script);
		} catch (e) {
			console.warn('[Web Vitals] Could not append script:', e);
		}
	}
	
	// Initialize Web Vitals tracking
	function initWebVitals() {
		if (typeof webVitals === 'undefined') {
			console.error('[Web Vitals] Library not loaded');
			return;
		}
		
		// Track all Core Web Vitals
		webVitals.onLCP((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('LCP', metric.value)
			});
		});
		
		webVitals.onFID((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('FID', metric.value)
			});
		});
		
		webVitals.onCLS((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('CLS', metric.value)
			});
		});
		
		webVitals.onTTFB((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('TTFB', metric.value)
			});
		});
		
		webVitals.onFCP((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('FCP', metric.value)
			});
		});
		
		webVitals.onINP((metric) => {
			sendToAnalytics({
				...metric,
				rating: getRating('INP', metric.value)
			});
		});
		
		console.log('[Web Vitals] Tracking initialized');
	}
	
	// Performance Observer for additional metrics
	function trackPerformanceMetrics() {
		if (!window.PerformanceObserver) return;
		
		// Track long tasks
		try {
			const longTaskObserver = new PerformanceObserver((list) => {
				for (const entry of list.getEntries()) {
					if (entry.duration > 50) {
						console.warn('[Performance] Long task detected:', {
							duration: Math.round(entry.duration),
							startTime: Math.round(entry.startTime)
						});
					}
				}
			});
			longTaskObserver.observe({ entryTypes: ['longtask'] });
		} catch (e) {
			// Long task API not supported
		}
		
		// Track layout shifts
		try {
			const layoutShiftObserver = new PerformanceObserver((list) => {
				for (const entry of list.getEntries()) {
					if (!entry.hadRecentInput && entry.value > 0.1) {
						console.warn('[Performance] Layout shift:', {
							value: entry.value.toFixed(4),
							sources: entry.sources?.map(s => s.node?.tagName)
						});
					}
				}
			});
			layoutShiftObserver.observe({ entryTypes: ['layout-shift'] });
		} catch (e) {
			// Layout shift API not supported
		}
	}
	
	// Export metrics for debugging
	window.getWebVitalsMetrics = () => metrics;
	
	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			// Disabled: External CDN often blocked by ad blockers
			// loadWebVitals();
			trackPerformanceMetrics();
		});
	} else {
		// Disabled: External CDN often blocked by ad blockers
		// loadWebVitals();
		trackPerformanceMetrics();
	}
	
})();
