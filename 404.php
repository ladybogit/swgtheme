<?php get_header();?>
<section class="page-wrap"><div class="container">
	<div id="swgimage"></div>
	<section class="row">
		<div id="slide" class="slide">
<?php if( is_active_sidebar('gallery-sidebar')):?>
				<?php dynamic_sidebar('gallery-sidebar');?>
		<?php endif;?>
	</div>
	</section>
	<section class="row">
		<div class="col-lg-3"><div class="side-menu"> <?php
			wp_nav_menu(
				array(
					'theme_location' => 'side-menu'
				)
			);?>
			<?php if( is_active_sidebar('page-sidebar')):?>
			<?php dynamic_sidebar('page-sidebar');?>
			<?php endif;?>
		
			</div>
		</div>
		<div class="col-lg-9">
			<?php 
			$title_404 = get_option( 'swgtheme_404_title', 'Page Not Found' );
			$message_404 = get_option( 'swgtheme_404_message', 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' );
			$button_text_404 = get_option( 'swgtheme_404_button_text', 'Go to Homepage' );
			?>
			<h1 class="head1"><?php echo esc_html( $title_404 ); ?></h1>
			<div class="swg-404-content">
				<p><?php echo esc_html( $message_404 ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button swg-404-button">
					<?php echo esc_html( $button_text_404 ); ?>
				</a>
			</div>
		</div>
</section>
<section class="row">	
		<div id="footer2" class="footer2">
<?php if( is_active_sidebar('footer-sidebar')):?>
				<?php dynamic_sidebar('footer-sidebar');?>
		<?php endif;?>
	</div>
</section>
</div></section>

<?php get_footer();?>