<?php
/**
 * User Profile Template
 * 
 * @package swgtheme
 */

$username = get_query_var( 'swg_user_profile' );
$user = get_user_by( 'login', $username );

if ( ! $user ) {
	get_template_part( '404' );
	return;
}

get_header();
?>

<div id="primary" class="content-area" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
	<main id="main" class="site-main">
		
		<div class="user-profile-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
			<div class="user-avatar" style="margin-bottom: 20px;">
				<?php echo get_avatar( $user->ID, 150, '', $user->display_name, array( 'class' => 'avatar-circle', 'style' => 'border-radius: 50%; border: 5px solid white; box-shadow: 0 4px 20px rgba(0,0,0,0.2);' ) ); ?>
			</div>
			<h1 style="margin: 0 0 10px 0; font-size: 32px;"><?php echo esc_html( $user->display_name ); ?></h1>
			<p style="margin: 0; opacity: 0.9; font-size: 16px;">@<?php echo esc_html( $user->user_login ); ?></p>
			
			<?php
			$bio = get_user_meta( $user->ID, 'swg_bio', true );
			if ( $bio ) {
				echo '<p style="margin-top: 20px; font-size: 18px; max-width: 600px; margin-left: auto; margin-right: auto;">' . esc_html( $bio ) . '</p>';
			}
			?>
			
			<div class="user-meta" style="margin-top: 20px; display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
				<?php
				$location = get_user_meta( $user->ID, 'swg_location', true );
				if ( $location ) {
					echo '<div>📍 ' . esc_html( $location ) . '</div>';
				}
				
				$member_since = human_time_diff( strtotime( $user->user_registered ), current_time( 'timestamp' ) );
				echo '<div>📅 ' . sprintf( __( 'Member for %s', 'swgtheme' ), $member_since ) . '</div>';
				?>
			</div>
			
			<?php
			$social_links = array();
			$twitter = get_user_meta( $user->ID, 'swg_twitter', true );
			$facebook = get_user_meta( $user->ID, 'swg_facebook', true );
			$instagram = get_user_meta( $user->ID, 'swg_instagram', true );
			$linkedin = get_user_meta( $user->ID, 'swg_linkedin', true );
			$website = get_user_meta( $user->ID, 'swg_website', true );
			
			if ( $twitter || $facebook || $instagram || $linkedin || $website ) {
				echo '<div class="user-social" style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">';
				if ( $website ) echo '<a href="' . esc_url( $website ) . '" target="_blank" style="color: white; font-size: 24px;" title="Website">🌐</a>';
				if ( $twitter ) echo '<a href="' . esc_url( $twitter ) . '" target="_blank" style="color: white; font-size: 24px;" title="Twitter">🐦</a>';
				if ( $facebook ) echo '<a href="' . esc_url( $facebook ) . '" target="_blank" style="color: white; font-size: 24px;" title="Facebook">📘</a>';
				if ( $instagram ) echo '<a href="' . esc_url( $instagram ) . '" target="_blank" style="color: white; font-size: 24px;" title="Instagram">📷</a>';
				if ( $linkedin ) echo '<a href="' . esc_url( $linkedin ) . '" target="_blank" style="color: white; font-size: 24px;" title="LinkedIn">💼</a>';
				echo '</div>';
			}
			?>
		</div>
		
		<div class="user-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
			<div class="stat-box" style="background: #f8f9fa; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
				<div style="font-size: 48px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo count_user_posts( $user->ID, 'post' ); ?></div>
				<div style="font-size: 16px; color: #666; margin-top: 10px;"><?php esc_html_e( 'Posts', 'swgtheme' ); ?></div>
			</div>
			
			<div class="stat-box" style="background: #f8f9fa; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
				<div style="font-size: 48px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo get_comments( array( 'user_id' => $user->ID, 'status' => 'approve', 'count' => true ) ); ?></div>
				<div style="font-size: 16px; color: #666; margin-top: 10px;"><?php esc_html_e( 'Comments', 'swgtheme' ); ?></div>
			</div>
			
			<?php if ( get_option( 'swgtheme_enable_badges', '0' ) === '1' ): ?>
			<div class="stat-box" style="background: #f8f9fa; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
				<?php
				$badges = get_user_meta( $user->ID, 'swg_badges', true );
				$badge_count = is_array( $badges ) ? count( $badges ) : 0;
				?>
				<div style="font-size: 48px; font-weight: bold; color: var(--primary-color, #dc3545);"><?php echo $badge_count; ?></div>
				<div style="font-size: 16px; color: #666; margin-top: 10px;"><?php esc_html_e( 'Badges', 'swgtheme' ); ?></div>
			</div>
			<?php endif; ?>
		</div>
		
		<?php if ( get_option( 'swgtheme_enable_badges', '0' ) === '1' ): ?>
		<div class="user-badges-section" style="margin-bottom: 40px;">
			<h2 style="margin-bottom: 20px;"><?php esc_html_e( 'Achievements', 'swgtheme' ); ?></h2>
			<div class="badges-grid" style="display: flex; gap: 10px; flex-wrap: wrap;">
				<?php
				$badges = get_user_meta( $user->ID, 'swg_badges', true );
				if ( $badges && is_array( $badges ) ) {
					foreach ( $badges as $badge ) {
						echo '<div class="badge-item" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 25px; font-size: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">';
						echo '<span style="font-size: 24px; margin-right: 8px;">' . esc_html( $badge['icon'] ) . '</span>';
						echo esc_html( $badge['name'] );
						echo '</div>';
					}
				} else {
					echo '<p style="color: #666;">' . __( 'No badges earned yet.', 'swgtheme' ) . '</p>';
				}
				?>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="user-posts-section">
			<h2 style="margin-bottom: 20px;"><?php printf( __( 'Posts by %s', 'swgtheme' ), esc_html( $user->display_name ) ); ?></h2>
			
			<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$user_posts = new WP_Query( array(
				'author'         => $user->ID,
				'posts_per_page' => 10,
				'paged'          => $paged,
				'post_status'    => 'publish',
			) );
			
			if ( $user_posts->have_posts() ) {
				echo '<div class="posts-grid" style="display: grid; gap: 20px;">';
				while ( $user_posts->have_posts() ) {
					$user_posts->the_post();
					?>
					<article class="post-item" style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; transition: transform 0.2s, box-shadow 0.2s;">
						<h3 style="margin: 0 0 10px 0;">
							<a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
								<?php the_title(); ?>
							</a>
						</h3>
						<div class="post-meta" style="color: #666; font-size: 14px; margin-bottom: 15px;">
							<?php echo get_the_date(); ?> • <?php comments_number( '0 Comments', '1 Comment', '% Comments' ); ?>
						</div>
						<div class="post-excerpt" style="color: #666;">
							<?php the_excerpt(); ?>
						</div>
						<a href="<?php the_permalink(); ?>" style="color: var(--primary-color, #dc3545); text-decoration: none; font-weight: bold;">
							<?php esc_html_e( 'Read More →', 'swgtheme' ); ?>
						</a>
					</article>
					<?php
				}
				echo '</div>';
				
				// Pagination
				if ( $user_posts->max_num_pages > 1 ) {
					echo '<div class="pagination" style="margin-top: 30px; text-align: center;">';
					echo paginate_links( array(
						'total'   => $user_posts->max_num_pages,
						'current' => $paged,
						'format'  => '?paged=%#%',
					) );
					echo '</div>';
				}
				
				wp_reset_postdata();
			} else {
				echo '<p style="color: #666;">' . __( 'No posts published yet.', 'swgtheme' ) . '</p>';
			}
			?>
		</div>
		
	</main>
</div>

<style>
.post-item:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>

<?php
get_footer();
