<?php
/**
 * Comments template.
 *
 * @package Proenem
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$proenem_comment_count = get_comments_number();

			if ( 1 === (int) $proenem_comment_count ) {
				esc_html_e( 'One comment', 'proenem-wordpress-theme' );
			} else {
				printf(
					/* translators: %s: Number of comments. */
					esc_html__( '%s comments', 'proenem-wordpress-theme' ),
					esc_html( number_format_i18n( $proenem_comment_count ) )
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php
	if ( ! comments_open() && get_comments_number() ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'proenem-wordpress-theme' ); ?></p>
		<?php
	endif;

	comment_form();
	?>
</section>
