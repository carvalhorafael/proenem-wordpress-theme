<?php
/**
 * Create the published page used by the content layout browser test.
 *
 * @package Proenem
 */

/**
 * Add a published custom link to the E2E navigation menu.
 *
 * @param int                 $menu_id Menu term ID.
 * @param array<string,mixed> $args    Menu item arguments.
 * @return int
 * @throws RuntimeException When WordPress cannot create the menu item.
 */
function proenem_e2e_add_menu_item( $menu_id, $args ) {
	$item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array_merge(
			array(
				'menu-item-status' => 'publish',
			),
			$args
		)
	);

	if ( is_wp_error( $item_id ) ) {
		throw new RuntimeException( esc_html( $item_id->get_error_message() ) );
	}

	return (int) $item_id;
}

$menu_name    = 'Proenem E2E Primary Navigation';
$fixture_menu = wp_get_nav_menu_object( $menu_name );

if ( $fixture_menu ) {
	$menu_id            = (int) $fixture_menu->term_id;
	$fixture_menu_items = wp_get_nav_menu_items( $menu_id );

	if ( is_array( $fixture_menu_items ) ) {
		foreach ( $fixture_menu_items as $menu_item ) {
			wp_delete_post( $menu_item->ID, true );
		}
	}
} else {
	$menu_id = wp_create_nav_menu( $menu_name );

	if ( is_wp_error( $menu_id ) ) {
		throw new RuntimeException( esc_html( $menu_id->get_error_message() ) );
	}
}

proenem_e2e_add_menu_item(
	$menu_id,
	array(
		'menu-item-title'   => 'Comece grátis',
		'menu-item-url'     => 'https://estude.proenem.com.br/signup',
		'menu-item-classes' => 'pen-navbar-action pen-navbar-action-primary',
	)
);

$login_item_id = proenem_e2e_add_menu_item(
	$menu_id,
	array(
		'menu-item-title'   => 'Entrar',
		'menu-item-url'     => '#',
		'menu-item-classes' => 'pen-navbar-action pen-navbar-action-secondary',
	)
);

proenem_e2e_add_menu_item(
	$menu_id,
	array(
		'menu-item-title'     => 'Acessar Proenem',
		'menu-item-url'       => 'https://app.proenem.com.br/',
		'menu-item-parent-id' => $login_item_id,
	)
);
proenem_e2e_add_menu_item(
	$menu_id,
	array(
		'menu-item-title'     => 'Acessar Promedicina',
		'menu-item-url'       => 'https://app.promedicina.com.br/',
		'menu-item-parent-id' => $login_item_id,
	)
);

$menu_locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
$menu_locations['primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $menu_locations );

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
