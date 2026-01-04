/**
 * Newsletter Subscription Script
 */

(function($) {
	'use strict';
	
	// Handle newsletter form submission
	$(document).on('submit', '.swg-newsletter-form', function(e) {
		e.preventDefault();
		
		var $form = $(this);
		var $submitBtn = $form.find('button[type="submit"]');
		var $message = $form.find('.swg-newsletter-message');
		var $email = $form.find('input[name="email"]');
		var $name = $form.find('input[name="name"]');
		
		// Validate email
		var email = $email.val().trim();
		if (!email || !isValidEmail(email)) {
			showMessage($form, 'error', 'Please enter a valid email address.');
			return;
		}
		
		// Disable submit button
		$submitBtn.prop('disabled', true).text('Subscribing...');
		
		// Send AJAX request
		$.ajax({
			url: swgTheme.ajaxUrl,
			type: 'POST',
			data: {
				action: 'swg_mailchimp_subscribe',
				nonce: swgTheme.ajaxNonce,
				email: email,
				name: $name.val()
			},
			success: function(response) {
				if (response.success) {
					showMessage($form, 'success', response.data.message);
					$form[0].reset();
				} else {
					showMessage($form, 'error', response.data.message);
				}
			},
			error: function() {
				showMessage($form, 'error', 'Connection error. Please try again.');
			},
			complete: function() {
				$submitBtn.prop('disabled', false).text('Subscribe');
			}
		});
	});
	
	function isValidEmail(email) {
		var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return re.test(email);
	}
	
	function showMessage($form, type, text) {
		var $message = $form.find('.swg-newsletter-message');
		
		if (!$message.length) {
			$message = $('<div class="swg-newsletter-message"></div>');
			$form.append($message);
		}
		
		$message
			.removeClass('success error')
			.addClass(type)
			.text(text)
			.fadeIn();
		
		setTimeout(function() {
			$message.fadeOut();
		}, 5000);
	}
	
})(jQuery);
