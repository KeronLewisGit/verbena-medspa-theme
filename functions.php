<?php
/**
 * Verbena Aesthetics theme bootstrap.
 *
 * This file only defines constants and loads the modules in inc/.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

define( 'VERBENA_VERSION', '1.0.0' );
define( 'VERBENA_DIR', get_template_directory() );
define( 'VERBENA_URI', get_template_directory_uri() );

$verbena_modules = array(
	'helpers',
	'fields-schema',
	'setup',
	'performance',
	'post-types',
	'acf-fields',
	'settings-fallback',
	'template-tags',
	'seo',
	'redirects',
	'analytics',
	'booking',
	'forms',
	'gravity-forms',
	'demo-content',
);

foreach ( $verbena_modules as $verbena_module ) {
	require_once VERBENA_DIR . '/inc/' . $verbena_module . '.php';
}
unset( $verbena_modules, $verbena_module );
