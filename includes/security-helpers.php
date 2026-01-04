<?php
/**
 * Security Hardening Functions
 * Enhanced security features and attack prevention
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Security Manager Class
 */
class SWGTheme_Security {
	
	/**
	 * Maximum login attempts before lockout
	 */
	const MAX_LOGIN_ATTEMPTS = 5;
	
	/**
	 * Lockout duration in seconds (15 minutes)
	 */
	const LOCKOUT_DURATION = 900;
	
	/**
	 * Initialize security features
	 */
	public static function init() {
		// Login security
		add_filter( 'authenticate', array( __CLASS__, 'check_login_attempts' ), 30, 3 );
		add_action( 'wp_login_failed', array( __CLASS__, 'log_failed_login' ) );
		add_action( 'wp_login', array( __CLASS__, 'clear_login_attempts' ), 10, 2 );
		
		// File upload security
		add_filter( 'upload_mimes', array( __CLASS__, 'restrict_mime_types' ) );
		add_filter( 'wp_handle_upload_prefilter', array( __CLASS__, 'validate_file_upload' ) );
		
		// Additional security headers
		add_action( 'send_headers', array( __CLASS__, 'add_security_headers' ) );
		
		// Disable XML-RPC if not needed
		add_filter( 'xmlrpc_enabled', array( __CLASS__, 'disable_xmlrpc' ) );
		
		// Hide WordPress version
		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );
		
