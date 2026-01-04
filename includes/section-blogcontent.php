<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="entry-meta">
		<span class="posted-on"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
		<span class="byline"><?php esc_html_e( 'by', 'swgtheme' ); ?> <?php the_author(); ?></span>
	</div>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php
	$tags = get_the_tags();
	if ( $tags && is_array( $tags ) ) : ?>
		<div class="entry-tags">
			<span class="tags-label"><?php esc_html_e( 'Tags:', 'swgtheme' ); ?></span>
			<?php foreach ( $tags as $tag ) : ?>
				<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="badge badge-success">
					<?php echo esc_html( $tag->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif;

	$categories = get_the_category();
	if ( $categories && is_array( $categories ) ) : ?>
		<div class="entry-categories">
			<span class="categories-label"><?php esc_html_e( 'Categories:', 'swgtheme' ); ?></span>
			<?php foreach ( $categories as $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="badge badge-success">
					<?php echo esc_html( $cat->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
		<?php endwhile; else: endif;?>
		<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
		<?php wp_list_comments( $args ); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php comments_template( $file, $separate_comments ); ?>
			