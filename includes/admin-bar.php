<?php
/**
 * Admin Bar Customization
 * Add custom menu items to WordPress admin bar for quick access
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add SWG Theme menu to admin bar
 */
function swgtheme_admin_bar_menu( $wp_admin_bar ) {
	// Only show to users who can manage options
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Main SWG Theme menu item
	$wp_admin_bar->add_node( array(
		'id'    => 'swg-theme',
		'title' => '⚙️ SWG Theme',
		'href'  => admin_url( 'themes.php?page=swgtheme-options' ),
		'meta'  => array(
			'title' => __( 'SWG Theme Settings', 'swgtheme' ),
		),
	) );
	
	// --- Settings Group ---
	$wp_admin_bar->add_group( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-settings-group',
		'meta'   => array(
			'class' => 'ab-sub-secondary',
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-theme-options',
		'title'  => '🎨 Theme Options',
		'href'   => admin_url( 'themes.php?page=swgtheme-options' ),
		'meta'   => array(
			'title' => __( 'Configure theme settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-slider-settings',
		'title'  => '🖼️ Slider Settings',
		'href'   => admin_url( 'edit.php?post_type=swg_images&page=swg-slider-options' ),
		'meta'   => array(
			'title' => __( 'Manage slider images', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-user-social',
		'title'  => '👥 User Social Links',
		'href'   => admin_url( 'themes.php?page=swgtheme-user-social' ),
		'meta'   => array(
			'title' => __( 'Configure social media links', 'swgtheme' ),
		),
	) );
	
	// --- Security & Management ---
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-security',
		'title'  => '🔒 Security',
		'href'   => admin_url( 'themes.php?page=swgtheme-security' ),
		'meta'   => array(
			'title' => __( 'Security dashboard and logs', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-integrations',
		'title'  => '🔌 Integrations',
		'href'   => admin_url( 'themes.php?page=swgtheme-integrations' ),
		'meta'   => array(
			'title' => __( 'Third-party service integrations', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-performance',
		'title'  => '⚡ Performance',
		'href'   => admin_url( 'themes.php?page=swgtheme-performance' ),
		'meta'   => array(
			'title' => __( 'Performance optimization settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-admin-management',
		'title'  => '⚙️ Admin Management',
		'href'   => admin_url( 'themes.php?page=swgtheme-admin-management' ),
		'meta'   => array(
			'title' => __( 'Admin backend customization', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-ux-settings',
		'title'  => '🎯 UX & Interaction',
		'href'   => admin_url( 'themes.php?page=swgtheme-ux' ),
		'meta'   => array(
			'title' => __( 'User experience settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-seo',
		'title'  => '📊 Advanced SEO',
		'href'   => admin_url( 'themes.php?page=swgtheme-advanced-seo' ),
		'meta'   => array(
			'title' => __( 'SEO and analytics settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-membership',
		'title'  => '👥 Membership',
		'href'   => admin_url( 'themes.php?page=swgtheme-membership' ),
		'meta'   => array(
			'title' => __( 'Membership and user management', 'swgtheme' ),
		),
	) );
	
	// --- Content Management ---
	$wp_admin_bar->add_group( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-content-group',
		'meta'   => array(
			'class' => 'ab-sub-secondary',
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-content-group',
		'id'     => 'swg-slider-images',
		'title'  => '🖼️ Manage Slider',
		'href'   => admin_url( 'edit.php?post_type=swg_images' ),
		'meta'   => array(
			'title' => __( 'Manage slider images', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-content-group',
		'id'     => 'swg-user-badges',
		'title'  => '🏆 User Badges',
		'href'   => admin_url( 'users.php?page=swgtheme-badges' ),
		'meta'   => array(
			'title' => __( 'Manage user badges', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-content-group',
		'id'     => 'swg-menus',
		'title'  => '📋 Menus',
		'href'   => admin_url( 'nav-menus.php' ),
		'meta'   => array(
			'title' => __( 'Manage navigation menus', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-content-group',
		'id'     => 'swg-widgets',
		'title'  => '🧩 Widgets',
		'href'   => admin_url( 'widgets.php' ),
		'meta'   => array(
			'title' => __( 'Manage widgets', 'swgtheme' ),
		),
	) );
	
	// --- Developer Tools (only if enabled) ---
	if ( function_exists( 'is_dev_mode' ) && is_dev_mode() ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-theme',
			'id'     => 'swg-dev-tools',
			'title'  => '🔧 Developer Tools',
			'href'   => '#',
			'meta'   => array(
				'title' => __( 'Developer utilities', 'swgtheme' ),
			),
		) );
		
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-dev-tools',
			'id'     => 'swg-clear-cache',
			'title'  => '🗑️ Clear All Caches',
			'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=swg_clear_cache' ), 'swg_clear_cache' ),
			'meta'   => array(
				'title' => __( 'Clear transients and object cache', 'swgtheme' ),
			),
		) );
		
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-dev-tools',
			'id'     => 'swg-regenerate-colors',
			'title'  => '🎨 Regenerate Custom Colors',
			'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=swg_regenerate_colors' ), 'swg_regenerate_colors' ),
			'meta'   => array(
				'title' => __( 'Rebuild custom-colors.css file', 'swgtheme' ),
			),
		) );
		
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-dev-tools',
			'id'     => 'swg-view-debug-log',
			'title'  => '📋 Debug Log',
			'href'   => admin_url( 'admin.php?page=swg-debug-log' ),
			'meta'   => array(
				'title' => __( 'View WordPress debug.log', 'swgtheme' ),
			),
		) );
	}
	
	// --- Quick Actions ---
	$wp_admin_bar->add_group( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-quick-actions',
		'meta'   => array(
			'class' => 'ab-sub-secondary',
		),
	) );
	
	// Dark mode toggle (frontend only)
	if ( ! is_admin() && get_option( 'swgtheme_enable_dark_mode', '1' ) === '1' ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-quick-actions',
			'id'     => 'swg-toggle-dark-mode',
			'title'  => '🌓 Toggle Dark Mode',
			'href'   => '#',
			'meta'   => array(
				'title'   => __( 'Switch between light and dark themes', 'swgtheme' ),
				'onclick' => 'if(typeof toggleDarkMode === "function"){toggleDarkMode();} return false;',
			),
		) );
	}
	
	// View on frontend (admin only)
	if ( is_admin() ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-quick-actions',
			'id'     => 'swg-view-site',
			'title'  => '🌐 View Site',
			'href'   => home_url( '/' ),
			'meta'   => array(
				'title'  => __( 'View site frontend', 'swgtheme' ),
				'target' => '_blank',
			),
		) );
	}
	
	// Customize link
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-customize',
		'title'  => '✏️ Customize',
		'href'   => admin_url( 'customize.php' ),
		'meta'   => array(
			'title' => __( 'Open WordPress Customizer', 'swgtheme' ),
		),
	) );
	
	// New Post
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-new-post',
		'title'  => '📝 New Post',
		'href'   => admin_url( 'post-new.php' ),
		'meta'   => array(
			'title' => __( 'Create a new post', 'swgtheme' ),
		),
	) );
	
	// New Page
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-new-page',
		'title'  => '📄 New Page',
		'href'   => admin_url( 'post-new.php?post_type=page' ),
		'meta'   => array(
			'title' => __( 'Create a new page', 'swgtheme' ),
		),
	) );
	
	// Media Library
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-media',
		'title'  => '📁 Media Library',
		'href'   => admin_url( 'upload.php' ),
		'meta'   => array(
			'title' => __( 'Manage media files', 'swgtheme' ),
		),
	) );
	
	// Users
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-users',
		'title'  => '👤 Users',
		'href'   => admin_url( 'users.php' ),
		'meta'   => array(
			'title' => __( 'Manage users', 'swgtheme' ),
		),
	) );
	
	// Plugins
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-quick-actions',
		'id'     => 'swg-plugins',
		'title'  => '🔌 Plugins',
		'href'   => admin_url( 'plugins.php' ),
		'meta'   => array(
			'title' => __( 'Manage plugins', 'swgtheme' ),
		),
	) );
	
	// --- System Info ---
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-info',
		'title'  => 'ℹ️ Theme Info',
		'href'   => '#',
		'meta'   => array(
			'title' => __( 'Theme version and information', 'swgtheme' ),
		),
	) );
	
	// Theme version
	$theme = wp_get_theme();
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-info',
		'id'     => 'swg-version',
		'title'  => sprintf( __( 'Version: %s', 'swgtheme' ), $theme->get( 'Version' ) ),
		'href'   => '#',
		'meta'   => array(
			'class' => 'swg-admin-bar-info',
		),
	) );
	
	// WordPress version
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-info',
		'id'     => 'swg-wp-version',
		'title'  => sprintf( __( 'WordPress: %s', 'swgtheme' ), get_bloginfo( 'version' ) ),
		'href'   => '#',
		'meta'   => array(
			'class' => 'swg-admin-bar-info',
		),
	) );
	
	// PHP version
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-info',
		'id'     => 'swg-php-version',
		'title'  => sprintf( __( 'PHP: %s', 'swgtheme' ), PHP_VERSION ),
		'href'   => '#',
		'meta'   => array(
			'class' => 'swg-admin-bar-info',
		),
	) );
	
	// Active integrations count
	$active_integrations = 0;
	if ( get_option( 'swgtheme_mailchimp_api_key' ) ) {
		$active_integrations++;
	}
	if ( get_option( 'swgtheme_discord_webhook_url' ) ) {
		$active_integrations++;
	}
	if ( get_option( 'swgtheme_twitch_client_id' ) ) {
		$active_integrations++;
	}
	if ( get_option( 'swgtheme_youtube_api_key' ) ) {
		$active_integrations++;
	}
	
	if ( $active_integrations > 0 ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'swg-info',
			'id'     => 'swg-integrations-count',
			'title'  => sprintf( __( 'Active Integrations: %d', 'swgtheme' ), $active_integrations ),
			'href'   => admin_url( 'themes.php?page=swgtheme-integrations' ),
			'meta'   => array(
				'class' => 'swg-admin-bar-info',
			),
		) );
	}
}
add_action( 'admin_bar_menu', 'swgtheme_admin_bar_menu', 100 );

