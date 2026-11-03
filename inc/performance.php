<?php
/**
 * Front-end performance clean-up.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove emoji detection, oEmbed discovery and other head clutter.
 *
 * @return void
 */
function verbena_remove_bloat() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
}
add_action( 'init', 'verbena_remove_bloat' );

/**
 * Drop the emoji DNS prefetch hint.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function verbena_resource_hints( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$urls = array_filter(
			$urls,
			static function ( $url ) {
				return ! is_string( $url ) || false === strpos( $url, 's.w.org' );
			}
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'verbena_resource_hints', 10, 2 );

/**
 * Dequeue jQuery when nothing queued depends on it, and block CSS when the
 * current page has no blocks. Gravity Forms needs jQuery, so this naturally
 * keeps it on pages that render a GF form.
 *
 * @return void
 */
function verbena_trim_frontend_assets() {
	if ( is_admin() ) {
		return;
	}

	$scripts = wp_scripts();
	if ( ! verbena_queue_needs( $scripts, array( 'jquery', 'jquery-core', 'jquery-migrate' ) ) ) {
		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'jquery-migrate' );
	}

	$has_blocks = is_singular() && has_blocks( get_post() );
	if ( ! $has_blocks ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'verbena_trim_frontend_assets', 100 );

/**
 * Whether any queued script (other than the handles themselves) depends on
 * one of the given handles.
 *
 * @param WP_Scripts $scripts Script registry.
 * @param string[]   $handles Handles to look for.
 * @return bool
 */
function verbena_queue_needs( $scripts, $handles ) {
	$seen  = array();
	$stack = array_diff( $scripts->queue, $handles );
	while ( $stack ) {
		$handle = array_pop( $stack );
		if ( isset( $seen[ $handle ] ) || ! isset( $scripts->registered[ $handle ] ) ) {
			continue;
		}
		$seen[ $handle ] = true;
		foreach ( $scripts->registered[ $handle ]->deps as $dep ) {
			if ( in_array( $dep, $handles, true ) ) {
				return true;
			}
			$stack[] = $dep;
		}
	}
	return false;
}

/**
 * Don't load core's separate block CSS for unused blocks on classic pages.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
