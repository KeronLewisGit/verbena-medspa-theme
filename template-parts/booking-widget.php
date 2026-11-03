<?php
/**
 * Click-to-load booking widget facade.
 *
 * No third-party script or iframe is requested until the visitor clicks. The
 * button carries the iframe URL; assets/js/app.js swaps the facade for the
 * iframe and pushes a book_now_click event.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_src       = $args['src'] ?? '';
$verbena_height    = (int) ( $args['height'] ?? 760 );
$verbena_treatment = $args['treatment_name'] ?? '';
$verbena_provider  = $args['provider'] ?? '';
$verbena_title     = $args['title'] ? $args['title'] : __( 'Choose a time that suits you', 'verbena' );
?>
<div id="book" class="not-prose relative overflow-hidden rounded-brand border border-line bg-paper" data-facade data-facade-kind="booking">
	<div class="grid gap-6 p-6 sm:p-8" data-facade-placeholder>
		<div class="flex items-start gap-4">
			<span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-accent text-accent-ink"><?php verbena_the_icon( 'calendar', 'size-6' ); ?></span>
			<div>
				<p class="text-sm font-semibold uppercase tracking-[0.14em] text-muted">
					<?php
					/* translators: %s: booking provider name. */
					echo esc_html( sprintf( __( 'Online booking · %s', 'verbena' ), $verbena_provider ) );
					?>
				</p>
				<p class="mt-1 font-display text-2xl leading-tight text-ink md:text-[1.75rem]"><?php echo esc_html( $verbena_title ); ?></p>
				<?php if ( $verbena_treatment ) : ?>
					<p class="mt-2 text-ink/80">
						<?php
						/* translators: %s: treatment name. */
						echo esc_html( sprintf( __( 'Preselected: %s', 'verbena' ), $verbena_treatment ) );
						?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<ul class="grid gap-2 text-[0.98rem] text-ink/80 sm:grid-cols-3">
			<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Live availability', 'verbena' ); ?></li>
			<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Instant confirmation', 'verbena' ); ?></li>
			<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Free to reschedule', 'verbena' ); ?></li>
		</ul>

		<div>
			<button type="button" class="btn btn-primary w-full sm:w-auto" data-facade-load data-src="<?php echo esc_url( $verbena_src ); ?>" data-height="<?php echo esc_attr( (string) $verbena_height ); ?>" data-title="<?php echo esc_attr( __( 'Online booking calendar', 'verbena' ) ); ?>" data-treatment="<?php echo esc_attr( $verbena_treatment ); ?>">
				<?php esc_html_e( 'Load booking calendar', 'verbena' ); ?>
				<?php verbena_the_icon( 'arrow', 'size-4' ); ?>
			</button>
			<p class="mt-3 text-sm text-muted"><?php esc_html_e( 'The calendar loads from our booking provider when you click. Nothing third-party loads before that.', 'verbena' ); ?></p>
			<noscript>
				<p class="mt-3"><a class="font-medium text-accent underline underline-offset-4" href="<?php echo esc_url( $verbena_src ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open the booking calendar in a new tab', 'verbena' ); ?></a></p>
			</noscript>
		</div>
	</div>
</div>
