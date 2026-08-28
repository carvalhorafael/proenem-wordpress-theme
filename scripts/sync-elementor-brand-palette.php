<?php
/**
 * Write the Proenem brand palette into the active Elementor kit.
 *
 * Elementor global colors are content, not theme code, so this runs as an
 * explicit operation instead of a filter that would fight the editor. It is
 * idempotent: running it again rewrites the same palette.
 *
 * Run with:
 * wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-elementor-brand-palette.php
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
function proenem_palette_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

/**
 * The published brand colors, resolved to literal hex values.
 *
 * The Elementor kit cannot consume CSS custom properties, so the hex values are
 * mirrored here from the design system tokens.
 *
 * @return array<string,array{title:string,color:string}>
 */
function proenem_brand_palette_colors() {
	return array(
		'proenem_red'    => array(
			'title' => 'Vermelho da marca',
			'color' => '#BB0922',
		),
		'ink'            => array(
			'title' => 'Tinta',
			'color' => '#1A1A1A',
		),
		'yellow_brand'   => array(
			'title' => 'Amarelo da marca',
			'color' => '#F9C200',
		),
		'yellow_bright'  => array(
			'title' => 'Amarelo vivo',
			'color' => '#FFD600',
		),
		'canvas'         => array(
			'title' => 'Fundo',
			'color' => '#FEF2F2',
		),
		'canvas_white'   => array(
			'title' => 'Superfície',
			'color' => '#FFFFFF',
		),
		'purple_lp'      => array(
			'title' => 'Roxo',
			'color' => '#4D17F5',
		),
		'purple'         => array(
			'title' => 'Roxo claro',
			'color' => '#8952FD',
		),
		'pink_hot'       => array(
			'title' => 'Rosa',
			'color' => '#FF2D87',
		),
		'pink_lp'        => array(
			'title' => 'Rosa claro',
			'color' => '#FF90E8',
		),
		'mint'           => array(
			'title' => 'Verde',
			'color' => '#06D6A0',
		),
		'teal'           => array(
			'title' => 'Azul esverdeado',
			'color' => '#00B4A6',
		),
		'cyan'           => array(
			'title' => 'Azul',
			'color' => '#00BFFF',
		),
		'orange'         => array(
			'title' => 'Laranja',
			'color' => '#FF7051',
		),
	);
}

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	proenem_palette_log( 'Elementor nao esta ativo.' );
	return;
}

$kit_id = (int) get_option( 'elementor_active_kit' );

if ( $kit_id <= 0 || 'not-found' === get_post_status( $kit_id ) ) {
	proenem_palette_log( 'Nenhum kit ativo do Elementor foi encontrado.' );
	return;
}

$palette = proenem_brand_palette_colors();

// The four system colors have fixed ids in Elementor and cannot be renamed away.
$system_colors = array(
	array(
		'_id'   => 'primary',
		'title' => $palette['proenem_red']['title'],
		'color' => $palette['proenem_red']['color'],
	),
	array(
		'_id'   => 'secondary',
		'title' => $palette['ink']['title'],
		'color' => $palette['ink']['color'],
	),
	array(
		'_id'   => 'text',
		'title' => $palette['ink']['title'],
		'color' => $palette['ink']['color'],
	),
	array(
		'_id'   => 'accent',
		'title' => $palette['yellow_brand']['title'],
		'color' => $palette['yellow_brand']['color'],
	),
);

$system_keys   = array( 'proenem_red', 'ink', 'yellow_brand' );
$custom_colors = array();

foreach ( $palette as $key => $color ) {
	if ( in_array( $key, $system_keys, true ) ) {
		continue;
	}

	$custom_colors[] = array(
		'_id'   => 'proenem_' . $key,
		'title' => $color['title'],
		'color' => $color['color'],
	);
}

$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
$settings = is_array( $settings ) ? $settings : array();

$settings['system_colors'] = $system_colors;
$settings['custom_colors'] = $custom_colors;

update_post_meta( $kit_id, '_elementor_page_settings', $settings );

// Global colors are compiled into the kit CSS, so the cache has to be dropped.
\Elementor\Plugin::$instance->files_manager->clear_cache();

proenem_palette_log(
	sprintf(
		'Paleta gravada no kit %d: %d cores de sistema e %d cores personalizadas.',
		$kit_id,
		count( $system_colors ),
		count( $custom_colors )
	)
);
proenem_palette_log( 'Atencao: Roxo claro (#8952FD) nao alcanca AA para texto normal com tinta nem com branco. Use apenas como elemento decorativo.' );
