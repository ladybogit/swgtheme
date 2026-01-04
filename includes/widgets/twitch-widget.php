<?php
/**
 * Twitch Stream Status Widget
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SWGTheme_Twitch_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
			'swgtheme_twitch_widget',
			__( 'SWG Twitch Stream Status', 'swgtheme' ),
			array(
				'description' => __( 'Display live stream status from Twitch', 'swgtheme' ),
			)
		);
	}
	
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Live Stream', 'swgtheme' );
		$username = ! empty( $instance['username'] ) ? $instance['username'] : '';
		$show_preview = ! empty( $instance['show_preview'] );
		
		if ( empty( $username ) ) {
			return;
		}
		
		echo $args['before_widget'];
		
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		
		// Check if integrations class exists
		if ( class_exists( 'SWGTheme_Integrations' ) ) {
			$integrations = new SWGTheme_Integrations();
			$stream_data = $integrations->get_twitch_stream_status( $username );
			
			if ( $stream_data && isset( $stream_data['is_live'] ) && $stream_data['is_live'] ) {
				?>
				<div class="swg-twitch-widget">
					<div class="twitch-status online">
						<span class="status-indicator"></span>
						<span class="status-text"><?php esc_html_e( 'LIVE NOW', 'swgtheme' ); ?></span>
					</div>
					
					<?php if ( isset( $stream_data['title'] ) ) : ?>
						<h4 class="stream-title"><?php echo esc_html( $stream_data['title'] ); ?></h4>
					<?php endif; ?>
					
					<?php if ( isset( $stream_data['game_name'] ) ) : ?>
						<p class="stream-game">
							<strong><?php esc_html_e( 'Playing:', 'swgtheme' ); ?></strong> 
							<?php echo esc_html( $stream_data['game_name'] ); ?>
						</p>
					<?php endif; ?>
					
					<?php if ( isset( $stream_data['viewer_count'] ) ) : ?>
						<p class="stream-viewers">
							<strong><?php esc_html_e( 'Viewers:', 'swgtheme' ); ?></strong> 
							<?php echo number_format_i18n( $stream_data['viewer_count'] ); ?>
						</p>
					<?php endif; ?>
					
					<?php if ( $show_preview && isset( $stream_data['thumbnail_url'] ) ) : ?>
						<div class="stream-preview">
							<img src="<?php echo esc_url( str_replace( array( '{width}', '{height}' ), array( '320', '180' ), $stream_data['thumbnail_url'] ) ); ?>" 
								alt="<?php esc_attr_e( 'Stream Preview', 'swgtheme' ); ?>" />
						</div>
					<?php endif; ?>
					
					<a href="https://twitch.tv/<?php echo esc_attr( $username ); ?>" 
						class="btn btn-primary" 
						target="_blank" 
						rel="noopener noreferrer">
						<?php esc_html_e( 'Watch Stream', 'swgtheme' ); ?>
					</a>
				</div>
				<?php
			} else {
				?>
				<div class="swg-twitch-widget">
					<div class="twitch-status offline">
						<span class="status-indicator"></span>
						<span class="status-text"><?php esc_html_e( 'Offline', 'swgtheme' ); ?></span>
					</div>
					
					<p><?php esc_html_e( 'Stream is currently offline.', 'swgtheme' ); ?></p>
					
					<a href="https://twitch.tv/<?php echo esc_attr( $username ); ?>" 
						class="btn btn-outline-primary" 
						target="_blank" 
						rel="noopener noreferrer">
						<?php esc_html_e( 'View Channel', 'swgtheme' ); ?>
					</a>
				</div>
				<style>
				.swg-twitch-widget { padding: 15px; }
				.twitch-status { 
					display: flex; 
					align-items: center; 
					margin-bottom: 15px;
					font-weight: bold;
				}
				.status-indicator {
					width: 12px;
					height: 12px;
					border-radius: 50%;
					margin-right: 8px;
					animation: pulse 2s infinite;
				}
				.twitch-status.online .status-indicator {
					background-color: #9147ff;
					box-shadow: 0 0 10px rgba(145, 71, 255, 0.5);
				}
				.twitch-status.offline .status-indicator {
					background-color: #999;
				}
				.twitch-status.online .status-text {
					color: #9147ff;
				}
				.stream-title {
					margin: 10px 0;
					font-size: 16px;
				}
				.stream-game, .stream-viewers {
					margin: 5px 0;
					font-size: 14px;
				}
				.stream-preview {
					margin: 15px 0;
				}
				.stream-preview img {
					width: 100%;
					height: auto;
					border-radius: 4px;
				}
				@keyframes pulse {
					0%, 100% { opacity: 1; }
					50% { opacity: 0.6; }
				}
				</style>
				<?php
			}
		} else {
			echo '<p>' . esc_html__( 'Please configure Twitch integration in Theme Options.', 'swgtheme' ) . '</p>';
		}
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Live Stream', 'swgtheme' );
		$username = ! empty( $instance['username'] ) ? $instance['username'] : '';
		$show_preview = ! empty( $instance['show_preview'] );
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
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>">
				<?php esc_html_e( 'Twitch Username:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $username ); ?>" 
				placeholder="yourusername" />
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_preview ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_preview' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_preview' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_preview' ) ); ?>">
				<?php esc_html_e( 'Show stream preview image', 'swgtheme' ); ?>
			</label>
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['username'] = ! empty( $new_instance['username'] ) ? sanitize_text_field( $new_instance['username'] ) : '';
		$instance['show_preview'] = ! empty( $new_instance['show_preview'] ) ? 1 : 0;
		
		return $instance;
	}
}

// Register widget
function swgtheme_register_twitch_widget() {
	register_widget( 'SWGTheme_Twitch_Widget' );
}
add_action( 'widgets_init', 'swgtheme_register_twitch_widget' );
