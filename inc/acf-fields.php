<?php
/**
 * ACF Pro / Secure Custom Fields registration.
 *
 * Field groups are registered in PHP (from inc/fields-schema.php) so they are
 * version controlled and cannot drift between environments.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the options page and local field groups.
 *
 * @return void
 */
function verbena_register_acf() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Verbena site settings', 'verbena' ),
				'menu_title' => __( 'Site settings', 'verbena' ),
				'menu_slug'  => 'verbena-settings',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-admin-generic',
				'position'   => 59,
				'redirect'   => false,
			)
		);
	}

	foreach ( verbena_field_groups() as $group ) {
		acf_add_local_field_group( $group );
	}
}
add_action( 'acf/init', 'verbena_register_acf' );

/**
 * Hide the plain WordPress editor on templates that are fully field driven.
 *
 * @return void
 */
function verbena_hide_editor_for_field_templates() {
	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && (int) get_option( 'page_on_front' ) === $post_id ) {
		remove_post_type_support( 'page', 'editor' );
	}
}
add_action( 'current_screen', 'verbena_hide_editor_for_field_templates' );
