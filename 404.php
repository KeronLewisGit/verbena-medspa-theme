<?php
/**
 * 404.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

verbena_page_hero(
	array(
		'eyebrow' => __( 'Error 404', 'verbena' ),
		'title'   => __( 'That page has moved or never existed', 'verbena' ),
		'intro'   => __( 'Try the treatment menu, or book a consultation and we will point you in the right direction.', 'verbena' ),
		'actions' => array(
			'<a class="btn btn-primary" href="' . esc_url( get_post_type_archive_link( 'treatment' ) ) . '">' . esc_html__( 'View treatments', 'verbena' ) . '</a>',
			'<a class="btn btn-ghost" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Back to home', 'verbena' ) . '</a>',
		),
	)
);

get_footer();
