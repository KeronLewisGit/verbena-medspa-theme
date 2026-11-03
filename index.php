<?php
/**
 * Fallback template (blog index, search, generic archives).
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search query. */
	$verbena_title = sprintf( __( 'Search results for "%s"', 'verbena' ), get_search_query() );
} elseif ( is_archive() ) {
	$verbena_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$verbena_title = __( 'Journal', 'verbena' );
}

verbena_page_hero(
	array(
		'eyebrow' => get_bloginfo( 'name' ),
		'title'   => $verbena_title,
	)
);
?>
<div class="wrap py-14 lg:py-20">
	<?php if ( have_posts() ) : ?>
		<ul class="grid max-w-3xl gap-10">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li>
					<h2 class="text-3xl text-ink"><a class="hover:text-accent" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="mt-3 text-ink/80"><?php the_excerpt(); ?></div>
				</li>
			<?php endwhile; ?>
		</ul>
		<div class="mt-12"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p class="text-lg"><?php esc_html_e( 'Nothing found.', 'verbena' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
