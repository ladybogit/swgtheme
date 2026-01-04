/**
 * Infinite Scroll
 */
(function($) {
	'use strict';
	
	if (typeof swgTheme === 'undefined' || swgTheme.infiniteScroll !== '1') {
		return;
	}
	
	var page = 2;
	var loading = false;
	var finished = false;
	
	$(window).on('scroll', function() {
		if (loading || finished) {
			return;
		}
		
		var $pagination = $('.pagination, .nav-links');
		if (!$pagination.length) {
			finished = true;
			return;
		}
		
		var scrollPosition = $(window).scrollTop() + $(window).height();
		var documentHeight = $(document).height();
		
		// Trigger when 200px from bottom
		if (scrollPosition >= documentHeight - 200) {
			loading = true;
			
			// Show loading indicator
			if (!$('#swg-infinite-loader').length) {
				$pagination.before('<div id="swg-infinite-loader" class="swg-loading"><div class="swg-spinner"></div><p>Loading more posts...</p></div>');
			}
			
			// Get next page URL
			var nextPageUrl = $('.pagination .next, .nav-links .next').attr('href');
			
			if (!nextPageUrl) {
				finished = true;
				$('#swg-infinite-loader').remove();
				$pagination.after('<div class="swg-no-more-posts">No more posts to load.</div>');
				return;
			}
			
			$.ajax({
				url: nextPageUrl,
				type: 'GET',
				success: function(data) {
					var $newPosts = $(data).find('article, .post');
					
					if ($newPosts.length) {
						// Find container for posts
						var $container = $('article:last, .post:last').parent();
						$container.append($newPosts);
						
						// Update page counter
						page++;
						
						// Update pagination
						var $newPagination = $(data).find('.pagination, .nav-links');
						if ($newPagination.length) {
							$pagination.replaceWith($newPagination);
						} else {
							finished = true;
							$pagination.remove();
							$('#swg-infinite-loader').remove();
							$container.after('<div class="swg-no-more-posts">No more posts to load.</div>');
						}
					} else {
						finished = true;
					}
					
					$('#swg-infinite-loader').remove();
					loading = false;
				},
				error: function() {
					$('#swg-infinite-loader').html('<p>Error loading posts. Please try again.</p>');
					loading = false;
				}
			});
		}
	});
	
})(jQuery);
