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
?>

<main id="primary" class="site-main pro-home">
	<nav class="pro-home-nav" aria-label="<?php esc_attr_e( 'Navegação da home', 'proenem-wordpress-theme' ); ?>">
		<a class="pro-home-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ProEnem', 'proenem-wordpress-theme' ); ?></a>
		<div class="pro-home-nav__links">
			<a href="#metodo"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></a>
			<a href="#planos"><?php esc_html_e( 'Planos', 'proenem-wordpress-theme' ); ?></a>
			<a href="#questoes"><?php esc_html_e( 'Questões', 'proenem-wordpress-theme' ); ?></a>
			<a href="#aprovados"><?php esc_html_e( 'Aprovados', 'proenem-wordpress-theme' ); ?></a>
			<a href="#faq"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></a>
		</div>
		<a class="pro-home-nav__cta" href="#planos"><?php esc_html_e( 'Comece grátis', 'proenem-wordpress-theme' ); ?></a>
	</nav>

	<section class="pro-hero" aria-labelledby="pro-home-title">
		<div class="pro-hero__stage">
			<img class="pro-hero__student" src="<?php echo esc_url( $home_asset_uri( 'hero-student.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com cadernos nas mãos.', 'proenem-wordpress-theme' ); ?>">
			<span class="pro-sticker pro-sticker--pink"><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></span>
			<span class="pro-sticker pro-sticker--yellow"><?php esc_html_e( 'Performance', 'proenem-wordpress-theme' ); ?></span>
			<span class="pro-sticker pro-sticker--green"><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></span>
			<span class="pro-sticker pro-sticker--orange"><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-home-title" class="pro-hero__title">
				<span><?php esc_html_e( 'Sua', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span><?php esc_html_e( 'não é', 'proenem-wordpress-theme' ); ?> <strong><?php esc_html_e( 'sorte', 'proenem-wordpress-theme' ); ?></strong></span>
				<span><?php esc_html_e( 'é', 'proenem-wordpress-theme' ); ?> <em><?php esc_html_e( 'método', 'proenem-wordpress-theme' ); ?></em></span>
			</h1>
		</div>
		<div class="pro-hero__bar">
			<p><?php esc_html_e( 'O Método PRO não é apenas um cronograma, é uma engenharia de resultados dividida em 4 pilares: Meta. Diagnóstico. Execução. Performance.', 'proenem-wordpress-theme' ); ?></p>
			<a href="#metodo"><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?> <span aria-hidden="true">-></span></a>
		</div>
	</section>

	<div class="pro-marquee" aria-hidden="true">
		<span><?php esc_html_e( 'A engenharia da sua aprovação', 'proenem-wordpress-theme' ); ?></span>
		<span><?php esc_html_e( 'Troque a sorte pela estratégia', 'proenem-wordpress-theme' ); ?></span>
		<span><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
	</div>

	<section id="metodo" class="pro-pillars" aria-labelledby="pro-pillars-title">
		<div class="pro-pillars__copy">
			<p class="pro-pill"><?php esc_html_e( 'Método Pro', 'proenem-wordpress-theme' ); ?></p>
			<h2 id="pro-pillars-title"><?php esc_html_e( 'Os 4 pilares que organizam a sua aprovação', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'O ENEM não é prova de quem estuda mais. É de quem estuda com estratégia.', 'proenem-wordpress-theme' ); ?></p>
			<p><?php esc_html_e( 'O Método PRO estrutura toda a sua preparação em um sistema claro e ajustável.', 'proenem-wordpress-theme' ); ?></p>
			<a class="pro-button pro-button--red" href="#planos"><?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?> <span aria-hidden="true">-></span></a>
		</div>
		<div class="pro-pillars__cards">
			<article class="pro-pillar-card pro-pillar-card--small">
				<img src="<?php echo esc_url( $home_asset_uri( 'pillar-meta.webp' ) ); ?>" alt="">
				<span>01</span>
				<h3><?php esc_html_e( 'Meta', 'proenem-wordpress-theme' ); ?></h3>
			</article>
			<article class="pro-pillar-card pro-pillar-card--large">
				<img src="<?php echo esc_url( $home_asset_uri( 'pillar-diagnostico.webp' ) ); ?>" alt="">
				<span>02</span>
				<div>
					<h3><?php esc_html_e( 'Diagnóstico', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Mapeamos suas forças e lacunas com simulados adaptativos. Você vê exatamente onde está.', 'proenem-wordpress-theme' ); ?></p>
				</div>
			</article>
			<article class="pro-pillar-card pro-pillar-card--small pro-pillar-card--red">
				<img src="<?php echo esc_url( $home_asset_uri( 'pillar-execucao.webp' ) ); ?>" alt="">
				<span>03</span>
				<h3><?php esc_html_e( 'Execução', 'proenem-wordpress-theme' ); ?></h3>
			</article>
		</div>
	</section>

	<section id="aprovados" class="pro-proof" aria-labelledby="pro-proof-title">
		<div class="pro-proof__students">
			<?php for ( $index = 0; $index < 6; $index++ ) : ?>
				<img src="<?php echo esc_url( $home_asset_uri( 'proof-students-1.webp' ) ); ?>" alt="<?php esc_attr_e( 'Aluno aprovado exibindo aprovação.', 'proenem-wordpress-theme' ); ?>">
			<?php endfor; ?>
		</div>
		<div class="pro-proof__strip">
			<h2 id="pro-proof-title"><?php esc_html_e( '+ de 40.000 aprovados em universidades públicas', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'UFRJ · UFRGS · Unicamp · UERJ · USP · Unifesp', 'proenem-wordpress-theme' ); ?></p>
		</div>
	</section>

	<section class="pro-pain" aria-labelledby="pro-pain-title">
		<p class="pro-pill"><?php esc_html_e( 'Você se identifica?', 'proenem-wordpress-theme' ); ?></p>
		<h2 id="pro-pain-title"><?php esc_html_e( 'Já sentiu que estuda muito, mas a nota não sobe?', 'proenem-wordpress-theme' ); ?></h2>
		<div class="pro-card-grid">
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

	<section class="pro-platform" aria-labelledby="pro-platform-title">
		<h2 id="pro-platform-title"><?php esc_html_e( 'Explore por dentro cada detalhe', 'proenem-wordpress-theme' ); ?></h2>
		<div class="pro-platform__panel">
			<ul>
				<li><?php esc_html_e( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( '+50 mil questões', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Plano personalizado', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Tutor com IA', 'proenem-wordpress-theme' ); ?></li>
				<li><?php esc_html_e( 'Correção de redação', 'proenem-wordpress-theme' ); ?></li>
				<li class="is-active"><?php esc_html_e( 'Simulados com TRI', 'proenem-wordpress-theme' ); ?></li>
			</ul>
			<div class="pro-platform__screen">
				<div>
					<h3><?php esc_html_e( 'Simulados com a mesma lógica de correção do ENEM.', 'proenem-wordpress-theme' ); ?></h3>
					<p><?php esc_html_e( 'Veja sua nota real, evolução por área e onde focar agora.', 'proenem-wordpress-theme' ); ?></p>
					<div class="pro-bars" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
				</div>
			</div>
		</div>
	</section>

	<section id="questoes" class="pro-questions" aria-labelledby="pro-questions-title">
		<h2 id="pro-questions-title"><?php esc_html_e( 'Explore +50 mil questões sem precisar criar conta.', 'proenem-wordpress-theme' ); ?></h2>
		<p><?php esc_html_e( 'Questões do ENEM e dos principais vestibulares, com resolução em vídeo. Escolha uma disciplina e comece agora.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pro-subjects">
			<?php foreach ( $subjects as $subject ) : ?>
				<a href="#planos">
					<strong><?php echo esc_html( $subject ); ?></strong>
					<span><?php esc_html_e( '512 questões · 40 aulas', 'proenem-wordpress-theme' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<a class="pro-button" href="#planos"><?php esc_html_e( 'Comece agora! É gratuito', 'proenem-wordpress-theme' ); ?></a>
	</section>

	<section id="planos" class="pro-pricing" aria-labelledby="pro-pricing-title">
		<h2 id="pro-pricing-title"><?php esc_html_e( 'Investimento que se paga em uma vaga.', 'proenem-wordpress-theme' ); ?></h2>
		<p><?php esc_html_e( 'Comece grátis. Cancele com 1 clique. 7 dias de garantia em todos os planos.', 'proenem-wordpress-theme' ); ?></p>
		<div class="pro-plans">
			<?php foreach ( $plans as $plan ) : ?>
				<article class="pro-plan<?php echo ! empty( $plan['featured'] ) ? ' is-featured' : ''; ?>">
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
					<a class="pro-button pro-button--red" href="#faq"><?php esc_html_e( 'Quero o Método PRO', 'proenem-wordpress-theme' ); ?> <span aria-hidden="true">-></span></a>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="pro-school" aria-labelledby="pro-school-title">
		<div>
			<h2 id="pro-school-title"><?php esc_html_e( 'Leve o Método PRO para a sua escola.', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Planos especiais para instituições que querem oferecer a melhor preparação para o ENEM em um único pacote.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pro-card-grid pro-card-grid--school">
			<article><h3><?php esc_html_e( 'Combo plataforma + apostilas', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Acesso completo à plataforma e kit de apostilas exclusivas.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Acompanhe cada aluno', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Painel exclusivo para desempenho, simulados e frequência.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Acesso para todos os alunos', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Licenças geradas para todas as turmas.', 'proenem-wordpress-theme' ); ?></p></article>
			<article><h3><?php esc_html_e( 'Onboarding dedicado', 'proenem-wordpress-theme' ); ?></h3><p><?php esc_html_e( 'Um especialista acompanha os primeiros ciclos.', 'proenem-wordpress-theme' ); ?></p></article>
		</div>
	</section>

	<section class="pro-final-cta" aria-labelledby="pro-final-title">
		<h2 id="pro-final-title"><?php esc_html_e( 'Pronto para transformar a preparação ENEM na sua escola?', 'proenem-wordpress-theme' ); ?></h2>
		<p><?php esc_html_e( 'Converse com nossa equipe e receba uma proposta personalizada.', 'proenem-wordpress-theme' ); ?></p>
		<a class="pro-button" href="#faq"><?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?> <span aria-hidden="true">-></span></a>
	</section>

	<section id="faq" class="pro-faq" aria-labelledby="pro-faq-title">
		<p class="pro-pill"><?php esc_html_e( 'Perguntas frequentes', 'proenem-wordpress-theme' ); ?></p>
		<h2 id="pro-faq-title"><?php esc_html_e( 'Já sentiu que estuda muito, mas a nota não sobe?', 'proenem-wordpress-theme' ); ?></h2>
		<div class="pro-faq__items">
			<?php foreach ( $faq_items as $index => $item ) : ?>
				<details <?php echo 1 === $index ? 'open' : ''; ?>>
					<summary><?php echo esc_html( $item['question'] ); ?></summary>
					<p><?php echo esc_html( $item['answer'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</section>

	<footer class="pro-home-footer">
		<a class="pro-home-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ProEnem', 'proenem-wordpress-theme' ); ?></a>
		<div>
			<p class="pro-pill"><?php esc_html_e( 'Manifesto Proenem', 'proenem-wordpress-theme' ); ?></p>
			<h2><?php esc_html_e( 'Sua Aprovação não é sorte. É método.', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Construímos a infraestrutura que transforma esforço em resultado. Você estuda, a engenharia faz o resto.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Links do rodapé da home', 'proenem-wordpress-theme' ); ?>">
			<a href="#metodo"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></a>
			<a href="#planos"><?php esc_html_e( 'Planos', 'proenem-wordpress-theme' ); ?></a>
			<a href="#questoes"><?php esc_html_e( 'Banco de questões', 'proenem-wordpress-theme' ); ?></a>
			<a href="#faq"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></a>
		</nav>
	</footer>
</main>

<?php
get_footer();
