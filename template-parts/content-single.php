<?php
/**
 * Single content template part.
 *
 * @package Proenem
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pen-blog-article' ); ?>>
	<section class="pen-article-hero">
		<span><?php echo esc_html( proenem_get_post_category_label( get_the_ID() ) ); ?></span>
		<?php the_title( '<h1>', '</h1>' ); ?>
		<p><?php echo esc_html( proenem_get_post_card_excerpt( get_the_ID() ) ); ?></p>
	</section>

	<?php $cover_image = proenem_get_post_image_slot( get_the_ID(), 'full' ); ?>
	<figure class="pen-article-figure pen-article-cover">
		<img src="<?php echo esc_url( $cover_image['src'] ); ?>" alt="<?php echo esc_attr( $cover_image['alt'] ); ?>">
	</figure>

	<div class="pen-article-body">
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

	<div class="pen-article-share-bar">
		<?php proenem_render_post_category_badge( proenem_get_post_category_label( get_the_ID() ) ); ?>
		<div class="pen-article-share-bar__links">
			<a class="pen-article-share pen-article-share--pink" href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>" aria-label="<?php esc_attr_e( 'Compartilhar no X', 'proenem-wordpress-theme' ); ?>">X</a>
			<a class="pen-article-share pen-article-share--yellow" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" aria-label="<?php esc_attr_e( 'Compartilhar no Facebook', 'proenem-wordpress-theme' ); ?>">F</a>
			<a class="pen-article-share pen-article-share--cyan" href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( get_permalink() ) ); ?>" aria-label="<?php esc_attr_e( 'Compartilhar no LinkedIn', 'proenem-wordpress-theme' ); ?>">L</a>
		</div>
	</div>

	<?php proenem_render_latest_posts_section( get_the_ID() ); ?>
</article>
