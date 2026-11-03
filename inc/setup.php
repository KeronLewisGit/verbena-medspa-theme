<?php
/**
 * Theme supports, menus and assets.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 *
 * @return void
 */
function verbena_setup() {
	load_theme_textdomain( 'verbena', VERBENA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'verbena' ),
			'footer'  => __( 'Footer navigation', 'verbena' ),
		)
	);

	set_post_thumbnail_size( 768, 0 );
}
add_action( 'after_setup_theme', 'verbena_setup' );

/**
 * Content width for embeds.
 *
 * @return void
 */
function verbena_content_width() {
	$GLOBALS['content_width'] = 720; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'verbena_content_width', 0 );

/**
 * Front-end CSS and JS.
 *
 * @return void
 */
function verbena_enqueue_assets() {
	$css = VERBENA_DIR . '/assets/css/app.css';
	$js  = VERBENA_DIR . '/assets/js/app.js';

	wp_enqueue_style( 'verbena-app', VERBENA_URI . '/assets/css/app.css', array(), (string) filemtime( $css ) );

	wp_enqueue_script(
		'verbena-app',
		VERBENA_URI . '/assets/js/app.js',
		array(),
		(string) filemtime( $js ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'verbena-app',
		'verbenaConfig',
		array(
			'analyticsMode' => verbena_analytics_mode(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'verbena_enqueue_assets' );

/**
 * Preload the display font used by the LCP headline.
 *
 * @return void
 */
function verbena_preload_fonts() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( VERBENA_URI . '/assets/fonts/gloock-latin.woff2' )
	);
}
add_action( 'wp_head', 'verbena_preload_fonts', 2 );

/**
 * Add Tailwind classes to primary menu links.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu args.
 * @return array
 */
function verbena_menu_link_attributes( $atts, $item, $args ) {
	if ( 'primary' === $args->theme_location ) {
		$atts['class'] = 'nav-link';
	} elseif ( 'footer' === $args->theme_location ) {
		$atts['class'] = 'inline-flex min-h-11 items-center hover:text-blush underline-offset-4 hover:underline';
	}
	if ( in_array( 'current-menu-item', (array) $item->classes, true ) || in_array( 'current_page_item', (array) $item->classes, true ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'verbena_menu_link_attributes', 10, 3 );

/**
 * Mark the Treatments archive menu item current on single treatments too.
 *
 * @param array   $classes Classes.
 * @param WP_Post $item    Menu item.
 * @return array
 */
function verbena_menu_item_classes( $classes, $item ) {
	if ( ( is_singular( 'treatment' ) || is_tax( 'treatment_area' ) ) && 'post_type_archive' === $item->type && 'treatment' === $item->object ) {
		$classes[] = 'current-menu-item';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'verbena_menu_item_classes', 10, 2 );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function verbena_body_classes( $classes ) {
	$classes[] = 'bg-bg text-ink font-body antialiased';
	return $classes;
}
add_filter( 'body_class', 'verbena_body_classes' );

/**
 * Archive query tweaks: show every treatment, ordered by menu order.
 *
 * @param WP_Query $query Query.
 * @return void
 */
function verbena_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'treatment' ) || $query->is_tax( 'treatment_area' ) ) {
		$query->set( 'posts_per_page', 24 );
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'verbena_pre_get_posts' );
