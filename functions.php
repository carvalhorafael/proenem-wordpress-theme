<?php
/**
 * Proenem theme bootstrap.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROENEM_THEME_DIR', __DIR__ );
define( 'PROENEM_THEME_URI', get_template_directory_uri() );
define( 'PROENEM_THEME_VERSION', wp_get_theme()->get( 'Version' ) );

require_once PROENEM_THEME_DIR . '/inc/setup.php';
require_once PROENEM_THEME_DIR . '/inc/vite.php';
require_once PROENEM_THEME_DIR . '/inc/assets.php';
require_once PROENEM_THEME_DIR . '/inc/template-tags.php';
require_once PROENEM_THEME_DIR . '/inc/updater.php';
