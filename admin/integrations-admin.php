<?php
/**
 * Integrations Admin Page
 * Manage third-party service integrations
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle settings save
if ( isset( $_POST['save_integrations'] ) && isset( $_POST['integrations_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['integrations_nonce'], 'swg_integrations_action' ) && current_user_can( 'manage_options' ) ) {
		// Mailchimp
		update_option( 'swgtheme_mailchimp_api_key', sanitize_text_field( wp_unslash( $_POST['mailchimp_api_key'] ?? '' ) ) );
		update_option( 'swgtheme_mailchimp_list_id', sanitize_text_field( wp_unslash( $_POST['mailchimp_list_id'] ?? '' ) ) );
		
		// Discord
		update_option( 'swgtheme_discord_webhook_url', esc_url_raw( wp_unslash( $_POST['discord_webhook_url'] ?? '' ) ) );
		update_option( 'swgtheme_discord_notify_posts', isset( $_POST['discord_notify_posts'] ) ? '1' : '0' );
		update_option( 'swgtheme_discord_notify_comments', isset( $_POST['discord_notify_comments'] ) ? '1' : '0' );
		
		// Twitch
		update_option( 'swgtheme_twitch_client_id', sanitize_text_field( wp_unslash( $_POST['twitch_client_id'] ?? '' ) ) );
		update_option( 'swgtheme_twitch_access_token', sanitize_text_field( wp_unslash( $_POST['twitch_access_token'] ?? '' ) ) );
		
		// YouTube
		update_option( 'swgtheme_youtube_api_key', sanitize_text_field( wp_unslash( $_POST['youtube_api_key'] ?? '' ) ) );
		
		// Plugin compatibility
		update_option( 'swgtheme_cf7_custom_styles', isset( $_POST['cf7_custom_styles'] ) ? '1' : '0' );
		update_option( 'swgtheme_bbpress_custom_styles', isset( $_POST['bbpress_custom_styles'] ) ? '1' : '0' );
		
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Integration settings saved.', 'swgtheme' ) . '</p></div>';
	}
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Third-Party Integrations', 'swgtheme' ); ?></h1>
	
	<!-- Tabs -->
	<h2 class="nav-tab-wrapper">
		<a href="#newsletter" class="nav-tab nav-tab-active"><?php esc_html_e( 'Newsletter', 'swgtheme' ); ?></a>
		<a href="#discord" class="nav-tab"><?php esc_html_e( 'Discord', 'swgtheme' ); ?></a>
		<a href="#social" class="nav-tab"><?php esc_html_e( 'Social Media', 'swgtheme' ); ?></a>
		<a href="#plugins" class="nav-tab"><?php esc_html_e( 'Plugin Compatibility', 'swgtheme' ); ?></a>
	</h2>
	
	<form method="post">
		<?php wp_nonce_field( 'swg_integrations_action', 'integrations_nonce' ); ?>
		
		<!-- Tab: Newsletter -->
		<div class="tab-content active" id="newsletter-tab">
			<h2><?php esc_html_e( 'Mailchimp Integration', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'Connect your Mailchimp account to enable newsletter signups throughout the site.', 'swgtheme' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="mailchimp_api_key"><?php esc_html_e( 'Mailchimp API Key', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="text" 
							id="mailchimp_api_key" 
							name="mailchimp_api_key" 
							value="<?php echo esc_attr( get_option( 'swgtheme_mailchimp_api_key', '' ) ); ?>" 
							class="regular-text code" 
							placeholder="abc123...xyz-us1" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: URL to Mailchimp API keys page */
								esc_html__( 'Get your API key from %s', 'swgtheme' ),
								'<a href="https://admin.mailchimp.com/account/api/" target="_blank">Mailchimp Account</a>'
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="mailchimp_list_id"><?php esc_html_e( 'Audience/List ID', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="text" 
							id="mailchimp_list_id" 
							name="mailchimp_list_id" 
							value="<?php echo esc_attr( get_option( 'swgtheme_mailchimp_list_id', '' ) ); ?>" 
							class="regular-text code" 
							placeholder="a1b2c3d4e5" />
						<p class="description">
							<?php esc_html_e( 'Find your list ID in Mailchimp under Audience → Settings → Audience name and defaults', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</table>
			
			<h3><?php esc_html_e( 'Usage Example', 'swgtheme' ); ?></h3>
			<p><?php esc_html_e( 'Add the newsletter widget to your sidebar or use this shortcode:', 'swgtheme' ); ?></p>
			<code>[swg_newsletter title="Subscribe" description="Stay updated with our latest news"]</code>
		</div>
		
		<!-- Tab: Discord -->
		<div class="tab-content" id="discord-tab" style="display: none;">
			<h2><?php esc_html_e( 'Discord Webhook Integration', 'swgtheme' ); ?></h2>
			<p><?php esc_html_e( 'Send automatic notifications to your Discord server when new content is published.', 'swgtheme' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="discord_webhook_url"><?php esc_html_e( 'Webhook URL', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="url" 
							id="discord_webhook_url" 
							name="discord_webhook_url" 
							value="<?php echo esc_url( get_option( 'swgtheme_discord_webhook_url', '' ) ); ?>" 
							class="large-text code" 
							placeholder="https://discord.com/api/webhooks/..." />
						<p class="description">
							<?php esc_html_e( 'Create a webhook in Discord: Server Settings → Integrations → Webhooks', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Notification Settings', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="discord_notify_posts" 
								value="1" 
								<?php checked( get_option( 'swgtheme_discord_notify_posts', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Notify when new posts are published', 'swgtheme' ); ?>
						</label>
						<br/>
						<label>
							<input type="checkbox" 
								name="discord_notify_comments" 
								value="1" 
								<?php checked( get_option( 'swgtheme_discord_notify_comments', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Notify when new comments are posted', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Tab: Social Media -->
		<div class="tab-content" id="social-tab" style="display: none;">
			<h2><?php esc_html_e( 'Social Media Feed Integration', 'swgtheme' ); ?></h2>
			
			<h3><?php esc_html_e( 'Twitch', 'swgtheme' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="twitch_client_id"><?php esc_html_e( 'Client ID', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="text" 
							id="twitch_client_id" 
							name="twitch_client_id" 
							value="<?php echo esc_attr( get_option( 'swgtheme_twitch_client_id', '' ) ); ?>" 
							class="regular-text code" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: URL to Twitch Developer Console */
								esc_html__( 'Create an app at %s', 'swgtheme' ),
								'<a href="https://dev.twitch.tv/console" target="_blank">Twitch Developer Console</a>'
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="twitch_access_token"><?php esc_html_e( 'Access Token', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="text" 
							id="twitch_access_token" 
							name="twitch_access_token" 
							value="<?php echo esc_attr( get_option( 'swgtheme_twitch_access_token', '' ) ); ?>" 
							class="regular-text code" />
					</td>
				</tr>
			</table>
			
			<h3><?php esc_html_e( 'YouTube', 'swgtheme' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="youtube_api_key"><?php esc_html_e( 'API Key', 'swgtheme' ); ?></label>
					</th>
					<td>
						<input type="text" 
							id="youtube_api_key" 
							name="youtube_api_key" 
							value="<?php echo esc_attr( get_option( 'swgtheme_youtube_api_key', '' ) ); ?>" 
							class="regular-text code" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: URL to Google Cloud Console */
								esc_html__( 'Get an API key from %s (enable YouTube Data API v3)', 'swgtheme' ),
								'<a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>'
							);
							?>
						</p>
					</td>
				</tr>
			</table>
			
			<h3><?php esc_html_e( 'Widget Usage', 'swgtheme' ); ?></h3>
			<p><?php esc_html_e( 'After configuring the APIs above, you can use these widgets:', 'swgtheme' ); ?></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><strong>Twitch Stream Status</strong> - Shows if your channel is live</li>
				<li><strong>YouTube Videos</strong> - Displays latest videos from your channel</li>
			</ul>
		</div>
		
		<!-- Tab: Plugin Compatibility -->
		<div class="tab-content" id="plugins-tab" style="display: none;">
			<h2><?php esc_html_e( 'Plugin Compatibility Settings', 'swgtheme' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Contact Form 7', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="cf7_custom_styles" 
								value="1" 
								<?php checked( get_option( 'swgtheme_cf7_custom_styles', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Use custom theme styles for Contact Form 7', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Replaces default CF7 styles with theme-matching styles.', 'swgtheme' ); ?>
							<?php if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) : ?>
								<br/><span style="color: #856404;">⚠️ <?php esc_html_e( 'Contact Form 7 is not installed.', 'swgtheme' ); ?></span>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Gravity Forms', 'swgtheme' ); ?></th>
					<td>
						<p>
							<?php esc_html_e( 'Gravity Forms integration is automatic. Forms will use theme button styles.', 'swgtheme' ); ?>
							<?php if ( ! class_exists( 'GFForms' ) ) : ?>
								<br/><span style="color: #856404;">⚠️ <?php esc_html_e( 'Gravity Forms is not installed.', 'swgtheme' ); ?></span>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'bbPress Forums', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
								name="bbpress_custom_styles" 
								value="1" 
								<?php checked( get_option( 'swgtheme_bbpress_custom_styles', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Use custom theme styles for bbPress forums', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Applies theme-matching styles to forum layouts.', 'swgtheme' ); ?>
							<?php if ( ! class_exists( 'bbPress' ) ) : ?>
								<br/><span style="color: #856404;">⚠️ <?php esc_html_e( 'bbPress is not installed.', 'swgtheme' ); ?></span>
							<?php endif; ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		
		<input type="hidden" name="save_integrations" value="1" />
		<?php submit_button( __( 'Save Integration Settings', 'swgtheme' ) ); ?>
	</form>
	
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
