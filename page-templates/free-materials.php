<?php
/**
 * Template Name: Materiais gratuitos
 * Template Post Type: page
 *
 * @package Proenem
 */

get_header();

$materials_taxonomy = proenem_get_free_materials_taxonomy();
$selected_slugs     = proenem_get_selected_material_category_slugs();
$terms              = proenem_free_materials_is_available()
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

$materials_query = proenem_free_materials_is_available()
	? new WP_Query( proenem_build_free_materials_query_args( $selected_slugs ) )
	: null;
?>

<main id="primary" class="site-main pro-materials-page">
	<section class="pro-materials-hero pro-materials-hero--catalog" aria-labelledby="pro-materials-title">
		<div class="pro-materials-hero__copy">
			<span class="pen-section-pill"><?php esc_html_e( 'Materiais gratuitos', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-materials-title"><?php esc_html_e( 'Guias para estudar com método antes da próxima prova', 'proenem-wordpress-theme' ); ?></h1>
			<p><?php esc_html_e( 'Materiais prontos para organizar revisão, prática e estratégia de estudo.', 'proenem-wordpress-theme' ); ?></p>
		</div>
	</section>

	<?php
	get_template_part(
		'template-parts/materials/catalog',
		null,
		array(
			'terms'          => $terms,
			'selected_slugs' => $selected_slugs,
			'query'          => $materials_query,
			'heading'        => __( 'Todos os materiais', 'proenem-wordpress-theme' ),
		)
	);
	?>
</main>

<?php
get_footer();
