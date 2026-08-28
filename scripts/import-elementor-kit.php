<?php
/**
 * Import an Elementor template kit from docs/elementor into a sales page.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/import-elementor-kit.php <arquivo.json> <slug>
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
function proenem_kit_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

$positional = array_values( (array) ( $args ?? array() ) );
$kit_file   = isset( $positional[0] ) ? (string) $positional[0] : '';
$slug       = isset( $positional[1] ) ? sanitize_title( (string) $positional[1] ) : '';

if ( '' === $kit_file || '' === $slug ) {
	proenem_kit_log( 'Uso: wp eval-file .../import-elementor-kit.php <arquivo.json> <slug>' );
	return;
}

$path = PROENEM_THEME_DIR . '/docs/elementor/' . basename( $kit_file );

if ( ! file_exists( $path ) ) {
	proenem_kit_log( 'Kit nao encontrado: ' . $path );
	return;
}

$kit = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

if ( ! is_array( $kit ) || empty( $kit['content'] ) ) {
	proenem_kit_log( 'Kit invalido ou sem conteudo.' );
	return;
}

if ( ! post_type_exists( 'sales_page' ) ) {
	proenem_kit_log( 'O CPT sales_page nao esta disponivel.' );
	return;
}

$existing = get_page_by_path( $slug, OBJECT, 'sales_page' );
$postarr  = array(
	'post_type'    => 'sales_page',
	'post_status'  => 'publish',
	'post_title'   => $kit['title'] ?? $slug,
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
	proenem_kit_log( 'Falha ao criar a pagina: ' . $page_id->get_error_message() );
	return;
}

update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-post' );
update_post_meta( $page_id, '_wp_page_template', $kit['page_settings']['template'] ?? 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $kit['content'] ) ) );
delete_post_meta( $page_id, '_elementor_css' );
delete_post_meta( $page_id, '_elementor_element_cache' );
delete_post_meta( $page_id, '_elementor_page_assets' );

proenem_kit_log( 'Kit importado: ' . get_permalink( $page_id ) );
