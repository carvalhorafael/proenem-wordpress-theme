<?php
/**
 * Vite integration helpers.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROENEM_VITE_DEV_SERVER', 'http://localhost:5173' );
define( 'PROENEM_VITE_MANIFEST_PATH', PROENEM_THEME_DIR . '/assets/dist/.vite/manifest.json' );

/**
 * Determine if the Vite dev server should be used.
 *
 * @return bool
 */
function proenem_vite_is_development() {
	return in_array( wp_get_environment_type(), array( 'local', 'development' ), true );
}

/**
 * Check if the Vite dev server is reachable.
 *
 * @return bool
 */
function proenem_vite_dev_server_is_running() {
	static $is_running = null;

	if ( null !== $is_running ) {
		return $is_running;
	}

	$response   = wp_remote_get(
		PROENEM_VITE_DEV_SERVER . '/@vite/client',
		array(
			'timeout'   => 0.5,
			'sslverify' => false,
		)
	);
	$is_running = ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response );

	return $is_running;
}

/**
 * Read the Vite production manifest.
 *
 * @return array<string, mixed>
 */
function proenem_vite_manifest() {
	static $manifest = null;

	if ( null !== $manifest ) {
		return $manifest;
	}

	if ( ! file_exists( PROENEM_VITE_MANIFEST_PATH ) ) {
		$manifest = array();
		return $manifest;
	}

	$contents = file_get_contents( PROENEM_VITE_MANIFEST_PATH ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$decoded  = json_decode( $contents, true );
	$manifest = is_array( $decoded ) ? $decoded : array();

	return $manifest;
}

/**
 * Get a manifest entry by source path.
 *
 * @param string $entry Entry source path.
 * @return array<string, mixed>|null
 */
function proenem_vite_manifest_entry( $entry ) {
	$manifest = proenem_vite_manifest();

	return isset( $manifest[ $entry ] ) && is_array( $manifest[ $entry ] ) ? $manifest[ $entry ] : null;
}

/**
 * Get a production asset URI from the manifest.
 *
 * @param string $file File path from manifest.
 * @return string
 */
function proenem_vite_asset_uri( $file ) {
	return PROENEM_THEME_URI . '/assets/dist/' . ltrim( $file, '/' );
}
