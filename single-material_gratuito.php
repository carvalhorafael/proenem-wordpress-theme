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

		$material_id   = get_the_ID();
		$category_name = proenem_get_material_category_label( $material_id );
		// Only an excerpt the editor actually wrote. The generated one repeated
		// the opening of the content, truncated with an ellipsis.
		$promise    = has_excerpt( $material_id ) ? get_the_excerpt( $material_id ) : '';
		$highlights = array_slice( proenem_get_material_highlights( $material_id ), 0, 5 );
		$specs      = proenem_get_material_specs( $material_id );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'pro-material-single' ); ?>>
			<section class="pro-material-single__hero" aria-labelledby="pro-material-title">
				<div class="pro-material-single__hero-copy">
					<a class="pro-material-single__back" href="<?php echo esc_url( proenem_get_free_materials_url() ); ?>"><?php esc_html_e( '← Materiais gratuitos', 'proenem-wordpress-theme' ); ?></a>
					<span class="pen-section-pill"><?php echo esc_html( $category_name ); ?></span>
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
				<aside id="material-download-form" class="pro-material-capture" aria-labelledby="pro-material-capture-title">
					<span class="pro-material-capture__eyebrow"><?php esc_html_e( 'Acesso imediato', 'proenem-wordpress-theme' ); ?></span>
					<h2 id="pro-material-capture-title"><?php esc_html_e( 'Receba o material agora', 'proenem-wordpress-theme' ); ?></h2>
					<p><?php esc_html_e( 'Enviamos o link de download para o contato que você informar.', 'proenem-wordpress-theme' ); ?></p>
					<form class="pro-material-capture__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-pro-material-capture-form>
						<input type="hidden" name="action" value="crm_leads_capture_free_material">
						<?php wp_nonce_field( 'crm_leads_capture_free_material' ); ?>
						<input type="hidden" name="material_id" value="<?php echo esc_attr( (string) $material_id ); ?>">
						<input class="pro-material-capture__honeypot" type="text" name="crm_leads_capture_website" value="" autocomplete="off" tabindex="-1" aria-hidden="true">
						<?php
						if ( function_exists( 'crm_leads_capture_render_free_material_error_message' ) ) {
							crm_leads_capture_render_free_material_error_message();
						}
						?>
						<label class="pro-material-capture__field">
							<span><?php esc_html_e( 'Nome', 'proenem-wordpress-theme' ); ?></span>
							<input
								type="text"
								id="pro-material-capture-name"
								name="name"
								autocomplete="name"
								placeholder="<?php esc_attr_e( 'Seu nome completo', 'proenem-wordpress-theme' ); ?>"
								aria-describedby="pro-material-capture-name-error"
								required
							>
							<em class="pro-material-capture__error" id="pro-material-capture-name-error" data-pro-capture-error hidden></em>
						</label>
						<label class="pro-material-capture__field">
							<span><?php esc_html_e( 'Email', 'proenem-wordpress-theme' ); ?></span>
							<input
								type="email"
								id="pro-material-capture-email"
								name="email"
								autocomplete="email"
								placeholder="<?php esc_attr_e( 'voce@exemplo.com', 'proenem-wordpress-theme' ); ?>"
								aria-describedby="pro-material-capture-email-error"
								required
							>
							<em class="pro-material-capture__error" id="pro-material-capture-email-error" data-pro-capture-error hidden></em>
						</label>
						<label class="pro-material-capture__field">
							<span>
								<?php esc_html_e( 'WhatsApp', 'proenem-wordpress-theme' ); ?>
								<small><?php esc_html_e( '(opcional)', 'proenem-wordpress-theme' ); ?></small>
							</span>
							<input
								type="tel"
								id="pro-material-capture-whatsapp"
								name="whatsapp"
								autocomplete="tel"
								inputmode="numeric"
								maxlength="16"
								placeholder="<?php esc_attr_e( '(00) 00000-0000', 'proenem-wordpress-theme' ); ?>"
								aria-describedby="pro-material-capture-whatsapp-error"
								data-pro-capture-phone
							>
							<em class="pro-material-capture__error" id="pro-material-capture-whatsapp-error" data-pro-capture-error hidden></em>
						</label>
						<button class="pen-button pen-button--primary pen-button--md pro-material-capture__button" type="submit">
							<?php esc_html_e( 'Baixar material gratuito', 'proenem-wordpress-theme' ); ?>
						</button>
					</form>
					<small><?php esc_html_e( 'Sem pagamento. Enviaremos o material para o contato informado.', 'proenem-wordpress-theme' ); ?></small>
				</aside>
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
				</div>

				<aside class="pro-material-download" aria-labelledby="pro-material-download-title">
					<span class="pro-material-download__eyebrow"><?php esc_html_e( 'Material gratuito', 'proenem-wordpress-theme' ); ?></span>
					<h2 id="pro-material-download-title"><?php esc_html_e( 'Pronto para usar nos estudos', 'proenem-wordpress-theme' ); ?></h2>
					<p><?php esc_html_e( 'Acesse o material, salve na sua rotina e volte para esta página sempre que precisar revisar.', 'proenem-wordpress-theme' ); ?></p>
					<a class="pen-button pen-button--primary pen-button--md pro-material-download__button" href="#material-download-form">
						<?php esc_html_e( 'Baixar material', 'proenem-wordpress-theme' ); ?>
						<span class="pen-button__arrow" aria-hidden="true">↑</span>
					</a>
				</aside>
			</div>

			<section class="pro-material-footer-cta" aria-labelledby="pro-material-footer-cta-title">
				<div class="pro-material-footer-cta__inner">
					<figure class="pro-material-footer-cta__media">
						<?php proenem_render_material_image( $material_id, 'medium_large', '(max-width: 780px) 92vw, 288px' ); ?>
					</figure>
					<div class="pro-material-footer-cta__copy">
						<span><?php esc_html_e( 'Material gratuito', 'proenem-wordpress-theme' ); ?></span>
						<h2 id="pro-material-footer-cta-title"><?php esc_html_e( 'Leve este material para sua rotina de estudos', 'proenem-wordpress-theme' ); ?></h2>
						<p><?php esc_html_e( 'Baixe agora e consulte sempre que precisar organizar sua próxima etapa.', 'proenem-wordpress-theme' ); ?></p>
					</div>
					<a class="pen-button pen-button--primary pen-button--md pro-material-footer-cta__button" href="#material-download-form">
						<?php esc_html_e( 'Baixar material', 'proenem-wordpress-theme' ); ?>
						<span class="pen-button__arrow" aria-hidden="true">↑</span>
					</a>
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
