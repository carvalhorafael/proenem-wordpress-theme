<?php
/**
 * Vite helper tests.
 *
 * @package Proenem
 */

/**
 * Tests Vite manifest helpers.
 */
class ViteTest extends WP_UnitTestCase {
	/**
	 * Production manifest should be readable after build.
	 *
	 * @return void
	 */
	public function test_manifest_entry_is_readable() {
		$entry = proenem_vite_manifest_entry( 'src/main.js' );

		$this->assertIsArray( proenem_vite_manifest() );
		$this->assertIsArray( $entry );
		$this->assertArrayHasKey( 'file', $entry );
	}
}
