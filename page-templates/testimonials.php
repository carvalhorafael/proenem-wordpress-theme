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

if ( ! empty( $selected_slugs ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Category filtering is the expected query contract for this listing page.
	$testimonials_query_args['tax_query'] = array(
		array(
			'taxonomy' => $testimonials_taxonomy,
			'field'    => 'slug',
			'terms'    => $selected_slugs,
		),
	);
}

$testimonials_query = proenem_testimonials_is_available() ? new WP_Query( $testimonials_query_args ) : null;
$total_testimonials = $testimonials_query instanceof WP_Query ? (int) $testimonials_query->found_posts : 0;
$hero_testimonials  = proenem_get_home_proof_testimonials( array(), 3 );
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

	<div class="pro-materials-layout pro-testimonials-layout">
		<aside class="pro-materials-layout__sidebar" aria-label="<?php esc_attr_e( 'Filtros de depoimentos', 'proenem-wordpress-theme' ); ?>">
			<?php proenem_render_testimonial_category_filters( $terms, $selected_slugs ); ?>
		</aside>

		<section class="pro-materials-results pro-testimonials-results" aria-labelledby="pro-testimonials-results-title">
			<div class="pro-materials-results__header">
				<h2 id="pro-testimonials-results-title"><?php esc_html_e( 'Todos os depoimentos', 'proenem-wordpress-theme' ); ?></h2>
				<p>
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
				<div class="pro-testimonials-grid">
					<?php
					while ( $testimonials_query->have_posts() ) :
						$testimonials_query->the_post();
						proenem_render_testimonial_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
					?>
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
</main>

<?php
get_footer();
