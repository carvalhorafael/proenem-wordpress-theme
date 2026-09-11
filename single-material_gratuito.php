<?php
/**
 * Single free material template.
 *
 * @package Proenem
 */

get_header();
?>

<main id="primary" class="site-main pro-material-single-page">
	<?php
	while ( have_posts() ) :
		the_post();

		$material_id = get_the_ID();
		// Only an excerpt the editor actually wrote. The generated one repeated
		// the opening of the content, truncated with an ellipsis.
		$promise    = has_excerpt( $material_id ) ? get_the_excerpt( $material_id ) : '';
		$highlights = array_slice( proenem_get_material_highlights( $material_id ), 0, 5 );
		$specs      = proenem_get_material_specs( $material_id );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'pro-material-single' ); ?>>
			<section class="pro-material-single__hero" aria-labelledby="pro-material-title">
				<div class="pro-material-single__hero-copy">
					<?php proenem_render_material_breadcrumb( $material_id ); ?>
					<?php the_title( '<h1 id="pro-material-title">', '</h1>' ); ?>
					<?php if ( '' !== $promise ) : ?>
						<p><?php echo esc_html( $promise ); ?></p>
					<?php endif; ?>
				</div>

				<div class="pro-material-single__preview">
					<figure class="pro-material-single__cover">
						<?php proenem_render_material_image( $material_id, 'medium_large', '(max-width: 980px) 40vw, 240px', true ); ?>
					</figure>

					<div class="pro-material-single__inside">
						<?php if ( $highlights ) : ?>
							<p class="pro-material-single__inside-title"><?php esc_html_e( 'O que tem dentro', 'proenem-wordpress-theme' ); ?></p>
							<ul class="pro-material-single__highlights">
								<?php foreach ( $highlights as $highlight ) : ?>
									<li><?php echo esc_html( $highlight ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php if ( $specs ) : ?>
							<p class="pro-material-single__specs"><?php echo esc_html( implode( ' · ', $specs ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<?php
				get_template_part(
					'template-parts/materials/capture',
					null,
					array(
						'anchor'      => 'material-download-form',
						'instance'    => 'hero',
						'material_id' => $material_id,
					)
				);
				?>
			</section>

			<div class="pro-material-single__layout">
				<div id="material-content" class="pro-material-single__content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'proenem-wordpress-theme' ) . '">',
							'after'  => '</nav>',
						)
					);
					?>

					<?php proenem_render_material_share( $material_id ); ?>
				</div>

				<?php proenem_render_material_reassurance( $material_id ); ?>
			</div>

			<?php proenem_render_related_materials( $material_id ); ?>

			<section class="pro-material-single__closing" aria-labelledby="pro-material-capture-footer-title">
				<div class="pro-material-single__closing-inner">
					<div class="pro-material-single__closing-copy">
						<span><?php esc_html_e( 'Material gratuito', 'proenem-wordpress-theme' ); ?></span>
						<h2><?php esc_html_e( 'Leve este material para sua rotina de estudos', 'proenem-wordpress-theme' ); ?></h2>
						<?php if ( $highlights ) : ?>
							<ul class="pro-material-single__highlights">
								<?php foreach ( $highlights as $highlight ) : ?>
									<li><?php echo esc_html( $highlight ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>

					<?php
					get_template_part(
						'template-parts/materials/capture',
						null,
						array(
							'eyebrow'     => __( 'Ainda dá tempo', 'proenem-wordpress-theme' ),
							'heading'     => __( 'Baixe agora, é gratuito', 'proenem-wordpress-theme' ),
							'instance'    => 'footer',
							'material_id' => $material_id,
						)
					);
					?>
				</div>
			</section>
		</article>

		<div class="pro-material-sticky-cta" data-pro-material-sticky-cta hidden>
			<div class="pro-material-sticky-cta__copy">
				<strong><?php echo esc_html( wp_trim_words( get_the_title( $material_id ), 6 ) ); ?></strong>
				<span><?php echo esc_html( $specs ? implode( ' · ', $specs ) : __( 'Material gratuito', 'proenem-wordpress-theme' ) ); ?></span>
			</div>
			<a class="pen-button pen-button--primary pen-button--md" href="#material-download-form" data-pro-material-sticky-cta-action>
				<?php echo esc_html( proenem_get_material_cta_label( $material_id ) ); ?>
			</a>
		</div>
	<?php endwhile; ?>
</main>

<?php
get_footer();
