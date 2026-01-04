<?php
/**
 * Third-Party Integrations
 * Mailchimp, Discord, Social Media Feeds, and Plugin Compatibility
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integration Manager Class
 */
class SWGTheme_Integrations {
	
	/**
	 * Initialize integrations
	 */
	public static function init() {
		// Newsletter integration
		add_action( 'wp_ajax_swg_mailchimp_subscribe', array( __CLASS__, 'handle_mailchimp_subscription' ) );
		add_action( 'wp_ajax_nopriv_swg_mailchimp_subscribe', array( __CLASS__, 'handle_mailchimp_subscription' ) );
		
		// Discord webhooks
		add_action( 'publish_post', array( __CLASS__, 'discord_notify_new_post' ), 10, 2 );
		add_action( 'comment_post', array( __CLASS__, 'discord_notify_new_comment' ), 10, 2 );
		
		// Contact Form 7 styling
		add_action( 'wpcf7_enqueue_styles', array( __CLASS__, 'dequeue_cf7_styles' ) );
		add_filter( 'wpcf7_form_class_attr', array( __CLASS__, 'add_cf7_classes' ) );
		
		// Gravity Forms compatibility
		add_filter( 'gform_submit_button', array( __CLASS__, 'gravity_forms_button' ), 10, 2 );
		
		// bbPress forum integration
		if ( class_exists( 'bbPress' ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'bbpress_styles' ) );
		}
	}
	
	/* ==============================================
	   MAILCHIMP NEWSLETTER INTEGRATION
	   ============================================== */
	
	/**
	 * Handle Mailchimp subscription via AJAX
	 */
	public static function handle_mailchimp_subscription() {
		check_ajax_referer( 'swg_mailchimp_nonce', 'nonce' );
		
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please enter a valid email address.', 'swgtheme' ),
			) );
		}
		
		$api_key = get_option( 'swgtheme_mailchimp_api_key', '' );
		$list_id = get_option( 'swgtheme_mailchimp_list_id', '' );
		
		if ( empty( $api_key ) || empty( $list_id ) ) {
			wp_send_json_error( array(
				'message' => __( 'Newsletter service is not configured.', 'swgtheme' ),
			) );
		}
		
		$result = self::subscribe_to_mailchimp( $email, $name, $api_key, $list_id );
		
		if ( $result['success'] ) {
			wp_send_json_success( array(
				'message' => __( 'Successfully subscribed! Please check your email to confirm.', 'swgtheme' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}
	}
	
	/**
	 * Subscribe email to Mailchimp list
	 */
	private static function subscribe_to_mailchimp( $email, $name, $api_key, $list_id ) {
		// Extract datacenter from API key
		$datacenter = substr( $api_key, strpos( $api_key, '-' ) + 1 );
		$url = "https://{$datacenter}.api.mailchimp.com/3.0/lists/{$list_id}/members/";
		
		$member_data = array(
			'email_address' => $email,
			'status' => 'pending', // Double opt-in
		);
		
		if ( ! empty( $name ) ) {
			$name_parts = explode( ' ', $name, 2 );
			$member_data['merge_fields'] = array(
				'FNAME' => $name_parts[0],
				'LNAME' => isset( $name_parts[1] ) ? $name_parts[1] : '',
			);
		}
		
		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $member_data ),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => __( 'Connection error. Please try again later.', 'swgtheme' ),
			);
		}
		
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$status_code = wp_remote_retrieve_response_code( $response );
		
		if ( $status_code === 200 ) {
			return array(
				'success' => true,
				'message' => __( 'Successfully subscribed!', 'swgtheme' ),
			);
		} elseif ( isset( $body['title'] ) && $body['title'] === 'Member Exists' ) {
			return array(
				'success' => false,
				'message' => __( 'This email is already subscribed.', 'swgtheme' ),
			);
		} else {
			return array(
				'success' => false,
				'message' => isset( $body['detail'] ) ? $body['detail'] : __( 'Subscription failed. Please try again.', 'swgtheme' ),
			);
		}
	}
	
	/* ==============================================
	   DISCORD WEBHOOK NOTIFICATIONS
	   ============================================== */
	
	/**
	 * Send Discord notification for new post
	 */
	public static function discord_notify_new_post( $post_id, $post ) {
		if ( get_option( 'swgtheme_discord_notify_posts', '0' ) !== '1' ) {
			return;
		}
		
		$webhook_url = get_option( 'swgtheme_discord_webhook_url', '' );
		
		if ( empty( $webhook_url ) ) {
			return;
		}
		
		$author = get_userdata( $post->post_author );
		$excerpt = wp_trim_words( $post->post_content, 30 );
		$thumbnail = get_the_post_thumbnail_url( $post_id, 'medium' );
		
		$embed = array(
			'username' => get_bloginfo( 'name' ),
			'embeds' => array(
				array(
					'title' => $post->post_title,
					'description' => $excerpt,
					'url' => get_permalink( $post_id ),
					'color' => hexdec( 'dc3545' ),
					'author' => array(
						'name' => $author->display_name,
					),
					'thumbnail' => $thumbnail ? array( 'url' => $thumbnail ) : null,
					'footer' => array(
						'text' => get_bloginfo( 'name' ),
					),
					'timestamp' => gmdate( 'c', strtotime( $post->post_date ) ),
				),
			),
		);
		
		self::send_discord_webhook( $webhook_url, $embed );
	}
	
	/**
	 * Send Discord notification for new comment
	 */
	public static function discord_notify_new_comment( $comment_id, $comment_approved ) {
		if ( $comment_approved !== 1 ) {
			return;
		}
		
		if ( get_option( 'swgtheme_discord_notify_comments', '0' ) !== '1' ) {
			return;
		}
		
		$webhook_url = get_option( 'swgtheme_discord_webhook_url', '' );
		
		if ( empty( $webhook_url ) ) {
			return;
		}
		
		$comment = get_comment( $comment_id );
		$post = get_post( $comment->comment_post_ID );
		
		$embed = array(
			'username' => get_bloginfo( 'name' ),
			'embeds' => array(
				array(
					'title' => '💬 New Comment',
					'description' => wp_trim_words( $comment->comment_content, 50 ),
					'url' => get_comment_link( $comment_id ),
					'color' => hexdec( '28a745' ),
					'fields' => array(
						array(
							'name' => 'Author',
							'value' => $comment->comment_author,
							'inline' => true,
						),
						array(
							'name' => 'Post',
							'value' => $post->post_title,
							'inline' => true,
						),
					),
					'timestamp' => gmdate( 'c', strtotime( $comment->comment_date ) ),
				),
			),
		);
		
		self::send_discord_webhook( $webhook_url, $embed );
	}
	
	/**
	 * Send webhook to Discord
	 */
	private static function send_discord_webhook( $url, $data ) {
		wp_remote_post( $url, array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $data ),
			'timeout' => 15,
		) );
	}
	
	/* ==============================================
	   SOCIAL MEDIA FEEDS
	   ============================================== */
	
	/**
	 * Get Twitch stream status
	 */
	public static function get_twitch_stream_status( $channel ) {
		$client_id = get_option( 'swgtheme_twitch_client_id', '' );
		$access_token = get_option( 'swgtheme_twitch_access_token', '' );
		
		if ( empty( $client_id ) || empty( $access_token ) ) {
			return false;
		}
		
		$transient_key = 'swg_twitch_stream_' . sanitize_key( $channel );
		$cached = get_transient( $transient_key );
		
		if ( $cached !== false ) {
			return $cached;
		}
		
		$url = 'https://api.twitch.tv/helix/streams?user_login=' . urlencode( $channel );
		
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Client-ID' => $client_id,
				'Authorization' => 'Bearer ' . $access_token,
			),
			'timeout' => 15,
		) );
		
		if ( is_wp_error( $response ) ) {
			return false;
		}
		
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$stream_data = isset( $body['data'][0] ) ? $body['data'][0] : null;
		
		set_transient( $transient_key, $stream_data, 5 * MINUTE_IN_SECONDS );
		
		return $stream_data;
	}
	
	/**
	 * Get YouTube channel videos
	 */
	public static function get_youtube_videos( $channel_id, $max_results = 5 ) {
		$api_key = get_option( 'swgtheme_youtube_api_key', '' );
		
		if ( empty( $api_key ) ) {
			return false;
		}
		
		$transient_key = 'swg_youtube_videos_' . sanitize_key( $channel_id );
		$cached = get_transient( $transient_key );
		
		if ( $cached !== false ) {
			return $cached;
		}
		
		$url = add_query_arg(
			array(
				'part' => 'snippet',
				'channelId' => $channel_id,
				'maxResults' => $max_results,
				'order' => 'date',
				'type' => 'video',
				'key' => $api_key,
			),
			'https://www.googleapis.com/youtube/v3/search'
		);
		
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		
		if ( is_wp_error( $response ) ) {
			return false;
		}
		
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$videos = isset( $body['items'] ) ? $body['items'] : array();
		
		set_transient( $transient_key, $videos, HOUR_IN_SECONDS );
		
		return $videos;
	}
	
	/* ==============================================
	   CONTACT FORM 7 INTEGRATION
	   ============================================== */
	
	/**
	 * Dequeue default CF7 styles (use theme styles instead)
	 */
	public static function dequeue_cf7_styles() {
		if ( get_option( 'swgtheme_cf7_custom_styles', '1' ) === '1' ) {
			wp_dequeue_style( 'contact-form-7' );
		}
	}
	
	/**
	 * Add custom classes to CF7 forms
	 */
	public static function add_cf7_classes( $class ) {
		$class .= ' swg-contact-form';
		return $class;
	}
	
	/* ==============================================
	   GRAVITY FORMS INTEGRATION
	   ============================================== */
	
	/**
	 * Customize Gravity Forms submit button
	 */
	public static function gravity_forms_button( $button, $form ) {
		$button = str_replace( 'gform_button', 'gform_button btn btn-primary', $button );
		return $button;
	}
	
	/* ==============================================
	   BBPRESS FORUM INTEGRATION
	   ============================================== */
	
	/**
	 * Enqueue bbPress custom styles
	 */
	public static function bbpress_styles() {
		if ( get_option( 'swgtheme_bbpress_custom_styles', '1' ) === '1' ) {
			wp_enqueue_style( 'swg-bbpress', get_template_directory_uri() . '/css/bbpress.css', array(), SWGTHEME_VERSION );
		}
	}
}

// Initialize integrations
SWGTheme_Integrations::init();
