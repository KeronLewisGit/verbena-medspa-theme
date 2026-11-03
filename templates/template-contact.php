<?php
/**
 * Template Name: Book / Contact
 *
 * Page content holds the [verbena_booking] shortcode; the consultation form
 * is Gravity Forms when configured, otherwise the built-in form.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	verbena_page_hero( verbena_page_hero_args() );
	?>
	<section class="py-14 lg:py-20">
		<div class="wrap grid gap-12 lg:grid-cols-12">
			<div class="lg:col-span-6">
				<h2 class="text-section text-ink"><?php esc_html_e( 'Book online', 'verbena' ); ?></h2>
				<div class="prose-verbena mt-5 [&_#book]:mt-8">
					<?php the_content(); ?>
				</div>
			</div>
			<div class="lg:col-span-6">
				<h2 class="text-section text-ink"><?php esc_html_e( 'Prefer to talk first?', 'verbena' ); ?></h2>
				<p class="mb-8 mt-5 text-ink/80"><?php esc_html_e( 'Send a request and a provider will call, text or email within one business day.', 'verbena' ); ?></p>
				<?php verbena_consultation_form(); ?>
			</div>
		</div>
	</section>
	<div class="border-t border-line bg-paper">
		<?php get_template_part( 'template-parts/visit' ); ?>
	</div>
	<?php
endwhile;

get_footer();
