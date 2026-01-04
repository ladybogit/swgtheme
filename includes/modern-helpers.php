<?php
/**
 * Modern PHP 8+ Utilities and Helpers
 * 
 * Demonstrates modern PHP 8 features including:
 * - Null coalescing operator (??)
 * - Null safe operator (?->)
 * - Match expressions
 * - Named arguments
 * - Arrow functions
 * - Union types
 * 
 * @package swgtheme
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modern Helper Class using PHP 8 Features
 */
class SWGTheme_Modern_Helpers {
	
	/**
	 * Get sanitized POST value with null coalescing
	 * 
	 * @param string $key POST key
	 * @param mixed $default Default value
	 * @return mixed Sanitized value
	 */
	public static function get_post_value( string $key, mixed $default = '' ): mixed {
		return isset( $_POST[ $key ] ) 
			? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) 
			: $default;
	}
	
	/**
	 * Get sanitized GET value using null coalescing
	 * 
	 * @param string $key GET key
	 * @param mixed $default Default value
	 * @return mixed Sanitized value
	 */
	public static function get_query_value( string $key, mixed $default = '' ): mixed {
		return sanitize_text_field( $_GET[ $key ] ?? $default );
	}
	
	/**
	 * Get user meta with null coalescing
	 * 
	 * @param int $user_id User ID
	 * @param string $key Meta key
	 * @param mixed $default Default value
	 * @return mixed Meta value
	 */
	public static function get_user_meta_safe( int $user_id, string $key, mixed $default = '' ): mixed {
		return get_user_meta( $user_id, $key, true ) ?: $default;
	}
	
	/**
	 * Get post meta with null safe operator
	 * 
	 * @param int|null $post_id Post ID
	 * @param string $key Meta key
	 * @param mixed $default Default value
	 * @return mixed Meta value
	 */
	public static function get_post_meta_safe( ?int $post_id, string $key, mixed $default = '' ): mixed {
		$post_id ??= get_the_ID();
		return get_post_meta( $post_id, $key, true ) ?: $default;
	}
	
	/**
	 * Get theme option with null coalescing
	 * 
	 * @param string $key Option key
	 * @param mixed $default Default value
	 * @return mixed Option value
	 */
	public static function get_option_safe( string $key, mixed $default = '' ): mixed {
		return get_option( $key, $default ) ?: $default;
	}
	
	/**
	 * Get user display name using null safe operator
	 * 
	 * @param int|null $user_id User ID
	 * @return string Display name
	 */
	public static function get_user_display_name( ?int $user_id = null ): string {
		$user_id ??= get_current_user_id();
		$user = get_userdata( $user_id );
		return $user?->display_name ?? __( 'Guest', 'swgtheme' );
	}
	
	/**
	 * Get user role using match expression
	 * 
	 * @param int|null $user_id User ID
	 * @return string Friendly role name
	 */
	public static function get_user_role_label( ?int $user_id = null ): string {
		$user_id ??= get_current_user_id();
		$user = get_userdata( $user_id );
		$role = $user?->roles[0] ?? 'subscriber';
		
		return match( $role ) {
			'administrator' => __( 'Admin', 'swgtheme' ),
			'editor' => __( 'Editor', 'swgtheme' ),
			'author' => __( 'Author', 'swgtheme' ),
			'contributor' => __( 'Contributor', 'swgtheme' ),
			'subscriber' => __( 'Member', 'swgtheme' ),
			default => __( 'User', 'swgtheme' ),
		};
	}
	
	/**
	 * Format post date using match expression
	 * 
	 * @param string $format Date format type
	 * @param int|null $post_id Post ID
	 * @return string Formatted date
	 */
	public static function format_post_date( string $format = 'relative', ?int $post_id = null ): string {
		$post_id ??= get_the_ID();
		$timestamp = get_post_timestamp( $post_id );
		
		return match( $format ) {
			'relative' => human_time_diff( $timestamp, time() ) . ' ' . __( 'ago', 'swgtheme' ),
			'short' => date_i18n( 'M j, Y', $timestamp ),
			'long' => date_i18n( 'F j, Y', $timestamp ),
			'full' => date_i18n( 'l, F j, Y - g:i a', $timestamp ),
			default => date_i18n( get_option( 'date_format' ), $timestamp ),
		};
	}
	
	/**
	 * Get post thumbnail URL with fallback
	 * 
	 * @param string $size Image size
	 * @param int|null $post_id Post ID
	 * @return string|null Image URL or null
	 */
	public static function get_post_thumbnail_url_safe( string $size = 'full', ?int $post_id = null ): ?string {
		$post_id ??= get_the_ID();
		return get_the_post_thumbnail_url( $post_id, $size ) ?: null;
	}
	
	/**
	 * Check if user can perform action using arrow function
	 * 
	 * @param string $capability Capability to check
	 * @param int|null $user_id User ID
	 * @return bool True if user can
	 */
	public static function user_can( string $capability, ?int $user_id = null ): bool {
		$user_id ??= get_current_user_id();
		return user_can( $user_id, $capability );
	}
	
	/**
	 * Get filtered array using arrow functions
	 * 
	 * @param array $items Array of items
	 * @param callable $filter Filter function
	 * @return array Filtered items
	 */
	public static function filter_array( array $items, callable $filter ): array {
		return array_filter( $items, $filter );
	}
	
	/**
	 * Get mapped array using arrow functions
	 * 
	 * @param array $items Array of items
	 * @param callable $mapper Map function
	 * @return array Mapped items
	 */
	public static function map_array( array $items, callable $mapper ): array {
		return array_map( $mapper, $items );
	}
	
	/**
	 * Get post categories as formatted array
	 * Using arrow functions for transformation
	 * 
	 * @param int|null $post_id Post ID
	 * @return array Array of category data
	 */
	public static function get_post_categories_formatted( ?int $post_id = null ): array {
		$post_id ??= get_the_ID();
		$categories = get_the_category( $post_id );
		
		if ( empty( $categories ) ) {
			return array();
		}
		
		return array_map(
			fn( $cat ) => array(
				'id' => $cat->term_id,
				'name' => $cat->name,
				'slug' => $cat->slug,
				'url' => get_category_link( $cat->term_id ),
				'count' => $cat->count,
			),
			$categories
		);
	}
	
	/**
	 * Get recent posts with modern syntax
	 * 
	 * @param int $limit Number of posts
	 * @param string $post_type Post type
	 * @return array Array of post data
	 */
	public static function get_recent_posts( int $limit = 5, string $post_type = 'post' ): array {
		$posts = get_posts( array(
			'numberposts' => $limit,
			'post_type' => $post_type,
			'post_status' => 'publish',
		) );
		
		return array_map(
			fn( $post ) => array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'url' => get_permalink( $post->ID ),
				'date' => self::format_post_date( 'relative', $post->ID ),
				'author' => get_the_author_meta( 'display_name', $post->post_author ),
				'thumbnail' => self::get_post_thumbnail_url_safe( 'medium', $post->ID ),
			),
			$posts
		);
	}
	
	/**
	 * Validate email with modern return type
	 * 
	 * @param string|null $email Email address
	 * @return bool True if valid
	 */
	public static function is_valid_email( ?string $email ): bool {
		return ! empty( $email ) && is_email( $email ) !== false;
	}
	
	/**
	 * Get excerpt with length limit using null coalescing
	 * 
	 * @param int $length Word limit
	 * @param int|null $post_id Post ID
	 * @return string Excerpt
	 */
	public static function get_excerpt( int $length = 55, ?int $post_id = null ): string {
		$post_id ??= get_the_ID();
		$excerpt = get_the_excerpt( $post_id );
		return wp_trim_words( $excerpt, $length, '...' );
	}
	
	/**
	 * Check if post has content
	 * 
	 * @param int|null $post_id Post ID
	 * @return bool True if has content
	 */
	public static function has_content( ?int $post_id = null ): bool {
		$post_id ??= get_the_ID();
		$content = get_post_field( 'post_content', $post_id );
		return ! empty( trim( strip_tags( $content ?? '' ) ) );
	}
	
	/**
	 * Get reading time using modern syntax
	 * 
	 * @param int|null $post_id Post ID
	 * @param int $words_per_minute Reading speed
	 * @return int Reading time in minutes
	 */
	public static function get_reading_time( ?int $post_id = null, int $words_per_minute = 200 ): int {
		$post_id ??= get_the_ID();
		$content = get_post_field( 'post_content', $post_id );
		$word_count = str_word_count( strip_tags( $content ?? '' ) );
		return max( 1, (int) ceil( $word_count / $words_per_minute ) );
	}
	
	/**
	 * Convert value to boolean using match
	 * 
	 * @param mixed $value Value to convert
	 * @return bool Boolean value
	 */
	public static function to_bool( mixed $value ): bool {
		return match( true ) {
			is_bool( $value ) => $value,
			is_string( $value ) => in_array( strtolower( $value ), array( '1', 'true', 'yes', 'on' ), true ),
			is_numeric( $value ) => (int) $value === 1,
			default => false,
		};
	}
}

