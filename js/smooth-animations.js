/**
 * Smooth Scrolling and Animations
 * Entrance animations, scroll effects, and micro-interactions
 */

(function() {
	'use strict';
	
	class SmoothAnimations {
		constructor() {
			this.observedElements = new Set();
			this.init();
		}
		
		init() {
			this.addStyles();
			this.initSmoothScroll();
			this.initScrollAnimations();
			this.initParallax();
			this.initCounters();
			this.initHoverEffects();
			this.initScrollToTop();
		}
		
		addStyles() {
			if (document.getElementById('smooth-animations-styles')) return;
			
			const style = document.createElement('style');
			style.id = 'smooth-animations-styles';
			style.textContent = `
				/* Smooth scrolling */
				html {
					scroll-behavior: smooth;
				}
				
				@media (prefers-reduced-motion: reduce) {
					html {
						scroll-behavior: auto;
					}
				}
				
				/* Fade in animations */
				.animate-fade-in {
					opacity: 0;
					transition: opacity 0.6s ease-out;
				}
				
				.animate-fade-in.animated {
					opacity: 1;
				}
				
				.animate-slide-up {
					opacity: 0;
					transform: translateY(30px);
					transition: opacity 0.6s ease-out, transform 0.6s ease-out;
				}
				
				.animate-slide-up.animated {
					opacity: 1;
					transform: translateY(0);
				}
				
				.animate-slide-left {
					opacity: 0;
					transform: translateX(30px);
					transition: opacity 0.6s ease-out, transform 0.6s ease-out;
				}
				
				.animate-slide-left.animated {
					opacity: 1;
					transform: translateX(0);
				}
				
				.animate-slide-right {
					opacity: 0;
					transform: translateX(-30px);
					transition: opacity 0.6s ease-out, transform 0.6s ease-out;
				}
				
				.animate-slide-right.animated {
					opacity: 1;
					transform: translateX(0);
				}
				
				.animate-scale {
					opacity: 0;
					transform: scale(0.9);
					transition: opacity 0.6s ease-out, transform 0.6s ease-out;
				}
				
				.animate-scale.animated {
					opacity: 1;
					transform: scale(1);
				}
				
				/* Stagger delays */
				.animate-stagger-1 { transition-delay: 0.1s; }
				.animate-stagger-2 { transition-delay: 0.2s; }
				.animate-stagger-3 { transition-delay: 0.3s; }
				.animate-stagger-4 { transition-delay: 0.4s; }
				.animate-stagger-5 { transition-delay: 0.5s; }
				
				/* Parallax */
				.parallax {
					transition: transform 0.1s ease-out;
					will-change: transform;
				}
				
				/* Hover effects */
				.hover-lift {
					transition: transform 0.3s ease, box-shadow 0.3s ease;
				}
				
				.hover-lift:hover {
					transform: translateY(-5px);
					box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
				}
				
				.hover-scale {
					transition: transform 0.3s ease;
				}
				
				.hover-scale:hover {
					transform: scale(1.05);
				}
				
				.hover-glow {
					transition: box-shadow 0.3s ease;
				}
				
				.hover-glow:hover {
					box-shadow: 0 0 20px rgba(220, 53, 69, 0.5);
				}
				
				/* Button ripple effect */
				.btn, button, .button {
					position: relative;
					overflow: hidden;
				}
				
				.ripple {
					position: absolute;
					border-radius: 50%;
					background: rgba(255, 255, 255, 0.5);
					transform: scale(0);
					animation: ripple-effect 0.6s ease-out;
					pointer-events: none;
				}
				
				@keyframes ripple-effect {
					to {
						transform: scale(4);
						opacity: 0;
					}
				}
				
				/* Scroll to top button */
				.scroll-to-top {
					position: fixed;
					bottom: 30px;
					right: 30px;
					width: 50px;
					height: 50px;
					background: #dc3545;
					color: white;
					border: none;
					border-radius: 50%;
					cursor: pointer;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 24px;
					box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
					opacity: 0;
					visibility: hidden;
					transition: all 0.3s ease;
					z-index: 999;
				}
				
				.scroll-to-top.visible {
					opacity: 1;
					visibility: visible;
				}
				
				.scroll-to-top:hover {
					background: #c82333;
					transform: translateY(-3px);
					box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
				}
				
				.scroll-to-top:active {
					transform: translateY(-1px);
				}
				
				@media (max-width: 768px) {
					.scroll-to-top {
						bottom: 20px;
						right: 20px;
						width: 45px;
						height: 45px;
						font-size: 20px;
					}
				}
				
				/* Progress indicator */
				.scroll-progress {
					position: fixed;
					top: 0;
					left: 0;
					height: 3px;
					background: linear-gradient(90deg, #dc3545, #ff6b6b);
					z-index: 9999;
					transform-origin: left;
					transition: transform 0.1s ease-out;
				}
				
				/* Reduced motion */
				@media (prefers-reduced-motion: reduce) {
					.animate-fade-in,
					.animate-slide-up,
					.animate-slide-left,
					.animate-slide-right,
					.animate-scale,
					.parallax,
					.hover-lift,
					.hover-scale,
					.scroll-to-top,
					.ripple {
						transition: none;
						animation: none;
					}
					
					.animate-fade-in,
					.animate-slide-up,
					.animate-slide-left,
					.animate-slide-right,
					.animate-scale {
						opacity: 1;
						transform: none;
					}
				}
			`;
			document.head.appendChild(style);
		}
		
		initSmoothScroll() {
			// Smooth scroll for anchor links
			document.querySelectorAll('a[href^="#"]').forEach(anchor => {
				anchor.addEventListener('click', (e) => {
					const href = anchor.getAttribute('href');
					if (href === '#' || href === '#0') return;
					
					const target = document.querySelector(href);
					if (target) {
						e.preventDefault();
						const offsetTop = target.getBoundingClientRect().top + window.scrollY - 80;
						
						window.scrollTo({
							top: offsetTop,
							behavior: 'smooth'
						});
					}
				});
			});
		}
		
		initScrollAnimations() {
			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('animated');
						// Only observe once
						if (!entry.target.dataset.repeatAnimation) {
							observer.unobserve(entry.target);
						}
					} else if (entry.target.dataset.repeatAnimation) {
						entry.target.classList.remove('animated');
					}
				});
			}, {
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px'
			});
			
			// Observe all animation elements
			const animationClasses = [
				'.animate-fade-in',
				'.animate-slide-up',
				'.animate-slide-left',
				'.animate-slide-right',
				'.animate-scale'
			];
			
			document.querySelectorAll(animationClasses.join(', ')).forEach(el => {
				observer.observe(el);
				this.observedElements.add(el);
			});
			
			// Auto-detect and animate common elements
			document.querySelectorAll('.post, article, .card').forEach((el, index) => {
				if (!el.classList.contains('animate-fade-in')) {
					el.classList.add('animate-slide-up');
					if (index > 0 && index < 6) {
						el.classList.add(`animate-stagger-${index}`);
					}
					observer.observe(el);
				}
			});
		}
		
		initParallax() {
			const parallaxElements = document.querySelectorAll('[data-parallax]');
			if (parallaxElements.length === 0) return;
			
			const handleScroll = () => {
				parallaxElements.forEach(el => {
					const speed = parseFloat(el.dataset.parallax) || 0.5;
					const rect = el.getBoundingClientRect();
					const scrolled = window.scrollY;
					
					if (rect.top < window.innerHeight && rect.bottom > 0) {
						const yPos = -(scrolled - el.offsetTop) * speed;
						el.style.transform = `translateY(${yPos}px)`;
					}
				});
			};
			
			window.addEventListener('scroll', handleScroll, { passive: true });
			handleScroll(); // Initial call
		}
		
		initCounters() {
			const counters = document.querySelectorAll('[data-counter]');
			if (counters.length === 0) return;
			
			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						const counter = entry.target;
						const target = parseInt(counter.dataset.counter);
						const duration = parseInt(counter.dataset.duration) || 2000;
						const start = 0;
						const increment = target / (duration / 16);
						
						let current = start;
						const timer = setInterval(() => {
							current += increment;
							if (current >= target) {
								counter.textContent = target.toLocaleString();
								clearInterval(timer);
							} else {
								counter.textContent = Math.floor(current).toLocaleString();
							}
						}, 16);
						
						observer.unobserve(counter);
					}
				});
			}, { threshold: 0.5 });
			
			counters.forEach(counter => observer.observe(counter));
		}
		
		initHoverEffects() {
			// Ripple effect on buttons
			document.querySelectorAll('.btn, button, .button').forEach(button => {
				button.addEventListener('click', function(e) {
					const ripple = document.createElement('span');
					ripple.className = 'ripple';
					
					const rect = this.getBoundingClientRect();
					const size = Math.max(rect.width, rect.height);
					const x = e.clientX - rect.left - size / 2;
					const y = e.clientY - rect.top - size / 2;
					
					ripple.style.width = ripple.style.height = size + 'px';
					ripple.style.left = x + 'px';
					ripple.style.top = y + 'px';
					
					this.appendChild(ripple);
					
					setTimeout(() => ripple.remove(), 600);
				});
			});
		}
		
		initScrollToTop() {
			const scrollBtn = document.createElement('button');
			scrollBtn.className = 'scroll-to-top';
			scrollBtn.innerHTML = '↑';
			scrollBtn.setAttribute('aria-label', 'Scroll to top');
			document.body.appendChild(scrollBtn);
			
			// Show/hide based on scroll position
			const toggleButton = () => {
				if (window.scrollY > 300) {
					scrollBtn.classList.add('visible');
				} else {
					scrollBtn.classList.remove('visible');
				}
			};
			
			window.addEventListener('scroll', toggleButton, { passive: true });
			
			// Scroll to top on click
			scrollBtn.addEventListener('click', () => {
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
			});
			
			// Scroll progress indicator
			const progressBar = document.createElement('div');
			progressBar.className = 'scroll-progress';
			document.body.appendChild(progressBar);
			
			const updateProgress = () => {
				const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
				const scrolled = window.scrollY;
				const progress = (scrolled / scrollHeight) * 100;
				progressBar.style.transform = `scaleX(${progress / 100})`;
			};
			
			window.addEventListener('scroll', updateProgress, { passive: true });
			updateProgress();
		}
		
		// Public API
		animateElement(element, animation = 'fade-in') {
			element.classList.add(`animate-${animation}`);
			element.classList.add('animated');
		}
		
		observeElement(element) {
			if (!this.observedElements.has(element)) {
				const observer = new IntersectionObserver((entries) => {
					entries.forEach(entry => {
						if (entry.isIntersecting) {
							entry.target.classList.add('animated');
						}
					});
				}, { threshold: 0.1 });
				
				observer.observe(element);
				this.observedElements.add(element);
			}
		}
	}
	
	// Create global instance
	window.smoothAnimations = new SmoothAnimations();
	
})();
