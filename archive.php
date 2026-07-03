<?php
/**
 * Archive template.
 *
 * @package Proenem
 */

get_header();

$archive_description = get_the_archive_description();
?>

<main id="primary" class="site-main pro-blog-index-page">
	<div class="pen-blog-index">
		<section class="pen-blog-hero">
			<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
			<?php if ( $archive_description ) : ?>
				<?php echo wp_kses_post( wpautop( $archive_description ) ); ?>
			<?php endif; ?>
		</section>

		<?php if ( have_posts() ) : ?>
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
</main>

<?php
get_footer();
