<?php
/**
 * Router for PHP's built-in server, for local testing only.
 *
 * Run from the WordPress root:  php -S 127.0.0.1:8140 /path/to/scripts/dev-router.php
 * Real files are served directly; everything else goes to index.php so
 * pretty permalinks work.
 *
 * @package Verbena
 */

// phpcs:disable WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification
$verbena_root = $_SERVER['DOCUMENT_ROOT'];
$verbena_path = (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url

if ( '/' !== $verbena_path && is_file( $verbena_root . $verbena_path ) ) {
	return false;
}
if ( is_dir( $verbena_root . $verbena_path ) && is_file( rtrim( $verbena_root . $verbena_path, '/' ) . '/index.php' ) ) {
	$_SERVER['SCRIPT_NAME'] = rtrim( $verbena_path, '/' ) . '/index.php';
	require rtrim( $verbena_root . $verbena_path, '/' ) . '/index.php';
	return;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $verbena_root . '/index.php';
