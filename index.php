<?php
/**
 * Main template file.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main">
	<header class="archive-header">
		<h1 class="archive-header__title"><?php esc_html_e( 'Latest posts', 'proenem-wordpress-theme' ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-list">
			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;
			?>
		</div>

		<?php proenem_render_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>

<?php
get_footer();
