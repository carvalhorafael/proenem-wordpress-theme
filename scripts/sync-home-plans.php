<?php
/**
 * Synchronize persisted Elementor home-plan data for issue #152.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-plans.php
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
function proenem_issue_152_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * Remove unavailable plans and update known plan copy in an Elementor tree.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @return bool Whether the tree changed.
 */
function proenem_issue_152_upgrade_elementor_tree( &$elements ) {
	$changed                = false;
	$unavailable_plan_names = array(
		__( 'Método PRO Avançado', 'proenem-wordpress-theme' ),
		__( 'Pro Medicina', 'proenem-wordpress-theme' ),
	);
	$plans_question         = __( 'Qual a diferença entre os planos?', 'proenem-wordpress-theme' );
	$legacy_answer_hashes   = array(
		'c04c5e89549191e3cc7ebfc768fc43be',
		'318df6b370eb72cd06b15467e92de841',
	);
	$updated_plans_answer   = __( 'O Grátis oferece diagnóstico e questões. O Método PRO acrescenta cronograma personalizado até a prova, duas correções de redação mensais, aulas gravadas, PDFs completos e simulados com nota TRI.', 'proenem-wordpress-theme' );

	foreach ( $elements as &$element ) {
		$widget_type = $element['widgetType'] ?? '';
		$settings    = &$element['settings'];

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( 'pro_home_pricing' === $widget_type && ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ) {
			$available_plans = array_values(
				array_filter(
					$settings['plans'],
					static function ( $plan ) use ( $unavailable_plan_names ) {
						return ! in_array( $plan['name'] ?? '', $unavailable_plan_names, true );
					}
				)
			);

			if ( $available_plans !== $settings['plans'] ) {
				$settings['plans'] = $available_plans;
				$changed           = true;
			}
		}

		if ( 'pro_home_faq' === $widget_type && ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) {
			foreach ( $settings['items'] as &$item ) {
				$answer = $item['answer'] ?? '';

				if ( $plans_question === ( $item['question'] ?? '' ) && in_array( md5( $answer ), $legacy_answer_hashes, true ) ) {
					$item['answer'] = $updated_plans_answer;
					$changed        = true;
				}
			}
			unset( $item );
		}

		if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$changed = proenem_issue_152_upgrade_elementor_tree( $element['elements'] ) || $changed;
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

	if ( ! is_array( $data ) || ! proenem_issue_152_upgrade_elementor_tree( $data ) ) {
		continue;
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	++$elementor_updates;
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

proenem_issue_152_log( sprintf( 'Elementor home pages updated: %d', $elementor_updates ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Home plan data is synchronized.' );
}
