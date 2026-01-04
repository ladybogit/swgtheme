<?php
/**
 * Title: Block Patterns for SWG Theme
 * Slug: swgtheme/patterns
 * Categories: featured
 *
 * @package swgtheme
 */

// Hero Section Pattern
register_block_pattern(
	'swgtheme/hero-section',
	array(
		'title'       => __( 'Hero Section - Star Wars', 'swgtheme' ),
		'description' => __( 'A hero section with gradient background', 'swgtheme' ),
		'categories'  => array( 'featured', 'hero' ),
		'keywords'    => array( 'hero', 'banner', 'header' ),
		'content'     => '<!-- wp:cover {"url":"","customGradient":"linear-gradient(135deg,rgb(220,53,69) 0%,rgb(139,0,0) 100%)","align":"full"} -->
<div class="wp-block-cover alignfull"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim has-background-gradient" style="background:linear-gradient(135deg,rgb(220,53,69) 0%,rgb(139,0,0) 100%)"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color">Welcome to Lords of the Outer Rim</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">Join the adventure in a galaxy far, far away</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"primary"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-primary-color has-white-background-color has-text-color has-background wp-element-button">Get Started</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
	)
);

// Two Column Feature Pattern
register_block_pattern(
	'swgtheme/two-column-features',
	array(
		'title'       => __( 'Two Column Features', 'swgtheme' ),
		'description' => __( 'Feature sections in two columns', 'swgtheme' ),
		'categories'  => array( 'columns', 'featured' ),
		'keywords'    => array( 'features', 'columns', 'two' ),
		'content'     => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"primary"} -->
<h3 class="wp-block-heading has-primary-color has-text-color">⚔️ Combat System</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Engage in intense lightsaber duels and tactical combat scenarios</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"primary"} -->
<h3 class="wp-block-heading has-primary-color has-text-color">🌟 Explore Galaxies</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Discover new worlds and embark on epic quests across the universe</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
	)
);

// Call to Action Pattern
register_block_pattern(
	'swgtheme/call-to-action',
	array(
		'title'       => __( 'Call to Action Box', 'swgtheme' ),
		'description' => __( 'CTA box with gradient background', 'swgtheme' ),
		'categories'  => array( 'call-to-action' ),
		'keywords'    => array( 'cta', 'action', 'box' ),
		'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px"}},"backgroundColor":"primary","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-primary-background-color has-background" style="border-radius:8px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"textAlign":"center","textColor":"white"} -->
<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color">Join the Battle Today!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">Experience the thrill of intergalactic warfare</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"primary"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-primary-color has-white-background-color has-text-color has-background wp-element-button">Sign Up Now</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
	)
);

// Stats/Numbers Pattern
register_block_pattern(
	'swgtheme/stats-section',
	array(
		'title'       => __( 'Stats Section', 'swgtheme' ),
		'description' => __( 'Display statistics in columns', 'swgtheme' ),
		'categories'  => array( 'featured' ),
		'keywords'    => array( 'stats', 'numbers', 'metrics' ),
		'content'     => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary"} -->
<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color">10K+</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Active Players</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary"} -->
<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color">50+</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Planets to Explore</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary"} -->
<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color">100+</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Unique Missions</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
	)
);

// Testimonial Pattern
register_block_pattern(
	'swgtheme/testimonial',
	array(
		'title'       => __( 'Testimonial', 'swgtheme' ),
		'description' => __( 'A testimonial block with quote', 'swgtheme' ),
		'categories'  => array( 'text' ),
		'keywords'    => array( 'testimonial', 'quote', 'review' ),
		'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px","left":{"color":"var:preset|color|primary","width":"4px"}}},"backgroundColor":"light-gray","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-light-gray-background-color has-background" style="border-radius:8px;border-left-color:var(--wp--preset--color--primary);border-left-width:4px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:quote {"className":"is-style-plain"} -->
<blockquote class="wp-block-quote is-style-plain"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.25rem"}}} -->
<p style="font-size:1.25rem">"This is the best Star Wars gaming experience I\'ve ever had!"</p>
<!-- /wp:paragraph --><cite>- Jedi Master Kyle</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->',
	)
);
