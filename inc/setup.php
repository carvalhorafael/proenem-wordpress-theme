<?php
/**
 * Theme setup.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and navigation areas.
 *
 * @return void
 */
function proenem_theme_setup() {
	load_theme_textdomain( 'proenem-wordpress-theme', PROENEM_THEME_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'html5',
		array(
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'search-form',
			'script',
			'style',
		)
	);

	add_editor_style( proenem_get_editor_stylesheets() );

	register_nav_menus(
		array(
			'primary'            => esc_html__( 'Primary menu', 'proenem-wordpress-theme' ),
			'footer'             => esc_html__( 'Footer menu', 'proenem-wordpress-theme' ),
			'footer-subjects'    => esc_html__( 'Rodapé - Matérias lecionadas', 'proenem-wordpress-theme' ),
			'footer-answer-keys' => esc_html__( 'Rodapé - Gabaritos', 'proenem-wordpress-theme' ),
			'footer-tools'       => esc_html__( 'Rodapé - Ferramentas e simulados', 'proenem-wordpress-theme' ),
			'footer-classes'     => esc_html__( 'Rodapé - Turmas', 'proenem-wordpress-theme' ),
			'footer-legal'       => esc_html__( 'Rodapé - Links legais', 'proenem-wordpress-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'proenem_theme_setup' );

/**
 * Register editor block styles.
 *
 * @return void
 */
function proenem_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'proenem-primary',
			'label' => __( 'Proenem primary', 'proenem-wordpress-theme' ),
		)
	);

	register_block_style(
		'core/quote',
		array(
			'name'  => 'proenem-panel',
			'label' => __( 'Proenem panel', 'proenem-wordpress-theme' ),
		)
	);
}
add_action( 'init', 'proenem_register_block_styles' );

/**
 * Register widget areas.
 *
 * @return void
 */
function proenem_register_widget_areas() {
	$widget_areas = array(
		'footer-1'             => __( 'Footer column 1', 'proenem-wordpress-theme' ),
		'footer-2'             => __( 'Footer column 2', 'proenem-wordpress-theme' ),
		'footer-3'             => __( 'Footer column 3', 'proenem-wordpress-theme' ),
		'footer-bottom'        => __( 'Footer bottom', 'proenem-wordpress-theme' ),
		'footer-social'        => __( 'Rodapé - Redes sociais', 'proenem-wordpress-theme' ),
		'footer-trust'         => __( 'Rodapé - Selos de confiança', 'proenem-wordpress-theme' ),
		'footer-payment'       => __( 'Rodapé - Formas de pagamento', 'proenem-wordpress-theme' ),
		'footer-company-info'  => __( 'Rodapé - Dados da empresa', 'proenem-wordpress-theme' ),
		'home-footer-platform' => __( 'Home footer - Plataforma', 'proenem-wordpress-theme' ),
		'home-footer-support'  => __( 'Home footer - Suporte', 'proenem-wordpress-theme' ),
	);

	foreach ( $widget_areas as $id => $name ) {
		register_sidebar(
			array(
				'id'            => $id,
				'name'          => $name,
				/* translators: %s: Widget area name. */
				'description'   => sprintf( __( 'Widgets added here appear in %s.', 'proenem-wordpress-theme' ), $name ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'proenem_register_widget_areas' );

/**
 * Add template-aware body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function proenem_body_classes( $classes ) {
	if ( is_front_page() || is_page_template( 'page-templates/home.php' ) ) {
		$classes[] = 'proenem-home-template';
	}

	if ( function_exists( 'proenem_is_free_materials_surface' ) && proenem_is_free_materials_surface() ) {
		$classes[] = 'proenem-free-materials-template';
	}

	if ( function_exists( 'proenem_is_testimonials_surface' ) && proenem_is_testimonials_surface() ) {
		$classes[] = 'proenem-testimonials-template';
	}

	return $classes;
}
add_filter( 'body_class', 'proenem_body_classes' );

/**
 * Get stylesheets loaded in the block editor canvas.
 *
 * @return string[]
 */
function proenem_get_editor_stylesheets() {
	if ( function_exists( 'proenem_vite_manifest_entry' ) && function_exists( 'proenem_vite_asset_uri' ) ) {
		$entry = proenem_vite_manifest_entry( 'src/editor.js' );

		if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
			return array_map( 'proenem_vite_asset_uri', $entry['css'] );
		}
	}

	return array( get_stylesheet_uri() );
}
