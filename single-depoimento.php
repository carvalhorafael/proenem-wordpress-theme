<?php
/**
 * Single testimonial template.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main pro-material-single-page pro-testimonial-single-page">
	<?php
	while ( have_posts() ) :
		the_post();

		$testimonial_id = get_the_ID();
		$media_image    = proenem_get_post_image_slot( $testimonial_id, 'full' );
		$category_name  = proenem_get_testimonial_category_label( $testimonial_id );
		$student_name   = proenem_get_testimonial_student_name( $testimonial_id );
		$approved_at    = proenem_get_testimonial_approved_at( $testimonial_id );
		$placement      = proenem_get_testimonial_placement( $testimonial_id );
		$approval_label = proenem_get_testimonial_approval_summary( $testimonial_id );
		$quote          = proenem_get_testimonial_quote( $testimonial_id, 34 );
		$video_url      = proenem_get_testimonial_video_url( $testimonial_id );
		$video_embed    = $video_url ? wp_oembed_get( $video_url ) : '';
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'pro-testimonial-single' ); ?>>
			<section class="pro-material-single__hero pro-testimonial-single__hero" aria-labelledby="pro-testimonial-title">
				<div class="pro-material-single__hero-copy">
					<a class="pro-material-single__back" href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>"><?php esc_html_e( '← Depoimentos', 'proenem-wordpress-theme' ); ?></a>
					<span class="pen-section-pill"><?php echo esc_html( $category_name ); ?></span>
					<h1 id="pro-testimonial-title"><?php echo esc_html( $student_name ); ?></h1>
					<p class="pro-testimonial-single__approval"><?php echo esc_html( $approval_label ); ?></p>
					<p><?php echo esc_html( $quote ); ?></p>
				</div>
				<figure class="pro-material-single__cover pro-testimonial-single__media">
					<?php if ( $video_embed ) : ?>
						<?php echo wp_kses( $video_embed, proenem_get_oembed_allowed_html() ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( $media_image['src'] ); ?>" alt="<?php echo esc_attr( $media_image['alt'] ); ?>">
					<?php endif; ?>
				</figure>
			</section>

			<div class="pro-material-single__layout pro-testimonial-single__layout">
				<div class="pro-material-single__content pro-testimonial-single__content">
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

				<aside class="pro-material-download pro-testimonial-single__aside" aria-labelledby="pro-testimonial-aside-title">
					<h2 id="pro-testimonial-aside-title"><?php echo esc_html( $student_name ); ?></h2>
					<dl class="pro-testimonial-single__details">
						<?php if ( $approved_at ) : ?>
							<div>
								<dt><?php esc_html_e( 'Onde passou', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $approved_at ); ?></dd>
							</div>
						<?php endif; ?>
					</dl>
					<p class="pro-testimonial-single__aside-quote"><?php echo esc_html( $quote ); ?></p>
					<a class="pen-button pen-button--primary pen-button--md pro-material-download__button" href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>">
						<?php esc_html_e( 'Ver depoimentos', 'proenem-wordpress-theme' ); ?>
						<span class="pen-button__arrow" aria-hidden="true">↗</span>
					</a>
				</aside>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
