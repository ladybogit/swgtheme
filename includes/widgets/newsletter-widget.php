<?php
/**
 * Newsletter Subscription Widget
 *
 * @package swgtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SWGTheme_Newsletter_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
			'swgtheme_newsletter_widget',
			__( 'SWG Newsletter Signup', 'swgtheme' ),
			array(
				'description' => __( 'Mailchimp newsletter subscription form', 'swgtheme' ),
			)
		);
	}
	
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Subscribe to Newsletter', 'swgtheme' );
		$description = ! empty( $instance['description'] ) ? $instance['description'] : '';
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'Subscribe', 'swgtheme' );
		$show_name_field = ! empty( $instance['show_name_field'] );
		
		echo $args['before_widget'];
		
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		
		?>
		<div class="swg-newsletter-widget">
			<?php if ( ! empty( $description ) ) : ?>
				<p class="newsletter-description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			
			<form class="swg-newsletter-form" method="post">
				<?php if ( $show_name_field ) : ?>
					<div class="form-group">
						<label for="swg-newsletter-name-<?php echo esc_attr( $this->id ); ?>" class="sr-only">
							<?php esc_html_e( 'Name', 'swgtheme' ); ?>
						</label>
						<input type="text" 
							id="swg-newsletter-name-<?php echo esc_attr( $this->id ); ?>" 
							name="name" 
							class="form-control" 
							placeholder="<?php esc_attr_e( 'Your Name', 'swgtheme' ); ?>" />
					</div>
				<?php endif; ?>
				
				<div class="form-group">
					<label for="swg-newsletter-email-<?php echo esc_attr( $this->id ); ?>" class="sr-only">
						<?php esc_html_e( 'Email', 'swgtheme' ); ?>
					</label>
					<input type="email" 
						id="swg-newsletter-email-<?php echo esc_attr( $this->id ); ?>" 
						name="email" 
						class="form-control" 
						placeholder="<?php esc_attr_e( 'Your Email', 'swgtheme' ); ?>" 
						required />
				</div>
				
				<button type="submit" class="btn btn-primary btn-block">
					<?php echo esc_html( $button_text ); ?>
				</button>
				
				<div class="newsletter-message" style="display: none; margin-top: 10px;"></div>
			</form>
		</div>
		
		<style>
		.swg-newsletter-widget { padding: 15px; }
		.newsletter-description {
			margin-bottom: 15px;
			font-size: 14px;
			color: #666;
		}
		.swg-newsletter-form .form-group {
			margin-bottom: 10px;
		}
		.swg-newsletter-form .form-control {
			width: 100%;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.swg-newsletter-form .btn-block {
			width: 100%;
		}
		.newsletter-message {
			padding: 10px;
			border-radius: 4px;
			font-size: 14px;
		}
		.newsletter-message.success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		.newsletter-message.error {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
		</style>
		<?php
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Subscribe to Newsletter', 'swgtheme' );
		$description = ! empty( $instance['description'] ) ? $instance['description'] : '';
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'Subscribe', 'swgtheme' );
		$show_name_field = ! empty( $instance['show_name_field'] );
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
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">
				<?php esc_html_e( 'Description:', 'swgtheme' ); ?>
			</label>
			<textarea class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" 
				rows="3"><?php echo esc_textarea( $description ); ?></textarea>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>">
				<?php esc_html_e( 'Button Text:', 'swgtheme' ); ?>
			</label>
			<input class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $button_text ); ?>" />
		</p>
		
		<p>
			<input class="checkbox" 
				type="checkbox" 
				<?php checked( $show_name_field ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_name_field' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_name_field' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_name_field' ) ); ?>">
				<?php esc_html_e( 'Show name field', 'swgtheme' ); ?>
			</label>
		</p>
		
		<p style="background: #f0f0f0; padding: 10px; border-left: 3px solid #0073aa;">
			<strong><?php esc_html_e( 'Note:', 'swgtheme' ); ?></strong><br/>
			<?php esc_html_e( 'Configure Mailchimp API settings in Appearance → Integrations', 'swgtheme' ); ?>
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['description'] = ! empty( $new_instance['description'] ) ? sanitize_textarea_field( $new_instance['description'] ) : '';
		$instance['button_text'] = ! empty( $new_instance['button_text'] ) ? sanitize_text_field( $new_instance['button_text'] ) : '';
		$instance['show_name_field'] = ! empty( $new_instance['show_name_field'] ) ? 1 : 0;
		
		return $instance;
	}
}

// Register widget
function swgtheme_register_newsletter_widget() {
	register_widget( 'SWGTheme_Newsletter_Widget' );
}
add_action( 'widgets_init', 'swgtheme_register_newsletter_widget' );

/**
 * Newsletter shortcode
 * Usage: [swg_newsletter title="Subscribe" description="Get updates" button_text="Sign Up" show_name="1"]
 */