		// Disable file editing from admin
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
	}
	
	/**
	 * Check login attempts before authentication
	 */
	public static function check_login_attempts( $user, $username, $password ) {
		if ( empty( $username ) ) {
			return $user;
		}
		
		$ip_address = self::get_client_ip();
		$attempts_key = 'swg_login_attempts_' . md5( $ip_address );
		$lockout_key = 'swg_login_lockout_' . md5( $ip_address );
		
		// Check if IP is locked out
		$lockout_time = get_transient( $lockout_key );
		if ( $lockout_time ) {
			$remaining = $lockout_time - time();
			
			self::log_security_event( 'login_blocked', array(
				'ip' => $ip_address,
				'username' => $username,
				'remaining_time' => $remaining,
			) );
			
			return new WP_Error(
				'too_many_attempts',
				sprintf(
					__( 'Too many failed login attempts. Please try again in %s minutes.', 'swgtheme' ),
					ceil( $remaining / 60 )
				)
			);
		}
		
		return $user;
	}
	
	/**
	 * Log failed login attempt
	 */
	public static function log_failed_login( $username ) {
		$ip_address = self::get_client_ip();
		$attempts_key = 'swg_login_attempts_' . md5( $ip_address );
		$lockout_key = 'swg_login_lockout_' . md5( $ip_address );
		
		// Get current attempts
		$attempts = get_transient( $attempts_key );
		$attempts = $attempts ? $attempts + 1 : 1;
		
		// Store attempts (expires in 1 hour)
		set_transient( $attempts_key, $attempts, HOUR_IN_SECONDS );
		
		// Log the event
		self::log_security_event( 'login_failed', array(
			'ip' => $ip_address,
			'username' => $username,
			'attempts' => $attempts,
		) );
		
		// Lock out if max attempts reached
		if ( $attempts >= self::MAX_LOGIN_ATTEMPTS ) {
			set_transient( $lockout_key, time() + self::LOCKOUT_DURATION, self::LOCKOUT_DURATION );
			
			self::log_security_event( 'login_lockout', array(
				'ip' => $ip_address,
				'username' => $username,
				'attempts' => $attempts,
				'duration' => self::LOCKOUT_DURATION,
			) );
			
			// Notify admin if enabled
			if ( get_option( 'swgtheme_security_notify_lockouts', '1' ) === '1' ) {
				self::notify_admin_lockout( $ip_address, $username, $attempts );
			}
		}
	}
	
	/**
	 * Clear login attempts on successful login
	 */
	public static function clear_login_attempts( $user_login, $user ) {
		$ip_address = self::get_client_ip();
		$attempts_key = 'swg_login_attempts_' . md5( $ip_address );
		
		delete_transient( $attempts_key );
		
		self::log_security_event( 'login_success', array(
			'ip' => $ip_address,
			'username' => $user_login,
			'user_id' => $user->ID,
		) );
	}
	
	/**
	 * Restrict allowed MIME types
	 */
	public static function restrict_mime_types( $mimes ) {
		// Remove potentially dangerous file types
		unset( $mimes['exe'] );
		unset( $mimes['com'] );
		unset( $mimes['bat'] );
		unset( $mimes['cmd'] );
		unset( $mimes['sh'] );
		unset( $mimes['php'] );
		unset( $mimes['pl'] );
		unset( $mimes['cgi'] );
		unset( $mimes['py'] );
		unset( $mimes['js'] );
		
		// Allow SVG only for admins
		if ( ! current_user_can( 'administrator' ) ) {
			unset( $mimes['svg'] );
			unset( $mimes['svgz'] );
		}
		
		return $mimes;
	}
	
	/**
	 * Validate file uploads
	 */
	public static function validate_file_upload( $file ) {
		// Check file size (max 10MB)
		$max_size = 10 * 1024 * 1024; // 10MB in bytes
		
		if ( $file['size'] > $max_size ) {
			$file['error'] = sprintf(
				__( 'File size exceeds maximum allowed size of %s MB.', 'swgtheme' ),
				round( $max_size / 1024 / 1024 )
			);
			
			self::log_security_event( 'file_upload_blocked', array(
				'reason' => 'file_too_large',
				'file_name' => $file['name'],
				'file_size' => $file['size'],
			) );
			
			return $file;
		}
		
		// Additional MIME type validation
		$allowed_types = get_allowed_mime_types();
		$file_type = wp_check_filetype( $file['name'], $allowed_types );
		
		if ( ! $file_type['type'] ) {
			$file['error'] = __( 'This file type is not allowed for security reasons.', 'swgtheme' );
			
			self::log_security_event( 'file_upload_blocked', array(
				'reason' => 'invalid_file_type',
				'file_name' => $file['name'],
			) );
			
			return $file;
		}
		
		// Check for double extensions (e.g., .php.jpg)
		if ( preg_match( '/\.(php|exe|sh|bat|cmd|pl|cgi|py)\./i', $file['name'] ) ) {
			$file['error'] = __( 'File contains dangerous double extension.', 'swgtheme' );
			
			self::log_security_event( 'file_upload_blocked', array(
				'reason' => 'double_extension',
				'file_name' => $file['name'],
			) );
			
			return $file;
		}
		
		return $file;
	}
	
	/**
	 * Add additional security headers
	 */
	public static function add_security_headers() {
		if ( ! is_admin() ) {
			// Prevent MIME type sniffing
			header( 'X-Content-Type-Options: nosniff' );
			
			// Referrer Policy
			header( 'Referrer-Policy: strict-origin-when-cross-origin' );
			
			// Permissions Policy (formerly Feature Policy)
			header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
			
			// Expect-CT header
			header( 'Expect-CT: enforce, max-age=86400' );
		}
	}
	
	/**
	 * Disable XML-RPC
	 */
	public static function disable_xmlrpc() {
		return get_option( 'swgtheme_security_disable_xmlrpc', '1' ) === '1' ? false : true;
	}
	
	/**
	 * Log security event
	 */
	public static function log_security_event( $event_type, $data = array() ) {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'swgtheme_security_log';
		
		// Create table if it doesn't exist
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
			self::create_security_log_table();
		}
		
		$wpdb->insert(
			$table_name,
			array(
				'event_type' => $event_type,
				'event_data' => wp_json_encode( $data ),
				'user_id' => get_current_user_id(),
				'ip_address' => self::get_client_ip(),
				'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s' )
		);
	}
	
	/**
	 * Create security log table
	 */
	public static function create_security_log_table() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'swgtheme_security_log';
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			event_type varchar(50) NOT NULL,
			event_data longtext,
			user_id bigint(20) UNSIGNED DEFAULT 0,
			ip_address varchar(100),
			user_agent varchar(255),
			created_at datetime NOT NULL,
			PRIMARY KEY (id),
			KEY event_type (event_type),
			KEY ip_address (ip_address),
			KEY created_at (created_at)
		) $charset_collate;";
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
	
	/**
	 * Get client IP address
	 */
	public static function get_client_ip() {
		$ip_keys = array(
			'HTTP_CF_CONNECTING_IP', // Cloudflare
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);
		
		foreach ( $ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				
				// Handle comma-separated IPs
				if ( strpos( $ip, ',' ) !== false ) {
					$ips = explode( ',', $ip );
					$ip = trim( $ips[0] );
				}
				
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
					return $ip;
				}
			}
		}
		
		return '0.0.0.0';
	}
	
	/**
	 * Notify admin of lockout
	 */
	private static function notify_admin_lockout( $ip_address, $username, $attempts ) {
		$admin_email = get_option( 'admin_email' );
		$site_name = get_bloginfo( 'name' );
		
		$subject = sprintf( '[%s] Security Alert: Account Lockout', $site_name );
		
		$message = sprintf(
			"A user account has been locked out due to too many failed login attempts.\n\n" .
			"Details:\n" .
			"Username: %s\n" .
			"IP Address: %s\n" .
			"Failed Attempts: %d\n" .
			"Time: %s\n\n" .
			"The account will be automatically unlocked in %d minutes.\n\n" .
			"If this was not you, please check your security settings.",
			$username,
			$ip_address,
			$attempts,
			current_time( 'mysql' ),
			ceil( self::LOCKOUT_DURATION / 60 )
		);
		
		wp_mail( $admin_email, $subject, $message );
	}
	
	/**
	 * Get security logs
	 */
	public static function get_security_logs( $args = array() ) {
		global $wpdb;
		
		$defaults = array(
			'limit' => 100,
			'offset' => 0,
			'event_type' => '',
			'ip_address' => '',
			'orderby' => 'created_at',
			'order' => 'DESC',
		);
		
		$args = wp_parse_args( $args, $defaults );
		$table_name = $wpdb->prefix . 'swgtheme_security_log';
		
		$where = array( '1=1' );
		
		if ( ! empty( $args['event_type'] ) ) {
			$where[] = $wpdb->prepare( 'event_type = %s', $args['event_type'] );
		}
		
		if ( ! empty( $args['ip_address'] ) ) {
			$where[] = $wpdb->prepare( 'ip_address = %s', $args['ip_address'] );
		}
		
		$where_sql = implode( ' AND ', $where );
		$order_sql = sprintf( '%s %s', sanitize_sql_orderby( $args['orderby'] ), $args['order'] );
		
		$sql = "SELECT * FROM $table_name WHERE $where_sql ORDER BY $order_sql LIMIT %d OFFSET %d";
		
		return $wpdb->get_results( $wpdb->prepare( $sql, $args['limit'], $args['offset'] ) );
	}
	
	/**
	 * Get blocked IPs
	 */
	public static function get_blocked_ips() {
		global $wpdb;
		
		$transients = $wpdb->get_results(
			"SELECT option_name, option_value FROM $wpdb->options 
			WHERE option_name LIKE '_transient_swg_login_lockout_%'"
		);
		
		$blocked_ips = array();
		
		foreach ( $transients as $transient ) {
			$ip_hash = str_replace( '_transient_swg_login_lockout_', '', $transient->option_name );
			$lockout_time = (int) $transient->option_value;
			$remaining = $lockout_time - time();
			
			if ( $remaining > 0 ) {
				// Try to find the IP from recent logs
				$table_name = $wpdb->prefix . 'swgtheme_security_log';
				$log = $wpdb->get_row( $wpdb->prepare(
					"SELECT ip_address FROM $table_name 
					WHERE event_type = 'login_lockout' 
					AND MD5(ip_address) = %s 
					ORDER BY created_at DESC LIMIT 1",
					$ip_hash
				) );
				
				if ( $log ) {
					$blocked_ips[] = array(
						'ip' => $log->ip_address,
						'remaining' => $remaining,
						'unlock_time' => date( 'Y-m-d H:i:s', $lockout_time ),
					);
				}
			}
		}
		
		return $blocked_ips;
	}
	
	/**
	 * Manually unblock an IP
	 */
	public static function unblock_ip( $ip_address ) {
		$lockout_key = 'swg_login_lockout_' . md5( $ip_address );
		$attempts_key = 'swg_login_attempts_' . md5( $ip_address );
		
		delete_transient( $lockout_key );
		delete_transient( $attempts_key );
		
		self::log_security_event( 'ip_unblocked', array(
			'ip' => $ip_address,
			'unblocked_by' => get_current_user_id(),
		) );
		
		return true;
	}
}

