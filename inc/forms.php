<?php
/**
 * Consultation form.
 *
 * If Gravity Forms is active and a form ID is set in site settings, the GF
 * form is rendered (styled in inc/gravity-forms.php). Otherwise the theme's
 * own form posts to admin-post.php with a nonce, honeypot and minimum fill
 * time, validates on the server, keeps old input and errors across the
 * redirect, stores the request as a private Lead and emails the clinic.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render whichever consultation form applies.
 *
 * @param array $args Optional: heading, treatment (slug to preselect).
 * @return void
 */
function verbena_consultation_form( $args = array() ) {
	$form_id = absint( verbena_option( 'gf_form_id' ) );
	if ( $form_id && function_exists( 'gravity_form' ) ) {
		echo '<div class="verbena-gf">';
		gravity_form( $form_id, false, false, false, null, true );
		echo '</div>';
		return;
	}
	get_template_part( 'template-parts/consultation-form', null, array_merge( $args, verbena_consult_state() ) );
}

/**
 * Errors, old input or success state passed back via a short-lived token.
 *
 * @return array
 */
function verbena_consult_state() {
	$state = array(
		'status' => '',
		'errors' => array(),
		'old'    => array(),
	);
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- reading a display token, no state change beyond deleting it.
	$status = isset( $_GET['consult'] ) ? sanitize_key( wp_unslash( $_GET['consult'] ) ) : '';
	$token  = isset( $_GET['ct'] ) ? sanitize_key( wp_unslash( $_GET['ct'] ) ) : '';
	// phpcs:enable
	if ( ! $status || ! $token ) {
		return $state;
	}
	$data = get_transient( 'verbena_consult_' . $token );
	if ( ! is_array( $data ) ) {
		return $state;
	}
	delete_transient( 'verbena_consult_' . $token );

	$state['status'] = 'sent' === $status ? 'sent' : 'error';
	$state['errors'] = $data['errors'] ?? array();
	$state['old']    = $data['old'] ?? array();
	return $state;
}

/**
 * Valid "interested in" choices: treatment slugs plus "not-sure".
 *
 * @return array slug => label
 */
function verbena_consult_treatment_choices() {
	$choices = array();
	foreach ( verbena_get_treatments() as $treatment ) {
		$choices[ $treatment->post_name ] = $treatment->post_title;
	}
	$choices['not-sure'] = __( 'Not sure yet, I would like advice', 'verbena' );
	return $choices;
}

/**
 * Handle a submission.
 *
 * @return void
 */
