<?php
/**
 * Synchronize persisted WordPress home conversion data for issue #110.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-conversion.php
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
function proenem_issue_110_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * Check whether an Elementor element tree contains a widget.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @param string                         $widget_type Widget type.
 * @return bool
 */
function proenem_issue_110_contains_widget( $elements, $widget_type ) {
	foreach ( $elements as $element ) {
		if ( $widget_type === ( $element['widgetType'] ?? '' ) ) {
			return true;
		}

		if ( ! empty( $element['elements'] ) && proenem_issue_110_contains_widget( $element['elements'], $widget_type ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Insert the shared navbar in the container that owns the home widgets.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @return bool Whether the tree changed.
 */
function proenem_issue_110_ensure_navbar( &$elements ) {
	if ( proenem_issue_110_contains_widget( $elements, 'pro_navbar' ) ) {
		return false;
	}

	foreach ( $elements as &$element ) {
		if ( empty( $element['elements'] ) || ! is_array( $element['elements'] ) ) {
			continue;
		}

		$owns_home_hero = false;

		foreach ( $element['elements'] as $child ) {
			if ( 'pro_home_hero' === ( $child['widgetType'] ?? '' ) ) {
				$owns_home_hero = true;
				break;
			}
		}

		if ( $owns_home_hero ) {
			array_unshift(
				$element['elements'],
				array(
					'id'         => 'issue110navbar',
					'elType'     => 'widget',
					'widgetType' => 'pro_navbar',
					'isInner'    => false,
					'settings'   => array(
						'mode'               => 'menu',
						'menu_id'            => 0,
						'aria_label'         => __( 'Navegação da home', 'proenem-wordpress-theme' ),
						'mobile_cta_enabled' => 'yes',
						'mobile_cta_label'   => __( 'Criar conta grátis', 'proenem-wordpress-theme' ),
						'mobile_cta_url'     => array(
							'url' => proenem_get_home_cta_destination( 'signup' ),
						),
					),
					'elements'   => array(),
				)
			);

			return true;
		}

		if ( proenem_issue_110_ensure_navbar( $element['elements'] ) ) {
			return true;
		}
	}
	unset( $element );

	return false;
}

/**
 * Upgrade a legacy Elementor URL setting.
 *
 * @param array<string,mixed> $settings Elementor widget settings.
 * @param string              $key Setting key.
 * @param string              $intent Conversion intent.
 * @return bool Whether the setting changed.
 */
function proenem_issue_110_upgrade_url( &$settings, $key, $intent ) {
	$current = $settings[ $key ] ?? array();
	$updated = proenem_upgrade_home_cta_link( $current, $intent );

	if ( $updated === $current ) {
		return false;
	}

	$settings[ $key ] = $updated;

	return true;
}

/**
 * Upgrade home conversion settings throughout an Elementor tree.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @return bool Whether the tree changed.
 */
function proenem_issue_110_upgrade_elementor_tree( &$elements ) {
	$changed = false;

	foreach ( $elements as &$element ) {
		$widget_type = $element['widgetType'] ?? '';
		$settings    = &$element['settings'];

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( 'pro_navbar' === $widget_type ) {
			$navbar_settings = array(
				'mode'               => 'menu',
				'mobile_cta_enabled' => 'yes',
				'mobile_cta_label'   => __( 'Criar conta grátis', 'proenem-wordpress-theme' ),
			);

			foreach ( $navbar_settings as $key => $value ) {
				if ( ( $settings[ $key ] ?? null ) !== $value ) {
					$settings[ $key ] = $value;
					$changed          = true;
				}
			}

			$changed = proenem_issue_110_upgrade_url( $settings, 'mobile_cta_url', 'signup' ) || $changed;
		}

		$signup_widgets = array(
			'pro_home_action_bar' => 'primary_button_url',
			'pro_home_pain'       => 'button_url',
			'pro_home_pillars'    => 'button_url',
		);

		if ( isset( $signup_widgets[ $widget_type ] ) ) {
			$url_key = $signup_widgets[ $widget_type ];
			$changed = proenem_issue_110_upgrade_url( $settings, $url_key, 'signup' ) || $changed;

			$label_key = 'pro_home_action_bar' === $widget_type ? 'primary_button_label' : 'button_label';

			if ( empty( $settings[ $label_key ] ) || __( 'Começar grátis', 'proenem-wordpress-theme' ) === $settings[ $label_key ] ) {
				$settings[ $label_key ] = __( 'Criar conta grátis', 'proenem-wordpress-theme' );
				$changed                = true;
			}
		}

		if ( 'pro_home_questions' === $widget_type ) {
			$changed = proenem_issue_110_upgrade_url( $settings, 'button_url', 'questions' ) || $changed;
		}

		if ( 'pro_home_pricing' === $widget_type && ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ) {
			foreach ( $settings['plans'] as &$plan ) {
				$plan_name = $plan['name'] ?? '';

				if ( __( 'Grátis', 'proenem-wordpress-theme' ) === $plan_name ) {
					$changed = proenem_issue_110_upgrade_url( $plan, 'button_url', 'signup' ) || $changed;
				}

				if ( __( 'Método PRO', 'proenem-wordpress-theme' ) === $plan_name ) {
					$changed = proenem_issue_110_upgrade_url( $plan, 'button_url', 'method_pro' ) || $changed;
				}

				if ( in_array( $plan_name, array( __( 'Método PRO Avançado', 'proenem-wordpress-theme' ), __( 'Pro Medicina', 'proenem-wordpress-theme' ) ), true ) ) {
					$changed = proenem_issue_110_upgrade_url( $plan, 'button_url', 'advanced' ) || $changed;
				}
			}
			unset( $plan );
		}

		if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$changed = proenem_issue_110_upgrade_elementor_tree( $element['elements'] ) || $changed;
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

	if ( ! is_array( $data ) || ! proenem_issue_110_contains_widget( $data, 'pro_home_hero' ) ) {
		continue;
	}

	$changed = proenem_issue_110_ensure_navbar( $data );
	$changed = proenem_issue_110_upgrade_elementor_tree( $data ) || $changed;

	if ( ! $changed ) {
		continue;
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	++$elementor_updates;
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

proenem_issue_110_log( sprintf( 'Elementor home pages updated: %d', $elementor_updates ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Home conversion data is synchronized.' );
}
