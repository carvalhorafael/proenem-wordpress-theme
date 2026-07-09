<?php
/**
 * Elementor sales page widget registration.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the Elementor widget classes exposed by the theme.
 *
 * @return string[]
 */
function proenem_get_elementor_sales_widget_classes() {
	return array(
		'Proenem_Elementor_Navbar_Widget',
		'Proenem_Elementor_Footer_Widget',
		'Proenem_Elementor_Offer_Hero_Widget',
		'Proenem_Elementor_Offer_Countdown_Widget',
		'Proenem_Elementor_Pricing_Grid_Widget',
		'Proenem_Elementor_Pricing_Card_Widget',
		'Proenem_Elementor_Benefits_List_Widget',
		'Proenem_Elementor_Plans_Comparison_Widget',
		'Proenem_Elementor_Cta_Widget',
		'Proenem_Elementor_Faq_Widget',
		'Proenem_Elementor_Home_Hero_Widget',
		'Proenem_Elementor_Home_Action_Bar_Widget',
		'Proenem_Elementor_Home_Marquee_Widget',
		'Proenem_Elementor_Home_Pillars_Widget',
		'Proenem_Elementor_Home_Proof_Widget',
		'Proenem_Elementor_Home_Pain_Widget',
		'Proenem_Elementor_Home_Platform_Widget',
		'Proenem_Elementor_Home_Questions_Widget',
		'Proenem_Elementor_Home_Pricing_Widget',
		'Proenem_Elementor_Home_Testimonials_Widget',
		'Proenem_Elementor_Home_Schools_Widget',
		'Proenem_Elementor_Home_Final_Cta_Widget',
		'Proenem_Elementor_Home_Faq_Widget',
	);
}

/**
 * Register the Proenem Elementor category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 * @return void
 */
function proenem_register_elementor_sales_category( $elements_manager ) {
	$elements_manager->add_category(
		'proenem-sales',
		array(
			'title' => esc_html__( 'Proenem', 'proenem-wordpress-theme' ),
			'icon'  => 'fa fa-plug',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'proenem_register_elementor_sales_category' );

/**
 * Register Proenem Elementor sales widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function proenem_register_elementor_sales_widgets( $widgets_manager ) {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return;
	}

	require_once PROENEM_THEME_DIR . '/inc/class-proenem-elementor-sales-widget-base.php';
	require_once PROENEM_THEME_DIR . '/inc/class-proenem-elementor-home-widget-base.php';

	foreach ( proenem_get_elementor_sales_widget_classes() as $class_name ) {
		if ( class_exists( $class_name ) ) {
			$widgets_manager->register( new $class_name() );
		}
	}
}
add_action( 'elementor/widgets/register', 'proenem_register_elementor_sales_widgets' );
