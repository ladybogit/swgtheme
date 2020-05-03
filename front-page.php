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
			<?php if( is_active_sidebar('page-sidebar')):?>
				<?php dynamic_sidebar('page-sidebar');?>
				<div id="search" class="search"><?php get_search_form();?></div>
		<?php endif;?>
		</div>
		<div class="col-lg-9">
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
</div>
</section>

<?php get_footer();?>