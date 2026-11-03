<?php
/**
 * Single treatment.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$verbena_id    = get_the_ID();
	$verbena_slug  = get_post_field( 'post_name' );
	$verbena_title = get_the_title();
	$verbena_price = verbena_treatment_price_label( $verbena_id );
	$verbena_faqs  = verbena_field( 'treatment_faqs', $verbena_id );
	$verbena_notes = (string) verbena_field( 'before_after_notes', $verbena_id );

	$verbena_facts = array_values(
		array_filter(
			array(
				array( __( 'Price', 'verbena' ), $verbena_price ),
				array( __( 'Appointment', 'verbena' ), (string) verbena_field( 'duration', $verbena_id ) ),
				array( __( 'Downtime', 'verbena' ), (string) verbena_field( 'downtime', $verbena_id ) ),
			),
			static function ( $fact ) {
				return '' !== $fact[1];
			}
		)
	);

	verbena_page_hero(
		array(
			'eyebrow' => verbena_treatment_area_name( $verbena_id ),
			'title'   => $verbena_title,
			'intro'   => (string) verbena_field( 'summary', $verbena_id ),
			'image'   => (int) get_post_thumbnail_id(),
			'facts'   => $verbena_facts,
			'actions' => array(
				verbena_book_button( __( 'Book this treatment', 'verbena' ), $verbena_title, $verbena_slug, 'treatment_hero' ),
				'<a class="btn btn-ghost" href="#faq">' . esc_html__( 'Read the FAQ', 'verbena' ) . '</a>',
			),
		)
	);
	?>

	<div class="wrap grid gap-12 py-14 lg:grid-cols-12 lg:py-20">
		<article class="lg:col-span-7">
			<div class="prose-verbena">
				<?php the_content(); ?>
			</div>

			<?php if ( $verbena_notes ) : ?>
				<section class="mt-14 rounded-brand bg-surface p-7 lg:p-9" aria-labelledby="notes-heading">
					<h2 id="notes-heading" class="text-[2rem] leading-tight text-ink"><?php esc_html_e( 'Before and after your appointment', 'verbena' ); ?></h2>
					<div class="prose-verbena mt-5">
						<?php echo wp_kses_post( $verbena_notes ); ?>
					</div>
				</section>
			<?php endif; ?>

			<p class="mt-8 border-l-2 border-accent pl-4 text-base text-muted"><?php esc_html_e( 'Results vary from person to person. Every treatment starts with a consultation with a licensed provider, who will confirm whether it is right for you. This page is general information, not medical advice.', 'verbena' ); ?></p>
		</article>

		<aside class="lg:col-span-4 lg:col-start-9" aria-label="<?php esc_attr_e( 'Treatment summary', 'verbena' ); ?>">
			<div class="rounded-brand border border-line bg-paper p-7 lg:sticky lg:top-28">
				<p class="font-display text-2xl text-ink"><?php esc_html_e( 'At a glance', 'verbena' ); ?></p>
				<dl class="mt-4 divide-y divide-line">
					<?php
					$verbena_glance = array(
						__( 'Starting price', 'verbena' ) => $verbena_price,
						__( 'Areas treated', 'verbena' )  => (string) verbena_field( 'areas_treated', $verbena_id ),
						__( 'Typical plan', 'verbena' )   => (string) verbena_field( 'sessions', $verbena_id ),
						__( 'Appointment', 'verbena' )    => (string) verbena_field( 'duration', $verbena_id ),
						__( 'Downtime', 'verbena' )       => (string) verbena_field( 'downtime', $verbena_id ),
					);
					foreach ( array_filter( $verbena_glance ) as $verbena_label => $verbena_value ) :
						?>
						<div class="py-3">
							<dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted"><?php echo esc_html( $verbena_label ); ?></dt>
							<dd class="mt-1 text-ink"><?php echo esc_html( $verbena_value ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
				<div class="mt-6 grid gap-3">
					<?php echo wp_kses( verbena_book_button( __( 'Book this treatment', 'verbena' ), $verbena_title, $verbena_slug, 'treatment_sidebar' ), verbena_button_kses() ); ?>
					<?php if ( verbena_option( 'phone' ) ) : ?>
						<a class="btn btn-ghost" href="<?php echo esc_url( verbena_tel( verbena_option( 'phone' ) ) ); ?>" data-location="treatment_sidebar"><?php verbena_the_icon( 'phone', 'size-4' ); ?><?php echo esc_html( verbena_option( 'phone' ) ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</aside>
	</div>

	<?php if ( is_array( $verbena_faqs ) && $verbena_faqs ) : ?>
		<section class="border-t border-line bg-paper py-14 lg:py-20">
			<div class="wrap max-w-4xl">
				<?php
				/* translators: %s: treatment name. */
				verbena_faq_accordion( $verbena_faqs, sprintf( __( '%s: common questions', 'verbena' ), $verbena_title ), 'faq' );
				?>
			</div>
		</section>
	<?php endif; ?>

	<section class="py-14 lg:py-20" aria-labelledby="booking-heading">
		<div class="wrap max-w-4xl">
			<p class="eyebrow"><?php esc_html_e( 'Book online', 'verbena' ); ?></p>
			<h2 id="booking-heading" class="mb-8 mt-4 text-section text-ink">
				<?php
				/* translators: %s: treatment name. */
				echo esc_html( sprintf( __( 'Book %s', 'verbena' ), $verbena_title ) );
				?>
			</h2>
			<?php echo do_shortcode( '[verbena_booking treatment="' . esc_attr( $verbena_slug ) . '"]' ); ?>
		</div>
	</section>

	<?php
	$verbena_terms   = wp_get_post_terms( $verbena_id, 'treatment_area', array( 'fields' => 'ids' ) );
	$verbena_related = get_posts(
		array(
			'post_type'      => 'treatment',
			'posts_per_page' => 3,
			'post__not_in'   => array( $verbena_id ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- three posts from a small taxonomy.
			'tax_query'      => $verbena_terms ? array(
				array(
					'taxonomy' => 'treatment_area',
					'terms'    => $verbena_terms,
				),
			) : array(),
		)
	);
	if ( count( $verbena_related ) < 3 ) {
		$verbena_related = array_merge(
			$verbena_related,
			get_posts(
				array(
					'post_type'      => 'treatment',
					'posts_per_page' => 3 - count( $verbena_related ),
					'post__not_in'   => array_merge( array( $verbena_id ), wp_list_pluck( $verbena_related, 'ID' ) ),
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				)
			)
		);
	}
	if ( $verbena_related ) :
		?>
		<section class="border-t border-line py-14 lg:py-20" aria-labelledby="related-heading">
			<div class="wrap">
				<h2 id="related-heading" class="text-section text-ink"><?php esc_html_e( 'Often paired with', 'verbena' ); ?></h2>
				<div class="mt-10 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
					<?php
					foreach ( $verbena_related as $verbena_post ) {
						get_template_part( 'template-parts/treatment-card', null, array( 'post' => $verbena_post ) );
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/cta-band',
		null,
		array(
			'treatment' => $verbena_title,
			'slug'      => $verbena_slug,
		)
	);
endwhile;

get_footer();
