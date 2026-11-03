<?php
/**
 * Demo content importer.
 *
 * Builds the whole demo site (pages, treatments, media, menus, settings) the
 * first time the theme runs, so it can be deployed with nothing more than
 * "install WordPress, upload theme, activate".
 *
 * Triggers, in order of preference:
 * 1. after_switch_theme (fires on the first request after activation, also
 *    when activation happened through WP-CLI's `wp theme activate`).
 * 2. init, as a safety net, whenever the "imported" flag is missing.
 *
 * Concurrency: an INSERT IGNORE lock row makes sure only one request imports.
 * A concurrent front-end visitor gets a short 503 "setting up" page with
 * Retry-After instead of a half-built site.
 *
 * Tools > Verbena demo has a nonce-protected button to wipe and rebuild it.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

const VERBENA_DEMO_FLAG    = 'verbena_demo_imported';
const VERBENA_DEMO_LOCK    = 'verbena_demo_lock';
const VERBENA_DEMO_VERSION = '1';

/**
 * Result of an import run during this request, if any.
 *
 * @param string|null $set Value to store.
 * @return string
 */
function verbena_demo_request_result( $set = null ) {
	static $result = '';
	if ( null !== $set ) {
		$result = $set;
	}
	return $result;
}

/**
 * Run the import on theme activation. Core fires this from
 * check_theme_switched() on init (priority 99) of the first request after
 * switch_theme(), including after `wp theme activate`. The response is left
 * to verbena_demo_on_init() so core can finish its own switch bookkeeping.
 *
 * @return void
 */
function verbena_demo_on_switch() {
	verbena_demo_request_result( verbena_demo_maybe_import() );
}
add_action( 'after_switch_theme', 'verbena_demo_on_switch', 99 );

/**
 * Safety net: import on any request if the flag is missing, then respond.
 *
 * @return void
 */
function verbena_demo_on_init() {
	$result = verbena_demo_request_result();
	if ( '' === $result ) {
		if ( get_option( VERBENA_DEMO_FLAG ) || wp_installing() ) {
			return;
		}
		$result = verbena_demo_maybe_import();
	}

	$is_page_view = ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) && ! ( defined( 'WP_CLI' ) && WP_CLI )
		&& isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'];
	if ( ! $is_page_view ) {
		return;
	}
	if ( 'locked' === $result ) {
		verbena_demo_setup_screen();
	}
	if ( 'imported' === $result && isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
		// Reload so this first visitor gets the finished site with fresh rewrite rules.
		$url = set_url_scheme( 'http://' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		wp_safe_redirect( $url, 302 );
		exit;
	}
}
add_action( 'init', 'verbena_demo_on_init', 120 );

/**
 * Minimal holding page while another request is importing.
 *
 * @return void
 */
function verbena_demo_setup_screen() {
	status_header( 503 );
	header( 'Retry-After: 10' );
	nocache_headers();
	echo '<!doctype html><meta charset="utf-8"><meta http-equiv="refresh" content="6"><meta name="viewport" content="width=device-width"><title>' . esc_html__( 'Setting up', 'verbena' ) . '</title>';
	echo '<body style="font:18px/1.6 system-ui;background:#F7F2EA;color:#2B1A2F;display:grid;place-items:center;min-height:100vh;margin:0"><p>' . esc_html__( 'Setting up the demo site. This page will refresh in a few seconds.', 'verbena' ) . '</p></body>';
	exit;
}

/**
 * Import if not done yet, holding a lock.
 *
 * @param bool $force Re-run even if the flag is set (used by Reset).
 * @return string imported|done|locked|error
 */
function verbena_demo_maybe_import( $force = false ) {
	if ( ! $force && get_option( VERBENA_DEMO_FLAG ) ) {
		return 'done';
	}

	if ( ! verbena_demo_acquire_lock() ) {
		return 'locked';
	}

	// Re-check inside the lock, straight from the database (the options
	// cache for this request was primed before another request finished).
	if ( ! $force && verbena_demo_flag_in_db() ) {
		verbena_demo_release_lock();
		return 'done';
	}

	try {
		verbena_demo_import();
		update_option( VERBENA_DEMO_FLAG, VERBENA_DEMO_VERSION );
		$status = 'imported';
	} catch ( Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Verbena demo import failed: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
		$status = 'error';
	}

	verbena_demo_release_lock();
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'notoptions', 'options' );
	return $status;
}

/**
 * Atomic lock, same technique as core's WP_Upgrader::create_lock():
 * INSERT IGNORE succeeds for exactly one request. (add_option() can't be
 * used: it upserts, so two requests would both "win".)
 *
 * @return bool
 */
