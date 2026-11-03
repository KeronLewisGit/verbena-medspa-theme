<?php
/**
 * Template Name: FAQ
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	verbena_page_hero( verbena_page_hero_args() );
	$verbena_faqs = verbena_field( 'page_faqs' );
	?>
	<section class="py-14 lg:py-20">
		<div class="wrap grid gap-12 lg:grid-cols-12">
			<div class="lg:col-span-8">
				<?php verbena_faq_accordion( $verbena_faqs, __( 'Booking, pricing and visits', 'verbena' ), 'faq' ); ?>
				<?php if ( get_the_content() ) : ?>
					<div class="prose-verbena mt-12"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
			<aside class="lg:col-span-4" aria-labelledby="faq-treatments-heading">
				<div class="rounded-brand bg-surface p-7 lg:sticky lg:top-28">
					<h2 id="faq-treatments-heading" class="text-2xl text-ink"><?php esc_html_e( 'Questions about a treatment?', 'verbena' ); ?></h2>
					<p class="mt-2 text-base text-ink/75"><?php esc_html_e( 'Each treatment page has its own FAQ, prep and aftercare notes.', 'verbena' ); ?></p>
					<ul class="mt-5 grid">
						<?php foreach ( verbena_get_treatments() as $verbena_t ) : ?>
							<li><a class="flex min-h-11 items-center justify-between border-b border-ink/10 text-ink hover:text-accent" href="<?php echo esc_url( get_permalink( $verbena_t ) . '#faq' ); ?>"><?php echo esc_html( get_the_title( $verbena_t ) ); ?><?php verbena_the_icon( 'arrow', 'size-4' ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</aside>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/cta-band' );
endwhile;

get_footer();
