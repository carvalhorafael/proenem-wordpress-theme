<?php
/**
 * Template Name: ProEnem Home
 * Template Post Type: page
 *
 * @package Proenem
 */

get_header();

$home_asset_uri = static function ( $filename ) {
	return PROENEM_THEME_URI . '/assets/images/home/' . $filename;
};

$brand_asset_uri = static function ( $filename ) {
	return PROENEM_THEME_URI . '/assets/images/brand/' . $filename;
};

$plans = array(
	array(
		'name'     => __( 'Essencial', 'proenem-wordpress-theme' ),
		'price'    => __( '39', 'proenem-wordpress-theme' ),
		'summary'  => __( 'Para começar a estudar com método.', 'proenem-wordpress-theme' ),
		'features' => array(
			__( 'Banco com +50k questões', 'proenem-wordpress-theme' ),
			__( 'Aulas gravadas completas', 'proenem-wordpress-theme' ),
			__( 'Cronograma básico', 'proenem-wordpress-theme' ),
			__( 'Comunidade de alunos', 'proenem-wordpress-theme' ),
		),
	),
	array(
		'name'     => __( 'Método PRO', 'proenem-wordpress-theme' ),
		'price'    => __( '99', 'proenem-wordpress-theme' ),
		'summary'  => __( 'O método completo, com IA e mentoria.', 'proenem-wordpress-theme' ),
		'featured' => true,
		'features' => array(
			__( 'Tudo do Essencial', 'proenem-wordpress-theme' ),
			__( 'Tutor IA ilimitado 24/7', 'proenem-wordpress-theme' ),
			__( 'Redação corrigida em 48h', 'proenem-wordpress-theme' ),
			__( 'Simulados TRI semanais', 'proenem-wordpress-theme' ),
			__( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ),
			__( 'Plano adaptativo por IA', 'proenem-wordpress-theme' ),
		),
	),
	array(
		'name'     => __( 'Elite', 'proenem-wordpress-theme' ),
		'price'    => __( '199', 'proenem-wordpress-theme' ),
		'summary'  => __( 'Mentoria 1:1 com aprovados.', 'proenem-wordpress-theme' ),
		'features' => array(
			__( 'Tudo do Método PRO', 'proenem-wordpress-theme' ),
			__( 'Mentor pessoal aprovado em Medicina', 'proenem-wordpress-theme' ),
			__( '2 sessões 1:1 por semana', 'proenem-wordpress-theme' ),
			__( 'Revisão de redação prioritária', 'proenem-wordpress-theme' ),
		),
	),
);

