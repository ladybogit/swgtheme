/**
 * Mobile Menu Toggle
 */
(function($) {
	'use strict';
	
	$(document).ready(function() {
		// Check if mobile menu is enabled
		if (typeof swgTheme !== 'undefined' && swgTheme.enableMobileMenu !== '1') {
			return;
		}
		
		// Add mobile menu toggle button
		if ($('.site-header').length && $('.top-menu').length) {
			var $mobileToggle = $('<button class="swg-mobile-menu-toggle" aria-label="Toggle Mobile Menu"><span></span><span></span><span></span></button>');
			$('.site-header .container').prepend($mobileToggle);
			
			// Clone menu for mobile
			var $mobileMenu = $('.top-menu').clone().addClass('swg-mobile-menu').removeClass('top-menu');
			$('body').append('<div class="swg-mobile-menu-overlay"></div>');
			$('body').append($mobileMenu);
			
			// Toggle mobile menu
			$mobileToggle.on('click', function() {
				$(this).toggleClass('active');
				$('.swg-mobile-menu').toggleClass('active');
				$('.swg-mobile-menu-overlay').toggleClass('active');
				$('body').toggleClass('swg-mobile-menu-open');
			});
			
			// Close menu on overlay click
			$('.swg-mobile-menu-overlay').on('click', function() {
				$mobileToggle.removeClass('active');
				$('.swg-mobile-menu').removeClass('active');
				$(this).removeClass('active');
				$('body').removeClass('swg-mobile-menu-open');
			});
			
			// Close menu when clicking a link
			$('.swg-mobile-menu a').on('click', function() {
				$mobileToggle.removeClass('active');
				$('.swg-mobile-menu').removeClass('active');
				$('.swg-mobile-menu-overlay').removeClass('active');
				$('body').removeClass('swg-mobile-menu-open');
			});
		}
	});
})(jQuery);
