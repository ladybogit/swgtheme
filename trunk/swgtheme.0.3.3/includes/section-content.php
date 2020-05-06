<?php if(have_posts() ): while(have_posts() ): the_post();?> 
<?php the_content();?>
	<?php endwhile; else: endif;?>
	<?php if ( ! isset( $content_width ) ) $content_width = 900;?>