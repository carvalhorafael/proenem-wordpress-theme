<?php
/**
 * Page content template part.
 *
 * @package Proenem
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
	<header class="entry__header">
		<?php the_title( '<h1 class="entry__title">', '</h1>' ); ?>
	</header>

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
</article>
