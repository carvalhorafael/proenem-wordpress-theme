<?php
/**
 * Synchronize persisted Elementor question-bank data for issue #113.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-question-bank.php
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
function proenem_issue_113_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * Replace the legacy question-bank threshold in an editorial string.
 *
 * @param string $value Editorial value.
 * @return string
 */
function proenem_issue_113_upgrade_copy( $value ) {
	return str_replace(
		array( '+50 mil questoes', '+50 mil questões', 'Mais de 50 mil questoes', 'Mais de 50 mil questões', '50 mil questoes —', '50 mil questões —' ),
		array( '+60 mil questoes', '+60 mil questões', 'Mais de 60 mil questoes', 'Mais de 60 mil questões', '60 mil questoes —', '60 mil questões —' ),
		$value
	);
}

/**
 * Upgrade question-bank settings throughout an Elementor tree.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @return bool Whether the tree changed.
 */
function proenem_issue_113_upgrade_elementor_tree( &$elements ) {
	$changed = false;

	foreach ( $elements as &$element ) {
		$widget_type = $element['widgetType'] ?? '';
		$settings    = &$element['settings'];

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( 'pro_home_platform' === $widget_type && ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) {
			foreach ( $settings['items'] as &$item ) {
				foreach ( array( 'label', 'title' ) as $copy_key ) {
					$current = $item[ $copy_key ] ?? '';
					$updated = proenem_issue_113_upgrade_copy( $current );

					if ( $updated !== $current ) {
						$item[ $copy_key ] = $updated;
						$changed            = true;
					}
				}
			}
			unset( $item );
		}

		if ( 'pro_home_questions' === $widget_type ) {
			$current = $settings['title_emphasis'] ?? '';
			$updated = proenem_issue_113_upgrade_copy( $current );

			if ( $updated !== $current ) {
				$settings['title_emphasis'] = $updated;
				$changed                    = true;
			}

			if ( ! empty( $settings['subjects'] ) && is_array( $settings['subjects'] ) ) {
				foreach ( $settings['subjects'] as &$subject ) {
					foreach ( array( 'questions', 'classes' ) as $legacy_key ) {
						if ( array_key_exists( $legacy_key, $subject ) ) {
							unset( $subject[ $legacy_key ] );
							$changed = true;
						}
					}
				}
				unset( $subject );
			}
		}

		if ( 'pro_home_pricing' === $widget_type && ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ) {
			foreach ( $settings['plans'] as &$plan ) {
				$current = $plan['features'] ?? '';
				$updated = proenem_issue_113_upgrade_copy( $current );

				if ( $updated !== $current ) {
					$plan['features'] = $updated;
					$changed          = true;
				}
			}
			unset( $plan );
		}

		if ( 'pro_home_pain' === $widget_type && array_key_exists( 'subjects', $settings ) ) {
			unset( $settings['subjects'] );
			$changed = true;
		}

		if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$changed = proenem_issue_113_upgrade_elementor_tree( $element['elements'] ) || $changed;
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

	if ( ! is_array( $data ) || ! proenem_issue_113_upgrade_elementor_tree( $data ) ) {
		continue;
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	++$elementor_updates;
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

proenem_issue_113_log( sprintf( 'Elementor home pages updated: %d', $elementor_updates ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Home question-bank data is synchronized.' );
}
