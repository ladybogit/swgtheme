<?php 

function load_css()
{
		wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', 
		array(), false, 'all');
		wp_enqueue_style('bootstrap');

		wp_register_style('stylesheet', get_template_directory_uri() . '/css/main.css', 
		array(), false, 'all');
		wp_enqueue_style('stylesheet');
}

add_action('wp_enqueue_scripts','load_css');

function load_js()
{
		wp_enqueue_script('jquery');
		wp_register_script('bootstrapjs', get_template_directory_uri() . '/js/bootstrap.min.js', 'jquery', false, true);
		wp_enqueue_script('bootstrapjs');
}

add_action('wp_enqueue_scripts','load_js');

/*add_theme_support('menus');*/
add_theme_support('post-thumbnails');
add_theme_support('widgets');
add_theme_support( "custom-header", $args );
add_theme_support( "custom-background", $args ) ;
add_theme_support( "title-tag" );
add_theme_support( 'automatic-feed-links' );
add_editor_style();

register_nav_menus(
array(
'top-menu' => 'Top Menu Locaion',
'side-menu' => 'Side Menu Locaion',
'mobile-menu' => 'Mobile Menu Locaion',
	)
);

add_image_size('blog-large', 800, 400, true);
add_image_size('blog-small', 300, 200, true);

function my_sidebars()
{
register_sidebar(
	array(
			'name' => 'Page Sidebar',
			'id' => 'page-sidebar',
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>'
	)
);
}
add_action('widgets_init','my_sidebars');


function tags_support_all() {
	register_taxonomy_for_object_type('post_tag', 'page');
}


function tags_support_query($wp_query) {
	if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
}

add_action('init', 'tags_support_all');
add_action('pre_get_posts', 'tags_support_query');

add_theme_support( 'title-tag' );
add_theme_support( 'custom-logo', array(
    'height' => 480,
    'width'  => 720,
) );
