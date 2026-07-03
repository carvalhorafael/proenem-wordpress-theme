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
		<?php $primary_navigation = proenem_get_primary_navigation_items(); ?>
		<nav class="pen-navbar pen-navbar--proenem-red pen-navbar--collapsible" aria-label="<?php esc_attr_e( 'Navegação principal', 'proenem-wordpress-theme' ); ?>" data-pen-navbar style="--pen-navbar-closed-height: 72px; --pen-navbar-logo-height: 34px;">
			<a class="pen-brand-logo pen-brand-logo--image" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img class="pen-brand-logo__image" src="<?php echo esc_url( PROENEM_THEME_URI . '/assets/images/brand/logo_proenem.svg' ); ?>" alt="<?php esc_attr_e( 'Proenem', 'proenem-wordpress-theme' ); ?>" width="139" height="32">
			</a>

			<button class="pen-navbar__toggle" type="button" aria-controls="pen-navbar-menu" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-navbar__toggle-line" aria-hidden="true"></span>
				<span class="pen-navbar__toggle-line" aria-hidden="true"></span>
				<span class="pen-navbar__toggle-line" aria-hidden="true"></span>
			</button>

			<div id="pen-navbar-menu" class="pen-navbar__menu">
				<div class="pen-navbar__links">
					<?php foreach ( $primary_navigation['links'] as $navigation_link ) : ?>
						<?php
						$navigation_link_class = 'pen-navbar__link';

						if ( ! empty( $navigation_link['active'] ) ) {
							$navigation_link_class .= ' pen-navbar__link--active';
						}

						$navigation_link_rel = $navigation_link['rel'] ?? '';

						if ( '_blank' === ( $navigation_link['target'] ?? '' ) && empty( $navigation_link_rel ) ) {
							$navigation_link_rel = 'noopener';
						}
						?>
						<a
							class="<?php echo esc_attr( $navigation_link_class ); ?>"
							href="<?php echo esc_url( $navigation_link['url'] ); ?>"
							<?php echo ! empty( $navigation_link['target'] ) ? 'target="' . esc_attr( $navigation_link['target'] ) . '"' : ''; ?>
							<?php echo ! empty( $navigation_link_rel ) ? 'rel="' . esc_attr( $navigation_link_rel ) . '"' : ''; ?>
							<?php echo ! empty( $navigation_link['active'] ) ? 'aria-current="page"' : ''; ?>
						>
							<?php echo esc_html( $navigation_link['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>

				<div class="pen-navbar__actions">
					<?php foreach ( $primary_navigation['actions'] as $navigation_action ) : ?>
						<?php
						$navigation_action_variant = in_array( $navigation_action['variant'] ?? '', array( 'primary', 'secondary' ), true )
							? $navigation_action['variant']
							: 'primary';
						$navigation_action_rel     = $navigation_action['rel'] ?? '';

						if ( '_blank' === ( $navigation_action['target'] ?? '' ) && empty( $navigation_action_rel ) ) {
							$navigation_action_rel = 'noopener';
						}
						?>
						<a
							class="<?php echo esc_attr( 'pen-navbar__action pen-navbar__action--' . $navigation_action_variant ); ?>"
							href="<?php echo esc_url( $navigation_action['url'] ); ?>"
							<?php echo ! empty( $navigation_action['target'] ) ? 'target="' . esc_attr( $navigation_action['target'] ) . '"' : ''; ?>
							<?php echo ! empty( $navigation_action_rel ) ? 'rel="' . esc_attr( $navigation_action_rel ) . '"' : ''; ?>
						>
							<?php echo esc_html( $navigation_action['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</nav>
	</header>
	<?php endif; ?>
