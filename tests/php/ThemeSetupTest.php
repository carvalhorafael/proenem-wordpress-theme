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
		$this->assertArrayHasKey( 'footer-subjects', $locations );
		$this->assertArrayHasKey( 'footer-answer-keys', $locations );
		$this->assertArrayHasKey( 'footer-tools', $locations );
		$this->assertArrayHasKey( 'footer-classes', $locations );
		$this->assertArrayHasKey( 'footer-legal', $locations );
		$this->assertArrayNotHasKey( 'footer', $locations );
	}

	/**
	 * Footer widget areas should be registered for configurable mixed content.
	 *
	 * @return void
	 */
	public function test_footer_widget_areas_are_registered() {
		global $wp_registered_sidebars;

		$this->assertArrayHasKey( 'footer-social', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-trust', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-payment', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-company-info', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-1', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-2', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-3', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-bottom', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'home-footer-platform', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'home-footer-support', $wp_registered_sidebars );
	}

	/**
	 * Footer columns should expose the expected configurable menu locations.
	 *
	 * @return void
	 */
	public function test_footer_menu_columns_are_declared() {
		$this->assertSame(
			array(
				'footer-subjects'    => 'Matérias lecionadas',
				'footer-answer-keys' => 'Gabaritos',
				'footer-tools'       => 'Ferramentas',
			),
			proenem_get_footer_menu_columns()
		);
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
		$this->assertFileExists( PROENEM_THEME_DIR . '/single-material_gratuito.php' );
	}

	/**
	 * Navbar actions should preserve direct submenu items from WordPress menus.
	 *
	 * @return void
	 */
	public function test_navbar_actions_include_submenu_items() {
		$menu_id = wp_create_nav_menu( 'Proenem test menu' );
		$parent  = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'   => 'Entrar',
				'menu-item-url'     => '#entrar',
				'menu-item-status'  => 'publish',
				'menu-item-classes' => 'pen-navbar-action pen-navbar-action-secondary',
			)
		);

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => 'Acessar Proenem',
				'menu-item-url'       => 'https://app.proenem.com.br/',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => 'Acessar Promedicina',
				'menu-item-url'       => 'https://app.promedicina.com.br/',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
			)
		);

		$navigation = proenem_get_primary_navigation_items( 'site', $menu_id );

		$this->assertCount( 1, $navigation['actions'] );
		$this->assertSame( 'Entrar', $navigation['actions'][0]['label'] );
		$this->assertCount( 2, $navigation['actions'][0]['children'] );
		$this->assertSame( 'Acessar Proenem', $navigation['actions'][0]['children'][0]['label'] );
		$this->assertSame( 'Acessar Promedicina', $navigation['actions'][0]['children'][1]['label'] );
	}

	/**
	 * Navbar should not invent items when the primary menu location is empty.
	 *
	 * @return void
	 */
	public function test_navbar_does_not_fallback_when_location_is_empty() {
		set_theme_mod( 'nav_menu_locations', array() );

		$navigation = proenem_get_primary_navigation_items( 'site' );

		$this->assertSame( array(), $navigation['links'] );
		$this->assertSame( array(), $navigation['actions'] );
	}

	/**
	 * Navbar actions should only come from menu items with action classes.
	 *
	 * @return void
	 */
	public function test_navbar_does_not_fallback_when_action_classes_are_missing() {
		$menu_id = wp_create_nav_menu( 'Proenem menu without actions' );

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Planos',
				'menu-item-url'    => '#planos',
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Entrar',
				'menu-item-url'    => '#entrar',
				'menu-item-status' => 'publish',
			)
		);

		$navigation = proenem_get_primary_navigation_items( 'site', $menu_id );

		$this->assertSame( array(), $navigation['actions'] );
		$this->assertSame( 'Planos', $navigation['links'][0]['label'] );
		$this->assertSame( 'Entrar', $navigation['links'][1]['label'] );
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
	 * Elementor widgets should expose stable technical names.
	 *
	 * @return void
	 */
	public function test_elementor_sales_widget_contracts_are_declared() {
		$this->assertSame(
			array(
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
