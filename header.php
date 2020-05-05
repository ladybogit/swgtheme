<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<title><?php wp_title();?></title>
	<?php wp_head();?>
</head>
<body <?php body_class();?>>
<header>
<div class="container sticky-top" id="menu">
	<?php
		wp_nav_menu(
			array(
				'theme_location' => 'top-menu',
				'menu_class' => 'top-menu'
			)
		);
	?>
</div>
 
<div class="search-bar"><?php get_search_form();?></div>
</header>
