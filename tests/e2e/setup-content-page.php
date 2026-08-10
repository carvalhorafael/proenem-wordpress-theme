<?php
/**
 * Create the published page used by the content layout browser test.
 *
 * @package Proenem
 */

$fixture = get_page_by_path( 'e2e-content-layout', OBJECT, 'page' );

$postarr = array(
	'ID'           => $fixture ? $fixture->ID : 0,
	'post_type'    => 'page',
	'post_status'  => 'publish',
	'post_name'    => 'e2e-content-layout',
	'post_title'   => 'E2E Content Layout',
	'post_content' => '<!-- wp:paragraph --><p>Content layout fixture.</p><!-- /wp:paragraph -->',
);

$fixture_post_id = wp_insert_post( $postarr, true );

if ( is_wp_error( $fixture_post_id ) ) {
	throw new RuntimeException( esc_html( $fixture_post_id->get_error_message() ) );
}

$elementor_model_path = PROENEM_THEME_DIR . '/docs/elementor/proenem-home.json';
$elementor_model      = json_decode( (string) file_get_contents( $elementor_model_path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

if ( empty( $elementor_model['content'] ) || ! is_array( $elementor_model['content'] ) ) {
	throw new RuntimeException( 'The Elementor home fixture model is invalid.' );
}

$elementor_fixture = get_page_by_path( 'e2e-elementor-home', OBJECT, 'page' );
$elementor_post_id = wp_insert_post(
	array(
		'ID'           => $elementor_fixture ? $elementor_fixture->ID : 0,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => 'e2e-elementor-home',
		'post_title'   => 'E2E Elementor Home',
		'post_content' => '',
	),
	true
);

if ( is_wp_error( $elementor_post_id ) ) {
	throw new RuntimeException( esc_html( $elementor_post_id->get_error_message() ) );
}

update_post_meta( $elementor_post_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $elementor_post_id, '_elementor_template_type', 'wp-page' );
update_post_meta( $elementor_post_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $elementor_post_id, '_elementor_data', wp_slash( wp_json_encode( $elementor_model['content'] ) ) );
delete_post_meta( $elementor_post_id, '_elementor_css' );
