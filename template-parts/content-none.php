<?php
/**
 * Empty content template part.
 *
 * @package Proenem
 */

?>
<section class="empty-state no-results not-found">
	<h1 class="empty-state__title">
		<?php
		if ( is_search() ) {
			esc_html_e( 'Nothing found', 'proenem-wordpress-theme' );
		} else {
			esc_html_e( 'No posts yet', 'proenem-wordpress-theme' );
		}
		?>
	</h1>
	<p class="empty-state__description">
		<?php
		if ( is_search() ) {
			esc_html_e( 'Try a different search or return to the homepage.', 'proenem-wordpress-theme' );
		} else {
			esc_html_e( 'This archive does not have published posts yet.', 'proenem-wordpress-theme' );
		}
		?>
	</p>

	<?php if ( is_search() ) : ?>
		<?php get_search_form(); ?>
	<?php else : ?>
		<a class="button-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to homepage', 'proenem-wordpress-theme' ); ?></a>
	<?php endif; ?>
</section>
