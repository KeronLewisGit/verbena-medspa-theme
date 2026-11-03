<?php
/**
 * Gravity Forms integration. Only hooks in when GF is active.
 *
 * - Turns off GF's own theme CSS and applies the theme's Tailwind classes to
 *   field wrappers, inputs and the submit button, so a GF form matches the
 *   built-in form exactly.
 * - Pushes a generate_lead dataLayer event from the confirmation.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

add_filter( 'gform_disable_css', '__return_true' );
add_filter( 'gform_init_scripts_footer', '__return_true' );

/**
 * Field wrapper classes.
 *
 * @param string $classes Classes.
 * @param object $field   GF_Field.
 * @return string
 */
function verbena_gf_field_classes( $classes, $field ) {
	$classes .= ' mb-5 grid gap-2';
	if ( in_array( $field->type, array( 'textarea', 'message' ), true ) ) {
		$classes .= ' sm:col-span-2';
	}
	return $classes;
}
add_filter( 'gform_field_css_class', 'verbena_gf_field_classes', 10, 2 );

/**
 * Inject Tailwind classes into inputs, selects, textareas and labels.
 *
 * @param string $content Field HTML.
 * @param object $field   GF_Field.
 * @return string
 */
function verbena_gf_field_content( $content, $field ) {
	if ( is_admin() || ! is_object( $field ) ) {
		return $content;
	}
	$input = 'form-input';
	$label = 'form-label';

	$content = preg_replace( '/<(input|select|textarea)(?![^>]*type=[\'"](?:hidden|checkbox|radio|submit)[\'"])([^>]*?)class=([\'"])/', '<$1$2class=$3' . $input . ' ', $content );
	$content = preg_replace( '/<(input|select|textarea)(?![^>]*class=)(?![^>]*type=[\'"](?:hidden|checkbox|radio|submit)[\'"])/', '<$1 class="' . $input . '"', $content );
	$content = str_replace( "class='gfield_label", "class='" . $label . ' gfield_label', $content );
	$content = str_replace( 'class="gfield_label', 'class="' . $label . ' gfield_label', $content );
	$content = str_replace( "class='gfield_description", "class='text-sm text-muted gfield_description", $content );
	$content = str_replace( 'validation_message', 'form-error validation_message', $content );
	return $content;
}
add_filter( 'gform_field_content', 'verbena_gf_field_content', 10, 2 );

/**
 * Replace the submit input with the theme button.
 *
 * @param string $button Button HTML.
 * @param array  $form   Form.
 * @return string
 */
function verbena_gf_submit_button( $button, $form ) {
	$label = ! empty( $form['button']['text'] ) ? $form['button']['text'] : __( 'Request consultation', 'verbena' );
	return sprintf(
		'<button type="submit" class="btn btn-primary w-full sm:w-auto" id="gform_submit_button_%1$d">%2$s</button>',
		absint( $form['id'] ),
		esc_html( $label )
	);
}
add_filter( 'gform_submit_button', 'verbena_gf_submit_button', 10, 2 );

/**
 * Validation summary styling.
 *
 * @param string $message Message HTML.
 * @return string
 */
function verbena_gf_validation_message( $message ) {
	return '<div class="form-alert form-alert-error" role="alert">' . wp_kses_post( wp_strip_all_tags( $message ) ) . '</div>';
}
add_filter( 'gform_validation_message', 'verbena_gf_validation_message' );

/**
 * Append the generate_lead dataLayer push to text confirmations.
 *
 * @param string|array $confirmation Confirmation.
 * @param array        $form         Form.
 * @param array        $entry        Entry.
 * @return string|array
 */
function verbena_gf_confirmation( $confirmation, $form, $entry ) {
	unset( $entry );
	if ( ! is_string( $confirmation ) ) {
		return $confirmation; // Redirect confirmations: track on the thank-you page instead.
	}
	$js = verbena_track_js(
		'generate_lead',
		array(
			'form_id'   => 'gf_' . absint( $form['id'] ),
			'form_name' => (string) $form['title'],
		)
	);
	return '<div class="form-alert form-alert-success" role="status">' . $confirmation . '</div>' . wp_get_inline_script_tag( $js );
}
add_filter( 'gform_confirmation', 'verbena_gf_confirmation', 10, 3 );
