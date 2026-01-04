/**
 * Advanced Theme Features
 */
(function($) {
	'use strict';
	
	$(window).on('load', function() {
		// Preloader fade out
		var preloader = $('.swg-preloader');
		if (preloader.length) {
			var fadeDuration = parseInt(preloader.data('fade-duration')) || 500;
			preloader.css('transition', 'opacity ' + fadeDuration + 'ms ease-out');
			
			setTimeout(function() {
				preloader.addClass('fade-out');
				setTimeout(function() {
					preloader.remove();
				}, fadeDuration);
			}, 200);
		}
	});
	
	$(document).ready(function() {
		
		// Reading Progress Bar
		if ($('.swg-reading-progress').length) {
			$(window).on('scroll', function() {
				var winScroll = $(window).scrollTop();
				var height = $(document).height() - $(window).height();
				var scrolled = (winScroll / height) * 100;
				$('.swg-reading-progress').css('width', scrolled + '%');
			});
		}
		
		// Cookie Consent Banner
		var cookieConsent = $('#swgCookieConsent');
		if (cookieConsent.length) {
			if (!localStorage.getItem('swg-cookie-accepted')) {
				cookieConsent.fadeIn();
			}
			
			$('.swg-cookie-accept').on('click', function() {
				localStorage.setItem('swg-cookie-accepted', 'true');
				cookieConsent.fadeOut();
			});
		}
		
		// Table of Contents Generator
		if (typeof swgTheme !== 'undefined' && swgTheme.tocEnabled === '1') {
			var headings = $('.entry-content, .page-content').find('h2, h3');
			var minHeadings = parseInt(swgTheme.tocMinHeadings) || 3;
			
			if (headings.length >= minHeadings) {
				var tocHtml = '<div class="swg-toc"><h3>Table of Contents</h3><ul>';
				var counter = 1;
				
				headings.each(function() {
					var $heading = $(this);
					var headingText = $heading.text();
					var headingId = 'toc-' + counter;
					
					$heading.attr('id', headingId);
					
					var level = $heading.prop('tagName').toLowerCase() === 'h3' ? 'swg-toc-sub' : '';
					tocHtml += '<li class="' + level + '"><a href="#' + headingId + '">' + headingText + '</a></li>';
					counter++;
				});
				
				tocHtml += '</ul></div>';
				
				$('.entry-content, .page-content').prepend(tocHtml);
				
				// Smooth scroll for TOC links
				$('.swg-toc a').on('click', function(e) {
					e.preventDefault();
					var target = $(this).attr('href');
					$('html, body').animate({
						scrollTop: $(target).offset().top - 100
					}, 500);
				});
			}
		}
		
		// Sticky Header
		if (typeof swgTheme !== 'undefined' && swgTheme.stickyHeader === '1') {
			var header = $('.site-header');
			var headerOffset = header.offset().top;
			
			$(window).on('scroll', function() {
				if ($(window).scrollTop() > headerOffset) {
					header.addClass('swg-sticky-active');
					$('body').css('padding-top', header.outerHeight() + 'px');
				} else {
					header.removeClass('swg-sticky-active');
					$('body').css('padding-top', '0');
				}
			});
		}
		
		// Lazy Loading Images
		if (typeof swgTheme !== 'undefined' && swgTheme.lazyLoad === '1') {
			if ('IntersectionObserver' in window) {
				var imageObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(entry) {
						if (entry.isIntersecting) {
							var img = entry.target;
							if (img.dataset.src) {
								img.src = img.dataset.src;
								img.removeAttribute('data-src');
							}
							if (img.dataset.srcset) {
								img.srcset = img.dataset.srcset;
								img.removeAttribute('data-srcset');
							}
							img.classList.remove('swg-lazy');
							imageObserver.unobserve(img);
						}
					});
				});
				
				document.querySelectorAll('img.swg-lazy').forEach(function(img) {
					imageObserver.observe(img);
				});
			} else {
				// Fallback: load all images immediately
				$('img.swg-lazy').each(function() {
					if ($(this).data('src')) {
						$(this).attr('src', $(this).data('src'));
					}
					if ($(this).data('srcset')) {
						$(this).attr('srcset', $(this).data('srcset'));
					}
					$(this).removeClass('swg-lazy');
				});
			}
		}
		
		// AJAX Live Search
		if (typeof swgTheme !== 'undefined' && swgTheme.ajaxSearch === '1') {
			var searchInput = $('.search-field, input[type="search"]');
			var searchResults = $('<div class="swg-ajax-search-results"></div>');
			var searchTimeout;
			
			searchInput.after(searchResults);
			
			searchInput.on('keyup', function() {
				var query = $(this).val();
				
				clearTimeout(searchTimeout);
				
				if (query.length < 3) {
					searchResults.hide();
					return;
				}
				
				searchTimeout = setTimeout(function() {
					$.ajax({
						url: swgTheme.ajaxUrl,
						type: 'POST',
						data: {
							action: 'swg_ajax_search',
							query: query,
							nonce: swgTheme.ajaxNonce
						},
						success: function(response) {
							if (response.success) {
								searchResults.html(response.data).fadeIn();
							}
						}
					});
				}, 300);
			});
			
			// Close search results when clicking outside
			$(document).on('click', function(e) {
				if (!$(e.target).closest('.search-form, .swg-ajax-search-results').length) {
					searchResults.hide();
				}
			});
		}
		
		// Social Share Button Click Tracking
		$('.swg-share-btn').on('click', function() {
			var platform = $(this).attr('class').match(/swg-share-(\w+)/)[1];
			if (typeof gtag !== 'undefined') {
				gtag('event', 'share', {
					'method': platform
				});
			}
		});
		
	});
	
})(jQuery);
