	</main><!-- #primary -->
	<footer id="footer" class="footer no-animation" style="position: fixed !important; bottom: 0 !important; left: 0 !important; width: 100vw !important; transform: none !important; animation: none !important; transition: none !important;">
		<div class="container">
			<nav class="footer-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'swgtheme' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer-menu',
					'menu_class'     => 'footer-menu',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
				?>
			</nav>
			<div class="site-info">
				<?php
				$footer_copyright = get_option( 'swgtheme_footer_copyright', '' );
				if ( ! empty( $footer_copyright ) ) {
					echo esc_html( $footer_copyright );
				} else {
					printf( esc_html__( '&copy; %s Lords of the Outer Rim. All rights reserved.', 'swgtheme' ), esc_html( date_i18n( 'Y' ) ) );
				}
				
				$footer_links = get_option( 'swgtheme_footer_links', '' );
				if ( ! empty( $footer_links ) ) {
					$links = explode( "\n", $footer_links );
					echo ' | ';
					foreach ( $links as $index => $link ) {
						$parts = explode( '|', $link );
						if ( count( $parts ) === 2 ) {
							$text = trim( $parts[0] );
							$url = trim( $parts[1] );
							if ( $index > 0 ) echo ' | ';
							echo '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
						}
					}
				}
				?>
			</div>
		</div>
	</footer>
</div><!-- #page -->
<div id="sticky-footer" style="position: fixed; bottom: 0; left: 0; width: 100%; height: 35px; background: linear-gradient(top, #5E5E5E, #000000); border-top-left-radius: 1em; border-top-right-radius: 1em; box-shadow: 3px 3px 1px #000000; z-index: 99999; text-align: center; display: flex; align-items: center; justify-content: center; pointer-events: none;">
	<div style="pointer-events: auto;"></div>
</div>
<script>
(function() {
	// Clone footer content to sticky footer
	var originalFooter = document.getElementById('footer');
	var stickyFooter = document.getElementById('sticky-footer');
	if (originalFooter && stickyFooter) {
		// Hide original footer
		originalFooter.style.display = 'none';
		// Copy content to sticky footer
		var footerContent = originalFooter.querySelector('.container');
		if (footerContent) {
			stickyFooter.querySelector('div').innerHTML = footerContent.innerHTML;
		}
	}
})();
</script>
<?php wp_footer(); ?>
</body>
</html>