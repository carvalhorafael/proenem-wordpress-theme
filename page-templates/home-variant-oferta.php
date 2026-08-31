<?php
/**
 * Template Name: Proenem Home - Variante A (Oferta direta)
 * Template Post Type: page
 *
 * Conversion variant of the home first fold.
 *
 * Hypothesis: putting the offer, the price and the guarantee inside the first
 * fold, next to a single checkout action, raises checkout starts against the
 * control hero, which only states a promise and scrolls to the pricing anchor.
 *
 * Everything below the hero is the control page, rendered by
 * `template-parts/home/sections.php`.
 *
 * @package Proenem
 */

get_header();

$offer        = proenem_get_home_offer();
$universities = proenem_get_home_hero_universities();
$avatars      = proenem_get_home_hero_avatars();

$hero_benefits = array(
	__( 'Plano de estudo personalizado e revisado toda semana', 'proenem-wordpress-theme' ),
	__( 'Simulados com nota TRI e diagnóstico por área', 'proenem-wordpress-theme' ),
	__( 'Redação corrigida com devolutiva por competência', 'proenem-wordpress-theme' ),
	__( 'Mais de 60 mil questões com resolução comentada', 'proenem-wordpress-theme' ),
);
?>

<main id="primary" class="site-main pro-home pro-home--variant pro-home--variant-oferta">
	<?php
	proenem_render_site_navbar(
		array(
			'aria_label' => __( 'Navegação da home', 'proenem-wordpress-theme' ),
			'context'    => 'home',
		)
	);
	proenem_render_mobile_persistent_action(
		array(
			'label' => __( 'Quero Método PRO', 'proenem-wordpress-theme' ),
			'url'   => $offer['checkout_url'],
		)
	);
	?>

	<section class="pen-hero-section pro-hero-offer" aria-labelledby="pro-home-title">
		<div class="pro-hero-offer__stage">
			<div class="pro-hero-offer__copy">
				<p class="pro-hero-offer__eyebrow">
					<span class="pro-hero-offer__eyebrow-dot" aria-hidden="true"></span>
					<?php
					printf(
						/* translators: %s: Offer name, e.g. Turma Intensiva 2026. */
						esc_html__( 'Matrículas abertas para a %s', 'proenem-wordpress-theme' ),
						esc_html( $offer['name'] )
					);
					?>
				</p>

				<h1 id="pro-home-title" class="pro-hero-offer__title">
					<?php esc_html_e( 'Sua aprovação no', 'proenem-wordpress-theme' ); ?>
					<strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--blue"><?php esc_html_e( 'ENEM', 'proenem-wordpress-theme' ); ?></strong>
					<?php esc_html_e( 'começa', 'proenem-wordpress-theme' ); ?>
					<strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--yellow"><?php esc_html_e( 'aqui.', 'proenem-wordpress-theme' ); ?></strong>
				</h1>

				<p class="pro-hero-offer__subtitle">
					<?php esc_html_e( 'Um método que te diz o que estudar agora e acompanha sua evolução até a prova.', 'proenem-wordpress-theme' ); ?>
				</p>

				<ul class="pro-hero-offer__benefits">
					<?php foreach ( $hero_benefits as $benefit ) : ?>
						<li>
							<span class="pro-hero-offer__check" aria-hidden="true">
								<svg viewBox="0 0 24 24" focusable="false"><path d="m5 12.5 4.5 4.5L19 7.5"></path></svg>
							</span>
							<?php echo esc_html( $benefit ); ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="pro-hero-offer__action">
					<p class="pro-hero-offer__price">
						<span class="pro-hero-offer__price-prefix"><?php echo esc_html( $offer['price_prefix'] ); ?></span>
						<strong class="pro-hero-offer__price-value"><?php echo esc_html( $offer['price'] ); ?></strong>
						<span class="pro-hero-offer__price-details"><?php echo esc_html( $offer['price_details'] ); ?></span>
					</p>
					<a class="pen-button pen-button--lg pro-hero-offer__cta" href="<?php echo esc_url( $offer['checkout_url'] ); ?>" data-pro-hero-variant="oferta" data-pro-hero-action="checkout">
						<span class="pro-hero-cta__label">
							<?php esc_html_e( 'Quero Método PRO', 'proenem-wordpress-theme' ); ?>
							<span class="pen-button__arrow" aria-hidden="true">-&gt;</span>
						</span>
					</a>
					<a class="pro-hero-offer__secondary" href="<?php echo esc_url( $offer['plans_url'] ); ?>" data-pro-hero-variant="oferta" data-pro-hero-action="plans">
						<?php esc_html_e( 'Ver tudo o que está incluído', 'proenem-wordpress-theme' ); ?>
					</a>
				</div>

				<ul class="pro-hero-offer__reassurance">
					<li><?php echo esc_html( $offer['guarantee'] ); ?></li>
					<li><?php esc_html_e( 'Cancele quando quiser', 'proenem-wordpress-theme' ); ?></li>
					<li><?php esc_html_e( 'Acesso imediato após a compra', 'proenem-wordpress-theme' ); ?></li>
				</ul>
			</div>

			<div class="pro-hero-offer__figure">
				<img
					class="pro-hero-offer__image"
					src="<?php echo esc_url( proenem_home_asset_uri( 'hero-student.webp' ) ); ?>"
					alt="<?php esc_attr_e( 'Estudante sorrindo com cadernos nas mãos.', 'proenem-wordpress-theme' ); ?>"
					<?php
					echo proenem_home_image_attributes( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped by the helper.
						'hero-student.webp',
						array(
							'fetchpriority' => 'high',
							'loading'       => 'eager',
						)
					);
					echo proenem_home_image_source_set( 'hero-student.webp', '(max-width: 900px) 70vw, 34vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped by the helper.
					?>
				>
				<span class="pen-hero-sticker pro-hero-offer__sticker pro-hero-offer__sticker--tri"><?php esc_html_e( 'Simulados com TRI', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-hero-sticker pro-hero-offer__sticker pro-hero-offer__sticker--plan"><?php esc_html_e( 'Plano semanal', 'proenem-wordpress-theme' ); ?></span>

				<div class="pro-hero-offer__social">
					<ul class="pro-hero-offer__avatars" aria-hidden="true">
						<?php foreach ( $avatars as $avatar ) : ?>
							<li>
								<img
									src="<?php echo esc_url( proenem_home_asset_uri( $avatar ) ); ?>"
									alt=""
									<?php echo proenem_home_image_attributes( $avatar ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped by the helper. ?>
								>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="pro-hero-offer__social-copy">
						<strong><?php esc_html_e( '+ de 40.000 aprovados', 'proenem-wordpress-theme' ); ?></strong>
						<span><?php esc_html_e( 'em universidades públicas de todo o país.', 'proenem-wordpress-theme' ); ?></span>
					</p>
				</div>
			</div>
		</div>

		<div class="pro-hero-offer__universities">
			<p class="pro-hero-offer__universities-label"><?php esc_html_e( 'Alunos aprovados em', 'proenem-wordpress-theme' ); ?></p>
			<ul class="pro-hero-offer__universities-list">
				<?php foreach ( $universities as $university ) : ?>
					<li>
						<img
							src="<?php echo esc_url( proenem_home_asset_uri( $university['file'] ) ); ?>"
							alt="<?php echo esc_attr( $university['name'] ); ?>"
							<?php echo proenem_home_image_attributes( $university['file'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped by the helper. ?>
						>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php get_template_part( 'template-parts/home/sections' ); ?>

	<?php proenem_render_site_footer(); ?>
</main>

<?php
get_footer();
