<?php
/**
 * Native settings screen used only when no ACF-compatible plugin provides the
 * options page. Values are stored under the same option names ACF uses
 * ("options_{name}"), so switching ACF/SCF on later keeps every value.
 *
 * Repeater settings (hours, team) need ACF/SCF to edit; they are listed
 * read-only here.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the fallback page if ACF options pages are unavailable.
 *
 * @return void
 */
function verbena_fallback_settings_menu() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	add_menu_page(
		__( 'Verbena site settings', 'verbena' ),
		__( 'Site settings', 'verbena' ),
		'manage_options',
		'verbena-settings',
		'verbena_render_fallback_settings',
		'dashicons-admin-generic',
		59
	);
}
add_action( 'admin_menu', 'verbena_fallback_settings_menu' );

/**
 * Option fields from the schema.
 *
 * @return array
 */
function verbena_option_fields() {
	foreach ( verbena_field_groups() as $group ) {
		if ( 'group_verbena_options' === $group['key'] ) {
			return $group['fields'];
		}
	}
	return array();
}

/**
 * Render the fallback settings form.
 *
 * @return void
 */
function verbena_render_fallback_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Verbena site settings', 'verbena' ); ?></h1>
		<p><?php esc_html_e( 'Install ACF Pro or Secure Custom Fields for the full field UI. These values are shared with it.', 'verbena' ); ?></p>
		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Settings saved.', 'verbena' ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="verbena_save_settings">
			<?php wp_nonce_field( 'verbena_save_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php
				foreach ( verbena_option_fields() as $field ) :
					if ( 'tab' === $field['type'] ) {
						printf( '<tr><th colspan="2"><h2 style="margin:1em 0 0">%s</h2></th></tr>', esc_html( $field['label'] ) );
						continue;
					}
					$id    = 'verbena-' . $field['name'];
					$value = get_option( 'options_' . $field['name'], '' );
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php if ( 'repeater' === $field['type'] ) : ?>
								<?php
								$rows = verbena_field( $field['name'], 'option' );
								/* translators: %d: number of rows. */
								printf( esc_html__( '%d rows (edit with ACF/SCF).', 'verbena' ), is_array( $rows ) ? count( $rows ) : 0 );
								?>
							<?php elseif ( 'select' === $field['type'] ) : ?>
								<select id="<?php echo esc_attr( $id ); ?>" name="verbena[<?php echo esc_attr( $field['name'] ); ?>]">
									<?php foreach ( $field['choices'] as $choice => $label ) : ?>
										<option value="<?php echo esc_attr( $choice ); ?>" <?php selected( $value, $choice ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<input class="regular-text" id="<?php echo esc_attr( $id ); ?>" name="verbena[<?php echo esc_attr( $field['name'] ); ?>]" type="<?php echo esc_attr( in_array( $field['type'], array( 'email', 'url', 'number' ), true ) ? $field['type'] : 'text' ); ?>" value="<?php echo esc_attr( (string) $value ); ?>">
							<?php endif; ?>
							<?php if ( ! empty( $field['instructions'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['instructions'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Save handler for the fallback settings form.
 *
 * @return void
 */
function verbena_save_fallback_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change these settings.', 'verbena' ), 403 );
	}
	check_admin_referer( 'verbena_save_settings' );

	$input = isset( $_POST['verbena'] ) && is_array( $_POST['verbena'] ) ? wp_unslash( $_POST['verbena'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	foreach ( verbena_option_fields() as $field ) {
		if ( in_array( $field['type'], array( 'tab', 'repeater' ), true ) || ! array_key_exists( $field['name'], $input ) ) {
			continue;
		}
		$raw = (string) $input[ $field['name'] ];
		switch ( $field['type'] ) {
			case 'email':
				$value = sanitize_email( $raw );
				break;
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			case 'number':
				$value = '' === $raw ? '' : absint( $raw );
				break;
			case 'select':
				$value = array_key_exists( $raw, $field['choices'] ) ? $raw : '';
				break;
			default:
				$value = sanitize_text_field( $raw );
		}
		verbena_write_field( $field['name'], $value, 'option' );
	}

	wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=verbena-settings' ) ) );
	exit;
}
add_action( 'admin_post_verbena_save_settings', 'verbena_save_fallback_settings' );
