<?php
/**
 * Performance Optimization System
 * Advanced caching, optimization, and speed enhancements
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SWGTheme_Performance {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->init_hooks();
	}
	
	private function init_hooks() {
		// Fragment caching
		add_action( 'init', array( $this, 'init_fragment_cache' ) );
		
		// Image optimization
		if ( get_option( 'swgtheme_enable_webp', '0' ) === '1' ) {
			add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_webp' ), 10, 2 );
			add_filter( 'wp_get_attachment_image_src', array( $this, 'replace_with_webp' ), 10, 4 );
		}
		
		// Enhanced lazy loading
		if ( get_option( 'swgtheme_enhanced_lazy_load', '0' ) === '1' ) {
			add_filter( 'the_content', array( $this, 'add_lazy_loading' ), 20 );
			add_filter( 'post_thumbnail_html', array( $this, 'add_lazy_loading_thumbnail' ), 10, 5 );
		}
		
		// Asset optimization
		if ( get_option( 'swgtheme_optimize_assets', '0' ) === '1' ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'optimize_assets' ), 999 );
			add_action( 'wp_head', array( $this, 'add_resource_hints' ), 1 );
		}
		
		// Critical CSS
		if ( get_option( 'swgtheme_critical_css', '0' ) === '1' ) {
			add_action( 'wp_head', array( $this, 'inline_critical_css' ), 1 );
			add_filter( 'style_loader_tag', array( $this, 'defer_non_critical_css' ), 10, 4 );
		}
		
		// Database optimization
		if ( get_option( 'swgtheme_auto_db_cleanup', '0' ) === '1' ) {
			add_action( 'swgtheme_daily_cleanup', array( $this, 'cleanup_database' ) );
			if ( ! wp_next_scheduled( 'swgtheme_daily_cleanup' ) ) {
				wp_schedule_event( time(), 'daily', 'swgtheme_daily_cleanup' );
			}
		}
		
		// Minification
		if ( get_option( 'swgtheme_minify_html', '0' ) === '1' ) {
			add_action( 'template_redirect', array( $this, 'start_html_minification' ) );
		}
		
		// GZIP compression
		if ( get_option( 'swgtheme_enable_gzip', '0' ) === '1' ) {
			add_action( 'init', array( $this, 'enable_gzip_compression' ) );
		}
		
		// Remove query strings
		if ( get_option( 'swgtheme_remove_query_strings', '0' ) === '1' ) {
			add_filter( 'style_loader_src', array( $this, 'remove_query_strings' ), 10, 2 );
			add_filter( 'script_loader_src', array( $this, 'remove_query_strings' ), 10, 2 );
		}
		
		// Disable emojis (if not already done)
		if ( get_option( 'swgtheme_disable_emojis', '1' ) === '1' ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
		}
		
		// Disable embeds
		if ( get_option( 'swgtheme_disable_embeds', '0' ) === '1' ) {
			add_action( 'init', array( $this, 'disable_embeds' ) );
		}
	}
	
	/**
	 * Fragment caching for widgets and template parts
	 */
	public function init_fragment_cache() {
		// Cache is initialized, actual caching done via helper functions
	}
	
	/**
	 * Cache fragment with expiration
	 */
	public static function cache_fragment( $key, $callback, $expiration = 3600 ) {
		$cached = get_transient( 'swg_fragment_' . $key );
		
		if ( false !== $cached ) {
			return $cached;
		}
		
		ob_start();
		call_user_func( $callback );
		$output = ob_get_clean();
		
		set_transient( 'swg_fragment_' . $key, $output, $expiration );
		
		return $output;
	}
	
	/**
	 * Generate WebP versions of uploaded images
	 */
	public function generate_webp( $metadata, $attachment_id ) {
		if ( ! function_exists( 'imagewebp' ) ) {
			return $metadata;
		}
		
		$upload_dir = wp_upload_dir();
		$file_path = get_attached_file( $attachment_id );
		
		if ( ! file_exists( $file_path ) ) {
			return $metadata;
		}
		
		$file_info = pathinfo( $file_path );
		$extension = strtolower( $file_info['extension'] );
		
		// Only convert JPG and PNG
		if ( ! in_array( $extension, array( 'jpg', 'jpeg', 'png' ), true ) ) {
			return $metadata;
		}
		
		// Create WebP version
		$webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
		
		if ( 'png' === $extension ) {
			$image = imagecreatefrompng( $file_path );
			imagepalettetotruecolor( $image );
			imagealphablending( $image, true );
			imagesavealpha( $image, true );
		} else {
			$image = imagecreatefromjpeg( $file_path );
		}
		
		if ( $image ) {
			imagewebp( $image, $webp_path, 85 );
			imagedestroy( $image );
		}
		
		// Generate WebP for all sizes
		if ( isset( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
			foreach ( $metadata['sizes'] as $size => $size_data ) {
				$size_path = $upload_dir['path'] . '/' . $size_data['file'];
				$size_info = pathinfo( $size_path );
				$size_webp = $size_info['dirname'] . '/' . $size_info['filename'] . '.webp';
				
				if ( file_exists( $size_path ) ) {
					if ( 'png' === $extension ) {
						$size_image = imagecreatefrompng( $size_path );
						imagepalettetotruecolor( $size_image );
						imagealphablending( $size_image, true );
						imagesavealpha( $size_image, true );
					} else {
						$size_image = imagecreatefromjpeg( $size_path );
					}
					
					if ( $size_image ) {
						imagewebp( $size_image, $size_webp, 85 );
						imagedestroy( $size_image );
					}
				}
			}
		}
		
		return $metadata;
	}
	
	/**
	 * Replace image URLs with WebP versions if available
	 */
	public function replace_with_webp( $image, $attachment_id, $size, $icon ) {
		if ( ! $image ) {
			return $image;
		}
		
		// Check if browser supports WebP
		$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? $_SERVER['HTTP_ACCEPT'] : '';
		if ( strpos( $accept, 'image/webp' ) === false ) {
			return $image;
		}
		
		$webp_url = preg_replace( '/\.(jpg|jpeg|png)$/i', '.webp', $image[0] );
		$webp_path = str_replace( wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url );
		
		if ( file_exists( $webp_path ) ) {
			$image[0] = $webp_url;
		}
		
		return $image;
	}
	
	/**
	 * Add enhanced lazy loading to content images
	 */
	public function add_lazy_loading( $content ) {
		if ( is_feed() || is_preview() ) {
			return $content;
		}
		
		// Add loading="lazy" and blur-up placeholder
		$content = preg_replace_callback(
			'/<img([^>]+?)src=[\'"]([^\'">]+)[\'"]([^>]*)>/i',
			function( $matches ) {
				// Skip if already has loading attribute
				if ( strpos( $matches[0], 'loading=' ) !== false ) {
					return $matches[0];
				}
				
				$img_tag = $matches[0];
				
				// Add loading="lazy"
				$img_tag = str_replace( '<img', '<img loading="lazy"', $img_tag );
				
				// Add decoding="async"
				if ( strpos( $img_tag, 'decoding=' ) === false ) {
					$img_tag = str_replace( '<img', '<img decoding="async"', $img_tag );
				}
				
				return $img_tag;
			},
			$content
		);
		
		return $content;
	}
	
	/**
	 * Add lazy loading to post thumbnails
	 */
	public function add_lazy_loading_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if ( strpos( $html, 'loading=' ) === false ) {
			$html = str_replace( '<img', '<img loading="lazy" decoding="async"', $html );
		}
		return $html;
	}
	
	/**
	 * Optimize loaded assets
	 */
	public function optimize_assets() {
		global $wp_scripts, $wp_styles;
		
		// Defer JavaScript
		if ( get_option( 'swgtheme_defer_js', '0' ) === '1' ) {
			foreach ( $wp_scripts->queue as $handle ) {
				$wp_scripts->add_data( $handle, 'defer', true );
			}
		}
		
		// Async JavaScript
		if ( get_option( 'swgtheme_async_js', '0' ) === '1' ) {
			add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
		}
	}
	
	/**
	 * Add async attribute to scripts
	 */
	public function add_async_attribute( $tag, $handle ) {
		// Skip jQuery and admin scripts
		if ( 'jquery' === $handle || is_admin() ) {
			return $tag;
		}
		
		// Skip if already has async or defer
		if ( strpos( $tag, 'async' ) !== false || strpos( $tag, 'defer' ) !== false ) {
			return $tag;
		}
		
		return str_replace( ' src', ' async src', $tag );
	}
	
	/**
	 * Add resource hints for DNS prefetch and preconnect
	 */
	public function add_resource_hints() {
		$hints = array(
			'https://fonts.googleapis.com',
			'https://fonts.gstatic.com',
		);
		
		// Add custom CDN domains
		$cdn_url = get_option( 'swgtheme_cdn_url', '' );
		if ( ! empty( $cdn_url ) ) {
			$hints[] = $cdn_url;
		}
		
		foreach ( $hints as $hint ) {
			echo '<link rel="dns-prefetch" href="' . esc_url( $hint ) . '">' . "\n";
			echo '<link rel="preconnect" href="' . esc_url( $hint ) . '" crossorigin>' . "\n";
		}
		
		// Preload critical fonts
		$preload_fonts = get_option( 'swgtheme_preload_fonts', '' );
		if ( ! empty( $preload_fonts ) ) {
			$fonts = explode( "\n", $preload_fonts );
			foreach ( $fonts as $font ) {
				$font = trim( $font );
				if ( ! empty( $font ) ) {
					echo '<link rel="preload" href="' . esc_url( $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
				}
			}
		}
	}
	
	/**
	 * Inline critical CSS
	 */
	public function inline_critical_css() {
		$critical_css = get_option( 'swgtheme_critical_css_content', '' );
		
		if ( empty( $critical_css ) ) {
			$critical_css = $this->get_default_critical_css();
		}
		
		if ( ! empty( $critical_css ) ) {
			echo '<style id="swg-critical-css">' . wp_strip_all_tags( $critical_css ) . '</style>' . "\n";
		}
	}
	
	/**
	 * Get default critical CSS
	 */
	private function get_default_critical_css() {
		return '
body{margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif}
header{background:#000;color:#fff;padding:1rem}
.container{max-width:1200px;margin:0 auto;padding:0 15px}
h1,h2,h3,h4,h5,h6{margin:0 0 1rem;line-height:1.2}
a{color:#0073aa;text-decoration:none}
img{max-width:100%;height:auto}
.hidden{display:none}
';
	}
	
	/**
	 * Defer non-critical CSS
	 */
	public function defer_non_critical_css( $html, $handle, $href, $media ) {
		// Don't defer admin styles or critical styles
		if ( is_admin() || strpos( $handle, 'admin' ) !== false || strpos( $handle, 'critical' ) !== false ) {
			return $html;
		}
		
		// Defer loading with media print trick
		$html = str_replace( "media='$media'", "media='print' onload=\"this.media='$media'\"", $html );
		$html .= '<noscript>' . $html . '</noscript>';
		
		return $html;
	}
	
	/**
	 * Database cleanup
	 */
	public function cleanup_database() {
		global $wpdb;
		
		$days_old = intval( get_option( 'swgtheme_cleanup_days', 30 ) );
		
		// Delete old post revisions
		if ( get_option( 'swgtheme_cleanup_revisions', '1' ) === '1' ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $wpdb->posts WHERE post_type = 'revision' AND post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days_old
			) );
		}
		
		// Delete old auto-drafts
		if ( get_option( 'swgtheme_cleanup_drafts', '1' ) === '1' ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft' AND post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days_old
			) );
		}
		
		// Delete trashed posts
		if ( get_option( 'swgtheme_cleanup_trash', '1' ) === '1' ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $wpdb->posts WHERE post_status = 'trash' AND post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days_old
			) );
		}
		
		// Delete orphaned post meta
		$wpdb->query( "DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON pm.post_id = p.ID WHERE p.ID IS NULL" );
		
		// Delete orphaned term relationships
		$wpdb->query( "DELETE tr FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->posts p ON tr.object_id = p.ID WHERE p.ID IS NULL" );
		
		// Delete expired transients
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_value < UNIX_TIMESTAMP()" );
		
		// Optimize tables
		if ( get_option( 'swgtheme_optimize_tables', '1' ) === '1' ) {
			$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
			foreach ( $tables as $table ) {
				$wpdb->query( "OPTIMIZE TABLE {$table[0]}" );
			}
		}
	}
	
	/**
	 * Start HTML minification
	 */
	public function start_html_minification() {
		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}
		
		ob_start( array( $this, 'minify_html' ) );
	}
	
	/**
	 * Minify HTML output
	 */
	public function minify_html( $html ) {
		// Don't minify if WP_DEBUG is on
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return $html;
		}
		
		// Remove HTML comments (except IE conditionals)
		$html = preg_replace( '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html );
		
		// Remove whitespace between tags
		$html = preg_replace( '/>\s+</', '><', $html );
		
		// Remove whitespace in text
		$html = preg_replace( '/\s+/', ' ', $html );
		
		return trim( $html );
	}
	
	/**
	 * Enable GZIP compression
	 */
	public function enable_gzip_compression() {
		if ( ! headers_sent() && extension_loaded( 'zlib' ) && ! ini_get( 'zlib.output_compression' ) ) {
			if ( ! ob_start( 'ob_gzhandler' ) ) {
				ob_start();
			}
		}
	}
	
	/**
	 * Remove query strings from static resources
	 */
	public function remove_query_strings( $src, $handle ) {
		if ( strpos( $src, '?ver=' ) !== false ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}
	
	/**
	 * Disable WordPress embeds
	 */
	public function disable_embeds() {
		// Remove embed script
		wp_dequeue_script( 'wp-embed' );
		
		// Remove embed discovery links
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		
		// Remove embed-specific JavaScript
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		
		// Remove REST API embed endpoint
		remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );
	}
	
	/**
	 * Get cache statistics
	 */
	public static function get_cache_stats() {
		global $wpdb;
		
		$transients = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_transient_%'" );
		$fragment_cache = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_transient_swg_fragment_%'" );
		
		return array(
			'total_transients' => $transients,
			'fragment_cache' => $fragment_cache,
			'object_cache' => wp_using_ext_object_cache() ? 'Active' : 'Not Active',
		);
	}
	
	/**
	 * Clear all performance caches
	 */
	public static function clear_all_caches() {
		global $wpdb;
		
		// Delete all transients
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'" );
		
		// Clear object cache
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
		
		// Delete WebP files if regenerating
		if ( get_option( 'swgtheme_clear_webp', '0' ) === '1' ) {
			$upload_dir = wp_upload_dir();
			$webp_files = glob( $upload_dir['basedir'] . '/**/*.webp' );
			foreach ( $webp_files as $file ) {
				@unlink( $file );
			}
		}
		
		return true;
	}
}

// Initialize
SWGTheme_Performance::get_instance();

/**
 * Helper function for fragment caching
 */
function swg_cache_fragment( $key, $callback, $expiration = 3600 ) {
	return SWGTheme_Performance::cache_fragment( $key, $callback, $expiration );
}