function verbena_demo_acquire_lock() {
	global $wpdb;
	$now = time();

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- lock must bypass the object cache.
	$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `{$wpdb->options}` ( `option_name`, `option_value`, `autoload` ) VALUES ( %s, %s, 'no' )", VERBENA_DEMO_LOCK, (string) $now ) );
	if ( $inserted ) {
		return true;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$held_since = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM `{$wpdb->options}` WHERE option_name = %s", VERBENA_DEMO_LOCK ) );
	if ( $held_since && $now - $held_since > 5 * MINUTE_IN_SECONDS ) {
		// Stale lock from a request that died. Only one UPDATE can match the old value.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (bool) $wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->options}` SET option_value = %s WHERE option_name = %s AND option_value = %s", (string) $now, VERBENA_DEMO_LOCK, (string) $held_since ) );
	}
	return false;
}

/**
 * Release the import lock.
 *
 * @return void
 */
function verbena_demo_release_lock() {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->options}` WHERE option_name = %s", VERBENA_DEMO_LOCK ) );
	wp_cache_delete( VERBENA_DEMO_LOCK, 'options' );
}

/**
 * Whether the imported flag is set, read without the options cache.
 *
 * @return bool
 */
function verbena_demo_flag_in_db() {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM `{$wpdb->options}` WHERE option_name = %s", VERBENA_DEMO_FLAG ) );
}

/**
 * Build everything. Idempotent: existing demo items are found and updated.
 *
 * @return void
 */
function verbena_demo_import() {
	require_once VERBENA_DIR . '/inc/demo-data.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- image processing on shared hosting.
	}
	wp_raise_memory_limit( 'image' );
	wp_defer_term_counting( true );

	$data = verbena_demo_data();

	// Site identity and reading settings.
	update_option( 'blogname', 'Verbena Aesthetics' );
	update_option( 'blogdescription', 'Physician-led medspa in Uptown Dallas: injectables, facials, laser and wellness.' );
	update_option( 'timezone_string', 'America/Chicago' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );

	// Media.
	$images = array();
	foreach ( $data['images'] as $name => $alt ) {
		$images[ $name ] = verbena_demo_import_image( $name, $alt );
	}
	$image_id = static function ( $name ) use ( $images ) {
		return ( $name && isset( $images[ $name ] ) ) ? (int) $images[ $name ] : 0;
	};

	// Treatment areas.
	$terms = array();
	foreach ( $data['areas'] as $slug => $area ) {
		$existing = get_term_by( 'slug', $slug, 'treatment_area' );
		if ( $existing ) {
			$terms[ $slug ] = (int) $existing->term_id;
			wp_update_term( $existing->term_id, 'treatment_area', array( 'description' => $area[1] ) );
		} else {
			$created        = wp_insert_term(
				$area[0],
				'treatment_area',
				array(
					'slug'        => $slug,
					'description' => $area[1],
				)
			);
			$terms[ $slug ] = is_wp_error( $created ) ? 0 : (int) $created['term_id'];
		}
	}

	// Treatments.
	$treatment_ids = array();
	foreach ( $data['treatments'] as $order => $treatment ) {
		$post_id = verbena_demo_upsert_post(
			'treatment',
			$treatment['slug'],
			array(
				'post_title'   => $treatment['title'],
				'post_content' => $treatment['content'],
				'post_excerpt' => $treatment['excerpt'],
				'menu_order'   => $order,
			)
		);
		if ( ! $post_id ) {
			continue;
		}
		$treatment_ids[ $treatment['slug'] ] = $post_id;
		wp_set_object_terms( $post_id, array( $terms[ $treatment['area'] ] ), 'treatment_area' );
		if ( $image_id( $treatment['image'] ) ) {
			set_post_thumbnail( $post_id, $image_id( $treatment['image'] ) );
		}
		foreach ( $treatment['fields'] as $name => $value ) {
			verbena_write_field( $name, $value, $post_id );
		}
	}

	// Home page.
	$home_id = verbena_demo_upsert_post(
		'page',
		'home',
		array(
			'post_title'   => 'Home',
			'post_content' => '',
		)
	);
	foreach ( $data['front_page'] as $name => $value ) {
		if ( in_array( $name, array( 'home_hero_image', 'home_intro_image' ), true ) ) {
			$value = $image_id( $value );
		}
		verbena_write_field( $name, $value, $home_id );
	}
	verbena_write_field( 'seo_title', 'Verbena Aesthetics | Medspa in Uptown Dallas, TX', $home_id );
	verbena_write_field( 'seo_description', 'Physician-led medspa in Uptown Dallas. Botox, fillers, HydraFacial, laser hair removal, peels, microneedling and IV therapy with clear pricing.', $home_id );

	// Inner pages.
	$page_ids = array( 'home' => $home_id );
	foreach ( $data['pages'] as $slug => $page ) {
		$existing_id = 0;
		if ( 'privacy' === $slug ) {
			$existing_id = (int) get_option( 'wp_page_for_privacy_policy' );
			$existing_id = ( $existing_id && get_post( $existing_id ) ) ? $existing_id : 0;
		}
		$page_id = verbena_demo_upsert_post(
			'page',
			'privacy' === $slug ? 'privacy-policy' : $slug,
			array(
				'post_title'   => $page['title'],
				'post_content' => $page['content'],
			),
			$existing_id
		);
		if ( ! $page_id ) {
			continue;
		}
		$page_ids[ $slug ] = $page_id;
		update_post_meta( $page_id, '_wp_page_template', $page['template'] ? $page['template'] : 'default' );
		foreach ( $page['fields'] as $name => $value ) {
			if ( 'hero_image' === $name ) {
				$value = $image_id( $value );
			}
			verbena_write_field( $name, $value, $page_id );
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', 0 );
	if ( ! empty( $page_ids['privacy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $page_ids['privacy'] );
	}

	// Site settings (ACF options storage).
	foreach ( $data['options'] as $name => $value ) {
		if ( 'team' === $name ) {
			foreach ( $value as $i => $member ) {
				$value[ $i ]['photo'] = $image_id( $member['photo'] );
			}
		}
		verbena_write_field( $name, $value, 'option' );
	}

	// Menus.
	verbena_demo_menus( $page_ids );

	// Redirect examples.
	foreach ( $data['redirects'] as $rule ) {
		verbena_add_redirect( $rule[0], $rule[1], $rule[2] );
	}

	// Remove the stock "Hello world!" post and "Sample Page".
	foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as $stock ) {
		$stock_post = get_page_by_path( $stock[0], OBJECT, $stock[1] );
		if ( $stock_post && ! get_post_meta( $stock_post->ID, '_verbena_demo', true ) ) {
			wp_delete_post( $stock_post->ID, true );
		}
	}

	wp_defer_term_counting( false );

	// Pretty permalinks, written to .htaccess where the server supports it.
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	verbena_register_post_types();
	flush_rewrite_rules( true );
}

/**
 * Create or update a demo post by slug.
 *
 * @param string $post_type Post type.
 * @param string $slug      Slug.
 * @param array  $fields    wp_insert_post fields.
 * @param int    $existing  Known existing ID.
 * @return int Post ID or 0.
 */
function verbena_demo_upsert_post( $post_type, $slug, $fields, $existing = 0 ) {
	if ( ! $existing ) {
		$found    = get_posts(
			array(
				'post_type'      => $post_type,
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$existing = $found ? (int) $found[0] : 0;
	}

	$postarr = array_merge(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'post_name'      => $slug,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => verbena_demo_author(),
		),
		$fields
	);
	if ( $existing ) {
		$postarr['ID'] = $existing;
	}

	$post_id = $existing ? wp_update_post( wp_slash( $postarr ), true ) : wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $post_id ) ) {
		return 0;
	}
	update_post_meta( $post_id, '_verbena_demo', 1 );
	return (int) $post_id;
}

/**
 * First administrator, used as the author of demo content.
 *
 * @return int
 */
function verbena_demo_author() {
	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);
	return $admins ? (int) $admins[0] : 0;
}

/**
 * Copy a bundled image into the media library (once).
 *
 * @param string $name Image name (file without .webp).
 * @param string $alt  Alt text.
 * @return int Attachment ID or 0.
 */
function verbena_demo_import_image( $name, $alt ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_verbena_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $name, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		update_post_meta( (int) $existing[0], '_wp_attachment_image_alt', $alt );
		return (int) $existing[0];
	}

	$source = VERBENA_DIR . '/assets/images/' . $name . '.webp';
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	$tmp = wp_tempnam( $name . '.webp' );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}

	// Only the sizes the theme uses in srcset; keeps activation fast on shared hosting.
	$limit_sizes = static function ( $sizes ) {
		return array_intersect_key( $sizes, array_flip( array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) );
	};
	add_filter( 'intermediate_image_sizes_advanced', $limit_sizes );

	$attachment_id = media_handle_sideload(
		array(
			'name'     => 'verbena-' . $name . '.webp',
			'tmp_name' => $tmp,
		),
		0,
		$alt
	);

	remove_filter( 'intermediate_image_sizes_advanced', $limit_sizes );

	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $attachment_id, '_verbena_demo_image', $name );
	update_post_meta( $attachment_id, '_verbena_demo', 1 );
	return (int) $attachment_id;
}

/**
 * Create the primary and footer menus and assign locations.
 *
 * @param array $page_ids slug => ID.
 * @return void
 */
function verbena_demo_menus( $page_ids ) {
	$menus = array(
		'primary' => array(
			'name'  => 'Primary',
			'items' => array( 'archive:treatment', 'memberships', 'about', 'faq', 'book' ),
		),
		'footer'  => array(
			'name'  => 'Footer',
			'items' => array( 'archive:treatment', 'memberships', 'about', 'faq', 'book', 'privacy' ),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $menu ) {
		$existing = wp_get_nav_menu_object( $menu['name'] );
		if ( $existing ) {
			wp_delete_nav_menu( $existing->term_id );
		}
		$menu_id = wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		foreach ( $menu['items'] as $position => $item ) {
			if ( 'archive:treatment' === $item ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => 'Treatments',
						'menu-item-type'     => 'post_type_archive',
						'menu-item-object'   => 'treatment',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $position + 1,
					)
				);
				continue;
			}
			if ( empty( $page_ids[ $item ] ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => 'book' === $item ? 'Contact' : get_the_title( $page_ids[ $item ] ),
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids[ $item ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position + 1,
				)
			);
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Delete all demo-created content (posts, pages, treatments, media, menus).
 *
 * @return void
 */
function verbena_demo_reset() {
	$posts = get_posts(
		array(
			'post_type'      => array( 'page', 'treatment', 'attachment' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_verbena_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $posts as $post_id ) {
		if ( 'attachment' === get_post_type( $post_id ) ) {
			wp_delete_attachment( $post_id, true );
		} else {
			wp_delete_post( $post_id, true );
		}
	}
	foreach ( array( 'Primary', 'Footer' ) as $menu_name ) {
		$menu = wp_get_nav_menu_object( $menu_name );
		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}
	}
	delete_option( VERBENA_REDIRECTS_OPTION );
	delete_option( VERBENA_DEMO_FLAG );
}

/**
 * Tools > Verbena demo.
 *
 * @return void
 */
function verbena_demo_menu() {
	add_management_page( __( 'Verbena demo', 'verbena' ), __( 'Verbena demo', 'verbena' ), 'manage_options', 'verbena-demo', 'verbena_render_demo_page' );
}
add_action( 'admin_menu', 'verbena_demo_menu' );

/**
 * Render the demo tools screen.
 *
 * @return void
 */
function verbena_render_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done = isset( $_GET['verbena_reset'] ) ? sanitize_key( wp_unslash( $_GET['verbena_reset'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Verbena demo content', 'verbena' ); ?></h1>
		<?php if ( 'imported' === $done ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Demo content rebuilt.', 'verbena' ); ?></p></div>
		<?php elseif ( $done ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'The rebuild did not finish. Try again in a minute.', 'verbena' ); ?></p></div>
		<?php endif; ?>
		<p>
			<?php
			$imported = get_option( VERBENA_DEMO_FLAG );
			echo esc_html( $imported ? __( 'Status: demo content is installed.', 'verbena' ) : __( 'Status: demo content has not been imported.', 'verbena' ) );
			?>
		</p>
		<p><?php esc_html_e( 'Reset deletes the demo pages, treatments, images, menus and redirects, then imports them again. Leads and anything you created yourself are kept.', 'verbena' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="verbena_demo_reset">
			<?php wp_nonce_field( 'verbena_demo_reset' ); ?>
			<?php submit_button( __( 'Reset demo content', 'verbena' ), 'delete', 'submit', false, array( 'onclick' => "return confirm('" . esc_js( __( 'Delete and rebuild all demo content?', 'verbena' ) ) . "');" ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Handle the reset button.
 *
 * @return void
 */
function verbena_handle_demo_reset() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'verbena' ), 403 );
	}
	check_admin_referer( 'verbena_demo_reset' );

	verbena_demo_reset();
	$status = verbena_demo_maybe_import( true );

	wp_safe_redirect( add_query_arg( 'verbena_reset', $status, admin_url( 'tools.php?page=verbena-demo' ) ) );
	exit;
}
add_action( 'admin_post_verbena_demo_reset', 'verbena_handle_demo_reset' );
