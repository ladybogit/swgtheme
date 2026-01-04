<?php
/**
 * Performance Admin Panel
 * Manage all performance optimization settings
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle settings save
if ( isset( $_POST['save_performance'] ) && isset( $_POST['performance_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['performance_nonce'], 'swg_performance_action' ) && current_user_can( 'manage_options' ) ) {
		// Caching options
		update_option( 'swgtheme_enable_fragment_cache', isset( $_POST['enable_fragment_cache'] ) ? '1' : '0' );
		update_option( 'swgtheme_cache_expiration', absint( $_POST['cache_expiration'] ?? 3600 ) );
		
		// Image optimization
		update_option( 'swgtheme_enable_webp', isset( $_POST['enable_webp'] ) ? '1' : '0' );
		update_option( 'swgtheme_enhanced_lazy_load', isset( $_POST['enhanced_lazy_load'] ) ? '1' : '0' );
		update_option( 'swgtheme_webp_quality', absint( $_POST['webp_quality'] ?? 85 ) );
		
		// Asset optimization
		update_option( 'swgtheme_optimize_assets', isset( $_POST['optimize_assets'] ) ? '1' : '0' );
		update_option( 'swgtheme_defer_js', isset( $_POST['defer_js'] ) ? '1' : '0' );
		update_option( 'swgtheme_async_js', isset( $_POST['async_js'] ) ? '1' : '0' );
		update_option( 'swgtheme_remove_query_strings', isset( $_POST['remove_query_strings'] ) ? '1' : '0' );
		
		// Critical CSS
		update_option( 'swgtheme_critical_css', isset( $_POST['critical_css'] ) ? '1' : '0' );
		update_option( 'swgtheme_critical_css_content', wp_strip_all_tags( $_POST['critical_css_content'] ?? '' ) );
		
		// Resource hints
		update_option( 'swgtheme_preload_fonts', sanitize_textarea_field( $_POST['preload_fonts'] ?? '' ) );
		update_option( 'swgtheme_cdn_url', esc_url_raw( $_POST['cdn_url'] ?? '' ) );
		
		// Database optimization
		update_option( 'swgtheme_auto_db_cleanup', isset( $_POST['auto_db_cleanup'] ) ? '1' : '0' );
		update_option( 'swgtheme_cleanup_days', absint( $_POST['cleanup_days'] ?? 30 ) );
		update_option( 'swgtheme_cleanup_revisions', isset( $_POST['cleanup_revisions'] ) ? '1' : '0' );
		update_option( 'swgtheme_cleanup_drafts', isset( $_POST['cleanup_drafts'] ) ? '1' : '0' );
		update_option( 'swgtheme_cleanup_trash', isset( $_POST['cleanup_trash'] ) ? '1' : '0' );
		update_option( 'swgtheme_optimize_tables', isset( $_POST['optimize_tables'] ) ? '1' : '0' );
		
		// Minification
		update_option( 'swgtheme_minify_html', isset( $_POST['minify_html'] ) ? '1' : '0' );
		update_option( 'swgtheme_enable_gzip', isset( $_POST['enable_gzip'] ) ? '1' : '0' );
		
		// Other optimizations
		update_option( 'swgtheme_disable_embeds', isset( $_POST['disable_embeds'] ) ? '1' : '0' );
		update_option( 'swgtheme_disable_emojis', isset( $_POST['disable_emojis'] ) ? '1' : '0' );
		
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Performance settings saved.', 'swgtheme' ) . '</p></div>';
	}
}

// Handle manual cache clear
if ( isset( $_POST['clear_cache'] ) && isset( $_POST['clear_cache_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['clear_cache_nonce'], 'swg_clear_cache_action' ) && current_user_can( 'manage_options' ) ) {
		SWGTheme_Performance::clear_all_caches();
		echo '<div class="notice notice-success"><p>' . esc_html__( 'All caches cleared successfully.', 'swgtheme' ) . '</p></div>';
	}
}

// Handle manual database cleanup
if ( isset( $_POST['cleanup_db'] ) && isset( $_POST['cleanup_db_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['cleanup_db_nonce'], 'swg_cleanup_db_action' ) && current_user_can( 'manage_options' ) ) {
		$performance = SWGTheme_Performance::get_instance();
		$performance->cleanup_database();
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Database cleanup completed.', 'swgtheme' ) . '</p></div>';
	}
}

// Get cache stats
$cache_stats = SWGTheme_Performance::get_cache_stats();

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Performance Optimization', 'swgtheme' ); ?></h1>
	
	<!-- Tabs -->
	<h2 class="nav-tab-wrapper">
		<a href="#caching" class="nav-tab nav-tab-active"><?php esc_html_e( 'Caching', 'swgtheme' ); ?></a>
		<a href="#images" class="nav-tab"><?php esc_html_e( 'Images', 'swgtheme' ); ?></a>
		<a href="#assets" class="nav-tab"><?php esc_html_e( 'Assets', 'swgtheme' ); ?></a>
		<a href="#database" class="nav-tab"><?php esc_html_e( 'Database', 'swgtheme' ); ?></a>
		<a href="#advanced" class="nav-tab"><?php esc_html_e( 'Advanced', 'swgtheme' ); ?></a>
		<a href="#tools" class="nav-tab"><?php esc_html_e( 'Tools', 'swgtheme' ); ?></a>
	</h2>
	
	<form method="post">
		<?php wp_nonce_field( 'swg_performance_action', 'performance_nonce' ); ?>
		
		<!-- Tab: Caching -->
		<div class="tab-content active" id="caching-tab">
			<h2><?php esc_html_e( 'Caching Settings', 'swgtheme' ); ?></h2>
			
			<div class="performance-stats" style="background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 4px;">
				<h3><?php esc_html_e( 'Cache Statistics', 'swgtheme' ); ?></h3>
				<ul style="margin: 10px 0;">
					<li><strong><?php esc_html_e( 'Total Transients:', 'swgtheme' ); ?></strong> <?php echo esc_html( number_format_i18n( $cache_stats['total_transients'] ) ); ?></li>
					<li><strong><?php esc_html_e( 'Fragment Cache Items:', 'swgtheme' ); ?></strong> <?php echo esc_html( number_format_i18n( $cache_stats['fragment_cache'] ) ); ?></li>
					<li><strong><?php esc_html_e( 'Object Cache:', 'swgtheme' ); ?></strong> <?php echo esc_html( $cache_stats['object_cache'] ); ?></li>
				</ul>
			</div>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Fragment Caching', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="enable_fragment_cache" 
								value="1" 
								<?php checked( get_option( 'swgtheme_enable_fragment_cache', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Enable fragment caching for widgets and template parts', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Cache specific parts of your pages to improve load times.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cache_expiration"><?php esc_html_e( 'Cache Expiration', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="number" 
							id="cache_expiration" 
							name="cache_expiration" 
							value="<?php echo esc_attr( get_option( 'swgtheme_cache_expiration', '3600' ) ); ?>" 
							min="300" 
							step="300" 
							class="small-text" /> <?php esc_html_e( 'seconds', 'swgtheme' ); ?>
						<p class="description">
							<?php esc_html_e( 'How long to cache fragments. Default: 3600 (1 hour)', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</table>
			
			<h3><?php esc_html_e( 'Usage Example', 'swgtheme' ); ?></h3>
			<pre style="background: #f5f5f5; padding: 15px; border-left: 3px solid #0073aa; overflow-x: auto;"><code>&lt;?php
echo swg_cache_fragment( 'sidebar-popular-posts', function() {
    // Your widget code here
    get_template_part( 'template-parts/popular-posts' );
}, 3600 );
?&gt;</code></pre>
		</div>
		
		<!-- Tab: Images -->
		<div class="tab-content" id="images-tab">
			<h2><?php esc_html_e( 'Image Optimization', 'swgtheme' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'WebP Conversion', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="enable_webp" 
								value="1" 
								<?php checked( get_option( 'swgtheme_enable_webp', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Automatically generate WebP versions of uploaded images', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Creates WebP versions with up to 30% smaller file sizes. Requires PHP GD with WebP support.', 'swgtheme' ); ?>
							<?php if ( ! function_exists( 'imagewebp' ) ) : ?>
								<br/><span style="color: #d63638;">⚠️ <?php esc_html_e( 'WebP support not available on this server.', 'swgtheme' ); ?></span>
							<?php else : ?>
								<br/><span style="color: #00a32a;">✓ <?php esc_html_e( 'WebP support available', 'swgtheme' ); ?></span>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="webp_quality"><?php esc_html_e( 'WebP Quality', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="number" 
							id="webp_quality" 
							name="webp_quality" 
							value="<?php echo esc_attr( get_option( 'swgtheme_webp_quality', '85' ) ); ?>" 
							min="1" 
							max="100" 
							class="small-text" /> %
						<p class="description">
							<?php esc_html_e( 'Compression quality (1-100). Recommended: 85', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enhanced Lazy Loading', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="enhanced_lazy_load" 
								value="1" 
								<?php checked( get_option( 'swgtheme_enhanced_lazy_load', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Add loading="lazy" and decoding="async" to all images', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Improves page load times by deferring offscreen image loading.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Tab: Assets -->
		<div class="tab-content" id="assets-tab">
			<h2><?php esc_html_e( 'Asset Optimization', 'swgtheme' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'JavaScript Optimization', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="optimize_assets" 
								value="1" 
								<?php checked( get_option( 'swgtheme_optimize_assets', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Enable asset optimization', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="defer_js" 
								value="1" 
								<?php checked( get_option( 'swgtheme_defer_js', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Defer JavaScript loading', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="async_js" 
								value="1" 
								<?php checked( get_option( 'swgtheme_async_js', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Load JavaScript asynchronously (except jQuery)', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Critical CSS', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="critical_css" 
								value="1" 
								<?php checked( get_option( 'swgtheme_critical_css', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Inline critical CSS and defer non-critical styles', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Improves First Contentful Paint (FCP) by inlining above-the-fold CSS.', 'swgtheme' ); ?>
						</p>
						<textarea name="critical_css_content" 
							rows="10" 
							class="large-text code" 
							placeholder="<?php esc_attr_e( 'Paste your critical CSS here (optional - default will be used if empty)', 'swgtheme' ); ?>"><?php echo esc_textarea( get_option( 'swgtheme_critical_css_content', '' ) ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="preload_fonts"><?php esc_html_e( 'Preload Fonts', 'swgtheme' ); ?></label>
					</th>
					<td>
						<textarea id="preload_fonts" 
							name="preload_fonts" 
							rows="5" 
							class="large-text code" 
							placeholder="<?php esc_attr_e( 'One font URL per line (WOFF2 format recommended)', 'swgtheme' ); ?>"><?php echo esc_textarea( get_option( 'swgtheme_preload_fonts', '' ) ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Example: https://yoursite.com/fonts/custom-font.woff2', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cdn_url"><?php esc_html_e( 'CDN URL', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="url" 
							id="cdn_url" 
							name="cdn_url" 
							value="<?php echo esc_url( get_option( 'swgtheme_cdn_url', '' ) ); ?>" 
							class="regular-text" 
							placeholder="https://cdn.example.com" />
						<p class="description">
							<?php esc_html_e( 'Add DNS prefetch and preconnect for your CDN domain.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Query Strings', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="remove_query_strings" 
								value="1" 
								<?php checked( get_option( 'swgtheme_remove_query_strings', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Remove query strings from static resources', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Improves caching by removing ?ver= from CSS/JS URLs.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Tab: Database -->
		<div class="tab-content" id="database-tab">
			<h2><?php esc_html_e( 'Database Optimization', 'swgtheme' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Automatic Cleanup', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="auto_db_cleanup" 
								value="1" 
								<?php checked( get_option( 'swgtheme_auto_db_cleanup', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Enable automatic daily database cleanup', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cleanup_days"><?php esc_html_e( 'Cleanup Age', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="number" 
							id="cleanup_days" 
							name="cleanup_days" 
							value="<?php echo esc_attr( get_option( 'swgtheme_cleanup_days', '30' ) ); ?>" 
							min="1" 
							class="small-text" /> <?php esc_html_e( 'days', 'swgtheme' ); ?>
						<p class="description">
							<?php esc_html_e( 'Delete items older than this many days.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Cleanup Options', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="cleanup_revisions" 
								value="1" 
								<?php checked( get_option( 'swgtheme_cleanup_revisions', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Delete old post revisions', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="cleanup_drafts" 
								value="1" 
								<?php checked( get_option( 'swgtheme_cleanup_drafts', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Delete old auto-drafts', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="cleanup_trash" 
								value="1" 
								<?php checked( get_option( 'swgtheme_cleanup_trash', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Delete trashed posts permanently', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="optimize_tables" 
								value="1" 
								<?php checked( get_option( 'swgtheme_optimize_tables', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Optimize database tables', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Tab: Advanced -->
		<div class="tab-content" id="advanced-tab">
			<h2><?php esc_html_e( 'Advanced Optimizations', 'swgtheme' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'HTML Minification', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="minify_html" 
								value="1" 
								<?php checked( get_option( 'swgtheme_minify_html', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Minify HTML output', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Removes whitespace and comments from HTML. Disabled when WP_DEBUG is on.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'GZIP Compression', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="enable_gzip" 
								value="1" 
								<?php checked( get_option( 'swgtheme_enable_gzip', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Enable GZIP compression', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Compresses pages before sending to browser (50-70% size reduction).', 'swgtheme' ); ?>
							<?php if ( ! extension_loaded( 'zlib' ) ) : ?>
								<br/><span style="color: #d63638;">⚠️ <?php esc_html_e( 'Zlib extension not available.', 'swgtheme' ); ?></span>
							<?php else : ?>
								<br/><span style="color: #00a32a;">✓ <?php esc_html_e( 'Zlib support available', 'swgtheme' ); ?></span>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Disable Features', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="disable_embeds" 
								value="1" 
								<?php checked( get_option( 'swgtheme_disable_embeds', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Disable WordPress embeds', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="disable_emojis" 
								value="1" 
								<?php checked( get_option( 'swgtheme_disable_emojis', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Disable emoji scripts', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Removes unnecessary scripts to improve performance.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		
		<input type="hidden" name="save_performance" value="1" />
		<div class="tab-content-save-button">
			<?php submit_button( __( 'Save Performance Settings', 'swgtheme' ) ); ?>
		</div>
	</form>
	
	<!-- Tab: Tools (outside form to avoid nested forms) -->
	<div class="tab-content" id="tools-tab">
		<h2><?php esc_html_e( 'Performance Tools', 'swgtheme' ); ?></h2>
		
		<div style="background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 20px; margin: 20px 0;">
			<h3><?php esc_html_e( 'Clear All Caches', 'swgtheme' ); ?></h3>
			<p><?php esc_html_e( 'Clear all transients, fragment cache, and object cache.', 'swgtheme' ); ?></p>
			<form method="post" style="display: inline;">
				<?php wp_nonce_field( 'swg_clear_cache_action', 'clear_cache_nonce' ); ?>
				<button type="submit" name="clear_cache" class="button button-primary" onclick="return confirm('<?php esc_attr_e( 'Clear all caches?', 'swgtheme' ); ?>');">
					<?php esc_html_e( 'Clear Caches', 'swgtheme' ); ?>
				</button>
			</form>
		</div>
		
		<div style="background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 20px; margin: 20px 0;">
			<h3><?php esc_html_e( 'Database Cleanup', 'swgtheme' ); ?></h3>
			<p><?php esc_html_e( 'Manually run database cleanup and optimization.', 'swgtheme' ); ?></p>
			<form method="post" style="display: inline;">
				<?php wp_nonce_field( 'swg_cleanup_db_action', 'cleanup_db_nonce' ); ?>
				<button type="submit" name="cleanup_db" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Run database cleanup? This may take a few moments.', 'swgtheme' ); ?>');">
					<?php esc_html_e( 'Cleanup Database', 'swgtheme' ); ?>
				</button>
			</form>
		</div>
		
		<div style="background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 20px; margin: 20px 0;">
			<h3><?php esc_html_e( 'Performance Testing', 'swgtheme' ); ?></h3>
			<p><?php esc_html_e( 'Test your site speed with these tools:', 'swgtheme' ); ?></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><a href="https://pagespeed.web.dev/" target="_blank">Google PageSpeed Insights</a></li>
				<li><a href="https://gtmetrix.com/" target="_blank">GTmetrix</a></li>
				<li><a href="https://tools.pingdom.com/" target="_blank">Pingdom Tools</a></li>
				<li><a href="https://webpagetest.org/" target="_blank">WebPageTest</a></li>
			</ul>
		</div>
	</div>
	
	<style type="text/css">
		.wrap .tab-content { display: none !important; }
		.wrap .tab-content.active { display: block !important; }
	</style>
	
	<script type="text/javascript">
	(function($) {
		// Tab switching function
		function switchTab(target) {
			$('.nav-tab').removeClass('nav-tab-active');
			$('.nav-tab[href="' + target + '"]').addClass('nav-tab-active');
			
			$('.tab-content').removeClass('active');
			var tabId = target.replace('#', '') + '-tab';
			$('#' + tabId).addClass('active');
			
			// Hide save button for Tools tab, show for others
			if (target === '#tools') {
				$('.tab-content-save-button').hide();
			} else {
				$('.tab-content-save-button').show();
			}
		}
		
		// Initialize immediately - don't wait for document ready
		$(document).ready(function() {
			// Handle tab clicks
			$('.nav-tab-wrapper .nav-tab').on('click', function(e) {
				e.preventDefault();
				var target = $(this).attr('href');
				switchTab(target);
				
				// Update URL hash without scrolling
				if (history.pushState) {
					history.pushState(null, null, target);
				} else {
					location.hash = target;
				}
			});
			
			// Handle initial page load with hash
			if (window.location.hash) {
				switchTab(window.location.hash);
			} else {
				// Ensure first tab is active on initial load
				switchTab('#caching');
			}
			
			// Handle browser back/forward
			$(window).on('hashchange', function() {
				if (window.location.hash) {
					switchTab(window.location.hash);
				}
			});
		});
	})(jQuery);
	</script>
</div>
