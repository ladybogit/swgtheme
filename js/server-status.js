/**
 * Server Status Checker
 */
(function($) {
	'use strict';
	
	$(document).ready(function() {
		$('.swg-server-status').each(function() {
			var $widget = $(this);
			var serverIp = $widget.data('server');
			var serverPort = $widget.data('port');
			var refreshTime = $widget.data('refresh') * 1000; // Convert to milliseconds
			
			function checkServerStatus() {
				// Update indicator to checking state
				$widget.find('.swg-status-dot').removeClass('swg-status-online swg-status-offline').addClass('swg-status-checking');
				$widget.find('.swg-status-text').text('Checking...');
				
				// Use mcsrvstat.us API for Minecraft servers
				// For other server types, you'll need to implement a custom backend endpoint
				$.ajax({
					url: 'https://api.mcsrvstat.us/2/' + serverIp + ':' + serverPort,
					method: 'GET',
					dataType: 'json',
					timeout: 10000,
					success: function(data) {
						if (data.online) {
							// Server is online
							$widget.find('.swg-status-dot').removeClass('swg-status-checking swg-status-offline').addClass('swg-status-online');
							$widget.find('.swg-status-text').text('Online');
							
							// Update player count
							if (data.players) {
								$widget.find('.swg-players-count').text(data.players.online + '/' + data.players.max);
							}
							
							// Update version
							if (data.version) {
								$widget.find('.swg-version-text').text(data.version);
							}
							
							// Show details
							$widget.find('.swg-server-details').fadeIn();
						} else {
							// Server is offline
							$widget.find('.swg-status-dot').removeClass('swg-status-checking swg-status-online').addClass('swg-status-offline');
							$widget.find('.swg-status-text').text('Offline');
							$widget.find('.swg-server-details').fadeOut();
						}
					},
					error: function() {
						// Error checking server
						$widget.find('.swg-status-dot').removeClass('swg-status-checking swg-status-online').addClass('swg-status-offline');
						$widget.find('.swg-status-text').text('Offline');
						$widget.find('.swg-server-details').fadeOut();
					}
				});
			}
			
			// Initial check
			checkServerStatus();
			
			// Set up auto-refresh
			setInterval(checkServerStatus, refreshTime);
		});
	});
})(jQuery);
