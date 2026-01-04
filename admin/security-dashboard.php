<?php
/**
 * Security Admin Dashboard Page
 * View security logs, blocked IPs, and security settings
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle IP unblock action
if ( isset( $_POST['unblock_ip'] ) && isset( $_POST['security_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['security_nonce'], 'swg_security_action' ) && current_user_can( 'manage_options' ) ) {
		$ip = sanitize_text_field( wp_unslash( $_POST['unblock_ip'] ) );
		SWGTheme_Security::unblock_ip( $ip );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'IP address has been unblocked.', 'swgtheme' ) . '</p></div>';
	}
}

// Handle settings save
if ( isset( $_POST['save_security_settings'] ) && isset( $_POST['security_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['security_nonce'], 'swg_security_action' ) && current_user_can( 'manage_options' ) ) {
		update_option( 'swgtheme_security_notify_lockouts', isset( $_POST['notify_lockouts'] ) ? '1' : '0' );
		update_option( 'swgtheme_security_disable_xmlrpc', isset( $_POST['disable_xmlrpc'] ) ? '1' : '0' );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Security settings saved.', 'swgtheme' ) . '</p></div>';
	}
}

// Get filter parameters
$event_filter = isset( $_GET['event_type'] ) ? sanitize_text_field( wp_unslash( $_GET['event_type'] ) ) : '';
$logs_per_page = 50;
$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;

// Get security logs
$log_args = array(
	'limit' => $logs_per_page,
	'offset' => ( $paged - 1 ) * $logs_per_page,
);

if ( $event_filter ) {
	$log_args['event_type'] = $event_filter;
}

$logs = SWGTheme_Security::get_security_logs( $log_args );
$blocked_ips = SWGTheme_Security::get_blocked_ips();

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Security Dashboard', 'swgtheme' ); ?></h1>
	
	<!-- Tabs -->
	<h2 class="nav-tab-wrapper">
		<a href="#logs" class="nav-tab nav-tab-active"><?php esc_html_e( 'Security Logs', 'swgtheme' ); ?></a>
		<a href="#blocked" class="nav-tab"><?php esc_html_e( 'Blocked IPs', 'swgtheme' ); ?></a>
		<a href="#settings" class="nav-tab"><?php esc_html_e( 'Settings', 'swgtheme' ); ?></a>
	</h2>
	
	<!-- Tab: Security Logs -->
	<div class="tab-content active" id="logs-tab">
		<h2><?php esc_html_e( 'Recent Security Events', 'swgtheme' ); ?></h2>
		
		<!-- Filters -->
		<div class="tablenav top">
			<div class="alignleft actions">
				<form method="get">
					<input type="hidden" name="page" value="swgtheme-security" />
					<select name="event_type" id="event-filter">
						<option value=""><?php esc_html_e( 'All Events', 'swgtheme' ); ?></option>
						<option value="login_failed" <?php selected( $event_filter, 'login_failed' ); ?>><?php esc_html_e( 'Failed Logins', 'swgtheme' ); ?></option>
						<option value="login_lockout" <?php selected( $event_filter, 'login_lockout' ); ?>><?php esc_html_e( 'Lockouts', 'swgtheme' ); ?></option>
						<option value="login_blocked" <?php selected( $event_filter, 'login_blocked' ); ?>><?php esc_html_e( 'Blocked Attempts', 'swgtheme' ); ?></option>
						<option value="file_upload_blocked" <?php selected( $event_filter, 'file_upload_blocked' ); ?>><?php esc_html_e( 'Blocked Uploads', 'swgtheme' ); ?></option>
					</select>
					<input type="submit" class="button" value="<?php esc_attr_e( 'Filter', 'swgtheme' ); ?>" />
				</form>
			</div>
		</div>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="width: 150px;"><?php esc_html_e( 'Date/Time', 'swgtheme' ); ?></th>
					<th style="width: 120px;"><?php esc_html_e( 'Event Type', 'swgtheme' ); ?></th>
					<th style="width: 120px;"><?php esc_html_e( 'IP Address', 'swgtheme' ); ?></th>
					<th><?php esc_html_e( 'Details', 'swgtheme' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $logs ) ) : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No security events found.', 'swgtheme' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $logs as $log ) : ?>
						<?php
						$event_data = json_decode( $log->event_data, true );
						$event_class = '';
						
						switch ( $log->event_type ) {
							case 'login_failed':
								$event_class = 'warning';
								$event_label = __( 'Failed Login', 'swgtheme' );
								break;
							case 'login_lockout':
								$event_class = 'error';
								$event_label = __( 'Account Lockout', 'swgtheme' );
								break;
							case 'login_blocked':
								$event_class = 'error';
								$event_label = __( 'Login Blocked', 'swgtheme' );
								break;
							case 'login_success':
								$event_class = 'success';
								$event_label = __( 'Successful Login', 'swgtheme' );
								break;
							case 'file_upload_blocked':
								$event_class = 'error';
								$event_label = __( 'Upload Blocked', 'swgtheme' );
								break;
							default:
								$event_label = ucwords( str_replace( '_', ' ', $log->event_type ) );
						}
						?>
						<tr class="security-event-<?php echo esc_attr( $event_class ); ?>">
							<td><?php echo esc_html( $log->created_at ); ?></td>
							<td>
								<span class="security-badge security-badge-<?php echo esc_attr( $event_class ); ?>">
									<?php echo esc_html( $event_label ); ?>
								</span>
							</td>
							<td><code><?php echo esc_html( $log->ip_address ); ?></code></td>
							<td>
								<?php if ( isset( $event_data['username'] ) ) : ?>
									<strong><?php esc_html_e( 'Username:', 'swgtheme' ); ?></strong> <?php echo esc_html( $event_data['username'] ); ?><br/>
								<?php endif; ?>
								<?php if ( isset( $event_data['attempts'] ) ) : ?>
									<strong><?php esc_html_e( 'Attempts:', 'swgtheme' ); ?></strong> <?php echo esc_html( $event_data['attempts'] ); ?><br/>
								<?php endif; ?>
								<?php if ( isset( $event_data['reason'] ) ) : ?>
									<strong><?php esc_html_e( 'Reason:', 'swgtheme' ); ?></strong> <?php echo esc_html( $event_data['reason'] ); ?><br/>
								<?php endif; ?>
								<?php if ( isset( $event_data['file_name'] ) ) : ?>
									<strong><?php esc_html_e( 'File:', 'swgtheme' ); ?></strong> <?php echo esc_html( $event_data['file_name'] ); ?><br/>
								<?php endif; ?>
								<?php if ( ! empty( $log->user_agent ) ) : ?>
									<small><?php echo esc_html( substr( $log->user_agent, 0, 100 ) ); ?></small>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		
		<style>
		.security-badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 3px;
			font-size: 11px;
			font-weight: 600;
			text-transform: uppercase;
		}
		.security-badge-success { background: #d4edda; color: #155724; }
		.security-badge-warning { background: #fff3cd; color: #856404; }
		.security-badge-error { background: #f8d7da; color: #721c24; }
		.security-event-error td { border-left: 3px solid #dc3545; }
		.security-event-warning td { border-left: 3px solid #ffc107; }
		</style>
	</div>
	
	<!-- Tab: Blocked IPs -->
	<div class="tab-content" id="blocked-tab" style="display: none;">
		<h2><?php esc_html_e( 'Currently Blocked IP Addresses', 'swgtheme' ); ?></h2>
		
		<?php if ( empty( $blocked_ips ) ) : ?>
			<p><?php esc_html_e( 'No IP addresses are currently blocked.', 'swgtheme' ); ?></p>
		<?php else : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'IP Address', 'swgtheme' ); ?></th>
						<th><?php esc_html_e( 'Time Remaining', 'swgtheme' ); ?></th>
						<th><?php esc_html_e( 'Unlock Time', 'swgtheme' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'swgtheme' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $blocked_ips as $blocked ) : ?>
						<tr>
							<td><code><?php echo esc_html( $blocked['ip'] ); ?></code></td>
							<td><?php echo esc_html( human_time_diff( time(), time() + $blocked['remaining'] ) ); ?></td>
							<td><?php echo esc_html( $blocked['unlock_time'] ); ?></td>
							<td>
								<form method="post" style="display: inline;">
									<?php wp_nonce_field( 'swg_security_action', 'security_nonce' ); ?>
									<input type="hidden" name="unblock_ip" value="<?php echo esc_attr( $blocked['ip'] ); ?>" />
									<button type="submit" class="button button-small" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to unblock this IP?', 'swgtheme' ); ?>')">
										<?php esc_html_e( 'Unblock', 'swgtheme' ); ?>
									</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	
	<!-- Tab: Settings -->
	<div class="tab-content" id="settings-tab" style="display: none;">
		<h2><?php esc_html_e( 'Security Settings', 'swgtheme' ); ?></h2>
		
		<form method="post">
			<?php wp_nonce_field( 'swg_security_action', 'security_nonce' ); ?>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Login Lockout Settings', 'swgtheme' ); ?></th>
					<td>
						<p><strong><?php esc_html_e( 'Current Configuration:', 'swgtheme' ); ?></strong></p>
						<ul style="margin-left: 20px; list-style: disc;">
							<li><?php printf( esc_html__( 'Maximum login attempts: %d', 'swgtheme' ), SWGTheme_Security::MAX_LOGIN_ATTEMPTS ); ?></li>
							<li><?php printf( esc_html__( 'Lockout duration: %d minutes', 'swgtheme' ), SWGTheme_Security::LOCKOUT_DURATION / 60 ); ?></li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Email Notifications', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="notify_lockouts" value="1" <?php checked( get_option( 'swgtheme_security_notify_lockouts', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Send email notifications when accounts are locked out', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'XML-RPC', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="disable_xmlrpc" value="1" <?php checked( get_option( 'swgtheme_security_disable_xmlrpc', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Disable XML-RPC (recommended for security)', 'swgtheme' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'XML-RPC can be exploited for brute force attacks. Disable unless you specifically need it.', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'File Upload Security', 'swgtheme' ); ?></th>
					<td>
						<p><?php esc_html_e( 'Maximum upload size: 10 MB', 'swgtheme' ); ?></p>
						<p><?php esc_html_e( 'Dangerous file types (exe, php, sh, bat, etc.) are automatically blocked.', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Additional Security', 'swgtheme' ); ?></th>
					<td>
						<ul style="margin-left: 20px; list-style: disc;">
							<li><?php esc_html_e( '✓ WordPress version hidden', 'swgtheme' ); ?></li>
							<li><?php esc_html_e( '✓ File editing disabled in admin', 'swgtheme' ); ?></li>
							<li><?php esc_html_e( '✓ Additional security headers enabled', 'swgtheme' ); ?></li>
							<li><?php esc_html_e( '✓ MIME type sniffing prevented', 'swgtheme' ); ?></li>
						</ul>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="save_security_settings" value="1" />
			<?php submit_button( __( 'Save Security Settings', 'swgtheme' ) ); ?>
		</form>
	</div>
	
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Tab switching
		$('.nav-tab-wrapper .nav-tab').on('click', function(e) {
			e.preventDefault();
			
			$('.nav-tab').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			
			$('.tab-content').hide();
			var target = $(this).attr('href').replace('#', '') + '-tab';
			$('#' + target).show();
		});
	});
	</script>
</div>
