<?php
/**
 * Field group definitions.
 *
 * One source of truth for every custom field in the theme. The same arrays are
 * passed to acf_add_local_field_group() when ACF Pro or Secure Custom Fields is
 * active, used by verbena_field() to read repeaters from raw meta when it is
 * not, and used by the demo importer to write values in ACF's storage format.
 *
 * Field names are unique across all groups so the flat lookup in
 * verbena_field_definition() is unambiguous.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build a field array with a predictable key.
 *
 * @param string $name  Field name (meta key).
 * @param string $label Admin label.
 * @param string $type  ACF field type.
 * @param array  $extra Extra ACF settings.
 * @return array
 */
function verbena_def( $name, $label, $type = 'text', $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_verbena_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/**
 * Build a sub field for a repeater. Keys are namespaced by the parent.
 *
 * @param string $parent_name Parent repeater name.
 * @param string $name   Sub field name.
 * @param string $label  Admin label.
 * @param string $type   ACF field type.
 * @param array  $extra  Extra settings.
 * @return array
 */
function verbena_sub( $parent_name, $name, $label, $type = 'text', $extra = array() ) {
	$field        = verbena_def( $name, $label, $type, $extra );
	$field['key'] = 'field_verbena_' . $parent_name . '_' . $name;
	return $field;
}

/**
 * All field groups, in ACF local field group format.
 *
 * @return array
 */
function verbena_field_groups() {
	static $groups = null;
	if ( null !== $groups ) {
		return $groups;
	}

	$groups = array(
		array(
			'key'      => 'group_verbena_treatment',
			'title'    => __( 'Treatment details', 'verbena' ),
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'treatment',
					),
				),
			),
			'fields'   => array(
				verbena_def(
					'summary',
					__( 'Card summary', 'verbena' ),
					'textarea',
					array(
						'rows'         => 2,
						'new_lines'    => '',
						'instructions' => __( 'One or two sentences for cards and the page hero.', 'verbena' ),
					)
				),
				verbena_def(
					'price_from',
					__( 'Price from (USD)', 'verbena' ),
					'number',
					array(
						'min'     => 0,
						'prepend' => '$',
					)
				),
				verbena_def( 'price_unit', __( 'Price unit', 'verbena' ), 'text', array( 'placeholder' => __( 'per session', 'verbena' ) ) ),
				verbena_def( 'duration', __( 'Appointment length', 'verbena' ) ),
				verbena_def( 'downtime', __( 'Downtime', 'verbena' ) ),
				verbena_def( 'areas_treated', __( 'Areas treated', 'verbena' ) ),
				verbena_def( 'sessions', __( 'Typical plan', 'verbena' ) ),
				verbena_def(
					'before_after_notes',
					__( 'Before & after notes', 'verbena' ),
					'textarea',
					array(
						'new_lines'    => 'wpautop',
						'instructions' => __( 'Prep and aftercare guidance shown on the treatment page.', 'verbena' ),
					)
				),
				verbena_def( 'booking_service_id', __( 'Booking service ID', 'verbena' ), 'text', array( 'instructions' => __( 'Service ID in the booking provider, appended to the booking URL.', 'verbena' ) ) ),
				verbena_def(
					'treatment_faqs',
					__( 'FAQs', 'verbena' ),
					'repeater',
					array(
						'layout'       => 'block',
						'button_label' => __( 'Add question', 'verbena' ),
						'sub_fields'   => array(
							verbena_sub( 'treatment_faqs', 'question', __( 'Question', 'verbena' ) ),
							verbena_sub(
								'treatment_faqs',
								'answer',
								__( 'Answer', 'verbena' ),
								'textarea',
								array(
									'rows'      => 3,
									'new_lines' => '',
								)
							),
						),
					)
				),
			),
		),
		array(
			'key'      => 'group_verbena_page_hero',
			'title'    => __( 'Page hero', 'verbena' ),
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
					array(
						'param'    => 'page_type',
						'operator' => '!=',
						'value'    => 'front_page',
					),
				),
			),
			'fields'   => array(
				verbena_def( 'hero_eyebrow', __( 'Eyebrow', 'verbena' ) ),
				verbena_def(
					'hero_intro',
					__( 'Intro', 'verbena' ),
					'textarea',
					array(
						'rows'      => 3,
						'new_lines' => '',
					)
				),
				verbena_def(
					'hero_image',
					__( 'Image', 'verbena' ),
					'image',
					array(
						'return_format' => 'id',
						'preview_size'  => 'medium',
					)
				),
			),
		),
		array(
			'key'      => 'group_verbena_front_page',
			'title'    => __( 'Home page sections', 'verbena' ),
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'fields'   => array(
				verbena_def( 'tab_home_hero', __( 'Hero', 'verbena' ), 'tab' ),
				verbena_def( 'home_hero_eyebrow', __( 'Eyebrow', 'verbena' ) ),
				verbena_def( 'home_hero_title', __( 'Headline', 'verbena' ) ),
				verbena_def(
					'home_hero_text',
					__( 'Intro', 'verbena' ),
					'textarea',
					array(
						'rows'      => 3,
						'new_lines' => '',
					)
				),
				verbena_def( 'home_hero_image', __( 'Image', 'verbena' ), 'image', array( 'return_format' => 'id' ) ),
				verbena_def( 'tab_home_intro', __( 'Intro & steps', 'verbena' ), 'tab' ),
				verbena_def( 'home_intro_heading', __( 'Intro heading', 'verbena' ) ),
				verbena_def( 'home_intro_text', __( 'Intro text', 'verbena' ), 'textarea', array( 'new_lines' => 'wpautop' ) ),
				verbena_def( 'home_intro_image', __( 'Intro image', 'verbena' ), 'image', array( 'return_format' => 'id' ) ),
				verbena_def(
					'home_steps',
					__( 'How it works steps', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'table',
						'max'        => 4,
						'sub_fields' => array(
							verbena_sub( 'home_steps', 'title', __( 'Title', 'verbena' ) ),
							verbena_sub(
								'home_steps',
								'text',
								__( 'Text', 'verbena' ),
								'textarea',
								array(
									'rows'      => 2,
									'new_lines' => '',
								)
							),
						),
					)
				),
				verbena_def( 'tab_home_social', __( 'Testimonials', 'verbena' ), 'tab' ),
				verbena_def(
					'home_testimonials',
					__( 'Testimonials', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'block',
						'sub_fields' => array(
							verbena_sub(
								'home_testimonials',
								'quote',
								__( 'Quote', 'verbena' ),
								'textarea',
								array(
									'rows'      => 3,
									'new_lines' => '',
								)
							),
							verbena_sub( 'home_testimonials', 'name', __( 'Name', 'verbena' ) ),
							verbena_sub( 'home_testimonials', 'treatment', __( 'Treatment', 'verbena' ) ),
						),
					)
				),
			),
		),
		array(
			'key'      => 'group_verbena_memberships',
			'title'    => __( 'Membership tiers', 'verbena' ),
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/template-memberships.php',
					),
				),
			),
			'fields'   => array(
				verbena_def(
					'membership_tiers',
					__( 'Tiers', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'block',
						'max'        => 3,
						'sub_fields' => array(
							verbena_sub( 'membership_tiers', 'name', __( 'Name', 'verbena' ) ),
							verbena_sub( 'membership_tiers', 'price', __( 'Monthly price', 'verbena' ), 'number' ),
							verbena_sub(
								'membership_tiers',
								'summary',
								__( 'Summary', 'verbena' ),
								'textarea',
								array(
									'rows'      => 2,
									'new_lines' => '',
								)
							),
							verbena_sub(
								'membership_tiers',
								'perks',
								__( 'Perks (one per line)', 'verbena' ),
								'textarea',
								array(
									'rows'      => 5,
									'new_lines' => '',
								)
							),
							verbena_sub( 'membership_tiers', 'featured', __( 'Highlight this tier', 'verbena' ), 'true_false', array( 'ui' => 1 ) ),
						),
					)
				),
				verbena_def( 'membership_terms', __( 'Membership terms', 'verbena' ), 'textarea', array( 'new_lines' => 'wpautop' ) ),
			),
		),
		array(
			'key'      => 'group_verbena_faq_page',
			'title'    => __( 'General FAQs', 'verbena' ),
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/template-faq.php',
					),
				),
			),
			'fields'   => array(
				verbena_def(
					'page_faqs',
					__( 'Questions', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'block',
						'sub_fields' => array(
							verbena_sub( 'page_faqs', 'question', __( 'Question', 'verbena' ) ),
							verbena_sub(
								'page_faqs',
								'answer',
								__( 'Answer', 'verbena' ),
								'textarea',
								array(
									'rows'      => 3,
									'new_lines' => '',
								)
							),
						),
					)
				),
			),
		),
		array(
			'key'      => 'group_verbena_seo',
			'title'    => __( 'Search & social', 'verbena' ),
			'position' => 'side',
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'treatment',
					),
				),
			),
			'fields'   => array(
				verbena_def( 'seo_title', __( 'Meta title', 'verbena' ), 'text', array( 'maxlength' => 70 ) ),
				verbena_def(
					'seo_description',
					__( 'Meta description', 'verbena' ),
					'textarea',
					array(
						'rows'      => 3,
						'maxlength' => 170,
						'new_lines' => '',
					)
				),
				verbena_def( 'seo_noindex', __( 'Hide from search engines', 'verbena' ), 'true_false', array( 'ui' => 1 ) ),
			),
		),
		array(
			'key'      => 'group_verbena_options',
			'title'    => __( 'Site settings', 'verbena' ),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'verbena-settings',
					),
				),
			),
			'fields'   => array(
				verbena_def( 'tab_contact', __( 'Contact & hours', 'verbena' ), 'tab' ),
				verbena_def( 'phone', __( 'Phone', 'verbena' ) ),
				verbena_def( 'email', __( 'Public email', 'verbena' ), 'email' ),
				verbena_def( 'address_street', __( 'Street address', 'verbena' ) ),
				verbena_def( 'address_city', __( 'City', 'verbena' ) ),
				verbena_def( 'address_region', __( 'State', 'verbena' ) ),
				verbena_def( 'address_postal', __( 'ZIP', 'verbena' ) ),
				verbena_def( 'geo_lat', __( 'Latitude', 'verbena' ) ),
				verbena_def( 'geo_lng', __( 'Longitude', 'verbena' ) ),
				verbena_def( 'price_range', __( 'Price range (schema)', 'verbena' ), 'text', array( 'placeholder' => '$$' ) ),
				verbena_def(
					'hours',
					__( 'Opening hours', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'table',
						'sub_fields' => array(
							verbena_sub( 'hours', 'label', __( 'Days (display)', 'verbena' ) ),
							verbena_sub( 'hours', 'days', __( 'Days (schema, e.g. Tu,We,Th)', 'verbena' ) ),
							verbena_sub( 'hours', 'opens', __( 'Opens (24h)', 'verbena' ) ),
							verbena_sub( 'hours', 'closes', __( 'Closes (24h)', 'verbena' ) ),
						),
					)
				),
				verbena_def( 'instagram_url', __( 'Instagram URL', 'verbena' ), 'url' ),
				verbena_def( 'facebook_url', __( 'Facebook URL', 'verbena' ), 'url' ),
				verbena_def( 'tiktok_url', __( 'TikTok URL', 'verbena' ), 'url' ),
				verbena_def( 'tab_booking', __( 'Booking', 'verbena' ), 'tab' ),
				verbena_def(
					'booking_provider',
					__( 'Booking provider', 'verbena' ),
					'select',
					array(
						'choices' => array(
							'demo'      => __( 'Demo widget (bundled)', 'verbena' ),
							'zenoti'    => 'Zenoti',
							'boulevard' => 'Boulevard',
							'vagaro'    => 'Vagaro',
							'phorest'   => 'Phorest',
							'mindbody'  => 'Mindbody',
							'other'     => __( 'Other', 'verbena' ),
						),
					)
				),
				verbena_def( 'booking_url', __( 'Booking widget URL', 'verbena' ), 'url', array( 'instructions' => __( 'Embed or online-booking URL from the provider. Leave empty to use the bundled demo widget.', 'verbena' ) ) ),
				verbena_def( 'booking_service_param', __( 'Service query parameter', 'verbena' ), 'text', array( 'instructions' => __( 'Query string key the provider uses for a pre-selected service, e.g. serviceId.', 'verbena' ) ) ),
				verbena_def( 'tab_tracking', __( 'Tracking', 'verbena' ), 'tab' ),
				verbena_def( 'gtm_id', __( 'Google Tag Manager ID', 'verbena' ), 'text', array( 'placeholder' => 'GTM-XXXXXXX' ) ),
				verbena_def(
					'ga4_id',
					__( 'GA4 measurement ID', 'verbena' ),
					'text',
					array(
						'placeholder'  => 'G-XXXXXXXXXX',
						'instructions' => __( 'Only used when no GTM ID is set.', 'verbena' ),
					)
				),
				verbena_def( 'tab_forms', __( 'Forms', 'verbena' ), 'tab' ),
				verbena_def( 'gf_form_id', __( 'Gravity Forms form ID', 'verbena' ), 'number', array( 'instructions' => __( 'If Gravity Forms is active and this is set, it replaces the built-in consultation form.', 'verbena' ) ) ),
				verbena_def( 'lead_email', __( 'Send consultation requests to', 'verbena' ), 'email' ),
				verbena_def( 'tab_team', __( 'Team', 'verbena' ), 'tab' ),
				verbena_def(
					'team',
					__( 'Providers', 'verbena' ),
					'repeater',
					array(
						'layout'     => 'block',
						'sub_fields' => array(
							verbena_sub( 'team', 'name', __( 'Name', 'verbena' ) ),
							verbena_sub( 'team', 'role', __( 'Role', 'verbena' ) ),
							verbena_sub(
								'team',
								'bio',
								__( 'Short bio', 'verbena' ),
								'textarea',
								array(
									'rows'      => 3,
									'new_lines' => '',
								)
							),
							verbena_sub( 'team', 'photo', __( 'Photo', 'verbena' ), 'image', array( 'return_format' => 'id' ) ),
						),
					)
				),
			),
		),
	);

	return $groups;
}

/**
 * Flat lookup of top-level field definitions keyed by field name.
 *
 * @param string $name Field name.
 * @return array|null
 */
function verbena_field_definition( $name ) {
	static $map = null;
	if ( null === $map ) {
		$map = array();
		foreach ( verbena_field_groups() as $group ) {
			foreach ( $group['fields'] as $field ) {
				$map[ $field['name'] ] = $field;
			}
		}
	}
	return $map[ $name ] ?? null;
}
