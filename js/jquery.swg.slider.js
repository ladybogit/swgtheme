jQuery(document).ready(function($) {
	// Get settings from WordPress
	var settings = typeof swgSliderSettings !== 'undefined' ? swgSliderSettings : {
		autoplay: false,
		speed: 5000,
		pauseOnHover: true,
		loop: true
	};
	
	var currentSlide = 0;
	var $slider = $('#slider');
	var $slides = $slider.find('.slide');
	var slideCount = $slides.length;
	var autoplayInterval;
	
	if (slideCount === 0) return;
	
	// Show first slide
	$slides.eq(0).addClass('active');
	
	// Next slide function
	function nextSlide() {
		$slides.eq(currentSlide).removeClass('active');
		
		if (settings.loop) {
			currentSlide = (currentSlide + 1) % slideCount;
		} else {
			currentSlide = Math.min(currentSlide + 1, slideCount - 1);
		}
		
		$slides.eq(currentSlide).addClass('active');
	}
	
	// Previous slide function
	function prevSlide() {
		$slides.eq(currentSlide).removeClass('active');
		
		if (settings.loop) {
			currentSlide = (currentSlide - 1 + slideCount) % slideCount;
		} else {
			currentSlide = Math.max(currentSlide - 1, 0);
		}
		
		$slides.eq(currentSlide).addClass('active');
	}
	
	// Start autoplay
	function startAutoplay() {
		if (settings.autoplay && !autoplayInterval) {
			autoplayInterval = setInterval(nextSlide, settings.speed);
		}
	}
	
	// Stop autoplay
	function stopAutoplay() {
		if (autoplayInterval) {
			clearInterval(autoplayInterval);
			autoplayInterval = null;
		}
	}
	
	// Navigation controls
	$slider.on('click', '.swg-slider-next', nextSlide);
	$slider.on('click', '.swg-slider-prev', prevSlide);
	
	// Pause on hover
	if (settings.pauseOnHover) {
		$slider.on('mouseenter', stopAutoplay);
		$slider.on('mouseleave', startAutoplay);
	}
	
	// Start autoplay if enabled
	startAutoplay();
});
