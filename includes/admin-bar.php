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
	
	// --- Settings Group (Alphabetically Sorted) ---
	$wp_admin_bar->add_group( array(
		'parent' => 'swg-theme',
		'id'     => 'swg-settings-group',
		'meta'   => array(
			'class' => 'ab-sub-secondary',
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-admin-management',
		'title'  => '⚙️ Admin Management',
		'href'   => admin_url( 'themes.php?page=swgtheme-admin-management' ),
		'meta'   => array(
			'title' => __( 'Admin backend customization', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-seo',
		'title'  => '📊 Advanced SEO',
		'href'   => admin_url( 'themes.php?page=swgtheme-advanced-seo' ),
		'meta'   => array(
			'title' => __( 'SEO and analytics settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-documentation',
		'title'  => '📚 Documentation & Support',
		'href'   => admin_url( 'themes.php?page=swgtheme-documentation' ),
		'meta'   => array(
			'title' => __( 'Documentation and support settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-integrations',
		'title'  => '🔌 Integrations',
		'href'   => admin_url( 'themes.php?page=swgtheme-integrations' ),
		'meta'   => array(
			'title' => __( 'Third-party service integrations', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-membership',
		'title'  => '👥 Membership',
		'href'   => admin_url( 'themes.php?page=swgtheme-membership' ),
		'meta'   => array(
			'title' => __( 'Membership and user management', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-multilang',
		'title'  => '🌍 Multi-language',
		'href'   => admin_url( 'themes.php?page=swgtheme-multilang' ),
		'meta'   => array(
			'title' => __( 'Multi-language and translation settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-performance',
		'title'  => '⚡ Performance',
		'href'   => admin_url( 'themes.php?page=swgtheme-performance' ),
		'meta'   => array(
			'title' => __( 'Performance optimization settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-security',
		'title'  => '🔒 Security',
		'href'   => admin_url( 'themes.php?page=swgtheme-security' ),
		'meta'   => array(
			'title' => __( 'Security dashboard and logs', 'swgtheme' ),
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
		'id'     => 'swg-theme-options',
		'title'  => '🎨 Theme Options',
		'href'   => admin_url( 'themes.php?page=swgtheme-options' ),
		'meta'   => array(
			'title' => __( 'Configure theme settings', 'swgtheme' ),
		),
	) );
	
	$wp_admin_bar->add_node( array(
		'parent' => 'swg-settings-group',
		'id'     => 'swg-ux-settings',
		'title'  => '🎯 UX & Interaction',
		'href'   => admin_url( 'themes.php?page=swgtheme-ux' ),
		'meta'   => array(
			'title' => __( 'User experience settings', 'swgtheme' ),
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
		'href'   => admin_url( 'admin.php?page=swg-theme-info' ),
		'meta'   => array(
			'title' => __( 'Theme version and information', 'swgtheme' ),
		),
	) );
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
		/* Enable scrolling for long admin bar submenus */
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper {
			max-height: calc(100vh - 32px) !important;
			overflow-y: auto !important;
			overflow-x: hidden !important;
		}
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper > .ab-submenu {
			max-height: none !important;
		}
		/* Custom scrollbar styling for admin bar */
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper::-webkit-scrollbar {
			width: 8px;
		}
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper::-webkit-scrollbar-track {
			background: rgba(0,0,0,0.1);
		}
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper::-webkit-scrollbar-thumb {
			background: rgba(255,255,255,0.2);
			border-radius: 4px;
		}
		#wpadminbar #wp-admin-bar-swg-theme > .ab-sub-wrapper::-webkit-scrollbar-thumb:hover {
			background: rgba(255,255,255,0.3);
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

/**
 * Register Theme Info page
 */
function swgtheme_theme_info_page() {
	add_submenu_page(
		null, // Hidden from menu
		__( 'Theme Info', 'swgtheme' ),
		__( 'Theme Info', 'swgtheme' ),
		'manage_options',
		'swg-theme-info',
		'swgtheme_theme_info_page_content'
	);
}
add_action( 'admin_menu', 'swgtheme_theme_info_page' );

/**
 * Theme Info page content
 */
function swgtheme_theme_info_page_content() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to access this page', 'swgtheme' ) );
	}
	
	$theme = wp_get_theme();
	
	// Active integrations count
	$active_integrations = array();
	if ( get_option( 'swgtheme_mailchimp_api_key' ) ) {
		$active_integrations[] = 'MailChimp';
	}
	if ( get_option( 'swgtheme_discord_webhook_url' ) ) {
		$active_integrations[] = 'Discord';
	}
	if ( get_option( 'swgtheme_twitch_client_id' ) ) {
		$active_integrations[] = 'Twitch';
	}
	if ( get_option( 'swgtheme_youtube_api_key' ) ) {
		$active_integrations[] = 'YouTube';
	}
	
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'SWG Theme Information', 'swgtheme' ); ?></h1>
		
		<div class="card" style="max-width: 800px;">
			<h2><?php esc_html_e( 'Theme Details', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Theme Name:', 'swgtheme' ); ?></th>
					<td><strong><?php echo esc_html( $theme->get( 'Name' ) ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Theme Version:', 'swgtheme' ); ?></th>
					<td><strong><?php echo esc_html( $theme->get( 'Version' ) ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Author:', 'swgtheme' ); ?></th>
					<td><?php echo wp_kses_post( $theme->get( 'Author' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Description:', 'swgtheme' ); ?></th>
					<td><?php echo wp_kses_post( $theme->get( 'Description' ) ); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'System Information', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'WordPress Version:', 'swgtheme' ); ?></th>
					<td><strong><?php echo esc_html( get_bloginfo( 'version' ) ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'PHP Version:', 'swgtheme' ); ?></th>
					<td><strong><?php echo esc_html( PHP_VERSION ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'MySQL Version:', 'swgtheme' ); ?></th>
					<td><strong><?php global $wpdb; echo esc_html( $wpdb->db_version() ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Server:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Max Upload Size:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'PHP Memory Limit:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( ini_get( 'memory_limit' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'WordPress Memory Limit:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td>
				</tr>
			</table>
		</div>
		
		<?php if ( ! empty( $active_integrations ) ) : ?>
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'Active Integrations', 'swgtheme' ); ?></h2>
			<p>
				<?php
				printf(
					esc_html( _n( '%d integration is currently active:', '%d integrations are currently active:', count( $active_integrations ), 'swgtheme' ) ),
					count( $active_integrations )
				);
				?>
			</p>
			<ul style="list-style: disc; margin-left: 20px;">
				<?php foreach ( $active_integrations as $integration ) : ?>
					<li><?php echo esc_html( $integration ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=swgtheme-integrations' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Manage Integrations', 'swgtheme' ); ?>
				</a>
			</p>
		</div>
		<?php endif; ?>
		
		<h1 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 3px solid #0073aa;">
			<?php esc_html_e( 'System Information', 'swgtheme' ); ?>
		</h1>
		
		<!-- Tab Navigation -->
		<h2 class="nav-tab-wrapper">
			<a href="#host-system" class="nav-tab nav-tab-active"><?php esc_html_e( 'Host System', 'swgtheme' ); ?></a>
			<a href="#php-info" class="nav-tab"><?php esc_html_e( 'PHP Information', 'swgtheme' ); ?></a>
			<a href="#apache-info" class="nav-tab"><?php esc_html_e( 'Apache Server', 'swgtheme' ); ?></a>
			<a href="#mysql-info" class="nav-tab"><?php esc_html_e( 'MySQL Database', 'swgtheme' ); ?></a>
		</h2>
		
		<!-- Tab 1: Host System Information -->
		<div id="host-system" class="tab-content active">
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'Host System Information', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'Operating system and hardware details:', 'swgtheme' ); ?></p>
			<table class="form-table">
				<tr>
					<th scope="row" style="width: 35%;"><?php esc_html_e( 'Operating System:', 'swgtheme' ); ?></th>
					<td><strong><?php echo esc_html( php_uname( 's' ) . ' ' . php_uname( 'r' ) ); ?></strong></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'OS Version:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( php_uname( 'v' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hostname:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( php_uname( 'n' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Machine Type:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( php_uname( 'm' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Server Architecture:', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( php_uname( 'a' ) ); ?></td>
				</tr>
				<?php
				// Disk space information
				$disk_free = @disk_free_space( ABSPATH );
				$disk_total = @disk_total_space( ABSPATH );
				if ( $disk_free !== false && $disk_total !== false ) {
					$disk_used = $disk_total - $disk_free;
					$disk_percent = ( $disk_used / $disk_total ) * 100;
					?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disk Space (Total):', 'swgtheme' ); ?></th>
						<td><?php echo esc_html( size_format( $disk_total, 2 ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disk Space (Used):', 'swgtheme' ); ?></th>
						<td>
							<?php 
							echo esc_html( size_format( $disk_used, 2 ) ); 
							echo ' (' . esc_html( number_format( $disk_percent, 1 ) ) . '%)';
							?>
							<div style="background: #ddd; height: 20px; border-radius: 3px; margin-top: 5px; overflow: hidden;">
								<div style="background: <?php echo $disk_percent > 90 ? '#dc3232' : ( $disk_percent > 75 ? '#f0b849' : '#46b450' ); ?>; height: 100%; width: <?php echo esc_attr( $disk_percent ); ?>%;"></div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disk Space (Free):', 'swgtheme' ); ?></th>
						<td><?php echo esc_html( size_format( $disk_free, 2 ) ); ?></td>
					</tr>
					<?php
				}
				
				// PHP Memory Usage
				$memory_current = memory_get_usage( true );
				$memory_peak = memory_get_peak_usage( true );
				?>
				<tr>
					<th scope="row"><?php esc_html_e( 'PHP Memory Usage (Current):', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( size_format( $memory_current, 2 ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'PHP Memory Usage (Peak):', 'swgtheme' ); ?></th>
					<td><?php echo esc_html( size_format( $memory_peak, 2 ) ); ?></td>
				</tr>
				<?php
				// CPU Info (Windows specific)
				if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
					$cpu_name = getenv( 'PROCESSOR_IDENTIFIER' );
					$cpu_arch = getenv( 'PROCESSOR_ARCHITECTURE' );
					$cpu_count = getenv( 'NUMBER_OF_PROCESSORS' );
					
					if ( $cpu_name ) {
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'CPU:', 'swgtheme' ); ?></th>
							<td><?php echo esc_html( $cpu_name ); ?></td>
						</tr>
						<?php
					}
					if ( $cpu_arch ) {
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'CPU Architecture:', 'swgtheme' ); ?></th>
							<td><?php echo esc_html( $cpu_arch ); ?></td>
						</tr>
						<?php
					}
					if ( $cpu_count ) {
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Number of Processors:', 'swgtheme' ); ?></th>
							<td><?php echo esc_html( $cpu_count ); ?></td>
						</tr>
						<?php
					}
					
					// Try to get total RAM (Windows)
					@exec( 'wmic computersystem get totalphysicalmemory', $output );
					if ( isset( $output[1] ) && is_numeric( trim( $output[1] ) ) ) {
						$total_ram = trim( $output[1] );
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Total Physical Memory (RAM):', 'swgtheme' ); ?></th>
							<td><?php echo esc_html( size_format( $total_ram, 2 ) ); ?></td>
						</tr>
						<?php
					}
				}
				
				// Server uptime
				if ( function_exists( 'shell_exec' ) && strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
					$uptime_output = @shell_exec( 'net statistics server | find "since"' );
					if ( $uptime_output ) {
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Server Uptime:', 'swgtheme' ); ?></th>
							<td><?php echo esc_html( trim( str_replace( 'Statistics since', 'Since', $uptime_output ) ) ); ?></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
		</div>
		</div>
		
		<!-- Tab 2: PHP Information -->
		<div id="php-info" class="tab-content">
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'PHP Information', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'Complete PHP configuration and environment details:', 'swgtheme' ); ?></p>
			<div style="max-height: 600px; overflow-y: auto; overflow-x: auto; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px;">
				<?php
				ob_start();
				phpinfo();
				$phpinfo = ob_get_clean();
				
				// Remove HTML/HEAD/BODY tags to embed properly
				$phpinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo );
				
				// Add custom styling to phpinfo output
				?>
				<style>
					.phpinfo-container table {
						border-collapse: collapse;
						width: 100%;
					}
					.phpinfo-container td, .phpinfo-container th {
						border: 1px solid #ddd;
						padding: 8px;
						font-size: 12px;
					}
					.phpinfo-container th {
						background-color: #0073aa;
						color: white;
						text-align: left;
					}
					.phpinfo-container tr:nth-child(even) {
						background-color: #f9f9f9;
					}
					.phpinfo-container h2 {
						background-color: #0073aa;
						color: white;
						padding: 10px;
						margin: 0;
						font-size: 16px;
					}
					.phpinfo-container .e {
						background-color: #ccccff;
						font-weight: bold;
					}
					.phpinfo-container .v {
						background-color: #f0f0f0;
					}
					.phpinfo-container .h {
						background-color: #9999cc;
						color: white;
						font-weight: bold;
					}
				</style>
				<div class="phpinfo-container">
					<?php echo $phpinfo; ?>
				</div>
			</div>
		</div>
		</div>
		
		<!-- Tab 3: Apache Server Information -->
		<div id="apache-info" class="tab-content">
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'Apache Server Information', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'Apache/Server configuration and environment details:', 'swgtheme' ); ?></p>
			<div style="max-height: 600px; overflow-y: auto; overflow-x: auto; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
				<table class="widefat striped" style="font-size: 12px; font-family: 'Courier New', monospace;">
					<thead>
						<tr>
							<th style="width: 35%;"><?php esc_html_e( 'Variable', 'swgtheme' ); ?></th>
							<th><?php esc_html_e( 'Value', 'swgtheme' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Apache-specific functions
						if ( function_exists( 'apache_get_version' ) ) {
							?>
							<tr>
								<td><strong>Apache Version</strong></td>
								<td><?php echo esc_html( apache_get_version() ); ?></td>
							</tr>
							<?php
						}
						
						if ( function_exists( 'apache_get_modules' ) ) {
							$modules = apache_get_modules();
							?>
							<tr>
								<td><strong>Loaded Apache Modules</strong></td>
								<td>
									<?php 
									echo esc_html( implode( ', ', array_slice( $modules, 0, 10 ) ) ); 
									if ( count( $modules ) > 10 ) {
										echo '<br><em>' . esc_html( sprintf( __( '... and %d more modules', 'swgtheme' ), count( $modules ) - 10 ) ) . '</em>';
									}
									?>
								</td>
							</tr>
							<?php
						}
						
						// Server variables
						$server_vars = array(
							'SERVER_SOFTWARE' => 'Server Software',
							'SERVER_NAME' => 'Server Name',
							'SERVER_ADDR' => 'Server Address',
							'SERVER_PORT' => 'Server Port',
							'REMOTE_ADDR' => 'Remote Address',
							'DOCUMENT_ROOT' => 'Document Root',
							'SERVER_ADMIN' => 'Server Admin',
							'SERVER_PROTOCOL' => 'Server Protocol',
							'REQUEST_METHOD' => 'Request Method',
							'QUERY_STRING' => 'Query String',
							'HTTP_HOST' => 'HTTP Host',
							'HTTP_USER_AGENT' => 'User Agent',
							'HTTP_ACCEPT' => 'HTTP Accept',
							'HTTP_ACCEPT_LANGUAGE' => 'Accept Language',
							'HTTP_ACCEPT_ENCODING' => 'Accept Encoding',
							'HTTPS' => 'HTTPS Status',
							'SCRIPT_FILENAME' => 'Script Filename',
							'SCRIPT_NAME' => 'Script Name',
							'REQUEST_URI' => 'Request URI',
							'PHP_SELF' => 'PHP Self',
							'GATEWAY_INTERFACE' => 'Gateway Interface',
						);
						
						foreach ( $server_vars as $var => $label ) {
							if ( isset( $_SERVER[$var] ) ) {
								$value = $_SERVER[$var];
								// Truncate very long values
								if ( strlen( $value ) > 200 ) {
									$value = substr( $value, 0, 200 ) . '...';
								}
								?>
								<tr>
									<td><strong><?php echo esc_html( $label ); ?></strong></td>
									<td style="word-break: break-all;"><?php echo esc_html( $value ); ?></td>
								</tr>
								<?php
							}
						}
						
						// Additional environment variables
						?>
						<tr>
							<td colspan="2" style="background: #0073aa; color: white; font-weight: bold; padding: 8px;">
								<?php esc_html_e( 'Environment Variables', 'swgtheme' ); ?>
							</td>
						</tr>
						<?php
						
						$env_vars = array(
							'PATH' => 'System Path',
							'TEMP' => 'Temp Directory',
							'TMP' => 'TMP Directory',
							'WINDIR' => 'Windows Directory',
							'SystemRoot' => 'System Root',
							'PROCESSOR_ARCHITECTURE' => 'Processor Architecture',
							'NUMBER_OF_PROCESSORS' => 'Number of Processors',
						);
						
						foreach ( $env_vars as $var => $label ) {
							$value = getenv( $var );
							if ( $value !== false ) {
								if ( strlen( $value ) > 200 ) {
									$value = substr( $value, 0, 200 ) . '...';
								}
								?>
								<tr>
									<td><strong><?php echo esc_html( $label ); ?></strong></td>
									<td style="word-break: break-all;"><?php echo esc_html( $value ); ?></td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		</div>
		
		<!-- Tab 4: MySQL Database Information -->
		<div id="mysql-info" class="tab-content">
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'MySQL Database Information', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'MySQL/MariaDB server configuration and status:', 'swgtheme' ); ?></p>
			<div style="max-height: 600px; overflow-y: auto; overflow-x: auto; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
				<table class="widefat striped" style="font-size: 12px; font-family: 'Courier New', monospace;">
					<thead>
						<tr>
							<th style="width: 40%;"><?php esc_html_e( 'Variable', 'swgtheme' ); ?></th>
							<th><?php esc_html_e( 'Value', 'swgtheme' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						global $wpdb;
						
						// Basic database info
						?>
						<tr>
							<td colspan="2" style="background: #0073aa; color: white; font-weight: bold; padding: 8px;">
								<?php esc_html_e( 'Database Configuration', 'swgtheme' ); ?>
							</td>
						</tr>
						<tr>
							<td><strong>MySQL Version</strong></td>
							<td><?php echo esc_html( $wpdb->db_version() ); ?></td>
						</tr>
						<tr>
							<td><strong>Database Name</strong></td>
							<td><?php echo esc_html( DB_NAME ); ?></td>
						</tr>
						<tr>
							<td><strong>Database Host</strong></td>
							<td><?php echo esc_html( DB_HOST ); ?></td>
						</tr>
						<tr>
							<td><strong>Database User</strong></td>
							<td><?php echo esc_html( DB_USER ); ?></td>
						</tr>
						<tr>
							<td><strong>Database Charset</strong></td>
							<td><?php echo esc_html( DB_CHARSET ); ?></td>
						</tr>
						<tr>
							<td><strong>Database Collate</strong></td>
							<td><?php echo esc_html( DB_COLLATE ?: 'Default' ); ?></td>
						</tr>
						<tr>
							<td><strong>Table Prefix</strong></td>
							<td><?php echo esc_html( $wpdb->prefix ); ?></td>
						</tr>
						
						<?php
						// Get MySQL variables
						$mysql_vars = $wpdb->get_results( "SHOW VARIABLES WHERE Variable_name IN (
							'max_connections',
							'max_allowed_packet',
							'innodb_buffer_pool_size',
							'query_cache_size',
							'tmp_table_size',
							'max_heap_table_size',
							'key_buffer_size',
							'thread_cache_size',
							'table_open_cache',
							'sort_buffer_size',
							'read_buffer_size',
							'join_buffer_size',
							'innodb_log_file_size',
							'innodb_log_buffer_size',
							'character_set_server',
							'collation_server',
							'sql_mode',
							'max_execution_time',
							'wait_timeout',
							'interactive_timeout'
						)", ARRAY_A );
						
						if ( ! empty( $mysql_vars ) ) {
							?>
							<tr>
								<td colspan="2" style="background: #0073aa; color: white; font-weight: bold; padding: 8px;">
									<?php esc_html_e( 'MySQL Server Variables', 'swgtheme' ); ?>
								</td>
							</tr>
							<?php
							foreach ( $mysql_vars as $var ) {
								$value = $var['Value'];
								// Format large numbers
								if ( is_numeric( $value ) && $value > 1024 ) {
									$value .= ' (' . size_format( $value, 2 ) . ')';
								}
								?>
								<tr>
									<td><strong><?php echo esc_html( $var['Variable_name'] ); ?></strong></td>
									<td style="word-break: break-all;"><?php echo esc_html( $value ); ?></td>
								</tr>
								<?php
							}
						}
						
						// Get database status
						$status_vars = $wpdb->get_results( "SHOW STATUS WHERE Variable_name IN (
							'Uptime',
							'Threads_connected',
							'Questions',
							'Slow_queries',
							'Opens',
							'Flush_commands',
							'Open_tables',
							'Queries',
							'Connections',
							'Bytes_received',
							'Bytes_sent',
							'Aborted_connects',
							'Table_locks_waited',
							'Created_tmp_disk_tables',
							'Created_tmp_tables',
							'Max_used_connections'
						)", ARRAY_A );
						
						if ( ! empty( $status_vars ) ) {
							?>
							<tr>
								<td colspan="2" style="background: #0073aa; color: white; font-weight: bold; padding: 8px;">
									<?php esc_html_e( 'MySQL Server Status', 'swgtheme' ); ?>
								</td>
							</tr>
							<?php
							foreach ( $status_vars as $var ) {
								$value = $var['Value'];
								// Format specific values
								if ( $var['Variable_name'] === 'Uptime' && is_numeric( $value ) ) {
									$days = floor( $value / 86400 );
									$hours = floor( ( $value % 86400 ) / 3600 );
									$minutes = floor( ( $value % 3600 ) / 60 );
									$value .= sprintf( ' (%dd %dh %dm)', $days, $hours, $minutes );
								} elseif ( in_array( $var['Variable_name'], array( 'Bytes_received', 'Bytes_sent' ) ) && is_numeric( $value ) ) {
									$value .= ' (' . size_format( $value, 2 ) . ')';
								}
								?>
								<tr>
									<td><strong><?php echo esc_html( $var['Variable_name'] ); ?></strong></td>
									<td><?php echo esc_html( $value ); ?></td>
								</tr>
								<?php
							}
						}
						
						// Get table information
						$tables = $wpdb->get_results( "
							SELECT 
								TABLE_NAME,
								ENGINE,
								TABLE_ROWS,
								DATA_LENGTH,
								INDEX_LENGTH,
								AUTO_INCREMENT
							FROM information_schema.TABLES 
							WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
							AND TABLE_NAME LIKE '" . $wpdb->prefix . "%'
							ORDER BY DATA_LENGTH DESC
							LIMIT 10
						", ARRAY_A );
						
						if ( ! empty( $tables ) ) {
							?>
							<tr>
								<td colspan="2" style="background: #0073aa; color: white; font-weight: bold; padding: 8px;">
									<?php esc_html_e( 'Top 10 Largest Tables', 'swgtheme' ); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<table style="width: 100%; font-size: 11px; border-collapse: collapse;">
										<tr style="background: #f0f0f0;">
											<th style="text-align: left; padding: 5px; border: 1px solid #ddd;">Table</th>
											<th style="text-align: left; padding: 5px; border: 1px solid #ddd;">Engine</th>
											<th style="text-align: right; padding: 5px; border: 1px solid #ddd;">Rows</th>
											<th style="text-align: right; padding: 5px; border: 1px solid #ddd;">Data Size</th>
											<th style="text-align: right; padding: 5px; border: 1px solid #ddd;">Index Size</th>
										</tr>
										<?php foreach ( $tables as $table ) : ?>
											<tr>
												<td style="padding: 5px; border: 1px solid #ddd;"><?php echo esc_html( $table['TABLE_NAME'] ); ?></td>
												<td style="padding: 5px; border: 1px solid #ddd;"><?php echo esc_html( $table['ENGINE'] ); ?></td>
												<td style="padding: 5px; border: 1px solid #ddd; text-align: right;"><?php echo esc_html( number_format( $table['TABLE_ROWS'] ) ); ?></td>
												<td style="padding: 5px; border: 1px solid #ddd; text-align: right;"><?php echo esc_html( size_format( $table['DATA_LENGTH'], 2 ) ); ?></td>
												<td style="padding: 5px; border: 1px solid #ddd; text-align: right;"><?php echo esc_html( size_format( $table['INDEX_LENGTH'], 2 ) ); ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		</div>
		
		<!-- Tab Switching Script -->
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			function switchTab(tabId) {
				// Remove active class from all tabs and content
				$('.nav-tab').removeClass('nav-tab-active');
				$('.tab-content').removeClass('active');
				
				// Add active class to clicked tab and corresponding content
				$('a[href="' + tabId + '"]').addClass('nav-tab-active');
				$(tabId).addClass('active');
				
				// Update URL hash without scrolling
				if (history.pushState) {
					history.pushState(null, null, tabId);
				} else {
					location.hash = tabId;
				}
			}
			
			// Handle tab clicks
			$('.nav-tab').on('click', function(e) {
				e.preventDefault();
				var tabId = $(this).attr('href');
				switchTab(tabId);
			});
			
			// Handle initial hash on page load
			if (window.location.hash) {
				var hash = window.location.hash;
				if ($('.tab-content' + hash).length) {
					switchTab(hash);
				}
			}
			
			// Handle browser back/forward buttons
			$(window).on('hashchange', function() {
				if (window.location.hash) {
					var hash = window.location.hash;
					if ($('.tab-content' + hash).length) {
						switchTab(hash);
					}
				}
			});
		});
		</script>
		
		<style>
		.tab-content {
			display: none !important;
		}
		.tab-content.active {
			display: block !important;
		}
		</style>
		
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2><?php esc_html_e( 'Quick Links', 'swgtheme' ); ?></h2>
			<p>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=swgtheme-options' ) ); ?>" class="button">
					<?php esc_html_e( 'Theme Options', 'swgtheme' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=swgtheme-performance' ) ); ?>" class="button">
					<?php esc_html_e( 'Performance Settings', 'swgtheme' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=swgtheme-security' ) ); ?>" class="button">
					<?php esc_html_e( 'Security Settings', 'swgtheme' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php
}

// Register debug log page
if ( function_exists( 'is_dev_mode' ) && is_dev_mode() ) {
	add_action( 'admin_menu', 'swgtheme_debug_log_page' );
}
