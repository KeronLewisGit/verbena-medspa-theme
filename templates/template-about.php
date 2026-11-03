<?php
/**
 * Template Name: About
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
			<div class="prose-verbena lg:col-span-7">
				<?php the_content(); ?>
			</div>
			<figure class="lg:col-span-4 lg:col-start-9">
				<?php
				$verbena_side = verbena_demo_image_id( 'editorial-light' );
				verbena_the_image( $verbena_side, '(min-width: 1024px) 30vw, 100vw', 'aspect-[3/4] w-full rounded-brand object-cover', false, 'large' );
				?>
				<figcaption class="mt-3 text-sm text-muted"><?php esc_html_e( 'Treatment plans are reviewed by our medical director.', 'verbena' ); ?></figcaption>
			</figure>
		</div>
	</section>
	<div class="border-t border-line bg-paper">
		<?php get_template_part( 'template-parts/team' ); ?>
	</div>
	<?php
	get_template_part( 'template-parts/visit' );
	get_template_part( 'template-parts/cta-band' );
endwhile;

get_footer();
