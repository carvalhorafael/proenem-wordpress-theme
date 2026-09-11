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

// The highlight only makes sense on the unfiltered first page: on a category
// archive it could promote a material from another category, and repeating it
// on page two would just take the place of the materials being paged to.
$featured_material = empty( $selected_slugs ) && 1 === proenem_get_materials_paged()
	? proenem_get_featured_material()
	: null;
$featured_id       = $featured_material instanceof WP_Post ? (int) $featured_material->ID : 0;

// The highlight promotes a material, it does not remove it from the catalog.
// Dropping it from the grid would make "Todos os materiais" and the count lie.
$materials_query = proenem_free_materials_is_available()
	? new WP_Query( proenem_build_free_materials_query_args( $selected_slugs ) )
	: null;
?>

<main id="primary" class="site-main pro-materials-page">
	<section class="pro-materials-hero pro-materials-hero--catalog" aria-labelledby="pro-materials-title">
		<div class="pro-materials-hero__copy">
			<h1 id="pro-materials-title"><?php esc_html_e( 'Pegue o material que resolve o seu próximo passo', 'proenem-wordpress-theme' ); ?></h1>
			<p><?php esc_html_e( 'Guias, planners e checklists prontos para organizar revisão, prática e estratégia de estudo.', 'proenem-wordpress-theme' ); ?></p>
		</div>

		<?php proenem_render_material_category_tabs( $terms, $selected_slugs ); ?>
	</section>

	<?php
	proenem_render_featured_material( $featured_material );

	get_template_part(
		'template-parts/materials/catalog',
		null,
		array(
			'terms'          => $terms,
			'selected_slugs' => $selected_slugs,
			'query'          => $materials_query,
			'featured_id'    => $featured_id,
			'heading'        => __( 'Todos os materiais', 'proenem-wordpress-theme' ),
		)
	);
	?>
</main>

<?php
get_footer();
