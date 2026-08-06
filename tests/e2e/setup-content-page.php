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