/**
 * Handle clear cache action
 */
function swgtheme_handle_clear_cache() {
	// Verify nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'swg_clear_cache' ) ) {
		wp_die( __( 'Security check failed', 'swgtheme' ) );
	}
	
	// Check permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to perform this action', 'swgtheme' ) );
	}
	
	global $wpdb;
	
	// Delete all transients
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'" );
	
	// Clear object cache
	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}
	
	// Clear theme cache files
	$cache_files = array(
		get_template_directory() . '/css/custom-colors.css',
	);
	
	foreach ( $cache_files as $file ) {
		if ( file_exists( $file ) ) {
			touch( $file ); // Update modification time to force reload
		}
	}
	
	// Redirect back with success message
	wp_safe_redirect( add_query_arg( 'swg_cache_cleared', '1', wp_get_referer() ?: admin_url() ) );
	exit;
}
add_action( 'admin_post_swg_clear_cache', 'swgtheme_handle_clear_cache' );

/**
 * Handle regenerate colors action
 */
function swgtheme_handle_regenerate_colors() {
	// Verify nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'swg_regenerate_colors' ) ) {
		wp_die( __( 'Security check failed', 'swgtheme' ) );
	}
	
	// Check permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to perform this action', 'swgtheme' ) );
	}
	
	// Regenerate custom colors CSS
	if ( function_exists( 'swgtheme_custom_colors' ) ) {
		swgtheme_custom_colors();
	}
	
	// Redirect back with success message
	wp_safe_redirect( add_query_arg( 'swg_colors_regenerated', '1', wp_get_referer() ?: admin_url() ) );
	exit;
}
add_action( 'admin_post_swg_regenerate_colors', 'swgtheme_handle_regenerate_colors' );

