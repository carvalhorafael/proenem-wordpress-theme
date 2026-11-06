<?php
/**
 * Template Name: Proenem Home - PROMOBLACK
 * Template Post Type: page
 *
 * @package Proenem
 */

get_header();

$plans          = proenem_get_promoblack_plans();
$pillars        = proenem_get_promoblack_pillars();
$comparison     = proenem_get_promoblack_comparison();
$faq            = proenem_get_promoblack_faq();
$copy           = proenem_get_promoblack_copy();
$deadline       = proenem_get_promoblack_deadline_iso();
$deadline_label = proenem_get_promoblack_deadline_label();
$sisu_text      = proenem_get_promoblack_setting( 'sisu_text' );
$sisu_note      = proenem_get_promoblack_setting( 'sisu_note' );
$sisu_visible   = '' !== $sisu_text && '' !== $sisu_note;
$sisu_rules     = proenem_get_promoblack_setting( 'sisu_rules_url' );
$login_links    = array_filter(
	array(
		'proenem'     => array(
			'label' => __( 'Acessar Proenem', 'proenem-wordpress-theme' ),
			'url'   => proenem_get_promoblack_setting( 'login_url' ),
		),
		'promedicina' => array(
			'label' => __( 'Acessar Promedicina', 'proenem-wordpress-theme' ),
			'url'   => proenem_get_promoblack_setting( 'login_med_url' ),
		),
	),
	static function ( $link ) {
		return '' !== $link['url'];
	}
);
$desktop_media  = '(min-width: 1200px)';

$proenem_promoblack_wordmark = static function ( $tag = 'span', $classes = 'pro-pb-wordmark' ) {
	printf(
		'<%1$s class="%2$s"><span>%3$s</span><span class="pro-pb-wordmark__accent">%4$s</span></%1$s>',
		tag_escape( $tag ),
		esc_attr( $classes ),
		esc_html__( 'PROMO', 'proenem-wordpress-theme' ),
		esc_html__( 'BLACK', 'proenem-wordpress-theme' )
	);
};

$proenem_promoblack_plan_name = static function ( $plan ) {
	$name   = (string) $plan['name'];
	$accent = isset( $plan['name_accent'] ) ? (string) $plan['name_accent'] : '';

	if ( '' === $accent || substr( $name, -strlen( $accent ) ) !== $accent ) {
		echo esc_html( $name );
		return;
	}

	echo esc_html( rtrim( substr( $name, 0, -strlen( $accent ) ) ) ) . ' ';
	echo '<span class="pro-pb-gradient-text">' . esc_html( $accent ) . '</span>';
};

$proenem_promoblack_cell = static function ( $value ) {
	if ( is_array( $value ) ) {
		echo '<span class="pro-pb-compare__with-note">';
		printf(
			'<img class="pro-pb-compare__icon" src="%1$s" alt="%2$s" width="24" height="24">',
			esc_url( proenem_promoblack_asset_uri( 'table-check.svg' ) ),
			esc_attr__( 'Incluso', 'proenem-wordpress-theme' )
		);
		echo '<span class="pro-pb-compare__note">' . esc_html( $value['note'] ) . '</span></span>';
		return;
	}

	if ( true === $value ) {
		printf(
			'<img class="pro-pb-compare__icon" src="%1$s" alt="%2$s" width="24" height="24">',
			esc_url( proenem_promoblack_asset_uri( 'table-check.svg' ) ),
			esc_attr__( 'Incluso', 'proenem-wordpress-theme' )
		);
		return;
	}

	if ( false === $value ) {
		printf(
			'<img class="pro-pb-compare__icon" src="%1$s" alt="%2$s" width="24" height="24">',
			esc_url( proenem_promoblack_asset_uri( 'cross.svg' ) ),
			esc_attr__( 'Não incluso', 'proenem-wordpress-theme' )
		);
		return;
	}

	echo esc_html( $value );
};

