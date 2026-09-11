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

	<?php if ( ! proenem_is_home_surface() ) : ?>
	<header class="site-header<?php echo proenem_is_material_capture_surface() ? ' site-header--capture' : ''; ?>">
		<?php
		if ( proenem_is_material_capture_surface() ) {
			// A capture page has one job. The full menu offered seven ways out
			// of it, so it is reduced to the brand plus a single action, the
			// same shape the sales page widget already uses.
			proenem_render_site_navbar(
				array(
					'aria_label' => __( 'Navegação reduzida', 'proenem-wordpress-theme' ),
					'context'    => 'material',
					'cta'        => array(
						'label' => __( 'Conheça as turmas', 'proenem-wordpress-theme' ),
						'url'   => proenem_get_home_cta_destination( 'plans' ),
					),
					'logo_only'  => true,
				)
			);
		} else {
			proenem_render_site_navbar();
		}
		?>
	</header>
	<?php endif; ?>
