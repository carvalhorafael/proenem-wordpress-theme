<?php
/**
 * Shared template helpers.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render post metadata.
 *
 * @param int|null $post_id Post ID.
 * @return void
 */
function proenem_render_post_meta( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id ) {
		return;
	}
	?>
	<div class="entry-meta" aria-label="<?php esc_attr_e( 'Post information', 'proenem-wordpress-theme' ); ?>">
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>">
			<?php echo esc_html( get_the_date( '', $post_id ) ); ?>
		</time>
		<span class="entry-meta__separator" aria-hidden="true">/</span>
		<span><?php echo esc_html( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) ); ?></span>
	</div>
	<?php
}

/**
 * Render posts pagination.
 *
 * @return void
 */
function proenem_render_posts_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'prev_text' => __( 'Previous', 'proenem-wordpress-theme' ),
			'next_text' => __( 'Next', 'proenem-wordpress-theme' ),
		)
	);

	if ( empty( $links ) || ! is_array( $links ) ) {
		return;
	}
	?>
	<nav class="pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'proenem-wordpress-theme' ); ?>">
		<ul class="pagination__items">
			<?php foreach ( $links as $link ) : ?>
				<li class="pagination__item"><?php echo wp_kses_post( $link ); ?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Get a listing excerpt with a stable fallback.
 *
 * @return string
 */
function proenem_get_listing_excerpt() {
	if ( has_excerpt() ) {
		return get_the_excerpt();
	}

	return wp_trim_words( get_the_content(), 28 );
}
