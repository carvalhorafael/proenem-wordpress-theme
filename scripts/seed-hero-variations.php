<?php
/**
 * Seed a page with every hero layout, to compare them side by side.
 *
 * O hero e a secao que mais pesa na conversao, e as opcoes dele mudam altura e
 * distancia ate a chamada. Ter as quatro combinacoes na mesma pagina deixa a
 * comparacao ser medida, e nao lembrada.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/seed-hero-variations.php
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'sales_page' ) ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( 'O CPT sales_page nao esta disponivel.' );
	}
	return;
}
$variacoes = array(
	array( 'id' => 'hv01', 'layout' => 'split', 'title' => "A reta final que vai transformar o seu estudo em aprovação.", 'eyebrow' => 'Dividida com cards' ),
	array( 'id' => 'hv02', 'layout' => 'compact', 'title' => "A reta final que vai transformar o seu estudo em aprovação.", 'eyebrow' => 'Compacta centralizada' ),
	array( 'id' => 'hv03', 'layout' => 'compact', 'title' => "Sua vaga na Turma Intensiva ENEM 2026.", 'eyebrow' => 'Compacta com preço',
		'price_prefix' => '12x de', 'price' => 'R$ 29,90', 'price_details' => 'ou R$ 306,90 à vista • Garantia de 7 dias' ),
	array( 'id' => 'hv04', 'layout' => 'split', 'title' => "A reta final que vai transformar o seu estudo em aprovação.", 'eyebrow' => 'Dividida com preço',
		'price_prefix' => '12x de', 'price' => 'R$ 29,90', 'price_details' => 'ou R$ 306,90 à vista' ),
);
$els = array();
foreach ( $variacoes as $v ) {
	$s = array(
		'layout' => $v['layout'], 'eyebrow' => $v['eyebrow'], 'title' => $v['title'],
		'body' => 'Você estuda muito, mas a nota não sobe? Na Turma Intensiva você recebe um plano pronto, correção de redação e a estratégia que já aprovou mais de 44 mil alunos.',
		'primary_label' => 'Quero minha vaga na Turma Intensiva', 'primary_url' => array( 'url' => '#oferta' ),
		'microcopy' => 'Vagas limitadas • Início hoje • Acesso imediato',
		'tone' => 'brand', 'anchor_id' => $v['id'],
		'proof_cards' => array(
			array( '_id' => 'a', 'label' => 'Cronograma pronto', 'value' => 'Semana 1 de 12' ),
			array( '_id' => 'b', 'label' => 'Redação 940', 'value' => 'Corrigida em 10 dias' ),
			array( '_id' => 'c', 'label' => 'Organização diária', 'value' => '4 de 5 tarefas hoje' ),
		),
	);
	foreach ( array( 'price_prefix', 'price', 'price_details' ) as $k ) {
		if ( isset( $v[ $k ] ) ) { $s[ $k ] = $v[ $k ]; }
	}
	$els[] = array( 'id' => $v['id'], 'elType' => 'widget', 'widgetType' => 'pro_offer_hero', 'settings' => $s, 'elements' => array() );
}
$data = array( array( 'id' => 'hvcont', 'elType' => 'container', 'settings' => array( 'html_tag' => 'main', 'content_width' => 'full' ), 'elements' => $els ) );
$slug = 'checagem-hero-variacoes';
$ex = get_page_by_path( $slug, OBJECT, 'sales_page' );
$arr = array( 'post_type' => 'sales_page', 'post_status' => 'publish', 'post_title' => 'Checagem hero variacoes', 'post_name' => $slug, 'post_content' => '' );
if ( $ex instanceof WP_Post ) { $arr['ID'] = $ex->ID; $arr['page_template'] = ''; $id = wp_update_post( $arr, true ); }
else { $id = wp_insert_post( $arr, true ); }
update_post_meta( $id, '_elementor_edit_mode', 'builder' );
update_post_meta( $id, '_elementor_template_type', 'wp-post' );
update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
foreach ( array( '_elementor_css', '_elementor_element_cache', '_elementor_page_assets' ) as $m ) { delete_post_meta( $id, $m ); }
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::log( count( $els ) . ' variacoes em ' . get_permalink( $id ) );
}
