<?php if(have_posts() ): while(have_posts() ): the_post();?> 
	<?php echo get_the_date('d/m/Y');?>
		<?php the_content();?>
			<?php the_author();?></p>
			Tags:
			<?php
			$tags = get_the_tags();
			foreach($tags as $tag):?>
				<a href="<?php echo get_tag_link($tag->term_id);?>" class="badge badge-success">
				<?php echo $tag->name;?>
				</a>
			<?php endforeach;
			?></p>
			Categories:	
			<?php
			$categories = get_the_category();
			foreach($categories as $cat):?>
				<a href="<?php echo get_category_link($cat);?>" class="badge badge-success">
				<?php echo $cat->name;?>
				</a>
			<?php endforeach;?>
		<?php endwhile; else: endif;?>
		<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
		<?php wp_list_comments( $args ); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php comments_template( $file, $separate_comments ); ?>
			