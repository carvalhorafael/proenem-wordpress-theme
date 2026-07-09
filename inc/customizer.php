<?php
/**
 * Theme Customizer settings.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function proenem_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'proenem_footer_scripts_section',
		array(
			'title'       => esc_html__( 'Scripts do rodapé', 'proenem-wordpress-theme' ),
			'description' => esc_html__( 'Adicione códigos confiáveis que devem ser renderizados no rodapé do site, como botões de suporte ou atendimento.', 'proenem-wordpress-theme' ),
			'priority'    => 180,
		)
	);

	$wp_customize->add_setting(
		'proenem_footer_scripts',
		array(
			'default'           => '',
			'capability'        => 'unfiltered_html',
			'sanitize_callback' => 'proenem_sanitize_footer_scripts',
			'transport'         => 'refresh',
			'type'              => 'theme_mod',
		)
	);

	$wp_customize->add_control(
		'proenem_footer_scripts',
		array(
			'label'       => esc_html__( 'Código do rodapé', 'proenem-wordpress-theme' ),
			'description' => esc_html__( 'Cole aqui apenas scripts fornecidos por serviços confiáveis. O código será impresso antes do fechamento do body.', 'proenem-wordpress-theme' ),
			'section'     => 'proenem_footer_scripts_section',
			'settings'    => 'proenem_footer_scripts',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'proenem_customize_register' );

/**
 * Sanitize trusted footer scripts before saving.
 *
 * @param string $value Footer script markup.
 * @return string
 */
function proenem_sanitize_footer_scripts( $value ) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return '';
	}

	return is_string( $value ) ? $value : '';
}

/**
 * Render trusted footer scripts from the Customizer.
 *
 * @return void
 */
function proenem_render_footer_scripts() {
	$footer_scripts = get_theme_mod( 'proenem_footer_scripts', '' );

	if ( ! is_string( $footer_scripts ) || '' === trim( $footer_scripts ) ) {
		return;
	}

	echo "\n" . $footer_scripts . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted admin-provided footer scripts require raw script output.
}
add_action( 'wp_footer', 'proenem_render_footer_scripts', 20 );
