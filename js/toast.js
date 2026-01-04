/**
 * Toast Notifications System
 * Provides elegant user feedback
 */

(function() {
	'use strict';
	
	class ToastNotification {
		constructor() {
			this.container = null;
			this.queue = [];
			this.activeToasts = new Set();
			this.maxToasts = 5;
			this.init();
		}
		
		init() {
			// Create container
			this.container = document.createElement('div');
			this.container.id = 'toast-container';
			this.container.className = 'toast-container';
			this.container.setAttribute('aria-live', 'polite');
			this.container.setAttribute('aria-atomic', 'true');
			
			// Add styles
			this.addStyles();
			
			// Append to body when DOM is ready
			if (document.body) {
				document.body.appendChild(this.container);
			} else {
				document.addEventListener('DOMContentLoaded', () => {
					document.body.appendChild(this.container);
				});
			}
		}
		
		addStyles() {
			if (document.getElementById('toast-styles')) return;
			
			const style = document.createElement('style');
			style.id = 'toast-styles';
			style.textContent = `
				.toast-container {
					position: fixed;
					top: 20px;
					right: 20px;
					z-index: 999999;
					pointer-events: none;
					max-width: 400px;
				}
				
				@media (max-width: 576px) {
					.toast-container {
						top: 10px;
						right: 10px;
						left: 10px;
						max-width: none;
					}
				}
				
				.toast {
					background: #ffffff;
					color: #333333;
					padding: 16px 20px;
					margin-bottom: 10px;
					border-radius: 8px;
					box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
					display: flex;
					align-items: flex-start;
					gap: 12px;
					min-width: 300px;
					max-width: 100%;
					pointer-events: auto;
					animation: toastSlideIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
					position: relative;
					overflow: hidden;
				}
				
				@media (max-width: 576px) {
					.toast {
						min-width: auto;
					}
				}
				
				.toast.removing {
					animation: toastSlideOut 0.3s ease-in-out forwards;
				}
				
				@keyframes toastSlideIn {
					from {
						transform: translateX(120%);
						opacity: 0;
					}
					to {
						transform: translateX(0);
						opacity: 1;
					}
				}
				
				@keyframes toastSlideOut {
					to {
						transform: translateX(120%);
						opacity: 0;
					}
				}
				
				.toast-icon {
					flex-shrink: 0;
					font-size: 24px;
					line-height: 1;
				}
				
				.toast-content {
					flex: 1;
					min-width: 0;
				}
				
				.toast-title {
					font-weight: 600;
					margin-bottom: 4px;
					font-size: 15px;
				}
				
				.toast-message {
					font-size: 14px;
					line-height: 1.4;
					color: #666666;
					word-wrap: break-word;
				}
				
				.toast-close {
					flex-shrink: 0;
					background: none;
					border: none;
					color: #999999;
					cursor: pointer;
					font-size: 20px;
					padding: 0;
					width: 24px;
					height: 24px;
					display: flex;
					align-items: center;
					justify-content: center;
					border-radius: 4px;
					transition: all 0.2s;
				}
				
				.toast-close:hover {
					background: rgba(0, 0, 0, 0.05);
					color: #333333;
				}
				
				.toast-progress {
					position: absolute;
					bottom: 0;
					left: 0;
					height: 3px;
					background: currentColor;
					opacity: 0.3;
					animation: toastProgress linear forwards;
				}
				
				@keyframes toastProgress {
					from {
						width: 100%;
					}
					to {
						width: 0%;
					}
				}
				
				/* Toast types */
				.toast-success {
					border-left: 4px solid #28a745;
				}
				
				.toast-success .toast-icon {
					color: #28a745;
				}
				
				.toast-error {
					border-left: 4px solid #dc3545;
				}
				
				.toast-error .toast-icon {
					color: #dc3545;
				}
				
				.toast-warning {
					border-left: 4px solid #ffc107;
				}
				
				.toast-warning .toast-icon {
					color: #ffc107;
				}
				
				.toast-info {
					border-left: 4px solid #17a2b8;
				}
				
				.toast-info .toast-icon {
					color: #17a2b8;
				}
				
				/* Dark mode support */
				[data-theme="dark"] .toast {
					background: #2d2d2d;
					color: #ffffff;
					box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
				}
				
				[data-theme="dark"] .toast-message {
					color: #cccccc;
				}
				
				[data-theme="dark"] .toast-close {
					color: #999999;
				}
				
				[data-theme="dark"] .toast-close:hover {
					background: rgba(255, 255, 255, 0.1);
					color: #ffffff;
				}
				
				/* Reduced motion */
				@media (prefers-reduced-motion: reduce) {
					.toast {
						animation: none;
					}
					
					.toast.removing {
						opacity: 0;
						transition: opacity 0.2s;
					}
				}
			`;
			document.head.appendChild(style);
		}
		
		getIcon(type) {
			const icons = {
				success: '✓',
				error: '✕',
				warning: '⚠',
				info: 'ℹ'
			};
			return icons[type] || icons.info;
		}
		
		show(options) {
			const {
				type = 'info',
				title = '',
				message = '',
				duration = 5000,
				closable = true
			} = typeof options === 'string' ? { message: options } : options;
			
			// Check if we've hit the max
			if (this.activeToasts.size >= this.maxToasts) {
				this.queue.push({ type, title, message, duration, closable });
				return;
			}
			
			// Create toast element
			const toast = document.createElement('div');
			toast.className = `toast toast-${type}`;
			toast.setAttribute('role', 'alert');
			
			const icon = document.createElement('div');
			icon.className = 'toast-icon';
			icon.textContent = this.getIcon(type);
			
			const content = document.createElement('div');
			content.className = 'toast-content';
			
			if (title) {
				const titleEl = document.createElement('div');
				titleEl.className = 'toast-title';
				titleEl.textContent = title;
				content.appendChild(titleEl);
			}
			
			if (message) {
				const messageEl = document.createElement('div');
				messageEl.className = 'toast-message';
				messageEl.textContent = message;
				content.appendChild(messageEl);
			}
			
			toast.appendChild(icon);
			toast.appendChild(content);
			
			// Close button
			if (closable) {
				const closeBtn = document.createElement('button');
				closeBtn.className = 'toast-close';
				closeBtn.innerHTML = '&times;';
				closeBtn.setAttribute('aria-label', 'Close notification');
				closeBtn.onclick = () => this.remove(toast);
				toast.appendChild(closeBtn);
			}
			
			// Progress bar
			if (duration > 0) {
				const progress = document.createElement('div');
				progress.className = 'toast-progress';
				progress.style.animationDuration = `${duration}ms`;
				toast.appendChild(progress);
			}
			
			// Add to container
			this.container.appendChild(toast);
			this.activeToasts.add(toast);
			
			// Auto-remove
			if (duration > 0) {
				setTimeout(() => this.remove(toast), duration);
			}
		}
		
		remove(toast) {
			if (!toast || !this.activeToasts.has(toast)) return;
			
			toast.classList.add('removing');
			this.activeToasts.delete(toast);
			
			setTimeout(() => {
				toast.remove();
				
				// Show next in queue
				if (this.queue.length > 0) {
					const next = this.queue.shift();
					this.show(next);
				}
			}, 300);
		}
		
		success(message, title = 'Success') {
			this.show({ type: 'success', title, message });
		}
		
		error(message, title = 'Error') {
			this.show({ type: 'error', title, message });
		}
		
		warning(message, title = 'Warning') {
			this.show({ type: 'warning', title, message });
		}
		
		info(message, title = 'Info') {
			this.show({ type: 'info', title, message });
		}
		
		clear() {
			this.activeToasts.forEach(toast => this.remove(toast));
			this.queue = [];
		}
	}
	
	// Create global instance
	window.toast = new ToastNotification();
	
	// Add WordPress admin bar notices to toast
	if (document.querySelector('.notice')) {
		setTimeout(() => {
			document.querySelectorAll('.notice').forEach(notice => {
				const message = notice.textContent.trim();
				const isError = notice.classList.contains('notice-error');
				const isSuccess = notice.classList.contains('notice-success');
				const isWarning = notice.classList.contains('notice-warning');
				
				if (isError) {
					window.toast.error(message);
				} else if (isSuccess) {
					window.toast.success(message);
				} else if (isWarning) {
					window.toast.warning(message);
				} else {
					window.toast.info(message);
				}
			});
		}, 100);
	}
	
})();
