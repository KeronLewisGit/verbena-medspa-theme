<?php
/**
 * Inner-page hero. Used by every non-home template so headers stay consistent.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_args      = wp_parse_args(
	$args ?? array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'intro'   => '',
		'image'   => 0,
		'facts'   => array(),
		'actions' => array(),
	)
);
$verbena_has_image = ! empty( $verbena_args['image'] );
?>
<section class="relative overflow-hidden border-b border-line">
	<div class="wrap grid items-center gap-10 py-10 md:py-14 lg:grid-cols-12 lg:gap-12 lg:py-16">
		<div class="<?php echo esc_attr( $verbena_has_image ? 'lg:col-span-7' : 'lg:col-span-9' ); ?>">
			<?php verbena_breadcrumbs( 'mb-8' ); ?>
			<?php if ( $verbena_args['eyebrow'] ) : ?>
				<p class="eyebrow"><?php echo esc_html( $verbena_args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="mt-4 text-title text-ink"><?php echo esc_html( $verbena_args['title'] ); ?></h1>
			<?php if ( $verbena_args['intro'] ) : ?>
				<p class="mt-6 max-w-[58ch] text-lg text-ink/80 lg:text-xl"><?php echo esc_html( $verbena_args['intro'] ); ?></p>
			<?php endif; ?>

			<?php if ( $verbena_args['facts'] ) : ?>
				<dl class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-brand border border-line bg-line sm:grid-cols-3 max-sm:[&>*:last-child:nth-child(odd)]:col-span-2">
					<?php foreach ( $verbena_args['facts'] as $verbena_fact ) : ?>
						<div class="bg-paper px-4 py-3">
							<dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted"><?php echo esc_html( $verbena_fact[0] ); ?></dt>
							<dd class="mt-1 font-medium text-ink"><?php echo esc_html( $verbena_fact[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $verbena_args['actions'] ) : ?>
				<div class="mt-8 flex flex-wrap gap-3">
					<?php
					foreach ( $verbena_args['actions'] as $verbena_action ) {
						echo wp_kses( $verbena_action, verbena_button_kses() );
					}
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $verbena_has_image ) : ?>
			<div class="relative lg:col-span-5">
				<div aria-hidden="true" class="absolute -bottom-3 -right-3 h-2/3 w-2/3 rounded-brand bg-blush/60 lg:-bottom-5 lg:-right-5"></div>
				<?php verbena_the_image( $verbena_args['image'], '(min-width: 1024px) 34vw, 100vw', 'relative aspect-[4/3] w-full rounded-brand object-cover', true ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
