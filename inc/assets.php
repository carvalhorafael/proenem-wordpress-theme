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
 * Check whether the current request renders an approved-students surface.
 *
 * @return bool
 */
function proenem_is_approved_students_surface() {
	return is_page_template( 'page-templates/testimonials.php' ) || is_singular( proenem_get_testimonials_post_type() );
}

/**
 * Let modern browsers render emoji natively on approved-students surfaces.
 *
 * These templates use native text symbols and do not require WordPress's
 * Twemoji compatibility scripts or their image fallback styles.
 *
 * @return void
 */
function proenem_disable_approved_students_emoji_assets() {
	if ( ! proenem_is_approved_students_surface() ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'wp', 'proenem_disable_approved_students_emoji_assets' );

/**
 * Check whether a testimonial body needs WordPress block styles.
 *
 * @param int $post_id Testimonial post ID.
 * @return bool
 */
function proenem_testimonial_uses_blocks( $post_id ) {
	return has_blocks( $post_id );
}

/**
 * Remove block-library CSS from custom templates without block content.
 *
 * The home and approved-students listing are rendered by PHP template markup
 * instead of post block content. Individual stories retain the stylesheet only
 * when their body contains Gutenberg block markup.
 *
 * @return void
 */
function proenem_dequeue_custom_template_block_assets() {
	$plain_testimonial = is_singular( proenem_get_testimonials_post_type() ) && ! proenem_testimonial_uses_blocks( get_queried_object_id() );

	if ( ! is_front_page() && ! is_page_template( 'page-templates/home.php' ) && ! is_page_template( 'page-templates/testimonials.php' ) && ! $plain_testimonial ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'proenem_dequeue_custom_template_block_assets', 100 );

/**
 * Remove capture plugin assets from surfaces without a capture form.
 *
 * The free-material capture form is not rendered on the home or approved-student
 * pages, so these assets can stay limited to the material surfaces that need them.
 *
 * @return void
 */
function proenem_dequeue_unused_capture_assets() {
	if ( ! is_front_page() && ! is_page_template( 'page-templates/home.php' ) && ! proenem_is_approved_students_surface() ) {
		return;
	}

	wp_dequeue_style( 'crm-leads-capture-free-material' );
	wp_dequeue_script( 'crm-leads-capture-free-material' );
}
add_action( 'wp_enqueue_scripts', 'proenem_dequeue_unused_capture_assets', 100 );

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
