<?php
/**
 * Run Theme Check against the development theme.
 *
 * @package Proenem
 */

$theme_check_dir = WP_PLUGIN_DIR . '/theme-check';

if ( ! file_exists( $theme_check_dir . '/checkbase.php' ) ) {
	$theme_check_dir = WP_PLUGIN_DIR . '/theme-check.latest-stable';
}

require_once $theme_check_dir . '/checkbase.php';
require_once $theme_check_dir . '/main.php';

add_filter( 'tc_skip_development_directories', '__return_true' );
add_filter(
	'tc_common_dev_directories',
	static function ( $directories ) {
		return array_merge(
			$directories,
			array(
				'assets/dist',
				'coverage',
				'dist',
				'docs',
				'scripts',
				'src',
				'tests',
			)
		);
	}
);

$theme_slug = basename( dirname( __DIR__ ) );
$theme      = wp_get_theme( $theme_slug );
$success    = run_themechecks_against_theme( $theme, $theme_slug );
$results    = wp_strip_all_tags( display_themechecks() );

echo esc_html( trim( $results ) ) . PHP_EOL;

$expected_required_patterns = array(
	'/REQUIRED Update URI: is found from your style\.css header\. This feature is only for themes that are distributed outside the theme directory\. Remove from your style\.css file\./',
);

$blocking_results = preg_replace( $expected_required_patterns, '', $results );

if ( false !== strpos( $blocking_results, 'REQUIRED' ) ) {
	exit( 1 );
}
