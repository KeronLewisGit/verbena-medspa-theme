<?php
/**
 * Google Tag Manager / GA4 output and dataLayer helpers.
 *
 * IDs come from site settings, never from code. Nothing is printed when both
 * IDs are empty, so a site without consent for analytics loads no tags.
 *
 * Events pushed by the theme (see README):
 *   book_now_click  { treatment_name, link_location }
 *   phone_click     { link_location }
 *   generate_lead   { form_id, form_name, treatment_name }
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Validated GTM container ID or empty string.
 *
 * @return string
 */
function verbena_gtm_id() {
	$id = strtoupper( trim( (string) verbena_option( 'gtm_id' ) ) );
	return preg_match( '/^GTM-[A-Z0-9]{4,12}$/', $id ) ? $id : '';
}

/**
 * Validated GA4 measurement ID or empty string.
 *
 * @return string
 */
function verbena_ga4_id() {
	$id = strtoupper( trim( (string) verbena_option( 'ga4_id' ) ) );
	return preg_match( '/^G-[A-Z0-9]{4,15}$/', $id ) ? $id : '';
}

/**
 * Which tag setup is active: gtm, gtag or none.
 *
 * @return string
 */
function verbena_analytics_mode() {
	if ( verbena_gtm_id() ) {
		return 'gtm';
	}
	return verbena_ga4_id() ? 'gtag' : 'none';
}

/**
 * Head snippet: GTM container, or gtag.js when only a GA4 ID is set.
 *
 * @return void
 */
function verbena_analytics_head() {
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && ! apply_filters( 'verbena_track_editors', false ) ) {
		return;
	}
	$mode = verbena_analytics_mode();

	if ( 'gtm' === $mode ) {
		$gtm = verbena_gtm_id();
		$js  = "window.dataLayer=window.dataLayer||[];(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer'," . wp_json_encode( $gtm ) . ');';
		wp_print_inline_script_tag( $js );
		return;
	}

	if ( 'gtag' === $mode ) {
		$ga4 = verbena_ga4_id();
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- must be in <head> before other tags.
		printf( '<script async src="%s"></script>' . "\n", esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ga4 ) );
		wp_print_inline_script_tag( 'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode( $ga4 ) . ');' );
	}
}
add_action( 'wp_head', 'verbena_analytics_head', 1 );

/**
 * GTM noscript iframe right after <body>.
 *
 * @return void
 */
function verbena_analytics_body() {
	if ( 'gtm' !== verbena_analytics_mode() || ( is_user_logged_in() && current_user_can( 'edit_posts' ) && ! apply_filters( 'verbena_track_editors', false ) ) ) {
		return;
	}
	printf(
		'<noscript><iframe src="%s" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>' . "\n",
		esc_url( 'https://www.googletagmanager.com/ns.html?id=' . verbena_gtm_id() )
	);
}
add_action( 'wp_body_open', 'verbena_analytics_body', 1 );

/**
 * JS that records an event. Pushes to the dataLayer (for GTM) and, in
 * gtag-only mode, also sends it as a GA4 event so nothing is lost.
 *
 * @param string $event  Event name.
 * @param array  $params Event parameters.
 * @return string JavaScript.
 */
function verbena_track_js( $event, $params = array() ) {
	$payload = wp_json_encode( array_merge( array( 'event' => $event ), $params ), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
	$js      = 'window.dataLayer=window.dataLayer||[];window.dataLayer.push(' . $payload . ');';
	if ( 'gtag' === verbena_analytics_mode() ) {
		$js .= 'if(typeof gtag==="function"){gtag("event",' . wp_json_encode( $event ) . ',' . wp_json_encode( $params, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . ');}';
	}
	return $js;
}
