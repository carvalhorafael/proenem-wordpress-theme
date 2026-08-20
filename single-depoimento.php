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

		$testimonial_id          = get_the_ID();
		$media_image             = proenem_get_post_image_slot( $testimonial_id, 'full' );
		$student_name            = proenem_get_testimonial_student_name( $testimonial_id );
		$student_name_parts      = preg_split( '/\s+/', trim( $student_name ) );
		$student_first_name      = $student_name_parts[0] ?? $student_name;
		$approved_at             = proenem_get_testimonial_approved_at( $testimonial_id );
		$placement               = proenem_get_testimonial_placement( $testimonial_id );
		$course                  = proenem_get_testimonial_course( $testimonial_id );
		$institution             = proenem_get_testimonial_institution( $testimonial_id );
		$approval_year           = proenem_get_testimonial_approval_year( $testimonial_id );
		$preparation_time        = proenem_get_testimonial_preparation_time( $testimonial_id );
		$main_tip                = proenem_get_testimonial_main_tip( $testimonial_id );
		$has_structured_approval = $course || $institution || $approval_year;
		$approval_label          = proenem_get_testimonial_approval_summary( $testimonial_id );
		$hero_approval_label     = $approval_label;
		$related_testimonial_ids = proenem_get_related_testimonial_ids( $testimonial_id, 3 );
		$excerpt                 = has_excerpt( $testimonial_id ) ? get_the_excerpt( $testimonial_id ) : '';
		$video_url               = proenem_get_testimonial_video_url( $testimonial_id );
		$video_embed             = $video_url ? wp_oembed_get( $video_url ) : '';
		$is_portrait_image       = ! $video_embed && proenem_testimonial_has_portrait_image( $testimonial_id );
		$share_url               = get_permalink( $testimonial_id );
		$share_title             = sprintf(
			/* translators: %s: Student name. */
			__( 'A aprovação de %s', 'proenem-wordpress-theme' ),
			$student_name
		);
		$share_text = sprintf(
			/* translators: 1: Student name. 2: Approval summary. */
			__( '%1$s conquistou %2$s. Conheça essa história de aprovação na Proenem.', 'proenem-wordpress-theme' ),
			$student_name,
			$approval_label
		);
		$share_message     = rawurlencode( $share_text . ' ' . $share_url );
		$encoded_share_url = rawurlencode( $share_url );

		if ( $placement && $course ) {
			$hero_approval_label = sprintf(
				/* translators: 1: Placement. 2: Course name. */
				__( '%1$s em %2$s', 'proenem-wordpress-theme' ),
				$placement,
				$course
			);
		} elseif ( $course ) {
			$hero_approval_label = $course;
		}
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'pro-testimonial-single' ); ?>>
			<section class="pro-material-single__hero pro-testimonial-single__hero" aria-labelledby="pro-testimonial-title">
				<div class="pro-material-single__hero-copy">
					<a class="pen-section-pill pro-testimonial-single__back" href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>">
						<span aria-hidden="true">←</span>
						<?php esc_html_e( 'Mural de aprovados', 'proenem-wordpress-theme' ); ?>
					</a>
					<h1 id="pro-testimonial-title"><?php echo esc_html( $student_name ); ?></h1>
					<p class="pro-testimonial-single__approval">
						<span><?php echo esc_html( $hero_approval_label ); ?></span>
						<?php if ( $institution ) : ?>
							<span class="pro-testimonial-single__approval-institution"><?php echo esc_html( $institution ); ?></span>
						<?php endif; ?>
					</p>
					<?php if ( $excerpt ) : ?>
						<p class="pro-testimonial-single__excerpt"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>
					<a class="pen-button pen-button--secondary pen-button--md pro-testimonial-single__story-link" href="#pro-testimonial-story">
						<?php esc_html_e( 'Ler a história', 'proenem-wordpress-theme' ); ?>
						<span class="pen-button__arrow" aria-hidden="true">↓</span>
					</a>
				</div>
				<figure class="pro-material-single__cover pro-testimonial-single__media<?php echo $is_portrait_image ? ' pro-testimonial-single__media--portrait' : ''; ?>">
					<?php if ( $video_embed ) : ?>
						<?php echo wp_kses( $video_embed, proenem_get_oembed_allowed_html() ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( $media_image['src'] ); ?>" alt="<?php echo esc_attr( $media_image['alt'] ); ?>">
					<?php endif; ?>
				</figure>
			</section>

			<div class="pro-material-single__layout pro-testimonial-single__layout">
				<div id="pro-testimonial-story" class="pro-material-single__content pro-testimonial-single__content" aria-labelledby="pro-testimonial-story-title">
					<header class="pro-testimonial-single__story-header">
						<h2 id="pro-testimonial-story-title">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: Student first name. */
									__( 'A história de %s', 'proenem-wordpress-theme' ),
									$student_first_name
								)
							);
							?>
						</h2>
					</header>
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
					<span class="pro-testimonial-single__aside-eyebrow"><?php esc_html_e( 'Ficha da aprovação', 'proenem-wordpress-theme' ); ?></span>
					<h2 id="pro-testimonial-aside-title"><?php echo esc_html( $student_name ); ?></h2>
					<dl class="pro-testimonial-single__details">
						<?php if ( $placement ) : ?>
							<div>
								<dt><?php esc_html_e( 'Colocação', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $placement ); ?></dd>
							</div>
						<?php endif; ?>
						<?php if ( $approved_at && ! $has_structured_approval ) : ?>
							<div class="pro-testimonial-single__detail--wide">
								<dt><?php esc_html_e( 'Onde passou', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $approved_at ); ?></dd>
							</div>
						<?php endif; ?>
						<?php if ( $course ) : ?>
							<div>
								<dt><?php esc_html_e( 'Curso', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $course ); ?></dd>
							</div>
						<?php endif; ?>
						<?php if ( $institution ) : ?>
							<div>
								<dt><?php esc_html_e( 'Instituição', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $institution ); ?></dd>
							</div>
						<?php endif; ?>
						<?php if ( $approval_year ) : ?>
							<div>
								<dt><?php esc_html_e( 'Ano', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $approval_year ); ?></dd>
							</div>
						<?php endif; ?>
						<?php if ( $preparation_time ) : ?>
							<div class="pro-testimonial-single__detail--wide">
								<dt><?php esc_html_e( 'Tempo de preparação', 'proenem-wordpress-theme' ); ?></dt>
								<dd><?php echo esc_html( $preparation_time ); ?></dd>
							</div>
						<?php endif; ?>
					</dl>
					<?php if ( $main_tip ) : ?>
						<section class="pro-testimonial-single__superpower" aria-labelledby="pro-testimonial-superpower-title">
							<h3 id="pro-testimonial-superpower-title"><?php esc_html_e( 'Meu superpoder', 'proenem-wordpress-theme' ); ?></h3>
							<p><?php echo esc_html( $main_tip ); ?></p>
						</section>
					<?php endif; ?>
					<details
						class="pro-testimonial-single__share"
						data-pro-testimonial-share
						data-share-text="<?php echo esc_attr( $share_text ); ?>"
						data-share-title="<?php echo esc_attr( $share_title ); ?>"
						data-share-url="<?php echo esc_url( $share_url ); ?>"
					>
						<summary class="pen-button pen-button--primary pen-button--md pro-material-download__button">
							<?php esc_html_e( 'Compartilhar esta conquista', 'proenem-wordpress-theme' ); ?>
							<span class="pen-button__arrow" aria-hidden="true">↗</span>
						</summary>
						<div class="pro-testimonial-single__share-options">
							<span class="pro-testimonial-single__share-label"><?php esc_html_e( 'Compartilhar em', 'proenem-wordpress-theme' ); ?></span>
							<div class="pro-testimonial-single__share-links">
								<a href="<?php echo esc_url( 'https://wa.me/?text=' . $share_message ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
								<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_share_url ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
								<a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_share_url ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
								<button type="button" data-pro-testimonial-copy-link data-copy-label="<?php echo esc_attr__( 'Copiar link', 'proenem-wordpress-theme' ); ?>" data-copied-label="<?php echo esc_attr__( 'Link copiado', 'proenem-wordpress-theme' ); ?>" data-copy-error-label="<?php echo esc_attr__( 'Não foi possível copiar', 'proenem-wordpress-theme' ); ?>">
									<?php esc_html_e( 'Copiar link', 'proenem-wordpress-theme' ); ?>
								</button>
							</div>
							<span class="screen-reader-text" aria-live="polite" data-pro-testimonial-share-status></span>
						</div>
					</details>
					<a class="pro-testimonial-single__all-link" href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>">
						<?php esc_html_e( 'Ver outros aprovados', 'proenem-wordpress-theme' ); ?>
						<span aria-hidden="true">↗</span>
					</a>
				</aside>
			</div>

			<?php if ( $related_testimonial_ids ) : ?>
				<section class="pro-testimonial-single__related" aria-labelledby="pro-testimonial-related-title">
					<div class="pro-testimonial-single__related-inner">
						<header class="pro-testimonial-single__related-header">
							<h2 id="pro-testimonial-related-title"><?php esc_html_e( 'Outras histórias para continuar acreditando.', 'proenem-wordpress-theme' ); ?></h2>
							<a href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>">
								<?php esc_html_e( 'Ver todo o mural', 'proenem-wordpress-theme' ); ?>
								<span aria-hidden="true">→</span>
							</a>
						</header>
						<div class="pro-testimonials-grid pro-testimonial-single__related-grid">
							<?php
							foreach ( $related_testimonial_ids as $related_testimonial_id ) {
								proenem_render_testimonial_card( $related_testimonial_id );
							}
							?>
						</div>
					</div>
				</section>
			<?php endif; ?>
			<?php if ( is_active_sidebar( 'testimonial-page-footer' ) ) : ?>
				<section class="pro-testimonial-single__footer-widgets" aria-label="<?php esc_attr_e( 'Footer da página de depoimento', 'proenem-wordpress-theme' ); ?>">
					<?php dynamic_sidebar( 'testimonial-page-footer' ); ?>
				</section>
			<?php endif; ?>

			<div class="pro-testimonial-single__next-wrap">
				<section class="pro-testimonials-next" aria-labelledby="pro-testimonial-next-title">
					<div class="pro-testimonials-next__inner">
						<div class="pro-testimonials-next__copy">
							<span class="pro-testimonials-next__eyebrow"><?php esc_html_e( 'Seu próximo capítulo', 'proenem-wordpress-theme' ); ?></span>
							<h2 id="pro-testimonial-next-title"><?php esc_html_e( 'Agora é a sua vez de construir uma história para este mural.', 'proenem-wordpress-theme' ); ?></h2>
							<p><?php esc_html_e( 'Você não precisa ter todo o caminho resolvido. Precisa de um plano, apoio para continuar e coragem para dar o próximo passo.', 'proenem-wordpress-theme' ); ?></p>
							<div class="pro-testimonials-next__actions">
								<a class="pen-button pen-button--secondary pen-button--lg" href="<?php echo esc_url( proenem_get_home_cta_destination( 'plans' ) ); ?>">
									<?php esc_html_e( 'Quero começar minha preparação', 'proenem-wordpress-theme' ); ?>
									<span aria-hidden="true">→</span>
								</a>
								<a class="pro-testimonials-next__back" href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>"><?php esc_html_e( 'Ver todo o mural', 'proenem-wordpress-theme' ); ?></a>
							</div>
						</div>

						<div class="pro-testimonials-next__mural" aria-hidden="true">
							<span><?php esc_html_e( 'Próxima aprovação', 'proenem-wordpress-theme' ); ?></span>
							<strong><?php esc_html_e( 'Seu nome', 'proenem-wordpress-theme' ); ?></strong>
							<p><?php esc_html_e( 'pode estar aqui.', 'proenem-wordpress-theme' ); ?></p>
							<small>✦ 2026 ✦</small>
						</div>
					</div>
				</section>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
