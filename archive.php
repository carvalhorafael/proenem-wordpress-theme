<?php
/**
 * Archive template.
 *
 * @package Proenem
 */

get_header();

$archive_description = get_the_archive_description();
?>

<main id="primary" class="site-main">
	<header class="archive-header">
		<h1 class="archive-header__title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php if ( $archive_description ) : ?>
			<div class="archive-header__description">
				<?php echo wp_kses_post( wpautop( $archive_description ) ); ?>
			</div>
		<?php endif; ?>
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
