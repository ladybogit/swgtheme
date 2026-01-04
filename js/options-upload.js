jQuery(document).ready(function($) {
	'use strict';
	
	// Media uploader for background image
	var mediaUploader;
	
	$('.swgtheme-upload-image-button').on('click', function(e) {
		e.preventDefault();
		
		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		
		// Create the media uploader
		mediaUploader = wp.media({
			title: 'Select Background Image',
			button: {
				text: 'Use this image'
			},
			multiple: false
		});
		
		// When an image is selected, run a callback
		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#swgtheme_background_image').val(attachment.url);
		});
		
		// Open the uploader dialog
		mediaUploader.open();
	});
	
	// Media uploader for OG image
	var ogImageUploader;
	
	$('.swgtheme-upload-og-image-button').on('click', function(e) {
		e.preventDefault();
		
		if (ogImageUploader) {
			ogImageUploader.open();
			return;
		}
		
		ogImageUploader = wp.media({
			title: 'Select Open Graph Image',
			button: {
				text: 'Use this image'
			},
			multiple: false
		});
		
		ogImageUploader.on('select', function() {
			var attachment = ogImageUploader.state().get('selection').first().toJSON();
			$('#swgtheme_og_image').val(attachment.url);
		});
		
		ogImageUploader.open();
	});
	
	// Generic media uploader for all upload buttons
	$('.swg-upload-button').on('click', function(e) {
		e.preventDefault();
		
		var button = $(this);
		var targetInput = $('#' + button.data('target'));
		
		var genericUploader = wp.media({
			title: 'Select Image',
			button: {
				text: 'Use this image'
			},
			multiple: false
		});
		
		genericUploader.on('select', function() {
			var attachment = genericUploader.state().get('selection').first().toJSON();
			targetInput.val(attachment.url);
		});
		
		genericUploader.open();
	});
	
	// Export settings
	$('#swgtheme-export-settings').on('click', function() {
		var settings = {};
		
		// Collect all swgtheme settings
		$('form input, form textarea, form select').each(function() {
			var name = $(this).attr('name');
			if (name && name.indexOf('swgtheme_') === 0) {
				if ($(this).attr('type') === 'checkbox') {
					settings[name] = $(this).is(':checked') ? '1' : '0';
				} else {
					settings[name] = $(this).val();
				}
			}
		});
		
		// Create JSON file
		var dataStr = JSON.stringify(settings, null, 2);
		var dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
		
		var exportFileDefaultName = 'swgtheme-settings-' + new Date().toISOString().slice(0,10) + '.json';
		
		var linkElement = document.createElement('a');
		linkElement.setAttribute('href', dataUri);
		linkElement.setAttribute('download', exportFileDefaultName);
		linkElement.click();
	});
	
	// Import settings
	$('#swgtheme-import-settings').on('click', function() {
		$('#swgtheme-import-file').click();
	});
	
	$('#swgtheme-import-file').on('change', function(e) {
		var file = e.target.files[0];
		if (!file) return;
		
		var reader = new FileReader();
		reader.onload = function(e) {
			try {
				var settings = JSON.parse(e.target.result);
				
				// Apply settings to form
				$.each(settings, function(name, value) {
					var $field = $('[name="' + name + '"]');
					if ($field.length) {
						if ($field.attr('type') === 'checkbox') {
							$field.prop('checked', value === '1');
						} else {
							$field.val(value);
						}
					}
				});
				
				$('#swgtheme-import-status').html('<span style="color:green;margin-left:10px;">✓ Settings imported! Click "Save Theme Options" to apply.</span>');
				
				setTimeout(function() {
					$('#swgtheme-import-status').html('');
				}, 5000);
				
			} catch (error) {
				$('#swgtheme-import-status').html('<span style="color:red;margin-left:10px;">✗ Invalid JSON file</span>');
			}
		};
		reader.readAsText(file);
		
		// Reset file input
		$(this).val('');
	});
	
	// Toggle global color override fields
	function toggleGlobalColorOverrides() {
		if ($('#swgtheme_use_global_color').is(':checked')) {
			// When checked: hide overrides, show required fields
			$('.global-color-override').hide();
			$('.global-color-required').show();
		} else {
			// When unchecked: show overrides, hide required fields
			$('.global-color-override').show();
			$('.global-color-required').hide();
		}
	}
	
	// Run on page load
	toggleGlobalColorOverrides();
	
	// Run on checkbox change
	$('#swgtheme_use_global_color').on('change', toggleGlobalColorOverrides);
});
