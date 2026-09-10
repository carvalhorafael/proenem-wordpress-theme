<?php
/**
 * Seed the free materials catalog used by the browser tests.
 *
 * Creates the catalog page on the Materiais gratuitos template plus one
 * material per category, so the e2e suite can exercise filtering, the
 * category archive and the capture form against known content.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/seed-free-materials.php
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Report an operational message.
 *
 * @param string $message Message to report.
 * @return void
 */
function proenem_materials_seed_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

if ( ! function_exists( 'proenem_free_materials_is_available' ) || ! proenem_free_materials_is_available() ) {
	proenem_materials_seed_log( 'O plugin Free Materials nao esta ativo.' );
	return;
}

$materials_post_type = proenem_get_free_materials_post_type();
$materials_taxonomy  = proenem_get_free_materials_taxonomy();

$catalog_page = get_page_by_path( 'materiais-gratuitos' );

if ( ! $catalog_page instanceof WP_Post ) {
	$catalog_page_id = wp_insert_post(
		array(
			'post_name'   => 'materiais-gratuitos',
			'post_status' => 'publish',
			'post_title'  => 'Materiais gratuitos',
			'post_type'   => 'page',
		),
		true
	);

	if ( is_wp_error( $catalog_page_id ) ) {
		proenem_materials_seed_log( 'Nao foi possivel criar a pagina do catalogo: ' . $catalog_page_id->get_error_message() );
		return;
	}
} else {
	$catalog_page_id = (int) $catalog_page->ID;
}

update_post_meta( $catalog_page_id, '_wp_page_template', 'page-templates/free-materials.php' );
proenem_materials_seed_log( 'Pagina do catalogo pronta: ' . get_permalink( $catalog_page_id ) );

$materials = array(
	array(
		'slug'     => 'modelo-de-rotina-de-redacao',
		'title'    => 'Modelo de rotina de redação',
		'excerpt'  => 'Um roteiro para treinar repertório, estrutura e revisão de texto.',
		'category' => array(
			'slug' => 'redacao',
			'name' => 'Redação',
		),
	),
	array(
		'slug'     => 'mapa-de-analise-de-simulados',
		'title'    => 'Mapa de análise de simulados',
		'excerpt'  => 'Transforme o resultado do simulado em plano de estudo para a semana seguinte.',
		'category' => array(
			'slug' => 'simulados',
			'name' => 'Simulados',
		),
	),
	array(
		'slug'     => 'checklist-de-revisao-para-o-enem',
		'title'    => 'Checklist de revisão para o ENEM',
		'excerpt'  => 'Uma lista prática para revisar conteúdos prioritários sem perder ritmo.',
		'category' => array(
			'slug' => 'cronograma',
			'name' => 'Cronograma',
		),
	),
);

foreach ( $materials as $material ) {
	$existing = get_posts(
		array(
			'name'           => $material['slug'],
			'post_status'    => 'publish',
			'post_type'      => $materials_post_type,
			'posts_per_page' => 1,
		)
	);

	$material_id = $existing ? (int) $existing[0]->ID : 0;

	if ( ! $material_id ) {
		$material_id = wp_insert_post(
			array(
				'post_content' => '<!-- wp:paragraph --><p>' . $material['excerpt'] . '</p><!-- /wp:paragraph -->',
				'post_excerpt' => $material['excerpt'],
				'post_name'    => $material['slug'],
				'post_status'  => 'publish',
				'post_title'   => $material['title'],
				'post_type'    => $materials_post_type,
			),
			true
		);
	}

	if ( is_wp_error( $material_id ) ) {
		proenem_materials_seed_log( 'Nao foi possivel criar o material ' . $material['slug'] . ': ' . $material_id->get_error_message() );
		continue;
	}

	$term = get_term_by( 'slug', $material['category']['slug'], $materials_taxonomy );

	if ( ! $term instanceof WP_Term ) {
		$created = wp_insert_term( $material['category']['name'], $materials_taxonomy, array( 'slug' => $material['category']['slug'] ) );

		if ( is_wp_error( $created ) ) {
			proenem_materials_seed_log( 'Nao foi possivel criar a categoria ' . $material['category']['slug'] . ': ' . $created->get_error_message() );
			continue;
		}

		$term = get_term( (int) $created['term_id'], $materials_taxonomy );
	}

	wp_set_object_terms( $material_id, array( (int) $term->term_id ), $materials_taxonomy );

	proenem_materials_seed_log( 'Material pronto: ' . get_permalink( $material_id ) );
}

flush_rewrite_rules( false );
proenem_materials_seed_log( 'Regras de rewrite atualizadas.' );
