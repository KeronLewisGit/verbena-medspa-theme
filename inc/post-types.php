<?php
/**
 * Custom post types and taxonomies.
 *
 * Note: post types normally belong in a plugin so content survives a theme
 * change. They live here to keep the demo a single installable package.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the treatment CPT, its area taxonomy and the private lead CPT.
 *
 * @return void
 */
function verbena_register_post_types() {
	register_post_type(
		'treatment',
		array(
			'labels'        => array(
				'name'               => __( 'Treatments', 'verbena' ),
				'singular_name'      => __( 'Treatment', 'verbena' ),
				'add_new_item'       => __( 'Add treatment', 'verbena' ),
				'edit_item'          => __( 'Edit treatment', 'verbena' ),
				'new_item'           => __( 'New treatment', 'verbena' ),
				'view_item'          => __( 'View treatment', 'verbena' ),
				'search_items'       => __( 'Search treatments', 'verbena' ),
				'not_found'          => __( 'No treatments found', 'verbena' ),
				'all_items'          => __( 'All treatments', 'verbena' ),
				'archives'           => __( 'Treatments', 'verbena' ),
				'menu_name'          => __( 'Treatments', 'verbena' ),
				'featured_image'     => __( 'Treatment image', 'verbena' ),
				'set_featured_image' => __( 'Set treatment image', 'verbena' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => 'treatments',
			'rewrite'       => array(
				'slug'       => 'treatments',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-heart',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions' ),
		)
	);

	register_taxonomy(
		'treatment_area',
		'treatment',
		array(
			'labels'            => array(
				'name'          => __( 'Areas', 'verbena' ),
				'singular_name' => __( 'Area', 'verbena' ),
				'all_items'     => __( 'All areas', 'verbena' ),
				'edit_item'     => __( 'Edit area', 'verbena' ),
				'add_new_item'  => __( 'Add area', 'verbena' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'treatment-area',
				'with_front' => false,
			),
		)
	);

	register_post_type(
		'verbena_lead',
		array(
			'labels'          => array(
				'name'          => __( 'Leads', 'verbena' ),
				'singular_name' => __( 'Lead', 'verbena' ),
				'edit_item'     => __( 'Consultation request', 'verbena' ),
				'all_items'     => __( 'Consultation requests', 'verbena' ),
				'menu_name'     => __( 'Leads', 'verbena' ),
				'not_found'     => __( 'No requests yet', 'verbena' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 22,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'verbena_register_post_types' );

/**
 * Lead list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function verbena_lead_columns( $columns ) {
	return array(
		'cb'                => $columns['cb'],
		'title'             => __( 'Name', 'verbena' ),
		'verbena_email'     => __( 'Email', 'verbena' ),
		'verbena_phone'     => __( 'Phone', 'verbena' ),
		'verbena_treatment' => __( 'Interested in', 'verbena' ),
		'date'              => $columns['date'],
	);
}
add_filter( 'manage_verbena_lead_posts_columns', 'verbena_lead_columns' );

/**
 * Lead list column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 * @return void
 */
function verbena_lead_column_values( $column, $post_id ) {
	$map = array(
		'verbena_email'     => '_verbena_email',
		'verbena_phone'     => '_verbena_phone',
		'verbena_treatment' => '_verbena_treatment',
	);
	if ( isset( $map[ $column ] ) ) {
		echo esc_html( (string) get_post_meta( $post_id, $map[ $column ], true ) );
	}
}
add_action( 'manage_verbena_lead_posts_custom_column', 'verbena_lead_column_values', 10, 2 );

/**
 * Read-only meta box showing the submitted request.
 *
 * @return void
 */
function verbena_lead_meta_box() {
	add_meta_box( 'verbena-lead-details', __( 'Request details', 'verbena' ), 'verbena_render_lead_meta_box', 'verbena_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'verbena_lead_meta_box' );

/**
 * Render the lead details box.
 *
 * @param WP_Post $post Lead.
 * @return void
 */
function verbena_render_lead_meta_box( $post ) {
	$rows = array(
		__( 'Email', 'verbena' )             => get_post_meta( $post->ID, '_verbena_email', true ),
		__( 'Phone', 'verbena' )             => get_post_meta( $post->ID, '_verbena_phone', true ),
		__( 'Interested in', 'verbena' )     => get_post_meta( $post->ID, '_verbena_treatment', true ),
		__( 'Preferred contact', 'verbena' ) => get_post_meta( $post->ID, '_verbena_contact_pref', true ),
		__( 'Message', 'verbena' )           => get_post_meta( $post->ID, '_verbena_message', true ),
		__( 'Source page', 'verbena' )       => get_post_meta( $post->ID, '_verbena_source', true ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf( '<tr><th scope="row" style="width:180px">%s</th><td>%s</td></tr>', esc_html( $label ), nl2br( esc_html( (string) $value ) ) );
	}
	echo '</tbody></table>';
}
