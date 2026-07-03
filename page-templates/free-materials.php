<?php
/**
 * Template Name: Materiais gratuitos
 * Template Post Type: page
 *
 * @package Proenem
 */

get_header();

$materials_post_type = proenem_get_free_materials_post_type();
$materials_taxonomy  = proenem_get_free_materials_taxonomy();
$selected_slugs      = proenem_get_selected_material_category_slugs();
$terms               = proenem_free_materials_is_available()
	? get_terms(
		array(
			'taxonomy'   => $materials_taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	)
	: array();

if ( is_wp_error( $terms ) ) {
	$terms = array();
}

$materials_query_args = array(
	'post_type'           => $materials_post_type,
	'post_status'         => 'publish',
	'posts_per_page'      => -1,
	'ignore_sticky_posts' => true,
);

$materials_query = proenem_free_materials_is_available() ? new WP_Query( $materials_query_args ) : null;
$total_materials = $materials_query instanceof WP_Query ? (int) $materials_query->found_posts : 0;
?>

<main id="primary" class="site-main pro-materials-page">
	<section class="pro-materials-hero" aria-labelledby="pro-materials-title">
		<div class="pro-materials-hero__copy">
			<span class="pen-section-pill"><?php esc_html_e( 'Materiais gratuitos', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-materials-title"><?php esc_html_e( 'Guias para estudar com método antes da próxima prova', 'proenem-wordpress-theme' ); ?></h1>
			<p><?php esc_html_e( 'Escolha uma categoria e encontre materiais prontos para organizar revisão, prática e estratégia de estudo.', 'proenem-wordpress-theme' ); ?></p>
		</div>
	</section>

	<div class="pro-materials-layout">
		<aside class="pro-materials-layout__sidebar" aria-label="<?php esc_attr_e( 'Filtros de materiais gratuitos', 'proenem-wordpress-theme' ); ?>">
			<?php proenem_render_material_category_filters( $terms, $selected_slugs ); ?>
		</aside>

		<section class="pro-materials-results" aria-labelledby="pro-materials-results-title">
			<div class="pro-materials-results__header">
				<h2 id="pro-materials-results-title"><?php esc_html_e( 'Todos os materiais', 'proenem-wordpress-theme' ); ?></h2>
					<p
						data-pro-materials-count
						<?php /* translators: %s: Number of materials found. */ ?>
						data-count-template-singular="<?php esc_attr_e( '%s material disponível', 'proenem-wordpress-theme' ); ?>"
						<?php /* translators: %s: Number of materials found. */ ?>
						data-count-template-plural="<?php esc_attr_e( '%s materiais disponíveis', 'proenem-wordpress-theme' ); ?>"
					>
					<?php
					printf(
						/* translators: %s: Number of materials found. */
						esc_html__( '%s materiais disponíveis', 'proenem-wordpress-theme' ),
						esc_html( number_format_i18n( $total_materials ) )
					);
					?>
				</p>
			</div>

			<?php if ( ! proenem_free_materials_is_available() ) : ?>
				<?php
				proenem_render_materials_empty_state(
					__( 'Plugin Free Materials não está ativo.', 'proenem-wordpress-theme' ),
					__( 'Ative o plugin para publicar e listar materiais gratuitos nesta página.', 'proenem-wordpress-theme' )
				);
				?>
				<?php elseif ( $materials_query->have_posts() ) : ?>
					<div class="pro-materials-grid" data-pro-materials-grid>
						<?php
						while ( $materials_query->have_posts() ) :
							$materials_query->the_post();
							proenem_render_material_card( get_the_ID() );
						endwhile;
						wp_reset_postdata();
						?>
					</div>

					<div class="pro-materials-empty pro-materials-empty--filtered" hidden data-pro-materials-empty>
						<span aria-hidden="true">✦</span>
						<h2><?php esc_html_e( 'Nenhum material encontrado.', 'proenem-wordpress-theme' ); ?></h2>
						<p><?php esc_html_e( 'Tente selecionar outra categoria ou limpar os filtros.', 'proenem-wordpress-theme' ); ?></p>
					</div>
			<?php else : ?>
				<?php
				proenem_render_materials_empty_state(
					__( 'Nenhum material encontrado.', 'proenem-wordpress-theme' ),
					__( 'Tente limpar os filtros ou cadastre novos materiais gratuitos no WordPress.', 'proenem-wordpress-theme' )
				);
				?>
			<?php endif; ?>
		</section>
	</div>
</main>

<?php
get_footer();
