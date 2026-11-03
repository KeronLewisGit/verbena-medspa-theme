<?php
/**
 * Field access helpers.
 *
 * Every custom field read in the theme goes through verbena_field(). With ACF
 * Pro or Secure Custom Fields active it defers to get_field(); otherwise it
 * reads the same storage directly (post meta, or options saved with ACF's
 * "options_" prefix) so templates behave the same with or without the plugin.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether an ACF-compatible plugin (ACF Pro, ACF free or SCF) is loaded.
 *
 * @return bool
 */
function verbena_acf_active() {
	return function_exists( 'get_field' ) && function_exists( 'acf_add_local_field_group' );
}

/**
 * Read a custom field.
 *
 * @param string          $name    Field name.
 * @param int|string|null $post_id Post ID, 'option' for site settings, or null for the current post.
 * @return mixed
 */
function verbena_field( $name, $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	if ( verbena_acf_active() ) {
		return get_field( $name, $post_id );
	}

	$definition = verbena_field_definition( $name );
	$raw        = verbena_raw_value( $name, $post_id );

	if ( $definition && 'repeater' === $definition['type'] ) {
		$rows  = array();
		$count = (int) $raw;
		for ( $i = 0; $i < $count; $i++ ) {
			$row = array();
			foreach ( $definition['sub_fields'] as $sub ) {
				$row[ $sub['name'] ] = verbena_format_value( verbena_raw_value( "{$name}_{$i}_{$sub['name']}", $post_id ), $sub );
			}
			$rows[] = $row;
		}
		return $rows ? $rows : false;
	}

	return $definition ? verbena_format_value( $raw, $definition ) : $raw;
}

/**
 * Site setting shorthand.
 *
 * @param string $name    Option field name.
 * @param mixed  $fallback Value when empty.
 * @return mixed
 */
function verbena_option( $name, $fallback = '' ) {
	$value = verbena_field( $name, 'option' );
	return ( null === $value || '' === $value || false === $value ) ? $fallback : $value;
}

/**
 * Raw stored value, in ACF's storage layout.
 *
 * @param string     $meta_key Meta key / option suffix.
 * @param int|string $post_id  Post ID or 'option'.
 * @return mixed
 */
function verbena_raw_value( $meta_key, $post_id ) {
	if ( 'option' === $post_id || 'options' === $post_id ) {
		return get_option( 'options_' . $meta_key, '' );
	}
	if ( ! $post_id ) {
		return '';
	}
	return get_post_meta( (int) $post_id, $meta_key, true );
}

/**
 * Mirror the bits of ACF's format_value() the theme relies on.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function verbena_format_value( $value, $field ) {
	switch ( $field['type'] ) {
		case 'true_false':
			return (bool) $value;
		case 'number':
			return ( '' === $value || null === $value ) ? '' : 0 + $value;
		case 'image':
			return $value ? (int) $value : false;
		case 'textarea':
			if ( 'wpautop' === ( $field['new_lines'] ?? '' ) && '' !== $value ) {
				return wpautop( $value );
			}
			if ( 'br' === ( $field['new_lines'] ?? '' ) && '' !== $value ) {
				return nl2br( $value );
			}
			return $value;
		default:
			return $value;
	}
}

/**
 * Write a field in ACF's storage format (value + field-key reference).
 *
 * Used by the demo importer so the data is readable by get_field() and by the
 * fallback above, and editable in the ACF/SCF UI.
 *
 * @param string     $name    Field name.
 * @param mixed      $value   Value; repeaters take a list of associative rows.
 * @param int|string $post_id Post ID or 'option'.
 * @return void
 */
function verbena_write_field( $name, $value, $post_id ) {
	$definition = verbena_field_definition( $name );
	if ( ! $definition ) {
		return;
	}

	if ( 'repeater' === $definition['type'] ) {
		$rows = array_values( (array) $value );
		verbena_write_raw( $name, count( $rows ), $definition['key'], $post_id );
		foreach ( $rows as $i => $row ) {
			foreach ( $definition['sub_fields'] as $sub ) {
				$sub_value = $row[ $sub['name'] ] ?? '';
				if ( is_bool( $sub_value ) ) {
					$sub_value = $sub_value ? 1 : 0;
				}
				verbena_write_raw( "{$name}_{$i}_{$sub['name']}", $sub_value, $sub['key'], $post_id );
			}
		}
		return;
	}

	if ( is_bool( $value ) ) {
		$value = $value ? 1 : 0;
	}
	verbena_write_raw( $name, $value, $definition['key'], $post_id );
}

