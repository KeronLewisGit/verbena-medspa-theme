<?php
/**
 * Third-party booking widget integration.
 *
 * Medspas book through Zenoti, Boulevard, Vagaro, Phorest or Mindbody. Their
 * embeds are heavy (hundreds of KB of JS plus an iframe), so the theme renders
 * a click-to-load facade: a static panel with a button. The provider iframe is
 * only created when the visitor asks for it.
 *
 * Usage: [verbena_booking treatment="hydrafacial" height="760"]
 *
 * With no booking URL configured, the facade loads a bundled demo widget
 * (?verbena_booking_demo=1) so the integration can be clicked through.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Provider display names.
 *
 * @return array
 */
function verbena_booking_providers() {
	return array(
		'demo'      => __( 'Demo booking widget', 'verbena' ),
		'zenoti'    => 'Zenoti',
		'boulevard' => 'Boulevard',
		'vagaro'    => 'Vagaro',
		'phorest'   => 'Phorest',
		'mindbody'  => 'Mindbody',
		'other'     => __( 'online booking', 'verbena' ),
	);
}

/**
 * Booking widget URL, optionally preselecting a treatment.
 *
 * @param string $treatment_slug Treatment slug.
 * @return string
 */
function verbena_booking_url( $treatment_slug = '' ) {
	$base = (string) verbena_option( 'booking_url' );
	if ( '' === $base ) {
		$args = array( 'verbena_booking_demo' => '1' );
		if ( $treatment_slug ) {
			$args['service'] = $treatment_slug;
		}
		return add_query_arg( $args, home_url( '/' ) );
	}

	if ( $treatment_slug ) {
		$treatment = get_page_by_path( $treatment_slug, OBJECT, 'treatment' );
		$service   = $treatment ? (string) verbena_field( 'booking_service_id', $treatment->ID ) : '';
		$param     = (string) verbena_option( 'booking_service_param' );
		if ( $service && $param ) {
			$base = add_query_arg( sanitize_key( $param ), rawurlencode( $service ), $base );
		}
	}
	return $base;
}

/**
 * Shortcode handler.
 *
 * @param array $atts Attributes.
 * @return string
 */
function verbena_booking_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'treatment' => '',
			'height'    => '760',
			'title'     => '',
		),
		$atts,
		'verbena_booking'
	);

	$slug = sanitize_title( $atts['treatment'] );
	// Allow ?treatment=slug on the Book page to preselect a service.
	if ( '' === $slug && isset( $_GET['treatment'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only preselect.
		$slug = sanitize_title( wp_unslash( $_GET['treatment'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	$treatment = $slug ? get_page_by_path( $slug, OBJECT, 'treatment' ) : null;

	ob_start();
	get_template_part(
		'template-parts/booking-widget',
		null,
		array(
			'src'            => verbena_booking_url( $treatment ? $slug : '' ),
			'height'         => max( 480, min( 1400, absint( $atts['height'] ) ) ),
			'title'          => $atts['title'],
			'treatment_name' => $treatment ? get_the_title( $treatment ) : '',
			'provider'       => verbena_booking_providers()[ verbena_option( 'booking_provider', 'demo' ) ] ?? __( 'online booking', 'verbena' ),
		)
	);
	return (string) ob_get_clean();
}
add_shortcode( 'verbena_booking', 'verbena_booking_shortcode' );

/**
 * Register the demo widget query var.
 *
 * @param array $vars Query vars.
 * @return array
 */
function verbena_booking_query_vars( $vars ) {
	$vars[] = 'verbena_booking_demo';
	return $vars;
}
add_filter( 'query_vars', 'verbena_booking_query_vars' );

/**
 * Serve the bundled demo widget (stands in for a provider iframe).
 *
 * @return void
 */
function verbena_render_demo_booking() {
	if ( '1' !== get_query_var( 'verbena_booking_demo' ) ) {
		return;
	}
	header( 'X-Robots-Tag: noindex, nofollow' );
	status_header( 200 );
	get_template_part( 'template-parts/booking-demo-widget' );
	exit;
}
add_action( 'template_redirect', 'verbena_render_demo_booking', 0 );
