<?php
/**
 * Filterable treatment grid. Filters are real links to the area archives;
 * assets/js/app.js upgrades them to in-page filtering.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_posts   = $args['posts'] ?? verbena_get_treatments();
$verbena_heading = $args['card_heading'] ?? 'h3';
$verbena_current = is_tax( 'treatment_area' ) ? get_queried_object()->slug : 'all';
$verbena_terms   = get_terms(
	array(
		'taxonomy'   => 'treatment_area',
		'hide_empty' => true,
	)
);
?>
<div data-treatment-filter>
	<?php if ( $verbena_terms && ! is_wp_error( $verbena_terms ) ) : ?>
		<div class="flex flex-wrap gap-2" role="group" aria-label="<?php esc_attr_e( 'Filter treatments by area', 'verbena' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'treatment' ) ); ?>" class="filter-chip" data-filter="all" aria-pressed="<?php echo esc_attr( 'all' === $verbena_current ? 'true' : 'false' ); ?>"><?php esc_html_e( 'All', 'verbena' ); ?></a>
			<?php foreach ( $verbena_terms as $verbena_term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $verbena_term ) ); ?>" class="filter-chip" data-filter="<?php echo esc_attr( $verbena_term->slug ); ?>" aria-pressed="<?php echo esc_attr( $verbena_current === $verbena_term->slug ? 'true' : 'false' ); ?>">
					<?php echo esc_html( $verbena_term->name ); ?>
					<span class="text-current/70">(<?php echo esc_html( (string) $verbena_term->count ); ?>)</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<p class="sr-only" aria-live="polite" data-filter-status></p>

	<div class="mt-10 grid gap-x-6 gap-y-12 sm:grid-cols-2 xl:grid-cols-4">
		<?php
		foreach ( $verbena_posts as $verbena_post ) {
			get_template_part(
				'template-parts/treatment-card',
				null,
				array(
					'post'    => $verbena_post,
					'heading' => $verbena_heading,
				)
			);
		}
		?>
	</div>
</div>
