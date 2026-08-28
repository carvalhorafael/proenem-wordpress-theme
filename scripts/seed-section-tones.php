<?php
/**
 * Seed a page with one band per section background, to verify that every tone
 * of the closed list keeps a readable colour pair.
 *
 * Each tone declares surface and content colour together, so the check is that
 * every text inside the band pairs against the band, and not only the heading.
 * The grid of plans repeats on two tones because a card paints its own surface
 * and keeps its own pair, which is the case most likely to break.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/seed-section-tones.php
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
function proenem_tones_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

if ( ! post_type_exists( 'sales_page' ) ) {
	proenem_tones_log( 'O CPT sales_page nao esta disponivel.' );
	return;
}

if ( ! function_exists( 'proenem_get_brand_accents' ) ) {
	proenem_tones_log( 'proenem_get_brand_accents() nao esta disponivel.' );
	return;
}

$accents  = proenem_get_brand_accents();
$elements = array();
$index    = 0;

foreach ( $accents as $tone => $accent ) {
	$elements[] = array(
		'id'         => 'tn' . str_pad( (string) $index++, 6, '0', STR_PAD_LEFT ),
		'elType'     => 'widget',
		'widgetType' => 'pro_lp_spotlight',
		'settings'   => array(
			'tone'          => $tone,
			'eyebrow'       => 'Tom de seção',
			'title'         => $accent['label'],
			'body'          => 'Contraste declarado do par: ' . $accent['contrast'] . ' para um. Este texto, o selo acima e o botão abaixo devem todos permanecer legíveis sobre esta faixa.',
			'button_label'  => 'Botão nesta faixa',
			'button_link'   => array( 'url' => '#' ),
			'anchor_id'     => 'tom-' . $tone,
		),
		'elements'   => array(),
	);
}

/* A grade de planos repete em dois tons porque o cartao pinta a propria
   superficie: dentro dele o par e do cartao, nao da faixa. Um tom escuro e um
   claro cobrem as duas polaridades. */
foreach ( array( 'ink', 'yellow' ) as $tone ) {
	$elements[] = array(
		'id'         => 'tn' . str_pad( (string) $index++, 6, '0', STR_PAD_LEFT ),
		'elType'     => 'widget',
		'widgetType' => 'pro_pricing_grid',
		'settings'   => array(
			'tone'      => $tone,
			'eyebrow'   => 'Cartão dentro da faixa',
			'title'     => 'Grade de planos sobre faixa ' . $tone,
			'anchor_id' => 'planos-' . $tone,
			'plans'     => array(
				array(
					'_id'           => 'p' . $tone,
					'name'          => 'Turma Intensiva 2026',
					'description'   => 'O cartão pinta a própria superfície, então dentro dele o par de cores é do cartão e não da faixa.',
					'badge'         => 'Mais escolhido',
					'accent'        => 'yellow',
					'price_prefix'  => '12x de R$',
					'price'         => '29,90',
					'price_details' => 'ou R$ 306,90 à vista',
					'features'      => "Cronograma semanal\nCorreção de redação\nSimulados no padrão ENEM",
					'trust_items'   => "Pagamento 100% seguro\nGarantia de 7 dias",
					'button_label'  => 'Garantir minha vaga',
					'button_url'    => array( 'url' => '#' ),
				),
			),
		),
		'elements'   => array(),
	);
}

$data = array(
	array(
		'id'       => 'tncontainer',
		'elType'   => 'container',
		'settings' => array(
			'html_tag'      => 'main',
			'content_width' => 'full',
		),
		'elements' => $elements,
	),
);

$slug     = 'checagem-tons-de-secao';
$existing = get_page_by_path( $slug, OBJECT, 'sales_page' );
$postarr  = array(
	'post_type'    => 'sales_page',
	'post_status'  => 'publish',
	'post_title'   => 'Checagem tons de secao',
	'post_name'    => $slug,
	'post_content' => '',
);

if ( $existing instanceof WP_Post ) {
	$postarr['ID']            = $existing->ID;
	$postarr['page_template'] = '';
	$page_id                  = wp_update_post( $postarr, true );
} else {
	$page_id = wp_insert_post( $postarr, true );
}

if ( is_wp_error( $page_id ) ) {
	proenem_tones_log( 'Falha ao criar a pagina: ' . $page_id->get_error_message() );
	return;
}

update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-post' );
update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
delete_post_meta( $page_id, '_elementor_css' );
delete_post_meta( $page_id, '_elementor_element_cache' );
delete_post_meta( $page_id, '_elementor_page_assets' );

proenem_tones_log( sprintf( '%d faixas em %s', count( $elements ), get_permalink( $page_id ) ) );
