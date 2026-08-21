<?php
/**
 * Template Name: Depoimentos
 * Template Post Type: page
 *
 * @package Proenem
 */

get_header();

$testimonials_post_type = proenem_get_testimonials_post_type();
$testimonials_taxonomy  = proenem_get_testimonials_taxonomy();
$selected_slugs         = proenem_get_selected_testimonial_category_slugs();
$terms                  = proenem_testimonials_is_available()
	? get_terms(
		array(
			'taxonomy'   => $testimonials_taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	)
	: array();

if ( is_wp_error( $terms ) ) {
	$terms = array();
}

$testimonials_query_args = array(
	'post_type'           => $testimonials_post_type,
	'post_status'         => 'publish',
	'posts_per_page'      => -1,
	'ignore_sticky_posts' => true,
);

$testimonials_query   = proenem_testimonials_is_available() ? new WP_Query( $testimonials_query_args ) : null;
$total_testimonials   = 0;
$hero_testimonials    = proenem_get_testimonials_hero_selection( 3 );
$featured_testimonial = proenem_get_featured_testimonial();

if ( $testimonials_query instanceof WP_Query ) {
	foreach ( $testimonials_query->posts as $testimonial_post ) {
		$testimonial_terms = get_the_terms( $testimonial_post->ID, $testimonials_taxonomy );
		$testimonial_slugs = ! empty( $testimonial_terms ) && ! is_wp_error( $testimonial_terms )
			? wp_list_pluck( $testimonial_terms, 'slug' )
			: array();

		if ( empty( $selected_slugs ) || array_intersect( $selected_slugs, $testimonial_slugs ) ) {
			++$total_testimonials;
		}
	}
}
?>

<main id="primary" class="site-main pro-materials-page pro-testimonials-page">
	<section class="pro-materials-hero pro-testimonials-hero" aria-labelledby="pro-testimonials-title">
		<div class="pro-materials-hero__copy">
			<span class="pen-section-pill"><?php esc_html_e( 'Mural de aprovados', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-testimonials-title"><?php esc_html_e( 'Um dia, seu nome pode estar aqui.', 'proenem-wordpress-theme' ); ?></h1>
			<p><?php esc_html_e( 'Conheça estudantes que transformaram rotina, estratégia e constância em aprovação. Histórias reais, caminhos diferentes e uma conquista em comum.', 'proenem-wordpress-theme' ); ?></p>
			<a class="pen-button pen-button--secondary pen-button--md pro-testimonials-hero__action" href="#pro-testimonials-results-title">
				<?php esc_html_e( 'Conhecer as histórias', 'proenem-wordpress-theme' ); ?>
				<span aria-hidden="true">↓</span>
			</a>
		</div>

		<?php if ( $hero_testimonials ) : ?>
			<div class="pro-testimonials-hero__stage" aria-label="<?php esc_attr_e( 'Estudantes em destaque', 'proenem-wordpress-theme' ); ?>">
				<?php foreach ( $hero_testimonials as $index => $hero_testimonial ) : ?>
					<?php
					$hero_id          = $hero_testimonial->ID;
					$hero_image       = proenem_get_post_image_slot( $hero_id, 'large' );
					$hero_name        = proenem_get_testimonial_student_name( $hero_id );
					$hero_course      = proenem_get_testimonial_course( $hero_id );
					$hero_institution = proenem_get_testimonial_institution( $hero_id );
					$hero_result      = implode( ' · ', array_filter( array( $hero_course, $hero_institution ) ) );
					?>
					<figure class="pro-testimonials-hero__student pro-testimonials-hero__student--<?php echo esc_attr( (string) ( $index + 1 ) ); ?>">
						<img src="<?php echo esc_url( $hero_image['src'] ); ?>" alt="<?php echo esc_attr( $hero_image['alt'] ); ?>">
						<figcaption>
							<strong><?php echo esc_html( $hero_name ); ?></strong>
							<span><?php echo esc_html( $hero_result ); ?></span>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( $featured_testimonial instanceof WP_Post ) : ?>
		<?php
		$featured_id               = $featured_testimonial->ID;
		$featured_image            = proenem_get_post_image_slot( $featured_id, 'large' );
		$featured_name             = proenem_get_testimonial_student_name( $featured_id );
		$featured_first_name_parts = preg_split( '/\s+/', $featured_name );
		$featured_first_name       = $featured_first_name_parts ? $featured_first_name_parts[0] : $featured_name;
		$featured_course           = proenem_get_testimonial_course( $featured_id );
		$featured_institution      = proenem_get_testimonial_institution( $featured_id );
		$featured_preparation      = proenem_get_testimonial_preparation_time( $featured_id );
		$featured_quote            = proenem_get_testimonial_quote( $featured_id, 44 );
		$featured_title            = $featured_preparation
			? sprintf(
				/* translators: 1: Time away from studying. 2: Student first name. 3: Course. */
				__( 'Depois de %1$s, %2$s conquistou uma vaga em %3$s.', 'proenem-wordpress-theme' ),
				$featured_preparation,
				$featured_first_name,
				$featured_course
			)
			: sprintf(
				/* translators: %s: Student first name. */
				__( 'Conheça a história de %s.', 'proenem-wordpress-theme' ),
				$featured_first_name
			);
		?>
		<section class="pro-testimonials-featured" aria-labelledby="pro-testimonials-featured-title">
			<div class="pro-testimonials-featured__inner">
				<figure class="pro-testimonials-featured__media">
					<img src="<?php echo esc_url( $featured_image['src'] ); ?>" alt="<?php echo esc_attr( $featured_image['alt'] ); ?>">
				</figure>
				<div class="pro-testimonials-featured__copy">
					<span class="pro-testimonials-featured__eyebrow"><?php esc_html_e( 'História em destaque', 'proenem-wordpress-theme' ); ?></span>
					<h2 id="pro-testimonials-featured-title"><?php echo esc_html( $featured_title ); ?></h2>
					<blockquote>
						<p><?php echo esc_html( $featured_quote ); ?></p>
					</blockquote>
					<div class="pro-testimonials-featured__student">
						<strong><?php echo esc_html( $featured_name ); ?></strong>
						<span><?php echo esc_html( implode( ' · ', array_filter( array( $featured_course, $featured_institution ) ) ) ); ?></span>
					</div>
					<a class="pen-button pen-button--primary pen-button--md pro-testimonials-featured__action" href="<?php echo esc_url( get_permalink( $featured_id ) ); ?>">
						<?php
						printf(
							/* translators: %s: Student first name. */
							esc_html__( 'Conheça a história de %s', 'proenem-wordpress-theme' ),
							esc_html( $featured_first_name )
						);
						?>
						<span aria-hidden="true">→</span>
					</a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="pro-testimonials-wall" aria-labelledby="pro-testimonials-results-title">
		<header class="pro-testimonials-wall__intro">
			<span class="pro-testimonials-wall__eyebrow"><?php esc_html_e( 'Encontre sua referência', 'proenem-wordpress-theme' ); ?></span>
			<h2 id="pro-testimonials-results-title"><?php esc_html_e( 'Toda aprovação começa quando alguém acredita que também consegue.', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Explore histórias, reconheça caminhos possíveis e encontre a inspiração para continuar construindo a sua.', 'proenem-wordpress-theme' ); ?></p>
		</header>

		<div class="pro-materials-layout pro-testimonials-layout">
			<div class="pro-materials-layout__sidebar" aria-label="<?php esc_attr_e( 'Filtros de depoimentos', 'proenem-wordpress-theme' ); ?>">
				<?php proenem_render_testimonial_category_filters( $terms, $selected_slugs ); ?>
			</div>

			<section class="pro-materials-results pro-testimonials-results" aria-labelledby="pro-testimonials-list-title">
				<div class="pro-materials-results__header">
					<h3 id="pro-testimonials-list-title"><?php esc_html_e( 'Histórias reais. Conquistas possíveis.', 'proenem-wordpress-theme' ); ?></h3>
					<p
						data-pro-testimonials-count
						aria-live="polite"
						<?php /* translators: %s: Number of testimonials found. */ ?>
						data-count-template-singular="<?php esc_attr_e( '%s depoimento publicado', 'proenem-wordpress-theme' ); ?>"
						<?php /* translators: %s: Number of testimonials found. */ ?>
						data-count-template-plural="<?php esc_attr_e( '%s depoimentos publicados', 'proenem-wordpress-theme' ); ?>"
					>
						<?php
						printf(
							/* translators: %s: Number of testimonials found. */
							esc_html( _n( '%s depoimento publicado', '%s depoimentos publicados', $total_testimonials, 'proenem-wordpress-theme' ) ),
							esc_html( number_format_i18n( $total_testimonials ) )
						);
						?>
					</p>
				</div>

				<?php if ( ! proenem_testimonials_is_available() ) : ?>
					<?php
					proenem_render_testimonials_empty_state(
						__( 'Plugin Testimonials não está ativo.', 'proenem-wordpress-theme' ),
						__( 'Ative o plugin para publicar e listar depoimentos nesta página.', 'proenem-wordpress-theme' )
					);
					?>
				<?php elseif ( $testimonials_query->have_posts() ) : ?>
					<div class="pro-testimonials-grid" data-pro-testimonials-grid>
						<?php
						while ( $testimonials_query->have_posts() ) :
							$testimonials_query->the_post();
							proenem_render_testimonial_card( get_the_ID(), $selected_slugs );
						endwhile;
						wp_reset_postdata();
						?>
					</div>

					<div class="pro-materials-empty pro-testimonials-empty"<?php echo 0 === $total_testimonials ? '' : ' hidden'; ?> data-pro-testimonials-empty>
						<span aria-hidden="true">✦</span>
						<h3><?php esc_html_e( 'Nenhum depoimento encontrado.', 'proenem-wordpress-theme' ); ?></h3>
						<p><?php esc_html_e( 'Tente selecionar outra conquista ou limpar os filtros.', 'proenem-wordpress-theme' ); ?></p>
					</div>
				<?php else : ?>
					<?php
					proenem_render_testimonials_empty_state(
						__( 'Nenhum depoimento encontrado.', 'proenem-wordpress-theme' ),
						__( 'Tente limpar os filtros ou cadastre novos depoimentos no WordPress.', 'proenem-wordpress-theme' )
					);
					?>
				<?php endif; ?>
			</section>
		</div>
	</section>

	<section class="pro-testimonials-next" aria-labelledby="pro-testimonials-next-title">
		<div class="pro-testimonials-next__inner">
			<div class="pro-testimonials-next__copy">
				<span class="pro-testimonials-next__eyebrow"><?php esc_html_e( 'Seu próximo capítulo', 'proenem-wordpress-theme' ); ?></span>
				<h2 id="pro-testimonials-next-title"><?php esc_html_e( 'Agora é a sua vez de construir uma história para este mural.', 'proenem-wordpress-theme' ); ?></h2>
				<p><?php esc_html_e( 'Você não precisa ter todo o caminho resolvido. Precisa de um plano, apoio para continuar e coragem para dar o próximo passo.', 'proenem-wordpress-theme' ); ?></p>
				<div class="pro-testimonials-next__actions">
					<a class="pen-button pen-button--secondary pen-button--lg" href="<?php echo esc_url( proenem_get_home_cta_destination( 'plans' ) ); ?>">
						<?php esc_html_e( 'Quero começar minha preparação', 'proenem-wordpress-theme' ); ?>
						<span aria-hidden="true">→</span>
					</a>
					<a class="pro-testimonials-next__back" href="#pro-testimonials-results-title"><?php esc_html_e( 'Rever as histórias', 'proenem-wordpress-theme' ); ?></a>
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
</main>

<?php
get_footer();
