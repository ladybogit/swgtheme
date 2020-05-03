<?php
/*
Template Name: Full Page
*/
?>


<?php get_header();?>
<div id="swgimage"></div>
<section class="page-wrap"><div class="container">
	

	<div class="row">
		<div id="slide" class="slide">
<?php if ( function_exists( 'soliloquy' ) ) { soliloquy( '98' ); }?>
	</div>
	</div>
	<div class="row">
		<h1><?php the_title();?></h1>
	</div>
	<div class="row">

		<?php get_template_part('includes/section','content');?>
	</div>
</div>
<div class="row">
	<div class="cpr">
		&copy; Bogit codeing 2020
	</div>
</div></section>
<?php get_footer();?>