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
		
		// WooCommerce integration
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'after_setup_theme', array( __CLASS__, 'woocommerce_support' ) );
			add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'woocommerce_styles' ) );
		}
		
		// Yoast SEO compatibility
		if ( defined( 'WPSEO_VERSION' ) ) {
			add_filter( 'wpseo_breadcrumb_links', array( __CLASS__, 'yoast_breadcrumb_filter' ) );
		}
		
		// Elementor compatibility
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'elementor/widgets/widgets_registered', array( __CLASS__, 'elementor_widgets' ) );
		}
		
		// Jetpack compatibility
		if ( class_exists( 'Jetpack' ) ) {
			add_theme_support( 'infinite-scroll', array(
				'container' => 'main',
				'footer' => 'footer',
			) );
		}
		
		// Advanced Custom Fields (ACF) compatibility
		if ( class_exists( 'ACF' ) ) {
			add_filter( 'acf/settings/save_json', array( __CLASS__, 'acf_json_save_point' ) );
			add_filter( 'acf/settings/load_json', array( __CLASS__, 'acf_json_load_point' ) );
		}
		
		// WPML compatibility
		if ( defined( 'WPML_VERSION' ) ) {
			add_action( 'wp_head', array( __CLASS__, 'wpml_language_selector' ) );
		}
		
		// BuddyPress compatibility
		if ( class_exists( 'BuddyPress' ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'buddypress_styles' ) );
		}
		
		// The Events Calendar compatibility
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			add_filter( 'tribe_events_views_v2_bootstrap_html', array( __CLASS__, 'events_calendar_wrapper' ), 10, 2 );
		}
		
		// Easy Digital Downloads compatibility
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			add_theme_support( 'edd-templates' );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'edd_styles' ) );
		}
		
		// SWG Auth plugin compatibility
		if ( function_exists( 'swg_auth_run' ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'swg_auth_styles' ), 20 );
			add_filter( 'widget_text', array( __CLASS__, 'swg_auth_widget_wrapper' ), 9 );
			add_action( 'dynamic_sidebar_before', array( __CLASS__, 'swg_auth_sidebar_wrapper_start' ) );
			add_action( 'dynamic_sidebar_after', array( __CLASS__, 'swg_auth_sidebar_wrapper_end' ) );
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
	
	/* ==============================================
	   WOOCOMMERCE INTEGRATION
	   ============================================== */
	
	/**
	 * Declare WooCommerce support
	 */
	public static function woocommerce_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
	
	/**
	 * Customize WooCommerce styles
	 */
	public static function woocommerce_styles( $enqueue_styles ) {
		if ( get_option( 'swgtheme_woocommerce_custom_styles', '1' ) === '1' ) {
			// Dequeue default WooCommerce styles if custom styling is enabled
			unset( $enqueue_styles['woocommerce-general'] );
			wp_enqueue_style( 'swg-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', array(), SWGTHEME_VERSION );
		}
		return $enqueue_styles;
	}
	
	/* ==============================================
	   YOAST SEO INTEGRATION
	   ============================================== */
	
	/**
	 * Filter Yoast breadcrumbs
	 */
	public static function yoast_breadcrumb_filter( $links ) {
		// Customize breadcrumb links if needed
		return $links;
	}
	
	/* ==============================================
	   ELEMENTOR INTEGRATION
	   ============================================== */
	
	/**
	 * Register custom Elementor widgets
	 */
	public static function elementor_widgets() {
		// Register custom widgets if needed
		// Example: \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Custom_Widget() );
	}
	
	/* ==============================================
	   ADVANCED CUSTOM FIELDS INTEGRATION
	   ============================================== */
	
	/**
	 * Set ACF JSON save point
	 */
	public static function acf_json_save_point( $path ) {
		$path = get_template_directory() . '/acf-json';
		return $path;
	}
	
	/**
	 * Set ACF JSON load point
	 */
	public static function acf_json_load_point( $paths ) {
		unset( $paths[0] );
		$paths[] = get_template_directory() . '/acf-json';
		return $paths;
	}
	
	/* ==============================================
	   WPML INTEGRATION
	   ============================================== */
	
	/**
	 * Add WPML language selector to header
	 */
	public static function wpml_language_selector() {
		if ( function_exists( 'icl_get_languages' ) && get_option( 'swgtheme_wpml_show_selector', '1' ) === '1' ) {
			$languages = icl_get_languages( 'skip_missing=0&orderby=code' );
			if ( ! empty( $languages ) ) {
				echo '<div class="wpml-language-selector">';
				foreach ( $languages as $lang ) {
					$class = $lang['active'] ? 'active' : '';
					printf(
						'<a href="%s" class="%s">%s</a>',
						esc_url( $lang['url'] ),
						esc_attr( $class ),
						esc_html( $lang['native_name'] )
					);
				}
				echo '</div>';
			}
		}
	}
	
	/* ==============================================
	   BUDDYPRESS INTEGRATION
	   ============================================== */
	
	/**
	 * Enqueue BuddyPress custom styles
	 */
	public static function buddypress_styles() {
		if ( get_option( 'swgtheme_buddypress_custom_styles', '1' ) === '1' ) {
			wp_enqueue_style( 'swg-buddypress', get_template_directory_uri() . '/css/buddypress.css', array(), SWGTHEME_VERSION );
		}
	}
	
	/* ==============================================
	   THE EVENTS CALENDAR INTEGRATION
	   ============================================== */
	
	/**
	 * Wrap Events Calendar content
	 */
	public static function events_calendar_wrapper( $html, $view_slug ) {
		return '<div class="swg-events-wrapper">' . $html . '</div>';
	}
	
	/* ==============================================
	   EASY DIGITAL DOWNLOADS INTEGRATION
	   ============================================== */
	
	/**
	 * Enqueue EDD custom styles
	 */
	public static function edd_styles() {
		if ( get_option( 'swgtheme_edd_custom_styles', '1' ) === '1' ) {
			wp_enqueue_style( 'swg-edd', get_template_directory_uri() . '/css/edd.css', array(), SWGTHEME_VERSION );
		}
	}
	
	/* ==============================================
	   SWG AUTH PLUGIN INTEGRATION
	   ============================================== */
	
	/**
	 * Enqueue SWG Auth custom styles
	 */
	public static function swg_auth_styles() {
		if ( get_option( 'swgtheme_swg_auth_custom_styles', '1' ) === '1' ) {
			// Override plugin styles with theme styles
			wp_dequeue_style( 'swg-auth-metrics-widget' );
			wp_dequeue_style( 'swg-auth-resources' );
			wp_enqueue_style( 'swg-auth-theme', get_template_directory_uri() . '/css/swg-auth.css', array(), SWGTHEME_VERSION );
		}
	}
	
	/**
	 * Wrap SWG Auth widgets for better styling
	 */
	public static function swg_auth_widget_wrapper( $content ) {
		// Check if this is an SWG Auth widget
		if ( strpos( $content, 'swg-auth' ) !== false || strpos( $content, 'SWG Server' ) !== false ) {
			return '<div class="swg-auth-widget-wrapper">' . $content . '</div>';
		}
		return $content;
	}
	
	/**
	 * Add wrapper start for SWG Auth widgets
	 */
	public static function swg_auth_sidebar_wrapper_start() {
		echo '<div class="swg-auth-sidebar-widgets">';
	}
	
	/**
	 * Add wrapper end for SWG Auth widgets
	 */
	public static function swg_auth_sidebar_wrapper_end() {
		echo '</div>';
	}
}

// Initialize integrations
SWGTheme_Integrations::init();