function verbena_handle_consult() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified just below.
	$source = isset( $_POST['source'] ) ? esc_url_raw( wp_unslash( $_POST['source'] ) ) : '';
	$source = wp_validate_redirect( $source, home_url( '/' ) );
	$source = remove_query_arg( array( 'consult', 'ct' ), strtok( $source, '#' ) );

	if ( ! isset( $_POST['verbena_consult_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['verbena_consult_nonce'] ) ), 'verbena_consult' ) ) {
		wp_die(
			esc_html__( 'This form has expired. Please go back, refresh the page and try again.', 'verbena' ),
			esc_html__( 'Form expired', 'verbena' ),
			array(
				'response'  => 403,
				'back_link' => true,
			)
		);
	}

	$token = strtolower( wp_generate_password( 20, false ) );

	// Honeypot and minimum fill time: quietly pretend success for bots.
	$honeypot = isset( $_POST['company_website'] ) ? sanitize_text_field( wp_unslash( $_POST['company_website'] ) ) : '';
	$started  = isset( $_POST['ts'] ) ? absint( $_POST['ts'] ) : 0;
	if ( '' !== $honeypot || ( $started && time() - $started < 3 ) ) {
		set_transient( 'verbena_consult_' . $token, array( 'bot' => true ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect(
			add_query_arg(
				array(
					'consult' => 'sent',
					'ct'      => $token,
				),
				$source
			) . '#consult'
		);
		exit;
	}

	$old = array(
		'name'         => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
		'email'        => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'phone'        => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
		'treatment'    => isset( $_POST['treatment'] ) ? sanitize_title( wp_unslash( $_POST['treatment'] ) ) : '',
		'contact_pref' => isset( $_POST['contact_pref'] ) ? sanitize_key( wp_unslash( $_POST['contact_pref'] ) ) : '',
		'message'      => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
		'consent'      => ! empty( $_POST['consent'] ),
	);
	// phpcs:enable

	$errors  = array();
	$choices = verbena_consult_treatment_choices();

	if ( mb_strlen( $old['name'] ) < 2 || mb_strlen( $old['name'] ) > 80 ) {
		$errors['name'] = __( 'Please enter your name.', 'verbena' );
	}
	if ( ! is_email( $old['email'] ) ) {
		$errors['email'] = __( 'Please enter a valid email address.', 'verbena' );
	}
	$digits = preg_replace( '/\D/', '', $old['phone'] );
	if ( '' !== $old['phone'] && ( strlen( $digits ) < 10 || strlen( $digits ) > 15 ) ) {
		$errors['phone'] = __( 'Please enter a 10-digit phone number, or leave it blank.', 'verbena' );
	}
	if ( ! isset( $choices[ $old['treatment'] ] ) ) {
		$errors['treatment'] = __( 'Please choose a treatment, or "not sure".', 'verbena' );
	}
	if ( ! in_array( $old['contact_pref'], array( 'email', 'phone', 'text' ), true ) ) {
		$errors['contact_pref'] = __( 'How should we reach you?', 'verbena' );
	} elseif ( in_array( $old['contact_pref'], array( 'phone', 'text' ), true ) && '' === $digits ) {
		$errors['phone'] = __( 'Add a phone number so we can call or text you.', 'verbena' );
	}
	if ( mb_strlen( $old['message'] ) > 2000 ) {
		$errors['message'] = __( 'Please keep your message under 2,000 characters.', 'verbena' );
	}
	if ( ! $old['consent'] ) {
		$errors['consent'] = __( 'Please confirm we may contact you about your request.', 'verbena' );
	}

	// Light rate limit: five requests per address per ten minutes.
	$ip_key = 'verbena_consult_rl_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( ! $errors && $count >= 5 ) {
		$errors['form'] = __( 'We have received several requests from you already. Please call us instead.', 'verbena' );
	}

	if ( $errors ) {
		set_transient(
			'verbena_consult_' . $token,
			array(
				'errors' => $errors,
				'old'    => $old,
			),
			10 * MINUTE_IN_SECONDS
		);
		wp_safe_redirect(
			add_query_arg(
				array(
					'consult' => 'error',
					'ct'      => $token,
				),
				$source
			) . '#consult'
		);
		exit;
	}

	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$treatment_label = $choices[ $old['treatment'] ];
	$lead_id         = wp_insert_post(
		array(
			'post_type'   => 'verbena_lead',
			'post_status' => 'private',
			/* translators: 1: name, 2: treatment. */
			'post_title'  => sprintf( __( '%1$s: %2$s', 'verbena' ), $old['name'], $treatment_label ),
		),
		true
	);
	if ( ! is_wp_error( $lead_id ) ) {
		update_post_meta( $lead_id, '_verbena_email', $old['email'] );
		update_post_meta( $lead_id, '_verbena_phone', $old['phone'] );
		update_post_meta( $lead_id, '_verbena_treatment', $treatment_label );
		update_post_meta( $lead_id, '_verbena_contact_pref', $old['contact_pref'] );
		update_post_meta( $lead_id, '_verbena_message', $old['message'] );
		update_post_meta( $lead_id, '_verbena_source', $source );
	}

	$to = sanitize_email( (string) verbena_option( 'lead_email' ) );
	$to = $to ? $to : get_option( 'admin_email' );
	/* translators: %s: name. */
	$subject = sprintf( __( 'New consultation request from %s', 'verbena' ), $old['name'] );
	$body    = implode(
		"\n",
		array(
			__( 'Name:', 'verbena' ) . ' ' . $old['name'],
			__( 'Email:', 'verbena' ) . ' ' . $old['email'],
			__( 'Phone:', 'verbena' ) . ' ' . $old['phone'],
			__( 'Interested in:', 'verbena' ) . ' ' . $treatment_label,
			__( 'Preferred contact:', 'verbena' ) . ' ' . $old['contact_pref'],
			'',
			$old['message'],
			'',
			__( 'Sent from:', 'verbena' ) . ' ' . $source,
			is_wp_error( $lead_id ) ? '' : __( 'View in admin:', 'verbena' ) . ' ' . admin_url( 'post.php?action=edit&post=' . $lead_id ),
		)
	);
	wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $old['name'] . ' <' . $old['email'] . '>' ) );

	set_transient(
		'verbena_consult_' . $token,
		array(
			'old' => array(
				'name'      => $old['name'],
				'treatment' => $treatment_label,
			),
		),
		10 * MINUTE_IN_SECONDS
	);
	wp_safe_redirect(
		add_query_arg(
			array(
				'consult' => 'sent',
				'ct'      => $token,
			),
			$source
		) . '#consult'
	);
	exit;
}
add_action( 'admin_post_nopriv_verbena_consult', 'verbena_handle_consult' );
add_action( 'admin_post_verbena_consult', 'verbena_handle_consult' );

/**
 * Keep pages with the consultation form out of full-page caches. The form
 * carries a nonce, and a cached copy would go stale after a day.
 * DONOTCACHEPAGE is honoured by LiteSpeed Cache, WP Super Cache, W3TC and others.
 *
 * @return void
 */
function verbena_consult_page_no_cache() {
	if ( ! is_page_template( 'templates/template-contact.php' ) ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- shared cache-plugin convention.
	}
	header( 'X-LiteSpeed-Cache-Control: no-cache' );
	nocache_headers();
}
add_action( 'template_redirect', 'verbena_consult_page_no_cache' );
