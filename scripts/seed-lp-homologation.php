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
		'lpnav01',
		'pro_navbar',
		array(
			'mode'      => 'lp',
			'cta_label' => 'Garantir minha vaga',
			'cta_url'   => array( 'url' => '#oferta' ),
		)
	),
	proenem_lp_homologation_container(
		'lphero01',
		'pro_offer_hero',
		array(
			'eyebrow'       => 'Turma Intensiva ENEM 2026',
			'title'         => 'Hero de oferta com fundo de marca e ancora',
			'body'          => 'Secao com tone marca e ancora oferta para validar o contrato compartilhado.',
			'primary_label' => 'Quero minha vaga',
			'primary_url'   => array( 'url' => '#oferta' ),
			'microcopy'     => 'Vagas limitadas - Inicio hoje - Acesso imediato',
			'heading_level' => 'h1',
			'proof_cards'   => array(
				array(
					'_id'   => 'p1',
					'label' => 'Cronograma pronto',
					'value' => 'Semana 1 de 12',
				),
				array(
					'_id'   => 'p2',
					'label' => 'Redacao 940',
					'value' => 'Corrigida em 10 dias',
				),
				array(
					'_id'   => 'p3',
					'label' => 'Organizacao diaria',
					'value' => '4 de 5 tarefas hoje',
				),
			),
			'tone'          => 'brand',
			'anchor_id'     => 'inicio',
		)
	),
	proenem_lp_homologation_container(
		'lpbenef01',
		'pro_benefits_list',
		array(
			'eyebrow' => 'O metodo',
			'title'   => 'Lista de beneficios com quatro colunas e destaque',
			'body'    => 'Valida eyebrow, corpo, colunas, icone e item em destaque.',
			'columns' => '4',
			'tone'    => 'surface',
			'items'   => array(
				array(
					'_id'   => 'b1',
					'title' => 'Diagnostico da sua nota',
					'body'  => 'Item com icone da biblioteca.',
					'icon'  => array( 'url' => get_template_directory_uri() . '/assets/images/brand/logo_proenem.svg' ),
				),
				array(
					'_id'   => 'b2',
					'title' => 'Cronograma pronto',
					'body'  => 'Item com marcador padrao.',
				),
				array(
					'_id'   => 'b3',
					'title' => 'Evolucao acompanhada',
					'body'  => 'Terceiro item de teste.',
				),
				array(
					'_id'       => 'b4',
					'title'     => 'Correcao de redacao',
					'body'      => 'Item em destaque com selo.',
					'highlight' => 'yes',
					'badge'     => 'Destaque',
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
			'eyebrow'      => 'A reta final comecou',
			'title'        => 'CTA final com ancora oferta',
			'body'         => 'Destino da ancora usada pelo hero.',
			'button_label' => 'Garantir minha vaga',
			'button_url'   => array( 'url' => '#oferta' ),
			'microcopy'    => 'Acesso imediato - Garantia de 7 dias - Vagas limitadas',
			'tone'         => 'brand',
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
			'badge'         => 'Card, nao secao',
			'name'          => 'Turma Intensiva ENEM 2026',
			'description'   => 'Acesso completo ate o dia da prova.',
			'price_prefix'  => '12x de',
			'price'         => 'R$ 29,90',
			'price_details' => 'ou R$ 306,90 a vista',
			'features'      => "Cronograma semanal\nCorrecao de redacao\nSimulados corrigidos no padrao ENEM",
			'trust_items'   => "Pagamento 100% seguro\nGarantia de 7 dias\nAcesso liberado na hora",
			'button_label'  => 'Garantir minha vaga',
			'button_url'    => array( 'url' => '#oferta' ),
		)
	),
	proenem_lp_homologation_container(
		'lpcount01',
		'pro_offer_countdown',
		array(
			'title'    => 'Contador de oferta',
			'body'     => 'O campo de data renderiza como texto estatico, sem contagem dinamica.',
			'deadline' => '2026-12-31 23:59',
		)
	),
	proenem_lp_homologation_container(
		'lpcompare01',
		'pro_plans_comparison',
		array(
			'title'   => 'Comparativo de planos',
			'columns' => "Gratis\nIntensiva",
			'rows'    => array(
				array(
					'_id'     => 'r1',
					'feature' => 'Cronograma semanal',
					'values'  => "-\nSim",
				),
				array(
					'_id'     => 'r2',
					'feature' => 'Correcao de redacao',
					'values'  => "-\n4 por mes",
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lpgrid01',
		'pro_pricing_grid',
		array(
			'eyebrow' => 'Vagas por tempo limitado',
			'title'   => 'Grade de planos em faixa de marca',
			'body'    => 'Valida o par de cores dos componentes dentro da faixa.',
			'tone'    => 'brand',
			'plans'   => array(
				array(
					'_id'           => 'g1',
					'name'          => 'Turma Intensiva',
					'badge'         => 'Mais escolhido',
					'price_prefix'  => '12x de',
					'price'         => 'R$ 29,90',
					'recurrence'    => '',
					'price_details' => 'ou R$ 306,90 a vista',
					'features'      => "Cronograma semanal\nCorrecao de redacao",
					'trust_items'   => "Pagamento 100% seguro\nGarantia de 7 dias",
					'button_label'  => 'Garantir minha vaga',
					'button_url'    => array( 'url' => '#oferta' ),
				),
			),
		)
	),
	proenem_lp_homologation_container(
		'lpfaqbrand01',
		'pro_faq',
		array(
			'title' => 'FAQ em faixa de marca',
			'tone'  => 'brand',
			'items' => array(
				array(
					'_id'      => 'qb1',
					'question' => 'O item do FAQ mantem contraste na faixa de marca?',
					'answer'   => 'Deve manter, com superficie branca e texto ink.',
				),
			),
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
	proenem_lp_homologation_container(
		'lptesti01',
		'pro_home_testimonials',
		array(
			'limit' => 2,
		)
	),
	proenem_lp_homologation_container(
		'lpfooter01',
		'pro_footer',
		array(
			'mode' => 'minimal',
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
