/**
 * Loading States and Skeleton Screens
 * Improves perceived performance with visual feedback
 */

(function() {
	'use strict';
	
	class LoadingStates {
		constructor() {
			this.init();
		}
		
		init() {
			this.addStyles();
			this.createSkeletonScreens();
			this.handleImageLoading();
			this.handleFormSubmissions();
			this.handleAjaxRequests();
		}
		
		addStyles() {
			if (document.getElementById('loading-states-styles')) return;
			
			const style = document.createElement('style');
			style.id = 'loading-states-styles';
			style.textContent = `
				/* Skeleton screens */
				.skeleton {
					background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
					background-size: 200% 100%;
					animation: skeleton-loading 1.5s infinite;
					border-radius: 4px;
				}
				
				[data-theme="dark"] .skeleton {
					background: linear-gradient(90deg, #2d2d2d 25%, #3d3d3d 50%, #2d2d2d 75%);
					background-size: 200% 100%;
				}
				
				@keyframes skeleton-loading {
					0% {
						background-position: 200% 0;
					}
					100% {
						background-position: -200% 0;
					}
				}
				
				.skeleton-text {
					height: 1em;
					margin-bottom: 0.5em;
				}
				
				.skeleton-text:last-child {
					width: 60%;
				}
				
				.skeleton-title {
					height: 2em;
					width: 80%;
					margin-bottom: 1em;
				}
				
				.skeleton-avatar {
					width: 40px;
					height: 40px;
					border-radius: 50%;
				}
				
				.skeleton-image {
					width: 100%;
					padding-bottom: 56.25%; /* 16:9 aspect ratio */
					position: relative;
				}
				
				.skeleton-card {
					padding: 20px;
					border: 1px solid #e0e0e0;
					border-radius: 8px;
					margin-bottom: 20px;
				}
				
				[data-theme="dark"] .skeleton-card {
					border-color: #3d3d3d;
				}
				
				/* Loading spinner */
				.loading-spinner {
					display: inline-block;
					width: 20px;
					height: 20px;
					border: 3px solid rgba(0, 0, 0, 0.1);
					border-top-color: #dc3545;
					border-radius: 50%;
					animation: spin 0.8s linear infinite;
				}
				
				[data-theme="dark"] .loading-spinner {
					border-color: rgba(255, 255, 255, 0.1);
					border-top-color: #dc3545;
				}
				
				@keyframes spin {
					to {
						transform: rotate(360deg);
					}
				}
				
				.loading-spinner.large {
					width: 40px;
					height: 40px;
					border-width: 4px;
				}
				
				.loading-spinner.small {
					width: 16px;
					height: 16px;
					border-width: 2px;
				}
				
				/* Loading overlay */
				.loading-overlay {
					position: absolute;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					background: rgba(255, 255, 255, 0.8);
					display: flex;
					align-items: center;
					justify-content: center;
					z-index: 10;
					backdrop-filter: blur(2px);
				}
				
				[data-theme="dark"] .loading-overlay {
					background: rgba(0, 0, 0, 0.8);
				}
				
				/* Button loading state */
				.btn-loading {
					position: relative;
					color: transparent !important;
					pointer-events: none;
				}
				
				.btn-loading::after {
					content: '';
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					width: 16px;
					height: 16px;
					border: 2px solid rgba(255, 255, 255, 0.3);
					border-top-color: #fff;
					border-radius: 50%;
					animation: spin 0.8s linear infinite;
				}
				
				/* Image loading */
				.image-loading {
					position: relative;
					background: #f0f0f0;
				}
				
				.image-loading::after {
					content: '';
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					width: 40px;
					height: 40px;
					border: 4px solid rgba(0, 0, 0, 0.1);
					border-top-color: #dc3545;
					border-radius: 50%;
					animation: spin 1s linear infinite;
				}
				
				.image-loaded {
					animation: fadeIn 0.3s;
				}
				
				@keyframes fadeIn {
					from {
						opacity: 0;
					}
					to {
						opacity: 1;
					}
				}
				
				/* Progress bar */
				.progress-bar-container {
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					height: 3px;
					background: rgba(0, 0, 0, 0.1);
					z-index: 999999;
					display: none;
				}
				
				.progress-bar-container.active {
					display: block;
				}
				
				.progress-bar {
					height: 100%;
					background: #dc3545;
					width: 0%;
					transition: width 0.3s;
				}
				
				.progress-bar.indeterminate {
					width: 30%;
					animation: indeterminate 1.5s infinite;
				}
				
				@keyframes indeterminate {
					0% {
						transform: translateX(-100%);
					}
					100% {
						transform: translateX(400%);
					}
				}
				
				/* Reduced motion */
				@media (prefers-reduced-motion: reduce) {
					.skeleton,
					.loading-spinner,
					.progress-bar,
					.btn-loading::after,
					.image-loading::after {
						animation: none;
					}
					
					.skeleton {
						background: #e0e0e0;
					}
					
					[data-theme="dark"] .skeleton {
						background: #3d3d3d;
					}
				}
			`;
			document.head.appendChild(style);
		}
		
		createSkeletonScreens() {
			// Create skeleton for posts while they're loading
			document.querySelectorAll('[data-skeleton]').forEach(el => {
				const type = el.dataset.skeleton;
				const skeleton = this.generateSkeleton(type);
				el.innerHTML = skeleton;
			});
		}
		
		generateSkeleton(type) {
			const skeletons = {
				post: `
					<div class="skeleton-card">
						<div class="skeleton skeleton-image"></div>
						<div class="skeleton skeleton-title"></div>
						<div class="skeleton skeleton-text"></div>
						<div class="skeleton skeleton-text"></div>
						<div class="skeleton skeleton-text"></div>
					</div>
				`,
				card: `
					<div class="skeleton skeleton-image"></div>
					<div class="skeleton skeleton-title"></div>
					<div class="skeleton skeleton-text"></div>
					<div class="skeleton skeleton-text"></div>
				`,
				list: `
					<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
						<div class="skeleton skeleton-avatar"></div>
						<div style="flex: 1;">
							<div class="skeleton skeleton-text" style="width: 60%;"></div>
							<div class="skeleton skeleton-text" style="width: 40%;"></div>
						</div>
					</div>
				`,
				text: `
					<div class="skeleton skeleton-text"></div>
					<div class="skeleton skeleton-text"></div>
					<div class="skeleton skeleton-text"></div>
				`
			};
			
			return skeletons[type] || skeletons.card;
		}
		
		handleImageLoading() {
			document.querySelectorAll('img[data-src], img[loading="lazy"]').forEach(img => {
				if (!img.complete) {
					img.classList.add('image-loading');
					
					img.addEventListener('load', function() {
						this.classList.remove('image-loading');
						this.classList.add('image-loaded');
					}, { once: true });
					
					img.addEventListener('error', function() {
						this.classList.remove('image-loading');
						this.classList.add('image-error');
					}, { once: true });
				}
			});
		}
		
		handleFormSubmissions() {
			document.querySelectorAll('form').forEach(form => {
				form.addEventListener('submit', (e) => {
					const submitBtn = form.querySelector('[type="submit"]');
					if (submitBtn && !submitBtn.classList.contains('no-loading')) {
						submitBtn.classList.add('btn-loading');
						submitBtn.disabled = true;
					}
				});
			});
		}
		
		handleAjaxRequests() {
			const progressBar = document.createElement('div');
			progressBar.className = 'progress-bar-container';
			progressBar.innerHTML = '<div class="progress-bar indeterminate"></div>';
			document.body.appendChild(progressBar);
			
			// Intercept fetch requests
			const originalFetch = window.fetch;
			window.fetch = function(...args) {
				progressBar.classList.add('active');
				
				return originalFetch.apply(this, args).finally(() => {
					setTimeout(() => {
						progressBar.classList.remove('active');
					}, 300);
				});
			};
			
			// jQuery AJAX if available
			if (typeof jQuery !== 'undefined') {
				jQuery(document).ajaxStart(() => {
					progressBar.classList.add('active');
				});
				
				jQuery(document).ajaxStop(() => {
					setTimeout(() => {
						progressBar.classList.remove('active');
					}, 300);
				});
			}
		}
		
		// Public API
		showSpinner(element, size = 'normal') {
			const spinner = document.createElement('div');
			spinner.className = `loading-spinner ${size}`;
			element.appendChild(spinner);
			return spinner;
		}
		
		hideSpinner(spinner) {
			if (spinner && spinner.parentNode) {
				spinner.remove();
			}
		}
		
		showOverlay(element) {
			const overlay = document.createElement('div');
			overlay.className = 'loading-overlay';
			overlay.innerHTML = '<div class="loading-spinner large"></div>';
			element.style.position = 'relative';
			element.appendChild(overlay);
			return overlay;
		}
		
		hideOverlay(overlay) {
			if (overlay && overlay.parentNode) {
				overlay.remove();
			}
		}
	}
	
	// Create global instance
	window.loadingStates = new LoadingStates();
	
})();