$faq_items = array(
	array(
		'question' => __( 'O que é o MÉTODO PRO?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'É uma estrutura de preparação que combina meta, diagnóstico, execução e performance para organizar seus estudos até a aprovação.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'Posso começar a estudar de graça?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Sim. Você pode criar uma conta gratuita e acessar o banco de questões, listas e um diagnóstico simplificado.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'O que é a Aceleração PRO?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'É o plano para quem quer acompanhamento mais próximo, simulados com TRI, redação corrigida e rotina adaptada por IA.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'Posso entrar em qualquer época do ano?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Pode. O diagnóstico inicial ajusta o plano ao seu momento e ao tempo disponível até a prova.', 'proenem-wordpress-theme' ),
	),
);

$subjects = array(
	__( 'Química', 'proenem-wordpress-theme' ),
	__( 'Biologia', 'proenem-wordpress-theme' ),
	__( 'Matemática', 'proenem-wordpress-theme' ),
	__( 'História', 'proenem-wordpress-theme' ),
	__( 'Inglês', 'proenem-wordpress-theme' ),
	__( 'Português', 'proenem-wordpress-theme' ),
);

$home_nav_links   = array(
	array(
		'label'  => __( 'Método PRO', 'proenem-wordpress-theme' ),
		'url'    => '#metodo',
		'active' => true,
	),
	array(
		'label' => __( 'Planos', 'proenem-wordpress-theme' ),
		'url'   => '#planos',
	),
	array(
		'label' => __( 'Questões', 'proenem-wordpress-theme' ),
		'url'   => '#questoes',
	),
	array(
		'label' => __( 'Aprovados', 'proenem-wordpress-theme' ),
		'url'   => '#aprovados',
	),
	array(
		'label' => __( 'FAQ', 'proenem-wordpress-theme' ),
		'url'   => '#faq',
	),
);
$home_nav_actions = array(
	array(
		'label'   => __( 'Comece grátis', 'proenem-wordpress-theme' ),
		'url'     => '#planos',
		'variant' => 'primary',
	),
);

$nav_menu_locations = get_nav_menu_locations();

if ( ! empty( $nav_menu_locations['primary'] ) ) {
	$primary_menu_items = wp_get_nav_menu_items( $nav_menu_locations['primary'] );

	if ( ! empty( $primary_menu_items ) && ! is_wp_error( $primary_menu_items ) ) {
		$home_nav_links   = array();
		$home_nav_actions = array();

		foreach ( $primary_menu_items as $primary_menu_item ) {
			if ( '0' !== (string) $primary_menu_item->menu_item_parent ) {
				continue;
			}

			$primary_menu_item_classes = is_array( $primary_menu_item->classes )
				? $primary_menu_item->classes
				: array();
			$primary_menu_item_classes = array_filter(
				array_map( 'sanitize_html_class', $primary_menu_item_classes )
			);

			$home_nav_item = array(
				'label'   => $primary_menu_item->title,
				'url'     => $primary_menu_item->url,
				'target'  => $primary_menu_item->target,
				'rel'     => $primary_menu_item->xfn,
				'classes' => $primary_menu_item_classes,
				'active'  => ! empty(
					array_intersect(
						array(
							'current-menu-item',
							'current-menu-ancestor',
							'current_page_item',
						),
						$primary_menu_item_classes
					)
				),
			);

			if ( in_array( 'pen-navbar-action', $primary_menu_item_classes, true ) ) {
				$home_nav_item['variant'] = in_array( 'pen-navbar-action-secondary', $primary_menu_item_classes, true )
					? 'secondary'
					: 'primary';

				$home_nav_actions[] = $home_nav_item;
				continue;
			}

			$home_nav_links[] = $home_nav_item;
		}
	}
}
?>

<main id="primary" class="site-main pro-home">
	<nav class="pen-navbar" aria-label="<?php esc_attr_e( 'Navegação da home', 'proenem-wordpress-theme' ); ?>" data-pro-home-navbar>
		<a class="pen-brand-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( $brand_asset_uri( 'logo_proenem.svg' ) ); ?>" alt="<?php esc_attr_e( 'ProEnem', 'proenem-wordpress-theme' ); ?>" width="152" height="43">
		</a>
		<button class="pro-home-navbar-toggle" type="button" aria-controls="pro-home-navbar-menu" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'proenem-wordpress-theme' ); ?></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</button>
		<div id="pro-home-navbar-menu" class="pro-home-navbar-menu">
			<div class="pen-navbar__links">
			<?php foreach ( $home_nav_links as $home_nav_link ) : ?>
				<?php
				$home_nav_link_class = 'pen-navbar__link';

				if ( ! empty( $home_nav_link['active'] ) ) {
					$home_nav_link_class .= ' pen-navbar__link--active';
				}

				$home_nav_link_rel = $home_nav_link['rel'] ?? '';

				if ( '_blank' === ( $home_nav_link['target'] ?? '' ) && empty( $home_nav_link_rel ) ) {
					$home_nav_link_rel = 'noopener';
				}
				?>
				<a
					class="<?php echo esc_attr( $home_nav_link_class ); ?>"
					href="<?php echo esc_url( $home_nav_link['url'] ); ?>"
					<?php echo ! empty( $home_nav_link['target'] ) ? 'target="' . esc_attr( $home_nav_link['target'] ) . '"' : ''; ?>
					<?php echo ! empty( $home_nav_link_rel ) ? 'rel="' . esc_attr( $home_nav_link_rel ) . '"' : ''; ?>
					<?php echo ! empty( $home_nav_link['active'] ) ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $home_nav_link['label'] ); ?>
				</a>
			<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $home_nav_actions ) ) : ?>
				<div class="pen-navbar__actions">
					<?php foreach ( $home_nav_actions as $home_nav_action ) : ?>
						<?php
						$home_nav_action_variant = in_array( $home_nav_action['variant'] ?? '', array( 'primary', 'secondary' ), true )
							? $home_nav_action['variant']
							: 'primary';
						$home_nav_action_class   = 'pen-navbar__action pen-navbar__action--' . $home_nav_action_variant;
						$home_nav_action_class  .= ! empty( $home_nav_action['classes'] )
							? ' ' . implode( ' ', $home_nav_action['classes'] )
							: '';
						$home_nav_action_rel     = $home_nav_action['rel'] ?? '';

						if ( '_blank' === ( $home_nav_action['target'] ?? '' ) && empty( $home_nav_action_rel ) ) {
							$home_nav_action_rel = 'noopener';
						}
						?>
						<a
							class="<?php echo esc_attr( $home_nav_action_class ); ?>"
							href="<?php echo esc_url( $home_nav_action['url'] ); ?>"
							<?php echo ! empty( $home_nav_action['target'] ) ? 'target="' . esc_attr( $home_nav_action['target'] ) . '"' : ''; ?>
							<?php echo ! empty( $home_nav_action_rel ) ? 'rel="' . esc_attr( $home_nav_action_rel ) . '"' : ''; ?>
						>
							<?php echo esc_html( $home_nav_action['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</nav>

	<section class="pen-hero-section" aria-labelledby="pro-home-title">
		<div class="pen-hero-section__stage">
			<img class="pen-hero-section__image" src="<?php echo esc_url( $home_asset_uri( 'hero-student.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com cadernos nas mãos.', 'proenem-wordpress-theme' ); ?>">
			<span class="pen-hero-sticker pen-hero-sticker--pink"><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--yellow"><?php esc_html_e( 'Performance', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--green"><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--orange"><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-home-title" class="pen-hero-section__title">
				<span><?php esc_html_e( 'Sua', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'não é', 'proenem-wordpress-theme' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--blue"><?php esc_html_e( 'sorte', 'proenem-wordpress-theme' ); ?></strong></span>
				<span><?php esc_html_e( 'é', 'proenem-wordpress-theme' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--yellow"><?php esc_html_e( 'método', 'proenem-wordpress-theme' ); ?></strong></span>
			</h1>
		</div>
	</section>

	<aside class="pen-hero-action-bar">
		<p>
			<strong><?php esc_html_e( 'O Método PRO', 'proenem-wordpress-theme' ); ?></strong>
			<?php esc_html_e( 'não é apenas um cronograma, é uma engenharia de resultados dividida em 4 pilares: Meta. Diagnóstico. Execução. Performance.', 'proenem-wordpress-theme' ); ?>
		</p>
		<div class="pen-hero-action-bar__action">
			<a class="pen-button pen-button--secondary pen-button--md" href="#metodo">
				<?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?>
				<span class="pen-button__arrow" aria-hidden="true">-></span>
			</a>
		</div>
	</aside>

	<div class="pen-marquee" aria-hidden="true">
		<div class="pen-marquee__track">
			<?php for ( $marquee_iteration = 0; $marquee_iteration < 2; $marquee_iteration++ ) : ?>
				<span><?php esc_html_e( 'A engenharia da sua aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Troque a sorte pela estratégia', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
			<?php endfor; ?>
		</div>
	</div>

	<section id="metodo" class="pen-pillars-section" aria-labelledby="pro-pillars-title">
		<div class="pen-pillars-section__copy">
			<p class="pen-section-pill"><?php esc_html_e( 'Método Pro', 'proenem-wordpress-theme' ); ?></p>
			<h2 id="pro-pillars-title"><?php esc_html_e( 'Os 4 pilares que organizam a sua aprovação', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'O Método PRO não é apenas um cronograma, é uma engenharia de resultados dividida em 4 pilares: Meta. Diagnóstico. Execução. Performance.', 'proenem-wordpress-theme' ); ?></p>
			<p><?php esc_html_e( 'O ENEM não é prova de quem estuda mais. É de quem estuda com estratégia.', 'proenem-wordpress-theme' ); ?></p>
			<a class="pen-button pen-button--primary pen-button--md" href="#planos"><?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
		</div>
		<div class="pen-pillars-section__cards" data-pro-home-pillars-slider>
			<div class="pro-home-pillars-badge" aria-hidden="true"></div>
			<div class="pro-home-pillars-control" aria-label="<?php esc_attr_e( 'Navegação dos pilares', 'proenem-wordpress-theme' ); ?>">
				<button type="button" data-pro-home-pillars-prev aria-label="<?php esc_attr_e( 'Pilar anterior', 'proenem-wordpress-theme' ); ?>">‹</button>
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
				<button type="button" data-pro-home-pillars-next aria-label="<?php esc_attr_e( 'Próximo pilar', 'proenem-wordpress-theme' ); ?>">›</button>
			</div>
			<article class="pen-step-card pen-step-card--blue" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-meta.webp' ) ); ?>" alt="">
				<span>01</span>
				<div>
					<h3><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Transformamos objetivo em plano de estudo claro, com prioridades e metas semanais.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--featured is-active" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-diagnostico.webp' ) ); ?>" alt="">
				<span>02</span>
				<div>
					<h3><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Mapeamos suas forças e lacunas com simulados adaptativos. Você vê exatamente onde está.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--red" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-execucao.webp' ) ); ?>" alt="">
				<span>03</span>
				<div>
					<h3><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Você sabe o que estudar, quando revisar e como corrigir rota sem perder ritmo.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--pink" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-meta.webp' ) ); ?>" alt="">
				<span>04</span>
				<div>
					<h3><?php esc_html_e( 'Performance', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Acompanhamos sua evolução para trocar esforço solto por resultado mensurável.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
		</div>
	</section>

	<?php
	$proof_student_images = array(
		'proof-students-1.webp',
		'proof-students-2.png',
		'proof-students-3.png',
		'proof-students-4.png',
		'proof-students-5.png',
		'proof-students-6.png',
	);

	$proof_university_logos = array(
		array(
			'file' => 'proof-logo-ufrj.png',
			'name' => __( 'UFRJ', 'proenem-wordpress-theme' ),
		),
		array(
			'file' => 'proof-logo-ufrgs.png',
			'name' => __( 'UFRGS', 'proenem-wordpress-theme' ),
		),
		array(
			'file' => 'proof-logo-unicamp.png',
			'name' => __( 'Unicamp', 'proenem-wordpress-theme' ),
		),
		array(
			'file' => 'proof-logo-uerj.png',
			'name' => __( 'UERJ', 'proenem-wordpress-theme' ),
		),
		array(
			'file' => 'proof-logo-usp.png',
			'name' => __( 'USP', 'proenem-wordpress-theme' ),
		),
		array(
			'file' => 'proof-logo-unifesp.png',
			'name' => __( 'Unifesp', 'proenem-wordpress-theme' ),
		),
	);
	?>
	<section id="aprovados" class="pen-proof-section" aria-labelledby="pro-proof-title">
		<div class="pen-proof-section__students">
			<p class="pen-proof-section__badge">
				<span><?php esc_html_e( 'Nossos', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Alunos!', 'proenem-wordpress-theme' ); ?></span>
			</p>
			<?php foreach ( $proof_student_images as $proof_student_image ) : ?>
				<img class="pen-proof-section__image" src="<?php echo esc_url( $home_asset_uri( $proof_student_image ) ); ?>" alt="<?php esc_attr_e( 'Aluno aprovado exibindo aprovação.', 'proenem-wordpress-theme' ); ?>">
			<?php endforeach; ?>
		</div>
		<div class="pen-proof-section__strip">
			<h2 id="pro-proof-title"><?php esc_html_e( '+ de 40.000 aprovados em universidades públicas', 'proenem-wordpress-theme' ); ?></h2>
			<div class="pen-proof-section__logos" aria-label="<?php esc_attr_e( 'Universidades públicas com alunos aprovados pela Proenem', 'proenem-wordpress-theme' ); ?>">
				<?php foreach ( $proof_university_logos as $proof_university_logo ) : ?>
					<img
						class="pen-proof-section__logo"
						src="<?php echo esc_url( $home_asset_uri( $proof_university_logo['file'] ) ); ?>"
						alt="<?php echo esc_attr( $proof_university_logo['name'] ); ?>"
					>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="pen-feature-grid-section" aria-labelledby="pro-pain-title">
		<p class="pen-section-pill"><?php esc_html_e( 'Você se identifica?', 'proenem-wordpress-theme' ); ?></p>
		<h2 id="pro-pain-title"><?php esc_html_e( 'Já sentiu que estuda muito, mas a nota não sobe?', 'proenem-wordpress-theme' ); ?></h2>
		<div class="pen-feature-grid">
			<article>
				<h3><?php esc_html_e( 'Estuda sem direção', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Cronogramas bonitos, mas sem priorização. Estuda tudo igual e o resultado não aparece.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article>
				<h3><?php esc_html_e( 'Trava em simulados', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'A nota não sai do lugar. Faz simulados, mas não analisa os erros. Repete os mesmos equívocos.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article>
				<h3><?php esc_html_e( 'Ansiedade e insegurança', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Pressão familiar, medo de não passar e sensação de estar sempre atrasado em relação aos outros.', 'proenem-wordpress-theme' ); ?></p>
			</article>
		</div>
	</section>

	<section class="pen-platform-showcase" aria-labelledby="pro-platform-title">
		<h2 id="pro-platform-title"><?php esc_html_e( 'Explore por dentro cada detalhe', 'proenem-wordpress-theme' ); ?></h2>
		<div class="pen-platform-showcase__panel">
			<ul>
				<li><?php esc_html_e( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( '+50 mil questões', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Plano personalizado', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Tutor com IA', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Correção de redação', 'proenem-wordpress-theme' ); ?></li>
				<li class="is-active"><?php esc_html_e( 'Simulados com TRI', 'proenem-wordpress-theme' ); ?></li>
			</ul>
			<div class="pen-platform-showcase__screen">
				<div>
					<h3><?php esc_html_e( 'Simulados com a mesma lógica de correção do ENEM.', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Veja sua nota real, evolução por área e onde focar agora.', 'proenem-wordpress-theme' ); ?></p>
					<div class="pen-data-bars" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
				</div>
			</div>
		</div>
	</section>

	<section id="questoes" class="pen-question-bank-section" aria-labelledby="pro-questions-title">
		<h2 id="pro-questions-title"><?php esc_html_e( 'Explore +50 mil questões sem precisar criar conta.', 'proenem-wordpress-theme' ); ?></h2>
		<p><?php esc_html_e( 'Questões do ENEM e dos principais vestibulares, com resolução em vídeo. Escolha uma disciplina e comece agora.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pen-subject-grid">
			<?php foreach ( $subjects as $subject ) : ?>
				<a href="#planos">
					<strong><?php echo esc_html( $subject ); ?></strong>
					<span><?php esc_html_e( '512 questões · 40 aulas', 'proenem-wordpress-theme' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<a class="pen-action-link" href="#planos"><?php esc_html_e( 'Comece agora! É gratuito', 'proenem-wordpress-theme' ); ?></a>
	</section>

	<section id="planos" class="pen-pricing-section" aria-labelledby="pro-pricing-title">
		<h2 id="pro-pricing-title"><?php esc_html_e( 'Investimento que se paga em uma vaga.', 'proenem-wordpress-theme' ); ?></h2>
		<p><?php esc_html_e( 'Comece grátis. Cancele com 1 clique. 7 dias de garantia em todos os planos.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pen-plan-grid">
			<?php foreach ( $plans as $plan ) : ?>
				<article class="pen-plan-card<?php echo ! empty( $plan['featured'] ) ? ' is-featured' : ''; ?>">
					<header>
						<h3><?php echo esc_html( $plan['name'] ); ?></h3>
						<p><?php echo esc_html( $plan['summary'] ); ?></p>
						<strong><span><?php esc_html_e( 'R$', 'proenem-wordpress-theme' ); ?></span><?php echo esc_html( $plan['price'] ); ?><small><?php esc_html_e( 'ao mês', 'proenem-wordpress-theme' ); ?></small></strong>
					</header>
					<ul>
						<?php foreach ( $plan['features'] as $feature ) : ?>
							<li><?php echo esc_html( $feature ); ?></li>
						<?php endforeach; ?>
					</ul>
					<a class="pen-action-link pen-action-link--primary" href="#faq"><?php esc_html_e( 'Quero o Método PRO', 'proenem-wordpress-theme' ); ?> <span aria-hidden="true">-></span></a>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="pen-audience-section" aria-labelledby="pro-school-title">
		<div class="pen-audience-section__intro">
			<h2 id="pro-school-title"><?php esc_html_e( 'Leve o Método PRO para a sua escola.', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Planos especiais para instituições que querem oferecer a melhor preparação para o ENEM em um único pacote.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-feature-grid pen-feature-grid--school">
			<article><h3><?php esc_html_e( 'Combo plataforma + apostilas', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Acesso completo à plataforma e kit de apostilas exclusivas.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Acompanhe cada aluno', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Painel exclusivo para desempenho, simulados e frequência.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Acesso para todos os alunos', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Licenças geradas para todas as turmas.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Onboarding dedicado', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Um especialista acompanha os primeiros ciclos.', 'proenem-wordpress-theme' ); ?></p></article>
		</div>
	</section>

	<section class="pen-marketing-cta pro-home__final-cta" aria-labelledby="pro-final-title">
		<div class="pen-marketing-cta__content">
			<h2 id="pro-final-title"><?php esc_html_e( 'Pronto para transformar a preparação ENEM na sua escola?', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Converse com nossa equipe e receba uma proposta personalizada.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-marketing-cta__actions">
			<a class="pen-button pen-button--primary pen-button--lg" href="#faq"><?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
		</div>
	</section>

	<section id="faq" class="pen-faq-section" aria-labelledby="pro-faq-title">
		<div class="pen-faq-section__header">
			<span class="pen-pill-eyebrow"><?php esc_html_e( 'Perguntas frequentes', 'proenem-wordpress-theme' ); ?></span>
			<h2 id="pro-faq-title"><?php esc_html_e( 'Já sentiu que estuda muito, mas a nota não sobe?', 'proenem-wordpress-theme' ); ?></h2>
		</div>
		<div class="pen-faq-section__items">
			<?php foreach ( $faq_items as $index => $item ) : ?>
				<details class="pen-faq-item" <?php echo 1 === $index ? 'open' : ''; ?>>
					<summary><?php echo esc_html( $item['question'] ); ?></summary>
					<p><?php echo esc_html( $item['answer'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</section>

	<footer class="pen-site-footer">
		<div class="pen-site-footer__content">
			<p class="pen-section-pill"><?php esc_html_e( 'Manifesto Proenem', 'proenem-wordpress-theme' ); ?></p>
			<h2 class="pen-site-footer__title"><?php esc_html_e( 'Sua Aprovação não é sorte. É método.', 'proenem-wordpress-theme' ); ?></h2>
			<p class="pen-site-footer__body"><?php esc_html_e( 'Construímos a infraestrutura que transforma esforço em resultado. Você estuda, a engenharia faz o resto.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-site-footer__links">
			<nav class="pen-site-footer__column" aria-label="<?php esc_attr_e( 'Links do rodapé da home', 'proenem-wordpress-theme' ); ?>">
				<h3 class="pen-site-footer__column-title"><?php esc_html_e( 'Navegação', 'proenem-wordpress-theme' ); ?></h3>
				<a href="#metodo"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></a>
				<a href="#planos"><?php esc_html_e( 'Planos', 'proenem-wordpress-theme' ); ?></a>
				<a href="#questoes"><?php esc_html_e( 'Banco de questões', 'proenem-wordpress-theme' ); ?></a>
				<a href="#faq"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></a>
			</nav>
		</div>
		<div class="pen-site-footer__bottom">
			<a class="pen-site-footer__copyright" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ProEnem', 'proenem-wordpress-theme' ); ?></a>
			<span class="pen-site-footer__signature"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></span>
		</div>
	</footer>
</main>

<?php
get_footer();
