<?php
/**
 * Built-in technical SEO: titles, meta description, canonical, Open Graph,
 * Twitter cards and a JSON-LD graph.
 *
 * Everything here stands down when a dedicated SEO plugin (Yoast, Rank Math,
 * SEOPress, AIOSEO) is active, so the theme never prints duplicate tags.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the theme should output SEO tags.
 *
 * @return bool
 */
function verbena_seo_enabled() {
	$plugin_active = defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| class_exists( 'RankMath' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' );

	/**
	 * Filter whether theme SEO output runs.
	 *
	 * @param bool $enabled Enabled.
	 */
	return (bool) apply_filters( 'verbena_seo_enabled', ! $plugin_active );
}

/**
 * Hook everything once plugins are loaded.
 *
 * @return void
 */
function verbena_seo_bootstrap() {
	if ( ! verbena_seo_enabled() ) {
		return;
	}
	remove_action( 'wp_head', 'rel_canonical' );
	add_filter( 'pre_get_document_title', 'verbena_document_title' );
	add_filter( 'document_title_separator', 'verbena_title_separator' );
	add_filter( 'wp_robots', 'verbena_robots' );
	add_action( 'wp_head', 'verbena_meta_tags', 3 );
	add_action( 'wp_head', 'verbena_print_schema', 20 );
}
add_action( 'after_setup_theme', 'verbena_seo_bootstrap', 20 );

/**
 * Title separator.
 *
 * @return string
 */
function verbena_title_separator() {
	return '|';
}

/**
 * ID of the post whose SEO fields describe the current view. The treatments
 * archive borrows the "Treatments" page so editors can control it.
 *
 * @return int
 */
function verbena_seo_source_id() {
	if ( is_singular() ) {
		return (int) get_queried_object_id();
	}
	if ( is_post_type_archive( 'treatment' ) ) {
		$page = get_page_by_path( 'treatments' );
		return $page ? (int) $page->ID : 0;
	}
	return 0;
}

/**
 * Custom meta title, when set.
 *
 * @param string $title Title.
 * @return string
 */
function verbena_document_title( $title ) {
	if ( is_tax( 'treatment_area' ) ) {
		/* translators: 1: area name, 2: city, 3: site name. */
		return sprintf( __( '%1$s Treatments in %2$s | %3$s', 'verbena' ), single_term_title( '', false ), verbena_option( 'address_city', 'Dallas' ), get_bloginfo( 'name' ) );
	}
	$source = verbena_seo_source_id();
	if ( $source ) {
		$custom = trim( (string) verbena_field( 'seo_title', $source ) );
		if ( '' !== $custom ) {
			return $custom;
		}
	}
	return $title;
}

/**
 * Meta description for the current view.
 *
 * @return string
 */
function verbena_meta_description() {
	$source = verbena_seo_source_id();
	$text   = '';

	if ( $source ) {
		$text = (string) verbena_field( 'seo_description', $source );
		if ( '' === trim( $text ) && 'treatment' === get_post_type( $source ) ) {
			$text = (string) verbena_field( 'summary', $source );
		}
		if ( '' === trim( $text ) && 'page' === get_post_type( $source ) ) {
			$text = (string) verbena_field( 'hero_intro', $source );
		}
		if ( '' === trim( $text ) && has_excerpt( $source ) ) {
			$text = get_the_excerpt( $source );
		}
	} elseif ( is_tax() ) {
		$text = term_description();
	}

	if ( '' === trim( $text ) && is_front_page() ) {
		$text = (string) verbena_field( 'home_hero_text', (int) get_option( 'page_on_front' ) );
	}
	if ( '' === trim( $text ) ) {
		$text = get_bloginfo( 'description' );
	}

	return wp_html_excerpt( wp_strip_all_tags( $text ), 160, '' );
}

/**
 * Canonical URL for the current view.
 *
 * @return string
 */
function verbena_canonical_url() {
	$url = '';
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_singular() ) {
		$url = get_permalink( get_queried_object_id() );
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$url = get_term_link( get_queried_object() );
	} elseif ( is_home() ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	}
	if ( ! $url || is_wp_error( $url ) ) {
		return '';
	}
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$url = trailingslashit( $url ) . user_trailingslashit( 'page/' . $paged, 'paged' );
	}
	return $url;
}

/**
 * Social share image: featured image, page hero image, or the home hero.
 *
 * @return array{0:string,1:int,2:int}|null
 */
function verbena_share_image() {
	$source = verbena_seo_source_id();
	$id     = 0;
	if ( $source ) {
		$id = (int) get_post_thumbnail_id( $source );
		if ( ! $id && 'page' === get_post_type( $source ) ) {
			$id = (int) verbena_field( 'hero_image', $source );
		}
	}
	if ( ! $id ) {
		$id = (int) verbena_field( 'home_hero_image', (int) get_option( 'page_on_front' ) );
	}
	return verbena_image_meta( $id );
}

