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
			<?php swgtheme_breadcrumbs(); ?>
			<h1 class="head1"><?php the_title();?></h1>
			
			<?php if (get_option('swgtheme_reading_time_enable', '1') || get_option('swgtheme_post_views_enable', '1')): ?>
			<div class="swg-post-meta" style="margin: 1rem 0; font-size: 0.9rem; opacity: 0.8;">
				<?php if (get_option('swgtheme_reading_time_enable', '1')): ?>
					<span class="swg-reading-time">⏱ <?php echo swgtheme_reading_time(); ?></span>
				<?php endif; ?>
				
				<?php if (get_option('swgtheme_post_views_enable', '1')): ?>
					<?php swgtheme_set_post_views(get_the_ID()); ?>
					<span class="swg-post-views" style="margin-left: 1rem;">👁 <?php echo swgtheme_get_post_views(get_the_ID()); ?> views</span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			
			<?php swgtheme_social_share_buttons(); ?>
			<?php if(has_post_thumbnail()):?>
				<img src="<?php the_post_thumbnail_url('blog-small');?>" class= "img-fliud mb-3 img-thumbnail mr-4">
				<?php endif?>
			<?php get_template_part('includes/section','blogcontent');?>
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