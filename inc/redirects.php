<?php
/**
 * Small 301/302 redirect manager (Tools > Redirects).
 *
 * Rules are stored in one option and matched on template_redirect against the
 * request path, ignoring case and trailing slashes. Each match increments a
 * hit counter so stale rules can be found and removed after a migration.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

const VERBENA_REDIRECTS_OPTION = 'verbena_redirects';

/**
 * Stored rules.
 *
 * @return array<int, array{from:string,to:string,code:int,hits:int,last_hit:string}>
 */
function verbena_get_redirects() {
	$rules = get_option( VERBENA_REDIRECTS_OPTION, array() );
	return is_array( $rules ) ? array_values( $rules ) : array();
}

/**
 * Normalise a path for comparison: leading slash, no trailing slash, lower case.
 *
 * @param string $path Path or URL.
 * @return string
 */
function verbena_normalize_path( $path ) {
	$path = (string) wp_parse_url( $path, PHP_URL_PATH );
	$path = '/' . trim( $path, '/' );
	return strtolower( rawurldecode( $path ) );
}

/**
 * Match the current request and redirect.
 *
 * @return void
 */
function verbena_maybe_redirect() {
	if ( is_admin() ) {
		return;
	}
	$rules = verbena_get_redirects();
	if ( ! $rules ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
	$home_path   = (string) wp_parse_url( home_url(), PHP_URL_PATH );
	$path        = verbena_normalize_path( $request_uri );
	if ( $home_path && 0 === strpos( $path, strtolower( untrailingslashit( $home_path ) ) ) ) {
		$path = verbena_normalize_path( substr( $path, strlen( untrailingslashit( $home_path ) ) ) );
	}

	foreach ( $rules as $index => $rule ) {
		if ( verbena_normalize_path( $rule['from'] ) !== $path ) {
			continue;
		}
		$target = $rule['to'];
		if ( 0 === strpos( $target, '/' ) ) {
			$target = home_url( $target );
		}
		$rules[ $index ]['hits']     = (int) $rule['hits'] + 1;
		$rules[ $index ]['last_hit'] = current_time( 'mysql' );
		update_option( VERBENA_REDIRECTS_OPTION, $rules, false );

		$code = in_array( (int) $rule['code'], array( 301, 302, 307, 308 ), true ) ? (int) $rule['code'] : 301;
		// Allow off-site targets the admin entered explicitly.
		wp_redirect( esc_url_raw( $target ), $code, 'Verbena Redirects' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}
}
add_action( 'template_redirect', 'verbena_maybe_redirect', 1 );

/**
 * Tools > Redirects screen.
 *
 * @return void
 */
function verbena_redirects_menu() {
	add_management_page(
		__( 'Redirects', 'verbena' ),
		__( 'Redirects', 'verbena' ),
		'manage_options',
		'verbena-redirects',
		'verbena_render_redirects_page'
	);
}
add_action( 'admin_menu', 'verbena_redirects_menu' );

/**
 * Render the redirects screen.
 *
 * @return void
 */
function verbena_render_redirects_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$rules   = verbena_get_redirects();
	$notice  = isset( $_GET['verbena_notice'] ) ? sanitize_key( wp_unslash( $_GET['verbena_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notices = array(
		'added'   => array( 'success', __( 'Redirect added.', 'verbena' ) ),
		'deleted' => array( 'success', __( 'Redirect deleted.', 'verbena' ) ),
		'reset'   => array( 'success', __( 'Hit counters reset.', 'verbena' ) ),
		'invalid' => array( 'error', __( 'Enter a source path starting with / and a target path or full URL.', 'verbena' ) ),
		'exists'  => array( 'error', __( 'A redirect from that path already exists.', 'verbena' ) ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Redirects', 'verbena' ); ?></h1>
		<p><?php esc_html_e( 'Old URLs from a previous site, campaign short links, retired treatments. Paths are matched without query strings, case-insensitive, with or without a trailing slash.', 'verbena' ); ?></p>
		<?php if ( isset( $notices[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( $notices[ $notice ][1] ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Add redirect', 'verbena' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="verbena_redirect_add">
			<?php wp_nonce_field( 'verbena_redirect_add' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="verbena-from"><?php esc_html_e( 'From path', 'verbena' ); ?></label></th>
					<td><input class="regular-text" id="verbena-from" name="from" placeholder="/old-page/" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="verbena-to"><?php esc_html_e( 'To', 'verbena' ); ?></label></th>
					<td><input class="regular-text" id="verbena-to" name="to" placeholder="/treatments/hydrafacial/" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="verbena-code"><?php esc_html_e( 'Type', 'verbena' ); ?></label></th>
					<td>
						<select id="verbena-code" name="code">
							<option value="301"><?php esc_html_e( '301 Permanent', 'verbena' ); ?></option>
							<option value="302"><?php esc_html_e( '302 Temporary', 'verbena' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Add redirect', 'verbena' ) ); ?>
		</form>

		<h2><?php esc_html_e( 'Active redirects', 'verbena' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'From', 'verbena' ); ?></th>
					<th scope="col"><?php esc_html_e( 'To', 'verbena' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Type', 'verbena' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Hits', 'verbena' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Last hit', 'verbena' ); ?></th>
					<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'verbena' ); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $rules ) : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No redirects yet.', 'verbena' ); ?></td></tr>
				<?php endif; ?>
				<?php foreach ( $rules as $index => $rule ) : ?>
					<tr>
						<td><code><?php echo esc_html( $rule['from'] ); ?></code></td>
						<td><code><?php echo esc_html( $rule['to'] ); ?></code></td>
						<td><?php echo esc_html( (string) $rule['code'] ); ?></td>
						<td><?php echo esc_html( number_format_i18n( (int) $rule['hits'] ) ); ?></td>
						<td><?php echo esc_html( $rule['last_hit'] ? $rule['last_hit'] : '—' ); ?></td>
						<td>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="verbena_redirect_delete">
								<input type="hidden" name="index" value="<?php echo esc_attr( (string) $index ); ?>">
								<?php wp_nonce_field( 'verbena_redirect_delete' ); ?>
								<button type="submit" class="button-link button-link-delete"><?php esc_html_e( 'Delete', 'verbena' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php if ( $rules ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1em">
				<input type="hidden" name="action" value="verbena_redirect_reset">
				<?php wp_nonce_field( 'verbena_redirect_reset' ); ?>
				<?php submit_button( __( 'Reset hit counters', 'verbena' ), 'secondary', 'submit', false ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Redirect back to the screen with a notice.
 *
 * @param string $notice Notice key.
 * @return void
 */
function verbena_redirects_back( $notice ) {
	wp_safe_redirect( add_query_arg( 'verbena_notice', $notice, admin_url( 'tools.php?page=verbena-redirects' ) ) );
	exit;
}

/**
 * Add a rule.
 *
 * @return void
 */
function verbena_handle_redirect_add() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to manage redirects.', 'verbena' ), 403 );
	}
	check_admin_referer( 'verbena_redirect_add' );

	$from = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from'] ) ) : '';
	$to   = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to'] ) ) : '';
	$code = isset( $_POST['code'] ) ? absint( $_POST['code'] ) : 301;

	$added = verbena_add_redirect( $from, $to, $code );
	verbena_redirects_back( true === $added ? 'added' : $added );
}
add_action( 'admin_post_verbena_redirect_add', 'verbena_handle_redirect_add' );

/**
 * Validate and store a rule. Shared with the demo importer.
 *
 * @param string $from Source path.
 * @param string $to   Target path or URL.
 * @param int    $code HTTP status.
 * @return true|string True or an error key.
 */
function verbena_add_redirect( $from, $to, $code = 301 ) {
	$from = '/' . ltrim( trim( $from ), '/' );
	$to   = trim( $to );
	if ( '/' === $from || '' === $to || ( 0 !== strpos( $to, '/' ) && ! wp_http_validate_url( $to ) ) ) {
		return 'invalid';
	}
	if ( verbena_normalize_path( $from ) === verbena_normalize_path( $to ) && 0 === strpos( $to, '/' ) ) {
		return 'invalid';
	}

	$rules = verbena_get_redirects();
	foreach ( $rules as $rule ) {
		if ( verbena_normalize_path( $rule['from'] ) === verbena_normalize_path( $from ) ) {
			return 'exists';
		}
	}
	$rules[] = array(
		'from'     => $from,
		'to'       => 0 === strpos( $to, '/' ) ? $to : esc_url_raw( $to ),
		'code'     => 302 === (int) $code ? 302 : 301,
		'hits'     => 0,
		'last_hit' => '',
	);
	update_option( VERBENA_REDIRECTS_OPTION, $rules, false );
	return true;
}

/**
 * Delete a rule.
 *
 * @return void
 */
function verbena_handle_redirect_delete() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to manage redirects.', 'verbena' ), 403 );
	}
	check_admin_referer( 'verbena_redirect_delete' );

	$index = isset( $_POST['index'] ) ? absint( $_POST['index'] ) : -1;
	$rules = verbena_get_redirects();
	if ( isset( $rules[ $index ] ) ) {
		unset( $rules[ $index ] );
		update_option( VERBENA_REDIRECTS_OPTION, array_values( $rules ), false );
	}
	verbena_redirects_back( 'deleted' );
}
add_action( 'admin_post_verbena_redirect_delete', 'verbena_handle_redirect_delete' );

/**
 * Reset hit counters.
 *
 * @return void
 */
function verbena_handle_redirect_reset() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to manage redirects.', 'verbena' ), 403 );
	}
	check_admin_referer( 'verbena_redirect_reset' );

	$rules = verbena_get_redirects();
	foreach ( $rules as $index => $rule ) {
		$rules[ $index ]['hits']     = 0;
		$rules[ $index ]['last_hit'] = '';
	}
	update_option( VERBENA_REDIRECTS_OPTION, $rules, false );
	verbena_redirects_back( 'reset' );
}
add_action( 'admin_post_verbena_redirect_reset', 'verbena_handle_redirect_reset' );
