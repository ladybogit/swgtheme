<?php 

function load_css()
{
		wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', 
			array(), false, 'all');
		wp_enqueue_style('bootstrap');

		wp_register_style('stylesheet', get_template_directory_uri() . '/css/main.css', 
			array(), false, 'all');
		wp_enqueue_style('stylesheet');

		wp_register_style('swg_styles', get_template_directory_uri() . 'css/swg-slider.css', array(), false, 'all');
		wp_enqueue_style('swg_styles');

    	wp_register_style('swg_styles_theme', get_template_directory_uri() . 'css/default.css',
    		array(), false, 'all');
		wp_enqueue_style('swg_styles_theme');
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
register_sidebar(
	array(
			'name' => 'galary Sidebar',
			'id' => 'galary-sidebar',
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

 
function swg_init() {
    $args = array(
        'public' => true,
        'label' => 'Star Wars Galaxies Image Slider',
        'supports' => array(
            'title',
            'thumbnail'
        )
    );
    register_post_type('swg_images', $args);
}
add_action('init', 'swg_init');
add_image_size('swg_widget', 1000, 1000, true);
add_image_size('swg_function', 1600, 1400, true);

function swg_function($type='swg_function') {
    $args = array(
        'post_type' => 'np_images',
        'posts_per_page' => 5
    );
    $result = '<div class="slider-wrapper theme-default">';
    $result .= '<div id="slider" class="swgSlider">';
 
    //the loop
    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();
 
        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);
        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
    }
    $result .= '</div>';
    $result .='<div id = "htmlcaption" class = "swg-html-caption">';
    $result .='<strong>This</strong> is an example of a <em>HTML</em> caption with <a href = "#">a link</a>.';
    $result .='</div>';
    $result .='</div>';
    return $result;
}
/*add_shortcode('swg-shortcode', 'swg_function');
*/
function swg_widgets_init() {
    register_widget('swg_Widget');
}
 
add_action('widgets_init', 'swg_widgets_init');
class swg_Widget extends WP_Widget {
 
    public function __construct() {
        parent::__construct('swg_Widget', 'SWG Slideshow', array('description' => __('A SWG Slideshow Widget', 'text_domain')));
    }

public function form($instance) {
    if (isset($instance['title'])) {
        $title = $instance['title'];
    }
    else {
        $title = __('Widget Slideshow', 'text_domain');
    }
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
    <?php
}
 
public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = strip_tags($new_instance['title']);
 
    return $instance;
}
public function widget($args, $instance) {
    extract($args);
    // the title
    $title = apply_filters('widget_title', $instance['title']);
    echo $before_widget;
    if (!empty($title))
        echo $before_title . $title . $after_title;
    echo swg_function('swg_widget');
    echo $after_widget;
}

}
