<?php
/**
 * Elementor landing page widget base.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared base for the generic Proenem landing page widgets.
 *
 * The widgets that extend this class are meant to be composed freely on any
 * sales page. The home widgets in `Proenem_Elementor_Home_Widget_Base` are not:
 * they carry the home copy inside the control structure and stay exclusive to
 * the home.
 */
abstract class Proenem_Elementor_Lp_Widget_Base extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'proenem-lp' );
	}

	/**
	 * Get shared widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'pro', 'lp', 'landing', 'campanha' );
	}
}
