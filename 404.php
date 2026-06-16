<?php
/**
 * 404 template.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="empty-state error-404 not-found">
		<h1 class="empty-state__title"><?php esc_html_e( 'Page not found', 'proenem-wordpress-theme' ); ?></h1>
		<p class="empty-state__description"><?php esc_html_e( 'The requested page could not be found. Try searching for what you need.', 'proenem-wordpress-theme' ); ?></p>
		<?php get_search_form(); ?>
		<p>
			<a class="button-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to homepage', 'proenem-wordpress-theme' ); ?></a>
		</p>
	</section>
</main>

<?php
get_footer();
