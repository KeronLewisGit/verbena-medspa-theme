<?php
/**
 * Home page. Every section is driven by the "Home page sections" field group.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

$verbena_home     = (int) get_option( 'page_on_front' );
$verbena_home     = $verbena_home ? $verbena_home : get_the_ID();
$verbena_hero_img = (int) verbena_field( 'home_hero_image', $verbena_home );
$verbena_steps    = verbena_field( 'home_steps', $verbena_home );
$verbena_quotes   = verbena_field( 'home_testimonials', $verbena_home );
$verbena_faq_page = verbena_page_by_template( 'templates/template-faq.php' );
$verbena_faqs     = $verbena_faq_page ? verbena_field( 'page_faqs', $verbena_faq_page ) : array();
$verbena_hours    = verbena_hours();
$verbena_mem_page = verbena_page_by_template( 'templates/template-memberships.php' );
$verbena_treats   = verbena_get_treatments();
?>

<section class="relative overflow-hidden">
	<div class="wrap grid items-center gap-10 pb-14 pt-8 md:pt-12 lg:grid-cols-12 lg:gap-8 lg:pb-24 lg:pt-16">
		<div class="lg:col-span-6 xl:col-span-6">
			<p class="eyebrow"><?php echo esc_html( (string) verbena_field( 'home_hero_eyebrow', $verbena_home ) ); ?></p>
			<h1 class="mt-5 text-display text-ink"><?php echo esc_html( (string) verbena_field( 'home_hero_title', $verbena_home ) ); ?></h1>
			<p class="mt-6 max-w-[46ch] text-lg text-ink/80 lg:text-xl"><?php echo esc_html( (string) verbena_field( 'home_hero_text', $verbena_home ) ); ?></p>
			<div class="mt-8 flex flex-wrap gap-3">
				<?php echo wp_kses( verbena_book_button( __( 'Book a free consult', 'verbena' ), '', '', 'home_hero' ), verbena_button_kses() ); ?>
				<a class="btn btn-ghost" href="<?php echo esc_url( get_post_type_archive_link( 'treatment' ) ); ?>"><?php esc_html_e( 'View treatments', 'verbena' ); ?></a>
			</div>
			<ul class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-[0.95rem] text-ink/75">
				<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Physician-led', 'verbena' ); ?></li>
				<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Prices published', 'verbena' ); ?></li>
				<li class="flex items-center gap-2"><?php verbena_the_icon( 'check', 'size-4 text-accent' ); ?><?php esc_html_e( 'Free consultations', 'verbena' ); ?></li>
			</ul>
		</div>

		<div class="relative lg:col-span-6 xl:col-span-6">
			<div aria-hidden="true" class="absolute -right-5 top-10 hidden h-[85%] w-1/2 rounded-brand bg-blush/70 sm:block lg:-right-8"></div>
			<?php verbena_the_image( $verbena_hero_img, '(min-width: 1024px) 46vw, 100vw', 'relative aspect-[5/4] w-full rounded-brand object-cover object-[50%_35%] lg:aspect-[4/5]', true, 'large' ); ?>
			<?php if ( $verbena_hours ) : ?>
				<div class="absolute -bottom-6 left-4 right-4 rounded-brand bg-bg p-5 shadow-[0_18px_40px_-20px_rgba(43,26,47,0.45)] sm:left-auto sm:right-8 sm:w-72">
					<p class="flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.14em] text-muted"><?php verbena_the_icon( 'clock', 'size-4' ); ?><?php esc_html_e( 'Hours', 'verbena' ); ?></p>
					<dl class="mt-2 grid gap-1 text-[0.95rem]">
						<?php foreach ( array_slice( $verbena_hours, 0, 2 ) as $verbena_row ) : ?>
							<div class="flex justify-between gap-3"><dt class="text-ink/75"><?php echo esc_html( $verbena_row['label'] ); ?></dt><dd class="font-medium text-ink"><?php echo esc_html( verbena_time_label( $verbena_row['opens'] ) . '–' . verbena_time_label( $verbena_row['closes'] ) ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="mt-6 bg-ink py-6 text-bg lg:mt-0" aria-label="<?php esc_attr_e( 'Treatment menu', 'verbena' ); ?>">
	<ul class="wrap flex flex-wrap items-center justify-center gap-x-8 gap-y-1 font-display text-xl text-bg/90 md:text-2xl">
		<?php foreach ( $verbena_treats as $verbena_t ) : ?>
			<li class="flex items-center gap-3">
				<span aria-hidden="true" class="size-1.5 rotate-45 bg-blush"></span>
				<a class="inline-flex min-h-11 items-center hover:text-blush" href="<?php echo esc_url( get_permalink( $verbena_t ) ); ?>"><?php echo esc_html( get_the_title( $verbena_t ) ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

<section class="py-16 lg:py-28" aria-labelledby="intro-heading">
	<div class="wrap grid items-center gap-12 lg:grid-cols-12">
		<div class="order-2 lg:order-1 lg:col-span-5">
			<?php verbena_the_image( (int) verbena_field( 'home_intro_image', $verbena_home ), '(min-width: 1024px) 38vw, 100vw', 'aspect-[4/5] w-full rounded-brand object-cover', false, 'large' ); ?>
		</div>
		<div class="order-1 lg:order-2 lg:col-span-6 lg:col-start-7">
			<p class="eyebrow"><?php esc_html_e( 'The Verbena approach', 'verbena' ); ?></p>
			<h2 id="intro-heading" class="mt-4 text-section text-ink"><?php echo esc_html( (string) verbena_field( 'home_intro_heading', $verbena_home ) ); ?></h2>
			<div class="prose-verbena mt-6 text-lg">
				<?php echo wp_kses_post( (string) verbena_field( 'home_intro_text', $verbena_home ) ); ?>
			</div>
			<?php if ( is_array( $verbena_steps ) && $verbena_steps ) : ?>
				<ol class="mt-10 grid gap-6 sm:grid-cols-3">
					<?php foreach ( $verbena_steps as $verbena_i => $verbena_step ) : ?>
						<li class="border-t border-ink pt-4">
							<span class="font-display text-4xl text-accent"><?php echo esc_html( sprintf( '%02d', $verbena_i + 1 ) ); ?></span>
							<h3 class="mt-2 text-2xl text-ink"><?php echo esc_html( $verbena_step['title'] ); ?></h3>
							<p class="mt-2 text-base text-ink/75"><?php echo esc_html( $verbena_step['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="border-t border-line bg-paper py-16 lg:py-24" aria-labelledby="treatments-heading">
	<div class="wrap">
		<div class="mb-10 grid gap-6 lg:grid-cols-12 lg:items-end">
			<div class="lg:col-span-7">
				<p class="eyebrow"><?php esc_html_e( 'Treatments', 'verbena' ); ?></p>
				<h2 id="treatments-heading" class="mt-4 text-section text-ink"><?php esc_html_e( 'A short menu, done well', 'verbena' ); ?></h2>
			</div>
			<p class="text-ink/75 lg:col-span-5"><?php esc_html_e( 'Starting prices and downtime are listed for every treatment. Your consultation confirms the exact plan.', 'verbena' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/treatment-grid', null, array( 'posts' => $verbena_treats ) ); ?>
	</div>
</section>

<?php if ( $verbena_mem_page ) : ?>
	<section class="py-16 lg:py-24" aria-labelledby="membership-heading">
		<div class="wrap">
			<div class="mb-10 grid gap-6 lg:grid-cols-12 lg:items-end">
				<div class="lg:col-span-7">
					<p class="eyebrow"><?php esc_html_e( 'Memberships', 'verbena' ); ?></p>
					<h2 id="membership-heading" class="mt-4 text-section text-ink"><?php esc_html_e( 'For clients who treat regularly', 'verbena' ); ?></h2>
				</div>
				<p class="text-ink/75 lg:col-span-5">
					<?php esc_html_e( 'Monthly credit, member pricing and no long contract.', 'verbena' ); ?>
					<a class="font-medium text-accent underline underline-offset-4 hover:text-ink" href="<?php echo esc_url( get_permalink( $verbena_mem_page ) ); ?>"><?php esc_html_e( 'Compare memberships', 'verbena' ); ?></a>
				</p>
			</div>
			<?php get_template_part( 'template-parts/membership-tiers', null, array( 'page_id' => $verbena_mem_page ) ); ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( is_array( $verbena_quotes ) && $verbena_quotes ) : ?>
	<section class="bg-plum py-16 text-bg lg:py-24" aria-labelledby="reviews-heading">
		<div class="wrap">
			<p class="eyebrow"><?php esc_html_e( 'Client notes', 'verbena' ); ?></p>
			<h2 id="reviews-heading" class="mt-4 max-w-2xl text-section"><?php esc_html_e( 'What clients tell us after the first visit', 'verbena' ); ?></h2>
			<ul class="mt-12 grid gap-6 lg:grid-cols-3">
				<?php foreach ( $verbena_quotes as $verbena_quote ) : ?>
					<li>
						<figure class="flex h-full flex-col rounded-brand border border-bg/15 bg-ink/40 p-7">
							<span aria-hidden="true" class="font-display text-6xl leading-none text-blush">&ldquo;</span>
							<blockquote class="mt-2 text-lg text-bg/95"><p><?php echo esc_html( $verbena_quote['quote'] ); ?></p></blockquote>
							<figcaption class="mt-auto pt-6 text-[0.95rem] text-mauve"><span class="font-semibold text-bg"><?php echo esc_html( $verbena_quote['name'] ); ?></span> · <?php echo esc_html( $verbena_quote['treatment'] ); ?></figcaption>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="mt-8 text-sm text-mauve"><?php esc_html_e( 'Sample reviews for this demo concept. Individual results vary.', 'verbena' ); ?></p>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/team' ); ?>

<?php if ( is_array( $verbena_faqs ) && $verbena_faqs ) : ?>
	<section class="border-t border-line bg-paper py-16 lg:py-24">
		<div class="wrap grid gap-10 lg:grid-cols-12">
			<div class="lg:col-span-4">
				<p class="eyebrow"><?php esc_html_e( 'Questions', 'verbena' ); ?></p>
				<p class="mt-4 text-ink/75"><?php esc_html_e( 'The things new clients ask most often.', 'verbena' ); ?></p>
				<a class="btn btn-ghost mt-6" href="<?php echo esc_url( get_permalink( $verbena_faq_page ) ); ?>"><?php esc_html_e( 'All questions', 'verbena' ); ?></a>
			</div>
			<div class="lg:col-span-8">
				<?php verbena_faq_accordion( array_slice( $verbena_faqs, 0, 4 ), __( 'Before you book', 'verbena' ), 'home-faq' ); ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/visit' ); ?>
<?php get_template_part( 'template-parts/cta-band' ); ?>

<?php
get_footer();