// Initialize security
SWGTheme_Security::init();

/**
 * Helper Functions
 */

/**
 * Sanitize and validate input with specific rules
 */
function swg_sanitize_input( $input, $type = 'text' ) {
	switch ( $type ) {
		case 'email':
			return sanitize_email( $input );
			
		case 'url':
			return esc_url_raw( $input );
			
		case 'int':
			return absint( $input );
			
		case 'float':
			return floatval( $input );
			
		case 'slug':
			return sanitize_title( $input );
			
		case 'key':
			return sanitize_key( $input );
			
		case 'html':
			return wp_kses_post( $input );
			
		case 'textarea':
			return sanitize_textarea_field( $input );
			
		case 'text':
		default:
			return sanitize_text_field( $input );
	}
}

/**
 * Escape output for different contexts
 */
function swg_escape_output( $output, $context = 'html' ) {
	switch ( $context ) {
		case 'attr':
			return esc_attr( $output );
			
		case 'url':
			return esc_url( $output );
			
		case 'js':
			return esc_js( $output );
			
		case 'textarea':
			return esc_textarea( $output );
			
		case 'html':
		default:
			return esc_html( $output );
	}
}

/**
 * Verify nonce with custom error handling
 */
function swg_verify_nonce( $nonce, $action = -1, $log_failure = true ) {
	$verified = wp_verify_nonce( $nonce, $action );
	
	if ( ! $verified && $log_failure ) {
		SWGTheme_Security::log_security_event( 'nonce_verification_failed', array(
			'action' => $action,
			'referer' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '',
		) );
	}
	
	return $verified;
}
