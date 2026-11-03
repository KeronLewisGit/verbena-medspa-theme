<?php
/**
 * Closing call to action.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_treatment = $args['treatment'] ?? '';
$verbena_slug      = $args['slug'] ?? '';
?>
<section class="bg-plum text-bg" aria-labelledby="cta-heading">
	<div class="wrap grid items-center gap-8 py-16 lg:grid-cols-12 lg:py-20">
		<div class="lg:col-span-8">
			<p class="eyebrow"><?php esc_html_e( 'Complimentary consultation', 'verbena' ); ?></p>
			<h2 id="cta-heading" class="mt-4 text-section">
				<?php
				echo esc_html(
					$verbena_treatment
						/* translators: %s: treatment name. */
						? sprintf( __( 'Talk to a provider about %s.', 'verbena' ), $verbena_treatment )
						: __( 'Not sure where to start? Start with a conversation.', 'verbena' )
				);
				?>
			</h2>
			<p class="mt-4 max-w-[56ch] text-mauve"><?php esc_html_e( 'Thirty minutes, no cost and no obligation. You leave with a written plan and clear prices.', 'verbena' ); ?></p>
		</div>
		<div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
			<?php echo wp_kses( verbena_book_button( __( 'Book a consult', 'verbena' ), $verbena_treatment, $verbena_slug, 'cta_band', 'light' ), verbena_button_kses() ); ?>
		</div>
	</div>
</section>
