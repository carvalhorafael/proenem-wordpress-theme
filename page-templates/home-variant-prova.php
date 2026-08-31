<?php
/**
 * Template Name: Proenem Home - Variante B (Prova social)
 * Template Post Type: page
 *
 * Conversion variant of the home first fold.
 *
 * Hypothesis: cold traffic hesitates on trust before it hesitates on price. A
 * first fold led by verified approvals, with one action and the price stated
 * under the button, should convert better than a promise-only hero.
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
$countdown    = proenem_get_home_exam_countdown();

$university_names = implode(
	', ',
	array_map(
		static function ( $university ) {
			return $university['name'];
		},
		$universities
	)
);
?>

<main id="primary" class="site-main pro-home pro-home--variant pro-home--variant-prova">
	<?php
	proenem_render_site_navbar(
		array(
			'aria_label' => __( 'Navegação da home', 'proenem-wordpress-theme' ),
			'context'    => 'home',
		)
	);
	proenem_render_mobile_persistent_action(
		array(
			'label' => __( 'Começar agora', 'proenem-wordpress-theme' ),
			'url'   => $offer['checkout_url'],
		)
	);
	?>

	<section class="pen-hero-section pro-hero-proof" aria-labelledby="pro-home-title">
		<div class="pro-hero-proof__stage">
			<p class="pro-hero-proof__badge">
				<span class="pro-hero-proof__badge-number"><?php esc_html_e( '+40.000', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'alunos aprovados em universidades públicas', 'proenem-wordpress-theme' ); ?></span>
			</p>

			<h1 id="pro-home-title" class="pro-hero-proof__title">
				<span class="pro-hero-proof__title-line"><?php esc_html_e( 'Todo ano, milhares passam', 'proenem-wordpress-theme' ); ?></span>
				<span class="pro-hero-proof__title-line"><?php esc_html_e( 'com a Proenem.', 'proenem-wordpress-theme' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--yellow"><?php esc_html_e( 'Agora é a sua vez.', 'proenem-wordpress-theme' ); ?></strong></span>
			</h1>

			<p class="pro-hero-proof__subtitle">
				<?php esc_html_e( 'Do diagnóstico ao dia da prova, o Método PRO transforma esforço em nota: plano semanal, aulas, mais de 60 mil questões, simulados com TRI e redação corrigida.', 'proenem-wordpress-theme' ); ?>
			</p>

			<div class="pro-hero-proof__action">
				<a class="pen-button pen-button--lg pro-hero-proof__cta" href="<?php echo esc_url( $offer['checkout_url'] ); ?>" data-pro-hero-variant="prova" data-pro-hero-action="checkout">
					<span class="pro-hero-cta__label">
						<?php esc_html_e( 'Garantir minha vaga na Turma Intensiva', 'proenem-wordpress-theme' ); ?>
						<span class="pen-button__arrow" aria-hidden="true">-&gt;</span>
					</span>
				</a>
				<p class="pro-hero-proof__price">
					<?php
					printf(
						/* translators: 1: Instalment prefix, e.g. 12x de. 2: Instalment price. 3: Guarantee, e.g. 7 dias de garantia. */
						esc_html__( '%1$s %2$s — %3$s, cancele quando quiser', 'proenem-wordpress-theme' ),
						esc_html( $offer['price_prefix'] ),
						esc_html( $offer['price'] ),
						esc_html( $offer['guarantee'] )
					);
					?>
				</p>
				<a class="pro-hero-proof__secondary" href="<?php echo esc_url( $offer['plans_url'] ); ?>" data-pro-hero-variant="prova" data-pro-hero-action="plans">
					<?php esc_html_e( 'Ver o que está incluído', 'proenem-wordpress-theme' ); ?>
				</a>
			</div>

			<?php if ( '' !== $countdown ) : ?>
				<p class="pro-hero-proof__countdown"><?php echo esc_html( $countdown ); ?></p>
			<?php endif; ?>
		</div>

		<div class="pro-hero-proof__evidence">
			<ul class="pro-hero-proof__gallery" aria-hidden="true">
				<?php foreach ( $avatars as $avatar ) : ?>
					<li>
						<img
							src="<?php echo esc_url( proenem_home_asset_uri( $avatar ) ); ?>"
							alt=""
							<?php echo proenem_home_image_attributes( $avatar, array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped by the helper. ?>
						>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="pro-hero-proof__evidence-copy">
				<?php
				printf(
					/* translators: %s: Comma separated list of universities. */
					esc_html__( 'Aprovados em %s e em dezenas de outras universidades públicas.', 'proenem-wordpress-theme' ),
					esc_html( $university_names )
				);
				?>
			</p>
		</div>

		<div class="pro-hero-proof__universities">
			<ul class="pro-hero-proof__universities-list">
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
