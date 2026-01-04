<?php
/**
 * swgtheme functions and definitions
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'SWGTHEME_VERSION', '1.0.0' );

// Load modern PHP 8+ helpers
if ( version_compare( PHP_VERSION, '8.0.0', '>=' ) && file_exists( get_template_directory() . '/includes/modern-helpers.php' ) ) {
	require_once get_template_directory() . '/includes/modern-helpers.php';
}

// Load developer helpers
if ( file_exists( get_template_directory() . '/includes/dev-helpers.php' ) ) {
	require_once get_template_directory() . '/includes/dev-helpers.php';
}

// Load security helpers
if ( file_exists( get_template_directory() . '/includes/security-helpers.php' ) ) {
	require_once get_template_directory() . '/includes/security-helpers.php';
}

// Load integrations
if ( file_exists( get_template_directory() . '/includes/integrations.php' ) ) {
	require_once get_template_directory() . '/includes/integrations.php';
}

// Load widgets
if ( file_exists( get_template_directory() . '/includes/widgets/newsletter-widget.php' ) ) {
	require_once get_template_directory() . '/includes/widgets/newsletter-widget.php';
}
if ( file_exists( get_template_directory() . '/includes/widgets/twitch-widget.php' ) ) {
	require_once get_template_directory() . '/includes/widgets/twitch-widget.php';
}
if ( file_exists( get_template_directory() . '/includes/widgets/youtube-widget.php' ) ) {
	require_once get_template_directory() . '/includes/widgets/youtube-widget.php';
}

// Load admin bar customization
if ( file_exists( get_template_directory() . '/includes/admin-bar.php' ) ) {
	require_once get_template_directory() . '/includes/admin-bar.php';
}

// Load performance optimization
if ( file_exists( get_template_directory() . '/includes/performance.php' ) ) {
	require_once get_template_directory() . '/includes/performance.php';
}

function load_css() {
	// Google Fonts
	$heading_font = get_option( 'swgtheme_heading_font', 'Roboto' );
	$body_font = get_option( 'swgtheme_body_font', 'Open Sans' );
	
	$fonts_url = '';
	$font_families = array();
	
	if ( $heading_font && $heading_font !== 'Default' ) {
		$font_families[] = $heading_font . ':400,500,600,700';
	}
	
	if ( $body_font && $body_font !== 'Default' && $body_font !== $heading_font ) {
		$font_families[] = $body_font . ':400,400i,600,700';
	}
	
	if ( ! empty( $font_families ) ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'display' => 'swap',
		), 'https://fonts.googleapis.com/css' );
		
		wp_register_style( 'swg-google-fonts', $fonts_url, array(), null );
		wp_enqueue_style( 'swg-google-fonts' );
	}
	
	wp_register_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', 
		array(), SWGTHEME_VERSION, 'all' );
	wp_enqueue_style( 'bootstrap' );

	wp_register_style( 'stylesheet', get_template_directory_uri() . '/css/main.css', 
		array(), SWGTHEME_VERSION, 'all' );
	wp_enqueue_style( 'stylesheet' );

	wp_register_style( 'swg_styles', get_template_directory_uri() . '/css/swg-slider.css', 
		array(), SWGTHEME_VERSION, 'all' );
	wp_enqueue_style( 'swg_styles' );

	wp_register_style( 'swg_styles_theme', get_template_directory_uri() . '/css/default.css',
		array(), SWGTHEME_VERSION, 'all' );
	wp_enqueue_style( 'swg_styles_theme' );
	
	// Custom colors - loads last
	wp_register_style( 'swg_custom_colors', get_template_directory_uri() . '/css/custom-colors.css',
		array( 'bootstrap', 'stylesheet', 'swg_styles', 'swg_styles_theme' ), time(), 'all' );
	wp_enqueue_style( 'swg_custom_colors' );
	
	// Dark mode stylesheet
	if ( get_option( 'swgtheme_enable_dark_mode', '1' ) === '1' ) {
		wp_register_style( 'swg_dark_mode', get_template_directory_uri() . '/css/dark-mode.css',
			array( 'swg_custom_colors' ), SWGTHEME_VERSION, 'all' );
		wp_enqueue_style( 'swg_dark_mode' );
		
		// Add dark mode background image if set
		$dark_bg_image = get_option( 'swgtheme_dark_mode_background_image', '' );
		if ( ! empty( $dark_bg_image ) ) {
			$dark_bg_css = '[data-theme="dark"] body { background-image: url("' . esc_url( $dark_bg_image ) . '") !important; }';
			wp_add_inline_style( 'swg_dark_mode', $dark_bg_css );
		}
	}
	
	// Print stylesheet
	wp_register_style( 'swg_print', get_template_directory_uri() . '/css/print.css',
		array(), SWGTHEME_VERSION, 'print' );
	wp_enqueue_style( 'swg_print' );
	
	// Integration styles (CF7, Gravity Forms, bbPress)
	if ( get_option( 'swgtheme_cf7_custom_styles', '1' ) === '1' || 
	     get_option( 'swgtheme_bbpress_custom_styles', '1' ) === '1' ) {
		wp_enqueue_style( 'swg-integrations', get_template_directory_uri() . '/css/integrations.css',
			array( 'bootstrap' ), SWGTHEME_VERSION, 'all' );
	}
}

add_action( 'wp_enqueue_scripts', 'load_css' );

// Output custom typography CSS
function swgtheme_custom_typography_css() {
	$heading_font = get_option( 'swgtheme_heading_font', 'Roboto' );
	$body_font = get_option( 'swgtheme_body_font', 'Open Sans' );
	$heading_font_size = floatval( get_option( 'swgtheme_heading_font_size', '32' ) );
	$body_font_size = floatval( get_option( 'swgtheme_body_font_size', '16' ) );
	$letter_spacing = floatval( get_option( 'swgtheme_letter_spacing', '0' ) );
	$line_height = floatval( get_option( 'swgtheme_line_height', '1.6' ) );
	
	$css = '<style type="text/css">';
	
	// Heading font
	if ( $heading_font !== 'Default' ) {
		$css .= 'h1, h2, h3, h4, h5, h6 { font-family: "' . esc_attr( $heading_font ) . '", sans-serif; }';
	}
	
	// Body font
	if ( $body_font !== 'Default' ) {
		$css .= 'body, p, li, a, span { font-family: "' . esc_attr( $body_font ) . '", sans-serif; }';
	}
	
	// Font sizes
	$css .= 'h1 { font-size: ' . esc_attr( $heading_font_size ) . 'px; }';
	$css .= 'h2 { font-size: ' . esc_attr( $heading_font_size * 0.875 ) . 'px; }';
	$css .= 'h3 { font-size: ' . esc_attr( $heading_font_size * 0.75 ) . 'px; }';
	$css .= 'h4 { font-size: ' . esc_attr( $heading_font_size * 0.625 ) . 'px; }';
	$css .= 'h5 { font-size: ' . esc_attr( $heading_font_size * 0.5 ) . 'px; }';
	$css .= 'h6 { font-size: ' . esc_attr( $heading_font_size * 0.4375 ) . 'px; }';
	$css .= 'body, p { font-size: ' . esc_attr( $body_font_size ) . 'px; }';
	
	// Letter spacing
	$css .= 'body, p, h1, h2, h3, h4, h5, h6 { letter-spacing: ' . esc_attr( $letter_spacing ) . 'px; }';
	
	// Line height
	$css .= 'body, p { line-height: ' . esc_attr( $line_height ) . '; }';
	
	$css .= '</style>';
	
	echo $css;
}
add_action( 'wp_head', 'swgtheme_custom_typography_css' );

// Output custom background CSS
function swgtheme_custom_background_css() {
	$bg_image = get_option( 'swgtheme_background_image', '' );
	$bg_position = get_option( 'swgtheme_background_position', 'center' );
	$bg_size = get_option( 'swgtheme_background_size', 'cover' );
	$parallax = get_option( 'swgtheme_enable_parallax', '0' );
	
	if ( ! empty( $bg_image ) ) {
		$css = '<style type="text/css">';
		$css .= 'body { ';
		$css .= 'background-image: url(' . esc_url( $bg_image ) . '); ';
		$css .= 'background-position: ' . esc_attr( $bg_position ) . '; ';
		$css .= 'background-size: ' . esc_attr( $bg_size ) . '; ';
		$css .= 'background-repeat: no-repeat; ';
		
		if ( $parallax === '1' ) {
			$css .= 'background-attachment: fixed; ';
		}
		
		$css .= '}';
		$css .= '</style>';
		
		echo $css;
	}
}
add_action( 'wp_head', 'swgtheme_custom_background_css' );

function load_js() {
	wp_enqueue_script( 'jquery' );
	
	wp_register_script( 'bootstrapjs', get_template_directory_uri() . '/js/bootstrap.min.js', 
		array( 'jquery' ), SWGTHEME_VERSION, true );
	wp_enqueue_script( 'bootstrapjs' );
	
	// Enqueue SWG slider scripts
	wp_register_script( 'swg-slider', get_template_directory_uri() . '/js/jquery.swg.slider.js', 
		array( 'jquery' ), SWGTHEME_VERSION, true );
	wp_enqueue_script( 'swg-slider' );
	
	// Localize slider settings
	wp_localize_script( 'swg-slider', 'swgSliderSettings', array(
		'autoplay' => get_option( 'swgtheme_slider_autoplay', '0' ) === '1',
		'speed' => intval( get_option( 'swgtheme_slider_speed', '5000' ) ),
		'pauseOnHover' => get_option( 'swgtheme_slider_pause_hover', '1' ) === '1',
		'loop' => get_option( 'swgtheme_slider_loop', '1' ) === '1',
	) );
	
	// Theme features scripts
	wp_register_script( 'swg-theme-features', get_template_directory_uri() . '/js/theme-features.js', 
		array( 'jquery' ), SWGTHEME_VERSION, true );
	wp_enqueue_script( 'swg-theme-features' );
	
	wp_localize_script( 'swg-theme-features', 'swgTheme', array(
		'darkModeEnabled' => get_option( 'swgtheme_enable_dark_mode', '1' ) === '1',
		'darkModeDefault' => get_option( 'swgtheme_dark_mode_default', 'dark' ),
		'darkModeSystemPref' => get_option( 'swgtheme_dark_mode_system_preference', '0' ) === '1',
		'darkModeAuto' => get_option( 'swgtheme_dark_mode_auto', '0' ) === '1',
		'darkModeAutoStart' => get_option( 'swgtheme_dark_mode_auto_start', '18:00' ),
		'darkModeAutoEnd' => get_option( 'swgtheme_dark_mode_auto_end', '06:00' ),
		'darkModeTogglePosition' => get_option( 'swgtheme_dark_mode_toggle_position', 'bottom-right' ),
		'darkModeTransitionSpeed' => get_option( 'swgtheme_dark_mode_transition_speed', 'normal' ),
		'preloaderEnabled' => get_option( 'swgtheme_enable_preloader', '0' ) === '1',
		'backToTopEnabled' => get_option( 'swgtheme_enable_back_to_top', '1' ) === '1',
		'enableMobileMenu' => get_option( 'swgtheme_enable_mobile_menu', '1' ),
		'animationsEnabled' => get_option( 'swgtheme_enable_animations', '0' ),
		'animationSpeed' => get_option( 'swgtheme_animation_speed', '400' ),
		'tocEnabled' => get_option( 'swgtheme_enable_toc', '0' ),
		'tocMinHeadings' => get_option( 'swgtheme_toc_min_headings', '3' ),
		'stickyHeader' => get_option( 'swgtheme_enable_sticky_header', '0' ),
		'lazyLoad' => get_option( 'swgtheme_enable_lazy_load', '0' ),
		'ajaxSearch' => get_option( 'swgtheme_enable_ajax_search', '0' ),
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'ajaxNonce' => wp_create_nonce( 'swgtheme_ajax_nonce' ),
		'infiniteScroll' => get_option( 'swgtheme_enable_infinite_scroll', '0' ),
	) );
	
	// Advanced features script
	wp_enqueue_script( 'swg-advanced-features', get_template_directory_uri() . '/js/advanced-features.js', 
		array( 'jquery' ), SWGTHEME_VERSION, true );
	
	// Infinite scroll script
	if ( get_option( 'swgtheme_enable_infinite_scroll', '0' ) === '1' ) {
		wp_enqueue_script( 'swg-infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', 
			array( 'jquery', 'swg-theme-features' ), SWGTHEME_VERSION, true );
	}
	
	// Mobile menu script
	if ( get_option( 'swgtheme_enable_mobile_menu', '1' ) === '1' ) {
		wp_enqueue_script( 'swg-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', 
			array( 'jquery' ), SWGTHEME_VERSION, true );
	}
	
	wp_register_script( 'swg-init', get_template_directory_uri() . '/js/swg.js', 
		array( 'jquery', 'swg-slider' ), SWGTHEME_VERSION, true );
	wp_enqueue_script( 'swg-init' );
	
	// Newsletter integration script
	wp_enqueue_script( 'swg-newsletter', get_template_directory_uri() . '/js/newsletter.js', 
		array( 'jquery' ), SWGTHEME_VERSION, true );
	wp_localize_script( 'swg-newsletter', 'swgNewsletter', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'swg_newsletter_nonce' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'load_js' );

/**
 * Add custom colors CSS based on theme options.
 */
function swgtheme_custom_colors() {
	$primary_color = get_option( 'swgtheme_primary_color', '#dc3545' );
	$use_global = get_option( 'swgtheme_use_global_color', '1' );
	
	// If using global color, use primary for everything
	if ( $use_global === '1' ) {
		$btn_color = $primary_color;
		$bdr_color = $primary_color;
		$lnk_color = $primary_color;
		$text_color = get_option( 'swgtheme_text_color', '' );
	} else {
		// Use individual colors
		$button_color = get_option( 'swgtheme_button_color', '' );
		$border_color = get_option( 'swgtheme_border_color', '' );
		$link_color = get_option( 'swgtheme_link_color', '' );
		$text_color = get_option( 'swgtheme_text_color', '' );
		
		// Fall back to primary if individual colors are empty
		$btn_color = !empty( $button_color ) ? $button_color : $primary_color;
		$bdr_color = !empty( $border_color ) ? $border_color : $primary_color;
		$lnk_color = !empty( $link_color ) ? $link_color : $primary_color;
	}
	
	// Debug output
	error_log( 'SWG Theme: Primary color is ' . $primary_color );
	
	// Write to custom-colors.css file
	$css_file = get_template_directory() . '/css/custom-colors.css';
	$custom_css = "/* Generated by SWG Theme Options */
/* Primary Color: {$primary_color} */

:root {
	--primary-color: {$primary_color};
	--button-color: {$btn_color};
	--border-color: {$bdr_color};
	--link-color: {$lnk_color};
}

body {
	--primary-color: {$primary_color};
	--button-color: {$btn_color};
	--border-color: {$bdr_color};
	--link-color: {$lnk_color};
}
" . ( !empty( $text_color ) ? "
/* Text Color Override - Only for regular content text */
.page-wrap p,
.page-wrap li,
.page-wrap h2:not(.head1),
.page-wrap h3,
.page-wrap h4,
.page-wrap h5,
.page-wrap h6,
.col-lg-9 p,
.col-lg-3 p {
	color: {$text_color} !important;
}
" : "" ) . "

/* Force Border Colors with Maximum Specificity */
.img-thumbnail,
img.img-thumbnail,
.col-lg-9 .img-thumbnail,
.col-lg-9 img.img-thumbnail,
.page-wrap .img-thumbnail,
.page-wrap img.img-thumbnail {
	border: 3px solid {$bdr_color} !important;
	border-color: {$bdr_color} !important;
}

.head1,
h1.head1,
.col-lg-9 .head1,
.col-lg-9 h1.head1,
.page-wrap .head1,
.page-wrap h1.head1,
.container .head1,
.container h1.head1 {
	border-bottom: 3px solid {$bdr_color} !important;
	border-bottom-color: {$bdr_color} !important;
	background: {$bdr_color} !important;
	background-color: {$bdr_color} !important;
}

/* Bootstrap Buttons */
.btn-danger,
.btn.btn-danger,
button.btn-danger,
.btn-outline-danger {
	background: {$btn_color} !important;
	background-color: {$btn_color} !important;
	background-image: none !important;
	border: 1px solid {$btn_color} !important;
	border-color: {$btn_color} !important;
}

.btn-danger:hover,
.btn-danger:focus,
.btn-danger:active,
.btn.btn-danger:hover,
.btn.btn-danger:focus,
.btn.btn-danger:active,
.btn-outline-danger:hover,
.btn-outline-danger:focus,
.btn-outline-danger:active {
	background: {$btn_color} !important;
	background-color: {$btn_color} !important;
	background-image: none !important;
	border: 1px solid {$btn_color} !important;
	border-color: {$btn_color} !important;
	filter: brightness(0.85);
}

/* Bootstrap Cards */
.card,
.card-header,
.card-footer {
	border-color: {$bdr_color} !important;
}

.card-danger,
.card-danger .card-header {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

/* Bootstrap Tables */
.table-bordered,
.table-bordered th,
.table-bordered td {
	border-color: {$bdr_color} !important;
}

.table-danger,
.table-danger > th,
.table-danger > td {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

/* Bootstrap Forms */
.form-control:focus {
	border-color: {$bdr_color} !important;
	box-shadow: 0 0 0 0.2rem rgba({$bdr_color}, 0.25) !important;
}

.input-group-text {
	border-color: {$bdr_color} !important;
}

/* Bootstrap Navigation */
.nav-tabs .nav-link.active {
	border-color: {$bdr_color} {$bdr_color} #fff !important;
}

.nav-pills .nav-link.active {
	background-color: {$btn_color} !important;
}

/* Bootstrap Pagination */
.page-item.active .page-link {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

.page-link:hover,
.page-link:focus {
	color: {$lnk_color} !important;
	border-color: {$bdr_color} !important;
}

/* Bootstrap Alerts */
.alert-danger {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

/* Bootstrap Badges */
.badge-success,
.badge-danger {
	background-color: {$btn_color} !important;
}

/* Bootstrap Borders */
.border,
.border-top,
.border-right,
.border-bottom,
.border-left,
.border-danger {
	border-color: {$bdr_color} !important;
}

/* Bootstrap Grid Columns with Borders */
.row {
	border-color: {$bdr_color} !important;
}

.slide {
	border-color: {$bdr_color} !important;
}

.top-menu,
.top-menu li,
.top-menu li a {
	border-color: {$bdr_color} !important;
}

.header .top-menu li a,
header .top-menu li a {
	background: {$btn_color} !important;
	background-color: {$btn_color} !important;
	background-image: none !important;
}

.header .top-menu li a:hover,
header .top-menu li a:hover {
	background: {$btn_color} !important;
	background-color: {$btn_color} !important;
	background-image: none !important;
	filter: brightness(0.85);
}

.col,
.col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
.col-7, .col-8, .col-9, .col-10, .col-11, .col-12,
.col-sm, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6,
.col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12,
.col-md, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6,
.col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12,
.col-lg, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6,
.col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12,
.col-xl, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6,
.col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12 {
	border-color: {$bdr_color} !important;
}

.col-lg-3,
.col-lg-9 {
	border-color: {$bdr_color} !important;
}
";

	// Column background colors
	$col_lg_3_bg = get_option( 'swgtheme_col_lg_3_bg_color', '#ffffff' );
	$col_lg_9_bg = get_option( 'swgtheme_col_lg_9_bg_color', '#ffffff' );
	
	$custom_css .= "
/* Column Background Colors */
.col-lg-3 {
	background-color: {$col_lg_3_bg} !important;
}

.col-lg-9 {
	background-color: {$col_lg_9_bg} !important;
}

/* Bootstrap Dropdowns */
.dropdown-item.active,
.dropdown-item:active {
	background-color: {$btn_color} !important;
}

/* Bootstrap List Groups */
.list-group-item-danger {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

.list-group-item.active {
	background-color: {$btn_color} !important;
	border-color: {$bdr_color} !important;
}

/* Bootstrap Progress */
.progress-bar {
	background-color: {$btn_color} !important;
}

/* Bootstrap Text & Background */
.text-danger {
	color: {$lnk_color} !important;
}

.bg-danger {
	background-color: {$btn_color} !important;
}

/* Links */
a {
	color: {$lnk_color} !important;
}

a:hover,
a:focus {
	color: {$lnk_color} !important;
	filter: brightness(0.85);
}

/* Widget Titles */
.widget-title,
h4.widget-title {
	background-color: {$btn_color} !important;
	border-left: 4px solid {$bdr_color} !important;
}
";
	
	// Write the file
	file_put_contents( $css_file, $custom_css );
}
add_action( 'wp_enqueue_scripts', 'swgtheme_custom_colors', 1 );

/**
 * Enqueue block editor styles.
 */
function swgtheme_block_editor_styles() {
	wp_enqueue_style( 'swgtheme-block-editor-styles', get_template_directory_uri() . '/css/main.css', array(), SWGTHEME_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'swgtheme_block_editor_styles' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function swgtheme_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'swgtheme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Switch default core markup to output valid HTML5.
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	) );

	// Add theme support for custom background.
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

	// Add theme support for custom header.
	add_theme_support( 'custom-header', array(
		'default-image' => '',
		'width'         => 1920,
		'height'        => 200,
		'flex-height'   => true,
	) );

	// Add support for custom logo.
	add_theme_support( 'custom-logo', array(
		'height'      => 480,
		'width'       => 720,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );
	add_editor_style();

	// Add Gutenberg/Block Editor support.
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	
	// Add support for custom line height.
	add_theme_support( 'custom-line-height' );
	
	// Add support for custom spacing.
	add_theme_support( 'custom-spacing' );
	
	// Add support for custom units.
	add_theme_support( 'custom-units' );
	
	// Add support for link color.
	add_theme_support( 'link-color' );
	
	// Add support for block templates.
	add_theme_support( 'block-templates' );
	
	// Add support for editor color palette.
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Strong Red', 'swgtheme' ),
			'slug'  => 'strong-red',
			'color' => '#dc3545',
		),
		array(
			'name'  => __( 'Dark Gray', 'swgtheme' ),
			'slug'  => 'dark-gray',
			'color' => '#343a40',
		),
		array(
			'name'  => __( 'Light Gray', 'swgtheme' ),
			'slug'  => 'light-gray',
			'color' => '#f8f9fa',
		),
		array(
			'name'  => __( 'White', 'swgtheme' ),
			'slug'  => 'white',
			'color' => '#ffffff',
		),
		array(
			'name'  => __( 'Black', 'swgtheme' ),
			'slug'  => 'black',
			'color' => '#000000',
		),
	) );
	
	// Add support for editor font sizes.
	add_theme_support( 'editor-font-sizes', array(
		array(
			'name'      => __( 'Small', 'swgtheme' ),
			'shortName' => __( 'S', 'swgtheme' ),
			'size'      => 14,
			'slug'      => 'small',
		),
		array(
			'name'      => __( 'Normal', 'swgtheme' ),
			'shortName' => __( 'M', 'swgtheme' ),
			'size'      => 16,
			'slug'      => 'normal',
		),
		array(
			'name'      => __( 'Large', 'swgtheme' ),
			'shortName' => __( 'L', 'swgtheme' ),
			'size'      => 24,
			'slug'      => 'large',
		),
		array(
			'name'      => __( 'Huge', 'swgtheme' ),
			'shortName' => __( 'XL', 'swgtheme' ),
			'size'      => 32,
			'slug'      => 'huge',
		),
	) );

	// Register navigation menus.
	register_nav_menus( array(
		'top-menu'    => __( 'Top Menu Location', 'swgtheme' ),
		'side-menu'   => __( 'Side Menu Location', 'swgtheme' ),
		'footer-menu' => __( 'Footer Menu Location', 'swgtheme' ),
		'mobile-menu' => __( 'Mobile Menu Location', 'swgtheme' ),
	) );

	// Add custom image sizes.
	add_image_size( 'blog-large', 800, 400, true );
	add_image_size( 'blog-small', 300, 200, true );
	add_image_size( 'swg_widget', 1000, 1000, true );
	add_image_size( 'swg_function', 1600, 1400, true );
}
add_action( 'after_setup_theme', 'swgtheme_setup' );

/**
 * Register widget areas.
 */
function swgtheme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'swgtheme' ),
		'id'            => 'page-sidebar',
		'description'   => __( 'Add widgets here to appear in your sidebar on pages.', 'swgtheme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Gallery Sidebar', 'swgtheme' ),
		'id'            => 'gallery-sidebar',
		'description'   => __( 'Add widgets here to appear in your gallery sidebar.', 'swgtheme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Sidebar', 'swgtheme' ),
		'id'            => 'footer-sidebar',
		'description'   => __( 'Add widgets here to appear in your footer.', 'swgtheme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'swgtheme_widgets_init' );

/**
 * Enable tags for pages.
 */
function tags_support_all() {
	register_taxonomy_for_object_type( 'post_tag', 'page' );
}

/**
 * Include pages in tag queries.
 *
 * @param WP_Query $wp_query The WP_Query instance.
 */
function tags_support_query( $wp_query ) {
	if ( $wp_query->get( 'tag' ) ) {
		$wp_query->set( 'post_type', 'any' );
	}
}

add_action( 'init', 'tags_support_all' );
add_action( 'pre_get_posts', 'tags_support_query' );

/**
 * Register custom post type for SWG images.
 */
function swg_init() {
	$labels = array(
		'name'               => __( 'SWG Images', 'swgtheme' ),
		'singular_name'      => __( 'SWG Image', 'swgtheme' ),
		'menu_name'          => __( 'SWG Slider Images', 'swgtheme' ),
		'add_new'            => __( 'Add New', 'swgtheme' ),
		'add_new_item'       => __( 'Add New Image', 'swgtheme' ),
		'edit_item'          => __( 'Edit Image', 'swgtheme' ),
		'new_item'           => __( 'New Image', 'swgtheme' ),
		'view_item'          => __( 'View Image', 'swgtheme' ),
		'search_items'       => __( 'Search Images', 'swgtheme' ),
		'not_found'          => __( 'No images found', 'swgtheme' ),
		'not_found_in_trash' => __( 'No images found in Trash', 'swgtheme' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'swg-images' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => false,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-images-alt2',
		'supports'            => array( 'title', 'thumbnail', 'editor' ),
	);

	register_post_type( 'swg_images', $args );
}
add_action( 'init', 'swg_init' );

/**
 * Add SWG Slider Options submenu page.
 */
function swgtheme_slider_options_menu() {
	add_submenu_page(
		'edit.php?post_type=swg_images',
		__( 'Slider Options', 'swgtheme' ),
		__( 'Slider Options', 'swgtheme' ),
		'manage_options',
		'swg-slider-options',
		'swgtheme_slider_options_page'
	);
}
add_action( 'admin_menu', 'swgtheme_slider_options_menu' );

/**
 * Register slider options settings.
 */
function swgtheme_register_slider_settings() {
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_caption_enabled' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_caption_text' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_use_background' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_background_color' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_use_border' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_border_color' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_use_shadow' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_shadow_color' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_use_global_text_color' );
	register_setting( 'swgtheme_slider_options_group', 'swgtheme_slider_text_color' );
}
add_action( 'admin_init', 'swgtheme_register_slider_settings' );

/**
 * SWG Slider Options page content.
 */
function swgtheme_slider_options_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'swgtheme_slider_options_group' );
			do_settings_sections( 'swgtheme_slider_options_group' );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Display Caption', 'swgtheme' ); ?>
					</th>
					<td>
						<label for="swgtheme_slider_caption_enabled">
							<input type="checkbox" 
								id="swgtheme_slider_caption_enabled" 
								name="swgtheme_slider_caption_enabled" 
								value="1" 
								<?php checked( get_option( 'swgtheme_slider_caption_enabled', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Enable slider caption', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="swgtheme_slider_caption_text">
							<?php esc_html_e( 'Caption Text', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="text" 
							id="swgtheme_slider_caption_text" 
							name="swgtheme_slider_caption_text" 
							value="<?php echo esc_attr( get_option( 'swgtheme_slider_caption_text', 'Star Wars Galaxies - A Galaxy Far, Far Away' ) ); ?>" 
							class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Caption text to display on the slider', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Caption Styling', 'swgtheme' ); ?>
					</th>
					<td>
						<p>
							<label for="swgtheme_slider_use_background">
								<input type="checkbox" 
									id="swgtheme_slider_use_background" 
									name="swgtheme_slider_use_background" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_use_background', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Use Background', 'swgtheme' ); ?>
							</label>
						</p>
						<p>
							<label for="swgtheme_slider_background_color">
								<?php esc_html_e( 'Background Color:', 'swgtheme' ); ?>
							</label><br>
							<input type="text" 
								id="swgtheme_slider_background_color" 
								name="swgtheme_slider_background_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_background_color', 'rgba(0, 0, 0, 0.7)' ) ); ?>" 
								class="swg-color-picker" />
						</p>
						<p>
							<label for="swgtheme_slider_use_border">
								<input type="checkbox" 
									id="swgtheme_slider_use_border" 
									name="swgtheme_slider_use_border" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_use_border', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Use Border', 'swgtheme' ); ?>
							</label>
						</p>
						<p>
							<label for="swgtheme_slider_border_color">
								<?php esc_html_e( 'Border Color:', 'swgtheme' ); ?>
							</label><br>
							<input type="text" 
								id="swgtheme_slider_border_color" 
								name="swgtheme_slider_border_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_border_color', '#ffffff' ) ); ?>" 
								class="swg-color-picker" />
						</p>
						<p>
							<label for="swgtheme_slider_use_shadow">
								<input type="checkbox" 
									id="swgtheme_slider_use_shadow" 
									name="swgtheme_slider_use_shadow" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_use_shadow', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Use Shadow', 'swgtheme' ); ?>
							</label>
						</p>
						<p>
							<label for="swgtheme_slider_shadow_color">
								<?php esc_html_e( 'Shadow Color:', 'swgtheme' ); ?>
							</label><br>
							<input type="text" 
								id="swgtheme_slider_shadow_color" 
								name="swgtheme_slider_shadow_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_shadow_color', 'rgba(0, 0, 0, 0.5)' ) ); ?>" 
								class="swg-color-picker" />
						</p>
						<p>
							<label for="swgtheme_slider_use_global_text_color">
								<input type="checkbox" 
									id="swgtheme_slider_use_global_text_color" 
									name="swgtheme_slider_use_global_text_color" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_use_global_text_color', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Use Global Text Color', 'swgtheme' ); ?>
							</label>
						</p>
						<p>
							<label for="swgtheme_slider_text_color">
								<?php esc_html_e( 'Text Color:', 'swgtheme' ); ?>
							</label><br>
							<input type="text" 
								id="swgtheme_slider_text_color" 
								name="swgtheme_slider_text_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_text_color', '#ffffff' ) ); ?>" 
								class="swg-color-picker" />
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	jQuery(document).ready(function($) {
		$('.swg-color-picker').wpColorPicker();
		
		// Toggle background color visibility
		function toggleBackgroundColor() {
			if ($('#swgtheme_slider_use_background').is(':checked')) {
				$('#swgtheme_slider_use_background').closest('p').next('p').show();
			} else {
				$('#swgtheme_slider_use_background').closest('p').next('p').hide();
			}
		}
		toggleBackgroundColor();
		$('#swgtheme_slider_use_background').on('change', toggleBackgroundColor);
		
		// Toggle border color visibility
		function toggleBorderColor() {
			if ($('#swgtheme_slider_use_border').is(':checked')) {
				$('#swgtheme_slider_use_border').closest('p').next('p').show();
			} else {
				$('#swgtheme_slider_use_border').closest('p').next('p').hide();
			}
		}
		toggleBorderColor();
		$('#swgtheme_slider_use_border').on('change', toggleBorderColor);
		
		// Toggle shadow color visibility
		function toggleShadowColor() {
			if ($('#swgtheme_slider_use_shadow').is(':checked')) {
				$('#swgtheme_slider_use_shadow').closest('p').next('p').show();
			} else {
				$('#swgtheme_slider_use_shadow').closest('p').next('p').hide();
			}
		}
		toggleShadowColor();
		$('#swgtheme_slider_use_shadow').on('change', toggleShadowColor);
		
		// Toggle text color visibility (opposite logic - hide when global is checked)
		function toggleTextColor() {
			if ($('#swgtheme_slider_use_global_text_color').is(':checked')) {
				$('#swgtheme_slider_use_global_text_color').closest('p').next('p').hide();
			} else {
				$('#swgtheme_slider_use_global_text_color').closest('p').next('p').show();
			}
		}
		toggleTextColor();
		$('#swgtheme_slider_use_global_text_color').on('change', toggleTextColor);
	});
	</script>
	<?php
}

/**
 * Enqueue color picker for slider options page.
 */
function swgtheme_slider_options_scripts( $hook ) {
	if ( 'swg_images_page_swg-slider-options' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'swgtheme_slider_options_scripts' );

/**
 * Enqueue media uploader for theme options page.
 */
function swgtheme_options_page_scripts( $hook ) {
	if ( 'appearance_page_swgtheme-options' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script( 
		'swgtheme-options-upload', 
		get_template_directory_uri() . '/js/options-upload.js', 
		array( 'jquery', 'wp-color-picker' ), 
		SWGTHEME_VERSION, 
		true 
	);
	
	// Add tab navigation script
	$tab_script = "
	jQuery(document).ready(function($) {
		console.log('Tab script loaded - enqueued version');
		console.log('Found tabs:', $('.nav-tab-wrapper .nav-tab').length);
		console.log('Found tab contents:', $('.tab-content').length);
		
		// Tab Navigation
		$('.nav-tab-wrapper').on('click', '.nav-tab', function(e) {
			e.preventDefault();
			console.log('Tab clicked:', $(this).text());
			
			var targetId = $(this).attr('href').replace('#', '') + '-tab';
			console.log('Switching to:', targetId);
			
			$('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			
			$('.tab-content').hide();
			$('#' + targetId).show();
			
			sessionStorage.setItem('swgtheme_active_tab', $(this).attr('href'));
		});
		
		// Use Global Color toggle
		$('#swgtheme_use_global_color').on('change', function() {
			if ($(this).is(':checked')) {
				$('#primary-color-row').show();
				$('.global-color-override').hide();
			} else {
				$('#primary-color-row').hide();
				$('.global-color-override').show();
			}
		});
		
		// Restore or show first tab
		var activeTab = sessionStorage.getItem('swgtheme_active_tab');
		if (activeTab && $('.nav-tab-wrapper .nav-tab[href=\"' + activeTab + '\"]').length) {
			$('.nav-tab-wrapper .nav-tab[href=\"' + activeTab + '\"]').trigger('click');
		} else {
			$('#general-tab').show();
		}
	});
	";
	wp_add_inline_script( 'swgtheme-options-upload', $tab_script );
}
add_action( 'admin_enqueue_scripts', 'swgtheme_options_page_scripts' );

/**

/**
 * Generate SWG slider HTML.
 *
 * @param string $type Image size.
 * @return string Slider HTML.
 */
function swg_function( $type = 'swg_function' ) {
	$args = array(
		'post_type'      => 'swg_images',
		'posts_per_page' => 5,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);

	$result = '<div class="slider-wrapper theme-default">';
	$result .= '<div id="slider" class="swgSlider" style="position: relative;">';

	$loop = new WP_Query( $args );
	
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$post_id = get_the_ID();
			$the_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $type );
			
			if ( $the_url && isset( $the_url[0] ) ) {
				$result .= sprintf(
					'<img title="%s" src="%s" data-thumb="%s" alt="%s"/>',
					esc_attr( get_the_title() ),
					esc_url( $the_url[0] ),
					esc_url( $the_url[0] ),
					esc_attr( get_the_title() )
				);
			}
		}
		wp_reset_postdata();
	}
	
	// Add caption if enabled
	$caption_enabled = get_option( 'swgtheme_slider_caption_enabled', '1' );
	if ( '1' === $caption_enabled ) {
		$caption_text = get_option( 'swgtheme_slider_caption_text', 'Star Wars Galaxies - A Galaxy Far, Far Away' );
		$use_background = get_option( 'swgtheme_slider_use_background', '1' );
		$background_color = get_option( 'swgtheme_slider_background_color', 'rgba(0, 0, 0, 0.7)' );
		$use_border = get_option( 'swgtheme_slider_use_border', '0' );
		$border_color = get_option( 'swgtheme_slider_border_color', '#ffffff' );
		$use_shadow = get_option( 'swgtheme_slider_use_shadow', '1' );
		$shadow_color = get_option( 'swgtheme_slider_shadow_color', 'rgba(0, 0, 0, 0.5)' );
		$use_global_text = get_option( 'swgtheme_slider_use_global_text_color', '1' );
		$text_color = get_option( 'swgtheme_slider_text_color', '#ffffff' );
		
		$caption_style = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 20px 40px; border-radius: 10px; text-align: center; font-size: 24px; font-weight: bold; z-index: 10; max-width: 80%;';
		
		if ( '1' === $use_global_text ) {
			$global_text_color = get_option( 'swgtheme_txt_color' );
			if ( $global_text_color ) {
				$caption_style .= ' color: ' . esc_attr( $global_text_color ) . ';';
			} else {
				$caption_style .= ' color: #fff;';
			}
		} else {
			$caption_style .= ' color: ' . esc_attr( $text_color ) . ';';
		}
		
		if ( '1' === $use_background ) {
			$caption_style .= ' background: ' . esc_attr( $background_color ) . ';';
		}
		
		if ( '1' === $use_border ) {
			$caption_style .= ' border: 3px solid ' . esc_attr( $border_color ) . ';';
		}
		
		if ( '1' === $use_shadow ) {
			$caption_style .= ' box-shadow: 0 4px 15px ' . esc_attr( $shadow_color ) . ';';
		}
		
		$result .= '<div id="htmlcaption" class="swg-html-caption" style="' . $caption_style . '">';
		$result .= esc_html( $caption_text );
		$result .= '</div>';
	}

	$result .= '</div>';
	$result .= '</div>';
	
	return $result;
}

/*add_shortcode('swg-shortcode', 'swg_function');
*/
/**
 * Register SWG widget.
 */
function swg_widgets_init() {
	register_widget( 'swg_Widget' );
	register_widget( 'SWG_Social_Media_Widget' );
	register_widget( 'SWG_Recent_Posts_Widget' );
	register_widget( 'SWG_Newsletter_Widget' );
	register_widget( 'SWG_Popular_Posts_Widget' );
	register_widget( 'SWG_Author_Bio_Widget' );
}
add_action( 'widgets_init', 'swg_widgets_init' );

/**
 * Register Custom Post Types
 */
function swgtheme_register_custom_post_types() {
	// Portfolio
	register_post_type( 'portfolio', array(
		'labels' => array(
			'name'          => __( 'Portfolio', 'swgtheme' ),
			'singular_name' => __( 'Portfolio Item', 'swgtheme' ),
			'add_new'       => __( 'Add New Item', 'swgtheme' ),
			'add_new_item'  => __( 'Add New Portfolio Item', 'swgtheme' ),
			'edit_item'     => __( 'Edit Portfolio Item', 'swgtheme' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'menu_icon'    => 'dashicons-portfolio',
		'rewrite'      => array( 'slug' => 'portfolio' ),
	) );
	
	// Testimonials
	register_post_type( 'testimonial', array(
		'labels' => array(
			'name'          => __( 'Testimonials', 'swgtheme' ),
			'singular_name' => __( 'Testimonial', 'swgtheme' ),
			'add_new'       => __( 'Add New Testimonial', 'swgtheme' ),
			'edit_item'     => __( 'Edit Testimonial', 'swgtheme' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'    => 'dashicons-format-quote',
		'rewrite'      => array( 'slug' => 'testimonials' ),
	) );
	
	// Team Members
	register_post_type( 'team', array(
		'labels' => array(
			'name'          => __( 'Team', 'swgtheme' ),
			'singular_name' => __( 'Team Member', 'swgtheme' ),
			'add_new'       => __( 'Add New Member', 'swgtheme' ),
			'edit_item'     => __( 'Edit Team Member', 'swgtheme' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'    => 'dashicons-groups',
		'rewrite'      => array( 'slug' => 'team' ),
	) );
	
	// FAQ
	register_post_type( 'faq', array(
		'labels' => array(
			'name'          => __( 'FAQs', 'swgtheme' ),
			'singular_name' => __( 'FAQ', 'swgtheme' ),
			'add_new'       => __( 'Add New FAQ', 'swgtheme' ),
			'edit_item'     => __( 'Edit FAQ', 'swgtheme' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor' ),
		'menu_icon'    => 'dashicons-editor-help',
		'rewrite'      => array( 'slug' => 'faq' ),
	) );
}
add_action( 'init', 'swgtheme_register_custom_post_types' );

/**
 * Post Views Counter
 */
function swgtheme_set_post_views( $post_id ) {
	if ( get_option( 'swgtheme_enable_post_views', '0' ) !== '1' ) {
		return;
	}
	
	$count_key = 'swg_post_views';
	$count = get_post_meta( $post_id, $count_key, true );
	
	if ( $count == '' ) {
		$count = 0;
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
	} else {
		$count++;
		update_post_meta( $post_id, $count_key, $count );
	}
}

function swgtheme_get_post_views( $post_id ) {
	$count_key = 'swg_post_views';
	$count = get_post_meta( $post_id, $count_key, true );
	
	if ( $count == '' ) {
		return '0';
	}
	
	return $count;
}

function swgtheme_track_post_views( $content ) {
	if ( is_single() ) {
		swgtheme_set_post_views( get_the_ID() );
	}
	return $content;
}
add_filter( 'the_content', 'swgtheme_track_post_views' );

/**
 * Estimated Reading Time
 */
function swgtheme_reading_time() {
	if ( get_option( 'swgtheme_enable_reading_time', '0' ) !== '1' || ! is_single() ) {
		return;
	}
	
	$content = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( strip_tags( $content ) );
	$reading_speed = absint( get_option( 'swgtheme_reading_speed', '200' ) );
	$reading_time = ceil( $word_count / $reading_speed );
	
	echo '<div class="swg-reading-time">';
	printf( esc_html__( '%d min read', 'swgtheme' ), $reading_time );
	echo '</div>';
}

/**
 * Shortcodes
 */
// Button Shortcode
function swgtheme_button_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'url'    => '#',
		'style'  => 'primary',
		'size'   => 'medium',
		'target' => '_self',
	), $atts );
	
	return sprintf(
		'<a href="%s" class="swg-button swg-button-%s swg-button-%s" target="%s">%s</a>',
		esc_url( $atts['url'] ),
		esc_attr( $atts['style'] ),
		esc_attr( $atts['size'] ),
		esc_attr( $atts['target'] ),
		do_shortcode( $content )
	);
}
add_shortcode( 'button', 'swgtheme_button_shortcode' );

// Alert Shortcode
function swgtheme_alert_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'type' => 'info',
	), $atts );
	
	return sprintf(
		'<div class="swg-alert swg-alert-%s">%s</div>',
		esc_attr( $atts['type'] ),
		do_shortcode( $content )
	);
}
add_shortcode( 'alert', 'swgtheme_alert_shortcode' );

// Columns Shortcode
function swgtheme_column_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'width' => '6',
	), $atts );
	
	return sprintf(
		'<div class="swg-column swg-col-%s">%s</div>',
		esc_attr( $atts['width'] ),
		do_shortcode( $content )
	);
}
add_shortcode( 'column', 'swgtheme_column_shortcode' );

/**
 * SWG Slideshow Widget Class.
 */
class swg_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'swg_Widget',
			__( 'SWG Slideshow', 'swgtheme' ),
			array( 'description' => __( 'Display a Star Wars Galaxies slideshow', 'swgtheme' ) )
		);
	}

	/**
	 * Widget form.
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'SWG Slideshow', 'swgtheme' );
		$image_count = isset( $instance['image_count'] ) ? absint( $instance['image_count'] ) : 5;
		$image_size = isset( $instance['image_size'] ) ? $instance['image_size'] : 'swg_function';
		$autoplay = isset( $instance['autoplay'] ) ? (bool) $instance['autoplay'] : true;
		$transition_speed = isset( $instance['transition_speed'] ) ? absint( $instance['transition_speed'] ) : 5000;
		$show_controls = isset( $instance['show_controls'] ) ? (bool) $instance['show_controls'] : true;
		$show_indicators = isset( $instance['show_indicators'] ) ? (bool) $instance['show_indicators'] : true;
		$slider_height = isset( $instance['slider_height'] ) ? absint( $instance['slider_height'] ) : 400;
		$pause_on_hover = isset( $instance['pause_on_hover'] ) ? (bool) $instance['pause_on_hover'] : true;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_count' ) ); ?>">
				<?php esc_html_e( 'Number of Images:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'image_count' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'image_count' ) ); ?>" 
				type="number" 
				min="1" 
				max="20" 
				value="<?php echo esc_attr( $image_count ); ?>" />
			<small><?php esc_html_e( 'How many images to display (1-20)', 'swgtheme' ); ?></small>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>">
				<?php esc_html_e( 'Image Size:', 'swgtheme' ); ?>
			</label>
			<select class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>">
				<option value="swg_function" <?php selected( $image_size, 'swg_function' ); ?>>
					<?php esc_html_e( 'Default', 'swgtheme' ); ?>
				</option>
				<option value="swg_widget" <?php selected( $image_size, 'swg_widget' ); ?>>
					<?php esc_html_e( 'Widget Size', 'swgtheme' ); ?>
				</option>
				<option value="large" <?php selected( $image_size, 'large' ); ?>>
					<?php esc_html_e( 'Large', 'swgtheme' ); ?>
				</option>
				<option value="medium" <?php selected( $image_size, 'medium' ); ?>>
					<?php esc_html_e( 'Medium', 'swgtheme' ); ?>
				</option>
				<option value="thumbnail" <?php selected( $image_size, 'thumbnail' ); ?>>
					<?php esc_html_e( 'Thumbnail', 'swgtheme' ); ?>
				</option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'slider_height' ) ); ?>">
				<?php esc_html_e( 'Slider Height (px):', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'slider_height' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'slider_height' ) ); ?>" 
				type="number" 
				min="200" 
				max="1200" 
				step="50" 
				value="<?php echo esc_attr( $slider_height ); ?>" />
			<small><?php esc_html_e( 'Height in pixels (200-1200)', 'swgtheme' ); ?></small>
		</p>
		
		<p>
			<input type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>" 
				<?php checked( $autoplay ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>">
				<?php esc_html_e( 'Enable Autoplay', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'transition_speed' ) ); ?>">
				<?php esc_html_e( 'Transition Speed (ms):', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'transition_speed' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'transition_speed' ) ); ?>" 
				type="number" 
				min="1000" 
				max="10000" 
				step="500" 
				value="<?php echo esc_attr( $transition_speed ); ?>" />
			<small><?php esc_html_e( 'Time between slides in milliseconds', 'swgtheme' ); ?></small>
		</p>
		
		<p>
			<input type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( 'pause_on_hover' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'pause_on_hover' ) ); ?>" 
				<?php checked( $pause_on_hover ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'pause_on_hover' ) ); ?>">
				<?php esc_html_e( 'Pause on Hover', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p>
			<input type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( 'show_controls' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_controls' ) ); ?>" 
				<?php checked( $show_controls ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_controls' ) ); ?>">
				<?php esc_html_e( 'Show Navigation Controls', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p>
			<input type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( 'show_indicators' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_indicators' ) ); ?>" 
				<?php checked( $show_indicators ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_indicators' ) ); ?>">
				<?php esc_html_e( 'Show Slide Indicators', 'swgtheme' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Update widget.
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 * @return array Updated instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? 
			sanitize_text_field( $new_instance['title'] ) : '';
		$instance['image_count'] = ! empty( $new_instance['image_count'] ) ? 
			absint( $new_instance['image_count'] ) : 5;
		$instance['image_size'] = ! empty( $new_instance['image_size'] ) ? 
			sanitize_text_field( $new_instance['image_size'] ) : 'swg_function';
		$instance['autoplay'] = isset( $new_instance['autoplay'] ) ? 
			(bool) $new_instance['autoplay'] : false;
		$instance['transition_speed'] = ! empty( $new_instance['transition_speed'] ) ? 
			absint( $new_instance['transition_speed'] ) : 5000;
		$instance['show_controls'] = isset( $new_instance['show_controls'] ) ? 
			(bool) $new_instance['show_controls'] : false;
		$instance['show_indicators'] = isset( $new_instance['show_indicators'] ) ? 
			(bool) $new_instance['show_indicators'] : false;
		$instance['slider_height'] = ! empty( $new_instance['slider_height'] ) ? 
			absint( $new_instance['slider_height'] ) : 400;
		$instance['pause_on_hover'] = isset( $new_instance['pause_on_hover'] ) ? 
			(bool) $new_instance['pause_on_hover'] : false;
		return $instance;
	}

	/**
	 * Display widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		
		// Get widget settings
		$image_count = ! empty( $instance['image_count'] ) ? absint( $instance['image_count'] ) : 5;
		$image_size = ! empty( $instance['image_size'] ) ? $instance['image_size'] : 'swg_widget';
		$autoplay = isset( $instance['autoplay'] ) ? (bool) $instance['autoplay'] : true;
		$transition_speed = ! empty( $instance['transition_speed'] ) ? absint( $instance['transition_speed'] ) : 5000;
		$show_controls = isset( $instance['show_controls'] ) ? (bool) $instance['show_controls'] : true;
		$show_indicators = isset( $instance['show_indicators'] ) ? (bool) $instance['show_indicators'] : true;
		$slider_height = ! empty( $instance['slider_height'] ) ? absint( $instance['slider_height'] ) : 400;
		$pause_on_hover = isset( $instance['pause_on_hover'] ) ? (bool) $instance['pause_on_hover'] : true;
		
		// Generate unique ID for this widget instance
		$widget_id = 'swg-slider-' . $this->id;
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		
		// Add custom styling for this slider instance
		echo '<style>
			#' . esc_attr( $widget_id ) . ' .carousel-inner,
			#' . esc_attr( $widget_id ) . ' .carousel-item,
			#' . esc_attr( $widget_id ) . ' .carousel-item img {
				max-height: ' . esc_attr( $slider_height ) . 'px;
			}
		</style>';
		
		// Add custom data attributes for JavaScript
		echo '<div id="' . esc_attr( $widget_id ) . '" class="swg-widget-slider" 
			data-autoplay="' . esc_attr( $autoplay ? '1' : '0' ) . '" 
			data-speed="' . esc_attr( $transition_speed ) . '" 
			data-pause-hover="' . esc_attr( $pause_on_hover ? '1' : '0' ) . '" 
			data-controls="' . esc_attr( $show_controls ? '1' : '0' ) . '" 
			data-indicators="' . esc_attr( $show_indicators ? '1' : '0' ) . '" 
			data-count="' . esc_attr( $image_count ) . '">';
		
		echo swg_function( $image_size );
		
		echo '</div>';
		
		echo $args['after_widget'];
	}
}

/**
 * Social Media Links Widget Class.
 */
class SWG_Social_Media_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'swg_social_media_widget',
			__( 'SWG Social Media Links', 'swgtheme' ),
			array( 'description' => __( 'Display social media links from theme options', 'swgtheme' ) )
		);
	}

	/**
	 * Widget form.
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Follow Us', 'swgtheme' );
		$show_labels = isset( $instance['show_labels'] ) ? (bool) $instance['show_labels'] : false;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<input type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( 'show_labels' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_labels' ) ); ?>" 
				<?php checked( $show_labels ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_labels' ) ); ?>">
				<?php esc_html_e( 'Show platform labels', 'swgtheme' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Update widget.
	 *
	 * @param array $new_instance New widget instance.
	 * @param array $old_instance Old widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['show_labels'] = isset( $new_instance['show_labels'] ) ? (bool) $new_instance['show_labels'] : false;
		return $instance;
	}

	/**
	 * Display widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$show_labels = isset( $instance['show_labels'] ) ? (bool) $instance['show_labels'] : false;

		// Get social media URLs from theme options
		$facebook = get_option( 'swgtheme_social_facebook', '' );
		$twitter = get_option( 'swgtheme_social_twitter', '' );
		$discord = get_option( 'swgtheme_social_discord', '' );
		$youtube = get_option( 'swgtheme_social_youtube', '' );
		$instagram = get_option( 'swgtheme_social_instagram', '' );
		$twitch = get_option( 'swgtheme_social_twitch', '' );
		$steam = get_option( 'swgtheme_social_steam', '' );

		// Only display if at least one social link is set
		if ( empty( $facebook ) && empty( $twitter ) && empty( $discord ) && empty( $youtube ) && empty( $instagram ) && empty( $twitch ) && empty( $steam ) ) {
			return;
		}

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		
		echo '<div class="swg-social-media-links">';
		
		if ( ! empty( $facebook ) ) {
			echo '<a href="' . esc_url( $facebook ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-facebook">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Facebook</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $twitter ) ) {
			echo '<a href="' . esc_url( $twitter ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-twitter">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Twitter/X</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $discord ) ) {
			echo '<a href="' . esc_url( $discord ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-discord">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Discord</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $youtube ) ) {
			echo '<a href="' . esc_url( $youtube ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-youtube">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
			if ( $show_labels ) {
				echo '<span>YouTube</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $instagram ) ) {
			echo '<a href="' . esc_url( $instagram ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-instagram">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Instagram</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $twitch ) ) {
			echo '<a href="' . esc_url( $twitch ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-twitch">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Twitch</span>';
			}
			echo '</a>';
		}
		
		if ( ! empty( $steam ) ) {
			echo '<a href="' . esc_url( $steam ) . '" target="_blank" rel="noopener noreferrer" class="swg-social-link swg-social-steam">';
			echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11.979 0C5.678 0 .511 4.86.022 11.037l6.432 2.658c.545-.371 1.203-.59 1.912-.59.063 0 .125.004.188.006l2.861-4.142V8.91c0-2.495 2.028-4.524 4.524-4.524 2.494 0 4.524 2.031 4.524 4.527s-2.03 4.525-4.524 4.525h-.105l-4.076 2.911c0 .052.004.105.004.159 0 1.875-1.515 3.396-3.39 3.396-1.635 0-3.016-1.173-3.331-2.727L.436 15.27C1.862 20.307 6.486 24 11.979 24c6.627 0 11.999-5.373 11.999-12S18.605 0 11.979 0zM7.54 18.21l-1.473-.61c.262.543.714.999 1.314 1.25 1.297.539 2.793-.076 3.332-1.375.263-.63.264-1.319.005-1.949s-.75-1.121-1.377-1.383c-.624-.26-1.29-.249-1.878-.03l1.523.63c.956.4 1.409 1.5 1.009 2.455-.397.957-1.497 1.41-2.454 1.012zm8.6-11.013c0-1.662-1.353-3.015-3.015-3.015-1.665 0-3.015 1.353-3.015 3.015 0 1.665 1.35 3.015 3.015 3.015 1.663 0 3.015-1.35 3.015-3.015zm-5.273-.005c0-1.252 1.013-2.266 2.265-2.266 1.249 0 2.266 1.014 2.266 2.266 0 1.251-1.017 2.265-2.266 2.265-1.253 0-2.265-1.014-2.265-2.265z"/></svg>';
			if ( $show_labels ) {
				echo '<span>Steam</span>';
			}
			echo '</a>';
		}
		
		echo '</div>';
		echo $args['after_widget'];
	}
}

/**
 * Recent Posts Widget
 */
class SWG_Recent_Posts_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'swg_recent_posts',
			__( 'SWG Recent Posts', 'swgtheme' ),
			array( 'description' => __( 'Display recent posts with thumbnails', 'swgtheme' ) )
		);
	}
	
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'swgtheme' );
		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : true;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : true;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
		
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		
		$recent_posts = new WP_Query( array(
			'posts_per_page'      => $number,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		) );
		
		if ( $recent_posts->have_posts() ) {
			echo '<ul class="swg-recent-posts">';
			
			while ( $recent_posts->have_posts() ) {
				$recent_posts->the_post();
				echo '<li class="swg-recent-post-item">';
				
				if ( $show_thumbnail && has_post_thumbnail() ) {
					echo '<div class="swg-recent-post-thumbnail">';
					echo '<a href="' . esc_url( get_permalink() ) . '">';
					the_post_thumbnail( 'thumbnail' );
					echo '</a>';
					echo '</div>';
				}
				
				echo '<div class="swg-recent-post-content">';
				echo '<h4 class="swg-recent-post-title">';
				echo '<a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>';
				echo '</h4>';
				
				if ( $show_date ) {
					echo '<span class="swg-recent-post-date">';
					echo esc_html( get_the_date() );
					echo '</span>';
				}
				
				if ( $show_excerpt ) {
					echo '<div class="swg-recent-post-excerpt">';
					echo wp_trim_words( get_the_excerpt(), 15, '...' );
					echo '</div>';
				}
				echo '</div>';
				
				echo '</li>';
			}
			
			echo '</ul>';
			wp_reset_postdata();
		}
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'swgtheme' );
		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : true;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : true;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<?php esc_html_e( 'Number of posts:', 'swgtheme' ); ?>
			</label>
			<input class="tiny-text" 
				id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" 
				type="number" 
				step="1" 
				min="1" 
				max="10" 
				value="<?php echo esc_attr( $number ); ?>" />
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_thumbnail ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>">
				<?php esc_html_e( 'Display thumbnail', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_date ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>">
				<?php esc_html_e( 'Display date', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_excerpt ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>">
				<?php esc_html_e( 'Display excerpt', 'swgtheme' ); ?>
			</label>
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['number'] = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
		$instance['show_thumbnail'] = ! empty( $new_instance['show_thumbnail'] ) ? 1 : 0;
		$instance['show_date'] = ! empty( $new_instance['show_date'] ) ? 1 : 0;
		$instance['show_excerpt'] = ! empty( $new_instance['show_excerpt'] ) ? 1 : 0;
		return $instance;
	}
}

/**
 * Newsletter Signup Widget
 */
class SWG_Newsletter_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'swg_newsletter',
			__( 'SWG Newsletter Signup', 'swgtheme' ),
			array( 'description' => __( 'Newsletter subscription form', 'swgtheme' ) )
		);
	}
	
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		
		$title = ! empty( $instance['title'] ) ? $instance['title'] : get_option( 'swgtheme_newsletter_title', __( 'Newsletter', 'swgtheme' ) );
		$description = ! empty( $instance['description'] ) ? $instance['description'] : get_option( 'swgtheme_newsletter_description', '' );
		$action = get_option( 'swgtheme_newsletter_action', '' );
		
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		
		if ( ! empty( $description ) ) {
			echo '<p class="swg-newsletter-description">' . esc_html( $description ) . '</p>';
		}
		?>
		<form class="swg-newsletter-form" action="<?php echo esc_url( $action ); ?>" method="post" target="_blank">
			<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'swgtheme' ); ?>" required />
			<button type="submit"><?php esc_html_e( 'Subscribe', 'swgtheme' ); ?></button>
		</form>
		<?php
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$description = ! empty( $instance['description'] ) ? $instance['description'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">
				<?php esc_html_e( 'Description:', 'swgtheme' ); ?>
			</label>
			<textarea class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" 
				rows="3"><?php echo esc_textarea( $description ); ?></textarea>
		</p>
		
		<p><small><?php esc_html_e( 'Configure form action URL in Theme Options.', 'swgtheme' ); ?></small></p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['description'] = ! empty( $new_instance['description'] ) ? sanitize_textarea_field( $new_instance['description'] ) : '';
		return $instance;
	}
}

/**
 * Popular Posts Widget
 */
class SWG_Popular_Posts_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'swg_popular_posts',
			__( 'SWG Popular Posts', 'swgtheme' ),
			array( 'description' => __( 'Display most viewed posts', 'swgtheme' ) )
		);
	}
	
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Popular Posts', 'swgtheme' );
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
		
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		
		$popular_args = array(
			'posts_per_page' => $count,
			'meta_key'       => 'swg_post_views',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		);
		
		$popular = new WP_Query( $popular_args );
		
		if ( $popular->have_posts() ) {
			echo '<ul class="swg-popular-posts-list">';
			while ( $popular->have_posts() ) {
				$popular->the_post();
				echo '<li>';
				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'thumbnail' ) . '</a>';
				}
				echo '<div class="swg-popular-post-content">';
				echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
				echo '<span class="swg-post-views">' . swgtheme_get_post_views( get_the_ID() ) . ' views</span>';
				echo '</div>';
				echo '</li>';
			}
			echo '</ul>';
			wp_reset_postdata();
		}
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of posts:', 'swgtheme' ); ?>
			</label>
			<input class="small-text" 
				id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" 
				type="number" 
				min="1" 
				max="20" 
				value="<?php echo esc_attr( $count ); ?>" />
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['count'] = ! empty( $new_instance['count'] ) ? absint( $new_instance['count'] ) : 5;
		return $instance;
	}
}

/**
 * Author Bio Widget
 */
class SWG_Author_Bio_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'swg_author_bio',
			__( 'SWG Author Bio', 'swgtheme' ),
			array( 'description' => __( 'Display author information', 'swgtheme' ) )
		);
	}
	
	public function widget( $args, $instance ) {
		if ( ! is_single() ) {
			return;
		}
		
		echo $args['before_widget'];
		
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'About the Author', 'swgtheme' );
		
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		
		global $post;
		$author_id = $post->post_author;
		?>
		<div class="swg-author-bio">
			<div class="swg-author-avatar">
				<?php echo get_avatar( $author_id, 80 ); ?>
			</div>
			<div class="swg-author-info">
				<h4><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
				<p><?php echo esc_html( get_the_author_meta( 'description', $author_id ) ); ?></p>
				<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" class="swg-author-link">
					<?php esc_html_e( 'View all posts', 'swgtheme' ); ?>
				</a>
			</div>
		</div>
		<?php
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		return $instance;
	}
}

/**
 * Add Theme Options page to admin menu.
 */
function swgtheme_add_admin_page() {
	// Debug: Log that this function is being called
	error_log('swgtheme_add_admin_page is being called');
	
	$page = add_theme_page(
		__( 'Theme Options', 'swgtheme' ),
		__( 'Theme Options', 'swgtheme' ),
		'manage_options',
		'swgtheme-options',
		'swgtheme_options_page'
	);
	
	// Debug: Log the page hook
	error_log('Theme Options page hook: ' . $page);
	
	add_theme_page(
		__( 'User & Social Features', 'swgtheme' ),
		__( 'User & Social', 'swgtheme' ),
		'manage_options',
		'swgtheme-user-social',
		'swgtheme_user_social_admin_page'
	);
	
	add_theme_page(
		__( 'Security Dashboard', 'swgtheme' ),
		__( '🔒 Security', 'swgtheme' ),
		'manage_options',
		'swgtheme-security',
		'swgtheme_security_dashboard_page'
	);
	
	add_theme_page(
		__( 'Integrations', 'swgtheme' ),
		__( '🔌 Integrations', 'swgtheme' ),
		'manage_options',
		'swgtheme-integrations',
		'swgtheme_integrations_page'
	);
	
	add_theme_page(
		__( 'Performance', 'swgtheme' ),
		__( '⚡ Performance', 'swgtheme' ),
		'manage_options',
		'swgtheme-performance',
		'swgtheme_performance_page'
	);
	
	// Add System Info page (hidden from menu, only accessible from dev tools)
	if ( function_exists( 'SWGTheme_Dev_Tools::is_dev_mode' ) && SWGTheme_Dev_Tools::is_dev_mode() ) {
		add_submenu_page(
			null, // Hidden from menu
			__( 'System Information', 'swgtheme' ),
			__( 'System Info', 'swgtheme' ),
			'manage_options',
			'swg-system-info',
			'swgtheme_system_info_page'
		);
	}
}
add_action( 'admin_menu', 'swgtheme_add_admin_page', 10 );

/**
 * Security Dashboard page callback
 */
function swgtheme_security_dashboard_page() {
	require_once get_template_directory() . '/admin/security-dashboard.php';
}

/**
 * Integrations admin page callback
 */
function swgtheme_integrations_page() {
	require_once get_template_directory() . '/admin/integrations-admin.php';
}

/**
 * Performance admin page callback
 */
function swgtheme_performance_page() {
	require_once get_template_directory() . '/admin/performance-admin.php';
}

/**
 * System Information page callback
 */
function swgtheme_system_info_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		
		<div class="swg-system-info">
			<h2>Server Environment</h2>
			<table class="widefat striped">
				<tbody>
					<tr>
						<th style="width:300px;">PHP Version</th>
						<td><?php echo PHP_VERSION; ?></td>
					</tr>
					<tr>
						<th>WordPress Version</th>
						<td><?php echo get_bloginfo( 'version' ); ?></td>
					</tr>
					<tr>
						<th>Theme Version</th>
						<td><?php echo defined( 'SWGTHEME_VERSION' ) ? SWGTHEME_VERSION : 'Unknown'; ?></td>
					</tr>
					<tr>
						<th>Server Software</th>
						<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ); ?></td>
					</tr>
					<tr>
						<th>PHP Memory Limit</th>
						<td><?php echo ini_get( 'memory_limit' ); ?></td>
					</tr>
					<tr>
						<th>WP Memory Limit</th>
						<td><?php echo WP_MEMORY_LIMIT; ?></td>
					</tr>
					<tr>
						<th>WP Max Memory Limit</th>
						<td><?php echo WP_MAX_MEMORY_LIMIT; ?></td>
					</tr>
					<tr>
						<th>Upload Max Filesize</th>
						<td><?php echo ini_get( 'upload_max_filesize' ); ?></td>
					</tr>
					<tr>
						<th>Post Max Size</th>
						<td><?php echo ini_get( 'post_max_size' ); ?></td>
					</tr>
					<tr>
						<th>Max Execution Time</th>
						<td><?php echo ini_get( 'max_execution_time' ); ?> seconds</td>
					</tr>
					<tr>
						<th>Debug Mode</th>
						<td><?php echo WP_DEBUG ? '<span style="color:green;">✓ Enabled</span>' : '<span style="color:gray;">Disabled</span>'; ?></td>
					</tr>
					<tr>
						<th>Environment</th>
						<td><?php echo class_exists( 'SWGTheme_Dev_Tools' ) && SWGTheme_Dev_Tools::is_local_environment() ? 'Local' : 'Production'; ?></td>
					</tr>
				</tbody>
			</table>
			
			<h2 style="margin-top:30px;">Database</h2>
			<table class="widefat striped">
				<tbody>
					<tr>
						<th style="width:300px;">Database Host</th>
						<td><?php echo DB_HOST; ?></td>
					</tr>
					<tr>
						<th>Database Name</th>
						<td><?php echo DB_NAME; ?></td>
					</tr>
					<tr>
						<th>Database Charset</th>
						<td><?php echo DB_CHARSET; ?></td>
					</tr>
					<tr>
						<th>Table Prefix</th>
						<td><?php global $wpdb; echo $wpdb->prefix; ?></td>
					</tr>
				</tbody>
			</table>
			
			<h2 style="margin-top:30px;">Theme Features</h2>
			<table class="widefat striped">
				<tbody>
					<tr>
						<th style="width:300px;">Dark Mode</th>
						<td><?php echo get_option( 'swgtheme_enable_dark_mode', '1' ) === '1' ? '✓ Enabled' : 'Disabled'; ?></td>
					</tr>
					<tr>
						<th>Slider</th>
						<td><?php echo get_option( 'swgtheme_slider_enabled', '0' ) === '1' ? '✓ Enabled' : 'Disabled'; ?></td>
					</tr>
					<tr>
						<th>Global Color Scheme</th>
						<td><?php echo get_option( 'swgtheme_use_global_color', '0' ) === '1' ? '✓ Enabled' : 'Disabled'; ?></td>
					</tr>
				</tbody>
			</table>
			
			<p style="margin-top:30px;">
				<a href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=swg_clear_cache' ), 'swg_clear_cache' ); ?>" class="button button-secondary">Clear All Caches</a>
			</p>
		</div>
	</div>
	<?php
}

/**
 * Register theme settings.
 */
function swgtheme_register_settings() {
	// Settings args with capability
	$args = array(
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
	);
	
	register_setting( 'swgtheme_options_group', 'swgtheme_logo_text', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_footer_text', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_enabled', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_count', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_use_global_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_primary_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_button_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_border_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_link_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_text_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_facebook', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_twitter', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_discord', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_youtube', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_instagram', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_twitch', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_steam', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_dark_mode', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_auto', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_auto_start', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_auto_end', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_toggle_position', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_transition_speed', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_system_preference', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_default', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dark_mode_background_image', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_preloader', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_style', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_bg_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_spinner_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_text', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_logo', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_speed', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_preloader_fade_duration', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_back_to_top', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_heading_font', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_body_font', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_heading_font_size', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_body_font_size', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_letter_spacing', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_line_height', $args );
	
	// Background Options
	register_setting( 'swgtheme_options_group', 'swgtheme_background_image', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_background_position', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_background_size', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_parallax', $args );
	
	// Slider Autoplay Options
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_autoplay', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_speed', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_pause_hover', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_slider_loop', $args );
	
	// Admin Backend Settings
	register_setting( 'swgtheme_options_group', 'swgtheme_login_logo', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_logo_width', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_logo_height', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_bg_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_bg_image', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_button_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_admin_primary_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_admin_accent_color', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_hide_wp_logo', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_hide_admin_bar_front', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dashboard_welcome_title', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_dashboard_welcome_text', $args );
	
	// Custom CSS
	register_setting( 'swgtheme_options_group', 'swgtheme_custom_css', $args );
	
	// Mobile Menu
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_mobile_menu', $args );
	
	// SEO Options
	register_setting( 'swgtheme_options_group', 'swgtheme_meta_description', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_meta_keywords', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_og_image', $args );
	
	// Notification Bar
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_notification', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_notification_text', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_notification_type', $args );
	
	// Analytics
	register_setting( 'swgtheme_options_group', 'swgtheme_google_analytics', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_facebook_pixel', $args );
	
	// Animation Controls
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_animations', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_animation_speed', $args );
	
	// Social Sharing
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_social_share', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_social_share_platforms', $args );
	
	// Reading Progress Bar
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_reading_progress', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_progress_bar_color', $args );
	
	// Related Posts
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_related_posts', $args );
	register_setting( 'swgtheme_options_group', 'swgtheme_related_posts_count', $args );
	
	// Breadcrumbs
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_breadcrumbs' );
	
	// Developer Mode
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_developer_mode' );
	
	// Table of Contents
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_toc' );
	register_setting( 'swgtheme_options_group', 'swgtheme_toc_min_headings' );
	
	// Cookie Consent
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_cookies' );
	register_setting( 'swgtheme_options_group', 'swgtheme_cookie_message' );
	register_setting( 'swgtheme_options_group', 'swgtheme_cookie_button_text' );
	
	// Maintenance Mode
	register_setting( 'swgtheme_options_group', 'swgtheme_maintenance_mode' );
	register_setting( 'swgtheme_options_group', 'swgtheme_maintenance_title' );
	register_setting( 'swgtheme_options_group', 'swgtheme_maintenance_message' );
	
	// Custom Login
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_custom_login' );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_logo' );
	register_setting( 'swgtheme_options_group', 'swgtheme_login_background' );
	
	// Footer Editor
	register_setting( 'swgtheme_options_group', 'swgtheme_footer_copyright' );
	register_setting( 'swgtheme_options_group', 'swgtheme_footer_links' );
	
	// Sticky Header
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_sticky_header' );
	
	// Custom 404
	register_setting( 'swgtheme_options_group', 'swgtheme_404_title' );
	register_setting( 'swgtheme_options_group', 'swgtheme_404_message' );
	register_setting( 'swgtheme_options_group', 'swgtheme_404_button_text' );
	
	// Lazy Loading
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_lazy_load' );
	
	// AJAX Search
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_ajax_search' );
	
	// Newsletter
	register_setting( 'swgtheme_options_group', 'swgtheme_newsletter_title' );
	register_setting( 'swgtheme_options_group', 'swgtheme_newsletter_description' );
	register_setting( 'swgtheme_options_group', 'swgtheme_newsletter_action' );
	
	// Performance
	register_setting( 'swgtheme_options_group', 'swgtheme_disable_emojis' );
	register_setting( 'swgtheme_options_group', 'swgtheme_disable_embeds' );
	register_setting( 'swgtheme_options_group', 'swgtheme_remove_query_strings' );
	
	// Schema Markup
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_schema' );
	register_setting( 'swgtheme_options_group', 'swgtheme_organization_name' );
	register_setting( 'swgtheme_options_group', 'swgtheme_organization_logo' );
	
	// Post Views & Reading Time
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_post_views' );
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_reading_time' );
	register_setting( 'swgtheme_options_group', 'swgtheme_reading_speed' ); // words per minute
	
	// Code Syntax Highlighting
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_syntax_highlighting' );
	register_setting( 'swgtheme_options_group', 'swgtheme_prism_theme' );
	
	// Infinite Scroll
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_infinite_scroll' );
	
	// Back to Top Customization
	register_setting( 'swgtheme_options_group', 'swgtheme_back_to_top_position' );
	register_setting( 'swgtheme_options_group', 'swgtheme_back_to_top_icon' );
	
	// Video Background
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_video_bg' );
	register_setting( 'swgtheme_options_group', 'swgtheme_video_bg_url' );
	register_setting( 'swgtheme_options_group', 'swgtheme_video_bg_poster' );
	
	// Comment Ratings
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_comment_ratings' );
	
	// Security Headers
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_security_headers' );
	
	// XML Sitemap
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_sitemap' );
	
	// Database Optimization
	register_setting( 'swgtheme_options_group', 'swgtheme_auto_cleanup' );
	register_setting( 'swgtheme_options_group', 'swgtheme_cleanup_frequency' );
	
	// CDN Integration
	register_setting( 'swgtheme_options_group', 'swgtheme_enable_cdn' );
	register_setting( 'swgtheme_options_group', 'swgtheme_cdn_url' );
	
	// Admin Branding
	register_setting( 'swgtheme_options_group', 'swgtheme_admin_logo' );
	register_setting( 'swgtheme_options_group', 'swgtheme_admin_footer_text' );
	
	// Column Background Colors
	register_setting( 'swgtheme_options_group', 'swgtheme_col_lg_3_bg_color' );
	register_setting( 'swgtheme_options_group', 'swgtheme_col_lg_9_bg_color' );
}
add_action( 'admin_init', 'swgtheme_register_settings' );

// Output custom CSS
function swgtheme_custom_css_output() {
	$custom_css = get_option( 'swgtheme_custom_css', '' );
	if ( ! empty( $custom_css ) ) {
		echo '<style type="text/css" id="swgtheme-custom-css">';
		echo wp_strip_all_tags( $custom_css );
		echo '</style>';
	}
}
add_action( 'wp_head', 'swgtheme_custom_css_output', 999 );

// Output SEO meta tags
function swgtheme_seo_meta_tags() {
	$description = get_option( 'swgtheme_meta_description', '' );
	$keywords = get_option( 'swgtheme_meta_keywords', '' );
	$og_image = get_option( 'swgtheme_og_image', '' );
	
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">';
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">';
	}
	
	if ( ! empty( $keywords ) ) {
		echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">';
	}
	
	if ( ! empty( $og_image ) ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">';
	}
	
	echo '<meta property="og:title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	echo '<meta property="og:type" content="website">';
}
add_action( 'wp_head', 'swgtheme_seo_meta_tags' );

// Output analytics codes
function swgtheme_analytics_codes() {
	$ga_code = get_option( 'swgtheme_google_analytics', '' );
	$fb_pixel = get_option( 'swgtheme_facebook_pixel', '' );
	
	if ( ! empty( $ga_code ) ) {
		echo "<!-- Google Analytics -->\n";
		echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $ga_code ) . "'></script>\n";
		echo "<script>\n";
		echo "window.dataLayer = window.dataLayer || [];\n";
		echo "function gtag(){dataLayer.push(arguments);}\n";
		echo "gtag('js', new Date());\n";
		echo "gtag('config', '" . esc_attr( $ga_code ) . "');\n";
		echo "</script>\n";
	}
	
	if ( ! empty( $fb_pixel ) ) {
		echo "<!-- Facebook Pixel -->\n";
		echo "<script>\n";
		echo "!function(f,b,e,v,n,t,s)\n";
		echo "{if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n";
		echo "n.callMethod.apply(n,arguments):n.queue.push(arguments)};\n";
		echo "if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\n";
		echo "n.queue=[];t=b.createElement(e);t.async=!0;\n";
		echo "t.src=v;s=b.getElementsByTagName(e)[0];\n";
		echo "s.parentNode.insertBefore(t,s)}(window,document,'script',\n";
		echo "'https://connect.facebook.net/en_US/fbevents.js');\n";
		echo "fbq('init', '" . esc_attr( $fb_pixel ) . "');\n";
		echo "fbq('track', 'PageView');\n";
		echo "</script>\n";
	}
}
add_action( 'wp_head', 'swgtheme_analytics_codes' );

// Reading Progress Bar
function swgtheme_reading_progress_bar() {
	if ( is_single() && get_option( 'swgtheme_enable_reading_progress', '0' ) === '1' ) {
		$color = get_option( 'swgtheme_progress_bar_color', '#dc3545' );
		echo '<div class="swg-reading-progress" style="background-color: ' . esc_attr( $color ) . ';"></div>';
	}
}
add_action( 'wp_body_open', 'swgtheme_reading_progress_bar', 5 );

// Breadcrumbs Function
function swgtheme_breadcrumbs() {
	if ( get_option( 'swgtheme_enable_breadcrumbs', '0' ) !== '1' || is_front_page() ) {
		return;
	}
	
	echo '<nav class="swg-breadcrumbs" aria-label="Breadcrumb">';
	echo '<ol>';
	echo '<li><a href="' . home_url() . '">' . __( 'Home', 'swgtheme' ) . '</a></li>';
	
	if ( is_category() || is_single() ) {
		$category = get_the_category();
		if ( ! empty( $category ) ) {
			echo '<li><a href="' . get_category_link( $category[0]->term_id ) . '">' . esc_html( $category[0]->name ) . '</a></li>';
		}
		if ( is_single() ) {
			echo '<li>' . get_the_title() . '</li>';
		}
	} elseif ( is_page() ) {
		echo '<li>' . get_the_title() . '</li>';
	} elseif ( is_search() ) {
		echo '<li>' . __( 'Search Results', 'swgtheme' ) . '</li>';
	} elseif ( is_404() ) {
		echo '<li>' . __( '404 Not Found', 'swgtheme' ) . '</li>';
	}
	
	echo '</ol>';
	echo '</nav>';
}

// Related Posts
function swgtheme_related_posts() {
	if ( ! is_single() || get_option( 'swgtheme_enable_related_posts', '0' ) !== '1' ) {
		return;
	}
	
	global $post;
	$count = absint( get_option( 'swgtheme_related_posts_count', '3' ) );
	$categories = wp_get_post_categories( $post->ID );
	
	if ( empty( $categories ) ) {
		return;
	}
	
	$args = array(
		'category__in'   => $categories,
		'post__not_in'   => array( $post->ID ),
		'posts_per_page' => $count,
		'orderby'        => 'rand',
	);
	
	$related = new WP_Query( $args );
	
	if ( $related->have_posts() ) {
		echo '<div class="swg-related-posts">';
		echo '<h3>' . __( 'Related Posts', 'swgtheme' ) . '</h3>';
		echo '<div class="swg-related-posts-grid">';
		
		while ( $related->have_posts() ) {
			$related->the_post();
			echo '<div class="swg-related-post">';
			if ( has_post_thumbnail() ) {
				echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a>';
			}
			echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
			echo '<div class="swg-related-meta">' . get_the_date() . '</div>';
			echo '</div>';
		}
		
		echo '</div>';
		echo '</div>';
		wp_reset_postdata();
	}
}
add_action( 'the_content', 'swgtheme_related_posts_filter' );
function swgtheme_related_posts_filter( $content ) {
	if ( is_single() ) {
		ob_start();
		swgtheme_related_posts();
		$related = ob_get_clean();
		return $content . $related;
	}
	return $content;
}

// Schema Markup
function swgtheme_schema_markup() {
	if ( get_option( 'swgtheme_enable_schema', '0' ) !== '1' ) {
		return;
	}
	
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url(),
	);
	
	$org_name = get_option( 'swgtheme_organization_name', '' );
	$org_logo = get_option( 'swgtheme_organization_logo', '' );
	
	if ( ! empty( $org_name ) ) {
		$schema['publisher'] = array(
			'@type' => 'Organization',
			'name'  => $org_name,
		);
		
		if ( ! empty( $org_logo ) ) {
			$schema['publisher']['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $org_logo,
			);
		}
	}
	
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}
add_action( 'wp_head', 'swgtheme_schema_markup' );

// Social Share Buttons
function swgtheme_social_share_buttons() {
	if ( ! is_single() || get_option( 'swgtheme_enable_social_share', '0' ) !== '1' ) {
		return;
	}
	
	$platforms = get_option( 'swgtheme_social_share_platforms', 'facebook,twitter,linkedin' );
	$platforms_array = explode( ',', $platforms );
	
	$url = urlencode( get_permalink() );
	$title = urlencode( get_the_title() );
	
	echo '<div class="swg-social-share">';
	echo '<span class="swg-share-label">' . __( 'Share:', 'swgtheme' ) . '</span>';
	
	foreach ( $platforms_array as $platform ) {
		$platform = trim( $platform );
		switch ( $platform ) {
			case 'facebook':
				echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank" rel="noopener" class="swg-share-btn swg-share-facebook" aria-label="Share on Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>';
				break;
			case 'twitter':
				echo '<a href="https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '" target="_blank" rel="noopener" class="swg-share-btn swg-share-twitter" aria-label="Share on Twitter"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>';
				break;
			case 'linkedin':
				echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '" target="_blank" rel="noopener" class="swg-share-btn swg-share-linkedin" aria-label="Share on LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>';
				break;
			case 'pinterest':
				echo '<a href="https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title . '" target="_blank" rel="noopener" class="swg-share-btn swg-share-pinterest" aria-label="Share on Pinterest"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg></a>';
				break;
		}
	}
	
	echo '</div>';
}

// Cookie Consent Banner
function swgtheme_cookie_consent() {
	if ( get_option( 'swgtheme_enable_cookies', '0' ) !== '1' ) {
		return;
	}
	
	$message = get_option( 'swgtheme_cookie_message', 'We use cookies to ensure you get the best experience on our website.' );
	$button_text = get_option( 'swgtheme_cookie_button_text', 'Got it!' );
	?>
	<div class="swg-cookie-consent" id="swgCookieConsent" style="display:none;">
		<div class="swg-cookie-content">
			<p><?php echo esc_html( $message ); ?></p>
			<button class="swg-cookie-accept"><?php echo esc_html( $button_text ); ?></button>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'swgtheme_cookie_consent' );

// Maintenance Mode Check
function swgtheme_maintenance_mode() {
	if ( get_option( 'swgtheme_maintenance_mode', '0' ) === '1' && ! current_user_can( 'manage_options' ) ) {
		$title = get_option( 'swgtheme_maintenance_title', 'Website Under Maintenance' );
		$message = get_option( 'swgtheme_maintenance_message', 'We are currently performing maintenance. Please check back soon!' );
		
		wp_die(
			'<h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $message ) . '</p>',
			esc_html( $title ),
			array( 'response' => 503 )
		);
	}
}
add_action( 'get_header', 'swgtheme_maintenance_mode' );

// Custom Login Page
function swgtheme_custom_login_logo() {
	if ( get_option( 'swgtheme_enable_custom_login', '0' ) !== '1' ) {
		return;
	}
	
	$logo = get_option( 'swgtheme_login_logo', '' );
	$bg = get_option( 'swgtheme_login_background', '' );
	?>
	<style type="text/css">
		#login h1 a, .login h1 a {
			<?php if ( ! empty( $logo ) ) : ?>
			background-image: url('<?php echo esc_url( $logo ); ?>');
			background-size: contain;
			width: 100%;
			height: 100px;
			<?php endif; ?>
		}
		<?php if ( ! empty( $bg ) ) : ?>
		body.login {
			background: url('<?php echo esc_url( $bg ); ?>') no-repeat center center fixed;
			background-size: cover;
		}
		<?php endif; ?>
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'swgtheme_custom_login_logo' );

// Performance Optimizations
if ( get_option( 'swgtheme_disable_emojis', '0' ) === '1' ) {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

if ( get_option( 'swgtheme_disable_embeds', '0' ) === '1' ) {
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}

if ( get_option( 'swgtheme_remove_query_strings', '0' ) === '1' ) {
	function swgtheme_remove_query_strings( $src ) {
		if ( strpos( $src, '?ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}
	add_filter( 'script_loader_src', 'swgtheme_remove_query_strings', 15 );
	add_filter( 'style_loader_src', 'swgtheme_remove_query_strings', 15 );
}

// AJAX Search Handler
function swgtheme_ajax_search() {
	check_ajax_referer( 'swgtheme_ajax_nonce', 'nonce' );
	
	$query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';
	
	if ( empty( $query ) ) {
		wp_send_json_error( 'No search query provided.' );
	}
	
	$args = array(
		's'              => $query,
		'posts_per_page' => 5,
		'post_status'    => 'publish',
	);
	
	$search_query = new WP_Query( $args );
	
	if ( $search_query->have_posts() ) {
		$output = '';
		while ( $search_query->have_posts() ) {
			$search_query->the_post();
			$output .= '<div class="search-result-item">';
			$output .= '<div class="search-result-title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></div>';
			$output .= '<p class="search-result-excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), 15 ) ) . '</p>';
			$output .= '</div>';
		}
		wp_reset_postdata();
		wp_send_json_success( $output );
	} else {
		wp_send_json_success( '<div class="search-result-item"><p>' . esc_html__( 'No results found.', 'swgtheme' ) . '</p></div>' );
	}
}
add_action( 'wp_ajax_swg_ajax_search', 'swgtheme_ajax_search' );
add_action( 'wp_ajax_nopriv_swg_ajax_search', 'swgtheme_ajax_search' );

// Lazy Load Images
if ( get_option( 'swgtheme_enable_lazy_load', '0' ) === '1' ) {
	function swgtheme_add_lazy_load( $content ) {
		if ( is_feed() || is_preview() ) {
			return $content;
		}
		
		$content = preg_replace_callback(
			'/<img([^>]+?)src=[\'"](.*?)[\'"]([^>]*?)>/i',
			function( $matches ) {
				$img_tag = $matches[0];
				$src = $matches[2];
				
				// Skip if already has loading attribute or data-src
				if ( strpos( $img_tag, 'loading=' ) !== false || strpos( $img_tag, 'data-src=' ) !== false ) {
					return $img_tag;
				}
				
				// Replace src with data-src and add lazy class
				$lazy_img = str_replace( '<img', '<img class="swg-lazy"', $img_tag );
				$lazy_img = str_replace( 'src="' . $src . '"', 'data-src="' . $src . '" src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E"', $lazy_img );
				
				return $lazy_img;
			},
			$content
		);
		
		return $content;
	}
	add_filter( 'the_content', 'swgtheme_add_lazy_load', 99 );
}

/**
 * Security Headers
 */
if ( get_option( 'swgtheme_enable_security_headers', '0' ) === '1' ) {
	function swgtheme_add_security_headers() {
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	}
	add_action( 'send_headers', 'swgtheme_add_security_headers' );
}

/**
 * XML Sitemap Generation
 */
function swgtheme_generate_sitemap() {
	if ( get_option( 'swgtheme_enable_sitemap', '0' ) !== '1' ) {
		return;
	}
	
	$posts = get_posts( array(
		'numberposts' => -1,
		'post_type'   => array( 'post', 'page', 'portfolio', 'testimonial', 'team', 'faq' ),
		'post_status' => 'publish',
	) );
	
	$sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
	$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	
	// Homepage
	$sitemap .= '<url>';
	$sitemap .= '<loc>' . esc_url( home_url( '/' ) ) . '</loc>';
	$sitemap .= '<changefreq>daily</changefreq>';
	$sitemap .= '<priority>1.0</priority>';
	$sitemap .= '</url>';
	
	foreach ( $posts as $post ) {
		$sitemap .= '<url>';
		$sitemap .= '<loc>' . esc_url( get_permalink( $post->ID ) ) . '</loc>';
		$sitemap .= '<lastmod>' . esc_html( get_the_modified_date( 'c', $post->ID ) ) . '</lastmod>';
		$sitemap .= '<changefreq>weekly</changefreq>';
		$sitemap .= '<priority>0.8</priority>';
		$sitemap .= '</url>';
	}
	
	$sitemap .= '</urlset>';
	
	file_put_contents( ABSPATH . 'sitemap.xml', $sitemap );
}
add_action( 'save_post', 'swgtheme_generate_sitemap' );

/**
 * Database Cleanup
 */
function swgtheme_database_cleanup() {
	if ( get_option( 'swgtheme_auto_cleanup', '0' ) !== '1' ) {
		return;
	}
	
	global $wpdb;
	
	// Delete post revisions
	$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'revision'" );
	
	// Delete auto-drafts
	$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'" );
	
	// Delete orphaned post meta
	$wpdb->query( "DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL" );
	
	// Delete spam and trashed comments
	$wpdb->query( "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam' OR comment_approved = 'trash'" );
	
	// Delete orphaned comment meta
	$wpdb->query( "DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)" );
	
	// Delete expired transients
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%_transient_%'" );
	
	// Optimize database tables
	$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
	foreach ( $tables as $table ) {
		$wpdb->query( "OPTIMIZE TABLE {$table[0]}" );
	}
}

// Schedule cleanup based on frequency
$cleanup_frequency = get_option( 'swgtheme_cleanup_frequency', 'weekly' );
if ( ! wp_next_scheduled( 'swgtheme_cleanup_hook' ) ) {
	wp_schedule_event( time(), $cleanup_frequency, 'swgtheme_cleanup_hook' );
}
add_action( 'swgtheme_cleanup_hook', 'swgtheme_database_cleanup' );

/**
 * CDN Integration
 */
if ( get_option( 'swgtheme_enable_cdn', '0' ) === '1' ) {
	function swgtheme_cdn_rewrite( $url ) {
		$cdn_url = get_option( 'swgtheme_cdn_url', '' );
		if ( ! empty( $cdn_url ) ) {
			$site_url = site_url();
			$url = str_replace( $site_url, rtrim( $cdn_url, '/' ), $url );
		}
		return $url;
	}
	add_filter( 'wp_get_attachment_url', 'swgtheme_cdn_rewrite' );
	add_filter( 'stylesheet_uri', 'swgtheme_cdn_rewrite' );
	add_filter( 'script_loader_src', 'swgtheme_cdn_rewrite' );
	add_filter( 'style_loader_src', 'swgtheme_cdn_rewrite' );
}

/**
 * Custom Admin Branding
 */
function swgtheme_custom_admin_logo() {
	$admin_logo = get_option( 'swgtheme_admin_logo', '' );
	if ( ! empty( $admin_logo ) ) {
		echo '<style type="text/css">
			#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
				background-image: url(' . esc_url( $admin_logo ) . ') !important;
				background-size: contain;
				content: "" !important;
			}
		</style>';
	}
}
add_action( 'admin_head', 'swgtheme_custom_admin_logo' );

function swgtheme_custom_admin_footer() {
	$footer_text = get_option( 'swgtheme_admin_footer_text', '' );
	if ( ! empty( $footer_text ) ) {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'swgtheme_custom_admin_footer' );

/**
 * Prism.js Syntax Highlighting
 */
function swgtheme_enqueue_prism() {
	if ( get_option( 'swgtheme_enable_syntax_highlighting', '0' ) === '1' ) {
		$theme = get_option( 'swgtheme_prism_theme', 'default' );
		wp_enqueue_style( 'prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism' . ( $theme !== 'default' ? '-' . $theme : '' ) . '.min.css', array(), '1.29.0' );
		wp_enqueue_script( 'prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js', array(), '1.29.0', true );
		wp_enqueue_script( 'prism-autoloader', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js', array( 'prism' ), '1.29.0', true );
	}
}
add_action( 'wp_enqueue_scripts', 'swgtheme_enqueue_prism' );

/**
 * Comment Ratings
 */
function swgtheme_comment_rating_field() {
	if ( get_option( 'swgtheme_enable_comment_ratings', '0' ) !== '1' ) {
		return;
	}
	?>
	<p class="comment-form-rating">
		<label for="rating"><?php esc_html_e( 'Your Rating', 'swgtheme' ); ?></label>
		<select name="rating" id="rating">
			<option value=""><?php esc_html_e( 'Rate...', 'swgtheme' ); ?></option>
			<option value="5"><?php esc_html_e( '5 Stars', 'swgtheme' ); ?></option>
			<option value="4"><?php esc_html_e( '4 Stars', 'swgtheme' ); ?></option>
			<option value="3"><?php esc_html_e( '3 Stars', 'swgtheme' ); ?></option>
			<option value="2"><?php esc_html_e( '2 Stars', 'swgtheme' ); ?></option>
			<option value="1"><?php esc_html_e( '1 Star', 'swgtheme' ); ?></option>
		</select>
	</p>
	<?php
}
add_action( 'comment_form_logged_in_after', 'swgtheme_comment_rating_field' );
add_action( 'comment_form_after_fields', 'swgtheme_comment_rating_field' );

function swgtheme_save_comment_rating( $comment_id ) {
	if ( isset( $_POST['rating'] ) && '' !== $_POST['rating'] ) {
		$rating = intval( $_POST['rating'] );
		add_comment_meta( $comment_id, 'rating', $rating );
	}
}
add_action( 'comment_post', 'swgtheme_save_comment_rating' );

function swgtheme_display_comment_rating( $comment_text ) {
	if ( get_option( 'swgtheme_enable_comment_ratings', '0' ) !== '1' ) {
		return $comment_text;
	}
	
	$rating = get_comment_meta( get_comment_ID(), 'rating', true );
	if ( $rating ) {
		$stars = str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
		$comment_text = '<div class="comment-rating">' . esc_html( $stars ) . '</div>' . $comment_text;
	}
	return $comment_text;
}
add_filter( 'comment_text', 'swgtheme_display_comment_rating' );

/**
 * Canonical URLs
 */
function swgtheme_add_canonical() {
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'swgtheme_add_canonical', 1 );

/**
 * Admin Dashboard Widgets
 */
function swgtheme_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'swgtheme_stats',
		__( 'Theme Statistics', 'swgtheme' ),
		'swgtheme_dashboard_stats_widget'
	);
}
add_action( 'wp_dashboard_setup', 'swgtheme_add_dashboard_widgets' );

function swgtheme_dashboard_stats_widget() {
	$post_count = wp_count_posts()->publish;
	$page_count = wp_count_posts( 'page' )->publish;
	$comment_count = wp_count_comments()->approved;
	
	echo '<ul>';
	echo '<li><strong>' . esc_html__( 'Published Posts:', 'swgtheme' ) . '</strong> ' . esc_html( $post_count ) . '</li>';
	echo '<li><strong>' . esc_html__( 'Published Pages:', 'swgtheme' ) . '</strong> ' . esc_html( $page_count ) . '</li>';
	echo '<li><strong>' . esc_html__( 'Approved Comments:', 'swgtheme' ) . '</strong> ' . esc_html( $comment_count ) . '</li>';
	echo '<li><strong>' . esc_html__( 'Theme Version:', 'swgtheme' ) . '</strong> ' . SWGTHEME_VERSION . '</li>';
	echo '</ul>';
}

/**
 * Theme Options page callback.
 */
function swgtheme_options_page() {
	// Debug: Log that callback is being called
	error_log('swgtheme_options_page callback is being called');
	error_log('Current user can manage_options: ' . (current_user_can('manage_options') ? 'YES' : 'NO'));
	
	if ( ! current_user_can( 'manage_options' ) ) {
		error_log('Permission denied - user lacks manage_options capability');
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'swgtheme' ) );
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		
		<!-- Tabs Navigation -->
		<h2 class="nav-tab-wrapper">
			<a href="#general" class="nav-tab nav-tab-active">General</a>
			<a href="#social" class="nav-tab">Social Media</a>
			<a href="#typography" class="nav-tab">Typography</a>
			<a href="#colors" class="nav-tab">Colors</a>
			<a href="#features" class="nav-tab">Features</a>
			<a href="#advanced" class="nav-tab">Advanced</a>
		</h2>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_options_group' ); ?>
			<?php do_settings_sections( 'swgtheme_options_group' ); ?>
			
			<!-- Tab: General -->
			<div class="tab-content active" id="general-tab">
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_logo_text">
								<?php esc_html_e( 'Custom Logo Text', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="swgtheme_logo_text" 
								name="swgtheme_logo_text" 
								value="<?php echo esc_attr( get_option( 'swgtheme_logo_text', '' ) ); ?>" 
								class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Enter custom text to display with your logo (optional).', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_footer_text">
								<?php esc_html_e( 'Footer Copyright Text', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<textarea id="swgtheme_footer_text" 
								name="swgtheme_footer_text" 
								rows="3" 
								class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_footer_text', '&copy; Lords of the Outer Rim. All rights reserved.' ) ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Enter your footer copyright text. HTML is allowed.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_slider_enabled">
								<?php esc_html_e( 'Enable SWG Slider', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" 
								id="swgtheme_slider_enabled" 
								name="swgtheme_slider_enabled" 
								value="1" 
								<?php checked( get_option( 'swgtheme_slider_enabled', '1' ), '1' ); ?> />
							<label for="swgtheme_slider_enabled">
								<?php esc_html_e( 'Show the SWG image slider on the homepage', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_slider_count">
								<?php esc_html_e( 'Number of Slider Images', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_slider_count" 
								name="swgtheme_slider_count" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_count', '5' ) ); ?>" 
								min="1" 
								max="20" 
								class="small-text" />
							<p class="description">
								<?php esc_html_e( 'How many images to display in the slider (1-20).', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			
			<!-- Tab: Social Media -->
			<div class="tab-content" id="social-tab" style="display: none;">
			<h2><?php esc_html_e( 'Social Media Links', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_social_facebook">
								<?php esc_html_e( 'Facebook URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_facebook" 
								name="swgtheme_social_facebook" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_facebook', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://facebook.com/yourpage" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_twitter">
								<?php esc_html_e( 'Twitter/X URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_twitter" 
								name="swgtheme_social_twitter" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_twitter', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://twitter.com/youraccount" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_discord">
								<?php esc_html_e( 'Discord Invite URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_discord" 
								name="swgtheme_social_discord" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_discord', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://discord.gg/yourserver" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_youtube">
								<?php esc_html_e( 'YouTube URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_youtube" 
								name="swgtheme_social_youtube" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_youtube', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://youtube.com/c/yourchannel" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_instagram">
								<?php esc_html_e( 'Instagram URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_instagram" 
								name="swgtheme_social_instagram" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_instagram', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://instagram.com/youraccount" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_twitch">
								<?php esc_html_e( 'Twitch URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_twitch" 
								name="swgtheme_social_twitch" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_twitch', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://twitch.tv/yourchannel" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_social_steam">
								<?php esc_html_e( 'Steam URL', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_social_steam" 
								name="swgtheme_social_steam" 
								value="<?php echo esc_url( get_option( 'swgtheme_social_steam', '' ) ); ?>" 
								class="regular-text" 
								placeholder="https://steamcommunity.com/groups/yourgroup" />
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			
			<!-- Tab: Typography -->
			<div class="tab-content" id="typography-tab" style="display: none;">
			<h2><?php esc_html_e( 'Typography', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_heading_font">
								<?php esc_html_e( 'Heading Font', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_heading_font" name="swgtheme_heading_font">
								<option value="Default" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Default' ); ?>>Default</option>
								<option value="Roboto" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Roboto' ); ?>>Roboto</option>
								<option value="Open Sans" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Open Sans' ); ?>>Open Sans</option>
								<option value="Lato" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Lato' ); ?>>Lato</option>
								<option value="Montserrat" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Montserrat' ); ?>>Montserrat</option>
								<option value="Raleway" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Raleway' ); ?>>Raleway</option>
								<option value="Oswald" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Oswald' ); ?>>Oswald</option>
								<option value="Playfair Display" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Playfair Display' ); ?>>Playfair Display</option>
								<option value="Poppins" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Poppins' ); ?>>Poppins</option>
								<option value="Merriweather" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Merriweather' ); ?>>Merriweather</option>
								<option value="Ubuntu" <?php selected( get_option( 'swgtheme_heading_font', 'Roboto' ), 'Ubuntu' ); ?>>Ubuntu</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Font family for headings (h1-h6)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_body_font">
								<?php esc_html_e( 'Body Font', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_body_font" name="swgtheme_body_font">
								<option value="Default" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Default' ); ?>>Default</option>
								<option value="Open Sans" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Open Sans' ); ?>>Open Sans</option>
								<option value="Roboto" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Roboto' ); ?>>Roboto</option>
								<option value="Lato" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Lato' ); ?>>Lato</option>
								<option value="Montserrat" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Montserrat' ); ?>>Montserrat</option>
								<option value="Raleway" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Raleway' ); ?>>Raleway</option>
								<option value="PT Sans" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'PT Sans' ); ?>>PT Sans</option>
								<option value="Source Sans Pro" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Source Sans Pro' ); ?>>Source Sans Pro</option>
								<option value="Nunito" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Nunito' ); ?>>Nunito</option>
								<option value="Ubuntu" <?php selected( get_option( 'swgtheme_body_font', 'Open Sans' ), 'Ubuntu' ); ?>>Ubuntu</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Font family for body text and paragraphs', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_heading_font_size">
								<?php esc_html_e( 'Heading Font Size', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_heading_font_size" 
								name="swgtheme_heading_font_size" 
								value="<?php echo esc_attr( get_option( 'swgtheme_heading_font_size', '32' ) ); ?>" 
								min="16" 
								max="72" 
								step="1" /> px
							<p class="description">
								<?php esc_html_e( 'Base font size for h1 headings (16-72px)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_body_font_size">
								<?php esc_html_e( 'Body Font Size', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_body_font_size" 
								name="swgtheme_body_font_size" 
								value="<?php echo esc_attr( get_option( 'swgtheme_body_font_size', '16' ) ); ?>" 
								min="12" 
								max="24" 
								step="1" /> px
							<p class="description">
								<?php esc_html_e( 'Font size for body text (12-24px)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_letter_spacing">
								<?php esc_html_e( 'Letter Spacing', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_letter_spacing" 
								name="swgtheme_letter_spacing" 
								value="<?php echo esc_attr( get_option( 'swgtheme_letter_spacing', '0' ) ); ?>" 
								min="-2" 
								max="5" 
								step="0.1" /> px
							<p class="description">
								<?php esc_html_e( 'Space between letters (-2 to 5px)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_line_height">
								<?php esc_html_e( 'Line Height', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_line_height" 
								name="swgtheme_line_height" 
								value="<?php echo esc_attr( get_option( 'swgtheme_line_height', '1.6' ) ); ?>" 
								min="1" 
								max="3" 
								step="0.1" />
							<p class="description">
								<?php esc_html_e( 'Line height multiplier (1-3)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			
			<!-- Tab: Colors -->
			<div class="tab-content" id="colors-tab" style="display: none;">
			<h2><?php esc_html_e( 'Theme Colors', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
						<label for="swgtheme_use_global_color">
							<?php esc_html_e( 'Use Global Color', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="checkbox" 
							id="swgtheme_use_global_color" 
							name="swgtheme_use_global_color" 
							value="1" 
							<?php checked( get_option( 'swgtheme_use_global_color', '1' ), '1' ); ?> />
						<label for="swgtheme_use_global_color">
							<?php esc_html_e( 'Apply primary color globally to all theme elements', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'When enabled, the primary color below will be applied to borders, buttons, links, and other theme elements. Disable to set custom colors below.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				
				<tr id="primary-color-row" style="display: <?php echo get_option( 'swgtheme_use_global_color', '1' ) === '1' ? 'table-row' : 'none'; ?>;">
					<th scope="row">
						<label for="swgtheme_primary_color">
							<?php esc_html_e( 'Primary Theme Color', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="color" 
							id="swgtheme_primary_color" 
							name="swgtheme_primary_color" 
							value="<?php echo esc_attr( get_option( 'swgtheme_primary_color', '#dc3545' ) ); ?>" />
						<p class="description">
							<?php esc_html_e( 'Main theme color used throughout the site', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				
				<tr id="button-color-row" class="global-color-override" style="display: <?php echo get_option( 'swgtheme_use_global_color', '1' ) === '1' ? 'none' : 'table-row'; ?>;">
					<th scope="row">
						<label for="swgtheme_button_color">
							<?php esc_html_e( 'Button Color', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="color" 
							id="swgtheme_button_color" 
							name="swgtheme_button_color" 
							value="<?php echo esc_attr( get_option( 'swgtheme_button_color', '#dc3545' ) ); ?>" />
						<p class="description">
							<?php esc_html_e( 'Custom button background color', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="swgtheme_text_color">
							<?php esc_html_e( 'Text Color', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="color" 
							id="swgtheme_text_color" name="swgtheme_text_color" value="<?php echo esc_attr( get_option( 'swgtheme_text_color', '#333333' ) ); ?>" />
						<p class="description">
							<?php esc_html_e( 'Main text color for content', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
				
				<tr id="border-color-row" class="global-color-override" style="display: <?php echo get_option( 'swgtheme_use_global_color', '1' ) === '1' ? 'none' : 'table-row'; ?>;">
					<th scope="row">
						<label for="swgtheme_border_color">
							<?php esc_html_e( 'Border Color', 'swgtheme' ); ?>
						</label>
					</th>
					<td>
						<input type="color" 
							id="swgtheme_border_color" name="swgtheme_border_color" value="<?php echo esc_attr( get_option( 'swgtheme_border_color', '#dc3545' ) ); ?>" />
							<p class="description">
								<?php esc_html_e( 'Custom border color for elements', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr id="link-color-row" class="global-color-override" style="display: <?php echo get_option( 'swgtheme_use_global_color', '1' ) === '1' ? 'none' : 'table-row'; ?>;">
						<th scope="row">
							<label for="swgtheme_link_color">
								<?php esc_html_e( 'Link Color', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="color" 
								id="swgtheme_link_color" name="swgtheme_link_color" value="<?php echo esc_attr( get_option( 'swgtheme_link_color', '#dc3545' ) ); ?>" />
							<p class="description">
								<?php esc_html_e( 'Custom hyperlink color', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_col_lg_3_bg_color">
								<?php esc_html_e( 'Sidebar Background Color (.col-lg-3)', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="color" 
								id="swgtheme_col_lg_3_bg_color" 
								name="swgtheme_col_lg_3_bg_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_col_lg_3_bg_color', '#ffffff' ) ); ?>" />
							<p class="description">
								<?php esc_html_e( 'Background color for sidebar column (.col-lg-3)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_col_lg_9_bg_color">
								<?php esc_html_e( 'Content Background Color (.col-lg-9)', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="color" 
								id="swgtheme_col_lg_9_bg_color" 
								name="swgtheme_col_lg_9_bg_color" 
								value="<?php echo esc_attr( get_option( 'swgtheme_col_lg_9_bg_color', '#ffffff' ) ); ?>" />
							<p class="description">
								<?php esc_html_e( 'Background color for main content column (.col-lg-9)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Dark Mode', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_dark_mode" 
									name="swgtheme_enable_dark_mode" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_dark_mode', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable dark/light mode toggle', 'swgtheme' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Displays a toggle button allowing users to switch between light and dark themes.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
				<tbody id="dark-mode-options" style="display: <?php echo get_option( 'swgtheme_enable_dark_mode', '1' ) === '1' ? 'table-row-group' : 'none'; ?>;">
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_default">
								<?php esc_html_e( 'Default Mode', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select name="swgtheme_dark_mode_default" id="swgtheme_dark_mode_default">
								<option value="dark" <?php selected( get_option( 'swgtheme_dark_mode_default', 'dark' ), 'dark' ); ?>><?php esc_html_e( 'Dark Mode', 'swgtheme' ); ?></option>
								<option value="light" <?php selected( get_option( 'swgtheme_dark_mode_default', 'dark' ), 'light' ); ?>><?php esc_html_e( 'Light Mode', 'swgtheme' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Default theme mode for first-time visitors', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_system_preference">
								<?php esc_html_e( 'System Preference', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_dark_mode_system_preference" id="swgtheme_dark_mode_system_preference" value="1" <?php checked( get_option( 'swgtheme_dark_mode_system_preference', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Auto-detect system dark mode preference', 'swgtheme' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Automatically match the user\'s operating system theme preference', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_auto">
								<?php esc_html_e( 'Auto Schedule', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_dark_mode_auto" id="swgtheme_dark_mode_auto" value="1" <?php checked( get_option( 'swgtheme_dark_mode_auto', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable automatic dark mode based on time', 'swgtheme' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Automatically switch to dark mode during specified hours', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr id="dark-mode-schedule" style="display: <?php echo get_option( 'swgtheme_dark_mode_auto', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<?php esc_html_e( 'Dark Mode Hours', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<?php esc_html_e( 'Start:', 'swgtheme' ); ?>
								<input type="time" name="swgtheme_dark_mode_auto_start" id="swgtheme_dark_mode_auto_start" value="<?php echo esc_attr( get_option( 'swgtheme_dark_mode_auto_start', '18:00' ) ); ?>" />
							</label>
							<label style="margin-left: 20px;">
								<?php esc_html_e( 'End:', 'swgtheme' ); ?>
								<input type="time" name="swgtheme_dark_mode_auto_end" id="swgtheme_dark_mode_auto_end" value="<?php echo esc_attr( get_option( 'swgtheme_dark_mode_auto_end', '06:00' ) ); ?>" />
							</label>
							<p class="description">
								<?php esc_html_e( 'Dark mode will activate between these times (24-hour format)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_toggle_position">
								<?php esc_html_e( 'Toggle Button Position', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select name="swgtheme_dark_mode_toggle_position" id="swgtheme_dark_mode_toggle_position">
								<option value="bottom-right" <?php selected( get_option( 'swgtheme_dark_mode_toggle_position', 'bottom-right' ), 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'swgtheme' ); ?></option>
								<option value="bottom-left" <?php selected( get_option( 'swgtheme_dark_mode_toggle_position', 'bottom-right' ), 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'swgtheme' ); ?></option>
								<option value="top-right" <?php selected( get_option( 'swgtheme_dark_mode_toggle_position', 'bottom-right' ), 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'swgtheme' ); ?></option>
								<option value="top-left" <?php selected( get_option( 'swgtheme_dark_mode_toggle_position', 'bottom-right' ), 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'swgtheme' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Position of the dark mode toggle button', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_transition_speed">
								<?php esc_html_e( 'Transition Speed', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select name="swgtheme_dark_mode_transition_speed" id="swgtheme_dark_mode_transition_speed">
								<option value="instant" <?php selected( get_option( 'swgtheme_dark_mode_transition_speed', 'normal' ), 'instant' ); ?>><?php esc_html_e( 'Instant', 'swgtheme' ); ?></option>
								<option value="fast" <?php selected( get_option( 'swgtheme_dark_mode_transition_speed', 'normal' ), 'fast' ); ?>><?php esc_html_e( 'Fast (0.2s)', 'swgtheme' ); ?></option>
								<option value="normal" <?php selected( get_option( 'swgtheme_dark_mode_transition_speed', 'normal' ), 'normal' ); ?>><?php esc_html_e( 'Normal (0.3s)', 'swgtheme' ); ?></option>
								<option value="slow" <?php selected( get_option( 'swgtheme_dark_mode_transition_speed', 'normal' ), 'slow' ); ?>><?php esc_html_e( 'Slow (0.5s)', 'swgtheme' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Speed of the dark/light mode transition animation', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_dark_mode_background_image">
								<?php esc_html_e( 'Dark Mode Background Image', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="swgtheme_dark_mode_background_image" id="swgtheme_dark_mode_background_image" value="<?php echo esc_attr( get_option( 'swgtheme_dark_mode_background_image', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_dark_mode_background_image"><?php esc_html_e( 'Upload Image', 'swgtheme' ); ?></button>
							<p class="description">
								<?php esc_html_e( 'Custom background image for dark mode (optional, uses main background if empty)', 'swgtheme' ); ?>
							</p>
							<?php if ( get_option( 'swgtheme_dark_mode_background_image' ) ) : ?>
								<img src="<?php echo esc_url( get_option( 'swgtheme_dark_mode_background_image' ) ); ?>" style="max-width: 300px; margin-top: 10px; display: block; border: 1px solid #ddd; padding: 5px;" />
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			
			<!-- Tab: Features -->
			<div class="tab-content" id="features-tab" style="display: none;">
			<h2><?php esc_html_e( 'Theme Features', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Back to Top Button', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_back_to_top" 
									name="swgtheme_enable_back_to_top" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_back_to_top', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable back to top button', 'swgtheme' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Shows a button to quickly scroll back to the top of the page.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Preloader', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_preloader" 
									name="swgtheme_enable_preloader" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_preloader', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable loading screen', 'swgtheme' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Displays a loading animation while the page loads.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
				<tbody id="preloader-options" style="display: <?php echo get_option( 'swgtheme_enable_preloader', '0' ) === '1' ? 'table-row-group' : 'none'; ?>;">
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_style">
								<?php esc_html_e( 'Preloader Style', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select name="swgtheme_preloader_style" id="swgtheme_preloader_style">
								<option value="spinner" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'spinner' ); ?>><?php esc_html_e( 'Spinner', 'swgtheme' ); ?></option>
								<option value="dots" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'dots' ); ?>><?php esc_html_e( 'Three Dots', 'swgtheme' ); ?></option>
								<option value="bars" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'bars' ); ?>><?php esc_html_e( 'Bars', 'swgtheme' ); ?></option>
								<option value="pulse" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'pulse' ); ?>><?php esc_html_e( 'Pulse', 'swgtheme' ); ?></option>
								<option value="ring" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'ring' ); ?>><?php esc_html_e( 'Ring', 'swgtheme' ); ?></option>
								<option value="dual-ring" <?php selected( get_option( 'swgtheme_preloader_style', 'spinner' ), 'dual-ring' ); ?>><?php esc_html_e( 'Dual Ring', 'swgtheme' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Choose the loading animation style', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_bg_color">
								<?php esc_html_e( 'Background Color', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="swgtheme_preloader_bg_color" id="swgtheme_preloader_bg_color" value="<?php echo esc_attr( get_option( 'swgtheme_preloader_bg_color', '#000000' ) ); ?>" class="color-picker" />
							<p class="description">
								<?php esc_html_e( 'Preloader background color', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_spinner_color">
								<?php esc_html_e( 'Spinner Color', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="swgtheme_preloader_spinner_color" id="swgtheme_preloader_spinner_color" value="<?php echo esc_attr( get_option( 'swgtheme_preloader_spinner_color', '#dc3545' ) ); ?>" class="color-picker" />
							<p class="description">
								<?php esc_html_e( 'Loading animation color', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_text">
								<?php esc_html_e( 'Loading Text', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="swgtheme_preloader_text" id="swgtheme_preloader_text" value="<?php echo esc_attr( get_option( 'swgtheme_preloader_text', 'Loading...' ) ); ?>" class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Text to display below the loader (leave empty to hide)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_logo">
								<?php esc_html_e( 'Custom Logo', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="swgtheme_preloader_logo" id="swgtheme_preloader_logo" value="<?php echo esc_attr( get_option( 'swgtheme_preloader_logo', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_preloader_logo"><?php esc_html_e( 'Upload Logo', 'swgtheme' ); ?></button>
							<p class="description">
								<?php esc_html_e( 'Display a logo above the loading animation (optional)', 'swgtheme' ); ?>
							</p>
							<?php if ( get_option( 'swgtheme_preloader_logo' ) ) : ?>
								<img src="<?php echo esc_url( get_option( 'swgtheme_preloader_logo' ) ); ?>" style="max-width: 150px; margin-top: 10px; display: block;" />
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_speed">
								<?php esc_html_e( 'Animation Speed', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select name="swgtheme_preloader_speed" id="swgtheme_preloader_speed">
								<option value="slow" <?php selected( get_option( 'swgtheme_preloader_speed', 'normal' ), 'slow' ); ?>><?php esc_html_e( 'Slow (2s)', 'swgtheme' ); ?></option>
								<option value="normal" <?php selected( get_option( 'swgtheme_preloader_speed', 'normal' ), 'normal' ); ?>><?php esc_html_e( 'Normal (1s)', 'swgtheme' ); ?></option>
								<option value="fast" <?php selected( get_option( 'swgtheme_preloader_speed', 'normal' ), 'fast' ); ?>><?php esc_html_e( 'Fast (0.5s)', 'swgtheme' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Speed of the loading animation', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swgtheme_preloader_fade_duration">
								<?php esc_html_e( 'Fade Out Duration', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" name="swgtheme_preloader_fade_duration" id="swgtheme_preloader_fade_duration" value="<?php echo esc_attr( get_option( 'swgtheme_preloader_fade_duration', '500' ) ); ?>" min="100" max="2000" step="100" /> ms
							<p class="description">
								<?php esc_html_e( 'How long the preloader takes to fade out (100-2000ms)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Background Options', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_background_image">
								<?php esc_html_e( 'Background Image', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_background_image" 
								name="swgtheme_background_image" 
								value="<?php echo esc_url( get_option( 'swgtheme_background_image', '' ) ); ?>" 
								class="regular-text" />
							<button type="button" class="button swgtheme-upload-image-button">
								<?php esc_html_e( 'Upload Image', 'swgtheme' ); ?>
							</button>
							<p class="description">
								<?php esc_html_e( 'Set a background image for the site.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_background_position">
								<?php esc_html_e( 'Background Position', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_background_position" name="swgtheme_background_position">
								<option value="top" <?php selected( get_option( 'swgtheme_background_position', 'center' ), 'top' ); ?>>Top</option>
								<option value="center" <?php selected( get_option( 'swgtheme_background_position', 'center' ), 'center' ); ?>>Center</option>
								<option value="bottom" <?php selected( get_option( 'swgtheme_background_position', 'center' ), 'bottom' ); ?>>Bottom</option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_background_size">
								<?php esc_html_e( 'Background Size', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_background_size" name="swgtheme_background_size">
								<option value="cover" <?php selected( get_option( 'swgtheme_background_size', 'cover' ), 'cover' ); ?>>Cover</option>
								<option value="contain" <?php selected( get_option( 'swgtheme_background_size', 'cover' ), 'contain' ); ?>>Contain</option>
								<option value="auto" <?php selected( get_option( 'swgtheme_background_size', 'cover' ), 'auto' ); ?>>Auto</option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Parallax Effect', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_parallax" 
									name="swgtheme_enable_parallax" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_parallax', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable parallax scrolling effect', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Slider Autoplay Options', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Enable Autoplay', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_slider_autoplay" 
									name="swgtheme_slider_autoplay" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_autoplay', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Automatically advance slides', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_slider_speed">
								<?php esc_html_e( 'Autoplay Speed', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="swgtheme_slider_speed" 
								name="swgtheme_slider_speed" 
								value="<?php echo esc_attr( get_option( 'swgtheme_slider_speed', '5000' ) ); ?>" 
								min="1000" 
								max="10000" 
								step="500" /> ms
							<p class="description">
								<?php esc_html_e( 'Time between slide transitions (1000-10000ms)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Pause on Hover', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_slider_pause_hover" 
									name="swgtheme_slider_pause_hover" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_pause_hover', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Pause autoplay when hovering over slider', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Loop Slides', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_slider_loop" 
									name="swgtheme_slider_loop" 
									value="1" 
									<?php checked( get_option( 'swgtheme_slider_loop', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Return to first slide after last slide', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Custom CSS', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_custom_css">
								<?php esc_html_e( 'Custom CSS Code', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<textarea id="swgtheme_custom_css" 
								name="swgtheme_custom_css" 
								rows="15" 
								class="large-text code"
								style="font-family: monospace;"><?php echo esc_textarea( get_option( 'swgtheme_custom_css', '' ) ); ?></textarea>
							<p class="description">
				<?php esc_html_e( 'Add custom CSS without editing theme files. Do not include <style> tags.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'SEO & Meta Tags', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_meta_description">
								<?php esc_html_e( 'Meta Description', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<textarea id="swgtheme_meta_description" 
								name="swgtheme_meta_description" 
								rows="3" 
								class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_meta_description', '' ) ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Site description for search engines (150-160 characters recommended)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_meta_keywords">
								<?php esc_html_e( 'Meta Keywords', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="swgtheme_meta_keywords" 
								name="swgtheme_meta_keywords" 
								value="<?php echo esc_attr( get_option( 'swgtheme_meta_keywords', '' ) ); ?>" 
								class="large-text" />
							<p class="description">
								<?php esc_html_e( 'Comma-separated keywords for your site', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_og_image">
								<?php esc_html_e( 'Open Graph Image', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="swgtheme_og_image" 
								name="swgtheme_og_image" 
								value="<?php echo esc_url( get_option( 'swgtheme_og_image', '' ) ); ?>" 
								class="regular-text" />
							<button type="button" class="button swgtheme-upload-og-image-button">
								<?php esc_html_e( 'Upload Image', 'swgtheme' ); ?>
							</button>
							<p class="description">
								<?php esc_html_e( 'Default image for social media sharing (1200x630px recommended)', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Analytics & Tracking', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_google_analytics">
								<?php esc_html_e( 'Google Analytics ID', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="swgtheme_google_analytics" 
								name="swgtheme_google_analytics" 
								value="<?php echo esc_attr( get_option( 'swgtheme_google_analytics', '' ) ); ?>" 
								class="regular-text" 
								placeholder="G-XXXXXXXXXX" />
							<p class="description">
								<?php esc_html_e( 'Enter your Google Analytics Measurement ID', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_facebook_pixel">
								<?php esc_html_e( 'Facebook Pixel ID', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="swgtheme_facebook_pixel" 
								name="swgtheme_facebook_pixel" 
								value="<?php echo esc_attr( get_option( 'swgtheme_facebook_pixel', '' ) ); ?>" 
								class="regular-text" 
								placeholder="123456789012345" />
							<p class="description">
								<?php esc_html_e( 'Enter your Facebook Pixel ID', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Notification Bar', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Enable Notification', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_notification" 
									name="swgtheme_enable_notification" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_notification', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show notification bar at top of site', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_notification_text">
								<?php esc_html_e( 'Notification Text', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="swgtheme_notification_text" 
								name="swgtheme_notification_text" 
								value="<?php echo esc_attr( get_option( 'swgtheme_notification_text', '' ) ); ?>" 
								class="large-text" 
								placeholder="Important announcement or message..." />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_notification_type">
								<?php esc_html_e( 'Notification Type', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_notification_type" name="swgtheme_notification_type">
								<option value="info" <?php selected( get_option( 'swgtheme_notification_type', 'info' ), 'info' ); ?>>Info (Blue)</option>
								<option value="success" <?php selected( get_option( 'swgtheme_notification_type', 'info' ), 'success' ); ?>>Success (Green)</option>
								<option value="warning" <?php selected( get_option( 'swgtheme_notification_type', 'info' ), 'warning' ); ?>>Warning (Orange)</option>
								<option value="error" <?php selected( get_option( 'swgtheme_notification_type', 'info' ), 'error' ); ?>>Error (Red)</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Mobile & Animations', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Mobile Menu', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_mobile_menu" 
									name="swgtheme_enable_mobile_menu" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_mobile_menu', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable responsive hamburger menu on mobile devices', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Page Animations', 'swgtheme' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									id="swgtheme_enable_animations" 
									name="swgtheme_enable_animations" 
									value="1" 
									<?php checked( get_option( 'swgtheme_enable_animations', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable smooth scroll and fade animations', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_animation_speed">
								<?php esc_html_e( 'Animation Speed', 'swgtheme' ); ?>
							</label>
						</th>
						<td>
							<select id="swgtheme_animation_speed" name="swgtheme_animation_speed">
								<option value="fast" <?php selected( get_option( 'swgtheme_animation_speed', 'normal' ), 'fast' ); ?>>Fast (200ms)</option>
								<option value="normal" <?php selected( get_option( 'swgtheme_animation_speed', 'normal' ), 'normal' ); ?>>Normal (400ms)</option>
								<option value="slow" <?php selected( get_option( 'swgtheme_animation_speed', 'normal' ), 'slow' ); ?>>Slow (600ms)</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Import / Export Settings', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Export Settings', 'swgtheme' ); ?>
						</th>
						<td>
							<button type="button" id="swgtheme-export-settings" class="button">
								<?php esc_html_e( 'Download Settings JSON', 'swgtheme' ); ?>
							</button>
							<p class="description">
								<?php esc_html_e( 'Export all theme settings as a JSON file for backup or migration', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Import Settings', 'swgtheme' ); ?>
						</th>
						<td>
							<input type="file" id="swgtheme-import-file" accept=".json" style="display:none;" />
							<button type="button" id="swgtheme-import-settings" class="button">
								<?php esc_html_e( 'Choose File & Import', 'swgtheme' ); ?>
							</button>
							<span id="swgtheme-import-status"></span>
							<p class="description">
								<?php esc_html_e( 'Import previously exported settings. This will override current settings.', 'swgtheme' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Social Sharing Section -->
			<h2><?php esc_html_e( 'Social Sharing', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Social Share', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_social_share" value="1" <?php checked( get_option( 'swgtheme_enable_social_share', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show social sharing buttons on posts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Share Platforms', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_social_share_platforms" value="<?php echo esc_attr( get_option( 'swgtheme_social_share_platforms', 'facebook,twitter,linkedin' ) ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Comma-separated: facebook, twitter, linkedin, pinterest', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Reading Progress Bar -->
			<h2><?php esc_html_e( 'Reading Progress Bar', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Reading Progress', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_reading_progress" value="1" <?php checked( get_option( 'swgtheme_enable_reading_progress', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show reading progress bar on single posts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Progress Bar Color', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_progress_bar_color" value="<?php echo esc_attr( get_option( 'swgtheme_progress_bar_color', '#dc3545' ) ); ?>" class="color-picker" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Related Posts -->
			<h2><?php esc_html_e( 'Related Posts', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Related Posts', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_related_posts" value="1" <?php checked( get_option( 'swgtheme_enable_related_posts', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show related posts at end of articles', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Number of Posts', 'swgtheme' ); ?></th>
						<td>
							<input type="number" name="swgtheme_related_posts_count" value="<?php echo esc_attr( get_option( 'swgtheme_related_posts_count', '3' ) ); ?>" min="1" max="12" class="small-text" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Breadcrumbs & TOC -->
			<h2><?php esc_html_e( 'Navigation Features', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Breadcrumbs', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_breadcrumbs" value="1" <?php checked( get_option( 'swgtheme_enable_breadcrumbs', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show breadcrumb navigation', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Table of Contents', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_toc" value="1" <?php checked( get_option( 'swgtheme_enable_toc', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Auto-generate table of contents for posts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Minimum Headings for TOC', 'swgtheme' ); ?></th>
						<td>
							<input type="number" name="swgtheme_toc_min_headings" value="<?php echo esc_attr( get_option( 'swgtheme_toc_min_headings', '3' ) ); ?>" min="1" max="10" class="small-text" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Cookie Consent -->
			<h2><?php esc_html_e( 'Cookie Consent Banner', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Cookie Banner', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_cookies" value="1" <?php checked( get_option( 'swgtheme_enable_cookies', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show GDPR cookie consent banner', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Cookie Message', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_cookie_message" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_cookie_message', 'We use cookies to ensure you get the best experience on our website.' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Accept Button Text', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_cookie_button_text" value="<?php echo esc_attr( get_option( 'swgtheme_cookie_button_text', 'Got it!' ) ); ?>" class="regular-text" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Maintenance Mode -->
			<h2><?php esc_html_e( 'Maintenance Mode', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Maintenance Mode', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_maintenance_mode" value="1" <?php checked( get_option( 'swgtheme_maintenance_mode', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show maintenance page to visitors (admins can still access)', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Maintenance Title', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_maintenance_title" value="<?php echo esc_attr( get_option( 'swgtheme_maintenance_title', 'Website Under Maintenance' ) ); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Maintenance Message', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_maintenance_message" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_maintenance_message', 'We are currently performing maintenance. Please check back soon!' ) ); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Custom Login Page -->
			<h2><?php esc_html_e( 'Custom Login Page', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Custom Login', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_custom_login" value="1" <?php checked( get_option( 'swgtheme_enable_custom_login', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Customize WordPress login page', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Login Logo URL', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_login_logo" id="swgtheme_login_logo" value="<?php echo esc_attr( get_option( 'swgtheme_login_logo', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_login_logo"><?php esc_html_e( 'Upload Logo', 'swgtheme' ); ?></button>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Login Background URL', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_login_background" id="swgtheme_login_background" value="<?php echo esc_attr( get_option( 'swgtheme_login_background', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_login_background"><?php esc_html_e( 'Upload Background', 'swgtheme' ); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Footer Editor -->
			<h2><?php esc_html_e( 'Footer Customization', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Copyright Text', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_footer_copyright" value="<?php echo esc_attr( get_option( 'swgtheme_footer_copyright', '© 2026 All Rights Reserved' ) ); ?>" class="large-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Footer Links', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_footer_links" rows="3" class="large-text" placeholder="Privacy Policy|/privacy&#10;Terms of Service|/terms"><?php echo esc_textarea( get_option( 'swgtheme_footer_links', '' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'One per line: Link Text|URL', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Sticky Header -->
			<h2><?php esc_html_e( 'Header Options', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Sticky Header', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_sticky_header" value="1" <?php checked( get_option( 'swgtheme_enable_sticky_header', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Header stays visible when scrolling', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Custom 404 Page -->
			<h2><?php esc_html_e( 'Custom 404 Error Page', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( '404 Page Title', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_404_title" value="<?php echo esc_attr( get_option( 'swgtheme_404_title', 'Page Not Found' ) ); ?>" class="large-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( '404 Message', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_404_message" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_404_message', 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Button Text', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_404_button_text" value="<?php echo esc_attr( get_option( 'swgtheme_404_button_text', 'Go to Homepage' ) ); ?>" class="regular-text" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Performance & Features -->
			<h2><?php esc_html_e( 'Performance Optimization', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Lazy Loading', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_lazy_load" value="1" <?php checked( get_option( 'swgtheme_enable_lazy_load', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Lazy load images for faster page speed', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable AJAX Search', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_ajax_search" value="1" <?php checked( get_option( 'swgtheme_enable_ajax_search', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Real-time search results', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disable Emojis', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_emojis" value="1" <?php checked( get_option( 'swgtheme_disable_emojis', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Remove WordPress emoji scripts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disable Embeds', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_embeds" value="1" <?php checked( get_option( 'swgtheme_disable_embeds', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Remove WordPress embed scripts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Remove Query Strings', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_remove_query_strings" value="1" <?php checked( get_option( 'swgtheme_remove_query_strings', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Remove version strings from CSS/JS for better caching', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Newsletter Settings -->
			<h2><?php esc_html_e( 'Newsletter Settings', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Newsletter Title', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_newsletter_title" value="<?php echo esc_attr( get_option( 'swgtheme_newsletter_title', 'Subscribe to Newsletter' ) ); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Newsletter Description', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_newsletter_description" rows="2" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_newsletter_description', 'Get the latest updates delivered to your inbox.' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Form Action URL', 'swgtheme' ); ?></th>
						<td>
							<input type="url" name="swgtheme_newsletter_action" value="<?php echo esc_attr( get_option( 'swgtheme_newsletter_action', '' ) ); ?>" class="large-text" placeholder="https://your-email-service.com/subscribe" />
							<p class="description"><?php esc_html_e( 'URL to your email marketing service (Mailchimp, ConvertKit, etc.)', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Schema Markup -->
			<h2><?php esc_html_e( 'Schema Markup (SEO)', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Schema Markup', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_schema" value="1" <?php checked( get_option( 'swgtheme_enable_schema', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Add structured data for better search results', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Organization Name', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_organization_name" value="<?php echo esc_attr( get_option( 'swgtheme_organization_name', '' ) ); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Organization Logo URL', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_organization_logo" id="swgtheme_organization_logo" value="<?php echo esc_attr( get_option( 'swgtheme_organization_logo', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_organization_logo"><?php esc_html_e( 'Upload Logo', 'swgtheme' ); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Content Features -->
			<h2><?php esc_html_e( 'Content Features', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Post Views Counter', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_post_views" value="1" <?php checked( get_option( 'swgtheme_enable_post_views', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Track and display post view counts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Reading Time', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_reading_time" value="1" <?php checked( get_option( 'swgtheme_enable_reading_time', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show estimated reading time on posts', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Reading Speed (WPM)', 'swgtheme' ); ?></th>
						<td>
							<input type="number" name="swgtheme_reading_speed" value="<?php echo esc_attr( get_option( 'swgtheme_reading_speed', '200' ) ); ?>" min="100" max="300" class="small-text" />
							<p class="description"><?php esc_html_e( 'Average words per minute (default: 200)', 'swgtheme' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Code Highlighting', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_syntax_highlighting" value="1" <?php checked( get_option( 'swgtheme_enable_syntax_highlighting', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Prism.js syntax highlighting for code blocks', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Prism Theme', 'swgtheme' ); ?></th>
						<td>
							<select name="swgtheme_prism_theme">
								<option value="default" <?php selected( get_option( 'swgtheme_prism_theme', 'default' ), 'default' ); ?>>Default</option>
								<option value="dark" <?php selected( get_option( 'swgtheme_prism_theme' ), 'dark' ); ?>>Dark</option>
								<option value="okaidia" <?php selected( get_option( 'swgtheme_prism_theme' ), 'okaidia' ); ?>>Okaidia</option>
								<option value="twilight" <?php selected( get_option( 'swgtheme_prism_theme' ), 'twilight' ); ?>>Twilight</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Infinite Scroll', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_infinite_scroll" value="1" <?php checked( get_option( 'swgtheme_enable_infinite_scroll', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Auto-load more posts when scrolling', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Advanced UX -->
			<h2><?php esc_html_e( 'Advanced UX Features', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Back to Top Position', 'swgtheme' ); ?></th>
						<td>
							<select name="swgtheme_back_to_top_position">
								<option value="right" <?php selected( get_option( 'swgtheme_back_to_top_position', 'right' ), 'right' ); ?>>Right</option>
								<option value="left" <?php selected( get_option( 'swgtheme_back_to_top_position' ), 'left' ); ?>>Left</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Back to Top Icon', 'swgtheme' ); ?></th>
						<td>
							<select name="swgtheme_back_to_top_icon">
								<option value="arrow" <?php selected( get_option( 'swgtheme_back_to_top_icon', 'arrow' ), 'arrow' ); ?>>Arrow</option>
								<option value="chevron" <?php selected( get_option( 'swgtheme_back_to_top_icon' ), 'chevron' ); ?>>Chevron</option>
								<option value="rocket" <?php selected( get_option( 'swgtheme_back_to_top_icon' ), 'rocket' ); ?>>Rocket</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Video Background', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_video_bg" value="1" <?php checked( get_option( 'swgtheme_enable_video_bg', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable video background on homepage', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Video Background URL', 'swgtheme' ); ?></th>
						<td>
							<input type="url" name="swgtheme_video_bg_url" value="<?php echo esc_attr( get_option( 'swgtheme_video_bg_url', '' ) ); ?>" class="large-text" placeholder="https://example.com/video.mp4" />
							<p class="description"><?php esc_html_e( 'MP4 video file recommended', 'swgtheme' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Video Poster Image', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_video_bg_poster" id="swgtheme_video_bg_poster" value="<?php echo esc_attr( get_option( 'swgtheme_video_bg_poster', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_video_bg_poster"><?php esc_html_e( 'Upload Image', 'swgtheme' ); ?></button>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Comment Ratings', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_comment_ratings" value="1" <?php checked( get_option( 'swgtheme_enable_comment_ratings', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Allow users to rate posts in comments (1-5 stars)', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Security & SEO -->
			<h2><?php esc_html_e( 'Security & SEO', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Security Headers', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_security_headers" value="1" <?php checked( get_option( 'swgtheme_enable_security_headers', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Add security headers (X-Frame-Options, Content-Security-Policy, etc.)', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable XML Sitemap', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_sitemap" value="1" <?php checked( get_option( 'swgtheme_enable_sitemap', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Auto-generate XML sitemap at /sitemap.xml', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Database Optimization -->
			<h2><?php esc_html_e( 'Database Optimization', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto Cleanup', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_auto_cleanup" value="1" <?php checked( get_option( 'swgtheme_auto_cleanup', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Automatically clean revisions, spam, transients', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Cleanup Frequency', 'swgtheme' ); ?></th>
						<td>
							<select name="swgtheme_cleanup_frequency">
								<option value="daily" <?php selected( get_option( 'swgtheme_cleanup_frequency', 'weekly' ), 'daily' ); ?>>Daily</option>
								<option value="weekly" <?php selected( get_option( 'swgtheme_cleanup_frequency', 'weekly' ), 'weekly' ); ?>>Weekly</option>
								<option value="monthly" <?php selected( get_option( 'swgtheme_cleanup_frequency' ), 'monthly' ); ?>>Monthly</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- CDN Integration -->
			<h2><?php esc_html_e( 'CDN Integration', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable CDN', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_cdn" value="1" <?php checked( get_option( 'swgtheme_enable_cdn', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Rewrite asset URLs to CDN', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'CDN URL', 'swgtheme' ); ?></th>
						<td>
							<input type="url" name="swgtheme_cdn_url" value="<?php echo esc_attr( get_option( 'swgtheme_cdn_url', '' ) ); ?>" class="large-text" placeholder="https://cdn.example.com" />
							<p class="description"><?php esc_html_e( 'Your CDN base URL (CloudFlare, AWS CloudFront, etc.)', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Admin Customization -->
			<h2><?php esc_html_e( 'Admin Panel Customization', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Admin Logo URL', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_admin_logo" id="swgtheme_admin_logo" value="<?php echo esc_attr( get_option( 'swgtheme_admin_logo', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_admin_logo"><?php esc_html_e( 'Upload Logo', 'swgtheme' ); ?></button>
							<p class="description"><?php esc_html_e( 'Replace WordPress logo in admin bar', 'swgtheme' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Admin Footer Text', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_admin_footer_text" value="<?php echo esc_attr( get_option( 'swgtheme_admin_footer_text', '' ) ); ?>" class="large-text" />
							<p class="description"><?php esc_html_e( 'Custom footer text in admin panel', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Admin Color Scheme -->
			<h2><?php esc_html_e( 'Admin Color Scheme', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Admin Primary Color', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_admin_primary_color" value="<?php echo esc_attr( get_option( 'swgtheme_admin_primary_color', '#2271b1' ) ); ?>" class="color-picker" />
							<p class="description"><?php esc_html_e( 'Primary color for admin interface (menus, buttons)', 'swgtheme' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Admin Accent Color', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_admin_accent_color" value="<?php echo esc_attr( get_option( 'swgtheme_admin_accent_color', '#72aee6' ) ); ?>" class="color-picker" />
							<p class="description"><?php esc_html_e( 'Accent color for hovers and highlights', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<!-- Admin Bar & Dashboard -->
			<h2><?php esc_html_e( 'Admin Bar & Dashboard', 'swgtheme' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Hide WordPress Logo', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_hide_wp_logo" value="1" <?php checked( get_option( 'swgtheme_hide_wp_logo', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Hide WordPress logo from admin bar', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			
			<!-- Tab: Advanced -->
			<div class="tab-content" id="advanced-tab" style="display: none;">
			<h2><?php esc_html_e( 'Advanced Options', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Hide Admin Bar (Frontend)', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_hide_admin_bar_front" value="1" <?php checked( get_option( 'swgtheme_hide_admin_bar_front', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Hide admin bar on front-end for all users', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Dashboard Welcome Title', 'swgtheme' ); ?></th>
						<td>
							<input type="text" name="swgtheme_dashboard_welcome_title" value="<?php echo esc_attr( get_option( 'swgtheme_dashboard_welcome_title', '' ) ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'Welcome to Your Site!', 'swgtheme' ); ?>" />
							<p class="description"><?php esc_html_e( 'Custom title for dashboard welcome panel', 'swgtheme' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Dashboard Welcome Text', 'swgtheme' ); ?></th>
						<td>
							<textarea name="swgtheme_dashboard_welcome_text" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Custom welcome message...', 'swgtheme' ); ?>"><?php echo esc_textarea( get_option( 'swgtheme_dashboard_welcome_text', '' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Custom welcome message on dashboard', 'swgtheme' ); ?></p>
						</td>
					</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Developer Mode', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_developer_mode" value="1" <?php checked( get_option( 'swgtheme_enable_developer_mode', '0' ), '1' ); ?> />
							<?php esc_html_e( 'Enable developer tools and debugging features', 'swgtheme' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Shows debug toolbar (Ctrl+Shift+D), performance metrics, console logging, and developer admin bar menu. Only enable during development.', 'swgtheme' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
		
		<?php submit_button( __( 'Save Theme Options', 'swgtheme' ) ); ?>
	</form>
	
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			function toggleColorOptions() {
				var useGlobal = $('#swgtheme_use_global_color').is(':checked');
				
				if (useGlobal) {
					// Show Primary Color and Text Color, hide individual colors
					$('#primary-color-row').show();
					$('#text-color-row').show();
					$('#button-color-row').hide();
					$('#border-color-row').hide();
					$('#link-color-row').hide();
				} else {
					// Hide Primary Color, show individual colors including Text Color
					$('#primary-color-row').hide();
					$('#text-color-row').show();
					$('#button-color-row').show();
					$('#border-color-row').show();
					$('#link-color-row').show();
				}
			}
			
			// Run on page load
			toggleColorOptions();
			
			// Run when checkbox changes
			$('#swgtheme_use_global_color').change(function() {
				toggleColorOptions();
			});
			
			// Toggle preloader options
			$('#swgtheme_enable_preloader').change(function() {
				if ($(this).is(':checked')) {
					$('#preloader-options').fadeIn();
				} else {
					$('#preloader-options').fadeOut();
				}
			});
			
			// Toggle dark mode options
			$('#swgtheme_enable_dark_mode').change(function() {
				if ($(this).is(':checked')) {
					$('#dark-mode-options').fadeIn();
				} else {
					$('#dark-mode-options').fadeOut();
				}
			});
			
			// Toggle dark mode schedule
			$('#swgtheme_dark_mode_auto').change(function() {
				if ($(this).is(':checked')) {
					$('#dark-mode-schedule').fadeIn();
				} else {
					$('#dark-mode-schedule').fadeOut();
				}
			});
			
			// Initialize color pickers
			if (typeof $.fn.wpColorPicker !== 'undefined') {
				$('.color-picker').wpColorPicker();
			}
		});
		</script>
	</div>
	<?php
}

// Login Page Customization
function swgtheme_custom_login_styles() {
	$login_logo = get_option( 'swgtheme_login_logo', '' );
	$logo_width = get_option( 'swgtheme_login_logo_width', '84' );
	$logo_height = get_option( 'swgtheme_login_logo_height', '84' );
	$bg_color = get_option( 'swgtheme_login_bg_color', '#f0f0f1' );
	$bg_image = get_option( 'swgtheme_login_bg_image', '' );
	$button_color = get_option( 'swgtheme_login_button_color', '#2271b1' );
	
	if ( $login_logo || $bg_color !== '#f0f0f1' || $bg_image || $button_color !== '#2271b1' ) :
	?>
	<style type="text/css">
		<?php if ( $login_logo ) : ?>
		body.login div#login h1 a {
			background-image: url(<?php echo esc_url( $login_logo ); ?>);
			width: <?php echo intval( $logo_width ); ?>px;
			height: <?php echo intval( $logo_height ); ?>px;
			background-size: contain;
			background-position: center;
		}
		<?php endif; ?>
		
		body.login {
			background-color: <?php echo esc_attr( $bg_color ); ?>;
			<?php if ( $bg_image ) : ?>
			background-image: url(<?php echo esc_url( $bg_image ); ?>);
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			<?php endif; ?>
		}
		
		<?php if ( $button_color !== '#2271b1' ) : ?>
		.wp-core-ui .button-primary {
			background: <?php echo esc_attr( $button_color ); ?>;
			border-color: <?php echo esc_attr( $button_color ); ?>;
		}
		.wp-core-ui .button-primary:hover,
		.wp-core-ui .button-primary:focus {
			background: <?php echo esc_attr( $button_color ); ?>;
			border-color: <?php echo esc_attr( $button_color ); ?>;
			filter: brightness(1.1);
		}
		<?php endif; ?>
	</style>
	<?php
	endif;
}
add_action( 'login_enqueue_scripts', 'swgtheme_custom_login_styles' );

// Change login logo URL
function swgtheme_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'swgtheme_login_logo_url' );

// Change login logo title
function swgtheme_login_logo_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'swgtheme_login_logo_title' );

// Admin Color Scheme
function swgtheme_admin_color_scheme() {
	$primary_color = get_option( 'swgtheme_admin_primary_color', '#2271b1' );
	$accent_color = get_option( 'swgtheme_admin_accent_color', '#72aee6' );
	
	if ( $primary_color !== '#2271b1' || $accent_color !== '#72aee6' ) :
	?>
	<style type="text/css">
		/* Admin menu */
		#adminmenu .wp-submenu a:hover,
		#adminmenu .wp-submenu a:focus,
		#adminmenu a:hover,
		#adminmenu li.menu-top:hover,
		#adminmenu li.opensub > a.menu-top,
		#adminmenu li > a.menu-top:focus {
			color: #fff;
			background-color: <?php echo esc_attr( $primary_color ); ?>;
		}
		
		#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
		#adminmenu .wp-menu-arrow,
		#adminmenu .wp-menu-arrow div,
		#adminmenu li.current a.menu-top,
		#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
			background: <?php echo esc_attr( $primary_color ); ?>;
		}
		
		/* Buttons */
		.wp-core-ui .button-primary {
			background: <?php echo esc_attr( $primary_color ); ?>;
			border-color: <?php echo esc_attr( $primary_color ); ?>;
		}
		
		.wp-core-ui .button-primary:hover,
		.wp-core-ui .button-primary:focus {
			background: <?php echo esc_attr( $accent_color ); ?>;
			border-color: <?php echo esc_attr( $accent_color ); ?>;
		}
		
		/* Links */
		a,
		#adminmenu a:focus,
		.row-actions .view a {
			color: <?php echo esc_attr( $primary_color ); ?>;
		}
		
		a:hover,
		a:active,
		a:focus {
			color: <?php echo esc_attr( $accent_color ); ?>;
		}
		
		/* Admin bar */
		#wpadminbar .ab-item:hover,
		#wpadminbar .ab-item:focus,
		#wpadminbar .menupop .ab-sub-wrapper,
		#wpadminbar .shortlink-input {
			background: <?php echo esc_attr( $primary_color ); ?>;
		}
		
		#wpadminbar .quicklinks .ab-sub-wrapper .menupop.hover > a,
		#wpadminbar .quicklinks .menupop.hover .ab-item:hover {
			color: <?php echo esc_attr( $accent_color ); ?>;
		}
	</style>
	<?php
	endif;
}
add_action( 'admin_head', 'swgtheme_admin_color_scheme' );

// Hide WordPress logo from admin bar
if ( get_option( 'swgtheme_hide_wp_logo', '0' ) === '1' ) {
	function swgtheme_remove_wp_logo( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'wp-logo' );
	}
	add_action( 'admin_bar_menu', 'swgtheme_remove_wp_logo', 999 );
}

// Hide admin bar on frontend
if ( get_option( 'swgtheme_hide_admin_bar_front', '0' ) === '1' ) {
	add_filter( 'show_admin_bar', '__return_false' );
}

// Custom dashboard welcome panel
function swgtheme_custom_dashboard_welcome() {
	$welcome_title = get_option( 'swgtheme_dashboard_welcome_title', '' );
	$welcome_text = get_option( 'swgtheme_dashboard_welcome_text', '' );
	
	if ( $welcome_title || $welcome_text ) {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
		add_action( 'welcome_panel', 'swgtheme_dashboard_welcome_panel' );
	}
}
add_action( 'load-index.php', 'swgtheme_custom_dashboard_welcome' );

function swgtheme_dashboard_welcome_panel() {
	$welcome_title = get_option( 'swgtheme_dashboard_welcome_title', 'Welcome to Your Site!' );
	$welcome_text = get_option( 'swgtheme_dashboard_welcome_text', '' );
	?>
	<div class="welcome-panel-content">
		<h2><?php echo esc_html( $welcome_title ); ?></h2>
		<?php if ( $welcome_text ) : ?>
			<p class="about-description"><?php echo wp_kses_post( wpautop( $welcome_text ) ); ?></p>
		<?php endif; ?>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h3><?php esc_html_e( 'Get Started', 'swgtheme' ); ?></h3>
				<a class="button button-primary button-hero" href="<?php echo admin_url( 'post-new.php' ); ?>"><?php esc_html_e( 'Write your first post', 'swgtheme' ); ?></a>
				<p><?php esc_html_e( 'or', 'swgtheme' ); ?>, <a href="<?php echo admin_url( 'post-new.php?post_type=page' ); ?>"> <?php esc_html_e( 'add a new page', 'swgtheme' ); ?></a></p>
			</div>
			<div class="welcome-panel-column">
				<h3><?php esc_html_e( 'Customize', 'swgtheme' ); ?></h3>
				<ul>
					<li><a href="<?php echo admin_url( 'customize.php' ); ?>" class="welcome-icon welcome-customize"><?php esc_html_e( 'Customize your site', 'swgtheme' ); ?></a></li>
					<li><a href="<?php echo admin_url( 'themes.php' ); ?>" class="welcome-icon welcome-widgets-menus"><?php esc_html_e( 'Manage widgets and menus', 'swgtheme' ); ?></a></li>
				</ul>
			</div>
			<div class="welcome-panel-column welcome-panel-last">
				<h3><?php esc_html_e( 'Quick Links', 'swgtheme' ); ?></h3>
				<ul>
					<li><a href="<?php echo admin_url( 'options-general.php' ); ?>" class="welcome-icon welcome-learn-more"><?php esc_html_e( 'Settings', 'swgtheme' ); ?></a></li>
					<li><a href="<?php echo admin_url( 'users.php' ); ?>" class="welcome-icon welcome-view-site"><?php esc_html_e( 'Manage Users', 'swgtheme' ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<?php
}

// ============================================
// USER & SOCIAL FEATURES
// ============================================

// Register User & Social settings
function swgtheme_register_user_social_settings() {
	// User Profiles
	register_setting( 'swgtheme-settings-group', 'swgtheme_author_profiles_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_author_profiles_social' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_author_profiles_posts_count' );
	
	// Social Share Counts
	register_setting( 'swgtheme-settings-group', 'swgtheme_social_share_counts' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_share_cache_time' );
	
	// Social Proof Notifications
	register_setting( 'swgtheme-settings-group', 'swgtheme_social_proof_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_social_proof_message' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_social_proof_delay' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_social_proof_duration' );
	
	// Related Authors
	register_setting( 'swgtheme-settings-group', 'swgtheme_related_authors_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_related_authors_count' );
	
	// Author Follow System
	register_setting( 'swgtheme-settings-group', 'swgtheme_author_follow_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_author_follow_button_text' );
	
	// User Ratings System
	register_setting( 'swgtheme-settings-group', 'swgtheme_user_rating_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_user_rating_position' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_user_rating_require_login' );
}
add_action( 'admin_init', 'swgtheme_register_user_social_settings' );

// Add User & Social admin menu page
function swgtheme_user_social_admin_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'User & Social Features', 'swgtheme' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'swgtheme-settings-group' );
			do_settings_sections( 'swgtheme-settings-group' );
			?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'User Profiles', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Enhanced Author Profiles', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_author_profiles_enable" value="1" <?php checked( get_option('swgtheme_author_profiles_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Show enhanced author profile pages with biography, social links, and recent posts', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="author-profile-options" style="<?php echo get_option('swgtheme_author_profiles_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Show Social Links', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_author_profiles_social" value="1" <?php checked( get_option('swgtheme_author_profiles_social', '1'), '1' ); ?> />
							<?php esc_html_e( 'Display author social media links on profile', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="author-profile-posts" style="<?php echo get_option('swgtheme_author_profiles_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Recent Posts to Show', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_author_profiles_posts_count" value="<?php echo esc_attr( get_option('swgtheme_author_profiles_posts_count', '6') ); ?>" min="1" max="20" />
						<p class="description"><?php esc_html_e( 'Number of recent posts to display on author profile', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Social Share Counts', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Show Share Counts', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_social_share_counts" value="1" <?php checked( get_option('swgtheme_social_share_counts', '0'), '1' ); ?> />
							<?php esc_html_e( 'Display share counts on social sharing buttons', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="share-cache-options" style="<?php echo get_option('swgtheme_social_share_counts', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Cache Duration', 'swgtheme' ); ?></th>
					<td>
						<select name="swgtheme_share_cache_time">
							<option value="3600" <?php selected( get_option('swgtheme_share_cache_time', '3600'), '3600' ); ?>>1 Hour</option>
							<option value="21600" <?php selected( get_option('swgtheme_share_cache_time', '3600'), '21600' ); ?>>6 Hours</option>
							<option value="43200" <?php selected( get_option('swgtheme_share_cache_time', '3600'), '43200' ); ?>>12 Hours</option>
							<option value="86400" <?php selected( get_option('swgtheme_share_cache_time', '3600'), '86400' ); ?>>24 Hours</option>
						</select>
						<p class="description"><?php esc_html_e( 'How long to cache share count data', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Social Proof Notifications', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Social Proof Popups', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_social_proof_enable" value="1" <?php checked( get_option('swgtheme_social_proof_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Show "X people reading this" notification popups', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="social-proof-message" style="<?php echo get_option('swgtheme_social_proof_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Notification Message', 'swgtheme' ); ?></th>
					<td>
						<input type="text" name="swgtheme_social_proof_message" value="<?php echo esc_attr( get_option('swgtheme_social_proof_message', '{count} people reading this now') ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Use {count} as placeholder for viewer count', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top" id="social-proof-delay" style="<?php echo get_option('swgtheme_social_proof_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Delay Before Showing', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_social_proof_delay" value="<?php echo esc_attr( get_option('swgtheme_social_proof_delay', '3') ); ?>" min="0" max="30" /> seconds
					</td>
				</tr>
				<tr valign="top" id="social-proof-duration" style="<?php echo get_option('swgtheme_social_proof_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Display Duration', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_social_proof_duration" value="<?php echo esc_attr( get_option('swgtheme_social_proof_duration', '5') ); ?>" min="1" max="30" /> seconds
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Related Authors', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Related Authors', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_related_authors_enable" value="1" <?php checked( get_option('swgtheme_related_authors_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Show authors who write about similar topics', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="related-authors-count" style="<?php echo get_option('swgtheme_related_authors_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Number of Authors', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_related_authors_count" value="<?php echo esc_attr( get_option('swgtheme_related_authors_count', '4') ); ?>" min="2" max="12" />
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Author Follow System', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Author Following', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_author_follow_enable" value="1" <?php checked( get_option('swgtheme_author_follow_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Let users follow their favorite authors', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="author-follow-text" style="<?php echo get_option('swgtheme_author_follow_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Follow Button Text', 'swgtheme' ); ?></th>
					<td>
						<input type="text" name="swgtheme_author_follow_button_text" value="<?php echo esc_attr( get_option('swgtheme_author_follow_button_text', 'Follow') ); ?>" class="regular-text" />
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'User Ratings System', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable User Ratings', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_user_rating_enable" value="1" <?php checked( get_option('swgtheme_user_rating_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Allow users to rate posts and pages', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="user-rating-position" style="<?php echo get_option('swgtheme_user_rating_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Rating Position', 'swgtheme' ); ?></th>
					<td>
						<select name="swgtheme_user_rating_position">
							<option value="before" <?php selected( get_option('swgtheme_user_rating_position', 'after'), 'before' ); ?>>Before Content</option>
							<option value="after" <?php selected( get_option('swgtheme_user_rating_position', 'after'), 'after' ); ?>>After Content</option>
							<option value="both" <?php selected( get_option('swgtheme_user_rating_position', 'after'), 'both' ); ?>>Before and After</option>
						</select>
					</td>
				</tr>
				<tr valign="top" id="user-rating-login" style="<?php echo get_option('swgtheme_user_rating_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Require Login', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_user_rating_require_login" value="1" <?php checked( get_option('swgtheme_user_rating_require_login', '0'), '1' ); ?> />
							<?php esc_html_e( 'Only logged-in users can rate content', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
			</table>
			</div>
			
			<?php submit_button(); ?>
		</form>
		
		<style>
		.nav-tab-wrapper {
			margin: 20px 0;
			border-bottom: 1px solid #ccc;
		}
		.nav-tab {
			position: relative;
			padding: 10px 15px;
			cursor: pointer;
		}
		.tab-content {
			display: none !important;
			padding: 20px 0;
		}
		.tab-content.active {
			display: block !important;
		}
		.tab-content h2 {
			margin-top: 0;
		}
		</style>
	</div>
	<?php
}

// Enhanced Author Profiles
if ( get_option( 'swgtheme_author_profiles_enable', '0' ) === '1' ) {
	// Add custom fields to user profile
	function swgtheme_add_author_custom_fields( $user ) {
		?>
		<h3><?php esc_html_e( 'Social Media Links', 'swgtheme' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="twitter"><?php esc_html_e( 'Twitter', 'swgtheme' ); ?></label></th>
				<td>
					<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="facebook"><?php esc_html_e( 'Facebook', 'swgtheme' ); ?></label></th>
				<td>
					<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="linkedin"><?php esc_html_e( 'LinkedIn', 'swgtheme' ); ?></label></th>
				<td>
					<input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_user_meta( $user->ID, 'linkedin', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="instagram"><?php esc_html_e( 'Instagram', 'swgtheme' ); ?></label></th>
				<td>
					<input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_user_meta( $user->ID, 'instagram', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="github"><?php esc_html_e( 'GitHub', 'swgtheme' ); ?></label></th>
				<td>
					<input type="text" name="github" id="github" value="<?php echo esc_attr( get_user_meta( $user->ID, 'github', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
		<?php
	}
	add_action( 'show_user_profile', 'swgtheme_add_author_custom_fields' );
	add_action( 'edit_user_profile', 'swgtheme_add_author_custom_fields' );
	
	// Save custom fields
	function swgtheme_save_author_custom_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		
		$social_fields = array( 'twitter', 'facebook', 'linkedin', 'instagram', 'github' );
		
		foreach ( $social_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_user_meta( $user_id, $field, sanitize_text_field( $_POST[ $field ] ) );
			}
		}
	}
	add_action( 'personal_options_update', 'swgtheme_save_author_custom_fields' );
	add_action( 'edit_user_profile_update', 'swgtheme_save_author_custom_fields' );
	
	// Enhanced author bio on posts
	function swgtheme_enhanced_author_bio( $content ) {
		if ( is_single() && is_main_query() ) {
			$author_id = get_the_author_meta( 'ID' );
			$author_bio = get_the_author_meta( 'description' );
			
			if ( $author_bio ) {
				$author_name = get_the_author();
				$author_posts_url = get_author_posts_url( $author_id );
				$author_avatar = get_avatar( $author_id, 80 );
				
				$bio_html = '<div class="author-bio-box">';
				$bio_html .= '<div class="author-avatar">' . $author_avatar . '</div>';
				$bio_html .= '<div class="author-info">';
				$bio_html .= '<h4 class="author-name"><a href="' . esc_url( $author_posts_url ) . '">' . esc_html( $author_name ) . '</a></h4>';
				$bio_html .= '<p class="author-description">' . wp_kses_post( $author_bio ) . '</p>';
				
				if ( get_option( 'swgtheme_author_profiles_social', '1' ) === '1' ) {
					$bio_html .= '<div class="author-social-links">';
					
					$social_links = array(
						'twitter' => 'Twitter',
						'facebook' => 'Facebook',
						'linkedin' => 'LinkedIn',
						'instagram' => 'Instagram',
						'github' => 'GitHub'
					);
					
					foreach ( $social_links as $key => $label ) {
						$url = get_user_meta( $author_id, $key, true );
						if ( $url ) {
							$bio_html .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="author-social-' . esc_attr( $key ) . '" title="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</a>';
						}
					}
					
					$bio_html .= '</div>';
				}
				
				if ( get_option( 'swgtheme_author_follow_enable', '0' ) === '1' ) {
					$bio_html .= '<button class="author-follow-btn" data-author-id="' . esc_attr( $author_id ) . '">';
					$bio_html .= esc_html( get_option( 'swgtheme_author_follow_button_text', 'Follow' ) );
					$bio_html .= '</button>';
				}
				
				$bio_html .= '</div></div>';
				
				$content .= $bio_html;
			}
		}
		
		return $content;
	}
	add_filter( 'the_content', 'swgtheme_enhanced_author_bio' );
}

// Social Share Counts
if ( get_option( 'swgtheme_social_share_counts', '0' ) === '1' ) {
	// Get Facebook share count
	function swgtheme_get_facebook_shares( $url ) {
		$cache_key = 'fb_shares_' . md5( $url );
		$cache_time = intval( get_option( 'swgtheme_share_cache_time', '3600' ) );
		
		$count = get_transient( $cache_key );
		
		if ( false === $count ) {
			$count = 0;
			// Note: Facebook Graph API requires App ID/Secret for accurate counts
			// This is a simplified example
			set_transient( $cache_key, $count, $cache_time );
		}
		
		return $count;
	}
	
	// Add share counts to social buttons
	function swgtheme_add_share_counts_js() {
		if ( is_single() ) {
			?>
			<script>
			jQuery(document).ready(function($) {
				// Add share count display to buttons
				$('.social-share-buttons a').each(function() {
					var $this = $(this);
					var network = $this.data('network');
					
					if (network) {
						$this.append('<span class="share-count" data-network="' + network + '">0</span>');
					}
				});
				
				// Update counts (would need AJAX endpoint for real counts)
				// This is a placeholder for demonstration
			});
			</script>
			<?php
		}
	}
	add_action( 'wp_footer', 'swgtheme_add_share_counts_js' );
}

// Social Proof Notifications
if ( get_option( 'swgtheme_social_proof_enable', '0' ) === '1' ) {
	function swgtheme_social_proof_notification() {
		if ( is_single() ) {
			$message = get_option( 'swgtheme_social_proof_message', '{count} people reading this now' );
			$delay = intval( get_option( 'swgtheme_social_proof_delay', '3' ) );
			$duration = intval( get_option( 'swgtheme_social_proof_duration', '5' ) );
			?>
			<div id="social-proof-notification" class="social-proof-popup" style="display: none;">
				<div class="social-proof-content">
					<span class="social-proof-icon">👁️</span>
					<span class="social-proof-text"></span>
				</div>
			</div>
			<script>
			jQuery(document).ready(function($) {
				setTimeout(function() {
					var viewerCount = Math.floor(Math.random() * 20) + 5; // Simulated count
					var message = '<?php echo esc_js( $message ); ?>';
					message = message.replace('{count}', viewerCount);
					
					$('#social-proof-notification .social-proof-text').text(message);
					$('#social-proof-notification').fadeIn(300);
					
					setTimeout(function() {
						$('#social-proof-notification').fadeOut(300);
					}, <?php echo $duration * 1000; ?>);
				}, <?php echo $delay * 1000; ?>);
			});
			</script>
			<?php
		}
	}
	add_action( 'wp_footer', 'swgtheme_social_proof_notification' );
}

// Related Authors
if ( get_option( 'swgtheme_related_authors_enable', '0' ) === '1' ) {
	function swgtheme_get_related_authors( $author_id, $post_id ) {
		$count = intval( get_option( 'swgtheme_related_authors_count', '4' ) );
		
		// Get current post categories
		$categories = wp_get_post_categories( $post_id );
		
		if ( empty( $categories ) ) {
			return array();
		}
		
		// Get other authors who write in same categories
		$args = array(
			'category__in' => $categories,
			'author__not_in' => array( $author_id ),
			'posts_per_page' => 50,
			'fields' => 'post_author'
		);
		
		$query = new WP_Query( $args );
		$authors = array();
		
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_author = get_the_author_meta( 'ID' );
				if ( ! in_array( $post_author, $authors ) ) {
					$authors[] = $post_author;
				}
				
				if ( count( $authors ) >= $count ) {
					break;
				}
			}
			wp_reset_postdata();
		}
		
		return array_slice( $authors, 0, $count );
	}
	
	function swgtheme_display_related_authors( $content ) {
		if ( is_single() && is_main_query() ) {
			$author_id = get_the_author_meta( 'ID' );
			$post_id = get_the_ID();
			$related_authors = swgtheme_get_related_authors( $author_id, $post_id );
			
			if ( ! empty( $related_authors ) ) {
				$output = '<div class="related-authors">';
				$output .= '<h3>' . esc_html__( 'Authors Writing About Similar Topics', 'swgtheme' ) . '</h3>';
				$output .= '<div class="related-authors-grid">';
				
				foreach ( $related_authors as $author ) {
					$author_name = get_the_author_meta( 'display_name', $author );
					$author_url = get_author_posts_url( $author );
					$author_avatar = get_avatar( $author, 60 );
					$author_posts_count = count_user_posts( $author );
					
					$output .= '<div class="related-author-card">';
					$output .= '<div class="related-author-avatar">' . $author_avatar . '</div>';
					$output .= '<h4><a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a></h4>';
					$output .= '<p class="related-author-posts">' . sprintf( _n( '%s post', '%s posts', $author_posts_count, 'swgtheme' ), number_format_i18n( $author_posts_count ) ) . '</p>';
					$output .= '</div>';
				}
				
				$output .= '</div></div>';
				
				$content .= $output;
			}
		}
		
		return $content;
	}
	add_filter( 'the_content', 'swgtheme_display_related_authors' );
}

// Author Follow System
if ( get_option( 'swgtheme_author_follow_enable', '0' ) === '1' ) {
	// AJAX handler for follow/unfollow
	function swgtheme_author_follow_handler() {
		check_ajax_referer( 'swgtheme_author_follow', 'nonce' );
		
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to follow authors.', 'swgtheme' ) ) );
		}
		
		$user_id = get_current_user_id();
		$author_id = intval( $_POST['author_id'] );
		
		$followed_authors = get_user_meta( $user_id, 'followed_authors', true );
		if ( ! is_array( $followed_authors ) ) {
			$followed_authors = array();
		}
		
		$is_following = in_array( $author_id, $followed_authors );
		
		if ( $is_following ) {
			// Unfollow
			$followed_authors = array_diff( $followed_authors, array( $author_id ) );
			$action = 'unfollowed';
			$button_text = get_option( 'swgtheme_author_follow_button_text', 'Follow' );
		} else {
			// Follow
			$followed_authors[] = $author_id;
			$action = 'followed';
			$button_text = 'Following';
		}
		
		update_user_meta( $user_id, 'followed_authors', array_values( $followed_authors ) );
		
		// Update follower count for author
		$followers = get_user_meta( $author_id, 'follower_count', true );
		$followers = intval( $followers );
		
		if ( $action === 'followed' ) {
			$followers++;
		} else {
			$followers = max( 0, $followers - 1 );
		}
		
		update_user_meta( $author_id, 'follower_count', $followers );
		
		wp_send_json_success( array(
			'action' => $action,
			'button_text' => $button_text,
			'followers' => $followers
		) );
	}
	add_action( 'wp_ajax_swgtheme_author_follow', 'swgtheme_author_follow_handler' );
	
	// Enqueue follow script
	function swgtheme_author_follow_script() {
		if ( is_single() || is_author() ) {
			?>
			<script>
			jQuery(document).ready(function($) {
				$('.author-follow-btn').on('click', function() {
					var $btn = $(this);
					var authorId = $btn.data('author-id');
					
					if (!authorId) return;
					
					$btn.prop('disabled', true);
					
					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						type: 'POST',
						data: {
							action: 'swgtheme_author_follow',
							author_id: authorId,
							nonce: '<?php echo wp_create_nonce( 'swgtheme_author_follow' ); ?>'
						},
						success: function(response) {
							if (response.success) {
								$btn.text(response.data.button_text);
								$btn.toggleClass('following');
							} else {
								alert(response.data.message);
							}
						},
						complete: function() {
							$btn.prop('disabled', false);
						}
					});
				});
				
				// Check if user is already following
				<?php if ( is_user_logged_in() ) : ?>
					$('.author-follow-btn').each(function() {
						var $btn = $(this);
						var authorId = $btn.data('author-id');
						
						// This would need an AJAX call to check following status
						// Simplified for now
					});
				<?php endif; ?>
			});
			</script>
			<?php
		}
	}
	add_action( 'wp_footer', 'swgtheme_author_follow_script' );
}

// User Ratings System
if ( get_option( 'swgtheme_user_rating_enable', '0' ) === '1' ) {
	// Display rating widget
	function swgtheme_display_user_rating( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		$ratings = get_post_meta( $post_id, 'user_ratings', true );
		if ( ! is_array( $ratings ) ) {
			$ratings = array();
		}
		
		$total_ratings = count( $ratings );
		$average_rating = $total_ratings > 0 ? array_sum( $ratings ) / $total_ratings : 0;
		
		$user_id = get_current_user_id();
		$user_rating = isset( $ratings[ $user_id ] ) ? $ratings[ $user_id ] : 0;
		
		$require_login = get_option( 'swgtheme_user_rating_require_login', '0' ) === '1';
		$can_rate = ! $require_login || is_user_logged_in();
		
		ob_start();
		?>
		<div class="user-rating-widget" data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<div class="rating-summary">
				<div class="rating-stars average" data-rating="<?php echo esc_attr( $average_rating ); ?>">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<span class="star <?php echo $i <= round( $average_rating ) ? 'filled' : ''; ?>">★</span>
					<?php endfor; ?>
				</div>
				<span class="rating-text">
					<?php
					if ( $total_ratings > 0 ) {
						printf(
							__( '%s out of 5 stars (%s ratings)', 'swgtheme' ),
							number_format( $average_rating, 1 ),
							number_format_i18n( $total_ratings )
						);
					} else {
						esc_html_e( 'No ratings yet', 'swgtheme' );
					}
					?>
				</span>
			</div>
			
			<?php if ( $can_rate ) : ?>
				<div class="rating-input">
					<p><?php esc_html_e( 'Rate this:', 'swgtheme' ); ?></p>
					<div class="rating-stars interactive" data-user-rating="<?php echo esc_attr( $user_rating ); ?>">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<span class="star <?php echo $i <= $user_rating ? 'filled' : ''; ?>" data-rating="<?php echo $i; ?>">★</span>
						<?php endfor; ?>
					</div>
				</div>
			<?php elseif ( $require_login ) : ?>
				<p class="rating-login-message">
					<a href="<?php echo wp_login_url( get_permalink() ); ?>"><?php esc_html_e( 'Login to rate', 'swgtheme' ); ?></a>
				</p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
	
	// Add rating to content
	function swgtheme_add_rating_to_content( $content ) {
		if ( is_single() && is_main_query() ) {
			$position = get_option( 'swgtheme_user_rating_position', 'after' );
			$rating_html = swgtheme_display_user_rating();
			
			if ( $position === 'before' ) {
				$content = $rating_html . $content;
			} elseif ( $position === 'after' ) {
				$content .= $rating_html;
			} elseif ( $position === 'both' ) {
				$content = $rating_html . $content . $rating_html;
			}
		}
		
		return $content;
	}
	add_filter( 'the_content', 'swgtheme_add_rating_to_content' );
	
	// AJAX handler for rating submission
	function swgtheme_submit_rating() {
		check_ajax_referer( 'swgtheme_rating', 'nonce' );
		
		$require_login = get_option( 'swgtheme_user_rating_require_login', '0' ) === '1';
		
		if ( $require_login && ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to rate.', 'swgtheme' ) ) );
		}
		
		$post_id = intval( $_POST['post_id'] );
		$rating = intval( $_POST['rating'] );
		
		if ( $rating < 1 || $rating > 5 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid rating value.', 'swgtheme' ) ) );
		}
		
		$user_id = is_user_logged_in() ? get_current_user_id() : $_SERVER['REMOTE_ADDR'];
		
		$ratings = get_post_meta( $post_id, 'user_ratings', true );
		if ( ! is_array( $ratings ) ) {
			$ratings = array();
		}
		
		$ratings[ $user_id ] = $rating;
		update_post_meta( $post_id, 'user_ratings', $ratings );
		
		$total_ratings = count( $ratings );
		$average_rating = array_sum( $ratings ) / $total_ratings;
		
		wp_send_json_success( array(
			'average' => number_format( $average_rating, 1 ),
			'total' => $total_ratings,
			'user_rating' => $rating
		) );
	}
	add_action( 'wp_ajax_swgtheme_submit_rating', 'swgtheme_submit_rating' );
	add_action( 'wp_ajax_nopriv_swgtheme_submit_rating', 'swgtheme_submit_rating' );
	
	// Enqueue rating script
	function swgtheme_rating_script() {
		if ( is_single() ) {
			?>
			<script>
			jQuery(document).ready(function($) {
				$('.rating-stars.interactive .star').on('click', function() {
					var $this = $(this);
					var rating = $this.data('rating');
					var $widget = $this.closest('.user-rating-widget');
					var postId = $widget.data('post-id');
					
					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						type: 'POST',
						data: {
							action: 'swgtheme_submit_rating',
							post_id: postId,
							rating: rating,
							nonce: '<?php echo wp_create_nonce( 'swgtheme_rating' ); ?>'
						},
						success: function(response) {
							if (response.success) {
								// Update stars
								$this.siblings('.star').removeClass('filled');
								$this.prevAll('.star').addBack().addClass('filled');
								
								// Update summary
								var avgStars = $widget.find('.rating-stars.average .star');
								var avgRating = parseFloat(response.data.average);
								
								avgStars.removeClass('filled');
								avgStars.slice(0, Math.round(avgRating)).addClass('filled');
								
								$widget.find('.rating-text').text(
									response.data.average + ' out of 5 stars (' + response.data.total + ' ratings)'
								);
								
								// Show confirmation
								var $confirmation = $('<div class="rating-confirmation">Thank you for rating!</div>');
								$widget.append($confirmation);
								setTimeout(function() {
									$confirmation.fadeOut(function() {
										$(this).remove();
									});
								}, 2000);
							} else {
								alert(response.data.message);
							}
						}
					});
				});
				
				// Hover effect
				$('.rating-stars.interactive .star').hover(
					function() {
						$(this).prevAll('.star').addBack().addClass('hover');
					},
					function() {
						$('.rating-stars.interactive .star').removeClass('hover');
					}
				);
			});
			</script>
			<?php
		}
	}
	add_action( 'wp_footer', 'swgtheme_rating_script' );
}

// ============================================
// ADVANCED SEO & ANALYTICS
// ============================================

// Register Advanced SEO settings
function swgtheme_register_advanced_seo_settings() {
	// Breadcrumb Schema
	register_setting( 'swgtheme-settings-group', 'swgtheme_breadcrumb_schema_enable' );
	
	// FAQ Schema
	register_setting( 'swgtheme-settings-group', 'swgtheme_faq_schema_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_faq_post_types' );
	
	// Article Schema Enhancement
	register_setting( 'swgtheme-settings-group', 'swgtheme_article_schema_enhance' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_article_publisher_logo' );
	
	// Local Business Schema
	register_setting( 'swgtheme-settings-group', 'swgtheme_local_business_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_business_name' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_business_type' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_business_address' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_business_phone' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_business_hours' );
	
	// Event Schema
	register_setting( 'swgtheme-settings-group', 'swgtheme_event_schema_enable' );
	
	// Review Schema
	register_setting( 'swgtheme-settings-group', 'swgtheme_review_schema_enable' );
	
	// Scroll Depth Tracking
	register_setting( 'swgtheme-settings-group', 'swgtheme_scroll_tracking_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_scroll_tracking_points' );
	
	// Auto Meta Descriptions
	register_setting( 'swgtheme-settings-group', 'swgtheme_auto_meta_desc' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_meta_desc_length' );
}
add_action( 'admin_init', 'swgtheme_register_advanced_seo_settings' );

// Add Advanced SEO admin menu page
function swgtheme_add_seo_admin_page() {
	add_theme_page(
		__( 'Advanced SEO', 'swgtheme' ),
		__( 'Advanced SEO', 'swgtheme' ),
		'manage_options',
		'swgtheme-advanced-seo',
		'swgtheme_advanced_seo_admin_page'
	);
}
add_action( 'admin_menu', 'swgtheme_add_seo_admin_page' );

function swgtheme_advanced_seo_admin_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Advanced SEO & Analytics', 'swgtheme' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'swgtheme-settings-group' );
			do_settings_sections( 'swgtheme-settings-group' );
			?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Structured Data / Schema Markup', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Breadcrumb Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_breadcrumb_schema_enable" value="1" <?php checked( get_option('swgtheme_breadcrumb_schema_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add JSON-LD breadcrumb structured data', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'FAQ Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_faq_schema_enable" value="1" <?php checked( get_option('swgtheme_faq_schema_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Auto-detect FAQ sections and add schema', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="faq-post-types" style="<?php echo get_option('swgtheme_faq_schema_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'FAQ Post Types', 'swgtheme' ); ?></th>
					<td>
						<input type="text" name="swgtheme_faq_post_types" value="<?php echo esc_attr( get_option('swgtheme_faq_post_types', 'post,page') ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Comma-separated post types (e.g., post,page,faq)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enhanced Article Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_article_schema_enhance" value="1" <?php checked( get_option('swgtheme_article_schema_enhance', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add detailed article metadata for better SEO', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="publisher-logo" style="<?php echo get_option('swgtheme_article_schema_enhance', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Publisher Logo URL', 'swgtheme' ); ?></th>
					<td>
						<input type="url" name="swgtheme_article_publisher_logo" value="<?php echo esc_url( get_option('swgtheme_article_publisher_logo', '') ); ?>" class="regular-text" />
						<button type="button" class="button upload-image-button"><?php esc_html_e( 'Upload', 'swgtheme' ); ?></button>
						<p class="description"><?php esc_html_e( 'Required for Article schema (recommended: 600x60px)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Review Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_review_schema_enable" value="1" <?php checked( get_option('swgtheme_review_schema_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add review/rating schema for review posts', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Event Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_event_schema_enable" value="1" <?php checked( get_option('swgtheme_event_schema_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add event schema for event posts', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Local Business SEO', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Local Business Schema', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_local_business_enable" value="1" <?php checked( get_option('swgtheme_local_business_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add local business structured data', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="business-name" style="<?php echo get_option('swgtheme_local_business_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Business Name', 'swgtheme' ); ?></th>
					<td>
						<input type="text" name="swgtheme_business_name" value="<?php echo esc_attr( get_option('swgtheme_business_name', get_bloginfo('name')) ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr valign="top" id="business-type" style="<?php echo get_option('swgtheme_local_business_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Business Type', 'swgtheme' ); ?></th>
					<td>
						<select name="swgtheme_business_type">
							<option value="LocalBusiness" <?php selected( get_option('swgtheme_business_type', 'LocalBusiness'), 'LocalBusiness' ); ?>>Local Business</option>
							<option value="Restaurant" <?php selected( get_option('swgtheme_business_type'), 'Restaurant' ); ?>>Restaurant</option>
							<option value="Store" <?php selected( get_option('swgtheme_business_type'), 'Store' ); ?>>Store</option>
							<option value="ProfessionalService" <?php selected( get_option('swgtheme_business_type'), 'ProfessionalService' ); ?>>Professional Service</option>
							<option value="HealthAndBeautyBusiness" <?php selected( get_option('swgtheme_business_type'), 'HealthAndBeautyBusiness' ); ?>>Health & Beauty</option>
						</select>
					</td>
				</tr>
				<tr valign="top" id="business-address" style="<?php echo get_option('swgtheme_local_business_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Business Address', 'swgtheme' ); ?></th>
					<td>
						<textarea name="swgtheme_business_address" rows="3" class="large-text"><?php echo esc_textarea( get_option('swgtheme_business_address', '') ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Street, City, State, ZIP, Country', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top" id="business-phone" style="<?php echo get_option('swgtheme_local_business_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Phone Number', 'swgtheme' ); ?></th>
					<td>
						<input type="tel" name="swgtheme_business_phone" value="<?php echo esc_attr( get_option('swgtheme_business_phone', '') ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr valign="top" id="business-hours" style="<?php echo get_option('swgtheme_local_business_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Business Hours', 'swgtheme' ); ?></th>
					<td>
						<textarea name="swgtheme_business_hours" rows="3" class="large-text"><?php echo esc_textarea( get_option('swgtheme_business_hours', 'Mo-Fr 09:00-17:00') ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Format: Mo-Fr 09:00-17:00, Sa 10:00-14:00', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Analytics & Tracking', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Scroll Depth Tracking', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_scroll_tracking_enable" value="1" <?php checked( get_option('swgtheme_scroll_tracking_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Track how far users scroll on pages', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="scroll-points" style="<?php echo get_option('swgtheme_scroll_tracking_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Tracking Points', 'swgtheme' ); ?></th>
					<td>
						<input type="text" name="swgtheme_scroll_tracking_points" value="<?php echo esc_attr( get_option('swgtheme_scroll_tracking_points', '25,50,75,90,100') ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Comma-separated percentages (e.g., 25,50,75,100)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Auto-Generated Content', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Auto Meta Descriptions', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_auto_meta_desc" value="1" <?php checked( get_option('swgtheme_auto_meta_desc', '0'), '1' ); ?> />
							<?php esc_html_e( 'Generate meta descriptions from content excerpts', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="meta-desc-length" style="<?php echo get_option('swgtheme_auto_meta_desc', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Description Length', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_meta_desc_length" value="<?php echo esc_attr( get_option('swgtheme_meta_desc_length', '155') ); ?>" min="100" max="300" />
						<p class="description"><?php esc_html_e( 'Characters (recommended: 150-160)', 'swgtheme' ); ?></p>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
		
		<script>
		jQuery(document).ready(function($) {
			// FAQ Schema toggle
			$('input[name="swgtheme_faq_schema_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#faq-post-types').fadeIn();
				} else {
					$('#faq-post-types').fadeOut();
				}
			});
			
			// Article Schema toggle
			$('input[name="swgtheme_article_schema_enhance"]').change(function() {
				if ($(this).is(':checked')) {
					$('#publisher-logo').fadeIn();
				} else {
					$('#publisher-logo').fadeOut();
				}
			});
			
			// Local Business toggle
			$('input[name="swgtheme_local_business_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#business-name, #business-type, #business-address, #business-phone, #business-hours').fadeIn();
				} else {
					$('#business-name, #business-type, #business-address, #business-phone, #business-hours').fadeOut();
				}
			});
			
			// Scroll Tracking toggle
			$('input[name="swgtheme_scroll_tracking_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#scroll-points').fadeIn();
				} else {
					$('#scroll-points').fadeOut();
				}
			});
			
			// Auto Meta toggle
			$('input[name="swgtheme_auto_meta_desc"]').change(function() {
				if ($(this).is(':checked')) {
					$('#meta-desc-length').fadeIn();
				} else {
					$('#meta-desc-length').fadeOut();
				}
			});
			
			// Image upload button
			$('.upload-image-button').click(function(e) {
				e.preventDefault();
				var button = $(this);
				var input = button.prev('input');
				
				var customUploader = wp.media({
					title: 'Select Publisher Logo',
					button: { text: 'Use this image' },
					multiple: false
				}).on('select', function() {
					var attachment = customUploader.state().get('selection').first().toJSON();
					input.val(attachment.url);
				}).open();
			});
		});
		</script>
	</div>
	<?php
}

// Breadcrumb Schema
if ( get_option( 'swgtheme_breadcrumb_schema_enable', '0' ) === '1' ) {
	function swgtheme_breadcrumb_schema() {
		if ( is_front_page() ) {
			return;
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array(
			'@type' => 'ListItem',
			'position' => 1,
			'name' => 'Home',
			'item' => home_url()
		);
		
		$position = 2;
		
		if ( is_category() || is_single() ) {
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$category = $categories[0];
				$breadcrumbs[] = array(
					'@type' => 'ListItem',
					'position' => $position++,
					'name' => $category->name,
					'item' => get_category_link( $category->term_id )
				);
			}
		}
		
		if ( is_single() || is_page() ) {
			$breadcrumbs[] = array(
				'@type' => 'ListItem',
				'position' => $position,
				'name' => get_the_title(),
				'item' => get_permalink()
			);
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $breadcrumbs
		);
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	}
	add_action( 'wp_head', 'swgtheme_breadcrumb_schema' );
}

// FAQ Schema
if ( get_option( 'swgtheme_faq_schema_enable', '0' ) === '1' ) {
	function swgtheme_faq_schema() {
		if ( ! is_singular() ) {
			return;
		}
		
		$allowed_types = explode( ',', get_option( 'swgtheme_faq_post_types', 'post,page' ) );
		$allowed_types = array_map( 'trim', $allowed_types );
		
		if ( ! in_array( get_post_type(), $allowed_types ) ) {
			return;
		}
		
		$content = get_the_content();
		
		// Simple FAQ detection - looking for h3/h4 as questions
		preg_match_all( '/<h[34][^>]*>(.*?)<\/h[34]>(.*?)(?=<h[34]|$)/is', $content, $matches, PREG_SET_ORDER );
		
		if ( count( $matches ) < 2 ) {
			return;
		}
		
		$faq_items = array();
		
		foreach ( $matches as $match ) {
			$question = wp_strip_all_tags( $match[1] );
			$answer = wp_strip_all_tags( $match[2] );
			
			if ( strlen( $question ) > 10 && strlen( $answer ) > 20 ) {
				$faq_items[] = array(
					'@type' => 'Question',
					'name' => $question,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => substr( $answer, 0, 500 )
					)
				);
			}
		}
		
		if ( ! empty( $faq_items ) ) {
			$schema = array(
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => $faq_items
			);
			
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
		}
	}
	add_action( 'wp_head', 'swgtheme_faq_schema' );
}

// Enhanced Article Schema
if ( get_option( 'swgtheme_article_schema_enhance', '0' ) === '1' ) {
	function swgtheme_enhanced_article_schema() {
		if ( ! is_single() ) {
			return;
		}
		
		$publisher_logo = get_option( 'swgtheme_article_publisher_logo', '' );
		if ( empty( $publisher_logo ) ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title(),
			'description' => get_the_excerpt(),
			'image' => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '',
			'datePublished' => get_the_date( 'c' ),
			'dateModified' => get_the_modified_date( 'c' ),
			'author' => array(
				'@type' => 'Person',
				'name' => get_the_author()
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name' => get_bloginfo( 'name' ),
				'logo' => array(
					'@type' => 'ImageObject',
					'url' => $publisher_logo
				)
			)
		);
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	}
	add_action( 'wp_head', 'swgtheme_enhanced_article_schema' );
}

// Local Business Schema
if ( get_option( 'swgtheme_local_business_enable', '0' ) === '1' ) {
	function swgtheme_local_business_schema() {
		$business_name = get_option( 'swgtheme_business_name', get_bloginfo('name') );
		$business_type = get_option( 'swgtheme_business_type', 'LocalBusiness' );
		$address = get_option( 'swgtheme_business_address', '' );
		$phone = get_option( 'swgtheme_business_phone', '' );
		
		if ( empty( $address ) ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => $business_type,
			'name' => $business_name,
			'address' => $address,
			'telephone' => $phone,
			'url' => home_url()
		);
		
		$hours = get_option( 'swgtheme_business_hours', '' );
		if ( ! empty( $hours ) ) {
			$schema['openingHours'] = $hours;
		}
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	}
	add_action( 'wp_head', 'swgtheme_local_business_schema' );
}

// Review Schema
if ( get_option( 'swgtheme_review_schema_enable', '0' ) === '1' ) {
	function swgtheme_review_schema() {
		if ( ! is_single() ) {
			return;
		}
		
		// Check if post has rating
		$rating = get_post_meta( get_the_ID(), 'review_rating', true );
		if ( empty( $rating ) ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Review',
			'itemReviewed' => array(
				'@type' => 'Thing',
				'name' => get_the_title()
			),
			'author' => array(
				'@type' => 'Person',
				'name' => get_the_author()
			),
			'reviewRating' => array(
				'@type' => 'Rating',
				'ratingValue' => $rating,
				'bestRating' => '5'
			),
			'datePublished' => get_the_date( 'c' )
		);
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	}
	add_action( 'wp_head', 'swgtheme_review_schema' );
}

// Event Schema
if ( get_option( 'swgtheme_event_schema_enable', '0' ) === '1' ) {
	function swgtheme_event_schema() {
		if ( ! is_single() || get_post_type() !== 'event' ) {
			return;
		}
		
		$start_date = get_post_meta( get_the_ID(), 'event_start_date', true );
		$location = get_post_meta( get_the_ID(), 'event_location', true );
		
		if ( empty( $start_date ) ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Event',
			'name' => get_the_title(),
			'description' => get_the_excerpt(),
			'startDate' => $start_date,
			'location' => array(
				'@type' => 'Place',
				'name' => $location ?: 'TBA'
			),
			'organizer' => array(
				'@type' => 'Organization',
				'name' => get_bloginfo( 'name' ),
				'url' => home_url()
			)
		);
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	}
	add_action( 'wp_head', 'swgtheme_event_schema' );
}

// Scroll Depth Tracking
if ( get_option( 'swgtheme_scroll_tracking_enable', '0' ) === '1' ) {
	function swgtheme_scroll_tracking_script() {
		if ( ! is_singular() ) {
			return;
		}
		
		$tracking_points = get_option( 'swgtheme_scroll_tracking_points', '25,50,75,90,100' );
		$points = array_map( 'intval', explode( ',', $tracking_points ) );
		?>
		<script>
		(function() {
			var tracked = {};
			var checkPoints = <?php echo wp_json_encode( $points ); ?>;
			
			function checkScrollDepth() {
				var windowHeight = window.innerHeight;
				var documentHeight = document.documentElement.scrollHeight;
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				var scrollPercent = Math.round((scrollTop + windowHeight) / documentHeight * 100);
				
				checkPoints.forEach(function(point) {
					if (scrollPercent >= point && !tracked[point]) {
						tracked[point] = true;
						
						// Log to console (can be sent to analytics)
						console.log('Scroll depth: ' + point + '%');
						
						// Send to Google Analytics if available
						if (typeof gtag !== 'undefined') {
							gtag('event', 'scroll_depth', {
								'event_category': 'engagement',
								'event_label': point + '%',
								'value': point
							});
						}
						
						// Send to Google Analytics (Universal)
						if (typeof ga !== 'undefined') {
							ga('send', 'event', 'Scroll Depth', point + '%', location.pathname);
						}
					}
				});
			}
			
			var scrollTimeout;
			window.addEventListener('scroll', function() {
				clearTimeout(scrollTimeout);
				scrollTimeout = setTimeout(checkScrollDepth, 100);
			});
			
			// Check on load
			checkScrollDepth();
		})();
		</script>
		<?php
	}
	add_action( 'wp_footer', 'swgtheme_scroll_tracking_script' );
}

// Auto Meta Descriptions
if ( get_option( 'swgtheme_auto_meta_desc', '0' ) === '1' ) {
	function swgtheme_auto_meta_description() {
		if ( is_singular() && ! has_excerpt() ) {
			$content = get_the_content();
			$content = wp_strip_all_tags( $content );
			$content = preg_replace( '/\s+/', ' ', $content );
			
			$length = intval( get_option( 'swgtheme_meta_desc_length', '155' ) );
			$description = substr( $content, 0, $length );
			
			// Cut at last complete word
			$last_space = strrpos( $description, ' ' );
			if ( $last_space !== false ) {
				$description = substr( $description, 0, $last_space );
			}
			
			echo '<meta name="description" content="' . esc_attr( $description . '...' ) . '">' . "\n";
		} elseif ( has_excerpt() ) {
			echo '<meta name="description" content="' . esc_attr( get_the_excerpt() ) . '">' . "\n";
		}
	}
	add_action( 'wp_head', 'swgtheme_auto_meta_description', 1 );
}

// ============================================
// PERFORMANCE & OPTIMIZATION
// ============================================

// Register Performance settings
function swgtheme_register_performance_settings() {
	// Security Headers
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_security_headers' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_x_frame_options' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_content_security_policy' );
	
	// Caching
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_browser_cache' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_cache_duration' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_gzip' );
	
	// Minification
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_css_minify' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_js_minify' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_defer_js' );
	
	// Image Optimization
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_webp' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_auto_convert_webp' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_webp_quality' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_image_quality' );
	
	// Rate Limiting
	register_setting( 'swgtheme-settings-group', 'swgtheme_enable_rate_limiting' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_rate_limit_max' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_rate_limit_window' );
	
	// Database Optimization
	register_setting( 'swgtheme-settings-group', 'swgtheme_optimize_queries' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_limit_revisions' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_revision_limit' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_increase_autosave' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_autosave_interval' );
	
	// Security
	register_setting( 'swgtheme-settings-group', 'swgtheme_prevent_enum' );
	
	// WebP Support
	register_setting( 'swgtheme-settings-group', 'swgtheme_webp_enable' );
	
	// Lazy Load Videos
	register_setting( 'swgtheme-settings-group', 'swgtheme_lazy_videos_enable' );
	
	// Resource Hints
	register_setting( 'swgtheme-settings-group', 'swgtheme_resource_hints_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_dns_prefetch_urls' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_preconnect_urls' );
	
	// Font Display
	register_setting( 'swgtheme-settings-group', 'swgtheme_font_display_swap' );
	
	// Minify HTML
	register_setting( 'swgtheme-settings-group', 'swgtheme_minify_html' );
	
	// Query Monitor
	register_setting( 'swgtheme-settings-group', 'swgtheme_query_monitor_enable' );
	register_setting( 'swgtheme-settings-group', 'swgtheme_slow_query_threshold' );
	
	// Asset Versioning
	register_setting( 'swgtheme-settings-group', 'swgtheme_asset_versioning' );
	
	// Disable Emojis
	register_setting( 'swgtheme-settings-group', 'swgtheme_disable_emojis' );
	
	// Disable Embeds
	register_setting( 'swgtheme-settings-group', 'swgtheme_disable_embeds' );
}
add_action( 'admin_init', 'swgtheme_register_performance_settings' );

// Add Performance admin menu page
function swgtheme_add_performance_admin_page() {
	add_theme_page(
		__( 'Performance', 'swgtheme' ),
		__( 'Performance', 'swgtheme' ),
		'manage_options',
		'swgtheme-performance',
		'swgtheme_performance_admin_page'
	);
}
add_action( 'admin_menu', 'swgtheme_add_performance_admin_page' );

function swgtheme_performance_admin_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Performance & Optimization', 'swgtheme' ); ?></h1>
		
		<h2 class="nav-tab-wrapper">
			<a href="#security" class="nav-tab nav-tab-active">🛡️ Security</a>
			<a href="#caching" class="nav-tab">⚡ Caching</a>
			<a href="#optimization" class="nav-tab">🚀 Optimization</a>
			<a href="#images" class="nav-tab">🖼️ Images</a>
		</h2>
		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'swgtheme-settings-group' );
			do_settings_sections( 'swgtheme-settings-group' );
			?>
			
			<!-- Security Tab -->
			<div id="security" class="tab-content">
				<table class="form-table">
					<tr>
						<th colspan="2"><h2>🔒 Security Headers</h2></th>
					</tr>
					<tr>
						<th scope="row">Enable Security Headers</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_security_headers" value="1" <?php checked( get_option('swgtheme_enable_security_headers', '1'), '1' ); ?> />
								Enable HTTP security headers (Recommended)
							</label>
							<p class="description">Adds X-Frame-Options, X-XSS-Protection, CSP, HSTS, and more</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Content Security Policy</th>
						<td>
							<textarea name="swgtheme_content_security_policy" rows="3" class="large-text" placeholder="Leave empty for default policy"><?php echo esc_textarea( get_option('swgtheme_content_security_policy', '') ); ?></textarea>
							<p class="description">Advanced: Custom CSP header (leave empty for secure defaults)</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Prevent Username Enumeration</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_prevent_enum" value="1" <?php checked( get_option('swgtheme_prevent_enum', '1'), '1' ); ?> />
								Block author ID enumeration attempts
							</label>
						</td>
					</tr>
					<tr>
						<th colspan="2"><h2>⏱️ Rate Limiting</h2></th>
					</tr>
					<tr>
						<th scope="row">Enable AJAX Rate Limiting</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_rate_limiting" value="1" <?php checked( get_option('swgtheme_enable_rate_limiting', '0'), '1' ); ?> />
								Limit AJAX requests per IP address
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Max Requests</th>
						<td>
							<input type="number" name="swgtheme_rate_limit_max" value="<?php echo esc_attr( get_option('swgtheme_rate_limit_max', '60') ); ?>" min="10" max="1000" />
							<p class="description">Maximum requests per time window</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Time Window (seconds)</th>
						<td>
							<input type="number" name="swgtheme_rate_limit_window" value="<?php echo esc_attr( get_option('swgtheme_rate_limit_window', '60') ); ?>" min="10" max="3600" />
						</td>
					</tr>
				</table>
			</div>
			
			<!-- Caching Tab -->
			<div id="caching" class="tab-content" style="display:none;">
				<table class="form-table">
					<tr>
						<th colspan="2"><h2>💾 Browser Caching</h2></th>
					</tr>
					<tr>
						<th scope="row">Enable Browser Caching</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_browser_cache" value="1" <?php checked( get_option('swgtheme_enable_browser_cache', '1'), '1' ); ?> />
								Add cache-control headers for static pages
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Cache Duration (seconds)</th>
						<td>
							<input type="number" name="swgtheme_cache_duration" value="<?php echo esc_attr( get_option('swgtheme_cache_duration', '604800') ); ?>" min="3600" max="31536000" />
							<p class="description">Default: 604800 (7 days)</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Enable Gzip Compression</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_gzip" value="1" <?php checked( get_option('swgtheme_enable_gzip', '1'), '1' ); ?> />
								Compress HTML, CSS, and JavaScript
							</label>
							<p class="description">Reduces file sizes by 70-90%</p>
						</td>
					</tr>
				</table>
			</div>
			
			<!-- Optimization Tab -->
			<div id="optimization" class="tab-content" style="display:none;">
				<table class="form-table">
					<tr>
						<th colspan="2"><h2>📦 Minification</h2></th>
					</tr>
					<tr>
						<th scope="row">Minify CSS</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_css_minify" value="1" <?php checked( get_option('swgtheme_enable_css_minify', '0'), '1' ); ?> />
								Remove whitespace and comments from CSS
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Minify JavaScript</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_js_minify" value="1" <?php checked( get_option('swgtheme_enable_js_minify', '0'), '1' ); ?> />
								Remove whitespace and comments from JS
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Defer JavaScript</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_defer_js" value="1" <?php checked( get_option('swgtheme_enable_defer_js', '0'), '1' ); ?> />
								Load JavaScript files after page content
							</label>
							<p class="description">Improves initial page load time</p>
						</td>
					</tr>
					<tr>
						<th colspan="2"><h2>🗄️ Database Optimization</h2></th>
					</tr>
					<tr>
						<th scope="row">Optimize Database Queries</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_optimize_queries" value="1" <?php checked( get_option('swgtheme_optimize_queries', '0'), '1' ); ?> />
								Remove unnecessary WordPress queries
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Limit Post Revisions</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_limit_revisions" value="1" <?php checked( get_option('swgtheme_limit_revisions', '0'), '1' ); ?> />
								Limit number of saved post revisions
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Revision Limit</th>
						<td>
							<input type="number" name="swgtheme_revision_limit" value="<?php echo esc_attr( get_option('swgtheme_revision_limit', '5') ); ?>" min="1" max="20" />
						</td>
					</tr>
					<tr>
						<th scope="row">Increase Autosave Interval</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_increase_autosave" value="1" <?php checked( get_option('swgtheme_increase_autosave', '0'), '1' ); ?> />
								Reduce autosave frequency
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Autosave Interval (seconds)</th>
						<td>
							<input type="number" name="swgtheme_autosave_interval" value="<?php echo esc_attr( get_option('swgtheme_autosave_interval', '300') ); ?>" min="60" max="600" />
							<p class="description">Default: 60 seconds</p>
						</td>
					</tr>
					<tr>
						<th colspan="2"><h2>🧹 Cleanup</h2></th>
					</tr>
					<tr>
						<th scope="row">Disable Emojis</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_emojis" value="1" <?php checked( get_option('swgtheme_disable_emojis', '0'), '1' ); ?> />
								Remove emoji scripts and styles
							</label>
							<p class="description">Saves ~15KB per page</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Disable Embeds</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_embeds" value="1" <?php checked( get_option('swgtheme_disable_embeds', '0'), '1' ); ?> />
								Disable WordPress embed functionality
							</label>
							<p class="description">Reduces HTTP requests</p>
						</td>
					</tr>
				</table>
			</div>
			
			<!-- Images Tab -->
			<div id="images" class="tab-content" style="display:none;">
				<table class="form-table">
					<tr>
						<th colspan="2"><h2>🖼️ Image Optimization</h2></th>
					</tr>
					<tr>
						<th scope="row">Enable WebP Support</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_webp" value="1" <?php checked( get_option('swgtheme_enable_webp', '1'), '1' ); ?> />
								Allow WebP image uploads
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Auto-Convert to WebP</th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_auto_convert_webp" value="1" <?php checked( get_option('swgtheme_auto_convert_webp', '0'), '1' ); ?> />
								Automatically convert JPG/PNG to WebP on upload
							</label>
							<p class="description">Requires GD library with WebP support</p>
						</td>
					</tr>
					<tr>
						<th scope="row">WebP Quality</th>
						<td>
							<input type="number" name="swgtheme_webp_quality" value="<?php echo esc_attr( get_option('swgtheme_webp_quality', '80') ); ?>" min="1" max="100" />
							<p class="description">1-100, recommended: 80</p>
						</td>
					</tr>
					<tr>
						<th scope="row">JPEG Quality</th>
						<td>
							<input type="number" name="swgtheme_image_quality" value="<?php echo esc_attr( get_option('swgtheme_image_quality', '82') ); ?>" min="1" max="100" />
							<p class="description">WordPress default: 82</p>
						</td>
					</tr>
				</table>
			</div>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Legacy Settings', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'WebP Image Support', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_webp_enable" value="1" <?php checked( get_option('swgtheme_webp_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Serve WebP images when supported by browser', 'swgtheme' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Automatically uses WebP format for faster loading', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top" id="webp-quality" style="<?php echo get_option('swgtheme_webp_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'WebP Quality', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_webp_quality" value="<?php echo esc_attr( get_option('swgtheme_webp_quality', '80') ); ?>" min="1" max="100" />
						<p class="description"><?php esc_html_e( 'Quality percentage (1-100, recommended: 80)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Lazy Loading', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Lazy Load Videos & Iframes', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_lazy_videos_enable" value="1" <?php checked( get_option('swgtheme_lazy_videos_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Defer loading of videos and iframes until needed', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Resource Hints', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Resource Hints', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_resource_hints_enable" value="1" <?php checked( get_option('swgtheme_resource_hints_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add DNS-prefetch, preconnect hints for faster loading', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="dns-prefetch" style="<?php echo get_option('swgtheme_resource_hints_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'DNS Prefetch URLs', 'swgtheme' ); ?></th>
					<td>
						<textarea name="swgtheme_dns_prefetch_urls" rows="3" class="large-text"><?php echo esc_textarea( get_option('swgtheme_dns_prefetch_urls', "//fonts.googleapis.com\n//fonts.gstatic.com") ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One URL per line (e.g., //fonts.googleapis.com)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top" id="preconnect" style="<?php echo get_option('swgtheme_resource_hints_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Preconnect URLs', 'swgtheme' ); ?></th>
					<td>
						<textarea name="swgtheme_preconnect_urls" rows="3" class="large-text"><?php echo esc_textarea( get_option('swgtheme_preconnect_urls', "https://fonts.gstatic.com") ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One URL per line (use for critical resources)', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Font Optimization', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Font Display Swap', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_font_display_swap" value="1" <?php checked( get_option('swgtheme_font_display_swap', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add font-display: swap to prevent invisible text', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'HTML Optimization', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Minify HTML Output', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_minify_html" value="1" <?php checked( get_option('swgtheme_minify_html', '0'), '1' ); ?> />
							<?php esc_html_e( 'Remove whitespace and comments from HTML', 'swgtheme' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Reduces HTML size by ~20-30%', 'swgtheme' ); ?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Database & Monitoring', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Database Query Monitor', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_query_monitor_enable" value="1" <?php checked( get_option('swgtheme_query_monitor_enable', '0'), '1' ); ?> />
							<?php esc_html_e( 'Show slow queries in admin dashboard widget', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="slow-query" style="<?php echo get_option('swgtheme_query_monitor_enable', '0') !== '1' ? 'display:none;' : ''; ?>">
					<th scope="row"><?php esc_html_e( 'Slow Query Threshold', 'swgtheme' ); ?></th>
					<td>
						<input type="number" name="swgtheme_slow_query_threshold" value="<?php echo esc_attr( get_option('swgtheme_slow_query_threshold', '0.05') ); ?>" step="0.01" min="0.01" max="1" /> seconds
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Asset Management', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Asset Versioning', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_asset_versioning" value="1" <?php checked( get_option('swgtheme_asset_versioning', '0'), '1' ); ?> />
							<?php esc_html_e( 'Add file modification time to asset URLs for cache busting', 'swgtheme' ); ?>
						</label>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row" colspan="2">
						<h2><?php esc_html_e( 'Disable Unnecessary Features', 'swgtheme' ); ?></h2>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Disable Emojis', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_emojis" value="1" <?php checked( get_option('swgtheme_disable_emojis', '0'), '1' ); ?> />
							<?php esc_html_e( 'Remove emoji scripts and styles', 'swgtheme' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Saves ~15KB per page load', 'swgtheme' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Disable Embeds', 'swgtheme' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_embeds" value="1" <?php checked( get_option('swgtheme_disable_embeds', '0'), '1' ); ?> />
							<?php esc_html_e( 'Remove oEmbed scripts', 'swgtheme' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Saves ~10KB per page load', 'swgtheme' ); ?></p>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
		
		<script>
		jQuery(document).ready(function($) {
			// Tab navigation
			$('.nav-tab').click(function(e) {
				e.preventDefault();
				var target = $(this).attr('href');
				
				$('.nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				
				$('.tab-content').hide();
				$(target).show();
			});
			
			// WebP toggle
			$('input[name="swgtheme_webp_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#webp-quality').fadeIn();
				} else {
					$('#webp-quality').fadeOut();
				}
			});
			
			// Resource Hints toggle
			$('input[name="swgtheme_resource_hints_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#dns-prefetch, #preconnect').fadeIn();
				} else {
					$('#dns-prefetch, #preconnect').fadeOut();
				}
			});
			
			// Query Monitor toggle
			$('input[name="swgtheme_query_monitor_enable"]').change(function() {
				if ($(this).is(':checked')) {
					$('#slow-query').fadeIn();
				} else {
					$('#slow-query').fadeOut();
				}
			});
		});
		</script>
	</div>
	<?php
}

// WebP Image Support
if ( get_option( 'swgtheme_webp_enable', '0' ) === '1' ) {
	function swgtheme_webp_upload_support( $mimes ) {
		$mimes['webp'] = 'image/webp';
		return $mimes;
	}
	add_filter( 'mime_types', 'swgtheme_webp_upload_support' );
	
	function swgtheme_webp_display_support( $result, $path ) {
		if ( $result === false ) {
			$displayable_image_types = array( IMAGETYPE_WEBP );
			$info = @getimagesize( $path );
			
			if ( empty( $info ) ) {
				$result = false;
			} elseif ( ! in_array( $info[2], $displayable_image_types ) ) {
				$result = false;
			} else {
				$result = true;
			}
		}
		
		return $result;
	}
	add_filter( 'file_is_displayable_image', 'swgtheme_webp_display_support', 10, 2 );
}

// Lazy Load Videos & Iframes
if ( get_option( 'swgtheme_lazy_videos_enable', '0' ) === '1' ) {
	function swgtheme_lazy_load_videos( $content ) {
		// Add loading="lazy" to iframes and videos
		$content = preg_replace( '/<iframe /i', '<iframe loading="lazy" ', $content );
		$content = preg_replace( '/<video /i', '<video loading="lazy" preload="none" ', $content );
		
		return $content;
	}
	add_filter( 'the_content', 'swgtheme_lazy_load_videos' );
	add_filter( 'widget_text', 'swgtheme_lazy_load_videos' );
}

// Resource Hints
if ( get_option( 'swgtheme_resource_hints_enable', '0' ) === '1' ) {
	function swgtheme_resource_hints() {
		// DNS Prefetch
		$dns_urls = get_option( 'swgtheme_dns_prefetch_urls', '' );
		if ( ! empty( $dns_urls ) ) {
			$urls = explode( "\n", $dns_urls );
			foreach ( $urls as $url ) {
				$url = trim( $url );
				if ( ! empty( $url ) ) {
					echo '<link rel="dns-prefetch" href="' . esc_url( $url ) . '">' . "\n";
				}
			}
		}
		
		// Preconnect
		$preconnect_urls = get_option( 'swgtheme_preconnect_urls', '' );
		if ( ! empty( $preconnect_urls ) ) {
			$urls = explode( "\n", $preconnect_urls );
			foreach ( $urls as $url ) {
				$url = trim( $url );
				if ( ! empty( $url ) ) {
					echo '<link rel="preconnect" href="' . esc_url( $url ) . '" crossorigin>' . "\n";
				}
			}
		}
	}
	add_action( 'wp_head', 'swgtheme_resource_hints', 1 );
}

// Font Display Swap
if ( get_option( 'swgtheme_font_display_swap', '0' ) === '1' ) {
	function swgtheme_font_display_swap( $html ) {
		return str_replace( 'fonts.googleapis.com/css', 'fonts.googleapis.com/css?display=swap', $html );
	}
	add_filter( 'style_loader_tag', 'swgtheme_font_display_swap' );
}

// Minify HTML Output
if ( get_option( 'swgtheme_minify_html', '0' ) === '1' ) {
	function swgtheme_minify_html_output( $buffer ) {
		// Don't minify if user is logged in
		if ( is_user_logged_in() ) {
			return $buffer;
		}
		
		// Remove comments
		$buffer = preg_replace( '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer );
		
		// Remove whitespace
		$buffer = preg_replace( '/\s+/', ' ', $buffer );
		$buffer = preg_replace( '/>\s+</', '><', $buffer );
		
		// Remove whitespace around tags
		$buffer = preg_replace( '/\s+(<\/?(?:a|b|i|em|strong|span|div|p|h[1-6]|ul|ol|li|table|tr|td|th|thead|tbody)[^>]*>)\s+/', '$1', $buffer );
		
		return $buffer;
	}
	
	function swgtheme_start_html_minify() {
		ob_start( 'swgtheme_minify_html_output' );
	}
	add_action( 'template_redirect', 'swgtheme_start_html_minify', 1 );
}

// Database Query Monitor
if ( get_option( 'swgtheme_query_monitor_enable', '0' ) === '1' ) {
	// Track queries
	function swgtheme_track_slow_queries() {
		global $wpdb;
		
		if ( ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}
	}
	add_action( 'init', 'swgtheme_track_slow_queries', 1 );
	
	// Dashboard widget
	function swgtheme_query_monitor_widget() {
		wp_add_dashboard_widget(
			'swgtheme_query_monitor',
			__( 'Database Query Monitor', 'swgtheme' ),
			'swgtheme_query_monitor_display'
		);
	}
	add_action( 'wp_dashboard_setup', 'swgtheme_query_monitor_widget' );
	
	function swgtheme_query_monitor_display() {
		global $wpdb;
		
		if ( ! isset( $wpdb->queries ) || empty( $wpdb->queries ) ) {
			echo '<p>' . esc_html__( 'No queries recorded yet. Visit a page to see queries.', 'swgtheme' ) . '</p>';
			return;
		}
		
		$threshold = floatval( get_option( 'swgtheme_slow_query_threshold', '0.05' ) );
		$slow_queries = array();
		$total_time = 0;
		
		foreach ( $wpdb->queries as $query ) {
			$time = floatval( $query[1] );
			$total_time += $time;
			
			if ( $time >= $threshold ) {
				$slow_queries[] = array(
					'query' => $query[0],
					'time' => $time,
					'stack' => isset( $query[2] ) ? $query[2] : ''
				);
			}
		}
		
		// Sort by time (slowest first)
		usort( $slow_queries, function( $a, $b ) {
			return $b['time'] <=> $a['time'];
		});
		
		?>
		<div class="query-monitor-stats">
			<p><strong><?php esc_html_e( 'Total Queries:', 'swgtheme' ); ?></strong> <?php echo count( $wpdb->queries ); ?></p>
			<p><strong><?php esc_html_e( 'Total Time:', 'swgtheme' ); ?></strong> <?php echo number_format( $total_time, 4 ); ?>s</p>
			<p><strong><?php esc_html_e( 'Slow Queries:', 'swgtheme' ); ?></strong> <?php echo count( $slow_queries ); ?> (><?php echo $threshold; ?>s)</p>
		</div>
		
		<?php if ( ! empty( $slow_queries ) ) : ?>
			<h4><?php esc_html_e( 'Slowest Queries:', 'swgtheme' ); ?></h4>
			<table class="widefat" style="margin-top: 10px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time (s)', 'swgtheme' ); ?></th>
						<th><?php esc_html_e( 'Query', 'swgtheme' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( array_slice( $slow_queries, 0, 10 ) as $query ) : ?>
						<tr>
							<td style="color: <?php echo $query['time'] > 0.1 ? 'red' : 'orange'; ?>;">
								<strong><?php echo number_format( $query['time'], 4 ); ?></strong>
							</td>
							<td>
								<code style="font-size: 11px; display: block; max-width: 600px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
									<?php echo esc_html( substr( $query['query'], 0, 150 ) ); ?>
								</code>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p style="color: green;"><?php esc_html_e( '✓ No slow queries detected!', 'swgtheme' ); ?></p>
		<?php endif; ?>
		<?php
	}
}

// Asset Versioning
if ( get_option( 'swgtheme_asset_versioning', '0' ) === '1' ) {
	function swgtheme_asset_version( $src ) {
		// Only apply to local assets
		if ( strpos( $src, home_url() ) === false && strpos( $src, '/' ) === 0 ) {
			return $src;
		}
		
		// Get file path
		$file_path = str_replace( home_url(), ABSPATH, $src );
		
		// Remove query string
		$file_path = preg_replace( '/\?.*/', '', $file_path );
		
		// Check if file exists and add modification time
		if ( file_exists( $file_path ) ) {
			$mtime = filemtime( $file_path );
			$src = add_query_arg( 'ver', $mtime, $src );
		}
		
		return $src;
	}
	add_filter( 'style_loader_src', 'swgtheme_asset_version' );
	add_filter( 'script_loader_src', 'swgtheme_asset_version' );
}

// Disable Emojis
if ( get_option( 'swgtheme_disable_emojis', '0' ) === '1' ) {
	function swgtheme_disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );	
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', 'swgtheme_disable_emojis_tinymce' );
		add_filter( 'wp_resource_hints', 'swgtheme_disable_emojis_remove_dns_prefetch', 10, 2 );
	}
	add_action( 'init', 'swgtheme_disable_emojis' );
	
	function swgtheme_disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}
		return array();
	}
	
	function swgtheme_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}
}

// Disable Embeds
if ( get_option( 'swgtheme_disable_embeds', '0' ) === '1' ) {
	function swgtheme_disable_embeds() {
		// Remove the REST API endpoint
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		
		// Turn off oEmbed auto discovery
		add_filter( 'embed_oembed_discover', '__return_false' );
		
		// Don't filter oEmbed results
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		
		// Remove oEmbed discovery links
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		
		// Remove oEmbed-specific JavaScript from the front-end and back-end
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		
		// Remove all embeds rewrite rules
		add_filter( 'rewrite_rules_array', 'swgtheme_disable_embeds_rewrites' );
		
		// Remove filter of the oEmbed result before any HTTP requests are made
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
	}
	add_action( 'init', 'swgtheme_disable_embeds', 9999 );
	
	function swgtheme_disable_embeds_rewrites( $rules ) {
		foreach ( $rules as $rule => $rewrite ) {
			if ( strpos( $rewrite, 'embed=true' ) !== false ) {
				unset( $rules[ $rule ] );
			}
		}
		return $rules;
	}
}

/**
 * ==================================================================
 * USER EXPERIENCE & INTERACTION FEATURES
 * ==================================================================
 */

/**
 * Register UX & Interaction settings
 */
function swgtheme_register_ux_settings() {
	// Smooth Scroll
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_smooth_scroll' );
	
	// Back to Top Button
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_back_to_top' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_back_to_top_position' );
	
	// Reading Progress Bar
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_reading_progress' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_reading_progress_color' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_reading_progress_height' );
	
	// Image Lightbox
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_lightbox' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_lightbox_caption' );
	
	// Tooltips
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_tooltips' );
	
	// Scroll Animations
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_scroll_animations' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_animation_style' );
	
	// Sticky Header
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_sticky_header' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_sticky_shrink' );
	
	// Loading Screen
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_loading_screen' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_loading_logo' );
	
	// Parallax Effect
	register_setting( 'swgtheme_ux_group', 'swgtheme_enable_parallax' );
	
	// Copy Protection
	register_setting( 'swgtheme_ux_group', 'swgtheme_disable_right_click' );
	register_setting( 'swgtheme_ux_group', 'swgtheme_disable_text_selection' );
}
add_action( 'admin_init', 'swgtheme_register_ux_settings' );

/**
 * Add UX & Interaction menu page
 */
function swgtheme_add_ux_menu() {
	add_submenu_page(
		'themes.php',
		__( 'UX & Interaction', 'swgtheme' ),
		__( 'UX & Interaction', 'swgtheme' ),
		'manage_options',
		'swgtheme-ux',
		'swgtheme_ux_page'
	);
}
add_action( 'admin_menu', 'swgtheme_add_ux_menu' );

/**
 * UX & Interaction page callback
 */
function swgtheme_ux_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_ux_group' ); ?>
			
			<h2><?php esc_html_e( 'Navigation & Scrolling', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Smooth Scroll', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_smooth_scroll" value="1" <?php checked( get_option( 'swgtheme_enable_smooth_scroll', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable smooth scrolling for anchor links', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Smooth animated scroll when clicking anchor links', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Back to Top Button', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_back_to_top" id="swgtheme_enable_back_to_top_ux" value="1" <?php checked( get_option( 'swgtheme_enable_back_to_top', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show "Back to Top" button', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Floating button to quickly return to page top', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="back-to-top-position" style="display: <?php echo get_option( 'swgtheme_enable_back_to_top', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_back_to_top_position"><?php esc_html_e( 'Button Position', 'swgtheme' ); ?></label>
						</th>
						<td>
							<select name="swgtheme_back_to_top_position" id="swgtheme_back_to_top_position">
								<option value="bottom-right" <?php selected( get_option( 'swgtheme_back_to_top_position', 'bottom-right' ), 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'swgtheme' ); ?></option>
								<option value="bottom-left" <?php selected( get_option( 'swgtheme_back_to_top_position', 'bottom-right' ), 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'swgtheme' ); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Sticky Header', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_sticky_header" id="swgtheme_enable_sticky_header" value="1" <?php checked( get_option( 'swgtheme_enable_sticky_header', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Make header sticky on scroll', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Header stays visible when scrolling down', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="sticky-shrink" style="display: <?php echo get_option( 'swgtheme_enable_sticky_header', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Shrink on Scroll', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_sticky_shrink" value="1" <?php checked( get_option( 'swgtheme_sticky_shrink', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Reduce header size when sticky', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Reading Experience', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Reading Progress Bar', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_reading_progress" id="swgtheme_enable_reading_progress" value="1" <?php checked( get_option( 'swgtheme_enable_reading_progress', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show reading progress indicator', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Progress bar showing scroll position on posts', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="reading-progress-color" style="display: <?php echo get_option( 'swgtheme_enable_reading_progress', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_reading_progress_color"><?php esc_html_e( 'Progress Bar Color', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="color" name="swgtheme_reading_progress_color" id="swgtheme_reading_progress_color" value="<?php echo esc_attr( get_option( 'swgtheme_reading_progress_color', '#dc3545' ) ); ?>" />
						</td>
					</tr>
					
					<tr id="reading-progress-height" style="display: <?php echo get_option( 'swgtheme_enable_reading_progress', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_reading_progress_height"><?php esc_html_e( 'Bar Height', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="number" name="swgtheme_reading_progress_height" id="swgtheme_reading_progress_height" value="<?php echo esc_attr( get_option( 'swgtheme_reading_progress_height', '4' ) ); ?>" min="2" max="10" /> px
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Visual Effects', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Image Lightbox', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_lightbox" id="swgtheme_enable_lightbox" value="1" <?php checked( get_option( 'swgtheme_enable_lightbox', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable lightbox for images', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Click images to view in fullscreen overlay', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="lightbox-caption" style="display: <?php echo get_option( 'swgtheme_enable_lightbox', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Show Captions', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_lightbox_caption" value="1" <?php checked( get_option( 'swgtheme_lightbox_caption', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Display image captions in lightbox', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Scroll Animations', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_scroll_animations" id="swgtheme_enable_scroll_animations" value="1" <?php checked( get_option( 'swgtheme_enable_scroll_animations', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Animate elements on scroll', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Elements fade/slide in when scrolling into view', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="animation-style" style="display: <?php echo get_option( 'swgtheme_enable_scroll_animations', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_animation_style"><?php esc_html_e( 'Animation Style', 'swgtheme' ); ?></label>
						</th>
						<td>
							<select name="swgtheme_animation_style" id="swgtheme_animation_style">
								<option value="fade-up" <?php selected( get_option( 'swgtheme_animation_style', 'fade-up' ), 'fade-up' ); ?>><?php esc_html_e( 'Fade Up', 'swgtheme' ); ?></option>
								<option value="fade-down" <?php selected( get_option( 'swgtheme_animation_style', 'fade-up' ), 'fade-down' ); ?>><?php esc_html_e( 'Fade Down', 'swgtheme' ); ?></option>
								<option value="fade-left" <?php selected( get_option( 'swgtheme_animation_style', 'fade-up' ), 'fade-left' ); ?>><?php esc_html_e( 'Fade Left', 'swgtheme' ); ?></option>
								<option value="fade-right" <?php selected( get_option( 'swgtheme_animation_style', 'fade-up' ), 'fade-right' ); ?>><?php esc_html_e( 'Fade Right', 'swgtheme' ); ?></option>
								<option value="zoom-in" <?php selected( get_option( 'swgtheme_animation_style', 'fade-up' ), 'zoom-in' ); ?>><?php esc_html_e( 'Zoom In', 'swgtheme' ); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Parallax Effect', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_parallax" value="1" <?php checked( get_option( 'swgtheme_enable_parallax', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable parallax scrolling for background images', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Add class "parallax" to elements for effect', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Tooltips', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_tooltips" value="1" <?php checked( get_option( 'swgtheme_enable_tooltips', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable tooltips on hover', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Use data-tooltip attribute for custom tooltips', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Loading & Performance', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Loading Screen', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_loading_screen" id="swgtheme_enable_loading_screen" value="1" <?php checked( get_option( 'swgtheme_enable_loading_screen', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show loading screen on page load', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Animated loading screen while page loads', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="loading-logo" style="display: <?php echo get_option( 'swgtheme_enable_loading_screen', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_loading_logo"><?php esc_html_e( 'Loading Logo URL', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="url" name="swgtheme_loading_logo" id="swgtheme_loading_logo" value="<?php echo esc_url( get_option( 'swgtheme_loading_logo', '' ) ); ?>" class="regular-text" placeholder="https://" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_loading_logo"><?php esc_html_e( 'Upload', 'swgtheme' ); ?></button>
							<p class="description"><?php esc_html_e( 'Optional logo to display during loading', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Content Protection', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disable Right Click', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_right_click" value="1" <?php checked( get_option( 'swgtheme_disable_right_click', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Disable right-click context menu', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Prevents right-clicking to save images', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Disable Text Selection', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_disable_text_selection" value="1" <?php checked( get_option( 'swgtheme_disable_text_selection', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Prevent text selection', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Disables highlighting and copying text', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	jQuery(document).ready(function($) {
		// Toggle Back to Top position
		$('#swgtheme_enable_back_to_top').on('change', function() {
			$('#back-to-top-position').toggle(this.checked);
		});
		
		// Toggle Sticky Header shrink
		$('#swgtheme_enable_sticky_header').on('change', function() {
			$('#sticky-shrink').toggle(this.checked);
		});
		
		// Toggle Reading Progress options
		$('#swgtheme_enable_reading_progress').on('change', function() {
			$('#reading-progress-color, #reading-progress-height').toggle(this.checked);
		});
		
		// Toggle Lightbox caption
		$('#swgtheme_enable_lightbox').on('change', function() {
			$('#lightbox-caption').toggle(this.checked);
		});
		
		// Toggle Animation style
		$('#swgtheme_enable_scroll_animations').on('change', function() {
			$('#animation-style').toggle(this.checked);
		});
		
		// Toggle Loading logo
		$('#swgtheme_enable_loading_screen').on('change', function() {
			$('#loading-logo').toggle(this.checked);
		});
	});
	</script>
	<?php
}

/**
 * Enqueue UX frontend scripts and styles
 */
function swgtheme_enqueue_ux_assets() {
	// Smooth Scroll
	if ( get_option( 'swgtheme_enable_smooth_scroll', '0' ) === '1' ) {
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$("a[href*=\'#\']:not([href=\'#\'])").click(function() {
					if (location.pathname.replace(/^\//,"") == this.pathname.replace(/^\//,"") && location.hostname == this.hostname) {
						var target = $(this.hash);
						target = target.length ? target : $("[name=" + this.hash.slice(1) +"]");
						if (target.length) {
							$("html, body").animate({scrollTop: target.offset().top - 80}, 800);
							return false;
						}
					}
				});
			});
		' );
	}
	
	// Back to Top Button
	if ( get_option( 'swgtheme_enable_back_to_top', '0' ) === '1' ) {
		$position = get_option( 'swgtheme_back_to_top_position', 'bottom-right' );
		$pos_style = ( $position === 'bottom-left' ) ? 'left: 20px;' : 'right: 20px;';
		
		add_action( 'wp_footer', function() use ( $pos_style ) {
			echo '<button id="back-to-top" style="' . esc_attr( $pos_style ) . '" aria-label="Back to top">↑</button>';
		} );
		
		wp_add_inline_style( 'swgtheme-style', '
			#back-to-top {
				position: fixed;
				bottom: 20px;
				background: var(--primary-color, #dc3545);
				color: white;
				border: none;
				border-radius: 50%;
				width: 50px;
				height: 50px;
				font-size: 24px;
				cursor: pointer;
				opacity: 0;
				visibility: hidden;
				transition: all 0.3s ease;
				z-index: 9999;
			}
			#back-to-top.visible {
				opacity: 1;
				visibility: visible;
			}
			#back-to-top:hover {
				transform: translateY(-5px);
				box-shadow: 0 5px 20px rgba(0,0,0,0.3);
			}
		' );
		
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(window).scroll(function() {
					if ($(this).scrollTop() > 300) {
						$("#back-to-top").addClass("visible");
					} else {
						$("#back-to-top").removeClass("visible");
					}
				});
				
				$("#back-to-top").click(function() {
					$("html, body").animate({scrollTop: 0}, 600);
					return false;
				});
			});
		' );
	}
	
	// Reading Progress Bar
	if ( get_option( 'swgtheme_enable_reading_progress', '0' ) === '1' && is_single() ) {
		$color = get_option( 'swgtheme_reading_progress_color', '#dc3545' );
		$height = get_option( 'swgtheme_reading_progress_height', '4' );
		
		add_action( 'wp_footer', function() {
			echo '<div id="reading-progress-bar"></div>';
		} );
		
		wp_add_inline_style( 'swgtheme-style', '
			#reading-progress-bar {
				position: fixed;
				top: 0;
				left: 0;
				height: ' . esc_attr( $height ) . 'px;
				background: ' . esc_attr( $color ) . ';
				width: 0%;
				z-index: 10000;
				transition: width 0.2s ease;
			}
		' );
		
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(window).scroll(function() {
					var scrollTop = $(window).scrollTop();
					var docHeight = $(document).height();
					var winHeight = $(window).height();
					var scrollPercent = (scrollTop) / (docHeight - winHeight);
					var scrollPercentRounded = Math.round(scrollPercent * 100);
					$("#reading-progress-bar").css("width", scrollPercentRounded + "%");
				});
			});
		' );
	}
	
	// Image Lightbox
	if ( get_option( 'swgtheme_enable_lightbox', '0' ) === '1' ) {
		$show_caption = get_option( 'swgtheme_lightbox_caption', '1' ) === '1';
		
		wp_add_inline_style( 'swgtheme-style', '
			.swg-lightbox {
				display: none;
				position: fixed;
				z-index: 99999;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				background: rgba(0,0,0,0.95);
				align-items: center;
				justify-content: center;
			}
			.swg-lightbox.active {
				display: flex;
			}
			.swg-lightbox-content {
				max-width: 90%;
				max-height: 90%;
				position: relative;
			}
			.swg-lightbox-content img {
				max-width: 100%;
				max-height: 85vh;
				object-fit: contain;
			}
			.swg-lightbox-caption {
				color: white;
				text-align: center;
				padding: 15px;
				font-size: 16px;
			}
			.swg-lightbox-close {
				position: absolute;
				top: 20px;
				right: 30px;
				color: white;
				font-size: 40px;
				font-weight: bold;
				cursor: pointer;
				z-index: 100000;
			}
			.swg-lightbox-close:hover {
				color: #ccc;
			}
		' );
		
		add_action( 'wp_footer', function() use ( $show_caption ) {
			echo '<div class="swg-lightbox" id="swg-lightbox">
				<span class="swg-lightbox-close">&times;</span>
				<div class="swg-lightbox-content">
					<img id="swg-lightbox-img" src="" alt="">
					' . ( $show_caption ? '<div class="swg-lightbox-caption" id="swg-lightbox-caption"></div>' : '' ) . '
				</div>
			</div>';
		} );
		
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(".entry-content img, .post-thumbnail img").css("cursor", "pointer").click(function() {
					$("#swg-lightbox-img").attr("src", $(this).attr("src"));
					' . ( $show_caption ? '$("#swg-lightbox-caption").text($(this).attr("alt"));' : '' ) . '
					$("#swg-lightbox").addClass("active");
				});
				
				$(".swg-lightbox-close, .swg-lightbox").click(function() {
					$("#swg-lightbox").removeClass("active");
				});
				
				$("#swg-lightbox-img").click(function(e) {
					e.stopPropagation();
				});
			});
		' );
	}
	
	// Tooltips
	if ( get_option( 'swgtheme_enable_tooltips', '0' ) === '1' ) {
		wp_add_inline_style( 'swgtheme-style', '
			[data-tooltip] {
				position: relative;
				cursor: help;
			}
			[data-tooltip]:before {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				background: rgba(0,0,0,0.9);
				color: white;
				padding: 8px 12px;
				border-radius: 4px;
				white-space: nowrap;
				opacity: 0;
				visibility: hidden;
				transition: all 0.3s ease;
				font-size: 14px;
				pointer-events: none;
				z-index: 1000;
				margin-bottom: 8px;
			}
			[data-tooltip]:after {
				content: "";
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				border: 6px solid transparent;
				border-top-color: rgba(0,0,0,0.9);
				opacity: 0;
				visibility: hidden;
				transition: all 0.3s ease;
				pointer-events: none;
				margin-bottom: 2px;
			}
			[data-tooltip]:hover:before,
			[data-tooltip]:hover:after {
				opacity: 1;
				visibility: visible;
			}
		' );
	}
	
	// Scroll Animations
	if ( get_option( 'swgtheme_enable_scroll_animations', '0' ) === '1' ) {
		$style = get_option( 'swgtheme_animation_style', 'fade-up' );
		
		wp_add_inline_style( 'swgtheme-style', '
			.swg-animate {
				opacity: 0;
				transition: all 0.6s ease;
			}
			.swg-animate.swg-animated {
				opacity: 1;
			}
			.swg-animate.fade-up {
				transform: translateY(30px);
			}
			.swg-animate.fade-up.swg-animated {
				transform: translateY(0);
			}
			.swg-animate.fade-down {
				transform: translateY(-30px);
			}
			.swg-animate.fade-down.swg-animated {
				transform: translateY(0);
			}
			.swg-animate.fade-left {
				transform: translateX(30px);
			}
			.swg-animate.fade-left.swg-animated {
				transform: translateX(0);
			}
			.swg-animate.fade-right {
				transform: translateX(-30px);
			}
			.swg-animate.fade-right.swg-animated {
				transform: translateX(0);
			}
			.swg-animate.zoom-in {
				transform: scale(0.9);
			}
			.swg-animate.zoom-in.swg-animated {
				transform: scale(1);
			}
		' );
		
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(".entry-content > *").addClass("swg-animate ' . esc_js( $style ) . '");
				
				var observer = new IntersectionObserver(function(entries) {
					entries.forEach(function(entry) {
						if (entry.isIntersecting) {
							entry.target.classList.add("swg-animated");
						}
					});
				}, {threshold: 0.1});
				
				$(".swg-animate").each(function() {
					observer.observe(this);
				});
			});
		' );
	}
	
	// Sticky Header
	if ( get_option( 'swgtheme_enable_sticky_header', '0' ) === '1' ) {
		$shrink = get_option( 'swgtheme_sticky_shrink', '1' ) === '1';
		
		wp_add_inline_style( 'swgtheme-style', '
			header.sticky {
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				z-index: 9998;
				background: rgba(0,0,0,0.95);
				transition: all 0.3s ease;
				box-shadow: 0 2px 10px rgba(0,0,0,0.3);
			}
			' . ( $shrink ? '
			header.sticky {
				padding: 10px 0 !important;
			}
			header.sticky .logo {
				font-size: 20px !important;
			}
			' : '' ) . '
		' );
		
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				var header = $("header");
				var headerOffset = header.offset().top;
				
				$(window).scroll(function() {
					if ($(window).scrollTop() > headerOffset + 100) {
						header.addClass("sticky");
					} else {
						header.removeClass("sticky");
					}
				});
			});
		' );
	}
	
	// Loading Screen
	if ( get_option( 'swgtheme_enable_loading_screen', '0' ) === '1' ) {
		$logo = get_option( 'swgtheme_loading_logo', '' );
		
		add_action( 'wp_body_open', function() use ( $logo ) {
			echo '<div id="swg-loading-screen">
				<div class="swg-loader">';
			if ( $logo ) {
				echo '<img src="' . esc_url( $logo ) . '" alt="Loading" class="swg-loading-logo">';
			}
			echo '<div class="swg-spinner"></div>
				</div>
			</div>';
		} );
		
		wp_add_inline_style( 'swgtheme-style', '
			#swg-loading-screen {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #000;
				z-index: 999999;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: opacity 0.5s ease;
			}
			#swg-loading-screen.loaded {
				opacity: 0;
				pointer-events: none;
			}
			.swg-loader {
				text-align: center;
			}
			.swg-loading-logo {
				max-width: 200px;
				margin-bottom: 30px;
				animation: swgPulse 1.5s ease-in-out infinite;
			}
			.swg-spinner {
				border: 4px solid rgba(255,255,255,0.1);
				border-left-color: var(--primary-color, #dc3545);
				border-radius: 50%;
				width: 50px;
				height: 50px;
				animation: swgSpin 1s linear infinite;
				margin: 0 auto;
			}
			@keyframes swgSpin {
				to { transform: rotate(360deg); }
			}
			@keyframes swgPulse {
				0%, 100% { opacity: 1; }
				50% { opacity: 0.6; }
			}
		' );
		
		wp_add_inline_script( 'jquery', '
			window.addEventListener("load", function() {
				setTimeout(function() {
					document.getElementById("swg-loading-screen").classList.add("loaded");
					setTimeout(function() {
						document.getElementById("swg-loading-screen").remove();
					}, 500);
				}, 500);
			});
		' );
	}
	
	// Parallax Effect
	if ( get_option( 'swgtheme_enable_parallax', '0' ) === '1' ) {
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(window).scroll(function() {
					var scrolled = $(window).scrollTop();
					$(".parallax").each(function() {
						var speed = 0.5;
						var offset = $(this).offset().top;
						var yPos = -(scrolled - offset) * speed;
						$(this).css("background-position", "center " + yPos + "px");
					});
				});
			});
		' );
	}
	
	// Copy Protection
	if ( get_option( 'swgtheme_disable_right_click', '0' ) === '1' ) {
		wp_add_inline_script( 'jquery', '
			document.addEventListener("contextmenu", function(e) {
				e.preventDefault();
			});
		' );
	}
	
	if ( get_option( 'swgtheme_disable_text_selection', '0' ) === '1' ) {
		wp_add_inline_style( 'swgtheme-style', '
			body {
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}
		' );
	}
}
add_action( 'wp_enqueue_scripts', 'swgtheme_enqueue_ux_assets' );

/**
 * ==================================================================
 * ADVANCED ADMIN & MANAGEMENT FEATURES
 * ==================================================================
 */

/**
 * Register Admin & Management settings
 */
function swgtheme_register_admin_settings() {
	// Duplicate Posts
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_duplicate_posts' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_duplicate_post_types' );
	
	// Activity Log
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_activity_log' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_activity_log_retention' );
	
	// Database Cleanup
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_auto_cleanup' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_cleanup_revisions' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_cleanup_autodrafts' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_cleanup_trash' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_cleanup_spam_comments' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_cleanup_orphaned_meta' );
	
	// Quick Edit
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_quick_edit_enhanced' );
	
	// Admin Color Scheme
	register_setting( 'swgtheme_admin_group', 'swgtheme_custom_admin_colors' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_admin_primary_color' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_admin_accent_color' );
	
	// Dashboard Widgets
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_theme_stats_widget' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_recent_activity_widget' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_system_info_widget' );
	
	// Login Customization
	register_setting( 'swgtheme_admin_group', 'swgtheme_custom_login_logo' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_login_background' );
	
	// Maintenance Mode
	register_setting( 'swgtheme_admin_group', 'swgtheme_enable_maintenance_mode' );
	register_setting( 'swgtheme_admin_group', 'swgtheme_maintenance_message' );
}
add_action( 'admin_init', 'swgtheme_register_admin_settings' );

/**
 * Add Admin & Management menu page
 */
function swgtheme_add_admin_management_menu() {
	add_submenu_page(
		'themes.php',
		__( 'Admin & Management', 'swgtheme' ),
		__( 'Admin & Management', 'swgtheme' ),
		'manage_options',
		'swgtheme-admin-management',
		'swgtheme_admin_management_page'
	);
}
add_action( 'admin_menu', 'swgtheme_add_admin_management_menu' );

/**
 * Admin & Management page callback
 */
function swgtheme_admin_management_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		
		<?php if ( isset( $_GET['cleanup'] ) && $_GET['cleanup'] === 'success' ): ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Database cleanup completed successfully!', 'swgtheme' ); ?></p>
			</div>
		<?php endif; ?>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_admin_group' ); ?>
			
			<h2><?php esc_html_e( 'Content Management', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Duplicate Posts/Pages', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_duplicate_posts" id="swgtheme_enable_duplicate_posts" value="1" <?php checked( get_option( 'swgtheme_enable_duplicate_posts', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable "Duplicate" action in post/page lists', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Adds a quick duplicate link to clone posts and pages', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="duplicate-post-types" style="display: <?php echo get_option( 'swgtheme_enable_duplicate_posts', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Post Types to Duplicate', 'swgtheme' ); ?></th>
						<td>
							<?php
							$enabled_types = get_option( 'swgtheme_duplicate_post_types', array( 'post', 'page' ) );
							$post_types = get_post_types( array( 'public' => true ), 'objects' );
							foreach ( $post_types as $post_type ) {
								if ( $post_type->name === 'attachment' ) continue;
								$checked = is_array( $enabled_types ) && in_array( $post_type->name, $enabled_types );
								echo '<label><input type="checkbox" name="swgtheme_duplicate_post_types[]" value="' . esc_attr( $post_type->name ) . '" ' . checked( $checked, true, false ) . ' /> ' . esc_html( $post_type->label ) . '</label><br>';
							}
							?>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Enhanced Quick Edit', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_quick_edit_enhanced" value="1" <?php checked( get_option( 'swgtheme_enable_quick_edit_enhanced', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Add featured image and excerpt to Quick Edit', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Enhances the Quick Edit panel with additional fields', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Activity & Monitoring', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Activity Log', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_activity_log" id="swgtheme_enable_activity_log" value="1" <?php checked( get_option( 'swgtheme_enable_activity_log', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Track user activity and changes', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Logs post updates, user logins, and admin actions', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="activity-log-retention" style="display: <?php echo get_option( 'swgtheme_enable_activity_log', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_activity_log_retention"><?php esc_html_e( 'Log Retention', 'swgtheme' ); ?></label>
						</th>
						<td>
							<select name="swgtheme_activity_log_retention" id="swgtheme_activity_log_retention">
								<option value="7" <?php selected( get_option( 'swgtheme_activity_log_retention', '30' ), '7' ); ?>>7 <?php esc_html_e( 'days', 'swgtheme' ); ?></option>
								<option value="30" <?php selected( get_option( 'swgtheme_activity_log_retention', '30' ), '30' ); ?>>30 <?php esc_html_e( 'days', 'swgtheme' ); ?></option>
								<option value="90" <?php selected( get_option( 'swgtheme_activity_log_retention', '30' ), '90' ); ?>>90 <?php esc_html_e( 'days', 'swgtheme' ); ?></option>
								<option value="365" <?php selected( get_option( 'swgtheme_activity_log_retention', '30' ), '365' ); ?>>1 <?php esc_html_e( 'year', 'swgtheme' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Automatically delete logs older than this period', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Database Cleanup', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto Cleanup', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_auto_cleanup" value="1" <?php checked( get_option( 'swgtheme_enable_auto_cleanup', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Run automatic weekly database cleanup', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Cleans database based on settings below every week', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Cleanup Options', 'swgtheme' ); ?></th>
						<td>
							<label><input type="checkbox" name="swgtheme_cleanup_revisions" value="1" <?php checked( get_option( 'swgtheme_cleanup_revisions', '0' ), '1' ); ?> /> <?php esc_html_e( 'Old post revisions (keep last 5)', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_cleanup_autodrafts" value="1" <?php checked( get_option( 'swgtheme_cleanup_autodrafts', '0' ), '1' ); ?> /> <?php esc_html_e( 'Auto-drafts older than 7 days', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_cleanup_trash" value="1" <?php checked( get_option( 'swgtheme_cleanup_trash', '0' ), '1' ); ?> /> <?php esc_html_e( 'Trashed posts older than 30 days', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_cleanup_spam_comments" value="1" <?php checked( get_option( 'swgtheme_cleanup_spam_comments', '0' ), '1' ); ?> /> <?php esc_html_e( 'Spam comments older than 30 days', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_cleanup_orphaned_meta" value="1" <?php checked( get_option( 'swgtheme_cleanup_orphaned_meta', '0' ), '1' ); ?> /> <?php esc_html_e( 'Orphaned post meta', 'swgtheme' ); ?></label>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Manual Cleanup', 'swgtheme' ); ?></th>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=swgtheme_run_cleanup' ) ); ?>" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Run database cleanup now? This cannot be undone.', 'swgtheme' ); ?>');">
								<?php esc_html_e( 'Run Cleanup Now', 'swgtheme' ); ?>
							</a>
							<p class="description"><?php esc_html_e( 'Manually trigger database cleanup based on settings above', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Dashboard Customization', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Dashboard Widgets', 'swgtheme' ); ?></th>
						<td>
							<label><input type="checkbox" name="swgtheme_enable_theme_stats_widget" value="1" <?php checked( get_option( 'swgtheme_enable_theme_stats_widget', '1' ), '1' ); ?> /> <?php esc_html_e( 'Theme Statistics Widget', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_enable_recent_activity_widget" value="1" <?php checked( get_option( 'swgtheme_enable_recent_activity_widget', '0' ), '1' ); ?> /> <?php esc_html_e( 'Recent Activity Widget', 'swgtheme' ); ?></label><br>
							<label><input type="checkbox" name="swgtheme_enable_system_info_widget" value="1" <?php checked( get_option( 'swgtheme_enable_system_info_widget', '0' ), '1' ); ?> /> <?php esc_html_e( 'System Information Widget', 'swgtheme' ); ?></label>
							<p class="description"><?php esc_html_e( 'Custom dashboard widgets with useful information', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom Admin Colors', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_custom_admin_colors" id="swgtheme_custom_admin_colors" value="1" <?php checked( get_option( 'swgtheme_custom_admin_colors', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Use custom admin color scheme', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Override WordPress admin colors', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="admin-primary-color" style="display: <?php echo get_option( 'swgtheme_custom_admin_colors', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_admin_primary_color"><?php esc_html_e( 'Admin Primary Color', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="color" name="swgtheme_admin_primary_color" id="swgtheme_admin_primary_color" value="<?php echo esc_attr( get_option( 'swgtheme_admin_primary_color', '#23282d' ) ); ?>" />
						</td>
					</tr>
					
					<tr id="admin-accent-color" style="display: <?php echo get_option( 'swgtheme_custom_admin_colors', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_admin_accent_color"><?php esc_html_e( 'Admin Accent Color', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="color" name="swgtheme_admin_accent_color" id="swgtheme_admin_accent_color" value="<?php echo esc_attr( get_option( 'swgtheme_admin_accent_color', '#0073aa' ) ); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Login Customization', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="swgtheme_custom_login_logo"><?php esc_html_e( 'Custom Login Logo', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="url" name="swgtheme_custom_login_logo" id="swgtheme_custom_login_logo" value="<?php echo esc_url( get_option( 'swgtheme_custom_login_logo', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_custom_login_logo"><?php esc_html_e( 'Upload', 'swgtheme' ); ?></button>
							<p class="description"><?php esc_html_e( 'Replace WordPress logo on login page', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="swgtheme_login_background_sec"><?php esc_html_e( 'Login Background', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="url" name="swgtheme_login_background" id="swgtheme_login_background_sec" value="<?php echo esc_url( get_option( 'swgtheme_login_background', '' ) ); ?>" class="regular-text" />
							<button type="button" class="button swg-upload-button" data-target="swgtheme_login_background"><?php esc_html_e( 'Upload', 'swgtheme' ); ?></button>
							<p class="description"><?php esc_html_e( 'Custom background image for login page', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Maintenance Mode', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Maintenance Mode', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_maintenance_mode" id="swgtheme_enable_maintenance_mode" value="1" <?php checked( get_option( 'swgtheme_enable_maintenance_mode', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show maintenance page to visitors (admins can still access)', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr id="maintenance-message" style="display: <?php echo get_option( 'swgtheme_enable_maintenance_mode', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_maintenance_message"><?php esc_html_e( 'Maintenance Message', 'swgtheme' ); ?></label>
						</th>
						<td>
							<textarea name="swgtheme_maintenance_message" id="swgtheme_maintenance_message" rows="4" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_maintenance_message', 'We are currently performing scheduled maintenance. We will be back shortly!' ) ); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	jQuery(document).ready(function($) {
		// Toggle duplicate post types
		$('#swgtheme_enable_duplicate_posts').on('change', function() {
			$('#duplicate-post-types').toggle(this.checked);
		});
		
		// Toggle activity log retention
		$('#swgtheme_enable_activity_log').on('change', function() {
			$('#activity-log-retention').toggle(this.checked);
		});
		
		// Toggle admin colors
		$('#swgtheme_custom_admin_colors').on('change', function() {
			$('#admin-primary-color, #admin-accent-color').toggle(this.checked);
		});
		
		// Toggle maintenance message
		$('#swgtheme_enable_maintenance_mode').on('change', function() {
			$('#maintenance-message').toggle(this.checked);
		});
	});
	</script>
	<?php
}

/**
 * Duplicate Post Functionality
 */
if ( get_option( 'swgtheme_enable_duplicate_posts', '0' ) === '1' ) {
	function swgtheme_duplicate_post_link( $actions, $post ) {
		$enabled_types = get_option( 'swgtheme_duplicate_post_types', array( 'post', 'page' ) );
		if ( ! is_array( $enabled_types ) ) {
			$enabled_types = array( 'post', 'page' );
		}
		
		if ( in_array( $post->post_type, $enabled_types ) && current_user_can( 'edit_posts' ) ) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=swgtheme_duplicate_post&post=' . $post->ID ), 'duplicate-post_' . $post->ID ) . '">' . __( 'Duplicate', 'swgtheme' ) . '</a>';
		}
		return $actions;
	}
	add_filter( 'post_row_actions', 'swgtheme_duplicate_post_link', 10, 2 );
	add_filter( 'page_row_actions', 'swgtheme_duplicate_post_link', 10, 2 );
	
	function swgtheme_duplicate_post_action() {
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['action'] ) || $_GET['action'] !== 'swgtheme_duplicate_post' ) {
			return;
		}
		
		$post_id = absint( $_GET['post'] );
		check_admin_referer( 'duplicate-post_' . $post_id );
		
		$post = get_post( $post_id );
		if ( ! $post ) {
			wp_die( __( 'Post not found.', 'swgtheme' ) );
		}
		
		$current_user = wp_get_current_user();
		$new_post = array(
			'post_title'     => $post->post_title . ' (Copy)',
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_status'    => 'draft',
			'post_type'      => $post->post_type,
			'post_author'    => $current_user->ID,
			'post_parent'    => $post->post_parent,
			'menu_order'     => $post->menu_order,
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
		);
		
		$new_post_id = wp_insert_post( $new_post );
		
		// Copy post meta
		$post_meta = get_post_meta( $post_id );
		foreach ( $post_meta as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
			}
		}
		
		// Copy taxonomies
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
			wp_set_object_terms( $new_post_id, $terms, $taxonomy );
		}
		
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	}
	add_action( 'admin_action_swgtheme_duplicate_post', 'swgtheme_duplicate_post_action' );
}

/**
 * Activity Log
 */
if ( get_option( 'swgtheme_enable_activity_log', '0' ) === '1' ) {
	// Create activity log table
	function swgtheme_create_activity_log_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'swgtheme_activity_log';
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			action varchar(50) NOT NULL,
			object_type varchar(50) NOT NULL,
			object_id bigint(20) DEFAULT NULL,
			description text,
			ip_address varchar(45),
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY action (action),
			KEY created_at (created_at)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	add_action( 'after_switch_theme', 'swgtheme_create_activity_log_table' );
	
	// Log function
	function swgtheme_log_activity( $action, $object_type, $object_id = null, $description = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'swgtheme_activity_log';
		
		$wpdb->insert(
			$table_name,
			array(
				'user_id'     => get_current_user_id(),
				'action'      => sanitize_text_field( $action ),
				'object_type' => sanitize_text_field( $object_type ),
				'object_id'   => absint( $object_id ),
				'description' => sanitize_text_field( $description ),
				'ip_address'  => $_SERVER['REMOTE_ADDR'],
			)
		);
	}
	
	// Track post updates
	add_action( 'save_post', function( $post_id ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		$post = get_post( $post_id );
		swgtheme_log_activity( 'post_updated', $post->post_type, $post_id, 'Post "' . $post->post_title . '" updated' );
	} );
	
	// Track user login
	add_action( 'wp_login', function( $user_login, $user ) {
		swgtheme_log_activity( 'user_login', 'user', $user->ID, 'User logged in' );
	}, 10, 2 );
	
	// Clean old logs
	add_action( 'wp_scheduled_delete', function() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'swgtheme_activity_log';
		$retention = get_option( 'swgtheme_activity_log_retention', '30' );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)", $retention ) );
	} );
}

/**
 * Database Cleanup
 */
function swgtheme_run_database_cleanup() {
	global $wpdb;
	$cleaned = array();
	
	// Clean revisions (keep last 5)
	if ( get_option( 'swgtheme_cleanup_revisions', '0' ) === '1' ) {
		$revisions = $wpdb->get_col( "
			SELECT r.ID FROM $wpdb->posts r
			WHERE r.post_type = 'revision'
			AND r.ID NOT IN (
				SELECT ID FROM (
					SELECT ID FROM $wpdb->posts
					WHERE post_type = 'revision'
					ORDER BY post_modified DESC
					LIMIT 5
				) AS keep_revisions
			)
		" );
		foreach ( $revisions as $revision_id ) {
			wp_delete_post_revision( $revision_id );
		}
		$cleaned['revisions'] = count( $revisions );
	}
	
	// Clean auto-drafts
	if ( get_option( 'swgtheme_cleanup_autodrafts', '0' ) === '1' ) {
		$autodrafts = $wpdb->query( "
			DELETE FROM $wpdb->posts
			WHERE post_status = 'auto-draft'
			AND DATE(post_modified) < DATE_SUB(NOW(), INTERVAL 7 DAY)
		" );
		$cleaned['autodrafts'] = $autodrafts;
	}
	
	// Clean trash
	if ( get_option( 'swgtheme_cleanup_trash', '0' ) === '1' ) {
		$trash = $wpdb->query( "
			DELETE FROM $wpdb->posts
			WHERE post_status = 'trash'
			AND DATE(post_modified) < DATE_SUB(NOW(), INTERVAL 30 DAY)
		" );
		$cleaned['trash'] = $trash;
	}
	
	// Clean spam comments
	if ( get_option( 'swgtheme_cleanup_spam_comments', '0' ) === '1' ) {
		$spam = $wpdb->query( "
			DELETE FROM $wpdb->comments
			WHERE comment_approved = 'spam'
			AND DATE(comment_date) < DATE_SUB(NOW(), INTERVAL 30 DAY)
		" );
		$cleaned['spam'] = $spam;
	}
	
	// Clean orphaned meta
	if ( get_option( 'swgtheme_cleanup_orphaned_meta', '0' ) === '1' ) {
		$orphaned = $wpdb->query( "
			DELETE pm FROM $wpdb->postmeta pm
			LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
			WHERE p.ID IS NULL
		" );
		$cleaned['orphaned_meta'] = $orphaned;
	}
	
	// Optimize tables
	$wpdb->query( "OPTIMIZE TABLE $wpdb->posts, $wpdb->postmeta, $wpdb->comments, $wpdb->commentmeta" );
	
	return $cleaned;
}

// Auto cleanup schedule
if ( get_option( 'swgtheme_enable_auto_cleanup', '0' ) === '1' ) {
	if ( ! wp_next_scheduled( 'swgtheme_auto_cleanup' ) ) {
		wp_schedule_event( time(), 'weekly', 'swgtheme_auto_cleanup' );
	}
	add_action( 'swgtheme_auto_cleanup', 'swgtheme_run_database_cleanup' );
} else {
	wp_clear_scheduled_hook( 'swgtheme_auto_cleanup' );
}

// Manual cleanup handler
add_action( 'admin_post_swgtheme_run_cleanup', function() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'Unauthorized', 'swgtheme' ) );
	}
	
	swgtheme_run_database_cleanup();
	wp_redirect( admin_url( 'themes.php?page=swgtheme-admin-management&cleanup=success' ) );
	exit;
} );

/**
 * Dashboard Widgets
 */
if ( get_option( 'swgtheme_enable_recent_activity_widget', '0' ) === '1' && get_option( 'swgtheme_enable_activity_log', '0' ) === '1' ) {
	add_action( 'wp_dashboard_setup', function() {
		wp_add_dashboard_widget( 'swgtheme_recent_activity', __( 'Recent Activity', 'swgtheme' ), function() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'swgtheme_activity_log';
			$activities = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10" );
			
			if ( $activities ) {
				echo '<ul style="margin: 0;">';
				foreach ( $activities as $activity ) {
					$user = get_userdata( $activity->user_id );
					$username = $user ? $user->display_name : __( 'Unknown', 'swgtheme' );
					echo '<li style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">';
					echo '<strong>' . esc_html( $username ) . '</strong> - ' . esc_html( $activity->description );
					echo '<br><small style="color: #666;">' . human_time_diff( strtotime( $activity->created_at ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'swgtheme' ) . '</small>';
					echo '</li>';
				}
				echo '</ul>';
			} else {
				echo '<p>' . __( 'No recent activity.', 'swgtheme' ) . '</p>';
			}
		} );
	} );
}

if ( get_option( 'swgtheme_enable_system_info_widget', '0' ) === '1' ) {
	add_action( 'wp_dashboard_setup', function() {
		wp_add_dashboard_widget( 'swgtheme_system_info', __( 'System Information', 'swgtheme' ), function() {
			global $wpdb;
			echo '<ul style="margin: 0;">';
			echo '<li><strong>' . __( 'WordPress Version:', 'swgtheme' ) . '</strong> ' . get_bloginfo( 'version' ) . '</li>';
			echo '<li><strong>' . __( 'PHP Version:', 'swgtheme' ) . '</strong> ' . phpversion() . '</li>';
			echo '<li><strong>' . __( 'MySQL Version:', 'swgtheme' ) . '</strong> ' . $wpdb->db_version() . '</li>';
			echo '<li><strong>' . __( 'Active Theme:', 'swgtheme' ) . '</strong> ' . wp_get_theme()->get( 'Name' ) . ' v' . wp_get_theme()->get( 'Version' ) . '</li>';
			echo '<li><strong>' . __( 'Active Plugins:', 'swgtheme' ) . '</strong> ' . count( get_option( 'active_plugins' ) ) . '</li>';
			
			$upload_dir = wp_upload_dir();
			$upload_size = 0;
			if ( is_dir( $upload_dir['basedir'] ) ) {
				$upload_size = swgtheme_get_dir_size( $upload_dir['basedir'] );
			}
			echo '<li><strong>' . __( 'Uploads Size:', 'swgtheme' ) . '</strong> ' . size_format( $upload_size ) . '</li>';
			
			$db_size = $wpdb->get_var( "
				SELECT SUM(data_length + index_length)
				FROM information_schema.TABLES
				WHERE table_schema = '" . DB_NAME . "'
			" );
			echo '<li><strong>' . __( 'Database Size:', 'swgtheme' ) . '</strong> ' . size_format( $db_size ) . '</li>';
			echo '</ul>';
		} );
	} );
	
	function swgtheme_get_dir_size( $directory ) {
		$size = 0;
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ) ) as $file ) {
			$size += $file->getSize();
		}
		return $size;
	}
}

/**
 * Custom Admin Colors
 */
if ( get_option( 'swgtheme_custom_admin_colors', '0' ) === '1' ) {
	add_action( 'admin_head', function() {
		$primary = get_option( 'swgtheme_admin_primary_color', '#23282d' );
		$accent = get_option( 'swgtheme_admin_accent_color', '#0073aa' );
		?>
		<style>
			#adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
				background-color: <?php echo esc_attr( $primary ); ?>;
			}
			#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
			#adminmenu .wp-menu-arrow,
			#adminmenu .wp-menu-arrow div,
			#adminmenu li.current a.menu-top,
			#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
			.folded #adminmenu li.current.menu-top,
			.folded #adminmenu li.wp-has-current-submenu {
				background: <?php echo esc_attr( $accent ); ?>;
			}
			.wp-core-ui .button-primary {
				background: <?php echo esc_attr( $accent ); ?>;
				border-color: <?php echo esc_attr( $accent ); ?>;
			}
		</style>
		<?php
	} );
}

/**
 * Custom Login Page
 */
if ( get_option( 'swgtheme_custom_login_logo', '' ) || get_option( 'swgtheme_login_background', '' ) ) {
	add_action( 'login_enqueue_scripts', function() {
		$logo = get_option( 'swgtheme_custom_login_logo', '' );
		$bg = get_option( 'swgtheme_login_background', '' );
		?>
		<style>
			<?php if ( $logo ): ?>
			#login h1 a {
				background-image: url(<?php echo esc_url( $logo ); ?>);
				background-size: contain;
				width: 100%;
				height: 100px;
			}
			<?php endif; ?>
			<?php if ( $bg ): ?>
			body.login {
				background: url(<?php echo esc_url( $bg ); ?>) no-repeat center center fixed;
				background-size: cover;
			}
			#login {
				background: rgba(255,255,255,0.95);
				padding: 30px;
				border-radius: 8px;
			}
			<?php endif; ?>
		</style>
		<?php
	} );
	
	add_filter( 'login_headerurl', function() {
		return home_url();
	} );
}

/**
 * Maintenance Mode
 */
if ( get_option( 'swgtheme_enable_maintenance_mode', '0' ) === '1' ) {
	add_action( 'template_redirect', function() {
		if ( ! current_user_can( 'manage_options' ) && ! is_admin() ) {
			$message = get_option( 'swgtheme_maintenance_message', 'We are currently performing scheduled maintenance. We will be back shortly!' );
			wp_die(
				'<h1>' . get_bloginfo( 'name' ) . '</h1><p>' . esc_html( $message ) . '</p>',
				__( 'Maintenance Mode', 'swgtheme' ),
				array( 'response' => 503 )
			);
		}
	} );
}

/**
 * ==================================================================
 * MEMBERSHIP & USER MANAGEMENT FEATURES
 * ==================================================================
 */

/**
 * Register Membership & User Management settings
 */
function swgtheme_register_membership_settings() {
	// Frontend Registration
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_frontend_registration' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_registration_redirect' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_require_email_verification' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_default_user_role' );
	
	// User Profiles
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_public_profiles' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_profile_fields' );
	
	// Avatar System
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_custom_avatars' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_avatar_max_size' );
	
	// Content Restriction
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_content_restriction' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_restriction_message' );
	
	// Activity Dashboard
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_user_dashboard' );
	
	// Badges & Achievements
	register_setting( 'swgtheme_membership_group', 'swgtheme_enable_badges' );
	register_setting( 'swgtheme_membership_group', 'swgtheme_badge_milestones' );
	
	// Custom Roles
	register_setting( 'swgtheme_membership_group', 'swgtheme_custom_roles' );
}
add_action( 'admin_init', 'swgtheme_register_membership_settings' );

/**
 * Add Membership menu page
 */
function swgtheme_add_membership_menu() {
	add_submenu_page(
		'themes.php',
		__( 'Membership & Users', 'swgtheme' ),
		__( 'Membership & Users', 'swgtheme' ),
		'manage_options',
		'swgtheme-membership',
		'swgtheme_membership_page'
	);
	
	add_submenu_page(
		'users.php',
		__( 'User Badges', 'swgtheme' ),
		__( 'User Badges', 'swgtheme' ),
		'manage_options',
		'swgtheme-badges',
		'swgtheme_badges_management_page'
	);
}
add_action( 'admin_menu', 'swgtheme_add_membership_menu' );

/**
 * Membership settings page callback
 */
function swgtheme_membership_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_membership_group' ); ?>
			
			<h2><?php esc_html_e( 'Frontend Registration', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Frontend Registration', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_frontend_registration" id="swgtheme_enable_frontend_registration" value="1" <?php checked( get_option( 'swgtheme_enable_frontend_registration', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Allow users to register from frontend', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Display custom registration form via [swg_register] shortcode', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="registration-redirect" style="display: <?php echo get_option( 'swgtheme_enable_frontend_registration', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_registration_redirect"><?php esc_html_e( 'After Registration Redirect', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="url" name="swgtheme_registration_redirect" id="swgtheme_registration_redirect" value="<?php echo esc_url( get_option( 'swgtheme_registration_redirect', '' ) ); ?>" class="regular-text" placeholder="<?php echo esc_url( home_url() ); ?>" />
							<p class="description"><?php esc_html_e( 'Redirect users to this URL after registration (leave empty for home)', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="email-verification" style="display: <?php echo get_option( 'swgtheme_enable_frontend_registration', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Email Verification', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_require_email_verification" value="1" <?php checked( get_option( 'swgtheme_require_email_verification', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Require email verification before activation', 'swgtheme' ); ?>
							</label>
						</td>
					</tr>
					
					<tr id="default-role" style="display: <?php echo get_option( 'swgtheme_enable_frontend_registration', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_default_user_role"><?php esc_html_e( 'Default User Role', 'swgtheme' ); ?></label>
						</th>
						<td>
							<select name="swgtheme_default_user_role" id="swgtheme_default_user_role">
								<?php wp_dropdown_roles( get_option( 'swgtheme_default_user_role', 'subscriber' ) ); ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'User Profiles', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Public User Profiles', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_public_profiles" id="swgtheme_enable_public_profiles" value="1" <?php checked( get_option( 'swgtheme_enable_public_profiles', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Enable public user profile pages', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Access profiles at /user/username/', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="profile-fields" style="display: <?php echo get_option( 'swgtheme_enable_public_profiles', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Custom Profile Fields', 'swgtheme' ); ?></th>
						<td>
							<?php
							$enabled_fields = get_option( 'swgtheme_profile_fields', array( 'bio', 'location', 'website' ) );
							$available_fields = array(
								'bio' => __( 'Biography', 'swgtheme' ),
								'location' => __( 'Location', 'swgtheme' ),
								'website' => __( 'Website', 'swgtheme' ),
								'twitter' => __( 'Twitter', 'swgtheme' ),
								'facebook' => __( 'Facebook', 'swgtheme' ),
								'instagram' => __( 'Instagram', 'swgtheme' ),
								'linkedin' => __( 'LinkedIn', 'swgtheme' ),
							);
							foreach ( $available_fields as $field => $label ) {
								$checked = is_array( $enabled_fields ) && in_array( $field, $enabled_fields );
								echo '<label><input type="checkbox" name="swgtheme_profile_fields[]" value="' . esc_attr( $field ) . '" ' . checked( $checked, true, false ) . ' /> ' . esc_html( $label ) . '</label><br>';
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Avatar System', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom Avatars', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_custom_avatars" id="swgtheme_enable_custom_avatars" value="1" <?php checked( get_option( 'swgtheme_enable_custom_avatars', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Allow users to upload custom avatars', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Overrides Gravatar with uploaded avatars', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="avatar-max-size" style="display: <?php echo get_option( 'swgtheme_enable_custom_avatars', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_avatar_max_size"><?php esc_html_e( 'Max Avatar Size', 'swgtheme' ); ?></label>
						</th>
						<td>
							<input type="number" name="swgtheme_avatar_max_size" id="swgtheme_avatar_max_size" value="<?php echo esc_attr( get_option( 'swgtheme_avatar_max_size', '2' ) ); ?>" min="1" max="10" step="1" /> MB
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Content Restriction', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Content Restriction', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_content_restriction" id="swgtheme_enable_content_restriction" value="1" <?php checked( get_option( 'swgtheme_enable_content_restriction', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Restrict content to logged-in users', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Use [members_only] shortcode to restrict content sections', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="restriction-message" style="display: <?php echo get_option( 'swgtheme_enable_content_restriction', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row">
							<label for="swgtheme_restriction_message"><?php esc_html_e( 'Restriction Message', 'swgtheme' ); ?></label>
						</th>
						<td>
							<textarea name="swgtheme_restriction_message" id="swgtheme_restriction_message" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_restriction_message', 'This content is available to members only. Please log in to view.' ) ); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'User Dashboard', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable User Dashboard', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_user_dashboard" value="1" <?php checked( get_option( 'swgtheme_enable_user_dashboard', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Show user activity dashboard', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Display via [user_dashboard] shortcode - shows user stats, posts, comments', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2><?php esc_html_e( 'Badges & Achievements', 'swgtheme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Badge System', 'swgtheme' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="swgtheme_enable_badges" id="swgtheme_enable_badges" value="1" <?php checked( get_option( 'swgtheme_enable_badges', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Award badges for achievements', 'swgtheme' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Gamification system based on user activity', 'swgtheme' ); ?></p>
						</td>
					</tr>
					
					<tr id="badge-milestones" style="display: <?php echo get_option( 'swgtheme_enable_badges', '0' ) === '1' ? 'table-row' : 'none'; ?>;">
						<th scope="row"><?php esc_html_e( 'Badge Milestones', 'swgtheme' ); ?></th>
						<td>
							<p><strong><?php esc_html_e( 'Automatic badges awarded for:', 'swgtheme' ); ?></strong></p>
							<ul style="margin-left: 20px;">
								<li>🏆 <?php esc_html_e( 'First Post - Publishing your first post', 'swgtheme' ); ?></li>
								<li>⭐ <?php esc_html_e( 'Active Commenter - 10 approved comments', 'swgtheme' ); ?></li>
								<li>🎖️ <?php esc_html_e( 'Prolific Writer - 25 published posts', 'swgtheme' ); ?></li>
								<li>👑 <?php esc_html_e( 'Veteran - 100 published posts', 'swgtheme' ); ?></li>
								<li>💬 <?php esc_html_e( 'Discussion Leader - 50 approved comments', 'swgtheme' ); ?></li>
								<li>🌟 <?php esc_html_e( 'Popular Author - Post with 100+ views', 'swgtheme' ); ?></li>
							</ul>
							<p class="description"><?php esc_html_e( 'Manage badge awards in Users → User Badges', 'swgtheme' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	jQuery(document).ready(function($) {
		$('#swgtheme_enable_frontend_registration').on('change', function() {
			$('#registration-redirect, #email-verification, #default-role').toggle(this.checked);
		});
		
		$('#swgtheme_enable_public_profiles').on('change', function() {
			$('#profile-fields').toggle(this.checked);
		});
		
		$('#swgtheme_enable_custom_avatars').on('change', function() {
			$('#avatar-max-size').toggle(this.checked);
		});
		
		$('#swgtheme_enable_content_restriction').on('change', function() {
			$('#restriction-message').toggle(this.checked);
		});
		
		$('#swgtheme_enable_badges').on('change', function() {
			$('#badge-milestones').toggle(this.checked);
		});
	});
	</script>
	<?php
}

/**
 * Frontend Registration Form
 */
if ( get_option( 'swgtheme_enable_frontend_registration', '0' ) === '1' ) {
	function swgtheme_registration_form() {
		if ( is_user_logged_in() ) {
			return '<p>' . __( 'You are already logged in.', 'swgtheme' ) . '</p>';
		}
		
		ob_start();
		?>
		<form id="swg-register-form" class="swg-register-form" method="post">
			<?php wp_nonce_field( 'swg_register_user', 'swg_register_nonce' ); ?>
			
			<p class="swg-form-row">
				<label for="swg_username"><?php esc_html_e( 'Username', 'swgtheme' ); ?> <span class="required">*</span></label>
				<input type="text" name="swg_username" id="swg_username" required />
			</p>
			
			<p class="swg-form-row">
				<label for="swg_email"><?php esc_html_e( 'Email', 'swgtheme' ); ?> <span class="required">*</span></label>
				<input type="email" name="swg_email" id="swg_email" required />
			</p>
			
			<p class="swg-form-row">
				<label for="swg_password"><?php esc_html_e( 'Password', 'swgtheme' ); ?> <span class="required">*</span></label>
				<input type="password" name="swg_password" id="swg_password" required />
			</p>
			
			<p class="swg-form-row">
				<label for="swg_password_confirm"><?php esc_html_e( 'Confirm Password', 'swgtheme' ); ?> <span class="required">*</span></label>
				<input type="password" name="swg_password_confirm" id="swg_password_confirm" required />
			</p>
			
			<p class="swg-form-row">
				<input type="submit" name="swg_register_submit" value="<?php esc_attr_e( 'Register', 'swgtheme' ); ?>" />
			</p>
			
			<div id="swg-register-message"></div>
		</form>
		
		<style>
		.swg-register-form .swg-form-row { margin-bottom: 15px; }
		.swg-register-form label { display: block; margin-bottom: 5px; font-weight: bold; }
		.swg-register-form input[type="text"],
		.swg-register-form input[type="email"],
		.swg-register-form input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
		.swg-register-form input[type="submit"] { padding: 12px 30px; background: var(--primary-color, #dc3545); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
		.swg-register-form .required { color: red; }
		#swg-register-message { margin-top: 15px; padding: 10px; border-radius: 4px; }
		#swg-register-message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
		#swg-register-message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
		</style>
		<?php
		return ob_get_clean();
	}
	add_shortcode( 'swg_register', 'swgtheme_registration_form' );
	
	// Process registration
	add_action( 'init', function() {
		if ( isset( $_POST['swg_register_submit'] ) && isset( $_POST['swg_register_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_POST['swg_register_nonce'], 'swg_register_user' ) ) {
				return;
			}
			
			$username = sanitize_user( $_POST['swg_username'] );
			$email = sanitize_email( $_POST['swg_email'] );
			$password = $_POST['swg_password'];
			$password_confirm = $_POST['swg_password_confirm'];
			
			$errors = array();
			
			if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
				$errors[] = __( 'All fields are required.', 'swgtheme' );
			}
			
			if ( $password !== $password_confirm ) {
				$errors[] = __( 'Passwords do not match.', 'swgtheme' );
			}
			
			if ( username_exists( $username ) ) {
				$errors[] = __( 'Username already exists.', 'swgtheme' );
			}
			
			if ( email_exists( $email ) ) {
				$errors[] = __( 'Email already registered.', 'swgtheme' );
			}
			
			if ( ! is_email( $email ) ) {
				$errors[] = __( 'Invalid email address.', 'swgtheme' );
			}
			
			if ( empty( $errors ) ) {
				$user_id = wp_create_user( $username, $password, $email );
				
				if ( ! is_wp_error( $user_id ) ) {
					$user = new WP_User( $user_id );
					$role = get_option( 'swgtheme_default_user_role', 'subscriber' );
					$user->set_role( $role );
					
					if ( get_option( 'swgtheme_require_email_verification', '0' ) === '1' ) {
						update_user_meta( $user_id, 'swg_email_verified', '0' );
						$verification_key = wp_generate_password( 20, false );
						update_user_meta( $user_id, 'swg_verification_key', $verification_key );
						
						// Send verification email
						$verify_url = add_query_arg( array(
							'action' => 'verify_email',
							'key' => $verification_key,
							'user' => $user_id
						), home_url() );
						
						wp_mail(
							$email,
							__( 'Verify your email', 'swgtheme' ),
							sprintf( __( 'Click here to verify your email: %s', 'swgtheme' ), $verify_url )
						);
						
						wp_redirect( add_query_arg( 'registration', 'verify', wp_get_referer() ) );
					} else {
						wp_set_current_user( $user_id );
						wp_set_auth_cookie( $user_id );
						
						$redirect = get_option( 'swgtheme_registration_redirect', home_url() );
						wp_redirect( $redirect );
					}
					exit;
				}
			}
		}
	} );
}

/**
 * Public User Profiles
 */
if ( get_option( 'swgtheme_enable_public_profiles', '0' ) === '1' ) {
	// Add rewrite rule
	add_action( 'init', function() {
		add_rewrite_rule( '^user/([^/]+)/?$', 'index.php?swg_user_profile=$matches[1]', 'top' );
	} );
	
	add_filter( 'query_vars', function( $vars ) {
		$vars[] = 'swg_user_profile';
		return $vars;
	} );
	
	add_action( 'template_redirect', function() {
		$username = get_query_var( 'swg_user_profile' );
		if ( $username ) {
			$user = get_user_by( 'login', $username );
			if ( ! $user ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				return;
			}
			
			include( get_template_directory() . '/user-profile-template.php' );
			exit;
		}
	} );
	
	// Save custom profile fields
	add_action( 'personal_options_update', 'swgtheme_save_profile_fields' );
	add_action( 'edit_user_profile_update', 'swgtheme_save_profile_fields' );
	
	function swgtheme_save_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		
		$fields = get_option( 'swgtheme_profile_fields', array() );
		foreach ( $fields as $field ) {
			if ( isset( $_POST['swg_' . $field] ) ) {
				update_user_meta( $user_id, 'swg_' . $field, sanitize_text_field( $_POST['swg_' . $field] ) );
			}
		}
	}
	
	// Add fields to profile page
	add_action( 'show_user_profile', 'swgtheme_show_profile_fields' );
	add_action( 'edit_user_profile', 'swgtheme_show_profile_fields' );
	
	function swgtheme_show_profile_fields( $user ) {
		$fields = get_option( 'swgtheme_profile_fields', array() );
		$field_labels = array(
			'bio' => __( 'Biography', 'swgtheme' ),
			'location' => __( 'Location', 'swgtheme' ),
			'website' => __( 'Website', 'swgtheme' ),
			'twitter' => __( 'Twitter', 'swgtheme' ),
			'facebook' => __( 'Facebook', 'swgtheme' ),
			'instagram' => __( 'Instagram', 'swgtheme' ),
			'linkedin' => __( 'LinkedIn', 'swgtheme' ),
		);
		
		echo '<h3>' . __( 'Additional Information', 'swgtheme' ) . '</h3>';
		echo '<table class="form-table">';
		foreach ( $fields as $field ) {
			$value = get_user_meta( $user->ID, 'swg_' . $field, true );
			$type = ( $field === 'bio' ) ? 'textarea' : 'text';
			
			echo '<tr><th><label for="swg_' . esc_attr( $field ) . '">' . esc_html( $field_labels[ $field ] ) . '</label></th><td>';
			if ( $type === 'textarea' ) {
				echo '<textarea name="swg_' . esc_attr( $field ) . '" id="swg_' . esc_attr( $field ) . '" rows="5" cols="30">' . esc_textarea( $value ) . '</textarea>';
			} else {
				echo '<input type="text" name="swg_' . esc_attr( $field ) . '" id="swg_' . esc_attr( $field ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
}

/**
 * Custom Avatar System
 */
if ( get_option( 'swgtheme_enable_custom_avatars', '0' ) === '1' ) {
	// Add avatar upload to profile
	add_action( 'show_user_profile', 'swgtheme_avatar_upload_field' );
	add_action( 'edit_user_profile', 'swgtheme_avatar_upload_field' );
	
	function swgtheme_avatar_upload_field( $user ) {
		?>
		<h3><?php esc_html_e( 'Custom Avatar', 'swgtheme' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="swg_avatar"><?php esc_html_e( 'Upload Avatar', 'swgtheme' ); ?></label></th>
				<td>
					<?php
					$avatar_url = get_user_meta( $user->ID, 'swg_avatar', true );
					if ( $avatar_url ) {
						echo '<img src="' . esc_url( $avatar_url ) . '" style="width: 96px; height: 96px; border-radius: 50%; display: block; margin-bottom: 10px;" />';
					}
					?>
					<input type="file" name="swg_avatar" id="swg_avatar" accept="image/*" />
					<p class="description"><?php printf( __( 'Max size: %d MB. Recommended: 256x256px', 'swgtheme' ), get_option( 'swgtheme_avatar_max_size', '2' ) ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
	
	// Save avatar
	add_action( 'personal_options_update', 'swgtheme_save_avatar' );
	add_action( 'edit_user_profile_update', 'swgtheme_save_avatar' );
	
	function swgtheme_save_avatar( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		
		if ( ! empty( $_FILES['swg_avatar']['name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			
			$max_size = get_option( 'swgtheme_avatar_max_size', '2' ) * 1024 * 1024;
			
			if ( $_FILES['swg_avatar']['size'] > $max_size ) {
				return false;
			}
			
			$uploaded = wp_handle_upload( $_FILES['swg_avatar'], array( 'test_form' => false ) );
			
			if ( isset( $uploaded['file'] ) ) {
				$attachment = array(
					'post_mime_type' => $uploaded['type'],
					'post_title'     => 'Avatar for user ' . $user_id,
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				
				$attach_id = wp_insert_attachment( $attachment, $uploaded['file'] );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded['file'] );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				
				update_user_meta( $user_id, 'swg_avatar', $uploaded['url'] );
			}
		}
	}
	
	// Override WordPress avatar
	add_filter( 'get_avatar_url', function( $url, $id_or_email, $args ) {
		$user = false;
		
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', absint( $id_or_email->user_id ) );
			}
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		}
		
		if ( $user && is_object( $user ) ) {
			$custom_avatar = get_user_meta( $user->ID, 'swg_avatar', true );
			if ( $custom_avatar ) {
				return $custom_avatar;
			}
		}
		
		return $url;
	}, 10, 3 );
}

/**
 * Content Restriction
 */
if ( get_option( 'swgtheme_enable_content_restriction', '0' ) === '1' ) {
	function swgtheme_members_only_shortcode( $atts, $content = null ) {
		if ( is_user_logged_in() ) {
			return do_shortcode( $content );
		} else {
			$message = get_option( 'swgtheme_restriction_message', 'This content is available to members only. Please log in to view.' );
			return '<div class="swg-members-only-message" style="padding: 20px; background: #f8f9fa; border-left: 4px solid var(--primary-color, #dc3545); margin: 20px 0;">' . esc_html( $message ) . ' <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . __( 'Log in', 'swgtheme' ) . '</a></div>';
		}
	}
	add_shortcode( 'members_only', 'swgtheme_members_only_shortcode' );
}

/**
 * User Dashboard
 */
if ( get_option( 'swgtheme_enable_user_dashboard', '0' ) === '1' ) {
	function swgtheme_user_dashboard_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . __( 'You must be logged in to view your dashboard.', 'swgtheme' ) . '</p>';
		}
		
		$user_id = get_current_user_id();
		$user = wp_get_current_user();
		
		$post_count = count_user_posts( $user_id, 'post' );
		$comment_count = get_comments( array( 'user_id' => $user_id, 'status' => 'approve', 'count' => true ) );
		
		ob_start();
		?>
		<div class="swg-user-dashboard">
			<h2><?php printf( __( 'Welcome, %s', 'swgtheme' ), esc_html( $user->display_name ) ); ?></h2>
			
			<div class="swg-dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
				<div class="swg-stat-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
					<div style="font-size: 36px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo esc_html( $post_count ); ?></div>
					<div><?php esc_html_e( 'Posts Published', 'swgtheme' ); ?></div>
				</div>
				
				<div class="swg-stat-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
					<div style="font-size: 36px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo esc_html( $comment_count ); ?></div>
					<div><?php esc_html_e( 'Comments Made', 'swgtheme' ); ?></div>
				</div>
				
				<div class="swg-stat-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
					<div style="font-size: 36px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo esc_html( human_time_diff( strtotime( $user->user_registered ), current_time( 'timestamp' ) ) ); ?></div>
					<div><?php esc_html_e( 'Member For', 'swgtheme' ); ?></div>
				</div>
			</div>
			
			<?php if ( get_option( 'swgtheme_enable_badges', '0' ) === '1' ): ?>
			<h3><?php esc_html_e( 'Your Badges', 'swgtheme' ); ?></h3>
			<div class="swg-user-badges" style="display: flex; gap: 10px; flex-wrap: wrap; margin: 20px 0;">
				<?php
				$badges = get_user_meta( $user_id, 'swg_badges', true );
				if ( $badges && is_array( $badges ) ) {
					foreach ( $badges as $badge ) {
						echo '<div class="swg-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 20px; font-size: 14px;">';
						echo esc_html( $badge['name'] ) . ' <span style="font-size: 20px;">' . esc_html( $badge['icon'] ) . '</span>';
						echo '</div>';
					}
				} else {
					echo '<p>' . __( 'No badges earned yet. Keep contributing!', 'swgtheme' ) . '</p>';
				}
				?>
			</div>
			<?php endif; ?>
			
			<h3><?php esc_html_e( 'Recent Posts', 'swgtheme' ); ?></h3>
			<?php
			$recent_posts = get_posts( array(
				'author'         => $user_id,
				'posts_per_page' => 5,
				'post_status'    => 'publish',
			) );
			
			if ( $recent_posts ) {
				echo '<ul class="swg-recent-posts">';
				foreach ( $recent_posts as $post ) {
					echo '<li><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a> - ' . get_the_date( '', $post->ID ) . '</li>';
				}
				echo '</ul>';
			} else {
				echo '<p>' . __( 'You haven\'t published any posts yet.', 'swgtheme' ) . '</p>';
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}
	add_shortcode( 'user_dashboard', 'swgtheme_user_dashboard_shortcode' );
}

/**
 * Badge System
 */
if ( get_option( 'swgtheme_enable_badges', '0' ) === '1' ) {
	// Award badges automatically
	function swgtheme_check_and_award_badges( $user_id ) {
		$badges = get_user_meta( $user_id, 'swg_badges', true );
		if ( ! is_array( $badges ) ) {
			$badges = array();
		}
		
		$badge_ids = wp_list_pluck( $badges, 'id' );
		
		// First Post
		if ( ! in_array( 'first_post', $badge_ids ) ) {
			$post_count = count_user_posts( $user_id, 'post' );
			if ( $post_count >= 1 ) {
				$badges[] = array( 'id' => 'first_post', 'name' => __( 'First Post', 'swgtheme' ), 'icon' => '🏆' );
			}
		}
		
		// Active Commenter
		if ( ! in_array( 'active_commenter', $badge_ids ) ) {
			$comment_count = get_comments( array( 'user_id' => $user_id, 'status' => 'approve', 'count' => true ) );
			if ( $comment_count >= 10 ) {
				$badges[] = array( 'id' => 'active_commenter', 'name' => __( 'Active Commenter', 'swgtheme' ), 'icon' => '⭐' );
			}
		}
		
		// Prolific Writer
		if ( ! in_array( 'prolific_writer', $badge_ids ) ) {
			$post_count = count_user_posts( $user_id, 'post' );
			if ( $post_count >= 25 ) {
				$badges[] = array( 'id' => 'prolific_writer', 'name' => __( 'Prolific Writer', 'swgtheme' ), 'icon' => '🎖️' );
			}
		}
		
		// Veteran
		if ( ! in_array( 'veteran', $badge_ids ) ) {
			$post_count = count_user_posts( $user_id, 'post' );
			if ( $post_count >= 100 ) {
				$badges[] = array( 'id' => 'veteran', 'name' => __( 'Veteran', 'swgtheme' ), 'icon' => '👑' );
			}
		}
		
		// Discussion Leader
		if ( ! in_array( 'discussion_leader', $badge_ids ) ) {
			$comment_count = get_comments( array( 'user_id' => $user_id, 'status' => 'approve', 'count' => true ) );
			if ( $comment_count >= 50 ) {
				$badges[] = array( 'id' => 'discussion_leader', 'name' => __( 'Discussion Leader', 'swgtheme' ), 'icon' => '💬' );
			}
		}
		
		update_user_meta( $user_id, 'swg_badges', $badges );
	}
	
	// Check badges on post publish
	add_action( 'publish_post', function( $post_id ) {
		$post = get_post( $post_id );
		swgtheme_check_and_award_badges( $post->post_author );
	} );
	
	// Check badges on comment approval
	add_action( 'comment_post', function( $comment_id, $approved ) {
		if ( $approved === 1 ) {
			$comment = get_comment( $comment_id );
			if ( $comment->user_id ) {
				swgtheme_check_and_award_badges( $comment->user_id );
			}
		}
	}, 10, 2 );
}

/**
 * Badge Management Page
 */
function swgtheme_badges_management_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'User Badges Management', 'swgtheme' ); ?></h1>
		<p><?php esc_html_e( 'View and manage user badges and achievements.', 'swgtheme' ); ?></p>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'User', 'swgtheme' ); ?></th>
					<th><?php esc_html_e( 'Badges Earned', 'swgtheme' ); ?></th>
					<th><?php esc_html_e( 'Total Posts', 'swgtheme' ); ?></th>
					<th><?php esc_html_e( 'Total Comments', 'swgtheme' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$users = get_users( array( 'orderby' => 'post_count', 'order' => 'DESC' ) );
				foreach ( $users as $user ) {
					$badges = get_user_meta( $user->ID, 'swg_badges', true );
					$badge_count = is_array( $badges ) ? count( $badges ) : 0;
					$post_count = count_user_posts( $user->ID, 'post' );
					$comment_count = get_comments( array( 'user_id' => $user->ID, 'status' => 'approve', 'count' => true ) );
					
					echo '<tr>';
					echo '<td><strong>' . esc_html( $user->display_name ) . '</strong><br><small>' . esc_html( $user->user_email ) . '</small></td>';
					echo '<td>';
					if ( is_array( $badges ) && ! empty( $badges ) ) {
						foreach ( $badges as $badge ) {
							echo '<span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 5px 10px; border-radius: 12px; font-size: 12px; margin-right: 5px; display: inline-block; margin-bottom: 5px;">';
							echo esc_html( $badge['icon'] ) . ' ' . esc_html( $badge['name'] );
							echo '</span>';
						}
					} else {
						echo '—';
					}
					echo '</td>';
					echo '<td>' . esc_html( $post_count ) . '</td>';
					echo '<td>' . esc_html( $comment_count ) . '</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}

// ========================================
// DOCUMENTATION & SUPPORT FEATURES
// ========================================

/**
 * Register Documentation & Support Settings
 */
function swgtheme_register_documentation_settings() {
	// Knowledge Base
	register_setting( 'swgtheme_documentation_options', 'swgtheme_enable_knowledge_base' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_kb_slug' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_kb_categories' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_kb_search' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_kb_views' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_kb_voting' );
	
	// FAQ System
	register_setting( 'swgtheme_documentation_options', 'swgtheme_enable_faq' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_faq_accordion_style' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_faq_schema' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_faq_search' );
	
	// Video Tutorial Library
	register_setting( 'swgtheme_documentation_options', 'swgtheme_enable_video_library' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_video_provider' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_video_categories' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_video_playlist' );
	
	// Support Ticket System
	register_setting( 'swgtheme_documentation_options', 'swgtheme_enable_support_tickets' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_ticket_email' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_ticket_priority' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_ticket_departments' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_ticket_auto_response' );
	
	// Contextual Help
	register_setting( 'swgtheme_documentation_options', 'swgtheme_enable_contextual_help' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_help_position' );
	register_setting( 'swgtheme_documentation_options', 'swgtheme_help_trigger' );
}
add_action( 'admin_init', 'swgtheme_register_documentation_settings' );

/**
 * Add Documentation & Support Admin Menu
 */
function swgtheme_documentation_menu() {
	add_theme_page(
		'Documentation & Support',
		'Documentation & Support',
		'manage_options',
		'swgtheme-documentation',
		'swgtheme_documentation_page'
	);
}
add_action( 'admin_menu', 'swgtheme_documentation_menu' );

/**
 * Knowledge Base Custom Post Type
 */
function swgtheme_register_knowledge_base_cpt() {
	if ( get_option( 'swgtheme_enable_knowledge_base' ) ) {
		$slug = get_option( 'swgtheme_kb_slug', 'knowledge-base' );
		
		register_post_type( 'knowledge_base', array(
			'labels' => array(
				'name' => 'Knowledge Base',
				'singular_name' => 'Article',
				'add_new' => 'Add New Article',
				'add_new_item' => 'Add New Article',
				'edit_item' => 'Edit Article',
				'new_item' => 'New Article',
				'view_item' => 'View Article',
				'search_items' => 'Search Articles',
				'not_found' => 'No articles found',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array( 'slug' => $slug ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions' ),
			'menu_icon' => 'dashicons-book-alt',
			'show_in_rest' => true,
		) );
		
		// KB Categories
		if ( get_option( 'swgtheme_kb_categories' ) ) {
			register_taxonomy( 'kb_category', 'knowledge_base', array(
				'labels' => array(
					'name' => 'KB Categories',
					'singular_name' => 'KB Category',
				),
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite' => array( 'slug' => $slug . '-category' ),
			) );
		}
	}
}
add_action( 'init', 'swgtheme_register_knowledge_base_cpt' );

/**
 * Track KB Article Views
 */
function swgtheme_track_kb_views() {
	if ( get_option( 'swgtheme_kb_views' ) && is_singular( 'knowledge_base' ) ) {
		$post_id = get_the_ID();
		$views = get_post_meta( $post_id, 'kb_views', true );
		$views = $views ? intval( $views ) + 1 : 1;
		update_post_meta( $post_id, 'kb_views', $views );
	}
}
add_action( 'wp_head', 'swgtheme_track_kb_views' );

/**
 * KB Article Voting System
 */
function swgtheme_kb_voting_html() {
	if ( ! get_option( 'swgtheme_kb_voting' ) || ! is_singular( 'knowledge_base' ) ) {
		return;
	}
	
	$post_id = get_the_ID();
	$helpful = get_post_meta( $post_id, 'kb_helpful', true ) ?: 0;
	$not_helpful = get_post_meta( $post_id, 'kb_not_helpful', true ) ?: 0;
	?>
	<div class="kb-voting" style="margin: 30px 0; padding: 20px; background: #f9f9f9; border-radius: 8px; text-align: center;">
		<p style="margin-bottom: 15px; font-weight: 600;">Was this article helpful?</p>
		<button class="kb-vote-btn" data-vote="helpful" data-post="<?php echo esc_attr( $post_id ); ?>" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 0 5px;">
			👍 Yes (<?php echo esc_html( $helpful ); ?>)
		</button>
		<button class="kb-vote-btn" data-vote="not-helpful" data-post="<?php echo esc_attr( $post_id ); ?>" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 0 5px;">
			👎 No (<?php echo esc_html( $not_helpful ); ?>)
		</button>
	</div>
	<?php
}
add_action( 'the_content', function( $content ) {
	if ( is_singular( 'knowledge_base' ) && get_option( 'swgtheme_kb_voting' ) ) {
		ob_start();
		swgtheme_kb_voting_html();
		$voting = ob_get_clean();
		return $content . $voting;
	}
	return $content;
} );

/**
 * AJAX Handler for KB Voting
 */
function swgtheme_kb_vote_handler() {
	check_ajax_referer( 'kb_vote_nonce', 'nonce' );
	
	$post_id = intval( $_POST['post_id'] );
	$vote = sanitize_text_field( $_POST['vote'] );
	
	if ( $vote === 'helpful' ) {
		$count = get_post_meta( $post_id, 'kb_helpful', true ) ?: 0;
		update_post_meta( $post_id, 'kb_helpful', intval( $count ) + 1 );
	} else {
		$count = get_post_meta( $post_id, 'kb_not_helpful', true ) ?: 0;
		update_post_meta( $post_id, 'kb_not_helpful', intval( $count ) + 1 );
	}
	
	wp_send_json_success();
}
add_action( 'wp_ajax_kb_vote', 'swgtheme_kb_vote_handler' );
add_action( 'wp_ajax_nopriv_kb_vote', 'swgtheme_kb_vote_handler' );

/**
 * KB Voting JavaScript
 */
function swgtheme_kb_voting_script() {
	if ( get_option( 'swgtheme_kb_voting' ) && is_singular( 'knowledge_base' ) ) {
		?>
		<script>
		jQuery(document).ready(function($) {
			$('.kb-vote-btn').on('click', function() {
				var btn = $(this);
				var vote = btn.data('vote');
				var postId = btn.data('post');
				
				$.ajax({
					url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					type: 'POST',
					data: {
						action: 'kb_vote',
						post_id: postId,
						vote: vote,
						nonce: '<?php echo wp_create_nonce( 'kb_vote_nonce' ); ?>'
					},
					success: function() {
						btn.prop('disabled', true);
						btn.text(vote === 'helpful' ? '✓ Marked Helpful' : '✓ Feedback Sent');
						btn.siblings('.kb-vote-btn').prop('disabled', true);
					}
				});
			});
		});
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'swgtheme_kb_voting_script' );

/**
 * FAQ Custom Post Type
 */
function swgtheme_register_faq_cpt() {
	if ( get_option( 'swgtheme_enable_faq' ) ) {
		register_post_type( 'faq', array(
			'labels' => array(
				'name' => 'FAQs',
				'singular_name' => 'FAQ',
				'add_new' => 'Add New FAQ',
				'add_new_item' => 'Add New FAQ',
				'edit_item' => 'Edit FAQ',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array( 'slug' => 'faq' ),
			'supports' => array( 'title', 'editor', 'page-attributes' ),
			'menu_icon' => 'dashicons-editor-help',
			'show_in_rest' => true,
		) );
		
		register_taxonomy( 'faq_category', 'faq', array(
			'labels' => array(
				'name' => 'FAQ Categories',
				'singular_name' => 'FAQ Category',
			),
			'hierarchical' => true,
			'show_in_rest' => true,
		) );
	}
}
add_action( 'init', 'swgtheme_register_faq_cpt' );

/**
 * FAQ Shortcode
 */
function swgtheme_faq_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'limit' => -1,
	), $atts );
	
	$args = array(
		'post_type' => 'faq',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby' => 'menu_order',
		'order' => 'ASC',
	);
	
	if ( $atts['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'faq_category',
				'field' => 'slug',
				'terms' => $atts['category'],
			),
		);
	}
	
	$faqs = new WP_Query( $args );
	
	ob_start();
	?>
	<div class="faq-accordion" style="max-width: 800px; margin: 0 auto;">
		<?php if ( $faqs->have_posts() ) : while ( $faqs->have_posts() ) : $faqs->the_post(); ?>
		<div class="faq-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
			<div class="faq-question" style="padding: 15px 20px; background: #f8f9fa; cursor: pointer; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
				<span><?php the_title(); ?></span>
				<span class="faq-toggle" style="font-size: 20px; transition: transform 0.3s;">+</span>
			</div>
			<div class="faq-answer" style="padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s;">
				<div style="padding: 15px 0;"><?php the_content(); ?></div>
			</div>
		</div>
		<?php endwhile; endif; wp_reset_postdata(); ?>
	</div>
	
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var questions = document.querySelectorAll('.faq-question');
		questions.forEach(function(question) {
			question.addEventListener('click', function() {
				var item = this.parentElement;
				var answer = item.querySelector('.faq-answer');
				var toggle = item.querySelector('.faq-toggle');
				var isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
				
				// Close all
				document.querySelectorAll('.faq-answer').forEach(function(a) {
					a.style.maxHeight = '0';
				});
				document.querySelectorAll('.faq-toggle').forEach(function(t) {
					t.textContent = '+';
					t.style.transform = 'rotate(0deg)';
				});
				
				// Open clicked if it was closed
				if (!isOpen) {
					answer.style.maxHeight = answer.scrollHeight + 'px';
					toggle.textContent = '−';
					toggle.style.transform = 'rotate(180deg)';
				}
			});
		});
	});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'faq', 'swgtheme_faq_shortcode' );

/**
 * FAQ Schema Markup
 */
function swgtheme_faq_schema() {
	if ( ! get_option( 'swgtheme_faq_schema' ) || ! is_post_type_archive( 'faq' ) ) {
		return;
	}
	
	$faqs = new WP_Query( array(
		'post_type' => 'faq',
		'posts_per_page' => -1,
	) );
	
	if ( ! $faqs->have_posts() ) {
		return;
	}
	
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'FAQPage',
		'mainEntity' => array(),
	);
	
	while ( $faqs->have_posts() ) {
		$faqs->the_post();
		$schema['mainEntity'][] = array(
			'@type' => 'Question',
			'name' => get_the_title(),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text' => wp_strip_all_tags( get_the_content() ),
			),
		);
	}
	wp_reset_postdata();
	
	echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
}
add_action( 'wp_head', 'swgtheme_faq_schema' );

/**
 * Video Tutorial Custom Post Type
 */
function swgtheme_register_video_tutorial_cpt() {
	if ( get_option( 'swgtheme_enable_video_library' ) ) {
		register_post_type( 'video_tutorial', array(
			'labels' => array(
				'name' => 'Video Tutorials',
				'singular_name' => 'Video Tutorial',
				'add_new' => 'Add New Video',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array( 'slug' => 'tutorials' ),
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon' => 'dashicons-video-alt3',
			'show_in_rest' => true,
		) );
		
		if ( get_option( 'swgtheme_video_categories' ) ) {
			register_taxonomy( 'video_category', 'video_tutorial', array(
				'labels' => array(
					'name' => 'Video Categories',
					'singular_name' => 'Video Category',
				),
				'hierarchical' => true,
				'show_in_rest' => true,
			) );
		}
	}
}
add_action( 'init', 'swgtheme_register_video_tutorial_cpt' );

/**
 * Video Tutorial Meta Box
 */
function swgtheme_video_meta_box() {
	add_meta_box(
		'video_url',
		'Video URL',
		'swgtheme_video_meta_box_callback',
		'video_tutorial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'swgtheme_video_meta_box' );

function swgtheme_video_meta_box_callback( $post ) {
	$video_url = get_post_meta( $post->ID, 'video_url', true );
	$video_duration = get_post_meta( $post->ID, 'video_duration', true );
	wp_nonce_field( 'video_meta_box', 'video_meta_box_nonce' );
	?>
	<p>
		<label for="video_url">Video URL (YouTube, Vimeo, etc.):</label><br>
		<input type="text" id="video_url" name="video_url" value="<?php echo esc_attr( $video_url ); ?>" style="width: 100%;">
	</p>
	<p>
		<label for="video_duration">Duration (e.g., 5:30):</label><br>
		<input type="text" id="video_duration" name="video_duration" value="<?php echo esc_attr( $video_duration ); ?>" style="width: 200px;">
	</p>
	<?php
}

function swgtheme_save_video_meta( $post_id ) {
	if ( ! isset( $_POST['video_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['video_meta_box_nonce'], 'video_meta_box' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['video_url'] ) ) {
		update_post_meta( $post_id, 'video_url', esc_url_raw( $_POST['video_url'] ) );
	}
	if ( isset( $_POST['video_duration'] ) ) {
		update_post_meta( $post_id, 'video_duration', sanitize_text_field( $_POST['video_duration'] ) );
	}
}
add_action( 'save_post_video_tutorial', 'swgtheme_save_video_meta' );

/**
 * Video Library Shortcode
 */
function swgtheme_video_library_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'columns' => 3,
	), $atts );
	
	$args = array(
		'post_type' => 'video_tutorial',
		'posts_per_page' => -1,
	);
	
	if ( $atts['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'video_category',
				'field' => 'slug',
				'terms' => $atts['category'],
			),
		);
	}
	
	$videos = new WP_Query( $args );
	
	ob_start();
	?>
	<div class="video-library" style="display: grid; grid-template-columns: repeat(<?php echo intval( $atts['columns'] ); ?>, 1fr); gap: 30px; margin: 30px 0;">
		<?php while ( $videos->have_posts() ) : $videos->the_post();
			$video_url = get_post_meta( get_the_ID(), 'video_url', true );
			$duration = get_post_meta( get_the_ID(), 'video_duration', true );
		?>
		<div class="video-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s;">
			<div class="video-thumbnail" style="position: relative; padding-bottom: 56.25%; background: #000;">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium', array( 'style' => 'position: absolute; width: 100%; height: 100%; object-fit: cover;' ) ); ?>
				<?php endif; ?>
				<?php if ( $duration ) : ?>
				<span style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
					<?php echo esc_html( $duration ); ?>
				</span>
				<?php endif; ?>
			</div>
			<div style="padding: 20px;">
				<h3 style="margin: 0 0 10px 0; font-size: 18px;">
					<a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #333;">
						<?php the_title(); ?>
					</a>
				</h3>
				<div style="font-size: 14px; color: #666; line-height: 1.6;">
					<?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
				</div>
				<?php if ( $video_url ) : ?>
				<a href="<?php echo esc_url( $video_url ); ?>" target="_blank" style="display: inline-block; margin-top: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">
					▶ Watch Now
				</a>
				<?php endif; ?>
			</div>
		</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	
	<style>
	.video-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 15px rgba(0,0,0,0.2);
	}
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode( 'video_library', 'swgtheme_video_library_shortcode' );

/**
 * Support Ticket Custom Post Type
 */
function swgtheme_register_support_ticket_cpt() {
	if ( get_option( 'swgtheme_enable_support_tickets' ) ) {
		register_post_type( 'support_ticket', array(
			'labels' => array(
				'name' => 'Support Tickets',
				'singular_name' => 'Ticket',
				'add_new' => 'Add New Ticket',
			),
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'post',
			'capabilities' => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap' => true,
			'supports' => array( 'title', 'editor', 'comments' ),
			'menu_icon' => 'dashicons-sos',
		) );
		
		register_taxonomy( 'ticket_status', 'support_ticket', array(
			'labels' => array(
				'name' => 'Status',
				'singular_name' => 'Status',
			),
			'hierarchical' => false,
			'public' => false,
			'show_ui' => true,
		) );
		
		if ( get_option( 'swgtheme_ticket_departments' ) ) {
			register_taxonomy( 'ticket_department', 'support_ticket', array(
				'labels' => array(
					'name' => 'Departments',
					'singular_name' => 'Department',
				),
				'hierarchical' => true,
				'public' => false,
				'show_ui' => true,
			) );
		}
	}
}
add_action( 'init', 'swgtheme_register_support_ticket_cpt' );

/**
 * Support Ticket Submission Form
 */
function swgtheme_support_ticket_form() {
	if ( ! is_user_logged_in() ) {
		return '<p>Please <a href="' . wp_login_url( get_permalink() ) . '">login</a> to submit a support ticket.</p>';
	}
	
	if ( isset( $_POST['submit_ticket'] ) && wp_verify_nonce( $_POST['ticket_nonce'], 'submit_ticket' ) ) {
		$title = sanitize_text_field( $_POST['ticket_title'] );
		$message = wp_kses_post( $_POST['ticket_message'] );
		$priority = sanitize_text_field( $_POST['ticket_priority'] );
		$department = sanitize_text_field( $_POST['ticket_department'] );
		
		$ticket_id = wp_insert_post( array(
			'post_type' => 'support_ticket',
			'post_title' => $title,
			'post_content' => $message,
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
		) );
		
		if ( $ticket_id ) {
			update_post_meta( $ticket_id, 'ticket_priority', $priority );
			wp_set_object_terms( $ticket_id, 'Open', 'ticket_status' );
			if ( $department ) {
				wp_set_object_terms( $ticket_id, $department, 'ticket_department' );
			}
			
			// Send email notification
			$admin_email = get_option( 'swgtheme_ticket_email', get_option( 'admin_email' ) );
			$user = wp_get_current_user();
			$subject = 'New Support Ticket: ' . $title;
			$body = "New support ticket from {$user->display_name} ({$user->user_email})\n\n";
			$body .= "Priority: {$priority}\n";
			$body .= "Message:\n{$message}\n\n";
			$body .= "View ticket: " . admin_url( 'post.php?post=' . $ticket_id . '&action=edit' );
			
			wp_mail( $admin_email, $subject, $body );
			
			// Auto-response
			if ( get_option( 'swgtheme_ticket_auto_response' ) ) {
				$auto_subject = 'Ticket #' . $ticket_id . ' Received';
				$auto_body = "Hello {$user->display_name},\n\nWe've received your support ticket and will respond as soon as possible.\n\nTicket ID: #{$ticket_id}\nSubject: {$title}\n\nThank you!";
				wp_mail( $user->user_email, $auto_subject, $auto_body );
			}
			
			return '<div style="padding: 15px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;">Ticket submitted successfully! Ticket ID: #' . $ticket_id . '</div>';
		}
	}
	
	ob_start();
	?>
	<form method="post" style="max-width: 600px; margin: 30px auto; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
		<?php wp_nonce_field( 'submit_ticket', 'ticket_nonce' ); ?>
		
		<h2 style="margin-top: 0;">Submit Support Ticket</h2>
		
		<p>
			<label for="ticket_title" style="display: block; font-weight: 600; margin-bottom: 5px;">Subject *</label>
			<input type="text" id="ticket_title" name="ticket_title" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
		</p>
		
		<p>
			<label for="ticket_priority" style="display: block; font-weight: 600; margin-bottom: 5px;">Priority *</label>
			<select id="ticket_priority" name="ticket_priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
				<option value="Low">Low</option>
				<option value="Normal" selected>Normal</option>
				<option value="High">High</option>
				<option value="Urgent">Urgent</option>
			</select>
		</p>
		
		<?php if ( get_option( 'swgtheme_ticket_departments' ) ) : ?>
		<p>
			<label for="ticket_department" style="display: block; font-weight: 600; margin-bottom: 5px;">Department</label>
			<select id="ticket_department" name="ticket_department" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
				<option value="">General Support</option>
				<option value="Technical">Technical</option>
				<option value="Billing">Billing</option>
				<option value="Sales">Sales</option>
			</select>
		</p>
		<?php endif; ?>
		
		<p>
			<label for="ticket_message" style="display: block; font-weight: 600; margin-bottom: 5px;">Message *</label>
			<textarea id="ticket_message" name="ticket_message" rows="8" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"></textarea>
		</p>
		
		<p>
			<button type="submit" name="submit_ticket" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: 600;">
				Submit Ticket
			</button>
		</p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'support_ticket', 'swgtheme_support_ticket_form' );

/**
 * Contextual Help Tooltips
 */
function swgtheme_contextual_help() {
	if ( ! get_option( 'swgtheme_enable_contextual_help' ) ) {
		return;
	}
	
	$position = get_option( 'swgtheme_help_position', 'bottom-right' );
	$trigger = get_option( 'swgtheme_help_trigger', 'click' );
	
	wp_enqueue_style( 'swgtheme-tooltips', get_template_directory_uri() . '/css/tooltips.css', array(), '1.0' );
	wp_add_inline_style( 'swgtheme-tooltips', '
		.help-tooltip {
			position: relative;
			display: inline-block;
			cursor: help;
			margin-left: 5px;
		}
		.help-tooltip .tooltip-icon {
			display: inline-block;
			width: 18px;
			height: 18px;
			line-height: 18px;
			text-align: center;
			background: #667eea;
			color: white;
			border-radius: 50%;
			font-size: 12px;
			font-weight: bold;
		}
		.help-tooltip .tooltip-content {
			display: none;
			position: absolute;
			' . ( strpos( $position, 'left' ) !== false ? 'right: 100%;' : 'left: 100%;' ) . '
			' . ( strpos( $position, 'top' ) !== false ? 'bottom: 0;' : 'top: 0;' ) . '
			margin-left: 10px;
			padding: 12px 15px;
			background: #333;
			color: white;
			border-radius: 6px;
			font-size: 14px;
			line-height: 1.5;
			width: 250px;
			z-index: 1000;
			box-shadow: 0 4px 6px rgba(0,0,0,0.2);
		}
		.help-tooltip:hover .tooltip-content {
			' . ( $trigger === 'hover' ? 'display: block;' : '' ) . '
		}
		.help-tooltip.active .tooltip-content {
			display: block;
		}
	' );
	
	if ( $trigger === 'click' ) {
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				$(".help-tooltip").on("click", function(e) {
					e.stopPropagation();
					$(".help-tooltip").not(this).removeClass("active");
					$(this).toggleClass("active");
				});
				$(document).on("click", function() {
					$(".help-tooltip").removeClass("active");
				});
			});
		' );
	}
}
add_action( 'wp_enqueue_scripts', 'swgtheme_contextual_help' );

/**
 * Help Tooltip Shortcode
 */
function swgtheme_help_tooltip_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts( array(
		'text' => '',
	), $atts );
	
	if ( ! get_option( 'swgtheme_enable_contextual_help' ) ) {
		return '';
	}
	
	return '<span class="help-tooltip">
		<span class="tooltip-icon">?</span>
		<span class="tooltip-content">' . esc_html( $atts['text'] ?: $content ) . '</span>
	</span>';
}
add_shortcode( 'help', 'swgtheme_help_tooltip_shortcode' );

/**
 * Documentation & Support Admin Page
 */
function swgtheme_documentation_page() {
	?>
	<div class="wrap">
		<h1>Documentation & Support Settings</h1>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_documentation_options' ); ?>
			
			<table class="form-table">
				<!-- Knowledge Base -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">📚 Knowledge Base System</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Knowledge Base</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_knowledge_base" value="1" <?php checked( get_option( 'swgtheme_enable_knowledge_base' ), 1 ); ?>>
							Create knowledge base custom post type
						</label>
					</td>
				</tr>
				<tr class="kb-option">
					<th scope="row">KB Slug</th>
					<td>
						<input type="text" name="swgtheme_kb_slug" value="<?php echo esc_attr( get_option( 'swgtheme_kb_slug', 'knowledge-base' ) ); ?>" class="regular-text">
						<p class="description">URL slug for knowledge base (e.g., /knowledge-base/article-name/)</p>
					</td>
				</tr>
				<tr class="kb-option">
					<th scope="row">KB Features</th>
					<td>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_kb_categories" value="1" <?php checked( get_option( 'swgtheme_kb_categories' ), 1 ); ?>>
							Enable KB categories
						</label>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_kb_search" value="1" <?php checked( get_option( 'swgtheme_kb_search' ), 1 ); ?>>
							Enable search functionality
						</label>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_kb_views" value="1" <?php checked( get_option( 'swgtheme_kb_views' ), 1 ); ?>>
							Track article views
						</label>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_kb_voting" value="1" <?php checked( get_option( 'swgtheme_kb_voting' ), 1 ); ?>>
							Enable helpful/not helpful voting
						</label>
					</td>
				</tr>
				
				<!-- FAQ System -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">❓ FAQ System</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable FAQ</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_faq" value="1" <?php checked( get_option( 'swgtheme_enable_faq' ), 1 ); ?>>
							Create FAQ custom post type with accordion display
						</label>
						<p class="description">Use shortcode: [faq] or [faq category="category-slug" limit="10"]</p>
					</td>
				</tr>
				<tr class="faq-option">
					<th scope="row">FAQ Features</th>
					<td>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_faq_schema" value="1" <?php checked( get_option( 'swgtheme_faq_schema' ), 1 ); ?>>
							Add FAQ schema markup (SEO)
						</label>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_faq_search" value="1" <?php checked( get_option( 'swgtheme_faq_search' ), 1 ); ?>>
							Enable FAQ search
						</label>
					</td>
				</tr>
				
				<!-- Video Tutorial Library -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🎥 Video Tutorial Library</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Video Library</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_video_library" value="1" <?php checked( get_option( 'swgtheme_enable_video_library' ), 1 ); ?>>
							Create video tutorial custom post type
						</label>
						<p class="description">Use shortcode: [video_library] or [video_library category="category-slug" columns="3"]</p>
					</td>
				</tr>
				<tr class="video-option">
					<th scope="row">Video Provider</th>
					<td>
						<select name="swgtheme_video_provider">
							<option value="youtube" <?php selected( get_option( 'swgtheme_video_provider' ), 'youtube' ); ?>>YouTube</option>
							<option value="vimeo" <?php selected( get_option( 'swgtheme_video_provider' ), 'vimeo' ); ?>>Vimeo</option>
							<option value="both" <?php selected( get_option( 'swgtheme_video_provider' ), 'both' ); ?>>Both</option>
						</select>
					</td>
				</tr>
				<tr class="video-option">
					<th scope="row">Video Features</th>
					<td>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_video_categories" value="1" <?php checked( get_option( 'swgtheme_video_categories' ), 1 ); ?>>
							Enable video categories
						</label>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_video_playlist" value="1" <?php checked( get_option( 'swgtheme_video_playlist' ), 1 ); ?>>
							Enable playlist functionality
						</label>
					</td>
				</tr>
				
				<!-- Support Ticket System -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🎫 Support Ticket System</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Support Tickets</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_support_tickets" value="1" <?php checked( get_option( 'swgtheme_enable_support_tickets' ), 1 ); ?>>
							Create support ticket system
						</label>
						<p class="description">Use shortcode: [support_ticket]</p>
					</td>
				</tr>
				<tr class="ticket-option">
					<th scope="row">Notification Email</th>
					<td>
						<input type="email" name="swgtheme_ticket_email" value="<?php echo esc_attr( get_option( 'swgtheme_ticket_email', get_option( 'admin_email' ) ) ); ?>" class="regular-text">
						<p class="description">Email address for ticket notifications</p>
					</td>
				</tr>
				<tr class="ticket-option">
					<th scope="row">Ticket Features</th>
					<td>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_ticket_priority" value="1" <?php checked( get_option( 'swgtheme_ticket_priority' ), 1 ); ?>>
							Enable priority levels (Low, Normal, High, Urgent)
						</label>
						<label style="display: block; margin-bottom: 8px;">
							<input type="checkbox" name="swgtheme_ticket_departments" value="1" <?php checked( get_option( 'swgtheme_ticket_departments' ), 1 ); ?>>
							Enable departments
						</label>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_ticket_auto_response" value="1" <?php checked( get_option( 'swgtheme_ticket_auto_response' ), 1 ); ?>>
							Send auto-response email to users
						</label>
					</td>
				</tr>
				
				<!-- Contextual Help -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">💡 Contextual Help Tooltips</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Help Tooltips</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_contextual_help" value="1" <?php checked( get_option( 'swgtheme_enable_contextual_help' ), 1 ); ?>>
							Enable contextual help tooltips
						</label>
						<p class="description">Use shortcode: [help text="Your help text here"]</p>
					</td>
				</tr>
				<tr class="help-option">
					<th scope="row">Tooltip Position</th>
					<td>
						<select name="swgtheme_help_position">
							<option value="top-right" <?php selected( get_option( 'swgtheme_help_position' ), 'top-right' ); ?>>Top Right</option>
							<option value="top-left" <?php selected( get_option( 'swgtheme_help_position' ), 'top-left' ); ?>>Top Left</option>
							<option value="bottom-right" <?php selected( get_option( 'swgtheme_help_position' ), 'bottom-right' ); ?>>Bottom Right</option>
							<option value="bottom-left" <?php selected( get_option( 'swgtheme_help_position' ), 'bottom-left' ); ?>>Bottom Left</option>
						</select>
					</td>
				</tr>
				<tr class="help-option">
					<th scope="row">Trigger Method</th>
					<td>
						<select name="swgtheme_help_trigger">
							<option value="hover" <?php selected( get_option( 'swgtheme_help_trigger' ), 'hover' ); ?>>Hover</option>
							<option value="click" <?php selected( get_option( 'swgtheme_help_trigger' ), 'click' ); ?>>Click</option>
						</select>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		function toggleOptions() {
			$('.kb-option').toggle($('input[name="swgtheme_enable_knowledge_base"]').is(':checked'));
			$('.faq-option').toggle($('input[name="swgtheme_enable_faq"]').is(':checked'));
			$('.video-option').toggle($('input[name="swgtheme_enable_video_library"]').is(':checked'));
			$('.ticket-option').toggle($('input[name="swgtheme_enable_support_tickets"]').is(':checked'));
			$('.help-option').toggle($('input[name="swgtheme_enable_contextual_help"]').is(':checked'));
		}
		
		toggleOptions();
		$('input[name="swgtheme_enable_knowledge_base"], input[name="swgtheme_enable_faq"], input[name="swgtheme_enable_video_library"], input[name="swgtheme_enable_support_tickets"], input[name="swgtheme_enable_contextual_help"]').on('change', toggleOptions);
	});
	</script>
	<?php
}

// ========================================
// MULTI-LANGUAGE & TRANSLATION FEATURES
// ========================================

/**
 * Register Multi-language Settings
 */
function swgtheme_register_multilang_settings() {
	// Language Switcher
	register_setting( 'swgtheme_multilang_options', 'swgtheme_enable_language_switcher' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_switcher_position' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_switcher_style' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_switcher_flags' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_available_languages' );
	
	// Plugin Integration
	register_setting( 'swgtheme_multilang_options', 'swgtheme_wpml_support' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_polylang_support' );
	
	// RTL Support
	register_setting( 'swgtheme_multilang_options', 'swgtheme_enable_rtl' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_rtl_languages' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_rtl_mirror_layout' );
	
	// Translation Management
	register_setting( 'swgtheme_multilang_options', 'swgtheme_translation_fallback' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_show_untranslated' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_translation_notice' );
	
	// Auto-translate
	register_setting( 'swgtheme_multilang_options', 'swgtheme_enable_auto_translate' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_translate_api' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_translate_api_key' );
	
	// Language-specific Content
	register_setting( 'swgtheme_multilang_options', 'swgtheme_lang_specific_menu' );
	register_setting( 'swgtheme_multilang_options', 'swgtheme_lang_specific_widgets' );
}
add_action( 'admin_init', 'swgtheme_register_multilang_settings' );

/**
 * Add Multi-language Admin Menu
 */
function swgtheme_multilang_menu() {
	add_theme_page(
		'Multi-language & Translation',
		'Multi-language',
		'manage_options',
		'swgtheme-multilang',
		'swgtheme_multilang_page'
	);
}
add_action( 'admin_menu', 'swgtheme_multilang_menu' );

/**
 * Get Available Languages
 */
function swgtheme_get_available_languages() {
	$languages = get_option( 'swgtheme_available_languages', array() );
	
	if ( empty( $languages ) ) {
		// Default languages
		$languages = array(
			'en' => array( 'name' => 'English', 'flag' => '🇺🇸', 'rtl' => false ),
			'es' => array( 'name' => 'Español', 'flag' => '🇪🇸', 'rtl' => false ),
			'fr' => array( 'name' => 'Français', 'flag' => '🇫🇷', 'rtl' => false ),
			'de' => array( 'name' => 'Deutsch', 'flag' => '🇩🇪', 'rtl' => false ),
			'ar' => array( 'name' => 'العربية', 'flag' => '🇸🇦', 'rtl' => true ),
		);
	}
	
	return $languages;
}

/**
 * Get Current Language
 */
function swgtheme_get_current_language() {
	// Check if WPML is active
	if ( function_exists( 'icl_get_current_language' ) && get_option( 'swgtheme_wpml_support' ) ) {
		return icl_get_current_language();
	}
	
	// Check if Polylang is active
	if ( function_exists( 'pll_current_language' ) && get_option( 'swgtheme_polylang_support' ) ) {
		return pll_current_language();
	}
	
	// Check cookie
	if ( isset( $_COOKIE['swgtheme_language'] ) ) {
		return sanitize_text_field( $_COOKIE['swgtheme_language'] );
	}
	
	// Fallback to site language
	return substr( get_locale(), 0, 2 );
}

/**
 * Language Switcher
 */
function swgtheme_language_switcher() {
	if ( ! get_option( 'swgtheme_enable_language_switcher' ) ) {
		return;
	}
	
	$current_lang = swgtheme_get_current_language();
	$languages = swgtheme_get_available_languages();
	$style = get_option( 'swgtheme_switcher_style', 'dropdown' );
	$show_flags = get_option( 'swgtheme_switcher_flags', true );
	
	ob_start();
	
	if ( $style === 'dropdown' ) {
		?>
		<div class="language-switcher-dropdown" style="position: relative; display: inline-block;">
			<button class="lang-switcher-btn" style="background: none; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
				<?php if ( $show_flags && isset( $languages[ $current_lang ]['flag'] ) ) : ?>
					<span><?php echo $languages[ $current_lang ]['flag']; ?></span>
				<?php endif; ?>
				<span><?php echo isset( $languages[ $current_lang ] ) ? esc_html( $languages[ $current_lang ]['name'] ) : strtoupper( $current_lang ); ?></span>
				<span style="font-size: 12px;">▼</span>
			</button>
			<div class="lang-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 150px; z-index: 1000; margin-top: 5px;">
				<?php foreach ( $languages as $code => $lang ) : ?>
				<a href="#" class="lang-option" data-lang="<?php echo esc_attr( $code ); ?>" style="display: flex; align-items: center; gap: 8px; padding: 10px 15px; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
					<?php if ( $show_flags ) : ?>
						<span><?php echo $lang['flag']; ?></span>
					<?php endif; ?>
					<span><?php echo esc_html( $lang['name'] ); ?></span>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	} else {
		// Inline style
		?>
		<div class="language-switcher-inline" style="display: flex; gap: 10px; align-items: center;">
			<?php foreach ( $languages as $code => $lang ) : ?>
			<a href="#" class="lang-option <?php echo $code === $current_lang ? 'active' : ''; ?>" data-lang="<?php echo esc_attr( $code ); ?>" style="display: flex; align-items: center; gap: 5px; padding: 5px 10px; text-decoration: none; color: <?php echo $code === $current_lang ? '#667eea' : '#666'; ?>; border-bottom: 2px solid <?php echo $code === $current_lang ? '#667eea' : 'transparent'; ?>;">
				<?php if ( $show_flags ) : ?>
					<span><?php echo $lang['flag']; ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( $lang['name'] ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
	
	return ob_get_clean();
}

/**
 * Language Switcher JavaScript
 */
function swgtheme_language_switcher_script() {
	if ( ! get_option( 'swgtheme_enable_language_switcher' ) ) {
		return;
	}
	?>
	<script>
	jQuery(document).ready(function($) {
		// Dropdown toggle
		$('.lang-switcher-btn').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).siblings('.lang-dropdown-menu').toggle();
		});
		
		// Close dropdown on outside click
		$(document).on('click', function() {
			$('.lang-dropdown-menu').hide();
		});
		
		// Language selection
		$('.lang-option').on('click', function(e) {
			e.preventDefault();
			var lang = $(this).data('lang');
			
			// Set cookie
			document.cookie = 'swgtheme_language=' + lang + '; path=/; max-age=31536000';
			
			// Reload page
			location.reload();
		});
		
		// Hover effects
		$('.lang-option').hover(
			function() { $(this).css('background', '#f8f9fa'); },
			function() { $(this).css('background', 'white'); }
		);
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'swgtheme_language_switcher_script' );

/**
 * Add Language Switcher to Navigation
 */
function swgtheme_add_language_switcher_to_nav( $items, $args ) {
	$position = get_option( 'swgtheme_switcher_position', 'menu' );
	
	if ( $position === 'menu' && $args->theme_location === 'primary' ) {
		$items .= '<li class="menu-item menu-item-language-switcher">' . swgtheme_language_switcher() . '</li>';
	}
	
	return $items;
}
add_filter( 'wp_nav_menu_items', 'swgtheme_add_language_switcher_to_nav', 10, 2 );

/**
 * Language Switcher Shortcode
 */
function swgtheme_language_switcher_shortcode() {
	return swgtheme_language_switcher();
}
add_shortcode( 'language_switcher', 'swgtheme_language_switcher_shortcode' );

/**
 * RTL Support
 */
function swgtheme_rtl_support() {
	if ( ! get_option( 'swgtheme_enable_rtl' ) ) {
		return;
	}
	
	$current_lang = swgtheme_get_current_language();
	$rtl_languages = get_option( 'swgtheme_rtl_languages', array( 'ar', 'he', 'fa', 'ur' ) );
	
	if ( in_array( $current_lang, $rtl_languages ) ) {
		// Add RTL stylesheet
		wp_enqueue_style( 'swgtheme-rtl', get_template_directory_uri() . '/css/rtl.css', array(), '1.0' );
		
		// Add RTL body class
		add_filter( 'body_class', function( $classes ) {
			$classes[] = 'rtl';
			return $classes;
		} );
		
		// Mirror layout if enabled
		if ( get_option( 'swgtheme_rtl_mirror_layout' ) ) {
			add_action( 'wp_head', function() {
				echo '<style>body.rtl { direction: rtl; }</style>';
			} );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'swgtheme_rtl_support' );

/**
 * Create RTL Stylesheet
 */
function swgtheme_create_rtl_stylesheet() {
	$rtl_css_path = get_template_directory() . '/css/rtl.css';
	
	if ( ! file_exists( $rtl_css_path ) && get_option( 'swgtheme_enable_rtl' ) ) {
		$rtl_css = '/* RTL Styles */
body.rtl {
	direction: rtl;
	text-align: right;
}

body.rtl .site-header,
body.rtl .site-navigation,
body.rtl .site-content {
	direction: rtl;
}

body.rtl .menu {
	text-align: right;
}

body.rtl .menu li {
	float: right;
}

body.rtl .alignleft {
	float: right;
	margin: 0 0 1em 1em;
}

body.rtl .alignright {
	float: left;
	margin: 0 1em 1em 0;
}

body.rtl blockquote {
	border-left: none;
	border-right: 4px solid #667eea;
	padding-left: 0;
	padding-right: 20px;
}

body.rtl .sidebar {
	float: left;
}

body.rtl .content-area {
	float: right;
}
';
		
		$css_dir = get_template_directory() . '/css';
		if ( ! file_exists( $css_dir ) ) {
			wp_mkdir_p( $css_dir );
		}
		
		file_put_contents( $rtl_css_path, $rtl_css );
	}
}
add_action( 'admin_init', 'swgtheme_create_rtl_stylesheet' );

/**
 * Translation Fallback
 */
function swgtheme_translation_fallback( $text, $domain = 'default' ) {
	if ( ! get_option( 'swgtheme_translation_fallback' ) ) {
		return $text;
	}
	
	$current_lang = swgtheme_get_current_language();
	$fallback_lang = 'en';
	
	// Get translation
	$translated = translate( $text, $domain );
	
	// If no translation found and show untranslated is disabled
	if ( $translated === $text && ! get_option( 'swgtheme_show_untranslated' ) && $current_lang !== $fallback_lang ) {
		// Try to get fallback translation
		$locale_backup = get_locale();
		switch_to_locale( $fallback_lang . '_US' );
		$fallback_text = translate( $text, $domain );
		switch_to_locale( $locale_backup );
		
		return $fallback_text;
	}
	
	return $translated;
}

/**
 * Translation Notice for Untranslated Content
 */
function swgtheme_translation_notice() {
	if ( ! get_option( 'swgtheme_translation_notice' ) ) {
		return;
	}
	
	$current_lang = swgtheme_get_current_language();
	
	if ( is_singular() && $current_lang !== 'en' ) {
		$post_lang = get_post_meta( get_the_ID(), 'post_language', true );
		
		if ( empty( $post_lang ) || $post_lang !== $current_lang ) {
			echo '<div style="padding: 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0;">';
			echo '⚠️ This content is not available in your selected language. Showing original version.';
			echo '</div>';
		}
	}
}
add_action( 'the_content', function( $content ) {
	ob_start();
	swgtheme_translation_notice();
	$notice = ob_get_clean();
	return $notice . $content;
}, 1 );

/**
 * Auto-translate API Integration
 */
function swgtheme_auto_translate( $text, $target_lang ) {
	if ( ! get_option( 'swgtheme_enable_auto_translate' ) ) {
		return $text;
	}
	
	$api = get_option( 'swgtheme_translate_api', 'google' );
	$api_key = get_option( 'swgtheme_translate_api_key' );
	
	if ( empty( $api_key ) ) {
		return $text;
	}
	
	// Check cache first
	$cache_key = 'swg_translate_' . md5( $text . $target_lang );
	$cached = get_transient( $cache_key );
	
	if ( $cached !== false ) {
		return $cached;
	}
	
	$translated = '';
	
	if ( $api === 'google' ) {
		$url = 'https://translation.googleapis.com/language/translate/v2';
		$response = wp_remote_post( $url, array(
			'body' => json_encode( array(
				'q' => $text,
				'target' => $target_lang,
				'key' => $api_key,
			) ),
			'headers' => array( 'Content-Type' => 'application/json' ),
		) );
		
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $body['data']['translations'][0]['translatedText'] ) ) {
				$translated = $body['data']['translations'][0]['translatedText'];
			}
		}
	} elseif ( $api === 'deepl' ) {
		$url = 'https://api.deepl.com/v2/translate';
		$response = wp_remote_post( $url, array(
			'body' => array(
				'auth_key' => $api_key,
				'text' => $text,
				'target_lang' => strtoupper( $target_lang ),
			),
		) );
		
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $body['translations'][0]['text'] ) ) {
				$translated = $body['translations'][0]['text'];
			}
		}
	}
	
	// Cache for 1 week
	if ( ! empty( $translated ) ) {
		set_transient( $cache_key, $translated, WEEK_IN_SECONDS );
	}
	
	return ! empty( $translated ) ? $translated : $text;
}

/**
 * Language-specific Menus
 */
function swgtheme_language_specific_menu( $args ) {
	if ( ! get_option( 'swgtheme_lang_specific_menu' ) ) {
		return $args;
	}
	
	$current_lang = swgtheme_get_current_language();
	$menu_name = $args['theme_location'] . '_' . $current_lang;
	
	// Check if language-specific menu exists
	if ( has_nav_menu( $menu_name ) ) {
		$args['theme_location'] = $menu_name;
	}
	
	return $args;
}
add_filter( 'wp_nav_menu_args', 'swgtheme_language_specific_menu' );

/**
 * Register Language-specific Menu Locations
 */
function swgtheme_register_language_menus() {
	if ( ! get_option( 'swgtheme_lang_specific_menu' ) ) {
		return;
	}
	
	$languages = swgtheme_get_available_languages();
	
	foreach ( $languages as $code => $lang ) {
		register_nav_menu( 'primary_' . $code, 'Primary Menu (' . $lang['name'] . ')' );
	}
}
add_action( 'init', 'swgtheme_register_language_menus' );

/**
 * Language-specific Widgets
 */
function swgtheme_language_specific_widgets( $sidebars_widgets ) {
	if ( ! get_option( 'swgtheme_lang_specific_widgets' ) ) {
		return $sidebars_widgets;
	}
	
	$current_lang = swgtheme_get_current_language();
	
	// Replace sidebars with language-specific versions if they exist
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
		$lang_sidebar_id = $sidebar_id . '_' . $current_lang;
		
		if ( isset( $sidebars_widgets[ $lang_sidebar_id ] ) ) {
			$sidebars_widgets[ $sidebar_id ] = $sidebars_widgets[ $lang_sidebar_id ];
		}
	}
	
	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'swgtheme_language_specific_widgets' );

/**
 * Register Language-specific Sidebars
 */
function swgtheme_register_language_sidebars() {
	if ( ! get_option( 'swgtheme_lang_specific_widgets' ) ) {
		return;
	}
	
	$languages = swgtheme_get_available_languages();
	
	foreach ( $languages as $code => $lang ) {
		register_sidebar( array(
			'name'          => 'Sidebar (' . $lang['name'] . ')',
			'id'            => 'sidebar_' . $code,
			'description'   => 'Language-specific sidebar for ' . $lang['name'],
			'before_widget' => '<div class="widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'swgtheme_register_language_sidebars' );

/**
 * Multi-language Admin Page
 */
function swgtheme_multilang_page() {
	?>
	<div class="wrap">
		<h1>Multi-language & Translation Settings</h1>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_multilang_options' ); ?>
			
			<table class="form-table">
				<!-- Language Switcher -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🌍 Language Switcher</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Language Switcher</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_language_switcher" value="1" <?php checked( get_option( 'swgtheme_enable_language_switcher' ), 1 ); ?>>
							Enable language switcher
						</label>
						<p class="description">Use shortcode: [language_switcher] or add to menu automatically</p>
					</td>
				</tr>
				<tr class="switcher-option">
					<th scope="row">Switcher Position</th>
					<td>
						<select name="swgtheme_switcher_position">
							<option value="menu" <?php selected( get_option( 'swgtheme_switcher_position' ), 'menu' ); ?>>In Navigation Menu</option>
							<option value="header" <?php selected( get_option( 'swgtheme_switcher_position' ), 'header' ); ?>>In Header</option>
							<option value="footer" <?php selected( get_option( 'swgtheme_switcher_position' ), 'footer' ); ?>>In Footer</option>
							<option value="shortcode" <?php selected( get_option( 'swgtheme_switcher_position' ), 'shortcode' ); ?>>Shortcode Only</option>
						</select>
					</td>
				</tr>
				<tr class="switcher-option">
					<th scope="row">Switcher Style</th>
					<td>
						<select name="swgtheme_switcher_style">
							<option value="dropdown" <?php selected( get_option( 'swgtheme_switcher_style' ), 'dropdown' ); ?>>Dropdown</option>
							<option value="inline" <?php selected( get_option( 'swgtheme_switcher_style' ), 'inline' ); ?>>Inline Links</option>
						</select>
					</td>
				</tr>
				<tr class="switcher-option">
					<th scope="row">Show Flags</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_switcher_flags" value="1" <?php checked( get_option( 'swgtheme_switcher_flags' ), 1 ); ?>>
							Show country flags next to language names
						</label>
					</td>
				</tr>
				
				<!-- Plugin Integration -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🔌 Plugin Integration</h2></th>
				</tr>
				<tr>
					<th scope="row">WPML Support</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_wpml_support" value="1" <?php checked( get_option( 'swgtheme_wpml_support' ), 1 ); ?>>
							Enable WPML integration
						</label>
						<p class="description">Integrate with WPML if installed</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Polylang Support</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_polylang_support" value="1" <?php checked( get_option( 'swgtheme_polylang_support' ), 1 ); ?>>
							Enable Polylang integration
						</label>
						<p class="description">Integrate with Polylang if installed</p>
					</td>
				</tr>
				
				<!-- RTL Support -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">↔️ RTL Support</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable RTL</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_rtl" value="1" <?php checked( get_option( 'swgtheme_enable_rtl' ), 1 ); ?>>
							Enable right-to-left language support
						</label>
					</td>
				</tr>
				<tr class="rtl-option">
					<th scope="row">RTL Languages</th>
					<td>
						<input type="text" name="swgtheme_rtl_languages" value="<?php echo esc_attr( implode( ', ', get_option( 'swgtheme_rtl_languages', array( 'ar', 'he', 'fa', 'ur' ) ) ) ); ?>" class="regular-text">
						<p class="description">Comma-separated language codes (e.g., ar, he, fa, ur)</p>
					</td>
				</tr>
				<tr class="rtl-option">
					<th scope="row">Mirror Layout</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_rtl_mirror_layout" value="1" <?php checked( get_option( 'swgtheme_rtl_mirror_layout' ), 1 ); ?>>
							Automatically mirror entire layout for RTL languages
						</label>
					</td>
				</tr>
				
				<!-- Translation Management -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">📝 Translation Management</h2></th>
				</tr>
				<tr>
					<th scope="row">Translation Fallback</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_translation_fallback" value="1" <?php checked( get_option( 'swgtheme_translation_fallback' ), 1 ); ?>>
							Fall back to English if translation not available
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Show Untranslated</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_show_untranslated" value="1" <?php checked( get_option( 'swgtheme_show_untranslated' ), 1 ); ?>>
							Show original content if no translation exists
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Translation Notice</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_translation_notice" value="1" <?php checked( get_option( 'swgtheme_translation_notice' ), 1 ); ?>>
							Show notice when viewing untranslated content
						</label>
					</td>
				</tr>
				
				<!-- Auto-translate -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🤖 Auto-translate API</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Auto-translate</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_auto_translate" value="1" <?php checked( get_option( 'swgtheme_enable_auto_translate' ), 1 ); ?>>
							Enable automatic translation via API
						</label>
					</td>
				</tr>
				<tr class="translate-option">
					<th scope="row">Translation API</th>
					<td>
						<select name="swgtheme_translate_api">
							<option value="google" <?php selected( get_option( 'swgtheme_translate_api' ), 'google' ); ?>>Google Translate API</option>
							<option value="deepl" <?php selected( get_option( 'swgtheme_translate_api' ), 'deepl' ); ?>>DeepL API</option>
						</select>
					</td>
				</tr>
				<tr class="translate-option">
					<th scope="row">API Key</th>
					<td>
						<input type="text" name="swgtheme_translate_api_key" value="<?php echo esc_attr( get_option( 'swgtheme_translate_api_key' ) ); ?>" class="regular-text">
						<p class="description">Enter your translation API key</p>
					</td>
				</tr>
				
				<!-- Language-specific Content -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🎯 Language-specific Content</h2></th>
				</tr>
				<tr>
					<th scope="row">Language-specific Menus</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_lang_specific_menu" value="1" <?php checked( get_option( 'swgtheme_lang_specific_menu' ), 1 ); ?>>
							Enable separate menu for each language
						</label>
						<p class="description">Creates menu locations like "Primary Menu (Español)"</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Language-specific Widgets</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_lang_specific_widgets" value="1" <?php checked( get_option( 'swgtheme_lang_specific_widgets' ), 1 ); ?>>
							Enable separate widget areas for each language
						</label>
						<p class="description">Creates sidebars like "Sidebar (Español)"</p>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		function toggleOptions() {
			$('.switcher-option').toggle($('input[name="swgtheme_enable_language_switcher"]').is(':checked'));
			$('.rtl-option').toggle($('input[name="swgtheme_enable_rtl"]').is(':checked'));
			$('.translate-option').toggle($('input[name="swgtheme_enable_auto_translate"]').is(':checked'));
		}
		
		toggleOptions();
		$('input[name="swgtheme_enable_language_switcher"], input[name="swgtheme_enable_rtl"], input[name="swgtheme_enable_auto_translate"]').on('change', toggleOptions);
	});
	</script>
	<?php
}

// ========================================
// ADVANCED SECURITY FEATURES
// ========================================

/**
 * Register Security Settings
 */
function swgtheme_register_security_settings() {
	// Login Security
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_login_limiting' );
	register_setting( 'swgtheme_security_options', 'swgtheme_login_attempts' );
	register_setting( 'swgtheme_security_options', 'swgtheme_lockout_duration' );
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_2fa' );
	register_setting( 'swgtheme_security_options', 'swgtheme_2fa_method' );
	
	// IP Security
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_ip_blocking' );
	register_setting( 'swgtheme_security_options', 'swgtheme_blocked_ips' );
	register_setting( 'swgtheme_security_options', 'swgtheme_whitelist_ips' );
	register_setting( 'swgtheme_security_options', 'swgtheme_auto_block_failed_login' );
	
	// File Security
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_file_detection' );
	register_setting( 'swgtheme_security_options', 'swgtheme_scan_uploads' );
	register_setting( 'swgtheme_security_options', 'swgtheme_allowed_file_types' );
	
	// Database Security
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_db_hardening' );
	register_setting( 'swgtheme_security_options', 'swgtheme_db_prefix_obfuscation' );
	register_setting( 'swgtheme_security_options', 'swgtheme_disable_db_error_display' );
	
	// WordPress Security
	register_setting( 'swgtheme_security_options', 'swgtheme_disable_xmlrpc' );
	register_setting( 'swgtheme_security_options', 'swgtheme_disable_file_editing' );
	register_setting( 'swgtheme_security_options', 'swgtheme_hide_wp_version' );
	register_setting( 'swgtheme_security_options', 'swgtheme_disable_user_enumeration' );
	
	// Admin Security
	register_setting( 'swgtheme_security_options', 'swgtheme_custom_admin_url' );
	register_setting( 'swgtheme_security_options', 'swgtheme_admin_url_slug' );
	register_setting( 'swgtheme_security_options', 'swgtheme_custom_login_url' );
	register_setting( 'swgtheme_security_options', 'swgtheme_login_url_slug' );
	
	// Forms Security
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_honeypot' );
	register_setting( 'swgtheme_security_options', 'swgtheme_honeypot_forms' );
	
	// HTTP Security Headers
	register_setting( 'swgtheme_security_options', 'swgtheme_enable_security_headers' );
	register_setting( 'swgtheme_security_options', 'swgtheme_content_security_policy' );
	register_setting( 'swgtheme_security_options', 'swgtheme_x_frame_options' );
}
add_action( 'admin_init', 'swgtheme_register_security_settings' );

/**
 * Add Security Admin Menu
 */
function swgtheme_security_menu() {
	add_theme_page(
		'Advanced Security',
		'Security',
		'manage_options',
		'swgtheme-security',
		'swgtheme_security_page'
	);
}
add_action( 'admin_menu', 'swgtheme_security_menu' );

/**
 * Login Attempt Limiting
 */
function swgtheme_check_login_attempts( $user, $username, $password ) {
	if ( ! get_option( 'swgtheme_enable_login_limiting' ) ) {
		return $user;
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_key = 'swg_login_attempts_' . md5( $ip );
	$attempts = get_transient( $transient_key );
	$max_attempts = intval( get_option( 'swgtheme_login_attempts', 5 ) );
	$lockout_duration = intval( get_option( 'swgtheme_lockout_duration', 30 ) );
	
	if ( $attempts && $attempts >= $max_attempts ) {
		return new WP_Error( 'too_many_attempts', sprintf(
			'Too many failed login attempts. Please try again in %d minutes.',
			$lockout_duration
		) );
	}
	
	return $user;
}
add_filter( 'authenticate', 'swgtheme_check_login_attempts', 30, 3 );

/**
 * Track Failed Login Attempts
 */
function swgtheme_track_failed_login( $username ) {
	if ( ! get_option( 'swgtheme_enable_login_limiting' ) ) {
		return;
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_key = 'swg_login_attempts_' . md5( $ip );
	$attempts = get_transient( $transient_key );
	$attempts = $attempts ? intval( $attempts ) + 1 : 1;
	$lockout_duration = intval( get_option( 'swgtheme_lockout_duration', 30 ) ) * MINUTE_IN_SECONDS;
	
	set_transient( $transient_key, $attempts, $lockout_duration );
	
	// Auto-block IP after max attempts
	if ( get_option( 'swgtheme_auto_block_failed_login' ) ) {
		$max_attempts = intval( get_option( 'swgtheme_login_attempts', 5 ) );
		if ( $attempts >= $max_attempts ) {
			$blocked_ips = get_option( 'swgtheme_blocked_ips', '' );
			$blocked_ips .= "\n" . $ip;
			update_option( 'swgtheme_blocked_ips', trim( $blocked_ips ) );
		}
	}
}
add_action( 'wp_login_failed', 'swgtheme_track_failed_login' );

/**
 * Clear Login Attempts on Successful Login
 */
function swgtheme_clear_login_attempts( $user_login, $user ) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_key = 'swg_login_attempts_' . md5( $ip );
	delete_transient( $transient_key );
}
add_action( 'wp_login', 'swgtheme_clear_login_attempts', 10, 2 );

/**
 * IP Blocking
 */
function swgtheme_block_ip() {
	if ( ! get_option( 'swgtheme_enable_ip_blocking' ) ) {
		return;
	}
	
	$current_ip = $_SERVER['REMOTE_ADDR'];
	
	// Check whitelist first
	$whitelist = get_option( 'swgtheme_whitelist_ips', '' );
	$whitelist_ips = array_filter( array_map( 'trim', explode( "\n", $whitelist ) ) );
	
	if ( in_array( $current_ip, $whitelist_ips ) ) {
		return;
	}
	
	// Check blocklist
	$blocked = get_option( 'swgtheme_blocked_ips', '' );
	$blocked_ips = array_filter( array_map( 'trim', explode( "\n", $blocked ) ) );
	
	foreach ( $blocked_ips as $blocked_ip ) {
		// Support CIDR notation and wildcards
		if ( strpos( $blocked_ip, '*' ) !== false ) {
			$pattern = str_replace( '.', '\.', $blocked_ip );
			$pattern = str_replace( '*', '.*', $pattern );
			if ( preg_match( '/^' . $pattern . '$/', $current_ip ) ) {
				wp_die( 'Access denied. Your IP address has been blocked.', 'Access Denied', array( 'response' => 403 ) );
			}
		} elseif ( $current_ip === $blocked_ip ) {
			wp_die( 'Access denied. Your IP address has been blocked.', 'Access Denied', array( 'response' => 403 ) );
		}
	}
}
add_action( 'init', 'swgtheme_block_ip', 1 );

/**
 * Malicious File Detection
 */
function swgtheme_scan_uploaded_file( $file ) {
	if ( ! get_option( 'swgtheme_enable_file_detection' ) || ! get_option( 'swgtheme_scan_uploads' ) ) {
		return $file;
	}
	
	$allowed_types = get_option( 'swgtheme_allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx' );
	$allowed_array = array_map( 'trim', explode( ',', $allowed_types ) );
	
	$file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
	
	if ( ! in_array( $file_ext, $allowed_array ) ) {
		$file['error'] = 'File type not allowed for security reasons.';
		return $file;
	}
	
	// Check for PHP code in uploaded files
	if ( isset( $file['tmp_name'] ) && file_exists( $file['tmp_name'] ) ) {
		$content = file_get_contents( $file['tmp_name'], false, null, 0, 1024 );
		
		// Check for suspicious patterns
		$patterns = array(
			'/<\?php/i',
			'/eval\s*\(/i',
			'/base64_decode/i',
			'/system\s*\(/i',
			'/exec\s*\(/i',
			'/shell_exec/i',
		);
		
		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $content ) ) {
				$file['error'] = 'Potentially malicious file detected and blocked.';
				return $file;
			}
		}
	}
	
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'swgtheme_scan_uploaded_file' );

/**
 * Database Security Hardening
 */
function swgtheme_database_hardening() {
	if ( ! get_option( 'swgtheme_enable_db_hardening' ) ) {
		return;
	}
	
	// Disable database error display
	if ( get_option( 'swgtheme_disable_db_error_display' ) ) {
		global $wpdb;
		$wpdb->hide_errors();
		$wpdb->suppress_errors();
	}
}
add_action( 'init', 'swgtheme_database_hardening' );

/**
 * Disable XML-RPC
 */
function swgtheme_disable_xmlrpc() {
	if ( get_option( 'swgtheme_disable_xmlrpc' ) ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
		
		// Also block xmlrpc.php completely
		add_action( 'init', function() {
			if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'xmlrpc.php' ) !== false ) {
				wp_die( 'XML-RPC services are disabled.', 'Service Disabled', array( 'response' => 403 ) );
			}
		}, 1 );
	}
}
add_action( 'init', 'swgtheme_disable_xmlrpc' );

/**
 * Disable File Editing
 */
function swgtheme_disable_file_editing() {
	if ( get_option( 'swgtheme_disable_file_editing' ) ) {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
	}
}
add_action( 'init', 'swgtheme_disable_file_editing', 1 );

/**
 * Hide WordPress Version
 */
function swgtheme_hide_wp_version() {
	if ( get_option( 'swgtheme_hide_wp_version' ) ) {
		// Remove version from head
		remove_action( 'wp_head', 'wp_generator' );
		
		// Remove version from RSS feeds
		add_filter( 'the_generator', '__return_empty_string' );
		
		// Remove version from scripts and styles
		add_filter( 'style_loader_src', 'swgtheme_remove_version_from_assets', 9999 );
		add_filter( 'script_loader_src', 'swgtheme_remove_version_from_assets', 9999 );
	}
}
add_action( 'init', 'swgtheme_hide_wp_version' );

function swgtheme_remove_version_from_assets( $src ) {
	if ( strpos( $src, 'ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

/**
 * Disable User Enumeration
 */
function swgtheme_disable_user_enumeration() {
	if ( ! get_option( 'swgtheme_disable_user_enumeration' ) ) {
		return;
	}
	
	if ( ! is_admin() && isset( $_SERVER['REQUEST_URI'] ) ) {
		if ( preg_match( '/author=([0-9]*)/i', $_SERVER['REQUEST_URI'] ) || 
		     isset( $_GET['author'] ) ) {
			wp_die( 'Forbidden', 'Forbidden', array( 'response' => 403 ) );
		}
	}
}
add_action( 'init', 'swgtheme_disable_user_enumeration' );

/**
 * Custom Admin URL
 */
function swgtheme_custom_admin_url() {
	if ( ! get_option( 'swgtheme_custom_admin_url' ) ) {
		return;
	}
	
	$custom_slug = get_option( 'swgtheme_admin_url_slug', 'admin-panel' );
	
	// Redirect default wp-admin to custom URL
	if ( isset( $_SERVER['REQUEST_URI'] ) && 
	     strpos( $_SERVER['REQUEST_URI'], '/wp-admin' ) !== false && 
	     strpos( $_SERVER['REQUEST_URI'], '/' . $custom_slug ) === false ) {
		
		if ( ! is_user_logged_in() ) {
			wp_redirect( home_url( '/' . $custom_slug ) );
			exit;
		}
	}
	
	// Handle custom admin URL
	if ( isset( $_SERVER['REQUEST_URI'] ) && 
	     strpos( $_SERVER['REQUEST_URI'], '/' . $custom_slug ) !== false ) {
		$_SERVER['REQUEST_URI'] = str_replace( '/' . $custom_slug, '/wp-admin', $_SERVER['REQUEST_URI'] );
	}
}
add_action( 'init', 'swgtheme_custom_admin_url', 1 );

/**
 * Custom Login URL
 */
function swgtheme_custom_login_url() {
	if ( ! get_option( 'swgtheme_custom_login_url' ) ) {
		return;
	}
	
	$custom_slug = get_option( 'swgtheme_login_url_slug', 'login' );
	
	// Redirect default wp-login.php to custom URL
	if ( isset( $_SERVER['REQUEST_URI'] ) && 
	     strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && 
	     ! isset( $_GET['action'] ) ) {
		
		if ( ! is_user_logged_in() ) {
			wp_redirect( home_url( '/' . $custom_slug ) );
			exit;
		}
	}
	
	// Handle custom login URL
	if ( isset( $_SERVER['REQUEST_URI'] ) && 
	     $_SERVER['REQUEST_URI'] === '/' . $custom_slug || 
	     $_SERVER['REQUEST_URI'] === '/' . $custom_slug . '/' ) {
		
		$_SERVER['REQUEST_URI'] = '/wp-login.php';
		require_once ABSPATH . 'wp-login.php';
		exit;
	}
}
add_action( 'init', 'swgtheme_custom_login_url', 1 );

/**
 * Honeypot Field for Forms
 */
function swgtheme_add_honeypot_field() {
	if ( ! get_option( 'swgtheme_enable_honeypot' ) ) {
		return;
	}
	?>
	<input type="text" name="swg_honeypot" value="" style="display:none !important;" tabindex="-1" autocomplete="off">
	<?php
}

/**
 * Check Honeypot on Comment Submit
 */
function swgtheme_check_honeypot_comment( $commentdata ) {
	if ( ! get_option( 'swgtheme_enable_honeypot' ) ) {
		return $commentdata;
	}
	
	$honeypot_forms = get_option( 'swgtheme_honeypot_forms', array( 'comment' ) );
	
	if ( in_array( 'comment', $honeypot_forms ) && isset( $_POST['swg_honeypot'] ) && ! empty( $_POST['swg_honeypot'] ) ) {
		wp_die( 'Spam detected.', 'Spam Detected', array( 'response' => 403 ) );
	}
	
	return $commentdata;
}
add_filter( 'preprocess_comment', 'swgtheme_check_honeypot_comment' );

/**
 * Add Honeypot to Comment Form
 */
function swgtheme_honeypot_comment_form() {
	if ( get_option( 'swgtheme_enable_honeypot' ) ) {
		$honeypot_forms = get_option( 'swgtheme_honeypot_forms', array( 'comment' ) );
		if ( in_array( 'comment', $honeypot_forms ) ) {
			swgtheme_add_honeypot_field();
		}
	}
}
add_action( 'comment_form', 'swgtheme_honeypot_comment_form' );

/**
 * Security Headers
 */
function swgtheme_security_headers() {
	if ( ! get_option( 'swgtheme_enable_security_headers' ) ) {
		return;
	}
	
	// X-Frame-Options
	$x_frame = get_option( 'swgtheme_x_frame_options', 'SAMEORIGIN' );
	if ( $x_frame ) {
		header( 'X-Frame-Options: ' . $x_frame );
	}
	
	// X-Content-Type-Options
	header( 'X-Content-Type-Options: nosniff' );
	
	// X-XSS-Protection
	header( 'X-XSS-Protection: 1; mode=block' );
	
	// Referrer-Policy
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	
	// Content-Security-Policy
	$csp = get_option( 'swgtheme_content_security_policy' );
	if ( $csp ) {
		header( 'Content-Security-Policy: ' . $csp );
	}
	
	// Strict-Transport-Security (HSTS)
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}
	
	// Permissions-Policy
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
}
add_action( 'send_headers', 'swgtheme_security_headers' );

/**
 * Security Admin Page
 */
function swgtheme_security_page() {
	?>
	<div class="wrap">
		<h1>Advanced Security Settings</h1>
		
		<div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
			<strong>⚠️ Warning:</strong> Some security features may affect site functionality. Test thoroughly before enabling on production sites.
		</div>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'swgtheme_security_options' ); ?>
			
			<table class="form-table">
				<!-- Login Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🔐 Login Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Login Attempt Limiting</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_login_limiting" value="1" <?php checked( get_option( 'swgtheme_enable_login_limiting' ), 1 ); ?>>
							Enable login attempt limiting
						</label>
					</td>
				</tr>
				<tr class="login-option">
					<th scope="row">Max Login Attempts</th>
					<td>
						<input type="number" name="swgtheme_login_attempts" value="<?php echo esc_attr( get_option( 'swgtheme_login_attempts', 5 ) ); ?>" min="1" max="20" style="width: 80px;">
						<p class="description">Maximum failed login attempts before lockout</p>
					</td>
				</tr>
				<tr class="login-option">
					<th scope="row">Lockout Duration</th>
					<td>
						<input type="number" name="swgtheme_lockout_duration" value="<?php echo esc_attr( get_option( 'swgtheme_lockout_duration', 30 ) ); ?>" min="5" max="1440" style="width: 80px;"> minutes
						<p class="description">How long to lock out after max attempts</p>
					</td>
				</tr>
				<tr class="login-option">
					<th scope="row">Auto-block IPs</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_auto_block_failed_login" value="1" <?php checked( get_option( 'swgtheme_auto_block_failed_login' ), 1 ); ?>>
							Automatically block IPs after max failed attempts
						</label>
					</td>
				</tr>
				
				<!-- IP Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🌐 IP Blocking</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable IP Blocking</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_ip_blocking" value="1" <?php checked( get_option( 'swgtheme_enable_ip_blocking' ), 1 ); ?>>
							Enable IP address blocking
						</label>
					</td>
				</tr>
				<tr class="ip-option">
					<th scope="row">Blocked IPs</th>
					<td>
						<textarea name="swgtheme_blocked_ips" rows="5" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_blocked_ips' ) ); ?></textarea>
						<p class="description">One IP per line. Supports wildcards (e.g., 192.168.1.*)</p>
					</td>
				</tr>
				<tr class="ip-option">
					<th scope="row">Whitelisted IPs</th>
					<td>
						<textarea name="swgtheme_whitelist_ips" rows="5" class="large-text"><?php echo esc_textarea( get_option( 'swgtheme_whitelist_ips' ) ); ?></textarea>
						<p class="description">IPs that should never be blocked (one per line)</p>
					</td>
				</tr>
				
				<!-- File Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">📁 File Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Malicious File Detection</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_file_detection" value="1" <?php checked( get_option( 'swgtheme_enable_file_detection' ), 1 ); ?>>
							Enable malicious file detection
						</label>
					</td>
				</tr>
				<tr class="file-option">
					<th scope="row">Scan Uploads</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_scan_uploads" value="1" <?php checked( get_option( 'swgtheme_scan_uploads' ), 1 ); ?>>
							Scan uploaded files for malicious code
						</label>
					</td>
				</tr>
				<tr class="file-option">
					<th scope="row">Allowed File Types</th>
					<td>
						<input type="text" name="swgtheme_allowed_file_types" value="<?php echo esc_attr( get_option( 'swgtheme_allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx' ) ); ?>" class="large-text">
						<p class="description">Comma-separated file extensions</p>
					</td>
				</tr>
				
				<!-- Database Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">💾 Database Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Database Hardening</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_db_hardening" value="1" <?php checked( get_option( 'swgtheme_enable_db_hardening' ), 1 ); ?>>
							Enable database security hardening
						</label>
					</td>
				</tr>
				<tr class="db-option">
					<th scope="row">Hide Database Errors</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_db_error_display" value="1" <?php checked( get_option( 'swgtheme_disable_db_error_display' ), 1 ); ?>>
							Suppress database error messages
						</label>
						<p class="description">Prevents exposure of database structure</p>
					</td>
				</tr>
				
				<!-- WordPress Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🔧 WordPress Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Disable XML-RPC</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_xmlrpc" value="1" <?php checked( get_option( 'swgtheme_disable_xmlrpc' ), 1 ); ?>>
							Disable XML-RPC (prevents brute force attacks)
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Disable File Editing</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_file_editing" value="1" <?php checked( get_option( 'swgtheme_disable_file_editing' ), 1 ); ?>>
							Disable theme and plugin editor in admin
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Hide WordPress Version</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_hide_wp_version" value="1" <?php checked( get_option( 'swgtheme_hide_wp_version' ), 1 ); ?>>
							Hide WordPress version from public view
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Disable User Enumeration</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_disable_user_enumeration" value="1" <?php checked( get_option( 'swgtheme_disable_user_enumeration' ), 1 ); ?>>
							Prevent username discovery via author archives
						</label>
					</td>
				</tr>
				
				<!-- Admin Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">⚙️ Admin Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Custom Admin URL</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_custom_admin_url" value="1" <?php checked( get_option( 'swgtheme_custom_admin_url' ), 1 ); ?>>
							Enable custom admin URL
						</label>
						<p class="description">⚠️ Warning: Test carefully! May break some plugins.</p>
					</td>
				</tr>
				<tr class="admin-url-option">
					<th scope="row">Admin URL Slug</th>
					<td>
						<input type="text" name="swgtheme_admin_url_slug" value="<?php echo esc_attr( get_option( 'swgtheme_admin_url_slug', 'admin-panel' ) ); ?>" class="regular-text">
						<p class="description">Access admin at: <?php echo home_url( '/' ); ?><strong>[slug]</strong></p>
					</td>
				</tr>
				<tr>
					<th scope="row">Custom Login URL</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_custom_login_url" value="1" <?php checked( get_option( 'swgtheme_custom_login_url' ), 1 ); ?>>
							Enable custom login URL
						</label>
					</td>
				</tr>
				<tr class="login-url-option">
					<th scope="row">Login URL Slug</th>
					<td>
						<input type="text" name="swgtheme_login_url_slug" value="<?php echo esc_attr( get_option( 'swgtheme_login_url_slug', 'login' ) ); ?>" class="regular-text">
						<p class="description">Access login at: <?php echo home_url( '/' ); ?><strong>[slug]</strong></p>
					</td>
				</tr>
				
				<!-- Forms Security -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">📝 Forms Security</h2></th>
				</tr>
				<tr>
					<th scope="row">Honeypot Protection</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_honeypot" value="1" <?php checked( get_option( 'swgtheme_enable_honeypot' ), 1 ); ?>>
							Enable honeypot anti-spam protection
						</label>
					</td>
				</tr>
				<tr class="honeypot-option">
					<th scope="row">Protect Forms</th>
					<td>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_honeypot_forms[]" value="comment" <?php checked( in_array( 'comment', get_option( 'swgtheme_honeypot_forms', array( 'comment' ) ) ) ); ?>>
							Comment forms
						</label>
						<label style="display: block;">
							<input type="checkbox" name="swgtheme_honeypot_forms[]" value="registration" <?php checked( in_array( 'registration', get_option( 'swgtheme_honeypot_forms', array() ) ) ); ?>>
							Registration forms
						</label>
					</td>
				</tr>
				
				<!-- Security Headers -->
				<tr>
					<th colspan="2"><h2 style="margin: 30px 0 10px 0;">🛡️ HTTP Security Headers</h2></th>
				</tr>
				<tr>
					<th scope="row">Enable Security Headers</th>
					<td>
						<label>
							<input type="checkbox" name="swgtheme_enable_security_headers" value="1" <?php checked( get_option( 'swgtheme_enable_security_headers' ), 1 ); ?>>
							Enable HTTP security headers
						</label>
					</td>
				</tr>
				<tr class="headers-option">
					<th scope="row">X-Frame-Options</th>
					<td>
						<select name="swgtheme_x_frame_options">
							<option value="DENY" <?php selected( get_option( 'swgtheme_x_frame_options' ), 'DENY' ); ?>>DENY</option>
							<option value="SAMEORIGIN" <?php selected( get_option( 'swgtheme_x_frame_options', 'SAMEORIGIN' ), 'SAMEORIGIN' ); ?>>SAMEORIGIN</option>
						</select>
						<p class="description">Prevents clickjacking attacks</p>
					</td>
				</tr>
				<tr class="headers-option">
					<th scope="row">Content Security Policy</th>
					<td>
						<input type="text" name="swgtheme_content_security_policy" value="<?php echo esc_attr( get_option( 'swgtheme_content_security_policy' ) ); ?>" class="large-text">
						<p class="description">Example: default-src 'self'; script-src 'self' 'unsafe-inline'</p>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		function toggleOptions() {
			$('.login-option').toggle($('input[name="swgtheme_enable_login_limiting"]').is(':checked'));
			$('.ip-option').toggle($('input[name="swgtheme_enable_ip_blocking"]').is(':checked'));
			$('.file-option').toggle($('input[name="swgtheme_enable_file_detection"]').is(':checked'));
			$('.db-option').toggle($('input[name="swgtheme_enable_db_hardening"]').is(':checked'));
			$('.admin-url-option').toggle($('input[name="swgtheme_custom_admin_url"]').is(':checked'));
			$('.login-url-option').toggle($('input[name="swgtheme_custom_login_url"]').is(':checked'));
			$('.honeypot-option').toggle($('input[name="swgtheme_enable_honeypot"]').is(':checked'));
			$('.headers-option').toggle($('input[name="swgtheme_enable_security_headers"]').is(':checked'));
		}
		
		toggleOptions();
		$('input[type="checkbox"]').on('change', toggleOptions);
	});
	</script>
	<?php
}

/**
 * ============================================================================
 * SECURITY & PERFORMANCE ENHANCEMENTS
 * ============================================================================
 */

/**
 * Add Security Headers
 */
function swgtheme_add_security_headers() {
	if ( get_option( 'swgtheme_enable_security_headers', '1' ) !== '1' ) {
		return;
	}
	
	// X-Frame-Options
	$x_frame = get_option( 'swgtheme_x_frame_options', 'SAMEORIGIN' );
	if ( ! empty( $x_frame ) ) {
		header( 'X-Frame-Options: ' . $x_frame );
	}
	
	// X-Content-Type-Options
	header( 'X-Content-Type-Options: nosniff' );
	
	// X-XSS-Protection
	header( 'X-XSS-Protection: 1; mode=block' );
	
	// Referrer-Policy
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	
	// Permissions-Policy
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
	
	// Content-Security-Policy
	$csp = get_option( 'swgtheme_content_security_policy', '' );
	if ( ! empty( $csp ) ) {
		header( 'Content-Security-Policy: ' . $csp );
	} else {
		// Default CSP
		header( "Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';" );
	}
	
	// Strict-Transport-Security (HSTS)
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
	}
}
add_action( 'send_headers', 'swgtheme_add_security_headers' );

/**
 * Add Browser Caching Headers
 */
function swgtheme_add_cache_headers() {
	if ( get_option( 'swgtheme_enable_browser_cache', '1' ) !== '1' ) {
		return;
	}
	
	// Don't cache admin, login, or dynamic pages
	if ( is_admin() || is_user_logged_in() || is_search() || is_404() ) {
		return;
	}
	
	$cache_duration = intval( get_option( 'swgtheme_cache_duration', '604800' ) ); // Default 7 days
	
	// Set cache headers for static pages
	if ( is_singular() && ! is_front_page() ) {
		header( 'Cache-Control: public, max-age=' . $cache_duration );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $cache_duration ) . ' GMT' );
	}
}
add_action( 'send_headers', 'swgtheme_add_cache_headers' );

/**
 * Enable Gzip Compression
 */
function swgtheme_enable_gzip_compression() {
	if ( get_option( 'swgtheme_enable_gzip', '1' ) !== '1' ) {
		return;
	}
	
	if ( ! ob_start( 'ob_gzhandler' ) ) {
		ob_start();
	}
}
add_action( 'init', 'swgtheme_enable_gzip_compression', 1 );

/**
 * Minify CSS Output
 */
function swgtheme_minify_css( $css ) {
	if ( get_option( 'swgtheme_enable_css_minify', '0' ) !== '1' ) {
		return $css;
	}
	
	// Remove comments
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
	
	// Remove whitespace
	$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
	$css = preg_replace( '/\s+/', ' ', $css );
	$css = preg_replace( '/\s*([\{\};:,])\s*/', '$1', $css );
	
	// Remove trailing semicolons
	$css = str_replace( ';}', '}', $css );
	
	return trim( $css );
}

/**
 * Minify JS Output
 */
function swgtheme_minify_js( $js ) {
	if ( get_option( 'swgtheme_enable_js_minify', '0' ) !== '1' ) {
		return $js;
	}
	
	// Remove comments (simple regex, not perfect but sufficient)
	$js = preg_replace( '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $js );
	
	// Remove whitespace
	$js = preg_replace( '/\s+/', ' ', $js );
	
	return trim( $js );
}

/**
 * Add Preconnect for External Domains
 */
function swgtheme_add_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
			'crossorigin',
		);
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	
	if ( 'dns-prefetch' === $relation_type ) {
		$urls[] = 'https://www.googletagmanager.com';
		$urls[] = 'https://www.google-analytics.com';
	}
	
	return $urls;
}
add_filter( 'wp_resource_hints', 'swgtheme_add_resource_hints', 10, 2 );

/**
 * Defer JavaScript Loading
 */
function swgtheme_defer_scripts( $tag, $handle, $src ) {
	if ( get_option( 'swgtheme_enable_defer_js', '0' ) !== '1' ) {
		return $tag;
	}
	
	// Don't defer jQuery or admin scripts
	$defer_exclusions = array( 'jquery', 'jquery-core', 'jquery-migrate' );
	
	if ( in_array( $handle, $defer_exclusions ) || is_admin() ) {
		return $tag;
	}
	
	// Add defer attribute
	return str_replace( ' src', ' defer src', $tag );
}
add_filter( 'script_loader_tag', 'swgtheme_defer_scripts', 10, 3 );

/**
 * Add WebP Image Support
 */
function swgtheme_enable_webp_upload( $mimes ) {
	if ( get_option( 'swgtheme_enable_webp', '1' ) !== '1' ) {
		return $mimes;
	}
	
	$mimes['webp'] = 'image/webp';
	return $mimes;
}
add_filter( 'mime_types', 'swgtheme_enable_webp_upload' );

/**
 * Convert Images to WebP on Upload
 */
function swgtheme_convert_to_webp( $metadata ) {
	if ( get_option( 'swgtheme_auto_convert_webp', '0' ) !== '1' ) {
		return $metadata;
	}
	
	if ( ! function_exists( 'imagewebp' ) ) {
		return $metadata;
	}
	
	$upload_dir = wp_upload_dir();
	$file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
	
	if ( ! file_exists( $file_path ) ) {
		return $metadata;
	}
	
	$image_type = exif_imagetype( $file_path );
	
	if ( $image_type === IMAGETYPE_JPEG || $image_type === IMAGETYPE_PNG ) {
		$webp_path = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $file_path );
		
		if ( $image_type === IMAGETYPE_JPEG ) {
			$image = imagecreatefromjpeg( $file_path );
		} else {
			$image = imagecreatefrompng( $file_path );
			imagepalettetotruecolor( $image );
			imagealphablending( $image, true );
			imagesavealpha( $image, true );
		}
		
		if ( $image ) {
			$quality = intval( get_option( 'swgtheme_webp_quality', '80' ) );
			imagewebp( $image, $webp_path, $quality );
			imagedestroy( $image );
		}
	}
	
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'swgtheme_convert_to_webp' );

/**
 * Optimize Image Quality on Upload
 */
function swgtheme_optimize_image_quality() {
	$quality = intval( get_option( 'swgtheme_image_quality', '82' ) );
	return min( max( $quality, 1 ), 100 );
}
add_filter( 'jpeg_quality', 'swgtheme_optimize_image_quality' );
add_filter( 'wp_editor_set_quality', 'swgtheme_optimize_image_quality' );

/**
 * Disable Embeds for Performance
 */
function swgtheme_disable_embeds() {
	if ( get_option( 'swgtheme_disable_embeds', '0' ) !== '1' ) {
		return;
	}
	
	// Remove the REST API endpoint
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	
	// Turn off oEmbed auto discovery
	add_filter( 'embed_oembed_discover', '__return_false' );
	
	// Don't filter oEmbed results
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	
	// Remove oEmbed discovery links
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	
	// Remove oEmbed-specific JavaScript
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'swgtheme_disable_embeds' );

/**
 * Remove Emoji Scripts for Performance
 */
function swgtheme_disable_emojis() {
	if ( get_option( 'swgtheme_disable_emojis', '0' ) !== '1' ) {
		return;
	}
	
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	add_filter( 'tiny_mce_plugins', function( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
	} );
	
	add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}, 10, 2 );
}
add_action( 'init', 'swgtheme_disable_emojis' );

/**
 * Limit Post Revisions
 */
if ( get_option( 'swgtheme_limit_revisions', '0' ) === '1' ) {
	$revision_limit = intval( get_option( 'swgtheme_revision_limit', '5' ) );
	if ( ! defined( 'WP_POST_REVISIONS' ) ) {
		define( 'WP_POST_REVISIONS', $revision_limit );
	}
}

/**
 * Increase Autosave Interval
 */
if ( get_option( 'swgtheme_increase_autosave', '0' ) === '1' ) {
	$autosave_interval = intval( get_option( 'swgtheme_autosave_interval', '300' ) );
	if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
		define( 'AUTOSAVE_INTERVAL', $autosave_interval );
	}
}

/**
 * Sanitize File Names on Upload
 */
function swgtheme_sanitize_filename( $filename ) {
	$filename = remove_accents( $filename );
	$filename = preg_replace( '/[^a-zA-Z0-9._-]/', '-', $filename );
	$filename = preg_replace( '/-+/', '-', $filename );
	$filename = strtolower( $filename );
	return $filename;
}
add_filter( 'sanitize_file_name', 'swgtheme_sanitize_filename', 10 );

/**
 * Prevent Username Enumeration
 */
function swgtheme_prevent_username_enumeration() {
	if ( get_option( 'swgtheme_prevent_enum', '1' ) !== '1' ) {
		return;
	}
	
	if ( ! is_admin() && isset( $_REQUEST['author'] ) && intval( $_REQUEST['author'] ) ) {
		wp_die( 'Access denied.' );
	}
}
add_action( 'template_redirect', 'swgtheme_prevent_username_enumeration' );

/**
 * Add Rate Limiting for AJAX Requests
 */
function swgtheme_ajax_rate_limit( $action ) {
	if ( get_option( 'swgtheme_enable_rate_limiting', '0' ) !== '1' ) {
		return true;
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_key = 'ajax_rate_' . md5( $action . $ip );
	$requests = get_transient( $transient_key );
	
	$max_requests = intval( get_option( 'swgtheme_rate_limit_max', '60' ) );
	$time_window = intval( get_option( 'swgtheme_rate_limit_window', '60' ) );
	
	if ( $requests === false ) {
		set_transient( $transient_key, 1, $time_window );
		return true;
	}
	
	if ( $requests >= $max_requests ) {
		wp_send_json_error( array( 'message' => 'Rate limit exceeded. Please try again later.' ) );
		return false;
	}
	
	set_transient( $transient_key, $requests + 1, $time_window );
	return true;
}

/**
 * Database Query Optimization
 */
function swgtheme_optimize_db_queries() {
	if ( get_option( 'swgtheme_optimize_queries', '0' ) !== '1' ) {
		return;
	}
	
	// Remove unnecessary queries
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
}
add_action( 'init', 'swgtheme_optimize_db_queries' );
/**
 * ============================================================================
 * MODERN WORDPRESS STANDARDS - GUTENBERG & BLOCKS
 * ============================================================================
 */

/**
 * Register Custom Blocks
 */
function swgtheme_register_blocks() {
	// Register featured content block
	if ( file_exists( get_template_directory() . '/blocks/featured-content/block.json' ) ) {
		register_block_type( get_template_directory() . '/blocks/featured-content' );
	}
}
add_action( 'init', 'swgtheme_register_blocks' );

/**
 * Register Block Patterns
 */
function swgtheme_register_block_patterns() {
	// Register pattern category
	register_block_pattern_category(
		'swgtheme',
		array(
			'label' => __( 'Star Wars Galaxy', 'swgtheme' )
		)
	);
	
	// Load pattern file
	if ( file_exists( get_template_directory() . '/patterns/patterns.php' ) ) {
		require_once get_template_directory() . '/patterns/patterns.php';
	}
}
add_action( 'init', 'swgtheme_register_block_patterns' );

/**
 * Add Block Styles
 */
function swgtheme_register_block_styles() {
	// Rounded button style
	register_block_style(
		'core/button',
		array(
			'name'  => 'sith-button',
			'label' => __( 'Sith Style', 'swgtheme' ),
		)
	);
	
	// Highlighted quote style
	register_block_style(
		'core/quote',
		array(
			'name'  => 'imperial-quote',
			'label' => __( 'Imperial Quote', 'swgtheme' ),
		)
	);
	
	// Feature box for paragraphs
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'feature-box',
			'label' => __( 'Feature Box', 'swgtheme' ),
		)
	);
}
add_action( 'init', 'swgtheme_register_block_styles' );

/**
 * Add Editor Styles
 */
function swgtheme_add_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'css/editor-styles.css' );
}
add_action( 'after_setup_theme', 'swgtheme_add_editor_styles' );

/**
 * Enable Block-based Widgets
 */
add_theme_support( 'widgets-block-editor' );

/**
 * Enable Responsive Embeds
 */
add_theme_support( 'responsive-embeds' );

/**
 * Enable Align Wide Blocks
 */
add_theme_support( 'align-wide' );

/**
 * ============================================================================
 * REST API ENDPOINTS
 * ============================================================================
 */

/**
 * Register Custom REST API Routes
 */
function swgtheme_register_rest_routes() {
	// Endpoint for theme stats
	register_rest_route( 'swgtheme/v1', '/stats', array(
		'methods'  => 'GET',
		'callback' => 'swgtheme_get_theme_stats',
		'permission_callback' => '__return_true',
	) );
	
	// Endpoint for user profile
	register_rest_route( 'swgtheme/v1', '/profile/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => 'swgtheme_get_user_profile',
		'permission_callback' => '__return_true',
		'args' => array(
			'id' => array(
				'validate_callback' => function( $param ) {
					return is_numeric( $param );
				}
			),
		),
	) );
	
	// Endpoint for popular posts
	register_rest_route( 'swgtheme/v1', '/popular-posts', array(
		'methods'  => 'GET',
		'callback' => 'swgtheme_get_popular_posts',
		'permission_callback' => '__return_true',
		'args' => array(
			'limit' => array(
				'default' => 5,
				'sanitize_callback' => 'absint',
			),
		),
	) );
	
	// Endpoint for bookmarks (authenticated)
	register_rest_route( 'swgtheme/v1', '/bookmarks', array(
		'methods'  => 'POST',
		'callback' => 'swgtheme_toggle_bookmark',
		'permission_callback' => function() {
			return is_user_logged_in();
		},
		'args' => array(
			'post_id' => array(
				'required' => true,
				'validate_callback' => function( $param ) {
					return is_numeric( $param );
				},
				'sanitize_callback' => 'absint',
			),
		),
	) );
}
add_action( 'rest_api_init', 'swgtheme_register_rest_routes' );

/**
 * REST API: Get Theme Stats
 */
function swgtheme_get_theme_stats( $request ) {
	$stats = array(
		'total_posts' => wp_count_posts()->publish,
		'total_comments' => wp_count_comments()->approved,
		'total_users' => count_users()['total_users'],
		'theme_version' => SWGTHEME_VERSION,
	);
	
	return new WP_REST_Response( $stats, 200 );
}

/**
 * REST API: Get User Profile
 */
function swgtheme_get_user_profile( $request ) {
	$user_id = $request['id'];
	$user = get_userdata( $user_id );
	
	if ( ! $user ) {
		return new WP_Error( 'user_not_found', __( 'User not found', 'swgtheme' ), array( 'status' => 404 ) );
	}
	
	$profile = array(
		'id' => $user->ID,
		'name' => $user->display_name,
		'avatar' => get_avatar_url( $user->ID ),
		'bio' => get_user_meta( $user->ID, 'description', true ),
		'post_count' => count_user_posts( $user->ID ),
		'joined' => $user->user_registered,
	);
	
	return new WP_REST_Response( $profile, 200 );
}

/**
 * REST API: Get Popular Posts
 */
function swgtheme_get_popular_posts( $request ) {
	$limit = $request->get_param( 'limit' ) ?? 5;
	
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => $limit,
		'orderby' => 'comment_count',
		'order' => 'DESC',
	);
	
	$query = new WP_Query( $args );
	$posts = array();
	
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$posts[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'url' => get_permalink(),
				'excerpt' => get_the_excerpt(),
				'comments' => get_comments_number(),
				'date' => get_the_date(),
			);
		}
		wp_reset_postdata();
	}
	
	return new WP_REST_Response( $posts, 200 );
}

/**
 * REST API: Toggle Bookmark
 */
function swgtheme_toggle_bookmark( $request ) {
	$post_id = $request->get_param( 'post_id' );
	$user_id = get_current_user_id();
	
	if ( ! $post_id || ! get_post( $post_id ) ) {
		return new WP_Error( 'invalid_post', __( 'Invalid post ID', 'swgtheme' ), array( 'status' => 400 ) );
	}
	
	$bookmarks = get_user_meta( $user_id, 'bookmarked_posts', true );
	if ( ! is_array( $bookmarks ) ) {
		$bookmarks = array();
	}
	
	$is_bookmarked = in_array( $post_id, $bookmarks );
	
	if ( $is_bookmarked ) {
		$bookmarks = array_diff( $bookmarks, array( $post_id ) );
		$action = 'removed';
	} else {
		$bookmarks[] = $post_id;
		$action = 'added';
	}
	
	update_user_meta( $user_id, 'bookmarked_posts', array_values( $bookmarks ) );
	
	return new WP_REST_Response(
		array(
			'success' => true,
			'action' => $action,
			'bookmarked' => ! $is_bookmarked,
		),
		200
	);
}

/**
 * ============================================================================
 * ACCESSIBILITY ENHANCEMENTS (WCAG 2.1 AA)
 * ============================================================================
 */

/**
 * Add Skip to Content Link
 */
function swgtheme_skip_link() {
	echo '<a class="skip-link screen-reader-text" href="#primary" aria-label="' . esc_attr__( 'Skip to main content', 'swgtheme' ) . '">' . esc_html__( 'Skip to content', 'swgtheme' ) . '</a>';
}
add_action( 'wp_body_open', 'swgtheme_skip_link', 1 );

/**
 * Add ARIA Landmarks
 */
function swgtheme_add_aria_landmarks() {
	?>
	<style>
	.skip-link {
		position: absolute;
		top: -40px;
		left: 0;
		background: #dc3545;
		color: #fff;
		padding: 8px 16px;
		text-decoration: none;
		z-index: 100000;
		transition: top 0.3s;
	}
	.skip-link:focus {
		top: 0;
	}
	
	/* Focus styles for accessibility */
	a:focus,
	button:focus,
	input:focus,
	textarea:focus,
	select:focus {
		outline: 2px solid #dc3545;
		outline-offset: 2px;
	}
	
	/* High contrast mode support */
	@media (prefers-contrast: high) {
		.btn-danger,
		.btn.btn-danger {
			border: 2px solid currentColor;
		}
	}
	
	/* Reduced motion support */
	@media (prefers-reduced-motion: reduce) {
		*,
		*::before,
		*::after {
			animation-duration: 0.01ms !important;
			animation-iteration-count: 1 !important;
			transition-duration: 0.01ms !important;
		}
	}
	</style>
	<?php
}
add_action( 'wp_head', 'swgtheme_add_aria_landmarks' );

/**
 * Improve Image Accessibility
 */
function swgtheme_img_accessibility( $attr, $attachment ) {
	// Add alt text if missing
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
		if ( empty( $attr['alt'] ) ) {
			$attr['alt'] = $attachment->post_title;
		}
	}
	
	// Add loading attribute
	if ( ! isset( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'swgtheme_img_accessibility', 10, 2 );

/**
 * Add Language Attributes
 */
function swgtheme_language_attributes( $output ) {
	$output .= ' dir="' . ( is_rtl() ? 'rtl' : 'ltr' ) . '"';
	return $output;
}
add_filter( 'language_attributes', 'swgtheme_language_attributes' );

/**
 * Enhance Menu Accessibility
 */
function swgtheme_nav_menu_link_attributes( $atts, $item, $args ) {
	// Add aria-current for current page
	if ( in_array( 'current-menu-item', $item->classes ) ) {
		$atts['aria-current'] = 'page';
	}
	
	// Add aria-label for icon links
	if ( empty( $item->title ) && ! empty( $item->attr_title ) ) {
		$atts['aria-label'] = $item->attr_title;
	}
	
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'swgtheme_nav_menu_link_attributes', 10, 3 );

/**
 * Screen Reader Text Helper Function
 */
function swgtheme_screen_reader_text( $text ) {
	return '<span class="screen-reader-text">' . esc_html( $text ) . '</span>';
}

/* ==========================================================================
   USER EXPERIENCE ENHANCEMENTS
   ========================================================================== */

/**
 * PWA Support - Enqueue manifest and service worker
 */
function swgtheme_pwa_support() {
	// Add manifest link
	echo '<link rel="manifest" href="' . esc_url( get_template_directory_uri() . '/manifest.json' ) . '">' . "\n";
	
	// Add theme color meta tags
	echo '<meta name="theme-color" content="#dc3545">' . "\n";
	echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
	echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
	echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
	echo '<meta name="apple-mobile-web-app-title" content="Lords of the Outer Rim">' . "\n";
	
	// Use theme screenshot as fallback icon
	$icon_url = get_template_directory_uri() . '/screenshot.png';
	echo '<link rel="apple-touch-icon" href="' . esc_url( $icon_url ) . '">' . "\n";
}
add_action( 'wp_head', 'swgtheme_pwa_support' );

/**
 * Enqueue UX Enhancement Scripts
 */
function swgtheme_enqueue_ux_scripts() {
	// PWA initialization
	wp_enqueue_script( 'swg-pwa-init', get_template_directory_uri() . '/js/pwa-init.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Web Vitals monitoring
	wp_enqueue_script( 'swg-web-vitals', get_template_directory_uri() . '/js/web-vitals.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Toast notifications
	wp_enqueue_script( 'swg-toast', get_template_directory_uri() . '/js/toast.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Mobile enhancements
	wp_enqueue_script( 'swg-mobile-enhancements', get_template_directory_uri() . '/js/mobile-enhancements.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Loading states
	wp_enqueue_script( 'swg-loading-states', get_template_directory_uri() . '/js/loading-states.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Smooth animations
	wp_enqueue_script( 'swg-smooth-animations', get_template_directory_uri() . '/js/smooth-animations.js', 
		array(), SWGTHEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'swgtheme_enqueue_ux_scripts' );

/**
 * AJAX Handler for Web Vitals Tracking
 */
function swgtheme_track_web_vitals() {
	check_ajax_referer( 'swgtheme_ajax_nonce', 'nonce' );
	
	$metric_name = isset( $_POST['metric_name'] ) ? sanitize_text_field( $_POST['metric_name'] ) : '';
	$metric_value = isset( $_POST['metric_value'] ) ? floatval( $_POST['metric_value'] ) : 0;
	$metric_rating = isset( $_POST['metric_rating'] ) ? sanitize_text_field( $_POST['metric_rating'] ) : '';
	$page_url = isset( $_POST['page_url'] ) ? esc_url_raw( $_POST['page_url'] ) : '';
	
	if ( empty( $metric_name ) ) {
		wp_send_json_error( 'Invalid metric name' );
		return;
	}
	
	// Store in transient for analytics (expires in 1 day)
	$vitals_key = 'web_vitals_' . md5( $page_url );
	$vitals = get_transient( $vitals_key ) ?: array();
	
	$vitals[ $metric_name ] = array(
		'value' => $metric_value,
		'rating' => $metric_rating,
		'timestamp' => current_time( 'timestamp' ),
	);
	
	set_transient( $vitals_key, $vitals, DAY_IN_SECONDS );
	
	// Optionally log to database or external analytics
	do_action( 'swgtheme_web_vitals_tracked', $metric_name, $metric_value, $metric_rating, $page_url );
	
	wp_send_json_success( array(
		'message' => 'Web vital tracked successfully',
		'metric' => $metric_name,
	) );
}
add_action( 'wp_ajax_track_web_vitals', 'swgtheme_track_web_vitals' );
add_action( 'wp_ajax_nopriv_track_web_vitals', 'swgtheme_track_web_vitals' );

/**
 * Get Web Vitals Data for Admin Dashboard
 */
function swgtheme_get_web_vitals_data( $url = '' ) {
	if ( empty( $url ) ) {
		$url = home_url( '/' );
	}
	
	$vitals_key = 'web_vitals_' . md5( $url );
	return get_transient( $vitals_key ) ?: array();
}

/**
 * Add Preconnect and DNS-Prefetch for Performance
 */
function swgtheme_resource_hints( $hints, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$hints[] = array(
			'href' => 'https://fonts.googleapis.com',
			'crossorigin',
		);
		$hints[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
		$hints[] = array(
			'href' => 'https://unpkg.com',
			'crossorigin',
		);
	}
	
	if ( 'dns-prefetch' === $relation_type ) {
		$hints[] = 'https://fonts.googleapis.com';
		$hints[] = 'https://fonts.gstatic.com';
		$hints[] = 'https://unpkg.com';
	}
	
	return $hints;
}
add_filter( 'wp_resource_hints', 'swgtheme_resource_hints', 10, 2 );

/**
 * Add Viewport Meta Tag
 */
function swgtheme_viewport_meta() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">' . "\n";
}
add_action( 'wp_head', 'swgtheme_viewport_meta', 1 );

/**
 * Add Critical CSS Inline
 */
function swgtheme_critical_css() {
	if ( is_front_page() ) {
		?>
		<style id="critical-css">
			body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,sans-serif}
			.container{max-width:1140px;margin:0 auto;padding:0 15px}
			header{background:#000;color:#fff;padding:20px 0}
			.navbar{display:flex;justify-content:space-between;align-items:center}
			@media(max-width:768px){.navbar{flex-direction:column}}
		</style>
		<?php
	}
}
add_action( 'wp_head', 'swgtheme_critical_css', 1 );

/**
 * Lazy Load Videos
 */
function swgtheme_lazy_load_videos( $content ) {
	if ( is_singular() && ! is_admin() ) {
		$content = preg_replace( '/<iframe/', '<iframe loading="lazy"', $content );
	}
	return $content;
}
add_filter( 'the_content', 'swgtheme_lazy_load_videos' );

/**
 * Add Animation Classes to Posts
 */
function swgtheme_add_animation_classes( $classes ) {
	if ( is_home() || is_archive() ) {
		$classes[] = 'animate-slide-up';
	}
	return $classes;
}
add_filter( 'post_class', 'swgtheme_add_animation_classes' );

/**
 * Enhanced Search with Autocomplete Data
 */
function swgtheme_search_autocomplete_data() {
	if ( ! is_search() && ! wp_doing_ajax() ) {
		return;
	}
	
	$recent_posts = get_posts( array(
		'numberposts' => 10,
		'post_status' => 'publish',
		'fields' => 'ids',
	) );
	
	$autocomplete_data = array();
	foreach ( $recent_posts as $post_id ) {
		$autocomplete_data[] = array(
			'title' => get_the_title( $post_id ),
			'url' => get_permalink( $post_id ),
			'excerpt' => wp_trim_words( get_the_excerpt( $post_id ), 10 ),
		);
	}
	
	wp_localize_script( 'swg-advanced-features', 'swgSearchData', array(
		'suggestions' => $autocomplete_data,
	) );
}
add_action( 'wp_enqueue_scripts', 'swgtheme_search_autocomplete_data' );

/**
 * Add Custom Body Classes for UX
 */
function swgtheme_ux_body_classes( $classes ) {
	// Add device type
	$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	
	if ( preg_match( '/mobile|android|iphone|ipad|tablet/i', $user_agent ) ) {
		$classes[] = 'mobile-device';
	} else {
		$classes[] = 'desktop-device';
	}
	
	// Add browser
	if ( strpos( $user_agent, 'Chrome' ) !== false ) {
		$classes[] = 'browser-chrome';
	} elseif ( strpos( $user_agent, 'Firefox' ) !== false ) {
		$classes[] = 'browser-firefox';
	} elseif ( strpos( $user_agent, 'Safari' ) !== false ) {
		$classes[] = 'browser-safari';
	} elseif ( strpos( $user_agent, 'Edge' ) !== false ) {
		$classes[] = 'browser-edge';
	}
	
	// Add animation support
	$classes[] = 'animations-enabled';
	
	return $classes;
}
add_filter( 'body_class', 'swgtheme_ux_body_classes' );

/**
 * Offline Page Template Check
 */
function swgtheme_ensure_offline_page() {
	if ( ! file_exists( ABSPATH . 'offline.html' ) ) {
		// Log warning in debug mode
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'PWA offline.html page is missing. Please create one in the root directory.' );
		}
	}
}
add_action( 'admin_init', 'swgtheme_ensure_offline_page' );

/* ==========================================================================
   DEVELOPER EXPERIENCE ENHANCEMENTS
   ========================================================================== */

/**
 * Enqueue Developer Tools in Dev Mode
 */
function swgtheme_dev_tools_enqueue() {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return;
	}
	
	// Debug toolbar
	wp_enqueue_style( 'swg-debug-toolbar', get_template_directory_uri() . '/css/debug-toolbar.css', 
		array(), SWGTHEME_VERSION, 'all' );
	
	wp_enqueue_script( 'swg-debug-toolbar', get_template_directory_uri() . '/js/debug-toolbar.js', 
		array(), SWGTHEME_VERSION, true );
	
	// Pass dev data to JavaScript
	wp_localize_script( 'swg-debug-toolbar', 'swgDevData', array(
		'devMode' => true,
		'debugLog' => WP_DEBUG_LOG,
		'version' => SWGTHEME_VERSION,
		'phpVersion' => PHP_VERSION,
		'wpVersion' => get_bloginfo( 'version' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'swgtheme_dev_tools_enqueue' );

/**
 * Add Developer Admin Bar Menu
 */
function swgtheme_dev_admin_bar( $wp_admin_bar ) {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return;
	}
	
	// Parent menu
	$wp_admin_bar->add_node( array(
		'id' => 'swg-dev-tools',
		'title' => '🛠️ Dev Tools',
		'href' => '#',
	) );
	
	// System info
	$wp_admin_bar->add_node( array(
		'id' => 'swg-dev-system',
		'parent' => 'swg-dev-tools',
		'title' => 'System Info',
		'href' => admin_url( 'admin.php?page=swg-system-info' ),
	) );
	
	// Query monitor
	$wp_admin_bar->add_node( array(
		'id' => 'swg-dev-queries',
		'parent' => 'swg-dev-tools',
		'title' => 'Query Monitor (' . count( SWGTheme_Dev_Tools::get_queries() ) . ')',
		'href' => '#',
	) );
	
	// Clear cache
	$wp_admin_bar->add_node( array(
		'id' => 'swg-dev-cache',
		'parent' => 'swg-dev-tools',
		'title' => 'Clear Cache',
		'href' => wp_nonce_url( admin_url( 'admin-post.php?action=swg_clear_cache' ), 'swg_clear_cache' ),
	) );
	
	// Debug mode toggle
	$wp_admin_bar->add_node( array(
		'id' => 'swg-dev-debug',
		'parent' => 'swg-dev-tools',
		'title' => 'Debug: ON',
		'href' => '#',
		'meta' => array(
			'class' => 'swg-debug-active',
		),
	) );
}
add_action( 'admin_bar_menu', 'swgtheme_dev_admin_bar', 999 );

/**
 * Handle Clear Cache Action
 */
function swgtheme_clear_cache() {
	check_admin_referer( 'swg_clear_cache' );
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}
	
	// Clear WordPress object cache
	wp_cache_flush();
	
	// Clear transients
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" );
	
	// Clear theme cache
	delete_option( 'swgtheme_cache_version' );
	
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url() );
	exit;
}
add_action( 'admin_post_swg_clear_cache', 'swgtheme_clear_cache' );

/**
 * Development Footer Info
 */
function swgtheme_dev_footer_info() {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return;
	}
	
	$queries = count( SWGTheme_Dev_Tools::get_queries() );
	$memory = SWGTheme_Dev_Tools::get_memory_usage();
	$load_time = timer_stop( 0, 3 );
	
	?>
	<div id="swg-dev-footer" style="position:fixed;bottom:0;left:0;right:0;background:#000;color:#0f0;padding:5px 15px;font-family:monospace;font-size:11px;z-index:999997;display:flex;gap:20px;border-top:2px solid #0f0;">
		<span>⚡ Queries: <strong><?php echo esc_html( $queries ); ?></strong></span>
		<span>💾 Memory: <strong><?php echo esc_html( $memory ); ?></strong></span>
		<span>⏱️ Load: <strong><?php echo esc_html( $load_time ); ?>s</strong></span>
		<span>🐘 PHP: <strong><?php echo PHP_VERSION; ?></strong></span>
		<span>📦 WP: <strong><?php echo get_bloginfo( 'version' ); ?></strong></span>
		<span style="margin-left:auto;">Press <kbd style="background:#333;padding:2px 5px;border-radius:3px;">Ctrl+Shift+D</kbd> for Debug Toolbar</span>
	</div>
	<?php
}
add_action( 'wp_footer', 'swgtheme_dev_footer_info', 999 );

/**
 * Show Template Path in Source
 */
function swgtheme_template_hint() {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return;
	}
	
	global $template;
	$template_name = basename( $template );
	
	echo "\n<!-- Template: " . esc_html( $template_name ) . " -->\n";
	echo "<!-- Theme: swgtheme v" . SWGTHEME_VERSION . " -->\n";
	echo "<!-- Queries: " . count( SWGTheme_Dev_Tools::get_queries() ) . " -->\n\n";
}
add_action( 'wp_head', 'swgtheme_template_hint', 999 );
add_action( 'wp_footer', 'swgtheme_template_hint', 999 );

/**
 * Asset versioning for cache busting in dev mode
 */
function swgtheme_asset_version( $src ) {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return $src;
	}
	
	// Add timestamp to local assets only
	if ( strpos( $src, get_template_directory_uri() ) !== false ) {
		$src = add_query_arg( 'ver', time(), $src );
	}
	
	return $src;
}
add_filter( 'style_loader_src', 'swgtheme_asset_version', 999 );
add_filter( 'script_loader_src', 'swgtheme_asset_version', 999 );

/**
 * Log Slow Queries
 */
function swgtheme_log_slow_queries() {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() || ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {
		return;
	}
	
	$queries = SWGTheme_Dev_Tools::get_queries();
	$slow_queries = array();
	
	foreach ( $queries as $query ) {
		if ( $query[1] > 0.01 ) { // Slower than 10ms
			$slow_queries[] = array(
				'query' => $query[0],
				'time' => $query[1],
				'caller' => $query[2],
			);
		}
	}
	
	if ( ! empty( $slow_queries ) ) {
		swg_log( 'Found ' . count( $slow_queries ) . ' slow queries', $slow_queries, 'Performance' );
	}
}
add_action( 'shutdown', 'swgtheme_log_slow_queries' );

/**
 * Development Dashboard Widget
 */
function swgtheme_dev_dashboard_widget() {
	if ( ! SWGTheme_Dev_Tools::is_dev_mode() ) {
		return;
	}
	
	wp_add_dashboard_widget(
		'swg_dev_widget',
		'🛠️ SWG Development Status',
		'swgtheme_dev_dashboard_widget_content'
	);
}
add_action( 'wp_dashboard_setup', 'swgtheme_dev_dashboard_widget' );

function swgtheme_dev_dashboard_widget_content() {
	?>
	<div style="padding:10px;">
		<h4 style="margin-top:0;">Quick Stats</h4>
		<ul style="margin:0;padding-left:20px;">
			<li><strong>Environment:</strong> <?php echo SWGTheme_Dev_Tools::is_local_environment() ? 'Local' : 'Production'; ?></li>
			<li><strong>Debug Mode:</strong> <?php echo WP_DEBUG ? 'Enabled' : 'Disabled'; ?></li>
			<li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
			<li><strong>WordPress Version:</strong> <?php echo get_bloginfo( 'version' ); ?></li>
			<li><strong>Theme Version:</strong> <?php echo SWGTHEME_VERSION; ?></li>
		</ul>
		
		<h4>Quick Actions</h4>
		<p>
			<a href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=swg_clear_cache' ), 'swg_clear_cache' ); ?>" class="button">Clear Cache</a>
			<a href="<?php echo admin_url( 'admin.php?page=swg-system-info' ); ?>" class="button">System Info</a>
		</p>
		
		<p style="font-size:12px;color:#666;">
			Press <kbd>Ctrl+Shift+D</kbd> on frontend for debug toolbar
		</p>
	</div>
	<?php
}