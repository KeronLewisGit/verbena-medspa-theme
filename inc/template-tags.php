<?php
/**
 * Template helpers shared across templates.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Responsive attachment image. WordPress adds srcset, width/height and lazy
 * loading; the hero passes $priority = true to load eagerly with high priority.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $sizes         sizes attribute.
 * @param string $classes       Classes.
 * @param bool   $priority      LCP image.
 * @param string $size          Registered size used as src.
 * @return string
 */
function verbena_image( $attachment_id, $sizes, $classes = '', $priority = false, $size = 'large' ) {
	if ( ! $attachment_id ) {
		return '';
	}
	$attr = array(
		'class' => $classes,
		'sizes' => $sizes,
	);
	if ( $priority ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
		$attr['decoding']      = 'async';
	} else {
		$attr['loading']  = 'lazy';
		$attr['decoding'] = 'async';
	}
	return wp_get_attachment_image( (int) $attachment_id, $size, false, $attr );
}

/**
 * Allowed tags for verbena_image() output.
 *
 * @return array
 */
function verbena_img_kses() {
	return array(
		'img' => array(
			'src'           => true,
			'srcset'        => true,
			'sizes'         => true,
			'alt'           => true,
			'class'         => true,
			'width'         => true,
			'height'        => true,
			'loading'       => true,
			'decoding'      => true,
			'fetchpriority' => true,
		),
	);
}

/**
 * Echo a responsive image.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $sizes         sizes attribute.
 * @param string $classes       Classes.
 * @param bool   $priority      LCP image.
 * @param string $size          Size.
 * @return void
 */
function verbena_the_image( $attachment_id, $sizes, $classes = '', $priority = false, $size = 'large' ) {
	echo wp_kses( verbena_image( $attachment_id, $sizes, $classes, $priority, $size ), verbena_img_kses() );
}

/**
 * Breadcrumb trail as a list of [label, url] pairs. Shared by the visible
 * breadcrumb and the BreadcrumbList schema so they never disagree.
 *
 * @return array
 */
