<?php
/**
 * Single content template part.
 *
 * @package Proenem
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
	<header class="entry__header">
		<?php the_title( '<h1 class="entry__title">', '</h1>' ); ?>
		<?php proenem_render_post_meta(); ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry__media">
			<?php the_post_thumbnail( 'large' ); ?>
		</figure>
	<?php endif; ?>

	<div class="entry__content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'proenem-wordpress-theme' ) . '">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php if ( has_tag() ) : ?>
		<footer class="entry__footer">
			<?php the_tags( '<div class="entry-tags" aria-label="' . esc_attr__( 'Post tags', 'proenem-wordpress-theme' ) . '">', ' ', '</div>' ); ?>
		</footer>
	<?php endif; ?>
</article>
