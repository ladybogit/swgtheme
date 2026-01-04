<?php
/**
 * The template for displaying comments
 *
 * @package SWG Theme
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				printf( _x( 'One comment', 'comments title', 'swgtheme' ) );
			} else {
				printf(
					_nx(
						'%1$s comment',
						'%1$s comments',
						$comments_number,
						'comments title',
						'swgtheme'
					),
					number_format_i18n( $comments_number )
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 60,
					'callback'    => 'swgtheme_custom_comment',
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();
		
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'swgtheme' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php comment_form(); ?>

</div>
