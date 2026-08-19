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
						'mobile_cta_label'   => __( 'Ver plano e preço', 'proenem-wordpress-theme' ),
						'mobile_cta_url'     => array(
							'url' => proenem_get_home_cta_destination( 'plans' ),
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
 * Set a canonical URL while preserving Elementor link metadata.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @param string              $key Setting key.
 * @param string              $intent Conversion intent.
 * @return bool Whether the setting changed.
 */
function proenem_issue_110_set_url( &$settings, $key, $intent ) {
	$current   = $settings[ $key ] ?? array();
	$canonical = proenem_get_home_cta_destination( $intent );
	$updated   = is_array( $current ) ? array_merge( $current, array( 'url' => $canonical ) ) : $canonical;

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
				'mobile_cta_label'   => __( 'Ver plano e preço', 'proenem-wordpress-theme' ),
			);

			foreach ( $navbar_settings as $key => $value ) {
				if ( ( $settings[ $key ] ?? null ) !== $value ) {
					$settings[ $key ] = $value;
					$changed          = true;
				}
			}

			$changed = proenem_issue_110_set_url( $settings, 'mobile_cta_url', 'plans' ) || $changed;
		}

		$conversion_widgets = array(
			'pro_home_action_bar' => array(
				'label'     => __( 'Conheça a Turma Intensiva', 'proenem-wordpress-theme' ),
				'label_key' => 'primary_button_label',
				'url_key'   => 'primary_button_url',
			),
			'pro_home_pain'       => array(
				'badge'     => __( '7 dias de garantia', 'proenem-wordpress-theme' ),
				'label'     => __( 'Comece agora', 'proenem-wordpress-theme' ),
				'label_key' => 'button_label',
				'url_key'   => 'button_url',
			),
			'pro_home_pillars'    => array(
				'label'     => __( 'Ver a Turma Intensiva', 'proenem-wordpress-theme' ),
				'label_key' => 'button_label',
				'url_key'   => 'button_url',
			),
			'pro_home_questions'  => array(
				'badge'     => __( 'Ver plano e preço', 'proenem-wordpress-theme' ),
				'label'     => __( 'Conheça a Turma Intensiva', 'proenem-wordpress-theme' ),
				'label_key' => 'button_label',
				'url_key'   => 'button_url',
			),
		);

		if ( isset( $conversion_widgets[ $widget_type ] ) ) {
			$conversion = $conversion_widgets[ $widget_type ];
			$changed    = proenem_issue_110_set_url( $settings, $conversion['url_key'], 'plans' ) || $changed;

			if ( ( $settings[ $conversion['label_key'] ] ?? '' ) !== $conversion['label'] ) {
				$settings[ $conversion['label_key'] ] = $conversion['label'];
				$changed                = true;
			}

			if ( isset( $conversion['badge'] ) && ( $settings['button_badge'] ?? '' ) !== $conversion['badge'] ) {
				$settings['button_badge'] = $conversion['badge'];
				$changed                  = true;
			}
		}

		if ( 'pro_home_pricing' === $widget_type && ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ) {
			$pricing_settings = array(
				'body'             => __( 'Turma Intensiva 2026: cronograma personalizado, aulas, redação, simulados e mais de 60 mil questões. Comece agora com 7 dias de garantia.', 'proenem-wordpress-theme' ),
				'title_emphasis'   => __( 'até a prova.', 'proenem-wordpress-theme' ),
				'title_line_1'     => __( 'Sua preparação completa.', 'proenem-wordpress-theme' ),
				'title_line_2'     => __( 'Do diagnóstico', 'proenem-wordpress-theme' ),
			);

			foreach ( $pricing_settings as $key => $value ) {
				if ( ( $settings[ $key ] ?? '' ) !== $value ) {
					$settings[ $key ] = $value;
					$changed          = true;
				}
			}

			$available_plans = array();

			foreach ( $settings['plans'] as &$plan ) {
				$plan_name = $plan['name'] ?? '';

				if ( __( 'Grátis', 'proenem-wordpress-theme' ) === $plan_name || ! empty( $plan['free'] ) ) {
					$changed = true;
					continue;
				}

				if ( in_array( $plan_name, array( __( 'Método PRO', 'proenem-wordpress-theme' ), __( 'Turma Intensiva 2026', 'proenem-wordpress-theme' ) ), true ) ) {
					$intensive_settings = array(
						'button_label'  => __( 'Quero a Turma Intensiva', 'proenem-wordpress-theme' ),
						'features'      => __( "Cronograma semanal\nCorreção de redação\nAulas e pdfs com os melhores professores\nSimulados corrigidos no padrão ENEM\nRevisões inteligentes por matéria\nMais de 50 mil questões para praticar\n6 meses de acesso", 'proenem-wordpress-theme' ),
						'guarantee'     => __( 'Garantia de 7 dias', 'proenem-wordpress-theme' ),
						'name'          => __( 'Turma Intensiva 2026', 'proenem-wordpress-theme' ),
						'price_details' => __( 'ou R$ 306,90 à vista', 'proenem-wordpress-theme' ),
					);

					foreach ( $intensive_settings as $key => $value ) {
						if ( ( $plan[ $key ] ?? '' ) !== $value ) {
							$plan[ $key ] = $value;
							$changed       = true;
						}
					}

					$changed = proenem_issue_110_set_url( $plan, 'button_url', 'method_pro' ) || $changed;
				}

				if ( in_array( $plan_name, array( __( 'Método PRO Avançado', 'proenem-wordpress-theme' ), __( 'Pro Medicina', 'proenem-wordpress-theme' ) ), true ) ) {
					$changed = proenem_issue_110_upgrade_url( $plan, 'button_url', 'advanced' ) || $changed;
				}

				$available_plans[] = $plan;
			}
			unset( $plan );

			$settings['plans'] = $available_plans;
		}

		if ( 'pro_home_faq' === $widget_type && ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) {
			$updated_items = array();

			foreach ( $settings['items'] as $item ) {
				$question = $item['question'] ?? '';

				if ( __( 'Posso começar de graça?', 'proenem-wordpress-theme' ) === $question ) {
					$changed = true;
					continue;
				}

				if ( __( 'O que é o Método PRO?', 'proenem-wordpress-theme' ) === $question ) {
					$item['question'] = __( 'O que é a Turma Intensiva 2026?', 'proenem-wordpress-theme' );
					$changed          = true;
				}

				if ( __( 'Qual a diferença entre os planos?', 'proenem-wordpress-theme' ) === $question ) {
					$item['question'] = __( 'O que está incluído na Turma Intensiva?', 'proenem-wordpress-theme' );
					$item['answer']   = __( 'Diagnóstico inicial, nota prevista, banco de mais de 60 mil questões, cronograma personalizado até a prova, duas correções de redação mensais, aulas gravadas, PDFs completos e simulados com nota TRI.', 'proenem-wordpress-theme' );
					$changed          = true;
				}

				$updated_items[] = $item;
			}

			$settings['items'] = $updated_items;
		}

		if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$changed = proenem_issue_110_upgrade_elementor_tree( $element['elements'] ) || $changed;
		}

		unset( $settings );
	}
	unset( $element );

	return $changed;
}

/**
 * Update the known primary-menu conversion item without touching custom links.
 *
 * @return int Number of updated items.
 */
function proenem_issue_110_sync_primary_menu() {
	$locations = get_nav_menu_locations();
	$menu_id   = absint( $locations['primary'] ?? 0 );

	if ( ! $menu_id ) {
		return 0;
	}

	$updated = 0;

	foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $item ) {
		if ( ! in_array( $item->title, array( 'Comece grátis', 'Criar conta grátis' ), true ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'         => $item->ID,
				'post_title' => __( 'Conheça a Turma Intensiva', 'proenem-wordpress-theme' ),
			)
		);
		update_post_meta( $item->ID, '_menu_item_url', proenem_get_home_cta_destination( 'plans' ) );
		++$updated;
	}

	return $updated;
}

$elementor_updates = 0;
$menu_updates      = proenem_issue_110_sync_primary_menu();
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
proenem_issue_110_log( sprintf( 'Primary menu items updated: %d', $menu_updates ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Home conversion data is synchronized.' );
}
