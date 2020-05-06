<section class="footer">
<footer>
<div class="footer" id="footer">
	<div class="container"><div class="footer3">
	<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer-menu',
				'menu_class' => 'footer-menu'
			)
		);
	?>
</div>
</div>
<div class="cpr">
		&copy; Bogit codeing 2020
	</div>
</div>
</footer>
</section>
<?php wp_footer();?>
</body>
</html>