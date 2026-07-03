<?php
/**
 * Main template file.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main pro-blog-index-page">
	<div class="pen-blog-index">
		<section class="pen-blog-hero">
			<h1><?php esc_html_e( 'Blog Proenem', 'proenem-wordpress-theme' ); ?></h1>
			<p><?php esc_html_e( 'Guias, notícias e estratégias para estudar com método e transformar esforço em resultado.', 'proenem-wordpress-theme' ); ?></p>
		</section>

		<?php if ( have_posts() ) : ?>
			<?php
			the_post();
			proenem_render_blog_post_card( get_the_ID(), 'featured' );
			?>

			<?php proenem_render_blog_filter_bar(); ?>

			<div class="pen-post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					proenem_render_blog_post_card( get_the_ID() );
				endwhile;
				?>
			</div>

			<?php proenem_render_design_system_posts_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>

	<?php proenem_render_blog_index_after_sections(); ?>
</main>

<?php
get_footer();
