<?php
/**
 * Treatment card.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_post    = $args['post'] ?? get_post();
$verbena_heading = $args['heading'] ?? 'h3';
$verbena_id      = $verbena_post->ID;
$verbena_area    = verbena_treatment_area_name( $verbena_id );
$verbena_price   = verbena_treatment_price_label( $verbena_id );
$verbena_length  = verbena_field( 'duration', $verbena_id );
?>
<article class="group relative flex flex-col" data-area="<?php echo esc_attr( verbena_treatment_area_slugs( $verbena_id ) ); ?>" data-treatment-card>
	<div class="relative overflow-hidden rounded-brand bg-surface">
		<?php
		verbena_the_image(
			get_post_thumbnail_id( $verbena_id ),
			'(min-width: 1280px) 300px, (min-width: 640px) 50vw, 100vw',
			'aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]',
			false,
			'medium_large'
		);
		?>
		<?php if ( $verbena_area ) : ?>
			<span class="absolute left-3 top-3 rounded-full bg-bg/90 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-ink"><?php echo esc_html( $verbena_area ); ?></span>
		<?php endif; ?>
	</div>
	<<?php echo tag_escape( $verbena_heading ); ?> class="mt-5 text-[1.625rem] leading-tight text-ink">
		<a class="card-link focus:outline-none group-hover:text-accent" href="<?php echo esc_url( get_permalink( $verbena_id ) ); ?>"><?php echo esc_html( get_the_title( $verbena_id ) ); ?></a>
	</<?php echo tag_escape( $verbena_heading ); ?>>
	<p class="mb-5 mt-2 text-base text-ink/75"><?php echo esc_html( (string) verbena_field( 'summary', $verbena_id ) ); ?></p>
	<p class="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-line pt-4 text-sm font-medium text-ink">
		<?php if ( $verbena_price ) : ?>
			<span><?php echo esc_html( $verbena_price ); ?></span>
		<?php endif; ?>
		<?php if ( $verbena_length ) : ?>
			<span class="inline-flex items-center gap-1.5 text-muted"><?php verbena_the_icon( 'clock', 'size-4' ); ?><?php echo esc_html( $verbena_length ); ?></span>
		<?php endif; ?>
	</p>
</article>
