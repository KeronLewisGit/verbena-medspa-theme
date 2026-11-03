<?php
/**
 * Built-in consultation form (used when Gravity Forms is not configured).
 * Handler: verbena_handle_consult() in inc/forms.php.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_status  = $args['status'] ?? '';
$verbena_errors  = $args['errors'] ?? array();
$verbena_old     = $args['old'] ?? array();
$verbena_choices = verbena_consult_treatment_choices();
$verbena_pre     = isset( $_GET['treatment'] ) ? sanitize_title( wp_unslash( $_GET['treatment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preselect only.
$verbena_pre     = $verbena_old['treatment'] ?? ( $args['treatment'] ?? $verbena_pre );
$verbena_source  = get_permalink();

$verbena_value       = static function ( $key ) use ( $verbena_old ) {
	return isset( $verbena_old[ $key ] ) ? (string) $verbena_old[ $key ] : '';
};
$verbena_error_attrs = static function ( $key ) use ( $verbena_errors ) {
	if ( empty( $verbena_errors[ $key ] ) ) {
		return '';
	}
	return ' aria-invalid="true" aria-describedby="consult-' . esc_attr( $key ) . '-error"';
};
$verbena_error       = static function ( $key ) use ( $verbena_errors ) {
	if ( ! empty( $verbena_errors[ $key ] ) ) {
		printf( '<p id="consult-%1$s-error" class="form-error">%2$s</p>', esc_attr( $key ), esc_html( $verbena_errors[ $key ] ) );
	}
};
?>
<div id="consult" class="rounded-brand border border-line bg-paper p-6 sm:p-8">
	<?php if ( 'sent' === $verbena_status ) : ?>
		<div class="form-alert form-alert-success" role="status" tabindex="-1" data-focus-on-load>
			<p class="font-display text-2xl text-ink"><?php esc_html_e( 'Thank you. Your request is in.', 'verbena' ); ?></p>
			<p class="mt-2"><?php esc_html_e( 'A provider will contact you within one business day. If it is urgent, please call us.', 'verbena' ); ?></p>
		</div>
		<?php
		if ( ! empty( $verbena_old['name'] ) ) {
			wp_print_inline_script_tag(
				verbena_track_js(
					'generate_lead',
					array(
						'form_id'        => 'verbena_consult',
						'form_name'      => 'Consultation request',
						'treatment_name' => (string) ( $verbena_old['treatment'] ?? '' ),
					)
				)
			);
		}
		?>
	<?php else : ?>
		<p class="font-display text-[1.75rem] leading-tight text-ink"><?php esc_html_e( 'Request a consultation', 'verbena' ); ?></p>
		<p class="mt-2 text-ink/75"><?php esc_html_e( 'Tell us what you have in mind and a provider will call or email you back.', 'verbena' ); ?></p>

		<?php if ( $verbena_errors ) : ?>
			<div class="form-alert form-alert-error mt-6" role="alert" tabindex="-1" data-focus-on-load>
				<p class="font-semibold"><?php esc_html_e( 'Please check the highlighted fields.', 'verbena' ); ?></p>
				<?php if ( ! empty( $verbena_errors['form'] ) ) : ?>
					<p class="mt-1"><?php echo esc_html( $verbena_errors['form'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form class="mt-6 grid gap-5 sm:grid-cols-2" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="verbena_consult">
			<input type="hidden" name="source" value="<?php echo esc_url( $verbena_source ); ?>">
			<input type="hidden" name="ts" value="<?php echo esc_attr( (string) time() ); ?>">
			<?php wp_nonce_field( 'verbena_consult', 'verbena_consult_nonce' ); ?>

			<div class="absolute -left-[9999px]" aria-hidden="true">
				<label for="consult-company-website"><?php esc_html_e( 'Leave this field empty', 'verbena' ); ?></label>
				<input id="consult-company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
			</div>

			<div class="grid content-start gap-2">
				<label class="form-label" for="consult-name"><?php esc_html_e( 'Name', 'verbena' ); ?> <span class="text-accent" aria-hidden="true">*</span></label>
				<input class="form-input" id="consult-name" name="name" type="text" autocomplete="name" required value="<?php echo esc_attr( $verbena_value( 'name' ) ); ?>"<?php echo $verbena_error_attrs( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>>
				<?php $verbena_error( 'name' ); ?>
			</div>
			<div class="grid content-start gap-2">
				<label class="form-label" for="consult-email"><?php esc_html_e( 'Email', 'verbena' ); ?> <span class="text-accent" aria-hidden="true">*</span></label>
				<input class="form-input" id="consult-email" name="email" type="email" autocomplete="email" required value="<?php echo esc_attr( $verbena_value( 'email' ) ); ?>"<?php echo $verbena_error_attrs( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php $verbena_error( 'email' ); ?>
			</div>
			<div class="grid content-start gap-2">
				<label class="form-label" for="consult-phone"><?php esc_html_e( 'Phone', 'verbena' ); ?> <span class="font-normal text-muted"><?php esc_html_e( '(optional)', 'verbena' ); ?></span></label>
				<input class="form-input" id="consult-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" value="<?php echo esc_attr( $verbena_value( 'phone' ) ); ?>"<?php echo $verbena_error_attrs( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php $verbena_error( 'phone' ); ?>
			</div>
			<div class="grid content-start gap-2">
				<label class="form-label" for="consult-treatment"><?php esc_html_e( 'Interested in', 'verbena' ); ?> <span class="text-accent" aria-hidden="true">*</span></label>
				<select class="form-input" id="consult-treatment" name="treatment" required<?php echo $verbena_error_attrs( 'treatment' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<option value=""><?php esc_html_e( 'Choose one', 'verbena' ); ?></option>
					<?php foreach ( $verbena_choices as $verbena_slug => $verbena_label ) : ?>
						<option value="<?php echo esc_attr( $verbena_slug ); ?>" <?php selected( $verbena_pre, $verbena_slug ); ?>><?php echo esc_html( $verbena_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php $verbena_error( 'treatment' ); ?>
			</div>

			<fieldset class="sm:col-span-2"<?php echo $verbena_error_attrs( 'contact_pref' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<legend class="form-label"><?php esc_html_e( 'Best way to reach you', 'verbena' ); ?> <span class="text-accent" aria-hidden="true">*</span></legend>
				<div class="mt-2 flex flex-wrap gap-2">
					<?php
					$verbena_prefs = array(
						'email' => __( 'Email', 'verbena' ),
						'phone' => __( 'Phone call', 'verbena' ),
						'text'  => __( 'Text message', 'verbena' ),
					);
					$verbena_pref  = $verbena_value( 'contact_pref' ) ? $verbena_value( 'contact_pref' ) : 'email';
					foreach ( $verbena_prefs as $verbena_key => $verbena_label ) :
						?>
						<label class="relative inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-full border border-ink/25 bg-bg px-4 has-[:checked]:border-ink has-[:checked]:bg-ink has-[:checked]:text-bg has-[:focus-visible]:outline has-[:focus-visible]:outline-3 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent">
							<input class="sr-only" type="radio" name="contact_pref" value="<?php echo esc_attr( $verbena_key ); ?>" <?php checked( $verbena_pref, $verbena_key ); ?>>
							<?php echo esc_html( $verbena_label ); ?>
						</label>
					<?php endforeach; ?>
				</div>
				<?php $verbena_error( 'contact_pref' ); ?>
			</fieldset>

			<div class="grid gap-2 sm:col-span-2">
				<label class="form-label" for="consult-message"><?php esc_html_e( 'Anything we should know?', 'verbena' ); ?> <span class="font-normal text-muted"><?php esc_html_e( '(optional)', 'verbena' ); ?></span></label>
				<textarea class="form-input min-h-32" id="consult-message" name="message" rows="4" maxlength="2000"<?php echo $verbena_error_attrs( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $verbena_value( 'message' ) ); ?></textarea>
				<?php $verbena_error( 'message' ); ?>
			</div>

			<div class="grid gap-2 sm:col-span-2">
				<label class="flex items-start gap-3 text-base text-ink/85">
					<input class="mt-1 size-5 shrink-0 accent-[var(--brand-accent)]" type="checkbox" name="consent" value="1" <?php checked( ! empty( $verbena_old['consent'] ) ); ?><?php echo $verbena_error_attrs( 'consent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span><?php esc_html_e( 'Verbena may contact me about this request. I understand this form is not for medical emergencies.', 'verbena' ); ?></span>
				</label>
				<?php $verbena_error( 'consent' ); ?>
			</div>

			<div class="sm:col-span-2">
				<button type="submit" class="btn btn-primary w-full sm:w-auto"><?php esc_html_e( 'Send request', 'verbena' ); ?><?php verbena_the_icon( 'arrow', 'size-4' ); ?></button>
			</div>
		</form>
	<?php endif; ?>
</div>
