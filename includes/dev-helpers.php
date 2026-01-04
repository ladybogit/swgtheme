<?php
/**
 * Developer Helper Functions
 * Tools and utilities for theme development
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Development Mode Detection
 */
class SWGTheme_Dev_Tools {
	
	/**
	 * Check if in development mode
	 */
	public static function is_dev_mode() {
		// Check if Theme Options setting exists (has been saved)
		$dev_mode_option = get_option( 'swgtheme_enable_developer_mode', null );
		
		// If option has been explicitly set, respect user's choice
		if ( $dev_mode_option !== null ) {
			return $dev_mode_option === '1';
		}
		
		// Only use fallback checks if option has never been set
		return ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || 
		       ( defined( 'SWGTHEME_DEV_MODE' ) && SWGTHEME_DEV_MODE ) ||
		       self::is_local_environment();
	}
	
	/**
	 * Check if local environment
	 */
	public static function is_local_environment() {
		$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
		return in_array( $server_name, array( 'localhost', '127.0.0.1', '::1' ), true ) ||
		       str_contains( $server_name, '.local' ) ||
		       str_contains( $server_name, '.test' ) ||
		       str_contains( $server_name, '.dev' );
	}
	
	/**
	 * Debug log with context
	 */
	public static function debug_log( $message, $data = null, $context = '' ) {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		$log_message = '[SWGTheme Debug] ';
		
		if ( $context ) {
			$log_message .= "[$context] ";
		}
		
		$log_message .= $message;
		
		if ( $data !== null ) {
			$log_message .= ' | Data: ' . print_r( $data, true );
		}
		
		error_log( $log_message );
	}
	
	/**
	 * Dump and die for debugging
	 */
	public static function dd( ...$vars ) {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:20px;margin:20px;border-radius:5px;font-family:Consolas,monospace;font-size:14px;">';
		foreach ( $vars as $var ) {
			var_dump( $var );
		}
		echo '</pre>';
		die();
	}
	
	/**
	 * Pretty print for debugging
	 */
	public static function dump( ...$vars ) {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		echo '<div style="background:#f8f9fa;border:2px solid #dee2e6;padding:15px;margin:10px 0;border-radius:5px;font-family:Consolas,monospace;font-size:13px;">';
		echo '<strong style="color:#495057;display:block;margin-bottom:10px;">Debug Output:</strong>';
		echo '<pre style="margin:0;overflow-x:auto;">';
		foreach ( $vars as $var ) {
			print_r( $var );
			echo "\n";
		}
		echo '</pre>';
		echo '</div>';
	}
	
	/**
	 * Performance timer
	 */
	private static $timers = array();
	
	public static function timer_start( $name ) {
		self::$timers[ $name ] = microtime( true );
	}
	
	public static function timer_end( $name, $log = true ) {
		if ( ! isset( self::$timers[ $name ] ) ) {
			return null;
		}
		
		$elapsed = microtime( true ) - self::$timers[ $name ];
		
		if ( $log ) {
			self::debug_log( "Timer [$name]: " . round( $elapsed * 1000, 2 ) . 'ms' );
		}
		
		unset( self::$timers[ $name ] );
		
		return $elapsed;
	}
	
