<?php
/**
 * Single post template.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main pro-blog-article-page">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content', 'single' );
	endwhile;
	?>
</main>

<?php
get_footer();
