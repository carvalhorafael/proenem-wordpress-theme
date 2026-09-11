<?php
/**
 * Free Materials category archive.
 *
 * Without this template the category URL registered by the free-materials
 * plugin falls through to archive.php, which renders the blog index.
 *
 * @package Proenem
 */

get_header();

$material_term        = get_queried_object();
$material_term_name   = $material_term instanceof WP_Term ? $material_term->name : __( 'Materiais gratuitos', 'proenem-wordpress-theme' );
$material_term_slug   = $material_term instanceof WP_Term ? $material_term->slug : '';
$material_description = $material_term instanceof WP_Term ? wp_strip_all_tags( term_description( $material_term ) ) : '';
$selected_slugs       = '' !== $material_term_slug ? array( $material_term_slug ) : array();

$terms = get_terms(
	array(
		'taxonomy'   => proenem_get_free_materials_taxonomy(),
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

if ( is_wp_error( $terms ) ) {
	$terms = array();
}

$materials_query = new WP_Query( proenem_build_free_materials_query_args( $selected_slugs ) );

if ( '' === trim( $material_description ) ) {
	$material_description = sprintf(
		/* translators: %s: Material category name. */
		__( 'Materiais gratuitos de %s para organizar revisão, prática e estratégia de estudo.', 'proenem-wordpress-theme' ),
		$material_term_name
	);
}
?>

<main id="primary" class="site-main pro-materials-page">
	<section class="pro-materials-hero pro-materials-hero--catalog" aria-labelledby="pro-materials-title">
		<div class="pro-materials-hero__copy">
			<a class="pro-materials-hero__back" href="<?php echo esc_url( proenem_get_free_materials_url() ); ?>">
				<?php esc_html_e( '← Todos os materiais gratuitos', 'proenem-wordpress-theme' ); ?>
			</a>
			<span class="pen-section-pill"><?php esc_html_e( 'Materiais gratuitos', 'proenem-wordpress-theme' ); ?></span>
			<h1 id="pro-materials-title"><?php echo esc_html( $material_term_name ); ?></h1>
			<p><?php echo esc_html( $material_description ); ?></p>
		</div>

		<?php proenem_render_material_category_tabs( $terms, $selected_slugs ); ?>
	</section>

	<?php
	get_template_part(
		'template-parts/materials/catalog',
		null,
		array(
			'terms'          => $terms,
			'action_url'     => get_term_link( $material_term ) && ! is_wp_error( get_term_link( $material_term ) ) ? get_term_link( $material_term ) : proenem_get_free_materials_url(),
			'selected_slugs' => $selected_slugs,
			'query'          => $materials_query,
			'heading'        => sprintf(
				/* translators: %s: Material category name. */
				__( 'Materiais de %s', 'proenem-wordpress-theme' ),
				$material_term_name
			),
		)
	);
	?>
</main>

<?php
get_footer();