	/**
	 * Get all WordPress queries
	 */
	public static function get_queries() {
		global $wpdb;
		
		if ( ! self::is_dev_mode() || ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {
			return array();
		}
		
		return $wpdb->queries;
	}
	
	/**
	 * Display query analysis
	 */
	public static function analyze_queries() {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		$queries = self::get_queries();
		
		if ( empty( $queries ) ) {
			echo '<p style="color:#ffc107;">SAVEQUERIES not enabled. Add define(\'SAVEQUERIES\', true); to wp-config.php</p>';
			return;
		}
		
		$total_time = 0;
		$slow_queries = array();
		
		foreach ( $queries as $query ) {
			$total_time += $query[1];
			if ( $query[1] > 0.01 ) { // Queries slower than 10ms
				$slow_queries[] = $query;
			}
		}
		
		echo '<div style="background:#fff;border:2px solid #dc3545;padding:20px;margin:20px;border-radius:5px;">';
		echo '<h3 style="margin-top:0;color:#dc3545;">Query Analysis</h3>';
		echo '<p><strong>Total Queries:</strong> ' . count( $queries ) . '</p>';
		echo '<p><strong>Total Time:</strong> ' . round( $total_time, 4 ) . 's</p>';
		echo '<p><strong>Slow Queries (>10ms):</strong> ' . count( $slow_queries ) . '</p>';
		
		if ( ! empty( $slow_queries ) ) {
			echo '<h4>Slow Queries:</h4>';
			echo '<ul style="max-height:400px;overflow-y:auto;">';
			foreach ( $slow_queries as $query ) {
				echo '<li style="margin-bottom:10px;padding:10px;background:#f8f9fa;border-radius:3px;">';
				echo '<strong>Time:</strong> ' . round( $query[1] * 1000, 2 ) . 'ms<br>';
				echo '<code style="display:block;margin-top:5px;white-space:pre-wrap;word-break:break-all;">' . esc_html( $query[0] ) . '</code>';
				echo '<small style="color:#6c757d;display:block;margin-top:5px;">' . esc_html( $query[2] ) . '</small>';
				echo '</li>';
			}
			echo '</ul>';
		}
		
		echo '</div>';
	}
	
	/**
	 * Get memory usage
	 */
	public static function get_memory_usage( $peak = false ) {
		$bytes = $peak ? memory_get_peak_usage() : memory_get_usage();
		return self::format_bytes( $bytes );
	}
	
	/**
	 * Format bytes
	 */
	public static function format_bytes( $bytes, $precision = 2 ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		
		for ( $i = 0; $bytes > 1024 && $i < count( $units ) - 1; $i++ ) {
			$bytes /= 1024;
		}
		
		return round( $bytes, $precision ) . ' ' . $units[ $i ];
	}
	
	/**
	 * Display system information
	 */
	public static function system_info() {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		global $wpdb;
		
		$info = array(
			'WordPress Version' => get_bloginfo( 'version' ),
			'PHP Version' => PHP_VERSION,
			'MySQL Version' => $wpdb->db_version(),
			'Theme Version' => wp_get_theme()->get( 'Version' ),
			'Memory Limit' => WP_MEMORY_LIMIT,
			'Current Memory' => self::get_memory_usage(),
			'Peak Memory' => self::get_memory_usage( true ),
			'Max Execution Time' => ini_get( 'max_execution_time' ) . 's',
			'Upload Max Size' => ini_get( 'upload_max_filesize' ),
			'Post Max Size' => ini_get( 'post_max_size' ),
			'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
			'Environment' => self::is_local_environment() ? 'Local' : 'Production',
		);
		
		echo '<div style="background:#fff;border:2px solid #17a2b8;padding:20px;margin:20px;border-radius:5px;">';
		echo '<h3 style="margin-top:0;color:#17a2b8;">System Information</h3>';
		echo '<table style="width:100%;border-collapse:collapse;">';
		foreach ( $info as $label => $value ) {
			echo '<tr style="border-bottom:1px solid #dee2e6;">';
			echo '<td style="padding:10px;font-weight:bold;width:40%;">' . esc_html( $label ) . '</td>';
			echo '<td style="padding:10px;">' . esc_html( $value ) . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
	}
	
	/**
	 * Get WordPress hooks in use
	 */
	public static function get_hooks( $hook_name = null ) {
		global $wp_filter;
		
		if ( $hook_name ) {
			return $wp_filter[ $hook_name ] ?? array();
		}
		
		return $wp_filter;
	}
	
	/**
	 * Display registered hooks
	 */
	public static function display_hooks( $hook_name = null ) {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		$hooks = self::get_hooks( $hook_name );
		
		echo '<div style="background:#fff;border:2px solid #28a745;padding:20px;margin:20px;border-radius:5px;">';
		echo '<h3 style="margin-top:0;color:#28a745;">Registered Hooks' . ( $hook_name ? ": $hook_name" : '' ) . '</h3>';
		
		if ( $hook_name ) {
			$hooks = array( $hook_name => $hooks );
		}
		
		echo '<div style="max-height:600px;overflow-y:auto;">';
		foreach ( $hooks as $name => $hook ) {
			if ( ! $hook instanceof WP_Hook ) {
				continue;
			}
			
			echo '<details style="margin-bottom:15px;padding:10px;background:#f8f9fa;border-radius:3px;">';
			echo '<summary style="cursor:pointer;font-weight:bold;color:#28a745;">' . esc_html( $name ) . ' (' . count( $hook->callbacks ) . ' callbacks)</summary>';
			echo '<ul style="margin:10px 0 0 20px;">';
			
			foreach ( $hook->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					$function_name = 'Unknown';
					
					if ( is_string( $callback['function'] ) ) {
						$function_name = $callback['function'];
					} elseif ( is_array( $callback['function'] ) ) {
						$function_name = ( is_object( $callback['function'][0] ) ? get_class( $callback['function'][0] ) : $callback['function'][0] ) . '::' . $callback['function'][1];
					}
					
					echo '<li style="margin:5px 0;"><code>Priority ' . $priority . ':</code> ' . esc_html( $function_name ) . '</li>';
				}
			}
			
			echo '</ul>';
			echo '</details>';
		}
		echo '</div>';
		echo '</div>';
	}
	
	/**
	 * Template hierarchy helper
	 */
	public static function show_template_hierarchy() {
		if ( ! self::is_dev_mode() ) {
			return;
		}
		
		global $template;
		
		echo '<div style="position:fixed;bottom:20px;right:20px;background:#000;color:#0f0;padding:15px;border-radius:5px;font-family:monospace;font-size:12px;z-index:999999;max-width:400px;">';
		echo '<strong>Current Template:</strong><br>';
		echo esc_html( str_replace( get_template_directory(), '', $template ) );
		
		if ( is_singular() ) {
			echo '<br><strong>Post Type:</strong> ' . get_post_type();
		}
		
		if ( is_archive() ) {
			echo '<br><strong>Archive Type:</strong> ';
			if ( is_category() ) echo 'Category';
			if ( is_tag() ) echo 'Tag';
			if ( is_tax() ) echo 'Taxonomy';
			if ( is_author() ) echo 'Author';
			if ( is_date() ) echo 'Date';
		}
		
		echo '</div>';
	}
	
	/**
	 * Asset version helper for cache busting
	 */
	public static function asset_version( $file_path ) {
		if ( self::is_dev_mode() ) {
			return time(); // Always fresh in dev
		}
		
		$full_path = get_template_directory() . $file_path;
		
		if ( file_exists( $full_path ) ) {
			return filemtime( $full_path );
		}
		
		return SWGTHEME_VERSION;
	}
	
	/**
	 * Get template part with context
	 */
	public static function get_template_part_with_context( $slug, $name = null, $args = array() ) {
		self::debug_log( "Loading template part: $slug" . ( $name ? "-$name" : '' ), $args, 'Template' );
		
		if ( ! empty( $args ) ) {
			extract( $args );
		}
		
		get_template_part( $slug, $name );
	}
}

/**
 * Convenience functions
 */

if ( ! function_exists( 'swg_dd' ) ) {
	function swg_dd( ...$vars ) {
		SWGTheme_Dev_Tools::dd( ...$vars );
	}
}

if ( ! function_exists( 'swg_dump' ) ) {
	function swg_dump( ...$vars ) {
		SWGTheme_Dev_Tools::dump( ...$vars );
	}
}

if ( ! function_exists( 'swg_log' ) ) {
	function swg_log( $message, $data = null, $context = '' ) {
		SWGTheme_Dev_Tools::debug_log( $message, $data, $context );
	}
}

if ( ! function_exists( 'swg_timer_start' ) ) {
	function swg_timer_start( $name ) {
		SWGTheme_Dev_Tools::timer_start( $name );
	}
}

if ( ! function_exists( 'swg_timer_end' ) ) {
	function swg_timer_end( $name, $log = true ) {
		return SWGTheme_Dev_Tools::timer_end( $name, $log );
	}
}

if ( ! function_exists( 'swg_is_dev' ) ) {
	function swg_is_dev() {
		return SWGTheme_Dev_Tools::is_dev_mode();
	}
}
