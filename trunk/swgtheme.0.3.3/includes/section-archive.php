
<?php if(have_posts() ): while(have_posts() ): the_post();?> 
	<div class="card mb-3">
		<div class="card-body d-flex justify-content-center align-items-center">
			<?php if(has_post_thumbnail()):?>
				<img src="<?php the_post_thumbnail_url('blog-small');?>" class= "img-fliud mb-3 img-thumbnail mr-4">
			<?php endif?>
			<h3><?php the_title();?></h3>
		<div class="blog-content">
			<?php the_excerpt();?>
			<a href="<?php the_permalink();?>" class= "btn btn-success">Read more </a>
			<?php paginate_comments_links();?>
		</div>
	</div>
</div>
	<?php endwhile; else: endif;?>