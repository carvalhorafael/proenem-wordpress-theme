<?php
/**
 * Seed a page rendering every home Elementor widget, to verify the markup that
 * the theme templates do not exercise.
 *
 * The home is rendered by page-templates/home.php, not by the widgets, so the
 * widget path has no other coverage.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/seed-home-widgets-check.php
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Report an operational message.
 *
 * @param string $message Message to report.
 * @return void
 */
function proenem_home_check_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

if ( ! post_type_exists( 'sales_page' ) ) {
	proenem_home_check_log( 'O CPT sales_page nao esta disponivel.' );
	return;
}

$widgets = array(
	'pro_home_hero',
	'pro_home_action_bar',
	'pro_home_marquee',
	'pro_home_pillars',
	'pro_home_proof',
	'pro_home_pain',
	'pro_home_platform',
	'pro_home_questions',
	'pro_home_pricing',
	'pro_home_testimonials',
	'pro_home_schools',
	'pro_home_final_cta',
	'pro_home_faq',
);

$elements = array();

foreach ( $widgets as $index => $widget_type ) {
	$elements[] = array(
		'id'         => 'hw' . str_pad( (string) $index, 6, '0', STR_PAD_LEFT ),
		'elType'     => 'widget',
		'widgetType' => $widget_type,
		'settings'   => array(),
		'elements'   => array(),
	);
}

$data = array(
	array(
		'id'       => 'hwcontainer',
		'elType'   => 'container',
		'settings' => array(
			'html_tag'      => 'main',
			'content_width' => 'full',
		),
		'elements' => $elements,
	),
);

$slug     = 'checagem-widgets-home';
$existing = get_page_by_path( $slug, OBJECT, 'sales_page' );
$postarr  = array(
	'post_type'    => 'sales_page',
	'post_status'  => 'publish',
	'post_title'   => 'Checagem widgets da home',
	'post_name'    => $slug,
	'post_content' => '',
);

if ( $existing instanceof WP_Post ) {
	$postarr['ID']            = $existing->ID;
	$postarr['page_template'] = '';
	$page_id                  = wp_update_post( $postarr, true );
} else {
	$page_id = wp_insert_post( $postarr, true );
}

if ( is_wp_error( $page_id ) ) {
	proenem_home_check_log( 'Falha ao criar a pagina: ' . $page_id->get_error_message() );
	return;
}

update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-post' );
update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
delete_post_meta( $page_id, '_elementor_css' );
delete_post_meta( $page_id, '_elementor_element_cache' );
delete_post_meta( $page_id, '_elementor_page_assets' );

proenem_home_check_log( sprintf( '%d widgets da home em %s', count( $widgets ), get_permalink( $page_id ) ) );
