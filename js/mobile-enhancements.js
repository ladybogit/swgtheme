/**
 * Enhanced Mobile Responsiveness
 * Touch gestures, improved mobile navigation, and responsive enhancements
 */

(function() {
	'use strict';
	
	class MobileEnhancements {
		constructor() {
			this.touchStartX = 0;
			this.touchStartY = 0;
			this.touchEndX = 0;
			this.touchEndY = 0;
			this.swipeThreshold = 50;
			this.init();
		}
		
		init() {
			this.detectTouch();
			this.initSwipeGestures();
			this.initPullToRefresh();
			this.initMobileMenu();
			this.initTouchOptimizations();
			this.handleOrientation();
			this.addMobileStyles();
		}
		
		detectTouch() {
			if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
				document.documentElement.classList.add('touch-device');
			} else {
				document.documentElement.classList.add('no-touch');
			}
		}
		
		initSwipeGestures() {
			let startX, startY;
			
			document.addEventListener('touchstart', (e) => {
				startX = e.touches[0].clientX;
				startY = e.touches[0].clientY;
			}, { passive: true });
			
			document.addEventListener('touchend', (e) => {
				if (!startX || !startY) return;
				
				const endX = e.changedTouches[0].clientX;
				const endY = e.changedTouches[0].clientY;
				
				const diffX = endX - startX;
				const diffY = endY - startY;
				
				// Horizontal swipe
				if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > this.swipeThreshold) {
					if (diffX > 0) {
						this.onSwipeRight();
					} else {
						this.onSwipeLeft();
					}
				}
				
				// Vertical swipe
				if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > this.swipeThreshold) {
					if (diffY > 0) {
						this.onSwipeDown();
					} else {
						this.onSwipeUp();
					}
				}
				
				startX = null;
				startY = null;
			}, { passive: true });
		}
		
		onSwipeRight() {
			// Open mobile menu if closed
			const mobileMenu = document.querySelector('.mobile-menu');
			if (mobileMenu && !mobileMenu.classList.contains('active')) {
				this.toggleMobileMenu();
			}
			
			document.dispatchEvent(new CustomEvent('swipe:right'));
		}
		
		onSwipeLeft() {
			// Close mobile menu if open
			const mobileMenu = document.querySelector('.mobile-menu');
			if (mobileMenu && mobileMenu.classList.contains('active')) {
				this.toggleMobileMenu();
			}
			
			document.dispatchEvent(new CustomEvent('swipe:left'));
		}
		
		onSwipeDown() {
			document.dispatchEvent(new CustomEvent('swipe:down'));
		}
		
		onSwipeUp() {
			// Hide mobile keyboard on swipe up
			if (document.activeElement && document.activeElement.tagName === 'INPUT') {
				document.activeElement.blur();
			}
			
			document.dispatchEvent(new CustomEvent('swipe:up'));
		}
		
		initPullToRefresh() {
			let startY = 0;
			let isPulling = false;
			const pullThreshold = 80;
			const pullIndicator = document.createElement('div');
			pullIndicator.className = 'pull-to-refresh-indicator';
			pullIndicator.innerHTML = '<div class="pull-spinner"></div><div class="pull-text">Pull to refresh</div>';
			
			document.addEventListener('touchstart', (e) => {
				if (window.scrollY === 0) {
					startY = e.touches[0].clientY;
					isPulling = true;
				}
			}, { passive: true });
			
			document.addEventListener('touchmove', (e) => {
				if (!isPulling || window.scrollY > 0) return;
				
				const currentY = e.touches[0].clientY;
				const pullDistance = currentY - startY;
				
				if (pullDistance > 0 && pullDistance < pullThreshold * 2) {
					if (!pullIndicator.parentNode) {
						document.body.insertBefore(pullIndicator, document.body.firstChild);
					}
					
					pullIndicator.style.transform = `translateY(${Math.min(pullDistance, pullThreshold)}px)`;
					pullIndicator.style.opacity = Math.min(pullDistance / pullThreshold, 1);
					
					if (pullDistance >= pullThreshold) {
						pullIndicator.classList.add('ready');
						pullIndicator.querySelector('.pull-text').textContent = 'Release to refresh';
					} else {
						pullIndicator.classList.remove('ready');
						pullIndicator.querySelector('.pull-text').textContent = 'Pull to refresh';
					}
				}
			}, { passive: true });
			
			document.addEventListener('touchend', (e) => {
				if (!isPulling) return;
				
				const pullDistance = (e.changedTouches[0]?.clientY || 0) - startY;
				
				if (pullDistance >= pullThreshold) {
					pullIndicator.classList.add('refreshing');
					pullIndicator.querySelector('.pull-text').textContent = 'Refreshing...';
					
					// Trigger refresh
					setTimeout(() => {
						window.location.reload();
					}, 500);
				} else {
					pullIndicator.remove();
				}
				
				isPulling = false;
				startY = 0;
			}, { passive: true });
		}
		
		initMobileMenu() {
			const menuToggle = document.querySelector('.mobile-menu-toggle, .navbar-toggler');
			if (menuToggle) {
				menuToggle.addEventListener('click', () => this.toggleMobileMenu());
			}
			
			// Close menu when clicking outside
			document.addEventListener('click', (e) => {
				const mobileMenu = document.querySelector('.mobile-menu, .navbar-collapse');
				if (mobileMenu && mobileMenu.classList.contains('active', 'show')) {
					if (!mobileMenu.contains(e.target) && !e.target.closest('.mobile-menu-toggle, .navbar-toggler')) {
						this.toggleMobileMenu();
					}
				}
			});
		}
		
		toggleMobileMenu() {
			const mobileMenu = document.querySelector('.mobile-menu, .navbar-collapse');
			const menuToggle = document.querySelector('.mobile-menu-toggle, .navbar-toggler');
			
			if (mobileMenu) {
				mobileMenu.classList.toggle('active');
				mobileMenu.classList.toggle('show');
				document.body.classList.toggle('mobile-menu-open');
				
				if (menuToggle) {
					menuToggle.setAttribute('aria-expanded', 
						mobileMenu.classList.contains('active') ? 'true' : 'false'
					);
				}
			}
		}
		
		initTouchOptimizations() {
			// Increase touch target sizes
			document.querySelectorAll('a, button, input, select, textarea').forEach(el => {
				const rect = el.getBoundingClientRect();
				if (rect.height < 44 || rect.width < 44) {
					el.style.minHeight = '44px';
					el.style.minWidth = '44px';
				}
			});
			
			// Prevent double-tap zoom on buttons
			document.querySelectorAll('button, a').forEach(el => {
				el.addEventListener('touchend', (e) => {
					e.preventDefault();
					el.click();
				}, { passive: false });
			});
			
			// Fast click for links
			let lastTap = 0;
			document.addEventListener('touchend', (e) => {
				const currentTime = new Date().getTime();
				const tapLength = currentTime - lastTap;
				
				if (tapLength < 500 && tapLength > 0) {
					e.preventDefault();
				}
				
				lastTap = currentTime;
			}, { passive: false });
		}
		
		handleOrientation() {
			const updateOrientation = () => {
				if (window.innerHeight > window.innerWidth) {
					document.documentElement.classList.add('portrait');
					document.documentElement.classList.remove('landscape');
				} else {
					document.documentElement.classList.add('landscape');
					document.documentElement.classList.remove('portrait');
				}
			};
			
			window.addEventListener('orientationchange', updateOrientation);
			window.addEventListener('resize', updateOrientation);
			updateOrientation();
		}
		
		addMobileStyles() {
			if (document.getElementById('mobile-enhancement-styles')) return;
			
			const style = document.createElement('style');
			style.id = 'mobile-enhancement-styles';
			style.textContent = `
				/* Touch device optimizations */
				.touch-device a,
				.touch-device button {
					-webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
					touch-action: manipulation;
				}
				
				.touch-device * {
					-webkit-touch-callout: none;
				}
				
				/* Pull to refresh */
				.pull-to-refresh-indicator {
					position: fixed;
					top: -80px;
					left: 0;
					right: 0;
					height: 80px;
					display: flex;
					align-items: center;
					justify-content: center;
					gap: 10px;
					background: linear-gradient(to bottom, rgba(0,0,0,0.05), transparent);
					z-index: 999998;
					transition: all 0.3s;
					opacity: 0;
				}
				
				.pull-spinner {
					width: 20px;
					height: 20px;
					border: 2px solid rgba(0, 0, 0, 0.1);
					border-top-color: #dc3545;
					border-radius: 50%;
					animation: spin 1s linear infinite;
				}
				
				.pull-to-refresh-indicator.ready .pull-spinner {
					border-top-color: #28a745;
				}
				
				.pull-to-refresh-indicator.refreshing .pull-spinner {
					border-top-color: #007bff;
				}
				
				@keyframes spin {
					to { transform: rotate(360deg); }
				}
				
				.pull-text {
					font-size: 14px;
					color: #666;
				}
				
				/* Mobile menu overlay */
				.mobile-menu-open {
					overflow: hidden;
				}
				
				.mobile-menu-open::before {
					content: '';
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					background: rgba(0, 0, 0, 0.5);
					z-index: 999;
					animation: fadeIn 0.3s;
				}
				
				@keyframes fadeIn {
					from { opacity: 0; }
					to { opacity: 1; }
				}
				
				/* Responsive breakpoint indicators (dev only) */
				@media (max-width: 575px) {
					body::after {
						content: 'XS';
					}
				}
				
				@media (min-width: 576px) and (max-width: 767px) {
					body::after {
						content: 'SM';
					}
				}
				
				@media (min-width: 768px) and (max-width: 991px) {
					body::after {
						content: 'MD';
					}
				}
				
				@media (min-width: 992px) and (max-width: 1199px) {
					body::after {
						content: 'LG';
					}
				}
				
				@media (min-width: 1200px) {
					body::after {
						content: 'XL';
					}
				}
				
				body::after {
					display: none;
					position: fixed;
					bottom: 10px;
					left: 10px;
					background: rgba(0, 0, 0, 0.8);
					color: white;
					padding: 5px 10px;
					border-radius: 3px;
					font-size: 12px;
					z-index: 999999;
				}
				
				body.show-breakpoints::after {
					display: block;
				}
				
				/* Landscape orientation warning for small screens */
				@media (max-width: 767px) and (orientation: landscape) {
					.landscape-notice {
						position: fixed;
						top: 0;
						left: 0;
						right: 0;
						background: #ffc107;
						color: #000;
						padding: 10px;
						text-align: center;
						font-size: 14px;
						z-index: 999999;
					}
				}
			`;
			document.head.appendChild(style);
		}
	}
	
	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			new MobileEnhancements();
		});
	} else {
		new MobileEnhancements();
	}
	
	// Debug: Show breakpoint indicator
	// Uncomment in development:
	// document.body.classList.add('show-breakpoints');
	
})();
