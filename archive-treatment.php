<?php
/**
 * Treatments archive (/treatments/) and area archives.
 *
 * The hero copy comes from the "Treatments" page so editors control it in
 * the normal page editor; the URL itself is the post type archive.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

$verbena_source = get_page_by_path( 'treatments' );
$verbena_hero   = $verbena_source ? verbena_page_hero_args( $verbena_source->ID ) : array(
	'eyebrow' => __( 'Treatment menu', 'verbena' ),
	'title'   => __( 'Treatments', 'verbena' ),
	'intro'   => '',
	'image'   => 0,
);

if ( is_tax( 'treatment_area' ) ) {
	$verbena_term            = get_queried_object();
	$verbena_hero['eyebrow'] = __( 'Treatments by area', 'verbena' );
	/* translators: %s: area name. */
	$verbena_hero['title'] = sprintf( __( '%s treatments', 'verbena' ), $verbena_term->name );
	$verbena_hero['intro'] = wp_strip_all_tags( term_description( $verbena_term ) );
}

verbena_page_hero( $verbena_hero );

global $wp_query;
$verbena_posts = is_tax( 'treatment_area' ) ? verbena_get_treatments() : $wp_query->posts;
?>
<section class="py-14 lg:py-20" aria-label="<?php esc_attr_e( 'Treatment list', 'verbena' ); ?>">
	<div class="wrap">
		<?php if ( $verbena_posts ) : ?>
			<?php
			get_template_part(
				'template-parts/treatment-grid',
				null,
				array(
					'posts'        => $verbena_posts,
					'card_heading' => 'h2',
				)
			);
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'No treatments have been published yet.', 'verbena' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/cta-band' );
get_footer();
