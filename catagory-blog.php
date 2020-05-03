<?php get_header();?>
<div id="swgimage"></div>
<section class="page-wrap"><div class="container">
	

	<div class="row">
		<div id="slide" class="slide">
<?php if ( function_exists( 'soliloquy' ) ) { soliloquy( '40' ); }?>
	</div>
	</div>
	<div class="row">
		<div class="col-lg-2"><?php
		wp_nav_menu(
			array(
				'theme_location' => 'side-menu'
			)
		);
	?>
	<?php if( is_active_sidebar('page-sidebar')):?>
				<?php dynamic_sidebar('page-sidebar');?>
		<?php endif;?>
		</div>
		<h1><?php echo single_cat_title();?></h1>
		<div class="col-lg-10"><?php get_template_part('includes/section','archive');?>
		</div>
	</div>
</div>
<div class="row">
	<div class="cpr">
		&copy; Bogit codeing 2020
	</div>
</div></section>
<?php get_footer();?>