function swgtheme_newsletter_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'title' => __( 'Subscribe to Newsletter', 'swgtheme' ),
		'description' => '',
		'button_text' => __( 'Subscribe', 'swgtheme' ),
		'show_name' => '0',
	), $atts, 'swg_newsletter' );
	
	$unique_id = 'swg-newsletter-' . wp_rand( 1000, 9999 );
	
	ob_start();
	?>
	<div class="swg-newsletter-shortcode">
		<?php if ( ! empty( $atts['title'] ) ) : ?>
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
		<?php endif; ?>
		
		<?php if ( ! empty( $atts['description'] ) ) : ?>
			<p class="newsletter-description"><?php echo esc_html( $atts['description'] ); ?></p>
		<?php endif; ?>
		
		<form class="swg-newsletter-form" method="post">
			<?php if ( $atts['show_name'] === '1' ) : ?>
				<div class="form-group">
					<label for="<?php echo esc_attr( $unique_id ); ?>-name" class="sr-only">
						<?php esc_html_e( 'Name', 'swgtheme' ); ?>
					</label>
					<input type="text" 
						id="<?php echo esc_attr( $unique_id ); ?>-name" 
						name="name" 
						class="form-control" 
						placeholder="<?php esc_attr_e( 'Your Name', 'swgtheme' ); ?>" />
				</div>
			<?php endif; ?>
			
			<div class="form-group">
				<label for="<?php echo esc_attr( $unique_id ); ?>-email" class="sr-only">
					<?php esc_html_e( 'Email', 'swgtheme' ); ?>
				</label>
				<input type="email" 
					id="<?php echo esc_attr( $unique_id ); ?>-email" 
					name="email" 
					class="form-control" 
					placeholder="<?php esc_attr_e( 'Your Email', 'swgtheme' ); ?>" 
					required />
			</div>
			
			<button type="submit" class="btn btn-primary">
				<?php echo esc_html( $atts['button_text'] ); ?>
			</button>
			
			<div class="newsletter-message" style="display: none; margin-top: 10px;"></div>
		</form>
	</div>
	
	<style>
	.swg-newsletter-shortcode {
		max-width: 500px;
		margin: 20px auto;
		padding: 30px;
		background: #f9f9f9;
		border-radius: 8px;
	}
	.swg-newsletter-shortcode h3 {
		margin-top: 0;
		margin-bottom: 10px;
	}
	.swg-newsletter-shortcode .newsletter-description {
		margin-bottom: 20px;
		color: #666;
	}
	.swg-newsletter-shortcode .form-group {
		margin-bottom: 15px;
	}
	.swg-newsletter-shortcode .form-control {
		width: 100%;
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 4px;
		font-size: 16px;
	}
	.swg-newsletter-shortcode .btn {
		padding: 12px 30px;
		font-size: 16px;
	}
	</style>
	<?php
	
	return ob_get_clean();
}
add_shortcode( 'swg_newsletter', 'swgtheme_newsletter_shortcode' );
