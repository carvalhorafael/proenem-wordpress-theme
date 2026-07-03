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

$platform_icon_svg = static function ( $icon ) {
	$icons = array(
		'clock' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v5l3 2"></path></svg>',
		'book'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 5.5h7a4 4 0 0 1 4 4v9a4 4 0 0 0-4-4H5z"></path><path d="M19 5.5h-3a4 4 0 0 0-4 4"></path><path d="M19 5.5v11.7"></path><path d="m16 15 1.5-1.5L19 15"></path></svg>',
		'brain' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M9 5.3a3.2 3.2 0 0 0-4 3.1 3 3 0 0 0 .8 5.8 3 3 0 0 0 3.8 4.3"></path><path d="M15 5.3a3.2 3.2 0 0 1 4 3.1 3 3 0 0 1-.8 5.8 3 3 0 0 1-3.8 4.3"></path><path d="M9 5.3v13.2"></path><path d="M15 5.3v13.2"></path><path d="M9 9.2H7.2"></path><path d="M15 9.2h1.8"></path><path d="M9 14.2H7.2"></path><path d="M15 14.2h1.8"></path></svg>',
		'robot' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><rect x="6" y="8" width="12" height="10" rx="2"></rect><path d="M12 5v3"></path><path d="M9 12h.01"></path><path d="M15 12h.01"></path><path d="M9 16h6"></path><path d="M4 12h2"></path><path d="M18 12h2"></path></svg>',
		'edit'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19h14"></path><path d="m7 16 1-4 7.5-7.5a2.1 2.1 0 0 1 3 3L11 15z"></path></svg>',
		'chart' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19V5"></path><path d="M5 19h14"></path><path d="m8 15 3-3 2 2 4-6"></path></svg>',
	);

	return $icons[ $icon ] ?? $icons['clock'];
};

