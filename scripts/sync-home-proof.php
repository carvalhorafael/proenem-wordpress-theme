<?php
/**
 * Synchronize persisted Elementor home proof data for issue #111.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-proof.php
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Report an operational sync message.
 *
 * @param string $message Message to report.
 * @return void
 */
function proenem_issue_111_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * Upgrade verifiable proof settings throughout an Elementor tree.
 *
 * Only exact legacy values and obsolete media repeaters are changed. New
 * editorial selections and custom copy remain untouched.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @return bool Whether the tree changed.
 */
function proenem_issue_111_upgrade_elementor_tree( &$elements ) {
	$changed = false;

	foreach ( $elements as &$element ) {
		$widget_type = $element['widgetType'] ?? '';
		$settings    = &$element['settings'];

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( 'pro_home_proof' === $widget_type ) {
			foreach ( array( 'student_images', 'logos' ) as $legacy_key ) {
				if ( array_key_exists( $legacy_key, $settings ) ) {
					unset( $settings[ $legacy_key ] );
					$changed = true;
				}
			}

			foreach ( array( 'title', 'support' ) as $copy_key ) {
				$current = $settings[ $copy_key ] ?? '';
				$updated = proenem_normalize_home_proof_copy( $current, $copy_key );

				if ( $updated !== $current ) {
					$settings[ $copy_key ] = $updated;
					$changed              = true;
				}
			}

			if ( ! array_key_exists( 'testimonial_ids', $settings ) ) {
				$settings['testimonial_ids'] = array();
				$changed                     = true;
			}
		}

		if ( 'pro_home_testimonials' === $widget_type ) {
			$current = $settings['body'] ?? '';
			$updated = proenem_normalize_home_proof_copy( $current, 'testimonials' );

			if ( $updated !== $current ) {
				$settings['body'] = $updated;
				$changed          = true;
			}
		}

		if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$changed = proenem_issue_111_upgrade_elementor_tree( $element['elements'] ) || $changed;
		}

		unset( $settings );
	}
	unset( $element );

	return $changed;
}

$elementor_updates = 0;
$pages             = get_posts(
	array(
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'post_status'    => array( 'draft', 'publish' ),
		'post_type'      => 'page',
	)
);

foreach ( $pages as $page_id ) {
	$raw_data = get_post_meta( $page_id, '_elementor_data', true );
	$data     = json_decode( $raw_data, true );

	if ( ! is_array( $data ) || ! proenem_issue_111_upgrade_elementor_tree( $data ) ) {
		continue;
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	++$elementor_updates;
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

proenem_issue_111_log( sprintf( 'Elementor home pages updated: %d', $elementor_updates ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Home proof data is synchronized.' );
}
