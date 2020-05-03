<?php get_header();?>
<div id="swgimage"></div>
<section class="page-wrap"><div class="container">
	

	<div class="row">
		<div id="slide" class="slide">
<?php if ( function_exists( 'soliloquy' ) ) { soliloquy( '98' ); }?>
	</div>
	</div>
	<div class="row">
		<div class="col-lg-3">
			<div class="side-menu"> <?php
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

			<h1><?php echo single_cat_title();?></h1>
			<?php get_template_part('includes/section','archive');?>
		<?php
			global $wp_query;
			$big = 99999999;
			echo paginate_links( array(
				'base' => str_replace($big, '%#%', esc_url( get_pagenum_link( $big))),
				'format' => '?paged=%#%',
				'currant' => max( 1, get_query_var('paged')),
				'total' => $wp_query->max_num_pages
			));
		 ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="cpr">
		&copy; Bogit codeing 2020
	</div>
</div></section>
<?php get_footer();?>