$subject_icon_svg = static function ( $icon ) {
	$icons = array(
		'chemistry'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M9 3h6"></path><path d="M10 3v5l-5.2 9.2A2.6 2.6 0 0 0 7 21h10a2.6 2.6 0 0 0 2.2-3.8L14 8V3"></path><path d="M7.5 16h9"></path></svg>',
		'biology'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19c9.5 0 14-4.5 14-14C9.5 5 5 9.5 5 19z"></path><path d="M5 19 15 9"></path></svg>',
		'math'       => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M8 7h8"></path><path d="M8 11h2"></path><path d="M14 11h2"></path><path d="M8 15h2"></path><path d="M14 15h2"></path></svg>',
		'history'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M4 9h16"></path><path d="m5 9 7-5 7 5"></path><path d="M6 9v9"></path><path d="M10 9v9"></path><path d="M14 9v9"></path><path d="M18 9v9"></path><path d="M4 18h16"></path></svg>',
		'english'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 5h14v10H8l-3 3z"></path><path d="M8 9h8"></path><path d="M8 12h5"></path></svg>',
		'portuguese' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19h14"></path><path d="m7 16 1-4 7.5-7.5a2.1 2.1 0 0 1 3 3L11 15z"></path></svg>',
	);

	return $icons[ $icon ] ?? $icons['portuguese'];
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

$testimonials = array(
	array(
		'quote' => __( 'O Método PRO me deu clareza para estudar o que realmente importava. Parei de acumular tarefas e comecei a enxergar evolução semana a semana.', 'proenem-wordpress-theme' ),
		'name'  => __( 'Mariana Costa', 'proenem-wordpress-theme' ),
		'role'  => __( 'Aprovada em Medicina', 'proenem-wordpress-theme' ),
		'image' => 'proof-students-1.webp',
	),
	array(
		'quote' => __( 'A rotina ficou simples de seguir. Os simulados, o diagnóstico e a correção de redação mostravam exatamente onde eu precisava insistir.', 'proenem-wordpress-theme' ),
		'name'  => __( 'Lucas Almeida', 'proenem-wordpress-theme' ),
		'role'  => __( 'Aprovado em Engenharia', 'proenem-wordpress-theme' ),
		'image' => 'proof-students-3.png',
	),
	array(
		'quote' => __( 'Eu estudava muito, mas sem direção. Com o método, consegui organizar minhas prioridades e chegar na prova muito mais confiante.', 'proenem-wordpress-theme' ),
		'name'  => __( 'Beatriz Rocha', 'proenem-wordpress-theme' ),
		'role'  => __( 'Aprovada em Direito', 'proenem-wordpress-theme' ),
		'image' => 'proof-students-4.png',
	),
	array(
		'quote' => __( 'Ter um plano claro mudou tudo. Eu sabia o que fazer a cada semana e conseguia medir se estava avançando de verdade.', 'proenem-wordpress-theme' ),
		'name'  => __( 'Pedro Martins', 'proenem-wordpress-theme' ),
		'role'  => __( 'Aprovado em Psicologia', 'proenem-wordpress-theme' ),
		'image' => 'proof-students-5.png',
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
	array(
		'question' => __( 'E se eu não gostar?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Você pode cancelar quando quiser. A ideia é experimentar o método sem burocracia e seguir apenas se fizer sentido para sua rotina.', 'proenem-wordpress-theme' ),
	),
);

$subjects = array(
	array(
		'name'     => __( 'Química', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
		'icon'     => 'chemistry',
		'tone'     => 'yellow',
	),
	array(
		'name'     => __( 'Biologia', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
		'icon'     => 'biology',
		'tone'     => 'pink',
	),
	array(
		'name'     => __( 'Matemática', 'proenem-wordpress-theme' ),
		'category' => __( 'Matemática', 'proenem-wordpress-theme' ),
		'icon'     => 'math',
		'tone'     => 'pink',
	),
	array(
		'name'     => __( 'História', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências Humanas', 'proenem-wordpress-theme' ),
		'icon'     => 'history',
		'tone'     => 'pink',
	),
	array(
		'name'     => __( 'Inglês', 'proenem-wordpress-theme' ),
		'category' => __( 'Linguagens', 'proenem-wordpress-theme' ),
		'icon'     => 'english',
		'tone'     => 'pink',
	),
	array(
		'name'     => __( 'Português', 'proenem-wordpress-theme' ),
		'category' => __( 'Linguagens', 'proenem-wordpress-theme' ),
		'icon'     => 'portuguese',
		'tone'     => 'pink',
	),
);

?>

<main id="primary" class="site-main pro-home">
	<?php
	proenem_render_site_navbar(
		array(
			'aria_label' => __( 'Navegação da home', 'proenem-wordpress-theme' ),
			'context'    => 'home',
		)
	);
	?>

	<section class="pen-hero-section" aria-labelledby="pro-home-title">
		<div class="pen-hero-section__stage">
			<img class="pen-hero-section__image" src="<?php echo esc_url( $home_asset_uri( 'hero-student.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com cadernos nas mãos.', 'proenem-wordpress-theme' ); ?>">
			<span class="pen-hero-sticker pen-hero-sticker--pink"><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--yellow"><?php esc_html_e( 'Performance', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--green"><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></span>
			<span class="pen-hero-sticker pen-hero-sticker--orange"><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-home-title" class="pen-hero-section__title">
				<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php esc_html_e( 'Sua', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php esc_html_e( 'aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php esc_html_e( 'não é', 'proenem-wordpress-theme' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--blue"><?php esc_html_e( 'sorte', 'proenem-wordpress-theme' ); ?></strong></span>
				<span class="pen-hero-section__title-line pen-hero-section__title-line--right"><?php esc_html_e( 'é', 'proenem-wordpress-theme' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--yellow"><?php esc_html_e( 'método', 'proenem-wordpress-theme' ); ?></strong></span>
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
		<h2 id="pro-pain-title">
			<?php esc_html_e( 'Já sentiu que', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( 'estuda muito,', 'proenem-wordpress-theme' ); ?></strong>
			<br>
			<?php esc_html_e( 'mas a nota', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( 'não sobe?', 'proenem-wordpress-theme' ); ?></strong>
		</h2>
		<div class="pen-feature-grid">
			<article class="pro-home-pain-card pro-home-pain-card--blue">
				<span class="pro-home-pain-card__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false">
						<circle cx="12" cy="12" r="7"></circle>
						<path d="M15.5 8.5 13.8 14l-5.3 1.5L10.2 10z"></path>
					</svg>
				</span>
				<h3>
					<span><?php esc_html_e( 'Estuda', 'proenem-wordpress-theme' ); ?></span>
					<strong><?php esc_html_e( 'sem direção', 'proenem-wordpress-theme' ); ?></strong>
				</h3>
				<p><?php esc_html_e( 'Cronogramas bonitos, mas sem priorização. Estuda tudo igual e o resultado não aparece.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article class="pro-home-pain-card pro-home-pain-card--yellow">
				<span class="pro-home-pain-card__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false">
						<circle cx="12" cy="12" r="7"></circle>
						<circle cx="12" cy="12" r="4"></circle>
						<circle cx="12" cy="12" r="1.2"></circle>
					</svg>
				</span>
				<h3>
					<strong><?php esc_html_e( 'Trava', 'proenem-wordpress-theme' ); ?></strong>
					<span><?php esc_html_e( 'em simulados', 'proenem-wordpress-theme' ); ?></span>
				</h3>
				<p><?php esc_html_e( 'A nota não sai do lugar. Faz simulados, mas não analisa os erros. Repete os mesmos equívocos.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article class="pro-home-pain-card pro-home-pain-card--red">
				<span class="pro-home-pain-card__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false">
						<path d="M12 4.2 20 18H4z"></path>
						<path d="M12 9v4.2"></path>
						<path d="M12 16h.01"></path>
					</svg>
				</span>
				<h3>
					<strong><?php esc_html_e( 'Ansiedade', 'proenem-wordpress-theme' ); ?></strong>
					<span><?php esc_html_e( '&', 'proenem-wordpress-theme' ); ?></span>
					<strong><?php esc_html_e( 'insegurança', 'proenem-wordpress-theme' ); ?></strong>
				</h3>
				<p><?php esc_html_e( 'Pressão familiar, medo de não passar e sensação de estar sempre atrasado em relação aos outros.', 'proenem-wordpress-theme' ); ?></p>
			</article>
		</div>
		<p class="pro-home-pain-section__statement">
			<span><?php esc_html_e( 'O problema não é o esforço.', 'proenem-wordpress-theme' ); ?></span>
			<span>
				<?php esc_html_e( 'É estudar', 'proenem-wordpress-theme' ); ?>
				<strong><?php esc_html_e( 'sem método.', 'proenem-wordpress-theme' ); ?></strong>
			</span>
		</p>
		<a class="pen-button pen-button--primary pen-button--md pro-home-pain-section__cta" href="#planos">
			<?php esc_html_e( 'Comece agora!', 'proenem-wordpress-theme' ); ?>
			<span class="pen-button__badge"><?php esc_html_e( 'É gratuito', 'proenem-wordpress-theme' ); ?></span>
		</a>
		<span class="pro-home-pain-section__shape pro-home-pain-section__shape--blue" aria-hidden="true"></span>
		<span class="pro-home-pain-section__shape pro-home-pain-section__shape--pink" aria-hidden="true"></span>
	</section>

	<?php
	$platform_items       = array(
		array(
			'label'   => __( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ),
			'icon'    => 'clock',
			'tone'    => 'blue',
			'title'   => __( 'Aulas ao vivo para manter sua rotina em movimento.', 'proenem-wordpress-theme' ),
			'body'    => __( 'Entre em salas guiadas por professores e acompanhe os temas mais importantes da semana.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/aulas-ao-vivo', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Agenda diária de aulas', 'proenem-wordpress-theme' ),
				__( 'Revisões próximas das provas', 'proenem-wordpress-theme' ),
				__( 'Registro do que você já assistiu', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'   => __( '+50 mil questões', 'proenem-wordpress-theme' ),
			'icon'    => 'book',
			'tone'    => 'yellow',
			'title'   => __( 'Mais de 50 mil questões para treinar com intenção.', 'proenem-wordpress-theme' ),
			'body'    => __( 'Filtre por disciplina, assunto e dificuldade para transformar prática em diagnóstico.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/questoes', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Questões por área do conhecimento', 'proenem-wordpress-theme' ),
				__( 'Resoluções comentadas', 'proenem-wordpress-theme' ),
				__( 'Histórico de acertos e erros', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'   => __( 'Plano personalizado', 'proenem-wordpress-theme' ),
			'icon'    => 'brain',
			'tone'    => 'green',
			'title'   => __( 'Plano personalizado para estudar o que mais importa agora.', 'proenem-wordpress-theme' ),
			'body'    => __( 'A plataforma organiza prioridades a partir da sua meta, tempo disponível e evolução.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/plano', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Rotina ajustada por meta', 'proenem-wordpress-theme' ),
				__( 'Prioridade por lacuna', 'proenem-wordpress-theme' ),
				__( 'Próximas ações sempre visíveis', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'   => __( 'Tutor com IA', 'proenem-wordpress-theme' ),
			'icon'    => 'robot',
			'tone'    => 'red',
			'title'   => __( 'Tutor com IA para tirar dúvidas no seu ritmo.', 'proenem-wordpress-theme' ),
			'body'    => __( 'Receba explicações guiadas e volte para o estudo sem perder o contexto da tarefa.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/tutor-ia', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Explicação passo a passo', 'proenem-wordpress-theme' ),
				__( 'Apoio em questões difíceis', 'proenem-wordpress-theme' ),
				__( 'Disponível durante a rotina', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'   => __( 'Correção de redação', 'proenem-wordpress-theme' ),
			'icon'    => 'edit',
			'tone'    => 'blue',
			'title'   => __( 'Correção de redação com devolutiva objetiva.', 'proenem-wordpress-theme' ),
			'body'    => __( 'Entenda competência por competência onde melhorar para escrever com mais segurança.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/redacao', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Comentários por competência', 'proenem-wordpress-theme' ),
				__( 'Plano de reescrita', 'proenem-wordpress-theme' ),
				__( 'Evolução por envio', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'   => __( 'Simulados com TRI', 'proenem-wordpress-theme' ),
			'icon'    => 'chart',
			'tone'    => 'active',
			'active'  => true,
			'title'   => __( 'Simulados com a mesma lógica de correção do ENEM.', 'proenem-wordpress-theme' ),
			'body'    => __( 'Veja sua nota real, evolução por área e onde focar agora.', 'proenem-wordpress-theme' ),
			'url'     => __( 'proenem.com.br/app/simulados-com-tri', 'proenem-wordpress-theme' ),
			'bullets' => array(
				__( 'Nota real estimada pelo TRI', 'proenem-wordpress-theme' ),
				__( 'Comparativo com aprovados', 'proenem-wordpress-theme' ),
				__( 'Diagnóstico por área e tópico', 'proenem-wordpress-theme' ),
			),
		),
	);
	$platform_active_item = $platform_items[ count( $platform_items ) - 1 ];
	?>
	<section class="pen-platform-showcase" aria-labelledby="pro-platform-title" data-pro-home-platform-tabs>
		<div class="pen-platform-showcase__panel">
			<header class="pro-home-platform-header">
				<h2 id="pro-platform-title">
					<strong><?php esc_html_e( 'Explore', 'proenem-wordpress-theme' ); ?></strong>
					<span><?php esc_html_e( 'por dentro', 'proenem-wordpress-theme' ); ?></span><br>
					<?php esc_html_e( 'cada detalhe', 'proenem-wordpress-theme' ); ?>
				</h2>
				<p class="pro-home-platform-note">
					<img src="<?php echo esc_url( $home_asset_uri( 'sticker_explore_por_dentro.svg' ) ); ?>" alt="" aria-hidden="true">
					<span class="pro-home-platform-note__text"><?php esc_html_e( 'Clique em qualquer item à esquerda e veja exatamente como funciona — direto na plataforma.', 'proenem-wordpress-theme' ); ?></span>
				</p>
			</header>
			<div class="pro-home-platform-body">
				<ul class="pro-home-platform-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Recursos da plataforma', 'proenem-wordpress-theme' ); ?>">
					<?php foreach ( $platform_items as $index => $item ) : ?>
						<?php $is_active = ! empty( $item['active'] ); ?>
						<li role="presentation">
							<button
								type="button"
								class="pro-home-platform-tab pro-home-platform-tab--<?php echo esc_attr( $item['tone'] ); ?><?php echo $is_active ? ' is-active' : ''; ?>"
								role="tab"
								aria-selected="<?php echo esc_attr( $is_active ? 'true' : 'false' ); ?>"
								data-pro-home-platform-tab
								data-title="<?php echo esc_attr( $item['title'] ); ?>"
								data-body="<?php echo esc_attr( $item['body'] ); ?>"
								data-url="<?php echo esc_attr( $item['url'] ); ?>"
								data-bullets="<?php echo esc_attr( wp_json_encode( $item['bullets'] ) ); ?>"
							>
								<span class="pro-home-platform-tab__icon" aria-hidden="true">
									<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup is hardcoded in this template.
									echo $platform_icon_svg( $item['icon'] );
									?>
								</span>
								<span class="pro-home-platform-tab__label"><?php echo esc_html( $item['label'] ); ?></span>
								<span class="pro-home-platform-tab__arrow" aria-hidden="true">→</span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="pen-platform-showcase__screen">
					<div class="pro-home-platform-mock" data-pro-home-platform-screen>
						<div class="pro-home-platform-browser" aria-hidden="true">
							<span></span>
							<span></span>
							<span></span>
							<small data-pro-home-platform-url><?php echo esc_html( $platform_active_item['url'] ); ?></small>
						</div>
						<div class="pro-home-platform-mock__dashboard" aria-hidden="true">
							<span></span>
							<span></span>
							<span></span>
							<span></span>
						</div>
						<h3 data-pro-home-platform-title><?php echo esc_html( $platform_active_item['title'] ); ?></h3>
						<p data-pro-home-platform-body><?php echo esc_html( $platform_active_item['body'] ); ?></p>
						<ul class="pro-home-platform-mock__bullets" data-pro-home-platform-bullets>
							<?php foreach ( $platform_active_item['bullets'] as $bullet ) : ?>
								<li><?php echo esc_html( $bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<span class="pro-home-platform-star" aria-hidden="true"></span>
		</div>
	</section>

	<section id="questoes" class="pen-question-bank-section" aria-labelledby="pro-questions-title">
		<img class="pro-home-question-bank__background" src="<?php echo esc_url( $home_asset_uri( 'sticker_explore_questions.svg' ) ); ?>" alt="" aria-hidden="true">
		<h2 id="pro-questions-title">
			<?php esc_html_e( 'Explore', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( '+50 mil questões', 'proenem-wordpress-theme' ); ?></strong><br>
			<?php esc_html_e( 'sem precisar criar conta.', 'proenem-wordpress-theme' ); ?>
		</h2>
		<p><?php esc_html_e( 'Questões do ENEM e dos principais vestibulares, com resolução em vídeo. Escolha uma disciplina e comece agora.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pen-subject-grid">
			<?php foreach ( $subjects as $subject ) : ?>
				<a class="pro-home-subject-card pro-home-subject-card--<?php echo esc_attr( $subject['tone'] ); ?>" href="#planos">
					<span class="pro-home-subject-card__icon" aria-hidden="true">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup is hardcoded in this template.
						echo $subject_icon_svg( $subject['icon'] );
						?>
					</span>
					<span class="pro-home-subject-card__body">
						<strong><?php echo esc_html( $subject['name'] ); ?></strong>
						<small><?php echo esc_html( $subject['category'] ); ?></small>
						<span class="pro-home-subject-card__meta"><?php esc_html_e( '512 questões', 'proenem-wordpress-theme' ); ?></span>
						<span class="pro-home-subject-card__meta"><?php esc_html_e( '40 aulas', 'proenem-wordpress-theme' ); ?></span>
					</span>
					<span class="pro-home-subject-card__arrow" aria-hidden="true">→</span>
				</a>
			<?php endforeach; ?>
		</div>
		<a class="pen-button pen-button--secondary pen-button--sm pro-home-question-bank__cta" href="#planos">
			<?php esc_html_e( 'Comece agora!', 'proenem-wordpress-theme' ); ?>
			<span class="pen-button__badge"><?php esc_html_e( 'É gratuito', 'proenem-wordpress-theme' ); ?></span>
		</a>
		<img class="pro-home-question-bank__shape" src="<?php echo esc_url( $home_asset_uri( 'blue_3_semi-spheres.svg' ) ); ?>" alt="" aria-hidden="true">
	</section>

	<section id="planos" class="pen-pricing-section" aria-labelledby="pro-pricing-title">
		<img class="pro-home-pricing__strokes" src="<?php echo esc_url( $home_asset_uri( 'price_vector_strokes.svg' ) ); ?>" alt="" aria-hidden="true">
		<div class="pro-home-pricing__header">
			<div class="pro-home-pricing__seal" aria-hidden="true">
				<img class="pro-home-pricing__seal-bg" src="<?php echo esc_url( $home_asset_uri( 'Ellipse-fundo-price.svg' ) ); ?>" alt="" aria-hidden="true">
				<img class="pro-home-pricing__seal-text" src="<?php echo esc_url( $home_asset_uri( 'Cancele-quando-voce-quiser.svg' ) ); ?>" alt="" aria-hidden="true">
				<img class="pro-home-pricing__seal-check" src="<?php echo esc_url( $home_asset_uri( 'check-verified-01.svg' ) ); ?>" alt="" aria-hidden="true">
			</div>
			<div class="pro-home-pricing__intro">
				<h2 id="pro-pricing-title">
					<?php esc_html_e( 'Investimento que se', 'proenem-wordpress-theme' ); ?><br>
					<?php esc_html_e( 'paga em', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'uma vaga.', 'proenem-wordpress-theme' ); ?></strong>
				</h2>
				<p><?php esc_html_e( 'Comece grátis. Cancele com 1 clique.', 'proenem-wordpress-theme' ); ?><br><?php esc_html_e( '7 dias de garantia em todos os planos.', 'proenem-wordpress-theme' ); ?></p>
			</div>
		</div>
		<div class="pen-plan-grid">
			<?php foreach ( $plans as $plan ) : ?>
				<article class="pen-plan-card<?php echo ! empty( $plan['featured'] ) ? ' is-featured' : ''; ?>">
					<?php if ( ! empty( $plan['featured'] ) ) : ?>
						<span class="pro-home-plan-card__label"><?php esc_html_e( 'Mais escolhido', 'proenem-wordpress-theme' ); ?></span>
					<?php endif; ?>
					<header>
						<h3><?php echo esc_html( $plan['name'] ); ?></h3>
						<p><?php echo esc_html( $plan['summary'] ); ?></p>
						<strong <?php echo ! empty( $plan['featured'] ) ? 'style="' . esc_attr( '--pro-home-pricing-star: url(' . esc_url( $home_asset_uri( 'pricing_star.svg' ) ) . ');' ) . '"' : ''; ?>><span><?php esc_html_e( 'R$', 'proenem-wordpress-theme' ); ?></span><?php echo esc_html( $plan['price'] ); ?><small><?php esc_html_e( 'ao mês', 'proenem-wordpress-theme' ); ?></small></strong>
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

	<section class="pro-home-testimonials" aria-labelledby="pro-testimonials-title" data-pro-home-testimonials-slider>
		<div class="pro-home-testimonials__header">
			<span class="pen-section-pill"><?php esc_html_e( 'Aprovados', 'proenem-wordpress-theme' ); ?></span>
			<h2 id="pro-testimonials-title">
				<span><?php esc_html_e( 'Quem seguiu o método,', 'proenem-wordpress-theme' ); ?></span>
				<strong><?php esc_html_e( 'conquistou a vaga.', 'proenem-wordpress-theme' ); ?></strong>
			</h2>
			<p><?php esc_html_e( 'Mais de 40 mil alunos já foram aprovados com a ProEnem. Conheça algumas histórias.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pro-home-testimonials__viewport">
			<div class="pro-home-testimonials__track" data-pro-home-testimonials-track>
				<?php foreach ( $testimonials as $testimonial_index => $testimonial ) : ?>
					<article class="pro-home-testimonial-card<?php echo 1 === $testimonial_index ? ' is-active' : ''; ?>" data-pro-home-testimonial-card>
						<div class="pro-home-testimonial-card__quote">
							<span aria-hidden="true">“</span>
							<p><?php echo esc_html( $testimonial['quote'] ); ?></p>
						</div>
						<footer>
							<img src="<?php echo esc_url( $home_asset_uri( $testimonial['image'] ) ); ?>" alt="<?php echo esc_attr( $testimonial['name'] ); ?>">
							<span>
								<strong><?php echo esc_html( $testimonial['name'] ); ?></strong>
								<small><?php echo esc_html( $testimonial['role'] ); ?></small>
							</span>
						</footer>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="pro-home-testimonials__controls" aria-label="<?php esc_attr_e( 'Controles dos depoimentos', 'proenem-wordpress-theme' ); ?>">
			<button type="button" data-pro-home-testimonials-prev aria-label="<?php esc_attr_e( 'Depoimento anterior', 'proenem-wordpress-theme' ); ?>">←</button>
			<button type="button" data-pro-home-testimonials-next aria-label="<?php esc_attr_e( 'Próximo depoimento', 'proenem-wordpress-theme' ); ?>">→</button>
		</div>
	</section>

	<section class="pen-audience-section pro-home-school-section" aria-labelledby="pro-school-title">
		<div class="pro-home-school-section__marquee" aria-hidden="true">
			<div class="pro-home-school-section__marquee-track">
				<span><?php esc_html_e( 'A engenharia da sua aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Troque a sorte pela estratégia', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'A engenharia da sua aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Troque a sorte pela estratégia', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
			</div>
		</div>
		<figure class="pro-home-school-section__photo pro-home-school-section__photo--primary">
			<img src="<?php echo esc_url( $home_asset_uri( 'student_school_1.png' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo em ambiente escolar.', 'proenem-wordpress-theme' ); ?>">
		</figure>
		<div class="pen-audience-section__intro pro-home-school-section__intro">
			<div>
				<h2 id="pro-school-title">
					<?php esc_html_e( 'Leve o', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></strong><br>
					<?php esc_html_e( 'para a', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'sua escola.', 'proenem-wordpress-theme' ); ?></strong>
				</h2>
				<p><?php esc_html_e( 'Planos especiais para instituições que querem oferecer a melhor preparação para o ENEM. Plataforma, material didático e acompanhamento em um único pacote.', 'proenem-wordpress-theme' ); ?></p>
			</div>
			<img class="pro-home-school-section__photo-secondary" src="<?php echo esc_url( $home_asset_uri( 'student_school_2.png' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com livros ao fundo.', 'proenem-wordpress-theme' ); ?>">
			<span class="pro-home-school-section__burst" aria-hidden="true"></span>
		</div>
		<div class="pen-feature-grid pen-feature-grid--school">
			<article>
				<span class="pro-home-school-section__card-icon" aria-hidden="true"><?php echo $platform_icon_svg( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<h3><?php esc_html_e( 'Combo plataforma + apostilas', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Acesso completo à plataforma, mais o kit de apostilas exclusivas entregue na escola.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article>
				<span class="pro-home-school-section__card-icon" aria-hidden="true"><?php echo $platform_icon_svg( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<h3><?php esc_html_e( 'Acompanhe cada aluno', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Painel exclusivo para coordenadores e professores com desempenho, simulados e frequência em tempo real.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article>
				<span class="pro-home-school-section__card-icon" aria-hidden="true"><?php echo $platform_icon_svg( 'brain' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<h3><?php esc_html_e( 'Acesso para todos os alunos', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Licenças geradas para todas as turmas. Cada aluno tem seu perfil individual e plano personalizado.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article>
				<span class="pro-home-school-section__card-icon" aria-hidden="true"><?php echo $platform_icon_svg( 'robot' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<h3><?php esc_html_e( 'Onboarding dedicado', 'proenem-wordpress-theme' ); ?></h3>
				<p><?php esc_html_e( 'Um especialista cuida da implantação, treina os professores e acompanha os primeiros ciclos.', 'proenem-wordpress-theme' ); ?></p>
			</article>
		</div>
	</section>

	<section class="pen-marketing-cta pro-home__final-cta" aria-labelledby="pro-final-title">
		<div class="pen-marketing-cta__content">
			<h2 id="pro-final-title">
				<?php esc_html_e( 'Pronto para', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'transformar', 'proenem-wordpress-theme' ); ?></strong> <?php esc_html_e( 'a', 'proenem-wordpress-theme' ); ?><br>
				<?php esc_html_e( 'preparação', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'ENEM', 'proenem-wordpress-theme' ); ?></strong> <?php esc_html_e( 'na sua escola?', 'proenem-wordpress-theme' ); ?>
			</h2>
			<p><?php esc_html_e( 'Converse com nossa equipe e receba uma proposta personalizada de acordo com o tamanho e perfil da sua instituição.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-marketing-cta__actions">
			<a class="pen-button pen-button--primary pen-button--lg" href="#faq"><?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
		</div>
	</section>

	<section id="faq" class="pen-faq-section" aria-labelledby="pro-faq-title">
		<div class="pen-faq-section__header">
			<span class="pen-pill-eyebrow"><?php esc_html_e( 'Perguntas frequentes', 'proenem-wordpress-theme' ); ?></span>
			<h2 id="pro-faq-title">
				<?php esc_html_e( 'Já sentiu que', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'estuda muito,', 'proenem-wordpress-theme' ); ?></strong><br>
				<?php esc_html_e( 'mas a nota', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'não sobe?', 'proenem-wordpress-theme' ); ?></strong>
			</h2>
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
			<h2 class="pen-site-footer__title">
				<?php esc_html_e( 'Sua Aprovação', 'proenem-wordpress-theme' ); ?><br>
				<span><?php esc_html_e( 'não é sorte.', 'proenem-wordpress-theme' ); ?></span><br>
				<strong><?php esc_html_e( 'É método.', 'proenem-wordpress-theme' ); ?></strong>
			</h2>
			<p class="pen-site-footer__body"><?php esc_html_e( 'Construímos a infraestrutura que transforma esforço em resultado. Você estuda, a engenharia faz o resto.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-site-footer__links">
			<div class="pen-site-footer__column pen-site-footer__widget-area">
				<?php if ( is_active_sidebar( 'home-footer-platform' ) ) : ?>
					<?php dynamic_sidebar( 'home-footer-platform' ); ?>
				<?php else : ?>
					<h3 class="pen-site-footer__column-title"><?php esc_html_e( 'Plataforma', 'proenem-wordpress-theme' ); ?></h3>
					<ul>
						<li><a href="#metodo"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#planos"><?php esc_html_e( 'Planos', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#questoes"><?php esc_html_e( 'Banco de questões', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#redacao"><?php esc_html_e( 'Redação', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#aprovados"><?php esc_html_e( 'Aprovados', 'proenem-wordpress-theme' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>
			<div class="pen-site-footer__column pen-site-footer__widget-area">
				<?php if ( is_active_sidebar( 'home-footer-support' ) ) : ?>
					<?php dynamic_sidebar( 'home-footer-support' ); ?>
				<?php else : ?>
					<h3 class="pen-site-footer__column-title"><?php esc_html_e( 'Suporte', 'proenem-wordpress-theme' ); ?></h3>
					<ul>
						<li><a href="#ajuda"><?php esc_html_e( 'Central de ajuda', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#contato"><?php esc_html_e( 'Fale com a gente', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#faq"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#status"><?php esc_html_e( 'Status', 'proenem-wordpress-theme' ); ?></a></li>
						<li><a href="#carreiras"><?php esc_html_e( 'Carreiras', 'proenem-wordpress-theme' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<div class="pen-site-footer__bottom">
			<a class="pen-site-footer__copyright" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '@2026 ProEnem - Grupo Q Educação', 'proenem-wordpress-theme' ); ?></a>
			<span class="pen-site-footer__signature"><?php esc_html_e( 'Feito com ♥ para você', 'proenem-wordpress-theme' ); ?></span>
		</div>
	</footer>
</main>

<?php
get_footer();
