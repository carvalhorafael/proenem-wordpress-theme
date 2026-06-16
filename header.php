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

	<header class="site-header">
		<div class="site-header__inner">
			<div class="site-branding">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					<?php
				}
				?>
			</div>

			<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'proenem-wordpress-theme' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_class'     => 'primary-navigation__items',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 2,
					)
				);
				?>
			</nav>
		</div>
	</header>