/**
 * URL and real pixel size of an attachment's full image. (Named sizes are
 * constrained by $content_width in this context, which would misreport the
 * og:image dimensions.)
 *
 * @param int $id Attachment ID.
 * @return array{0:string,1:int,2:int}|null
 */
function verbena_image_meta( $id ) {
	$meta = $id ? wp_get_attachment_metadata( $id ) : false;
	$url  = $id ? wp_get_attachment_url( $id ) : false;
	if ( ! $url || empty( $meta['width'] ) ) {
		return null;
	}
	return array( $url, (int) $meta['width'], (int) $meta['height'] );
}

/**
 * Per-page noindex.
 *
 * @param array $robots Directives.
 * @return array
 */
function verbena_robots( $robots ) {
	$source = verbena_seo_source_id();
	if ( $source && verbena_field( 'seo_noindex', $source ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	} elseif ( ! is_404() && ! is_search() ) {
		$robots['max-image-preview'] = 'large';
	}
	return $robots;
}

/**
 * Description, canonical, Open Graph and Twitter tags.
 *
 * @return void
 */
function verbena_meta_tags() {
	$description = verbena_meta_description();
	$canonical   = verbena_canonical_url();
	$title       = wp_get_document_title();
	$image       = verbena_share_image();

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}

	$og = array(
		'og:locale'    => get_locale(),
		'og:type'      => is_singular( 'treatment' ) ? 'article' : 'website',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => $title,
		'og:url'       => $canonical,
	);
	if ( $description ) {
		$og['og:description'] = $description;
	}
	if ( $image ) {
		$og['og:image']        = $image[0];
		$og['og:image:width']  = (string) $image[1];
		$og['og:image:height'] = (string) $image[2];
	}
	foreach ( array_filter( $og ) as $property => $content ) {
		$escaped = in_array( $property, array( 'og:url', 'og:image' ), true ) ? esc_url( $content ) : esc_attr( $content );
		printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), $escaped ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}

	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image[0] ) );
	}
}

/**
 * Plain text for JSON-LD: no tags, entities decoded (json_encode escapes).
 *
 * @param string $text Text.
 * @return string
 */