$proenem_promoblack_background = static function ( $name, $width, $height, $mobile_width, $mobile_height, $eager = false ) use ( $desktop_media ) {
	?>
	<picture class="pro-pb-art" aria-hidden="true">
		<source
			media="<?php echo esc_attr( $desktop_media ); ?>"
			srcset="<?php echo esc_url( proenem_promoblack_asset_uri( $name . '-desktop.webp' ) ); ?> 1x, <?php echo esc_url( proenem_promoblack_asset_uri( $name . '-desktop@2x.webp' ) ); ?> 2x"
			width="<?php echo esc_attr( $width ); ?>"
			height="<?php echo esc_attr( $height ); ?>"
		>
		<img
			class="pro-pb-art__image"
			src="<?php echo esc_url( proenem_promoblack_asset_uri( $name . '-mobile.webp' ) ); ?>"
			alt=""
			width="<?php echo esc_attr( $mobile_width ); ?>"
			height="<?php echo esc_attr( $mobile_height ); ?>"
			<?php if ( $eager ) : ?>
				fetchpriority="high"
			<?php else : ?>
				loading="lazy"
			<?php endif; ?>
			decoding="async"
		>
	</picture>
	<?php
};
?>

<main id="primary" class="site-main pro-pb">
	<section class="pro-pb-hero" aria-labelledby="pro-pb-title">
		<?php $proenem_promoblack_background( 'hero-bg', 1440, 900, 390, 920, true ); ?>

		<div class="pro-pb-hero__brand">
			<a class="pro-pb-hero__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'logo-proenem-branca.webp' ) ); ?>" alt="<?php esc_attr_e( 'Proenem', 'proenem-wordpress-theme' ); ?>" width="154" height="43">
			</a>
			<div class="pro-pb-hero__actions">
				<?php $proenem_promoblack_wordmark( 'p', 'pro-pb-wordmark pro-pb-hero__wordmark' ); ?>
				<?php if ( ! empty( $login_links ) ) : ?>
					<details class="pro-pb-login" data-pro-pb-login>
						<summary class="pro-pb-login__toggle"><?php esc_html_e( 'Login', 'proenem-wordpress-theme' ); ?></summary>
						<ul class="pro-pb-login__menu">
							<?php foreach ( $login_links as $login_id => $login_link ) : ?>
								<li>
									<a class="pro-pb-login__link" href="<?php echo esc_url( $login_link['url'] ); ?>" data-pro-promoblack-action="login-<?php echo esc_attr( $login_id ); ?>"><?php echo esc_html( $login_link['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</details>
				<?php endif; ?>
			</div>
		</div>

		<div class="pro-pb-hero__content">
			<p class="pro-pb-pill">
				<?php $proenem_promoblack_wordmark(); ?>
				<span><?php esc_html_e( '· O menor preço do ano para o Enem 2027', 'proenem-wordpress-theme' ); ?></span>
			</p>

			<h1 id="pro-pb-title" class="pro-pb-hero__title">
				<?php esc_html_e( 'Um novo jeito de estudar para o', 'proenem-wordpress-theme' ); ?>
				<span class="pro-pb-gradient-text"><?php esc_html_e( 'Enem 2027', 'proenem-wordpress-theme' ); ?></span>
			</h1>

			<p class="pro-pb-hero__subtitle">
				<?php esc_html_e( 'Plano de estudos, questões, simulados com TRI e redação corrigida em um só lugar. Você aprende a fazer o Enem com estratégia e acompanha sua performance a cada etapa da preparação.', 'proenem-wordpress-theme' ); ?>
			</p>

			<p class="pro-pb-live">
				<span class="pro-pb-live__dot" aria-hidden="true"></span>
				<span>
					<?php $proenem_promoblack_wordmark(); ?>
					<?php esc_html_e( 'no ar: o lote atual está aberto', 'proenem-wordpress-theme' ); ?>
				</span>
			</p>

			<div class="pro-pb-hero__action">
				<a class="pro-pb-button pro-pb-button--primary pro-pb-button--hero" href="#planos" data-pro-promoblack-action="hero-plans">
					<span><?php esc_html_e( 'Escolher meu plano', 'proenem-wordpress-theme' ); ?></span>
					<img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'cta-arrow.svg' ) ); ?>" alt="" width="54" height="54">
				</a>
				<p class="pro-pb-hero__support"><?php esc_html_e( 'Pagamento em até 12x · Mais de 44 mil aprovados', 'proenem-wordpress-theme' ); ?></p>
			</div>
		</div>
	</section>

	<section class="pro-pb-pillars" aria-labelledby="pro-pb-pillars-title">
		<h2 id="pro-pb-pillars-title" class="pro-pb-section-title pro-pb-pillars__title">
			<?php esc_html_e( 'Todo ano, milhares passam com o Proenem.', 'proenem-wordpress-theme' ); ?>
			<span class="pro-pb-gradient-text"><?php esc_html_e( 'Agora é a sua vez.', 'proenem-wordpress-theme' ); ?></span>
		</h2>

		<ul class="pro-pb-pillars__list">
			<?php foreach ( $pillars as $pillar ) : ?>
				<?php $pillar_picture = proenem_get_promoblack_picture( $pillar['image'] ); ?>
				<li class="pro-pb-pillar">
					<picture class="pro-pb-pillar__media">
						<source media="<?php echo esc_attr( $desktop_media ); ?>" srcset="<?php echo esc_url( $pillar_picture['desktop'] ); ?>" width="564" height="380">
						<img
							class="pro-pb-pillar__image"
							src="<?php echo esc_url( $pillar_picture['mobile'] ); ?>"
							alt=""
							width="700"
							height="400"
							loading="lazy"
							decoding="async"
						>
					</picture>
					<div class="pro-pb-pillar__body">
						<p class="pro-pb-pillar__eyebrow">
							<span class="pro-pb-pillar__number"><?php echo esc_html( $pillar['number'] ); ?></span>
							<span class="pro-pb-pillar__label"><?php echo esc_html( $pillar['label'] ); ?></span>
						</p>
						<h3 class="pro-pb-pillar__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
						<p class="pro-pb-pillar__text"><?php echo esc_html( $pillar['body'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<a class="pro-pb-button pro-pb-button--primary pro-pb-pillars__cta" href="#planos" data-pro-promoblack-action="pillars-plans">
			<span><?php esc_html_e( 'Ver os planos', 'proenem-wordpress-theme' ); ?></span>
			<span aria-hidden="true">→</span>
		</a>
	</section>

	<section id="planos" class="pro-pb-plans" aria-labelledby="pro-pb-plans-title">
		<h2 id="pro-pb-plans-title" class="pro-pb-section-title">
			<?php esc_html_e( 'Escolha o', 'proenem-wordpress-theme' ); ?>
			<span class="pro-pb-gradient-text"><?php esc_html_e( 'seu plano', 'proenem-wordpress-theme' ); ?></span>
		</h2>
		<p class="pro-pb-section-subtitle"><?php echo esc_html( $copy['plans_subtitle'] ); ?></p>

		<?php if ( '' !== $deadline ) : ?>
			<div class="pro-pb-lots">
				<div class="pro-pb-lots__current">
					<div class="pro-pb-lots__label">
						<p class="pro-pb-lots__status"><span class="pro-pb-lots__status-dot" aria-hidden="true"></span><?php esc_html_e( 'Aberto agora', 'proenem-wordpress-theme' ); ?></p>
						<p class="pro-pb-lots__ends"><?php esc_html_e( 'Este lote acaba em', 'proenem-wordpress-theme' ); ?></p>
					</div>
					<?php proenem_render_promoblack_countdown(); ?>
				</div>
				<div class="pro-pb-lots__upcoming">
					<?php for ( $lot = 0; $lot < 2; $lot++ ) : ?>
						<div class="pro-pb-lots__next">
							<img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'lock.svg' ) ); ?>" alt="" width="40" height="40">
							<p><?php esc_html_e( 'Próximo lote', 'proenem-wordpress-theme' ); ?></p>
						</div>
					<?php endfor; ?>
				</div>
			</div>
			<p class="pro-pb-lots__note"><?php echo esc_html( $copy['lots_note'] ); ?></p>
		<?php endif; ?>

		<div class="pro-pb-plans__list">
			<?php foreach ( $plans as $plan_id => $plan ) : ?>
				<article class="pro-pb-plan<?php echo ! empty( $plan['featured'] ) ? ' pro-pb-plan--featured' : ''; ?>" aria-labelledby="pro-pb-plan-<?php echo esc_attr( $plan_id ); ?>">
					<header class="pro-pb-plan__header">
						<?php if ( '' !== $plan['badge'] ) : ?>
							<p class="pro-pb-plan__badge"><?php echo esc_html( $plan['badge'] ); ?></p>
						<?php endif; ?>
						<h3 id="pro-pb-plan-<?php echo esc_attr( $plan_id ); ?>" class="pro-pb-plan__name"><?php $proenem_promoblack_plan_name( $plan ); ?></h3>
					</header>
					<p class="pro-pb-plan__description"><?php echo esc_html( $plan['description'] ); ?></p>
					<?php if ( '' !== $plan['cash_price'] ) : ?>
						<div class="pro-pb-price">
							<?php if ( '' !== $plan['list_price'] ) : ?>
								<p class="pro-pb-price__from">
									<s>
										<?php
										/* translators: %s: Original plan price. */
										echo esc_html( sprintf( __( 'De %s', 'proenem-wordpress-theme' ), $plan['list_price'] ) );
										?>
									</s>
								</p>
							<?php endif; ?>
							<p class="pro-pb-price__cash">
								<?php
								/* translators: %s: Promotional cash price. */
								echo esc_html( sprintf( __( 'Por %s à vista', 'proenem-wordpress-theme' ), $plan['cash_price'] ) );
								?>
							</p>
							<?php if ( '' !== $plan['discount'] || '' !== $plan['price'] ) : ?>
								<p class="pro-pb-price__installments">
									<?php if ( '' !== $plan['discount'] ) : ?>
										<span class="pro-pb-price__discount"><?php echo esc_html( $plan['discount'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $plan['price'] ) : ?>
										<span>
											<?php
											/* translators: %s: Installment value. */
											echo esc_html( sprintf( __( 'ou 12x de %s', 'proenem-wordpress-theme' ), $plan['price'] ) );
											?>
										</span>
									<?php endif; ?>
								</p>
							<?php endif; ?>
							<?php if ( '' !== $plan['coupon'] ) : ?>
								<p class="pro-pb-price__coupon">
									<span class="pro-pb-price__coupon-label"><?php esc_html_e( 'Use o cupom', 'proenem-wordpress-theme' ); ?></span>
									<strong class="pro-pb-price__coupon-code"><?php echo esc_html( $plan['coupon'] ); ?></strong>
								</p>
							<?php endif; ?>
						</div>
					<?php elseif ( '' !== $plan['price'] ) : ?>
						<p class="pro-pb-plan__price">
							<span class="pro-pb-plan__price-prefix"><?php esc_html_e( '12x de', 'proenem-wordpress-theme' ); ?></span>
							<span class="pro-pb-plan__price-value"><?php echo esc_html( $plan['price'] ); ?></span>
						</p>
					<?php endif; ?>
					<p class="pro-pb-plan__includes"><?php esc_html_e( 'Inclui', 'proenem-wordpress-theme' ); ?></p>
					<ul class="pro-pb-plan__items">
						<?php foreach ( $plan['items'] as $item ) : ?>
							<li>
								<img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'list-check.svg' ) ); ?>" alt="" width="24" height="24">
								<span><?php echo esc_html( $item ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( '' !== $plan['url'] ) : ?>
						<a
							class="pro-pb-button pro-pb-plan__cta <?php echo ! empty( $plan['featured'] ) ? 'pro-pb-button--primary' : 'pro-pb-button--outline'; ?>"
							href="<?php echo esc_url( $plan['url'] ); ?>"
							data-pro-promoblack-action="checkout-<?php echo esc_attr( $plan_id ); ?>"
						>
							<span><?php echo esc_html( $plan['cta'] ); ?></span>
							<span aria-hidden="true">→</span>
						</a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $sisu_visible ) : ?>
		<aside class="pro-pb-sisu" aria-label="<?php esc_attr_e( 'Garantia SISU 2027', 'proenem-wordpress-theme' ); ?>">
			<img class="pro-pb-sisu__icon" src="<?php echo esc_url( proenem_promoblack_asset_uri( 'sisu-alert.svg' ) ); ?>" alt="" width="44" height="44">
			<p class="pro-pb-sisu__text">
				<?php
				echo esc_html( $sisu_text );
				printf(
					'<a class="pro-pb-sisu__mark" href="#pro-pb-sisu-note" aria-label="%s">*</a>',
					esc_attr__( 'Ler as condições da garantia', 'proenem-wordpress-theme' )
				);
				?>
			</p>
			<img class="pro-pb-sisu__icon" src="<?php echo esc_url( proenem_promoblack_asset_uri( 'sisu-alert.svg' ) ); ?>" alt="" width="44" height="44">
		</aside>
	<?php endif; ?>

	<section class="pro-pb-compare" aria-labelledby="pro-pb-compare-title">
		<h2 id="pro-pb-compare-title" class="pro-pb-section-title">
			<?php esc_html_e( 'Qual plano', 'proenem-wordpress-theme' ); ?>
			<span class="pro-pb-gradient-text"><?php esc_html_e( 'é o seu?', 'proenem-wordpress-theme' ); ?></span>
		</h2>
		<p class="pro-pb-section-subtitle"><?php echo esc_html( $copy['compare_subtitle'] ); ?></p>

		<div class="pro-pb-compare__frame">
			<table class="pro-pb-compare__table">
				<caption class="pro-pb-compare__caption"><?php echo esc_html( $copy['compare_caption'] ); ?></caption>
				<thead>
					<tr>
						<td></td>
						<th scope="col">
							<span class="pro-pb-compare__chip">
								<span class="pro-pb-compare__chip-full"><?php echo esc_html( $plans['enem-pro']['name'] ); ?></span>
								<span class="pro-pb-compare__chip-short" aria-hidden="true"><?php echo esc_html( $plans['enem-pro']['short_name'] ); ?></span>
							</span>
						</th>
						<th scope="col" class="pro-pb-compare__featured">
							<span class="pro-pb-compare__chip pro-pb-compare__chip--featured">
								<span class="pro-pb-compare__chip-full"><?php echo esc_html( $plans['med']['name'] ); ?></span>
								<span class="pro-pb-compare__chip-short" aria-hidden="true"><?php echo esc_html( $plans['med']['short_name'] ); ?></span>
							</span>
						</th>
					</tr>
				</thead>
				<?php foreach ( $comparison as $group ) : ?>
					<tbody>
						<tr class="pro-pb-compare__group">
							<th scope="colgroup" colspan="2"><?php echo esc_html( $group['title'] ); ?></th>
							<td class="pro-pb-compare__featured"></td>
						</tr>
						<?php foreach ( $group['rows'] as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<td><?php $proenem_promoblack_cell( $row['enem_pro'] ); ?></td>
								<td class="pro-pb-compare__featured"><?php $proenem_promoblack_cell( $row['med'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				<?php endforeach; ?>
			</table>
		</div>

		<ul class="pro-pb-assurance">
			<li><img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'assurance-parcelamento.svg' ) ); ?>" alt="" width="48" height="48"><span><?php esc_html_e( 'Pagamento em até 12x', 'proenem-wordpress-theme' ); ?></span></li>
			<li><img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'assurance-hotmart.svg' ) ); ?>" alt="" width="48" height="48"><span><?php esc_html_e( 'Compra pela Hotmart, com ambiente seguro', 'proenem-wordpress-theme' ); ?></span></li>
			<li><img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'assurance-garantia.svg' ) ); ?>" alt="" width="48" height="48"><span><?php esc_html_e( '7 dias para desistir da compra', 'proenem-wordpress-theme' ); ?></span></li>
		</ul>
	</section>

	<section id="faq" class="pro-pb-faq" aria-labelledby="pro-pb-faq-title">
		<h2 id="pro-pb-faq-title" class="pro-pb-section-title pro-pb-faq__title">
			<?php esc_html_e( 'Perguntas', 'proenem-wordpress-theme' ); ?>
			<span class="pro-pb-gradient-text"><?php esc_html_e( 'frequentes', 'proenem-wordpress-theme' ); ?></span>
		</h2>
		<div class="pro-pb-faq__list">
			<?php foreach ( $faq as $index => $item ) : ?>
				<details class="pro-pb-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
					<summary class="pro-pb-faq__question">
						<span><?php echo esc_html( $item['question'] ); ?></span>
						<img class="pro-pb-faq__icon pro-pb-faq__icon--open" src="<?php echo esc_url( proenem_promoblack_asset_uri( 'faq-plus.svg' ) ); ?>" alt="" width="32" height="32">
						<img class="pro-pb-faq__icon pro-pb-faq__icon--close" src="<?php echo esc_url( proenem_promoblack_asset_uri( 'faq-minus.svg' ) ); ?>" alt="" width="32" height="32">
					</summary>
					<p class="pro-pb-faq__answer"><?php echo esc_html( $item['answer'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="pro-pb-final" aria-labelledby="pro-pb-final-title">
		<?php $proenem_promoblack_background( 'final-bg', 1440, 748, 390, 971 ); ?>

		<div class="pro-pb-final__content">
			<p class="pro-pb-wordmark pro-pb-final__wordmark">
				<span><?php esc_html_e( 'PROMO', 'proenem-wordpress-theme' ); ?></span><span class="pro-pb-wordmark__accent"><?php esc_html_e( 'BLACK', 'proenem-wordpress-theme' ); ?></span>
				<img class="pro-pb-final__star" src="<?php echo esc_url( proenem_promoblack_asset_uri( 'estrela.svg' ) ); ?>" alt="" width="11" height="11">
			</p>
			<h2 id="pro-pb-final-title" class="pro-pb-final__title">
				<?php esc_html_e( 'Comece o seu', 'proenem-wordpress-theme' ); ?>
				<span class="pro-pb-gradient-text"><?php esc_html_e( 'novo jeito de estudar', 'proenem-wordpress-theme' ); ?></span>
			</h2>
			<p class="pro-pb-final__body">
				<?php if ( '' !== $deadline_label ) : ?>
					<?php
					printf(
						/* translators: %s: Lot closing date and time. */
						esc_html__( 'O lote atual da PROMOBLACK acaba em %s.', 'proenem-wordpress-theme' ),
						'<strong>' . esc_html( $deadline_label ) . '</strong>'
					);
					?>
				<?php endif; ?>
				<?php echo esc_html( $copy['final_body'] ); ?>
			</p>
			<?php proenem_render_promoblack_countdown( 'large' ); ?>
			<div class="pro-pb-final__actions">
				<a class="pro-pb-button pro-pb-button--outline" href="<?php echo esc_url( '' !== $plans['enem-pro']['url'] ? $plans['enem-pro']['url'] : '#planos' ); ?>" data-pro-promoblack-action="final-enem-pro">
					<span><?php echo esc_html( $plans['enem-pro']['cta'] ); ?></span>
					<span aria-hidden="true">→</span>
				</a>
				<a class="pro-pb-button pro-pb-button--primary" href="<?php echo esc_url( '' !== $plans['med']['url'] ? $plans['med']['url'] : '#planos' ); ?>" data-pro-promoblack-action="final-med">
					<span><?php echo esc_html( $plans['med']['cta'] ); ?></span>
					<span aria-hidden="true">→</span>
				</a>
			</div>
		</div>

		<footer class="pro-pb-footer">
			<a class="pro-pb-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( proenem_promoblack_asset_uri( 'logo-proenem-branca.webp' ) ); ?>" alt="<?php esc_attr_e( 'Proenem', 'proenem-wordpress-theme' ); ?>" width="130" height="36" loading="lazy" decoding="async">
			</a>
			<div class="pro-pb-footer__legal">
				<?php if ( $sisu_visible ) : ?>
					<p id="pro-pb-sisu-note">
						<?php echo esc_html( '* ' . $sisu_note ); ?>
						<?php if ( '' !== $sisu_rules ) : ?>
							<a href="<?php echo esc_url( $sisu_rules ); ?>"><?php echo esc_html( untrailingslashit( preg_replace( '#^https?://(www\.)?#', '', $sisu_rules ) ) ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<p><?php echo esc_html( $copy['legal'] ); ?></p>
			</div>
		</footer>
	</section>
</main>

<?php
get_footer();