function verbena_breadcrumb_items() {
	$items = array( array( __( 'Home', 'verbena' ), home_url( '/' ) ) );

	if ( is_front_page() ) {
		return $items;
	}

	if ( is_singular( 'treatment' ) ) {
		$items[] = array( __( 'Treatments', 'verbena' ), get_post_type_archive_link( 'treatment' ) );
		$terms   = get_the_terms( get_the_ID(), 'treatment_area' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$items[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
		}
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_post_type_archive( 'treatment' ) ) {
		$items[] = array( __( 'Treatments', 'verbena' ), get_post_type_archive_link( 'treatment' ) );
	} elseif ( is_tax( 'treatment_area' ) ) {
		$items[] = array( __( 'Treatments', 'verbena' ), get_post_type_archive_link( 'treatment' ) );
		$term    = get_queried_object();
		$items[] = array( $term->name, get_term_link( $term ) );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$items[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_singular() ) {
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_search() ) {
		$items[] = array( __( 'Search', 'verbena' ), '' );
	} elseif ( is_404() ) {
		$items[] = array( __( 'Page not found', 'verbena' ), '' );
	} elseif ( is_archive() ) {
		$items[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	return $items;
}

/**
 * Visible breadcrumb.
 *
 * @param string $classes Wrapper classes.
 * @return void
 */
function verbena_breadcrumbs( $classes = '' ) {
	$items = verbena_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	$last = count( $items ) - 1;
	printf( '<nav aria-label="%s" class="%s"><ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted">', esc_attr__( 'Breadcrumb', 'verbena' ), esc_attr( $classes ) );
	foreach ( $items as $i => $item ) {
		echo '<li class="flex items-center gap-2">';
		if ( $i === $last || ! $item[1] ) {
			printf( '<span aria-current="page" class="text-ink">%s</span>', esc_html( $item[0] ) );
		} else {
			printf( '<a class="underline-offset-4 hover:underline hover:text-ink" href="%s">%s</a><span aria-hidden="true">/</span>', esc_url( $item[1] ), esc_html( $item[0] ) );
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * The single inner-page hero used on every non-home template.
 *
 * @param array $args Keys: eyebrow, title (H1), intro, image (attachment ID),
 *                    facts (list of [label, value]), actions (escaped button HTML).
 * @return void
 */
function verbena_page_hero( $args ) {
	get_template_part( 'template-parts/page-hero', null, $args );
}

/**
 * Hero args for a regular page, read from the page hero field group.
 *
 * @param int $post_id Page ID.
 * @return array
 */
function verbena_page_hero_args( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$image   = verbena_field( 'hero_image', $post_id );
	return array(
		'eyebrow' => (string) verbena_field( 'hero_eyebrow', $post_id ),
		'title'   => get_the_title( $post_id ),
		'intro'   => (string) verbena_field( 'hero_intro', $post_id ),
		'image'   => $image ? (int) $image : (int) get_post_thumbnail_id( $post_id ),
	);
}

/**
 * Primary "Book" button. data-book attributes feed the book_now_click event.
 *
 * @param string $label     Button text.
 * @param string $treatment Treatment name for analytics.
 * @param string $slug      Treatment slug to preselect.
 * @param string $location  Where on the page the button sits.
 * @param string $variant   primary|ghost|light.
 * @return string
 */
function verbena_book_button( $label, $treatment = '', $slug = '', $location = 'content', $variant = 'primary' ) {
	return sprintf(
		'<a class="btn btn-%1$s" href="%2$s" data-book data-treatment="%3$s" data-location="%4$s">%5$s%6$s</a>',
		esc_attr( $variant ),
		esc_url( verbena_book_url( $slug ) ),
		esc_attr( $treatment ),
		esc_attr( $location ),
		esc_html( $label ),
		verbena_icon( 'arrow', 'size-4' )
	);
}

/**
 * Allowed markup for button helpers.
 *
 * @return array
 */
function verbena_button_kses() {
	return array_merge(
		verbena_svg_kses(),
		array(
			'a' => array(
				'class'          => true,
				'href'           => true,
				'data-book'      => true,
				'data-treatment' => true,
				'data-location'  => true,
				'aria-label'     => true,
			),
		)
	);
}

/**
 * Treatments for cards and filters.
 *
 * @param int $limit Max posts.
 * @return WP_Post[]
 */
function verbena_get_treatments( $limit = -1 ) {
	return get_posts(
		array(
			'post_type'      => 'treatment',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

/**
 * Space-separated area slugs for a treatment (used by the JS filter).
 *
 * @param int $post_id Treatment ID.
 * @return string
 */
function verbena_treatment_area_slugs( $post_id ) {
	$terms = get_the_terms( $post_id, 'treatment_area' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	return implode( ' ', wp_list_pluck( $terms, 'slug' ) );
}

/**
 * Pretty name of the first area term.
 *
 * @param int $post_id Treatment ID.
 * @return string
 */
function verbena_treatment_area_name( $post_id ) {
	$terms = get_the_terms( $post_id, 'treatment_area' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
}

/**
 * Price label like "From $195 per session".
 *
 * @param int $post_id Treatment ID.
 * @return string
 */
function verbena_treatment_price_label( $post_id ) {
	$price = verbena_field( 'price_from', $post_id );
	if ( '' === $price || null === $price || false === $price ) {
		return '';
	}
	$unit = (string) verbena_field( 'price_unit', $post_id );
	/* translators: 1: price, 2: unit such as "per session". */
	return trim( sprintf( __( 'From %1$s %2$s', 'verbena' ), verbena_price( $price ), $unit ) );
}

/**
 * Opening hours rows.
 *
 * @return array
 */
function verbena_hours() {
	$hours = verbena_field( 'hours', 'option' );
	return is_array( $hours ) ? $hours : array();
}

/**
 * Format a 24h time ("19:00") for display ("7 pm").
 *
 * @param string $time Time.
 * @return string
 */
function verbena_time_label( $time ) {
	$ts = strtotime( '2000-01-01 ' . $time );
	if ( ! $ts ) {
		return (string) $time;
	}
	return '00' === gmdate( 'i', $ts ) ? gmdate( 'g a', $ts ) : gmdate( 'g:i a', $ts );
}

/**
 * Social profile URLs that are set.
 *
 * @return array label => url
 */
function verbena_social_links() {
	return array_filter(
		array(
			'Instagram' => verbena_option( 'instagram_url' ),
			'Facebook'  => verbena_option( 'facebook_url' ),
			'TikTok'    => verbena_option( 'tiktok_url' ),
		)
	);
}

/**
 * FAQ accordion markup from a list of question/answer rows.
 *
 * @param array  $faqs    Rows with question/answer.
 * @param string $heading Section heading (H2).
 * @param string $id      Section id.
 * @return void
 */
function verbena_faq_accordion( $faqs, $heading = '', $id = 'faq' ) {
	if ( ! is_array( $faqs ) || ! $faqs ) {
		return;
	}
	get_template_part(
		'template-parts/faq-accordion',
		null,
		array(
			'faqs'    => $faqs,
			'heading' => $heading,
			'id'      => $id,
		)
	);
}

/**
 * Attachment ID of a bundled demo image, by name.
 *
 * @param string $name Image name (e.g. "editorial-light").
 * @return int
 */
function verbena_demo_image_id( $name ) {
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_verbena_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $name, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $ids ? (int) $ids[0] : 0;
}