/**
 * Low-level writer for verbena_write_field().
 *
 * @param string     $meta_key  Meta key.
 * @param mixed      $value     Value.
 * @param string     $field_key ACF field key.
 * @param int|string $post_id   Post ID or 'option'.
 * @return void
 */
function verbena_write_raw( $meta_key, $value, $field_key, $post_id ) {
	if ( 'option' === $post_id ) {
		update_option( 'options_' . $meta_key, $value, false );
		update_option( '_options_' . $meta_key, $field_key, false );
		return;
	}
	update_post_meta( (int) $post_id, $meta_key, $value );
	update_post_meta( (int) $post_id, '_' . $meta_key, $field_key );
}

/**
 * Inline SVG icon from a small fixed set. Output is static markup.
 *
 * @param string $name  Icon name.
 * @param string $classes CSS classes.
 * @return string
 */
function verbena_icon( $name, $classes = 'size-5' ) {
	$paths = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'phone'    => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
		'pin'      => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h10"/>',
		'close'    => '<path d="M6 6l12 12M18 6L6 18"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'check'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="%1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		esc_attr( $classes ),
		$paths[ $name ]
	);
}

/**
 * Allowed markup for verbena_icon() output when echoing through wp_kses().
 *
 * @return array
 */
function verbena_svg_kses() {
	$common = array(
		'd'               => true,
		'cx'              => true,
		'cy'              => true,
		'r'               => true,
		'x'               => true,
		'y'               => true,
		'width'           => true,
		'height'          => true,
		'rx'              => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
	);
	return array(
		'svg'    => array_merge(
			$common,
			array(
				'class'       => true,
				'viewbox'     => true,
				'aria-hidden' => true,
				'focusable'   => true,
			)
		),
		'path'   => $common,
		'circle' => $common,
		'rect'   => $common,
	);
}

/**
 * Echo an icon.
 *
 * @param string $name  Icon name.
 * @param string $classes CSS classes.
 * @return void
 */
function verbena_the_icon( $name, $classes = 'size-5' ) {
	echo wp_kses( verbena_icon( $name, $classes ), verbena_svg_kses() );
}

/**
 * Phone number formatted for a tel: link.
 *
 * @param string $phone Display phone.
 * @return string
 */
function verbena_tel( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $phone );
	if ( $digits && '+' !== $digits[0] && 10 === strlen( $digits ) ) {
		$digits = '+1' . $digits;
	}
	return 'tel:' . $digits;
}

/**
 * One-line postal address from site settings.
 *
 * @return string
 */
function verbena_address_line() {
	$parts = array_filter(
		array(
			verbena_option( 'address_street' ),
			verbena_option( 'address_city' ),
			trim( verbena_option( 'address_region' ) . ' ' . verbena_option( 'address_postal' ) ),
		)
	);
	return implode( ', ', $parts );
}

/**
 * Format a USD price without trailing zeros.
 *
 * @param mixed $amount Amount.
 * @return string
 */
function verbena_price( $amount ) {
	if ( '' === $amount || null === $amount || false === $amount ) {
		return '';
	}
	$amount = (float) $amount;
	return '$' . ( floor( $amount ) === $amount ? number_format_i18n( $amount ) : number_format_i18n( $amount, 2 ) );
}

/**
 * Get a page ID by its template file.
 *
 * @param string $template Template path relative to the theme.
 * @return int
 */
function verbena_page_by_template( $template ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $template, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $pages ? (int) $pages[0] : 0;
}

/**
 * URL of the Book / Contact page, optionally with a treatment preselected.
 *
 * @param string $treatment_slug Treatment slug.
 * @return string
 */
function verbena_book_url( $treatment_slug = '' ) {
	$page_id = verbena_page_by_template( 'templates/template-contact.php' );
	$url     = $page_id ? get_permalink( $page_id ) : home_url( '/' );
	if ( $treatment_slug ) {
		$url = add_query_arg( 'treatment', rawurlencode( $treatment_slug ), $url );
	}
	return $url . '#book';
}
