<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( is_front_page() && get_option( 'swgtheme_video_bg_enable', '0' ) === '1' ) : 
	$video_url = get_option( 'swgtheme_video_bg_url', '' );
	if ( ! empty( $video_url ) ) :
?>
<div class="swg-video-bg">
	<video autoplay muted loop playsinline>
		<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
	</video>
	<div class="swg-video-overlay"></div>
</div>
<?php endif; endif; ?>
<?php if ( get_option( 'swgtheme_enable_notification', '0' ) === '1' ) : 
	$notification_text = get_option( 'swgtheme_notification_text', '' );
	$notification_type = get_option( 'swgtheme_notification_type', 'info' );
	if ( ! empty( $notification_text ) ) :
?>
<div class="swg-notification-bar swg-notification-<?php echo esc_attr( $notification_type ); ?>" id="swgNotificationBar">
	<div class="swg-notification-content">
		<span class="swg-notification-text"><?php echo esc_html( $notification_text ); ?></span>
		<button class="swg-notification-close" aria-label="<?php esc_attr_e( 'Close notification', 'swgtheme' ); ?>">&times;</button>
	</div>
</div>
<?php endif; endif; ?>
<?php if ( get_option( 'swgtheme_enable_preloader', '0' ) === '1' ) : 
	$preloader_style = get_option( 'swgtheme_preloader_style', 'spinner' );
	$preloader_bg = get_option( 'swgtheme_preloader_bg_color', '#000000' );
	$preloader_color = get_option( 'swgtheme_preloader_spinner_color', '#dc3545' );
	$preloader_text = get_option( 'swgtheme_preloader_text', 'Loading...' );
	$preloader_logo = get_option( 'swgtheme_preloader_logo', '' );
	$preloader_speed = get_option( 'swgtheme_preloader_speed', 'normal' );
	$preloader_fade = get_option( 'swgtheme_preloader_fade_duration', '500' );
?>
<div class="swg-preloader" style="background-color: <?php echo esc_attr( $preloader_bg ); ?>;" data-fade-duration="<?php echo esc_attr( $preloader_fade ); ?>">
	<div class="swg-preloader-content">
		<?php if ( $preloader_logo ) : ?>
			<div class="swg-preloader-logo">
				<img src="<?php echo esc_url( $preloader_logo ); ?>" alt="Loading" />
			</div>
		<?php endif; ?>
		<div class="swg-preloader-<?php echo esc_attr( $preloader_style ); ?> swg-speed-<?php echo esc_attr( $preloader_speed ); ?>" style="--preloader-color: <?php echo esc_attr( $preloader_color ); ?>;"></div>
		<?php if ( $preloader_text ) : ?>
			<div class="swg-preloader-text"><?php echo esc_html( $preloader_text ); ?></div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'swgtheme' ); ?></a>
<div id="page" class="site">
	<section class="header" style="position: fixed !important; left: 0 !important; width: 100% !important; transform: none !important; animation: none !important; transition: none !important; z-index: 99998 !important;">
	<header id="masthead" class="site-header">
<div class="container" id="menu">
	<?php
		wp_nav_menu(
			array(
				'theme_location' => 'top-menu',
				'menu_class' => 'top-menu'
			)
		);
	?>

 
<div class="search-bar"><?php get_search_form();?></div></div>
</header>
</section>