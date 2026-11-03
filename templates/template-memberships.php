<?php
/**
 * Template Name: Memberships
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	verbena_page_hero( verbena_page_hero_args() );
	$verbena_terms = (string) verbena_field( 'membership_terms' );
	?>
	<section class="py-14 lg:py-20" aria-labelledby="tiers-heading">
		<div class="wrap">
			<h2 id="tiers-heading" class="mb-10 text-section text-ink"><?php esc_html_e( 'Choose a membership', 'verbena' ); ?></h2>
			<?php get_template_part( 'template-parts/membership-tiers', null, array( 'page_id' => get_the_ID() ) ); ?>
		</div>
	</section>

	<section class="border-t border-line bg-paper py-14 lg:py-20">
		<div class="wrap grid gap-12 lg:grid-cols-12">
			<div class="prose-verbena lg:col-span-7">
				<?php the_content(); ?>
			</div>
			<?php if ( $verbena_terms ) : ?>
				<aside class="lg:col-span-4 lg:col-start-9" aria-labelledby="terms-heading">
					<h2 id="terms-heading" class="font-body text-sm font-semibold uppercase tracking-[0.16em] text-muted"><?php esc_html_e( 'Membership terms', 'verbena' ); ?></h2>
					<div class="mt-4 text-base text-ink/75 [&_p+p]:mt-3">
						<?php echo wp_kses_post( $verbena_terms ); ?>
					</div>
				</aside>
			<?php endif; ?>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/cta-band' );
endwhile;

get_footer();
