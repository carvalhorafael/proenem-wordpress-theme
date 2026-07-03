<?php
/**
 * Default content template part.
 *
 * @package Proenem
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail( 'large' ); ?>
		</a>
	<?php endif; ?>

	<div class="post-card__body">
		<header class="post-card__header">
			<?php the_title( '<h2 class="post-card__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
			<?php proenem_render_post_meta(); ?>
		</header>

		<p class="post-card__excerpt">
			<?php echo esc_html( proenem_get_listing_excerpt() ); ?>
		</p>

		<a class="post-card__action" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'proenem-wordpress-theme' ); ?>
		</a>
	</div>
</article>