function verbena_plain( $text ) {
	return trim( html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
}

/**
 * Build the JSON-LD @graph for the current view.
 *
 * @return array
 */
function verbena_schema_graph() {
	$home        = home_url( '/' );
	$business_id = $home . '#business';
	$website_id  = $home . '#website';
	$graph       = array();

	$same_as = array_values( verbena_social_links() );

	$hours = array();
	foreach ( verbena_hours() as $row ) {
		if ( empty( $row['days'] ) || empty( $row['opens'] ) || empty( $row['closes'] ) ) {
			continue;
		}
		$hours[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array_map( 'verbena_schema_day', array_map( 'trim', explode( ',', $row['days'] ) ) ),
			'opens'     => $row['opens'],
			'closes'    => $row['closes'],
		);
	}

	$logo_image = verbena_image_meta( (int) verbena_field( 'home_hero_image', (int) get_option( 'page_on_front' ) ) );
	$business   = array(
		'@type'                     => array( 'MedicalBusiness', 'HealthAndBeautyBusiness' ),
		'@id'                       => $business_id,
		'name'                      => verbena_plain( get_bloginfo( 'name' ) ),
		'description'               => verbena_plain( get_bloginfo( 'description' ) ),
		'url'                       => $home,
		'telephone'                 => verbena_option( 'phone' ),
		'email'                     => verbena_option( 'email' ),
		'priceRange'                => verbena_option( 'price_range', '$$' ),
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => verbena_option( 'address_street' ),
			'addressLocality' => verbena_option( 'address_city' ),
			'addressRegion'   => verbena_option( 'address_region' ),
			'postalCode'      => verbena_option( 'address_postal' ),
			'addressCountry'  => 'US',
		),
		'openingHoursSpecification' => $hours,
		'sameAs'                    => $same_as,
	);
	if ( verbena_option( 'geo_lat' ) && verbena_option( 'geo_lng' ) ) {
		$business['geo']    = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) verbena_option( 'geo_lat' ),
			'longitude' => (float) verbena_option( 'geo_lng' ),
		);
		$business['hasMap'] = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( verbena_address_line() );
	}
	if ( $logo_image ) {
		$business['image'] = $logo_image[0];
	}
	$graph[] = array_filter( $business );

	$graph[] = array(
		'@type'      => 'WebSite',
		'@id'        => $website_id,
		'url'        => $home,
		'name'       => get_bloginfo( 'name' ),
		'publisher'  => array( '@id' => $business_id ),
		'inLanguage' => get_bloginfo( 'language' ),
	);

	$canonical = verbena_canonical_url();
	if ( $canonical ) {
		$graph[] = array(
			'@type'       => is_singular( 'treatment' ) || is_page() || is_front_page() ? 'WebPage' : 'CollectionPage',
			'@id'         => $canonical . '#webpage',
			'url'         => $canonical,
			'name'        => wp_get_document_title(),
			'description' => verbena_meta_description(),
			'isPartOf'    => array( '@id' => $website_id ),
			'about'       => array( '@id' => $business_id ),
			'breadcrumb'  => is_front_page() ? null : array( '@id' => $canonical . '#breadcrumb' ),
		);
	}

	if ( ! is_front_page() && $canonical ) {
		$list = array();
		foreach ( verbena_breadcrumb_items() as $i => $item ) {
			$crumb = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => verbena_plain( $item[0] ),
			);
			if ( $item[1] ) {
				$crumb['item'] = $item[1];
			}
			$list[] = $crumb;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $canonical . '#breadcrumb',
			'itemListElement' => $list,
		);
	}

	if ( is_singular( 'treatment' ) ) {
		$post_id = get_queried_object_id();
		$service = array(
			'@type'       => 'Service',
			'@id'         => get_permalink( $post_id ) . '#service',
			'name'        => verbena_plain( get_the_title( $post_id ) ),
			'serviceType' => verbena_plain( verbena_treatment_area_name( $post_id ) ),
			'description' => verbena_plain( (string) verbena_field( 'summary', $post_id ) ),
			'url'         => get_permalink( $post_id ),
			'provider'    => array( '@id' => $business_id ),
			'areaServed'  => array(
				'@type' => 'City',
				'name'  => verbena_option( 'address_city', 'Dallas' ),
			),
		);
		$image   = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $image ) {
			$service['image'] = $image;
		}
		$price = verbena_field( 'price_from', $post_id );
		if ( '' !== $price && null !== $price && false !== $price ) {
			$service['offers'] = array(
				'@type'              => 'Offer',
				'url'                => verbena_book_url( get_post_field( 'post_name', $post_id ) ),
				'priceCurrency'      => 'USD',
				'price'              => (string) $price,
				'priceSpecification' => array(
					'@type'         => 'PriceSpecification',
					'minPrice'      => (float) $price,
					'priceCurrency' => 'USD',
				),
			);
		}
		$graph[] = $service;

		$faq_graph = verbena_faq_schema( verbena_field( 'treatment_faqs', $post_id ), get_permalink( $post_id ) );
		if ( $faq_graph ) {
			$graph[] = $faq_graph;
		}
	}

	if ( is_page_template( 'templates/template-faq.php' ) ) {
		$faq_graph = verbena_faq_schema( verbena_field( 'page_faqs', get_queried_object_id() ), get_permalink() );
		if ( $faq_graph ) {
			$graph[] = $faq_graph;
		}
	}

	/**
	 * Filter the JSON-LD graph before output.
	 *
	 * @param array $graph Graph nodes.
	 */
	return apply_filters( 'verbena_schema_graph', array_map( 'verbena_schema_clean', $graph ) );
}

/**
 * Remove empty values from a node.
 *
 * @param array $node Node.
 * @return array
 */
function verbena_schema_clean( $node ) {
	return array_filter(
		$node,
		static function ( $value ) {
			return ! ( null === $value || '' === $value || array() === $value );
		}
	);
}

/**
 * FAQPage node from question/answer rows.
 *
 * @param mixed  $faqs Rows.
 * @param string $url  Page URL.
 * @return array|null
 */
function verbena_faq_schema( $faqs, $url ) {
	if ( ! is_array( $faqs ) || ! $faqs ) {
		return null;
	}
	$entities = array();
	foreach ( $faqs as $row ) {
		if ( empty( $row['question'] ) || empty( $row['answer'] ) ) {
			continue;
		}
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => verbena_plain( $row['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => verbena_plain( $row['answer'] ),
			),
		);
	}
	if ( ! $entities ) {
		return null;
	}
	return array(
		'@type'      => 'FAQPage',
		'@id'        => $url . '#faq',
		'mainEntity' => $entities,
	);
}

/**
 * Map a two-letter day code to a schema.org DayOfWeek URL.
 *
 * @param string $code Mo, Tu, We, Th, Fr, Sa, Su.
 * @return string
 */
function verbena_schema_day( $code ) {
	$days = array(
		'Mo' => 'Monday',
		'Tu' => 'Tuesday',
		'We' => 'Wednesday',
		'Th' => 'Thursday',
		'Fr' => 'Friday',
		'Sa' => 'Saturday',
		'Su' => 'Sunday',
	);
	$key  = ucfirst( strtolower( substr( $code, 0, 2 ) ) );
	return 'https://schema.org/' . ( $days[ $key ] ?? $code );
}

/**
 * Print the graph.
 *
 * @return void
 */
function verbena_print_schema() {
	if ( is_404() || is_search() ) {
		return;
	}
	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => verbena_schema_graph(),
	);
	wp_print_inline_script_tag(
		wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ),
		array( 'type' => 'application/ld+json' )
	);
}
