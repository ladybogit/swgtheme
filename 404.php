<?php get_header();?>
<section class="page-wrap"><div class="container">
<?php if ( function_exists( 'soliloquy' ) ) { soliloquy( '40' ); }?>
	<div class="row">
	</div>
	<div class="row">
		<div class="col-lg-2">
			<?php
		wp_nav_menu(
			array(
				'theme_location' => 'side-menu'
			)
		);
	?>
		</div>
		<div class="col-lg-10">
			<h1><?php the_title();?></h1>
			<?php if(has_post_thumbnail()):?>
				<img src="<?php the_post_thumbnail_url('blog-small');?>" class= "img-fliud mb-3 img-thumbnail mr-4">
				<?php endif?>
			<?php get_template_part('includes/section','content');?>
		</div>
	</div>
</div>
<div class="row">
	<div class="cpr">
		&copy; Bogit codeing 2020
	</div>
</div></section>
<?php get_footer();?>