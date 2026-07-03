<?php
/**
 * GitHub updater tests.
 *
 * @package Proenem
 */

/**
 * Tests the GitHub release updater helpers.
 */
class UpdaterTest extends WP_UnitTestCase {
	/**
	 * Newer releases should be converted to WordPress update responses.
	 *
	 * @return void
	 */
	public function test_newer_release_generates_update_payload() {
		$update = proenem_theme_update_from_release(
			array(
				'tag_name' => 'v0.2.0',
				'html_url' => 'https://github.com/carvalhorafael/proenem-wordpress-theme/releases/tag/v0.2.0',
				'assets'   => array(
					array(
						'name'                 => 'proenem-wordpress-theme.zip',
						'browser_download_url' => 'https://github.com/carvalhorafael/proenem-wordpress-theme/releases/download/v0.2.0/proenem-wordpress-theme.zip',
					),
				),
			),
			'0.1.0'
		);

		$this->assertIsArray( $update );
		$this->assertSame( 'proenem-wordpress-theme', $update['theme'] );
		$this->assertSame( '0.2.0', $update['new_version'] );
		$this->assertSame( 'https://github.com/carvalhorafael/proenem-wordpress-theme/releases/download/v0.2.0/proenem-wordpress-theme.zip', $update['package'] );
	}

	/**
	 * Older or equal releases should not trigger updates.
	 *
	 * @return void
	 */
	public function test_equal_release_does_not_generate_update_payload() {
		$update = proenem_theme_update_from_release(
			array(
				'tag_name' => 'v0.1.0',
				'assets'   => array(
					array(
						'name'                 => 'proenem-wordpress-theme.zip',
						'browser_download_url' => 'https://example.com/proenem-wordpress-theme.zip',
					),
				),
			),
			'0.1.0'
		);

		$this->assertFalse( $update );
	}

	/**
	 * Releases without the packaged theme ZIP should not trigger updates.
	 *
	 * @return void
	 */
	public function test_release_without_expected_zip_does_not_generate_update_payload() {
		$update = proenem_theme_update_from_release(
			array(
				'tag_name' => 'v0.2.0',
				'assets'   => array(
					array(
						'name'                 => 'other.zip',
						'browser_download_url' => 'https://example.com/other.zip',
					),
				),
			),
			'0.1.0'
		);

		$this->assertFalse( $update );
	}

	/**
	 * The update filter should ignore unrelated themes.
	 *
	 * @return void
	 */
	public function test_update_filter_ignores_other_theme_stylesheets() {
		$update = array( 'existing' => 'value' );

		$this->assertSame(
			$update,
			proenem_filter_github_theme_update(
				$update,
				array( 'UpdateURI' => PROENEM_THEME_UPDATE_URI ),
				'other-theme',
				array()
			)
		);
	}
}
