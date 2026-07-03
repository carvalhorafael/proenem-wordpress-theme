<?php
/**
 * Frontend and editor asset loading.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function proenem_enqueue_assets() {
	wp_enqueue_style(
		'proenem-style',
		get_stylesheet_uri(),
		array(),
		PROENEM_THEME_VERSION
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( proenem_vite_is_development() && proenem_vite_dev_server_is_running() ) {
		wp_enqueue_script( 'proenem-vite-client', PROENEM_VITE_DEV_SERVER . '/@vite/client', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script( 'proenem-theme', PROENEM_VITE_DEV_SERVER . '/src/main.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_script_add_data( 'proenem-vite-client', 'type', 'module' );
		wp_script_add_data( 'proenem-theme', 'type', 'module' );
		return;
	}

	$entry = proenem_vite_manifest_entry( 'src/main.js' );

	if ( ! $entry || empty( $entry['file'] ) ) {
		return;
	}

	if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
		foreach ( $entry['css'] as $index => $css_file ) {
			wp_enqueue_style(
				'proenem-theme-' . $index,
				proenem_vite_asset_uri( $css_file ),
				array(),
				PROENEM_THEME_VERSION
			);
		}
	}

	wp_enqueue_script(
		'proenem-theme',
		proenem_vite_asset_uri( $entry['file'] ),
		array(),
		PROENEM_THEME_VERSION,
		true
	);
	wp_script_add_data( 'proenem-theme', 'type', 'module' );
}
add_action( 'wp_enqueue_scripts', 'proenem_enqueue_assets' );

/**
 * Enqueue block editor assets.
 *
 * @return void
 */
function proenem_enqueue_editor_assets() {
	if ( proenem_vite_is_development() && proenem_vite_dev_server_is_running() ) {
		wp_enqueue_style( 'proenem-editor', PROENEM_VITE_DEV_SERVER . '/src/styles/editor.css', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		return;
	}

	$entry = proenem_vite_manifest_entry( 'src/editor.js' );

	if ( ! $entry || empty( $entry['file'] ) ) {
		return;
	}

	if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
		foreach ( $entry['css'] as $index => $css_file ) {
			wp_enqueue_style(
				'proenem-editor-' . $index,
				proenem_vite_asset_uri( $css_file ),
				array(),
				PROENEM_THEME_VERSION
			);
		}
	}
}
add_action( 'enqueue_block_editor_assets', 'proenem_enqueue_editor_assets' );
