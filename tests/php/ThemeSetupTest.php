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
}
