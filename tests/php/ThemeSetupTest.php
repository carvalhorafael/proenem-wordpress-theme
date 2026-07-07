<?php
/**
 * Theme setup tests.
 *
 * @package Proenem
 */

/**
 * Tests core theme setup contracts.
 */
class ThemeSetupTest extends WP_UnitTestCase {
	/**
	 * Theme supports should be registered.
	 *
	 * @return void
	 */
	public function test_theme_supports_are_registered() {
		$this->assertTrue( current_theme_supports( 'title-tag' ) );
		$this->assertTrue( current_theme_supports( 'post-thumbnails' ) );
		$this->assertTrue( current_theme_supports( 'editor-styles' ) );
	}

	/**
	 * Navigation locations should be registered.
	 *
	 * @return void
	 */
	public function test_navigation_locations_are_registered() {
		$locations = get_registered_nav_menus();

		$this->assertArrayHasKey( 'primary', $locations );
		$this->assertArrayHasKey( 'footer', $locations );
	}

	/**
	 * Free Materials fallbacks should expose the expected portable identifiers.
	 *
	 * @return void
	 */
	public function test_free_materials_contract_fallbacks_are_available() {
		$this->assertSame( 'material_gratuito', proenem_get_free_materials_post_type() );
		$this->assertSame( 'material_categoria', proenem_get_free_materials_taxonomy() );
		$this->assertSame( '_executive_signal_material_capture_label', proenem_get_free_materials_cta_label_meta_key() );
		$this->assertSame( '_brevo_leads_capture_delivery_url', proenem_get_free_materials_delivery_url_meta_key() );
	}

	/**
	 * Testimonials fallbacks should expose the expected portable identifiers.
	 *
	 * @return void
	 */
	public function test_testimonials_contract_fallbacks_are_available() {
		$this->assertSame( 'depoimento', proenem_get_testimonials_post_type() );
		$this->assertSame( 'depoimento_categoria', proenem_get_testimonials_taxonomy() );
		$this->assertSame( '_testimonials_video_url', proenem_get_testimonials_video_url_meta_key() );
	}

	/**
	 * The free materials listing should be exposed as an explicit page template.
	 *
	 * @return void
	 */
	public function test_free_materials_page_template_exists() {
		$this->assertFileExists( PROENEM_THEME_DIR . '/page-templates/free-materials.php' );
	}

	/**
	 * The testimonials listing should be exposed as an explicit page template.
	 *
	 * @return void
	 */
	public function test_testimonials_page_template_exists() {
		$this->assertFileExists( PROENEM_THEME_DIR . '/page-templates/testimonials.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/single-depoimento.php' );
	}

	/**
	 * Required plugin dependency contracts should be declared by plugin file.
	 *
	 * @return void
	 */
	public function test_required_plugin_dependencies_are_declared() {
		$required_plugins = proenem_get_required_plugins();

		$this->assertSame( 'free-materials/free-materials.php', $required_plugins['free-materials']['file'] );
		$this->assertSame( 'testimonials/testimonials.php', $required_plugins['testimonials']['file'] );
		$this->assertSame( 'crm-leads-capture/crm-leads-capture.php', $required_plugins['crm-leads-capture']['file'] );
	}

	/**
	 * Sales page Elementor widgets should expose stable technical names.
	 *
	 * @return void
	 */
	public function test_elementor_sales_widget_contracts_are_declared() {
		$this->assertSame(
			array(
				'Proenem_Elementor_Navbar_Widget',
				'Proenem_Elementor_Offer_Hero_Widget',
				'Proenem_Elementor_Offer_Countdown_Widget',
				'Proenem_Elementor_Pricing_Grid_Widget',
				'Proenem_Elementor_Pricing_Card_Widget',
				'Proenem_Elementor_Benefits_List_Widget',
				'Proenem_Elementor_Plans_Comparison_Widget',
				'Proenem_Elementor_Cta_Widget',
				'Proenem_Elementor_Faq_Widget',
			),
			proenem_get_elementor_sales_widget_classes()
		);
	}

	/**
	 * Active required plugins should not be reported as unmet.
	 *
	 * @return void
	 */
	public function test_active_required_plugins_are_not_reported_as_unmet() {
		$previous_active_plugins = get_option( 'active_plugins', array() );

		update_option(
			'active_plugins',
			array(
				'free-materials/free-materials.php',
				'testimonials/testimonials.php',
				'crm-leads-capture/crm-leads-capture.php',
			)
		);

		$this->assertSame( array(), proenem_get_unmet_required_plugins() );

		update_option( 'active_plugins', $previous_active_plugins );
	}
}
