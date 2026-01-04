<?php
/**
 * YouTube Videos Widget
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SWGTheme_YouTube_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
			'swgtheme_youtube_widget',
			__( 'SWG YouTube Videos', 'swgtheme' ),
			array(
				'description' => __( 'Display latest YouTube videos from a channel', 'swgtheme' ),
			)
		);
	}
	
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Videos', 'swgtheme' );
		$channel_id = ! empty( $instance['channel_id'] ) ? $instance['channel_id'] : '';
		$max_results = ! empty( $instance['max_results'] ) ? absint( $instance['max_results'] ) : 3;
		$show_thumbnails = ! empty( $instance['show_thumbnails'] );
		
		if ( empty( $channel_id ) ) {
			return;
		}
		
		echo $args['before_widget'];
		
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		
		// Check if integrations class exists
		if ( class_exists( 'SWGTheme_Integrations' ) ) {
			$integrations = new SWGTheme_Integrations();
			$videos = $integrations->get_youtube_videos( $channel_id, $max_results );
			
			if ( ! empty( $videos ) ) {
				?>
				<div class="swg-youtube-widget">
					<?php foreach ( $videos as $video ) : ?>
						<div class="youtube-video-item">
							<?php if ( $show_thumbnails && isset( $video['thumbnail'] ) ) : ?>
								<div class="video-thumbnail">
									<a href="https://youtube.com/watch?v=<?php echo esc_attr( $video['video_id'] ); ?>" 
										target="_blank" 
										rel="noopener noreferrer">
										<img src="<?php echo esc_url( $video['thumbnail'] ); ?>" 
											alt="<?php echo esc_attr( $video['title'] ); ?>" />
										<div class="play-overlay">
											<svg width="68" height="48" viewBox="0 0 68 48" fill="none">
												<path d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path>
												<path d="M 45,24 27,14 27,34" fill="#fff"></path>
											</svg>
										</div>
									</a>
								</div>
							<?php endif; ?>
							
							<div class="video-details">
								<h5 class="video-title">
									<a href="https://youtube.com/watch?v=<?php echo esc_attr( $video['video_id'] ); ?>" 
										target="_blank" 
										rel="noopener noreferrer">
										<?php echo esc_html( $video['title'] ); ?>
									</a>
								</h5>
								
								<?php if ( isset( $video['published_at'] ) ) : ?>
									<p class="video-date">
										<?php
										$date = new DateTime( $video['published_at'] );
										echo esc_html( human_time_diff( $date->getTimestamp(), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'swgtheme' ) );
										?>
									</p>
								<?php endif; ?>
								
								<?php if ( isset( $video['description'] ) && ! empty( $video['description'] ) ) : ?>
									<p class="video-description">
										<?php echo esc_html( wp_trim_words( $video['description'], 15 ) ); ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
					
					<a href="https://youtube.com/channel/<?php echo esc_attr( $channel_id ); ?>" 
						class="btn btn-primary btn-sm" 
						target="_blank" 
						rel="noopener noreferrer">
						<?php esc_html_e( 'View Channel', 'swgtheme' ); ?>
					</a>
				</div>
				
				<style>
				.swg-youtube-widget { padding: 15px; }
				.youtube-video-item {
					margin-bottom: 20px;
					padding-bottom: 15px;
					border-bottom: 1px solid #eee;
				}
				.youtube-video-item:last-of-type {
					border-bottom: none;
				}
				.video-thumbnail {
					position: relative;
					margin-bottom: 10px;
				}
				.video-thumbnail img {
					width: 100%;
					height: auto;
					border-radius: 4px;
				}
				.play-overlay {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					transition: transform 0.3s ease;
				}
				.video-thumbnail:hover .play-overlay {
					transform: translate(-50%, -50%) scale(1.1);
				}
				.video-title {
					font-size: 14px;
					margin: 5px 0;
				}
				.video-title a {
					text-decoration: none;
					color: inherit;
				}
				.video-title a:hover {
					color: #f00;
				}
				.video-date {
					font-size: 12px;
					color: #666;
					margin: 5px 0;
				}
				.video-description {
					font-size: 13px;
					color: #555;
					margin: 5px 0;
				}
				.swg-youtube-widget .btn {
					margin-top: 10px;
				}
				</style>
				<?php
			} else {
				echo '<p>' . esc_html__( 'No videos found.', 'swgtheme' ) . '</p>';
			}
		} else {
			echo '<p>' . esc_html__( 'Please configure YouTube integration in Theme Options.', 'swgtheme' ) . '</p>';
		}
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Videos', 'swgtheme' );
		$channel_id = ! empty( $instance['channel_id'] ) ? $instance['channel_id'] : '';
		$max_results = ! empty( $instance['max_results'] ) ? absint( $instance['max_results'] ) : 3;
		$show_thumbnails = ! empty( $instance['show_thumbnails'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'channel_id' ) ); ?>">
				<?php esc_html_e( 'YouTube Channel ID:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'channel_id' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'channel_id' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $channel_id ); ?>" 
				placeholder="UC..." />
			<small><?php esc_html_e( 'Find your channel ID in YouTube Studio → Customization → Basic Info', 'swgtheme' ); ?></small>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max_results' ) ); ?>">
				<?php esc_html_e( 'Number of Videos:', 'swgtheme' ); ?>
			</label>
			<input class="tiny-text" 
				id="<?php echo esc_attr( $this->get_field_id( 'max_results' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'max_results' ) ); ?>" 
				type="number" 
				min="1" 
				max="10" 
				value="<?php echo esc_attr( $max_results ); ?>" />
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_thumbnails ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnails' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnails' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbnails' ) ); ?>">
				<?php esc_html_e( 'Show video thumbnails', 'swgtheme' ); ?>
			</label>
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['channel_id'] = ! empty( $new_instance['channel_id'] ) ? sanitize_text_field( $new_instance['channel_id'] ) : '';
		$instance['max_results'] = ! empty( $new_instance['max_results'] ) ? absint( $new_instance['max_results'] ) : 3;
		$instance['show_thumbnails'] = ! empty( $new_instance['show_thumbnails'] ) ? 1 : 0;
		
		return $instance;
	}
}

// Register widget
function swgtheme_register_youtube_widget() {
	register_widget( 'SWGTheme_YouTube_Widget' );
}
add_action( 'widgets_init', 'swgtheme_register_youtube_widget' );