/**
 * Add admin notices for actions
 */
function swgtheme_admin_bar_notices() {
	if ( isset( $_GET['swg_cache_cleared'] ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'All caches have been cleared successfully.', 'swgtheme' ); ?></p>
		</div>
		<?php
	}
	
	if ( isset( $_GET['swg_colors_regenerated'] ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Custom colors CSS file has been regenerated.', 'swgtheme' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'swgtheme_admin_bar_notices' );

/**
 * Custom CSS for admin bar items
 */
function swgtheme_admin_bar_css() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style type="text/css">
		#wpadminbar .swg-admin-bar-info a {
			cursor: default !important;
			color: #999 !important;
		}
		#wpadminbar .swg-admin-bar-info a:hover {
			background: transparent !important;
			color: #999 !important;
		}
		#wpadminbar #wp-admin-bar-swg-theme > .ab-item {
			font-weight: 600;
		}
		#wpadminbar .ab-sub-secondary {
			border-top: 1px solid rgba(255,255,255,0.1);
			margin-top: 5px;
			padding-top: 5px;
		}
		/* Dark mode toggle highlight on frontend */
		body:not(.wp-admin) #wpadminbar #wp-admin-bar-swg-toggle-dark-mode > .ab-item:hover {
			background: #46b450 !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'swgtheme_admin_bar_css' );
add_action( 'admin_head', 'swgtheme_admin_bar_css' );

/**
 * Create debug log viewer page (if dev mode enabled)
 */
function swgtheme_debug_log_page() {
	add_submenu_page(
		null, // Hidden from menu
		__( 'Debug Log', 'swgtheme' ),
		__( 'Debug Log', 'swgtheme' ),
		'manage_options',
		'swg-debug-log',
		'swgtheme_debug_log_page_content'
	);
}

/**
 * Debug log page content
 */
