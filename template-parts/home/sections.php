<?php
/**
 * Home sections rendered below the hero.
 *
 * Shared by `page-templates/home.php` variants so an A/B test only changes the
 * first fold. The markup here mirrors the control template; keep both in sync
 * until a hero variant is promoted.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_asset_uri = static function ( $filename ) {
	return proenem_home_asset_uri( $filename );
};

$platform_asset_uri = static function ( $filename ) {
	return proenem_platform_asset_uri( $filename );
};

$home_image_attributes = static function ( $filename, $args = array() ) {
	return proenem_home_image_attributes( $filename, $args );
};

$home_image_source_set = static function ( $filename, $sizes ) {
	return proenem_home_image_source_set( $filename, $sizes );
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
		'name'          => __( 'Turma Intensiva 2026', 'proenem-wordpress-theme' ),
		'price_prefix'  => __( '12x de R$', 'proenem-wordpress-theme' ),
		'price'         => __( '29,90', 'proenem-wordpress-theme' ),
		'price_details' => __( 'ou R$ 306,90 à vista', 'proenem-wordpress-theme' ),
		'summary'       => __( 'Preparação completa até o dia da prova.', 'proenem-wordpress-theme' ),
		'featured'      => true,
		'button_label'  => __( 'Quero a Turma Intensiva', 'proenem-wordpress-theme' ),
		'button_url'    => proenem_get_home_cta_destination( 'method_pro' ),
		'features'      => array(
			__( 'Cronograma semanal', 'proenem-wordpress-theme' ),
			__( 'Correção de redação', 'proenem-wordpress-theme' ),
			__( 'Aulas e pdfs com os melhores professores', 'proenem-wordpress-theme' ),
			__( 'Simulados corrigidos no padrão ENEM', 'proenem-wordpress-theme' ),
			__( 'Revisões inteligentes por matéria', 'proenem-wordpress-theme' ),
			__( 'Mais de 50 mil questões para praticar', 'proenem-wordpress-theme' ),
			__( '6 meses de acesso', 'proenem-wordpress-theme' ),
		),
	),
);

$faq_items = array(
	array(
		'question' => __( 'O que é a Turma Intensiva 2026?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Uma estrutura de preparação que combina meta, diagnóstico, execução e performance para organizar seus estudos e transformar esforço em nota.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'O que está incluído na Turma Intensiva?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Diagnóstico inicial, nota prevista, banco de mais de 60 mil questões, cronograma personalizado até a prova, duas correções de redação mensais, aulas gravadas, PDFs completos e simulados com nota TRI.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'Posso entrar em qualquer época do ano?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Pode. O diagnóstico inicial ajusta o plano ao seu momento e ao tempo até a prova.', 'proenem-wordpress-theme' ),
	),
	array(
		'question' => __( 'E se eu não gostar?', 'proenem-wordpress-theme' ),
		'answer'   => __( 'Você tem 7 dias após a compra para experimentar o plano. Se não gostar, pode cancelar dentro desse prazo e usar a garantia.', 'proenem-wordpress-theme' ),
	),
);

$subjects = array(
	array(
		'name'     => __( 'Química', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
		'icon'     => 'chemistry',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/uimica-rganica/natureza/sa',
	),
	array(
		'name'     => __( 'Biologia', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
		'icon'     => 'biology',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/iologia-como-ciencia/natureza/sa',
	),
	array(
		'name'     => __( 'Matemática', 'proenem-wordpress-theme' ),
		'category' => __( 'Matemática', 'proenem-wordpress-theme' ),
		'icon'     => 'math',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/matematica/a',
	),
	array(
		'name'     => __( 'História', 'proenem-wordpress-theme' ),
		'category' => __( 'Ciências Humanas', 'proenem-wordpress-theme' ),
		'icon'     => 'history',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/istiria-eral/humanas/sa',
	),
	array(
		'name'     => __( 'Inglês', 'proenem-wordpress-theme' ),
		'category' => __( 'Linguagens', 'proenem-wordpress-theme' ),
		'icon'     => 'english',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/nsino-da-ingua-strangeira-nglesa/linguagens/sa',
	),
	array(
		'name'     => __( 'Português', 'proenem-wordpress-theme' ),
		'category' => __( 'Linguagens', 'proenem-wordpress-theme' ),
		'icon'     => 'portuguese',
		'tone'     => 'pink',
		'url'      => 'https://estude.proenem.com.br/treino/questoes/s/linguagens/a',
	),
);

?>

	<div class="pen-marquee" aria-hidden="true">
		<div class="pen-marquee__track">
			<?php for ( $marquee_iteration = 0; $marquee_iteration < 2; $marquee_iteration++ ) : ?>
				<span><?php esc_html_e( 'Sua aprovação não é sorte. É método.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Estude com estratégia, não com mais horas.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
			<?php endfor; ?>
		</div>
	</div>

	<section id="metodo" class="pen-pillars-section" aria-labelledby="pro-pillars-title">
		<div class="pen-pillars-section__copy">
			<p class="pen-section-pill"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></p>
			<h2 id="pro-pillars-title"><?php esc_html_e( 'Os 4 pilares que organizam a sua aprovação', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'O Método PRO não é um cronograma bonito. É um sistema que te acompanha do primeiro diagnóstico até a vaga — dizendo o que fazer agora e corrigindo a rota quando você trava.', 'proenem-wordpress-theme' ); ?></p>
			<p><?php esc_html_e( 'O ENEM não é prova de quem estuda mais. É de quem estuda com estratégia.', 'proenem-wordpress-theme' ); ?></p>
		<a class="pen-button pen-button--primary pen-button--md" href="<?php echo esc_url( proenem_get_home_cta_destination( 'plans' ) ); ?>"><?php esc_html_e( 'Ver a Turma Intensiva', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
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
			<article class="pen-step-card pen-step-card--blue is-active" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-meta.webp' ) ); ?>" alt=""<?php echo $home_image_attributes( 'pillar-meta.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $home_image_source_set( 'pillar-meta.webp', '(max-width: 700px) 40vw, 24vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<span>01</span>
				<div>
					<h3><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Seu objetivo vira um plano claro, com prioridades e metas semanais. Você sabe exatamente o que estudar primeiro.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--featured" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-diagnostico.webp' ) ); ?>" alt=""<?php echo $home_image_attributes( 'pillar-diagnostico.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $home_image_source_set( 'pillar-diagnostico.webp', '(max-width: 700px) 28vw, 8vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<span>02</span>
				<div>
					<h3><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Simulados adaptativos mapeiam suas forças e lacunas. Você vê, com dados, onde está e o que rende mais nota agora.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--red" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'pillar-execucao.webp' ) ); ?>" alt=""<?php echo $home_image_attributes( 'pillar-execucao.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $home_image_source_set( 'pillar-execucao.webp', '(max-width: 700px) 28vw, 8vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<span>03</span>
				<div>
					<h3><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'O plano diz o que estudar, quando revisar e como corrigir a rota — sem você se perder no meio do caminho.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pen-step-card pen-step-card--pink" data-pro-home-pillar-card>
				<img class="pen-step-card__image" src="<?php echo esc_url( $home_asset_uri( 'student_school_2.webp' ) ); ?>" alt=""<?php echo $home_image_attributes( 'student_school_2.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<span>04</span>
				<div>
					<h3><?php esc_html_e( 'Performance', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Acompanhamos sua evolução e agimos quando você trava ou some. O esforço vira resultado medido, não sensação.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
		</div>
	</section>

	<?php proenem_render_home_proof_section( proenem_get_home_proof_testimonials() ); ?>

	<section class="pen-feature-grid-section" aria-labelledby="pro-pain-title">
		<p class="pen-section-pill"><?php esc_html_e( 'Você se identifica?', 'proenem-wordpress-theme' ); ?></p>
		<h2 id="pro-pain-title">
			<?php esc_html_e( 'Estuda muito', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( 'e a nota', 'proenem-wordpress-theme' ); ?></strong>
			<?php esc_html_e( 'não', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( 'sobe?', 'proenem-wordpress-theme' ); ?></strong>
		</h2>
		<div class="pen-feature-grid pro-home-pain-grid--four">
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
				<p><?php esc_html_e( 'Cronograma bonito, mas sem prioridade. Você estuda tudo igual e o resultado não aparece.', 'proenem-wordpress-theme' ); ?></p>
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
				<p><?php esc_html_e( 'A nota empaca. Você faz simulado, mas não analisa o erro — e repete o mesmo tropeço.', 'proenem-wordpress-theme' ); ?></p>
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
				<p><?php esc_html_e( 'Pressão da família, medo de não passar e a sensação de estar sempre atrasado.', 'proenem-wordpress-theme' ); ?></p>
			</article>
			<article class="pro-home-pain-card pro-home-pain-card--blue">
				<span class="pro-home-pain-card__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false">
						<path d="M18.5 8.5A7 7 0 1 0 19 15"></path>
						<path d="M18.5 4.5v4h-4"></path>
					</svg>
				</span>
				<h3>
					<strong><?php esc_html_e( 'Começa', 'proenem-wordpress-theme' ); ?></strong>
					<span><?php esc_html_e( 'e abandona', 'proenem-wordpress-theme' ); ?></span>
				</h3>
				<p><?php esc_html_e( 'Começa animado e, em duas semanas, larga. Estudar sozinho é difícil quando ninguém te puxa de volta.', 'proenem-wordpress-theme' ); ?></p>
			</article>
		</div>
		<p class="pro-home-pain-section__statement">
			<span><?php esc_html_e( 'O esforço, sozinho, não basta.', 'proenem-wordpress-theme' ); ?></span>
			<span>
				<?php esc_html_e( 'É preciso', 'proenem-wordpress-theme' ); ?>
				<strong><?php esc_html_e( 'método e acompanhamento.', 'proenem-wordpress-theme' ); ?></strong>
			</span>
		</p>
		<a class="pen-button pen-button--primary pen-button--md pro-home-pain-section__cta" href="<?php echo esc_url( proenem_get_home_cta_destination( 'plans' ) ); ?>">
			<?php esc_html_e( 'Comece agora', 'proenem-wordpress-theme' ); ?>
			<span class="pen-button__badge"><?php esc_html_e( '7 dias de garantia', 'proenem-wordpress-theme' ); ?></span>
		</a>
		<span class="pro-home-pain-section__shape pro-home-pain-section__shape--blue" aria-hidden="true"></span>
		<span class="pro-home-pain-section__shape pro-home-pain-section__shape--pink" aria-hidden="true"></span>
	</section>

	<?php
	$platform_items       = array(
		array(
			'label'        => __( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ),
			'icon'         => 'clock',
			'tone'         => 'blue',
			'title'        => __( 'Aulas ao vivo para manter sua rotina em movimento.', 'proenem-wordpress-theme' ),
			'body'         => __( 'Entre em salas guiadas por professores e acompanhe os temas mais importantes da semana.', 'proenem-wordpress-theme' ),
			'url'          => __( 'proenem.com.br/app/aulas-ao-vivo', 'proenem-wordpress-theme' ),
			'image'        => 'live-960.webp',
			'image_sm'     => 'live-480.webp',
			'image_width'  => 960,
			'image_height' => 474,
			'image_alt'    => __( 'Tela da Proenem com a agenda de aulas ao vivo.', 'proenem-wordpress-theme' ),
			'bullets'      => array(
				__( 'Agenda diária de aulas', 'proenem-wordpress-theme' ),
				__( 'Revisões próximas das provas', 'proenem-wordpress-theme' ),
				__( 'Registro do que você já assistiu', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'        => __( '+60 mil questões', 'proenem-wordpress-theme' ),
			'icon'         => 'book',
			'tone'         => 'yellow',
			'title'        => __( 'Mais de 60 mil questões para treinar com intenção.', 'proenem-wordpress-theme' ),
			'body'         => __( 'Filtre por disciplina, assunto e dificuldade para transformar prática em diagnóstico.', 'proenem-wordpress-theme' ),
			'url'          => __( 'proenem.com.br/app/questoes', 'proenem-wordpress-theme' ),
			'image'        => 'question-bank-960.webp',
			'image_sm'     => 'question-bank-480.webp',
			'image_width'  => 960,
			'image_height' => 658,
			'image_alt'    => __( 'Tela do banco de questões da Proenem com filtros e uma questão aberta.', 'proenem-wordpress-theme' ),
			'bullets'      => array(
				__( 'Questões por área do conhecimento', 'proenem-wordpress-theme' ),
				__( 'Resoluções comentadas', 'proenem-wordpress-theme' ),
				__( 'Histórico de acertos e erros', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'        => __( 'Plano personalizado', 'proenem-wordpress-theme' ),
			'icon'         => 'brain',
			'tone'         => 'green',
			'title'        => __( 'Plano personalizado para estudar o que mais importa agora.', 'proenem-wordpress-theme' ),
			'body'         => __( 'A plataforma organiza prioridades a partir da sua meta, tempo disponível e evolução.', 'proenem-wordpress-theme' ),
			'url'          => __( 'proenem.com.br/app/plano', 'proenem-wordpress-theme' ),
			'image'        => 'study-plan-960.webp',
			'image_sm'     => 'study-plan-480.webp',
			'image_width'  => 960,
			'image_height' => 653,
			'image_alt'    => __( 'Tela da jornada personalizada da Proenem com o estudo do dia.', 'proenem-wordpress-theme' ),
			'bullets'      => array(
				__( 'Rotina ajustada por meta', 'proenem-wordpress-theme' ),
				__( 'Prioridade por lacuna', 'proenem-wordpress-theme' ),
				__( 'Próximas ações sempre visíveis', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'        => __( 'Correção de redação', 'proenem-wordpress-theme' ),
			'icon'         => 'edit',
			'tone'         => 'blue',
			'title'        => __( 'Correção de redação com devolutiva objetiva.', 'proenem-wordpress-theme' ),
			'body'         => __( 'Entenda competência por competência onde melhorar para escrever com mais segurança.', 'proenem-wordpress-theme' ),
			'url'          => __( 'proenem.com.br/app/redacao', 'proenem-wordpress-theme' ),
			'image'        => 'essay-feedback-960.webp',
			'image_sm'     => 'essay-feedback-480.webp',
			'image_width'  => 960,
			'image_height' => 473,
			'image_alt'    => __( 'Tela da correção de redação da Proenem com nota e avaliação por competência.', 'proenem-wordpress-theme' ),
			'bullets'      => array(
				__( 'Comentários por competência', 'proenem-wordpress-theme' ),
				__( 'Plano de reescrita', 'proenem-wordpress-theme' ),
				__( 'Evolução por envio', 'proenem-wordpress-theme' ),
			),
		),
		array(
			'label'        => __( 'Simulados com TRI', 'proenem-wordpress-theme' ),
			'icon'         => 'chart',
			'tone'         => 'active',
			'active'       => true,
			'title'        => __( 'Simulados com a mesma lógica de correção do ENEM.', 'proenem-wordpress-theme' ),
			'body'         => __( 'Veja sua nota real, a evolução por área e onde focar agora.', 'proenem-wordpress-theme' ),
			'url'          => __( 'proenem.com.br/app/simulados-com-tri', 'proenem-wordpress-theme' ),
			'image'        => 'simulations-960.webp',
			'image_sm'     => 'simulations-480.webp',
			'image_width'  => 960,
			'image_height' => 793,
			'image_alt'    => __( 'Tela de simulados da Proenem.', 'proenem-wordpress-theme' ),
			'bullets'      => array(
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
					<img src="<?php echo esc_url( $home_asset_uri( 'sticker_explore_por_dentro.svg' ) ); ?>" alt="" aria-hidden="true"<?php echo $home_image_attributes( 'sticker_explore_por_dentro.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="pro-home-platform-note__text"><?php esc_html_e( 'Clique em qualquer recurso e veja como funciona — direto na plataforma.', 'proenem-wordpress-theme' ); ?></span>
				</p>
			</header>
			<p class="pro-home-platform-guard">
				<span><?php esc_html_e( 'Não é um acervo de vídeos. É um sistema que te diz', 'proenem-wordpress-theme' ); ?></span>
				<strong><?php esc_html_e( 'o próximo passo.', 'proenem-wordpress-theme' ); ?></strong>
			</p>
			<div class="pen-pillars-slider__control pro-home-platform-tabs__controls" aria-label="<?php esc_attr_e( 'Controles dos recursos', 'proenem-wordpress-theme' ); ?>">
				<button type="button" data-pro-home-platform-prev aria-label="<?php esc_attr_e( 'Mostrar recursos anteriores', 'proenem-wordpress-theme' ); ?>">←</button>
				<button type="button" data-pro-home-platform-next aria-label="<?php esc_attr_e( 'Mostrar próximos recursos', 'proenem-wordpress-theme' ); ?>">→</button>
			</div>
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
								tabindex="<?php echo esc_attr( $is_active ? '0' : '-1' ); ?>"
								data-pro-home-platform-tab
								data-title="<?php echo esc_attr( $item['title'] ); ?>"
								data-body="<?php echo esc_attr( $item['body'] ); ?>"
								data-url="<?php echo esc_attr( $item['url'] ); ?>"
								data-bullets="<?php echo esc_attr( wp_json_encode( $item['bullets'] ) ); ?>"
								data-image="<?php echo esc_url( $platform_asset_uri( $item['image'] ) ); ?>"
								data-image-srcset="<?php echo esc_attr( esc_url( $platform_asset_uri( $item['image_sm'] ) ) . ' 480w, ' . esc_url( $platform_asset_uri( $item['image'] ) ) . ' 960w' ); ?>"
								data-image-alt="<?php echo esc_attr( $item['image_alt'] ); ?>"
								data-image-width="<?php echo esc_attr( $item['image_width'] ); ?>"
								data-image-height="<?php echo esc_attr( $item['image_height'] ); ?>"
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
						<div class="pro-home-platform-mock__media">
							<img
								data-pro-home-platform-image
								src="<?php echo esc_url( $platform_asset_uri( $platform_active_item['image'] ) ); ?>"
								srcset="<?php echo esc_attr( esc_url( $platform_asset_uri( $platform_active_item['image_sm'] ) ) . ' 480w, ' . esc_url( $platform_asset_uri( $platform_active_item['image'] ) ) . ' 960w' ); ?>"
								sizes="(max-width: 760px) calc(100vw - 4.2rem), min(46vw, 48rem)"
								width="<?php echo esc_attr( $platform_active_item['image_width'] ); ?>"
								height="<?php echo esc_attr( $platform_active_item['image_height'] ); ?>"
								alt="<?php echo esc_attr( $platform_active_item['image_alt'] ); ?>"
								loading="lazy"
								decoding="async"
							>
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
		<img class="pro-home-question-bank__background" src="<?php echo esc_url( $home_asset_uri( 'sticker_explore_questions.svg' ) ); ?>" alt="" aria-hidden="true"<?php echo $home_image_attributes( 'sticker_explore_questions.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<h2 id="pro-questions-title">
			<?php esc_html_e( 'Mais de', 'proenem-wordpress-theme' ); ?>
			<strong><?php esc_html_e( '60 mil questões —', 'proenem-wordpress-theme' ); ?></strong><br>
			<?php esc_html_e( 'e um plano que diz qual resolver agora.', 'proenem-wordpress-theme' ); ?>
		</h2>
		<p><?php esc_html_e( 'Questões do ENEM e dos principais vestibulares, com resolução em vídeo. O método escolhe as certas para a sua meta; você só executa.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pen-subject-grid">
			<?php foreach ( $subjects as $subject ) : ?>
				<a class="pro-home-subject-card pro-home-subject-card--<?php echo esc_attr( $subject['tone'] ); ?>" href="<?php echo esc_url( $subject['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="pro-home-subject-card__icon" aria-hidden="true">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup is hardcoded in this template.
						echo $subject_icon_svg( $subject['icon'] );
						?>
					</span>
					<span class="pro-home-subject-card__body">
						<strong><?php echo esc_html( $subject['name'] ); ?></strong>
						<small><?php echo esc_html( $subject['category'] ); ?></small>
					</span>
					<span class="pro-home-subject-card__arrow" aria-hidden="true">→</span>
				</a>
			<?php endforeach; ?>
		</div>
		<a class="pen-button pen-button--secondary pen-button--sm pro-home-question-bank__cta" href="<?php echo esc_url( proenem_get_home_cta_destination( 'plans' ) ); ?>">
			<?php esc_html_e( 'Conheça a Turma Intensiva', 'proenem-wordpress-theme' ); ?>
			<span class="pen-button__badge"><?php esc_html_e( 'Ver plano e preço', 'proenem-wordpress-theme' ); ?></span>
		</a>
		<img class="pro-home-question-bank__shape" src="<?php echo esc_url( $home_asset_uri( 'blue_3_semi-spheres.svg' ) ); ?>" alt="" aria-hidden="true"<?php echo $home_image_attributes( 'blue_3_semi-spheres.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	</section>

	<section id="planos" class="pen-pricing-section" aria-labelledby="pro-pricing-title">
		<img class="pro-home-pricing__strokes" src="<?php echo esc_url( $home_asset_uri( 'price_vector_strokes.svg' ) ); ?>" alt="" aria-hidden="true"<?php echo $home_image_attributes( 'price_vector_strokes.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="pro-home-pricing__header">
			<div class="pro-home-pricing__intro">
				<h2 id="pro-pricing-title">
					<?php esc_html_e( 'Sua preparação completa.', 'proenem-wordpress-theme' ); ?><br>
					<span class="pro-home-pricing__title-line"><?php esc_html_e( 'Do diagnóstico', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'até a prova.', 'proenem-wordpress-theme' ); ?></strong></span>
				</h2>
				<p><?php esc_html_e( 'Turma Intensiva 2026: cronograma personalizado, aulas, redação, simulados e mais de 60 mil questões. Comece agora com 7 dias de garantia.', 'proenem-wordpress-theme' ); ?></p>
			</div>
		</div>
		<div class="pen-plan-grid">
			<?php foreach ( $plans as $plan ) : ?>
				<article class="pen-plan-card pro-home-plan-card--split<?php echo ! empty( $plan['featured'] ) ? ' is-featured' : ''; ?><?php echo ! empty( $plan['free'] ) ? ' is-free' : ''; ?>">
				<?php if ( ! empty( $plan['featured'] ) ) : ?>
					<span class="pro-home-plan-card__label"><?php esc_html_e( 'Oferta 2026', 'proenem-wordpress-theme' ); ?></span>
					<?php endif; ?>
					<div class="pro-home-plan-card__benefits">
						<header>
							<h3><?php echo esc_html( $plan['name'] ); ?></h3>
							<p><?php echo esc_html( $plan['summary'] ); ?></p>
						</header>
						<ul>
							<?php foreach ( $plan['features'] as $feature ) : ?>
								<li><?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="pro-home-plan-card__checkout">
						<div class="pro-home-plan-card__price">
							<strong class="pro-home-plan-card__price-amount"><span><?php echo esc_html( $plan['price_prefix'] ); ?></span><?php echo esc_html( $plan['price'] ); ?></strong>
							<?php if ( ! empty( $plan['price_details'] ) ) : ?>
								<p><?php echo esc_html( $plan['price_details'] ); ?></p>
							<?php endif; ?>
						</div>
						<a class="pen-action-link pen-button pen-button--primary pen-button--lg" href="<?php echo esc_url( $plan['button_url'] ); ?>"><?php echo esc_html( $plan['button_label'] ); ?> <span aria-hidden="true">-></span></a>
						<ul class="pro-home-plan-card__trust">
							<li><span class="pro-home-plan-card__trust-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg></span><?php esc_html_e( 'Pagamento 100% seguro', 'proenem-wordpress-theme' ); ?></li>
							<li><span class="pro-home-plan-card__trust-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M12 3 5 6v5c0 4.6 2.8 8.1 7 10 4.2-1.9 7-5.4 7-10V6z"></path><path d="m9 12 2 2 4-4"></path></svg></span><?php esc_html_e( 'Garantia de 7 dias', 'proenem-wordpress-theme' ); ?></li>
							<li><span class="pro-home-plan-card__trust-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="12" r="9"></circle><path d="m8.5 12 2.2 2.2 4.8-4.8"></path></svg></span><?php esc_html_e( 'Acesso liberado na hora', 'proenem-wordpress-theme' ); ?></li>
						</ul>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<?php proenem_render_home_testimonials_section( proenem_get_home_testimonials() ); ?>

	<section class="pen-audience-section pro-home-school-section" aria-labelledby="pro-school-title">
		<div class="pro-home-school-section__marquee" aria-hidden="true">
			<div class="pro-home-school-section__marquee-track">
				<span><?php esc_html_e( 'Sua aprovação não é sorte. É método.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Estude com estratégia, não com mais horas.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Sua aprovação não é sorte. É método.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Estude com estratégia, não com mais horas.', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
			</div>
		</div>
		<figure class="pro-home-school-section__photo pro-home-school-section__photo--primary">
			<img src="<?php echo esc_url( $home_asset_uri( 'student_school_1.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo em ambiente escolar.', 'proenem-wordpress-theme' ); ?>"<?php echo $home_image_attributes( 'student_school_1.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		</figure>
		<div class="pen-audience-section__intro pro-home-school-section__intro">
			<div>
				<h2 id="pro-school-title">
					<?php esc_html_e( 'Leve o', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></strong><br>
					<?php esc_html_e( 'para a', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'sua escola.', 'proenem-wordpress-theme' ); ?></strong>
				</h2>
				<p><?php esc_html_e( 'Planos especiais para instituições que querem oferecer a melhor preparação para o ENEM: plataforma, apostilas e acompanhamento por aluno.', 'proenem-wordpress-theme' ); ?></p>
				<a class="pen-button pen-button--primary pen-button--lg pro-home-school-section__cta" href="mailto:pro-receita@questedu.dev?subject=Parceria%20com%20escola"><?php esc_html_e( 'Falar com nossa equipe', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
			</div>
			<img class="pro-home-school-section__photo-secondary" src="<?php echo esc_url( $home_asset_uri( 'student_school_2.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com livros ao fundo.', 'proenem-wordpress-theme' ); ?>"<?php echo $home_image_attributes( 'student_school_2.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
				<?php esc_html_e( 'preparação para o', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'ENEM', 'proenem-wordpress-theme' ); ?></strong> <?php esc_html_e( 'na sua escola?', 'proenem-wordpress-theme' ); ?>
			</h2>
			<p><?php esc_html_e( 'Converse com nossa equipe e receba uma proposta de acordo com o tamanho e o perfil da sua instituição.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-marketing-cta__actions">
			<a class="pen-button pen-button--primary pen-button--lg" href="mailto:pro-receita@questedu.dev?subject=Parceria%20com%20escola"><?php esc_html_e( 'Falar com nossa equipe', 'proenem-wordpress-theme' ); ?> <span class="pen-button__arrow" aria-hidden="true">-></span></a>
		</div>
	</section>

	<section id="faq" class="pen-faq-section" aria-labelledby="pro-faq-title">
		<div class="pen-faq-section__header">
			<span class="pen-pill-eyebrow"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></span>
			<h2 id="pro-faq-title">
				<?php esc_html_e( 'Perguntas', 'proenem-wordpress-theme' ); ?><br>
				<?php esc_html_e( 'frequentes', 'proenem-wordpress-theme' ); ?>
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
