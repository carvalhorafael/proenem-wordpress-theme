<?php
/**
 * Seed a sales page used to homologate the Proenem landing page widgets.
 *
 * The page intentionally repeats the same widget twice so duplicated DOM ids
 * become visible during review.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/seed-lp-homologation.php
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
function proenem_lp_homologation_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * Build an Elementor container holding a single widget.
 *
 * @param string $id Element id.
 * @param string $widget_type Widget technical name.
 * @param array  $settings Widget settings.
 * @return array
 */
function proenem_lp_homologation_container( $id, $widget_type, $settings ) {
	return array(
		'id'       => $id . 'c',
		'elType'   => 'container',
		'settings' => array(),
		'elements' => array(
			array(
				'id'         => $id,
				'elType'     => 'widget',
				'widgetType' => $widget_type,
				'settings'   => $settings,
			),
		),
	);
}

if ( ! post_type_exists( 'sales_page' ) ) {
	proenem_lp_homologation_log( 'O CPT sales_page nao esta disponivel. Ative o plugin sales-page.' );
	return;
}

$slug     = 'homologacao-widgets-lp';
$existing = get_page_by_path( $slug, OBJECT, 'sales_page' );

$elementor_data = array(
	proenem_lp_homologation_container(
		'lphero01',
		'pro_offer_hero',
		array(
			'eyebrow'       => 'Turma Intensiva ENEM 2026',
			'title'         => 'Hero de oferta com fundo de marca e ancora',
			'body'          => 'Secao com tone marca e ancora oferta para validar o contrato compartilhado.',
			'primary_label' => 'Quero minha vaga',
			'primary_url'   => array( 'url' => '#oferta' ),
			'tone'          => 'brand',
			'anchor_id'     => 'inicio',
		)
	),
	proenem_lp_homologation_container(
		'lpbenef01',
		'pro_benefits_list',
		array(
			'title' => 'Lista de beneficios com tone superficie',
			'tone'  => 'surface',
			'items' => array(
				array(
					'_id'   => 'b1',
					'title' => 'Diagnostico da sua nota',
					'body'  => 'Primeiro item de teste.',
				),
				array(
					'_id'   => 'b2',
					'title' => 'Cronograma pronto',
					'body'  => 'Segundo item de teste.',
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lpfaq01',
		'pro_faq',
		array(
			'title' => 'FAQ numero 1 na mesma pagina',
			'items' => array(
				array(
					'_id'      => 'q1',
					'question' => 'A primeira instancia gera id unico?',
					'answer'   => 'Deve gerar pro-faq-title mais o id do widget.',
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lpfaq02',
		'pro_faq',
		array(
			'title' => 'FAQ numero 2 na mesma pagina',
			'tone'  => 'surface',
			'items' => array(
				array(
					'_id'      => 'q2',
					'question' => 'A segunda instancia repete o id da primeira?',
					'answer'   => 'Nao deve repetir. Este e o teste do contrato.',
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lpcta01',
		'pro_cta',
		array(
			'title'        => 'CTA final com ancora oferta',
			'body'         => 'Destino da ancora usada pelo hero.',
			'button_label' => 'Garantir minha vaga',
			'button_url'   => array( 'url' => '#oferta' ),
			'anchor_id'    => 'oferta',
		)
	),
	proenem_lp_homologation_container(
		'lpmetrics01',
		'pro_lp_metrics',
		array(
			'tone' => 'surface',
		)
	),
	proenem_lp_homologation_container(
		'lpoffer01',
		'pro_lp_offer_highlight',
		array(
			'button_url' => array( 'url' => '#oferta' ),
			'tone'       => 'brand',
		)
	),
	proenem_lp_homologation_container(
		'lpspot01',
		'pro_lp_spotlight',
		array(
			'image'     => array( 'url' => get_template_directory_uri() . '/assets/images/platform/study-plan-960.webp' ),
			'image_alt' => 'Tela do plano de estudos semanal.',
		)
	),
	proenem_lp_homologation_container(
		'lpspot02',
		'pro_lp_spotlight',
		array(
			'eyebrow'        => 'Redacao',
			'title'          => 'Segundo spotlight com a imagem antes do texto.',
			'body'           => 'Valida o controle de posicao da imagem e o id de heading por instancia.',
			'media_position' => 'start',
			'image'          => array( 'url' => get_template_directory_uri() . '/assets/images/platform/essay-feedback-960.webp' ),
			'image_alt'      => 'Tela da correcao de redacao por competencia.',
			'tone'           => 'surface',
			'button_label'   => 'Quero treinar minha redacao',
			'button_url'     => array( 'url' => '#oferta' ),
		)
	),
	proenem_lp_homologation_container(
		'lpvideo01',
		'pro_lp_video_story',
		array(
			'video_url'  => array( 'url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ' ),
			'button_url' => array( 'url' => '#oferta' ),
		)
	),
	proenem_lp_homologation_container(
		'lpcard01',
		'pro_pricing_card',
		array(
			'badge'        => 'Card, nao secao',
			'name'         => 'Turma Intensiva ENEM 2026',
			'description'  => 'Deve continuar delimitado como card, sem sangria total.',
			'price'        => 'R$ 29,90',
			'recurrence'   => 'por mes',
			'features'     => "Cronograma semanal\nCorrecao de redacao",
			'button_label' => 'Garantir minha vaga',
			'button_url'   => array( 'url' => '#oferta' ),
		)
	),
	proenem_lp_homologation_container(
		'lphmfaq01',
		'pro_home_faq',
		array(
			'eyebrow'      => 'Duvidas',
			'title_line_1' => 'FAQ da home numero 1',
			'title_line_2' => 'na mesma pagina',
			'items'        => array(
				array(
					'_id'      => 'hq1',
					'question' => 'Os widgets da home ainda usam id fixo?',
					'answer'   => 'Nao devem usar. Este e o teste da correcao.',
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lphmfaq02',
		'pro_home_faq',
		array(
			'eyebrow'      => 'Duvidas',
			'title_line_1' => 'FAQ da home numero 2',
			'title_line_2' => 'na mesma pagina',
			'anchor_id'    => 'faq-2',
			'items'        => array(
				array(
					'_id'      => 'hq2',
					'question' => 'A segunda instancia repete o id da primeira?',
					'answer'   => 'Nao deve repetir.',
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lphmcta01',
		'pro_home_final_cta',
		array(
			'title'        => 'CTA final da home reutilizado em LP',
			'body'         => 'Valida que o id do heading vem do id do widget.',
			'button_label' => 'Ver planos',
			'button_url'   => array( 'url' => '#oferta' ),
		)
	),
);

$postarr = array(
	'post_type'    => 'sales_page',
	'post_status'  => 'publish',
	'post_title'   => 'Homologacao widgets de LP',
	'post_name'    => $slug,
	'post_content' => '',
);

if ( $existing instanceof WP_Post ) {
	$postarr['ID'] = $existing->ID;

	// wp_update_post() revalidates the stored template, and Elementor templates
	// are not registered for the post type outside the editor request.
	$postarr['page_template'] = '';

	$page_id = wp_update_post( $postarr, true );
} else {
	$page_id = wp_insert_post( $postarr, true );
}

if ( is_wp_error( $page_id ) ) {
	proenem_lp_homologation_log( 'Falha ao criar a pagina: ' . $page_id->get_error_message() );
	return;
}

update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-post' );
update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elementor_data ) ) );

// Elementor keeps rendered markup, generated CSS and the asset map in post
// meta, so a reseed has to drop them or the page keeps serving the old blocks.
delete_post_meta( $page_id, '_elementor_css' );
delete_post_meta( $page_id, '_elementor_element_cache' );
delete_post_meta( $page_id, '_elementor_page_assets' );

proenem_lp_homologation_log( 'Pagina de homologacao pronta: ' . get_permalink( $page_id ) );
