<?php
/**
 * Theme header.
 *
 * @package Proenem
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'proenem-wordpress-theme' ); ?></a>

	<?php if ( ! is_front_page() && ! is_page_template( 'page-templates/home.php' ) ) : ?>
	<header class="site-header">
		<?php proenem_render_site_navbar(); ?>
	</header>
	<?php endif; ?>
