<?php
/**
 * FAQ accordion using native <details>. Questions are H3s under the section H2.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_faqs    = $args['faqs'] ?? array();
$verbena_heading = $args['heading'] ?? '';
$verbena_id      = $args['id'] ?? 'faq';
?>
<div id="<?php echo esc_attr( $verbena_id ); ?>">
	<?php if ( $verbena_heading ) : ?>
		<h2 class="text-section text-ink"><?php echo esc_html( $verbena_heading ); ?></h2>
	<?php endif; ?>
	<div class="<?php echo esc_attr( $verbena_heading ? 'mt-8' : '' ); ?> divide-y divide-line border-y border-line">
		<?php foreach ( $verbena_faqs as $verbena_i => $verbena_faq ) : ?>
			<?php
			if ( empty( $verbena_faq['question'] ) ) {
				continue;
			}
			?>
			<details class="group" <?php echo 0 === $verbena_i ? 'open' : ''; ?>>
				<summary class="flex min-h-14 items-center justify-between gap-6 py-5 text-left">
					<h3 class="font-display text-xl leading-snug text-ink md:text-[1.5rem]"><?php echo esc_html( $verbena_faq['question'] ); ?></h3>
					<span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-ink/20 text-ink transition-transform duration-200 group-open:rotate-45"><?php verbena_the_icon( 'plus', 'size-5' ); ?></span>
				</summary>
				<div class="measure pb-6 pr-12 text-ink/80">
					<?php echo wp_kses_post( wpautop( $verbena_faq['answer'] ) ); ?>
				</div>
			</details>
		<?php endforeach; ?>
	</div>
</div>
