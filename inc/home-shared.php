<?php
/**
 * Shared home surface helpers.
 *
 * The home is rendered by `page-templates/home.php` (control) and by the
 * conversion hero variants under `page-templates/home-variant-*.php`. These
 * helpers keep asset metadata and surface detection in one place so a variant
 * never drifts from the control on anything other than the hero.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the page templates that render the home experience.
 *
 * The control template comes first. Variants exist to be A/B tested against it
 * and are expected to be removed once a winner is promoted into the control.
 *
 * @return string[]
 */
function proenem_get_home_templates() {
	$templates = array(
		'page-templates/home.php',
		'page-templates/home-variant-oferta.php',
		'page-templates/home-variant-prova.php',
	);

	/**
	 * Filter the page templates treated as the home experience.
	 *
	 * @param string[] $templates Template paths relative to the theme root.
	 */
	return (array) apply_filters( 'proenem_home_templates', $templates );
}

/**
 * Check whether the current request renders a home surface.
 *
 * @return bool
 */
function proenem_is_home_surface() {
	if ( is_front_page() ) {
		return true;
	}

	foreach ( proenem_get_home_templates() as $template ) {
		if ( is_page_template( $template ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check whether the current request renders a conversion hero variant.
 *
 * @return bool
 */
function proenem_is_home_variant_surface() {
	foreach ( array_slice( proenem_get_home_templates(), 1 ) as $template ) {
		if ( is_page_template( $template ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get a home image URI.
 *
 * @param string $filename Image file name.
 * @return string
 */
function proenem_home_asset_uri( $filename ) {
	return PROENEM_THEME_URI . '/assets/images/home/' . $filename;
}

/**
 * Get a platform image URI.
 *
 * @param string $filename Image file name.
 * @return string
 */
function proenem_platform_asset_uri( $filename ) {
	return PROENEM_THEME_URI . '/assets/images/platform/' . $filename;
}

/**
 * Get intrinsic dimensions for home images.
 *
 * @return array<string,array{0:int,1:int}>
 */
function proenem_home_asset_dimensions() {
	return array(
		'Cancele-quando-voce-quiser.svg' => array( 234, 232 ),
		'Ellipse-fundo-price.svg'        => array( 133, 133 ),
		'blue_3_semi-spheres.svg'        => array( 253, 184 ),
		'check-verified-01.svg'          => array( 70, 70 ),
		'hero-student-720.webp'          => array( 720, 807 ),
		'hero-student-780.webp'          => array( 780, 874 ),
		'hero-student-820.webp'          => array( 820, 919 ),
		'hero-student.webp'              => array( 919, 1030 ),
		'pillar-diagnostico-280.webp'    => array( 280, 423 ),
		'pillar-diagnostico-320.webp'    => array( 320, 484 ),
		'pillar-diagnostico-360.webp'    => array( 360, 544 ),
		'pillar-diagnostico.webp'        => array( 407, 615 ),
		'pillar-execucao-280.webp'       => array( 280, 441 ),
		'pillar-execucao-320.webp'       => array( 320, 503 ),
		'pillar-execucao-360.webp'       => array( 360, 566 ),
		'pillar-execucao.webp'           => array( 382, 601 ),
		'pillar-meta-520.webp'           => array( 520, 282 ),
		'pillar-meta.webp'               => array( 760, 412 ),
		'price_vector_strokes.svg'       => array( 1440, 1418 ),
		'proof-logo-ufmg.png'            => array( 206, 88 ),
		'proof-logo-ufmg.webp'           => array( 206, 88 ),
		'proof-logo-ufrgs.png'           => array( 117, 94 ),
		'proof-logo-ufrgs.webp'          => array( 117, 94 ),
		'proof-logo-ufrj.png'            => array( 206, 102 ),
		'proof-logo-ufrj.webp'           => array( 206, 102 ),
		'proof-logo-unicamp.png'         => array( 99, 105 ),
		'proof-logo-unicamp.webp'        => array( 99, 105 ),
		'proof-logo-unifesp.png'         => array( 182, 110 ),
		'proof-logo-unifesp.webp'        => array( 182, 110 ),
		'proof-logo-usp.png'             => array( 171, 70 ),
		'proof-logo-usp.webp'            => array( 171, 70 ),
		'proof-students-1-240.webp'      => array( 240, 299 ),
		'proof-students-1-360.webp'      => array( 360, 448 ),
		'proof-students-1.webp'          => array( 470, 585 ),
		'proof-students-2-240.webp'      => array( 240, 352 ),
		'proof-students-2-360.webp'      => array( 360, 527 ),
		'proof-students-2.webp'          => array( 482, 706 ),
		'proof-students-3-240.webp'      => array( 240, 352 ),
		'proof-students-3-360.webp'      => array( 360, 527 ),
		'proof-students-3.webp'          => array( 482, 706 ),
		'proof-students-4-240.webp'      => array( 240, 352 ),
		'proof-students-4-360.webp'      => array( 360, 527 ),
		'proof-students-4.webp'          => array( 482, 706 ),
		'proof-students-5-240.webp'      => array( 240, 352 ),
		'proof-students-5-360.webp'      => array( 360, 527 ),
		'proof-students-5.webp'          => array( 482, 706 ),
		'proof-students-6-240.webp'      => array( 240, 359 ),
		'proof-students-6-360.webp'      => array( 360, 538 ),
		'proof-students-6.webp'          => array( 472, 706 ),
		'sticker_explore_por_dentro.svg' => array( 313, 119 ),
		'sticker_explore_questions.svg'  => array( 1393, 965 ),
		'student_school_1.webp'          => array( 1280, 1508 ),
		'student_school_2.webp'          => array( 568, 584 ),
	);
}

/**
 * Get the smaller variants published for each responsive home image.
 *
 * @return array<string,string[]>
 */
function proenem_home_responsive_images() {
	return array(
		'hero-student.webp'       => array( 'hero-student-720.webp', 'hero-student-780.webp', 'hero-student-820.webp' ),
		'pillar-diagnostico.webp' => array( 'pillar-diagnostico-280.webp', 'pillar-diagnostico-320.webp', 'pillar-diagnostico-360.webp' ),
		'pillar-execucao.webp'    => array( 'pillar-execucao-280.webp', 'pillar-execucao-320.webp', 'pillar-execucao-360.webp' ),
		'pillar-meta.webp'        => array( 'pillar-meta-520.webp' ),
		'proof-students-1.webp'   => array( 'proof-students-1-240.webp', 'proof-students-1-360.webp' ),
		'proof-students-2.webp'   => array( 'proof-students-2-240.webp', 'proof-students-2-360.webp' ),
		'proof-students-3.webp'   => array( 'proof-students-3-240.webp', 'proof-students-3-360.webp' ),
		'proof-students-4.webp'   => array( 'proof-students-4-240.webp', 'proof-students-4-360.webp' ),
		'proof-students-5.webp'   => array( 'proof-students-5-240.webp', 'proof-students-5-360.webp' ),
		'proof-students-6.webp'   => array( 'proof-students-6-240.webp', 'proof-students-6-360.webp' ),
	);
}

/**
 * Render intrinsic size and loading attributes for a home image.
 *
 * @param string $filename Image file name.
 * @param array  $args     Attribute overrides: decoding, loading and fetchpriority.
 * @return string
 */
function proenem_home_image_attributes( $filename, $args = array() ) {
	$dimensions = proenem_home_asset_dimensions();
	$attributes = array(
		'decoding' => $args['decoding'] ?? 'async',
		'loading'  => $args['loading'] ?? 'lazy',
	);

	if ( isset( $dimensions[ $filename ] ) ) {
		$attributes['width']  = (string) $dimensions[ $filename ][0];
		$attributes['height'] = (string) $dimensions[ $filename ][1];
	}

	if ( isset( $args['fetchpriority'] ) ) {
		$attributes['fetchpriority'] = $args['fetchpriority'];
	}

	$rendered_attributes = '';

	foreach ( $attributes as $name => $value ) {
		if ( '' === $value || null === $value ) {
			continue;
		}

		$rendered_attributes .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
	}

	return $rendered_attributes;
}

/**
 * Render the srcset and sizes attributes for a responsive home image.
 *
 * @param string $filename Image file name.
 * @param string $sizes    Sizes attribute value.
 * @return string
 */
function proenem_home_image_source_set( $filename, $sizes ) {
	$dimensions = proenem_home_asset_dimensions();
	$responsive = proenem_home_responsive_images();

	if ( ! isset( $dimensions[ $filename ], $responsive[ $filename ] ) ) {
		return '';
	}

	$sources = array();

	foreach ( $responsive[ $filename ] as $variant ) {
		if ( ! isset( $dimensions[ $variant ] ) ) {
			continue;
		}

		$sources[] = esc_url( proenem_home_asset_uri( $variant ) ) . ' ' . absint( $dimensions[ $variant ][0] ) . 'w';
	}

	$sources[] = esc_url( proenem_home_asset_uri( $filename ) ) . ' ' . absint( $dimensions[ $filename ][0] ) . 'w';

	return sprintf( ' srcset="%s" sizes="%s"', esc_attr( implode( ', ', $sources ) ), esc_attr( $sizes ) );
}

/**
 * Get the offer summary used by the conversion hero variants.
 *
 * The values mirror the Turma Intensiva plan card rendered further down the
 * page. Keeping them here avoids a variant advertising a price the pricing
 * section no longer shows.
 *
 * @return array<string,string>
 */
function proenem_get_home_offer() {
	$offer = array(
		'name'          => __( 'Turma Intensiva 2026', 'proenem-wordpress-theme' ),
		'price_prefix'  => __( '12x de', 'proenem-wordpress-theme' ),
		'price'         => __( 'R$ 29,90', 'proenem-wordpress-theme' ),
		'price_details' => __( 'ou R$ 306,90 à vista, com 6 meses de acesso', 'proenem-wordpress-theme' ),
		'guarantee'     => __( '7 dias de garantia', 'proenem-wordpress-theme' ),
		'checkout_url'  => proenem_get_home_cta_destination( 'method_pro' ),
		'plans_url'     => proenem_get_home_cta_destination( 'plans' ),
	);

	/**
	 * Filter the offer summary shown in the conversion hero variants.
	 *
	 * @param array<string,string> $offer Offer summary.
	 */
	return (array) apply_filters( 'proenem_home_offer', $offer );
}

/**
 * Get the approved-student universities shown as hero social proof.
 *
 * @return array<int,array{name:string,file:string}>
 */
function proenem_get_home_hero_universities() {
	return array(
		array(
			'name' => __( 'USP', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-usp.webp',
		),
		array(
			'name' => __( 'Unicamp', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-unicamp.webp',
		),
		array(
			'name' => __( 'UFRJ', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-ufrj.webp',
		),
		array(
			'name' => __( 'UFMG', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-ufmg.webp',
		),
		array(
			'name' => __( 'UFRGS', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-ufrgs.webp',
		),
		array(
			'name' => __( 'Unifesp', 'proenem-wordpress-theme' ),
			'file' => 'proof-logo-unifesp.webp',
		),
	);
}

/**
 * Get the student photos shown as the hero approval avatar row.
 *
 * @return array<int,string>
 */
function proenem_get_home_hero_avatars() {
	return array(
		'proof-students-1-240.webp',
		'proof-students-2-240.webp',
		'proof-students-3-240.webp',
		'proof-students-4-240.webp',
		'proof-students-5-240.webp',
	);
}

/**
 * Get the honest urgency line shown in the proof hero variant.
 *
 * The exam date is intentionally empty by default: an invented or outdated
 * countdown is worse for conversion than no countdown at all. Set it with the
 * filter once the official ENEM date is published.
 *
 * @return string Rendered urgency line, or an empty string when unavailable.
 */
function proenem_get_home_exam_countdown() {
	/**
	 * Filter the exam date used by the conversion hero countdown.
	 *
	 * @param string $exam_date Exam date as `YYYY-MM-DD`, or an empty string.
	 */
	$exam_date = (string) apply_filters( 'proenem_home_exam_date', '' );

	if ( '' === $exam_date ) {
		return '';
	}

	$exam_timestamp = strtotime( $exam_date . ' 00:00:00' );

	if ( ! $exam_timestamp ) {
		return '';
	}

	$days = (int) ceil( ( $exam_timestamp - (int) current_time( 'timestamp' ) ) / DAY_IN_SECONDS ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- Local time is what the countdown communicates.

	if ( $days < 1 ) {
		return '';
	}

	return sprintf(
		/* translators: %s: Number of days left until the exam. */
		_n( 'Falta %s dia para a prova', 'Faltam %s dias para a prova', $days, 'proenem-wordpress-theme' ),
		number_format_i18n( $days )
	);
}