function swgtheme_debug_log_page_content() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to access this page', 'swgtheme' ) );
	}
	
	$log_file = WP_CONTENT_DIR . '/debug.log';
	$log_exists = file_exists( $log_file );
	$log_size = $log_exists ? filesize( $log_file ) : 0;
	$max_lines = 500; // Show last 500 lines
	
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'WordPress Debug Log', 'swgtheme' ); ?></h1>
		
		<?php if ( ! $log_exists ) : ?>
			<div class="notice notice-info">
				<p><?php esc_html_e( 'Debug log file does not exist yet.', 'swgtheme' ); ?></p>
				<p><?php esc_html_e( 'To enable debug logging, add these lines to wp-config.php:', 'swgtheme' ); ?></p>
				<pre style="background: #f0f0f0; padding: 15px; border-left: 3px solid #0073aa;">
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);</pre>
			</div>
		<?php else : ?>
			<div class="notice notice-info">
				<p>
					<strong><?php esc_html_e( 'Log file:', 'swgtheme' ); ?></strong> <?php echo esc_html( $log_file ); ?><br/>
					<strong><?php esc_html_e( 'File size:', 'swgtheme' ); ?></strong> <?php echo esc_html( size_format( $log_size ) ); ?><br/>
					<strong><?php esc_html_e( 'Showing:', 'swgtheme' ); ?></strong> <?php echo esc_html( sprintf( __( 'Last %d lines', 'swgtheme' ), $max_lines ) ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=swg_clear_debug_log' ), 'swg_clear_debug_log' ) ); ?>" 
						class="button button-secondary" 
						onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to clear the debug log?', 'swgtheme' ); ?>');">
						<?php esc_html_e( 'Clear Log', 'swgtheme' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=swg-debug-log' ) ); ?>" 
						class="button button-secondary">
						<?php esc_html_e( 'Refresh', 'swgtheme' ); ?>
					</a>
				</p>
			</div>
			
			<div style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.5; overflow-x: auto; max-height: 600px; overflow-y: auto;">
				<?php
				// Read last N lines
				$lines = array();
				$file_handle = fopen( $log_file, 'r' );
				
				if ( $file_handle ) {
					// Use tail-like approach for efficiency
					$buffer = 4096;
					$offset = -1;
					$line_count = 0;
					
					fseek( $file_handle, 0, SEEK_END );
					$file_size = ftell( $file_handle );
					
					if ( $file_size > 0 ) {
						$data = '';
						
						while ( $line_count < $max_lines && $file_size + $offset > 0 ) {
							$seek_offset = max( $file_size + $offset - $buffer + 1, 0 );
							fseek( $file_handle, $seek_offset, SEEK_SET );
							$chunk = fread( $file_handle, min( $buffer, $file_size + $offset + 1 - $seek_offset ) );
							$offset -= $buffer;
							$data = $chunk . $data;
							$line_count = substr_count( $data, "\n" );
						}
						
						$lines = explode( "\n", $data );
						$lines = array_slice( $lines, -$max_lines );
					}
					
					fclose( $file_handle );
				}
				
				if ( ! empty( $lines ) ) {
					foreach ( $lines as $line ) {
						if ( empty( trim( $line ) ) ) {
							continue;
						}
						
						// Color code different log levels
						$class = '';
						if ( stripos( $line, 'ERROR' ) !== false || stripos( $line, 'Fatal' ) !== false ) {
							$color = '#ff6b6b';
						} elseif ( stripos( $line, 'WARNING' ) !== false ) {
							$color = '#ffd93d';
						} elseif ( stripos( $line, 'NOTICE' ) !== false ) {
							$color = '#6bcf7f';
						} else {
							$color = '#d4d4d4';
						}
						
						echo '<div style="color: ' . esc_attr( $color ) . '; margin-bottom: 2px;">' . esc_html( $line ) . '</div>';
					}
				} else {
					echo '<div style="color: #999;">' . esc_html__( 'Log file is empty.', 'swgtheme' ) . '</div>';
				}
				?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Handle clear debug log action
 */
function swgtheme_handle_clear_debug_log() {
	// Verify nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'swg_clear_debug_log' ) ) {
		wp_die( __( 'Security check failed', 'swgtheme' ) );
	}
	
	// Check permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to perform this action', 'swgtheme' ) );
	}
	
	$log_file = WP_CONTENT_DIR . '/debug.log';
	
	if ( file_exists( $log_file ) ) {
		file_put_contents( $log_file, '' );
	}
	
	// Redirect back with success message
	wp_safe_redirect( add_query_arg( 'swg_log_cleared', '1', admin_url( 'admin.php?page=swg-debug-log' ) ) );
	exit;
}
add_action( 'admin_post_swg_clear_debug_log', 'swgtheme_handle_clear_debug_log' );

// Register debug log page
if ( function_exists( 'is_dev_mode' ) && is_dev_mode() ) {
	add_action( 'admin_menu', 'swgtheme_debug_log_page' );
}