/**
 * Helper function to get instance
 * 
 * @return SWGTheme_Modern_Helpers
 */
function swgtheme_helpers(): SWGTheme_Modern_Helpers {
	return new SWGTheme_Modern_Helpers();
}

/**
 * Example: Modern shortcode using PHP 8 features
 */
add_shortcode( 'swg_recent_posts', function( $atts ) {
	$atts = shortcode_atts( array(
		'limit' => 5,
		'type' => 'post',
	), $atts ?? array(), 'swg_recent_posts' );
	
	$posts = SWGTheme_Modern_Helpers::get_recent_posts(
		limit: (int) $atts['limit'],
		post_type: $atts['type']
	);
	
	if ( empty( $posts ) ) {
		return '<p>' . __( 'No posts found.', 'swgtheme' ) . '</p>';
	}
	
	$output = '<div class="swg-recent-posts">';
	
	foreach ( $posts as $post ) {
		$output .= sprintf(
			'<div class="swg-post-item">
				<h3><a href="%s">%s</a></h3>
				<p class="post-meta">%s | %s</p>
			</div>',
			esc_url( $post['url'] ),
			esc_html( $post['title'] ),
			esc_html( $post['author'] ),
			esc_html( $post['date'] )
		);
	}
	
	$output .= '</div>';
	
	return $output;
} );
