<?php
/**
 * Provider team grid (site settings > Team).
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_team = verbena_field( 'team', 'option' );
if ( ! is_array( $verbena_team ) || ! $verbena_team ) {
	return;
}
$verbena_heading_tag = $args['heading_tag'] ?? 'h2';
?>
<section class="py-16 lg:py-24" aria-labelledby="team-heading">
	<div class="wrap">
		<div class="grid gap-6 lg:grid-cols-12 lg:items-end">
			<div class="lg:col-span-7">
				<p class="eyebrow"><?php esc_html_e( 'Your providers', 'verbena' ); ?></p>
				<<?php echo tag_escape( $verbena_heading_tag ); ?> id="team-heading" class="mt-4 text-section text-ink"><?php esc_html_e( 'Licensed for exactly what they do', 'verbena' ); ?></<?php echo tag_escape( $verbena_heading_tag ); ?>>
			</div>
			<p class="text-ink/75 lg:col-span-5"><?php esc_html_e( 'Every treatment plan is reviewed by our medical director. You will see the same provider at each visit wherever possible.', 'verbena' ); ?></p>
		</div>
		<ul class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $verbena_team as $verbena_member ) : ?>
				<li>
					<?php verbena_the_image( (int) $verbena_member['photo'], '(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw', 'aspect-[4/5] w-full rounded-brand object-cover object-top', false, 'medium_large' ); ?>
					<h3 class="mt-5 text-2xl text-ink"><?php echo esc_html( $verbena_member['name'] ); ?></h3>
					<p class="mt-1 text-sm font-semibold uppercase tracking-[0.14em] text-accent"><?php echo esc_html( $verbena_member['role'] ); ?></p>
					<p class="mt-3 text-base text-ink/75"><?php echo esc_html( $verbena_member['bio'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
