<?php
/**
 * PROMOBLACK campaign home.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the PROMOBLACK page template path.
 *
 * @return string
 */
function proenem_get_promoblack_template() {
	return 'page-templates/home-promoblack.php';
}

/**
 * Register the PROMOBLACK template as a home surface.
 *
 * @param string[] $templates Home templates.
 * @return string[]
 */
function proenem_register_promoblack_home_template( $templates ) {
	$templates[] = proenem_get_promoblack_template();

	return $templates;
}
add_filter( 'proenem_home_templates', 'proenem_register_promoblack_home_template' );

/**
 * Get the editable PROMOBLACK settings with their defaults.
 *
 * @return array<string,array{label:string,description:string,type:string,default:string}>
 */
function proenem_get_promoblack_settings() {
	return array(
		'lot_deadline'          => array(
			'label'       => __( 'Fim do lote atual', 'proenem-wordpress-theme' ),
			'description' => __( 'Data e hora em que o lote atual fecha, no fuso do site. Formato AAAA-MM-DD HH:MM. Vazio ou já vencido esconde os contadores e a barra de lotes.', 'proenem-wordpress-theme' ),
			'type'        => 'datetime',
			'default'     => '',
		),
		'enem_pro_price'        => array(
			'label'       => __( 'ENEM Pro: preço do lote atual', 'proenem-wordpress-theme' ),
			'description' => __( 'Valor da parcela em 12x. Ex.: R$ 32,89. Vazio esconde a linha de preço.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => '',
		),
		'enem_pro_price_note'   => array(
			'label'       => __( 'ENEM Pro: complemento do preço', 'proenem-wordpress-theme' ),
			'description' => __( 'Ex.: ou R$ 394,68 à vista.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => '',
		),
		'enem_pro_checkout_url' => array(
			'label'       => __( 'ENEM Pro: link do checkout', 'proenem-wordpress-theme' ),
			'description' => __( 'Checkout da Hotmart com as UTMs da campanha. Vazio tira o botão do cartão do plano.', 'proenem-wordpress-theme' ),
			'type'        => 'url',
			'default'     => 'https://pay.hotmart.com/T102416176R?off=5na5b8bl&checkoutMode=10&utm_source=site-proenem&utm_medium=referral&utm_campaign=vendas_bf_promoblack&utm_content=home_planos_enempro2027&src=site%7Cvendas_bf_promoblack%7Corganico%7Chome_planos_enempro2027&sck=site_homePlanosPro',
		),
		'med_price'             => array(
			'label'       => __( 'Med Performance 2027: preço do lote atual', 'proenem-wordpress-theme' ),
			'description' => __( 'Valor da parcela em 12x. Ex.: R$ 59,89. Vazio esconde a linha de preço.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => '',
		),
		'med_price_note'        => array(
			'label'       => __( 'Med Performance 2027: complemento do preço', 'proenem-wordpress-theme' ),
			'description' => __( 'Ex.: ou R$ 718,68 à vista.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => '',
		),
		'med_checkout_url'      => array(
			'label'       => __( 'Med Performance 2027: link do checkout', 'proenem-wordpress-theme' ),
			'description' => __( 'Checkout da Hotmart com as UTMs da campanha. Vazio tira o botão do cartão do plano.', 'proenem-wordpress-theme' ),
			'type'        => 'url',
			'default'     => 'https://pay.hotmart.com/E108194423D?off=7rzl89vv&checkoutMode=10&utm_source=site-proenem&utm_medium=referral&utm_campaign=vendas_bf_promoblack&utm_content=home_planos_medperformance2027&src=site%7Cvendas_bf_promoblack%7Corganico%7Chome_planos_medperformance2027&sck=site_homePlanosMed',
		),
		'sisu_text'             => array(
			'label'       => __( 'Faixa da garantia SISU: texto', 'proenem-wordpress-theme' ),
			'description' => __( 'Vazio esconde a faixa. A faixa também só aparece com o texto do asterisco preenchido.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => __( 'Se você passar no SISU 2027, devolveremos o seu investimento', 'proenem-wordpress-theme' ),
		),
		'sisu_note'             => array(
			'label'       => __( 'Faixa da garantia SISU: texto do asterisco', 'proenem-wordpress-theme' ),
			'description' => __( 'Aparece no rodapé, ligado ao * da faixa. A faixa só aparece com este texto preenchido.', 'proenem-wordpress-theme' ),
			'type'        => 'text',
			'default'     => __( 'Leia o regulamento em', 'proenem-wordpress-theme' ),
		),
		'sisu_rules_url'        => array(
			'label'       => __( 'Faixa da garantia SISU: link do regulamento', 'proenem-wordpress-theme' ),
			'description' => __( 'Vai logo depois do texto do asterisco. Vazio mostra só o texto.', 'proenem-wordpress-theme' ),
			'type'        => 'url',
			'default'     => 'https://proenem.com.br/termos-de-uso/',
		),
	);
}

/**
 * Get the theme mod name of a PROMOBLACK setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function proenem_promoblack_setting_id( $key ) {
	return 'proenem_promoblack_' . $key;
}

/**
 * Sanitize a PROMOBLACK deadline.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function proenem_sanitize_promoblack_deadline( $value ) {
	if ( ! is_string( $value ) ) {
		return '';
	}

	$value = trim( str_replace( 'T', ' ', $value ) );

	if ( '' === $value ) {
		return '';
	}

	$value = substr( $value, 0, 16 );
	$date  = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $value, wp_timezone() );

	return $date && $date->format( 'Y-m-d H:i' ) === $value ? $value : '';
}

/**
 * Reject a PROMOBLACK deadline the sanitizer would discard.
 *
 * @param WP_Error $validity Validity object.
 * @param mixed    $value    Raw value.
 * @return WP_Error
 */
function proenem_validate_promoblack_deadline( $validity, $value ) {
	if ( is_string( $value ) && '' !== trim( $value ) && '' === proenem_sanitize_promoblack_deadline( $value ) ) {
		$validity->add( 'invalid_deadline', __( 'Data inválida. Use AAAA-MM-DD HH:MM, por exemplo 2026-11-12 23:59.', 'proenem-wordpress-theme' ) );
	}

	return $validity;
}

/**
 * Register the PROMOBLACK Customizer section.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function proenem_promoblack_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'proenem_promoblack_section',
		array(
			'title'       => esc_html__( 'PROMOBLACK', 'proenem-wordpress-theme' ),
			'description' => esc_html__( 'Lote, preços e checkout da home PROMOBLACK. Valem para a página que usa o modelo Proenem Home - PROMOBLACK.', 'proenem-wordpress-theme' ),
			'priority'    => 170,
		)
	);

	$sanitizers = array(
		'datetime' => 'proenem_sanitize_promoblack_deadline',
		'text'     => 'sanitize_text_field',
		'url'      => 'esc_url_raw',
	);

	foreach ( proenem_get_promoblack_settings() as $key => $setting ) {
		$id = proenem_promoblack_setting_id( $key );

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $setting['default'],
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => $sanitizers[ $setting['type'] ],
				'validate_callback' => 'datetime' === $setting['type'] ? 'proenem_validate_promoblack_deadline' : null,
				'transport'         => 'refresh',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'       => $setting['label'],
				'description' => $setting['description'],
				'section'     => 'proenem_promoblack_section',
				'settings'    => $id,
				'type'        => 'url' === $setting['type'] ? 'url' : 'text',
			)
		);
	}
}
add_action( 'customize_register', 'proenem_promoblack_customize_register' );

/**
 * Get a PROMOBLACK setting value.
 *
 * @param string $key Setting key.
 * @return string
 */
function proenem_get_promoblack_setting( $key ) {
	$settings = proenem_get_promoblack_settings();

	if ( ! isset( $settings[ $key ] ) ) {
		return '';
	}

	$value = get_theme_mod( proenem_promoblack_setting_id( $key ), $settings[ $key ]['default'] );

	return is_string( $value ) ? trim( $value ) : '';
}

/**
 * Get the current lot deadline as an ISO 8601 string.
 *
 * @return string
 */
function proenem_get_promoblack_deadline_iso() {
	$deadline = proenem_sanitize_promoblack_deadline( proenem_get_promoblack_setting( 'lot_deadline' ) );

	if ( '' === $deadline ) {
		return '';
	}

	$date = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $deadline, wp_timezone() );

	if ( ! $date || $date->getTimestamp() <= time() ) {
		return '';
	}

	return $date->format( DATE_ATOM );
}

/**
 * Get the current lot deadline formatted for reading.
 *
 * @return string
 */
function proenem_get_promoblack_deadline_label() {
	$deadline = proenem_get_promoblack_deadline_iso();

	if ( '' === $deadline ) {
		return '';
	}

	return wp_date( _x( 'd/m \à\s H\hi', 'PROMOBLACK lot deadline date format', 'proenem-wordpress-theme' ), strtotime( $deadline ) );
}

/**
 * Get the image sources of a PROMOBLACK responsive picture.
 *
 * @param string $name Image base name without extension.
 * @return array{desktop:string,mobile:string}
 */
function proenem_get_promoblack_picture( $name ) {
	return array(
		'desktop' => proenem_promoblack_asset_uri( $name . '.webp' ),
		'mobile'  => proenem_promoblack_asset_uri( $name . '-mobile.webp' ),
	);
}

/**
 * Get the PROMOBLACK plans.
 *
 * @return array<string,array<string,mixed>>
 */
function proenem_get_promoblack_plans() {
	$enem_pro_checkout = proenem_get_promoblack_setting( 'enem_pro_checkout_url' );
	$med_checkout      = proenem_get_promoblack_setting( 'med_checkout_url' );

	$plans = array(
		'enem-pro' => array(
			'name'        => __( 'ENEM Pro', 'proenem-wordpress-theme' ),
			'short_name'  => __( 'ENEM Pro', 'proenem-wordpress-theme' ),
			'name_accent' => '',
			'badge'       => '',
			'description' => __( 'Para estudar com direção, praticar no formato da prova e receber correção.', 'proenem-wordpress-theme' ),
			'price'       => proenem_get_promoblack_setting( 'enem_pro_price' ),
			'price_note'  => proenem_get_promoblack_setting( 'enem_pro_price_note' ),
			'cta'         => __( 'Quero o ENEM Pro', 'proenem-wordpress-theme' ),
			'url'         => $enem_pro_checkout,
			'featured'    => false,
			'items'       => array(
				__( 'Plano de estudos personalizado', 'proenem-wordpress-theme' ),
				__( 'Aulas gravadas', 'proenem-wordpress-theme' ),
				__( 'Mais de 50 mil questões', 'proenem-wordpress-theme' ),
				__( '10 simulados modelo Enem com correção TRI', 'proenem-wordpress-theme' ),
				__( 'Redação corrigida por especialistas da banca (2 créditos por mês)', 'proenem-wordpress-theme' ),
				__( 'Acesso à plataforma nova', 'proenem-wordpress-theme' ),
			),
		),
		'med'      => array(
			'name'        => __( 'Med Performance 2027', 'proenem-wordpress-theme' ),
			'short_name'  => __( 'Med Perf. 2027', 'proenem-wordpress-theme' ),
			'name_accent' => '2027',
			'badge'       => __( 'Mais completo', 'proenem-wordpress-theme' ),
			'description' => __( 'Tudo do ENEM Pro, com aulas ao vivo, mentoria e acompanhamento semanal da sua performance.', 'proenem-wordpress-theme' ),
			'price'       => proenem_get_promoblack_setting( 'med_price' ),
			'price_note'  => proenem_get_promoblack_setting( 'med_price_note' ),
			'cta'         => __( 'Quero o Med Performance 2027', 'proenem-wordpress-theme' ),
			'url'         => $med_checkout,
			'featured'    => true,
			'items'       => array(
				__( 'Tudo do ENEM Pro', 'proenem-wordpress-theme' ),
				__( 'Aulas ao vivo a partir de fev/2027', 'proenem-wordpress-theme' ),
				__( '10 + 4 simulados exclusivos TRI na Prática', 'proenem-wordpress-theme' ),
				__( 'Aulas mensais de TRI', 'proenem-wordpress-theme' ),
				__( 'Aula Vip TRI na Prática', 'proenem-wordpress-theme' ),
				__( 'Check-ups semanais com boletim de desempenho', 'proenem-wordpress-theme' ),
				__( 'Redação corrigida por especialistas da banca (4 créditos por mês)', 'proenem-wordpress-theme' ),
				__( 'Acesso à plataforma nova', 'proenem-wordpress-theme' ),
			),
		),
	);

	return (array) apply_filters( 'proenem_promoblack_plans', $plans );
}

/**
 * Get the PROMOBLACK benefit cards.
 *
 * @return array<int,array<string,string>>
 */
function proenem_get_promoblack_pillars() {
	return array(
		array(
			'number' => '01',
			'label'  => __( 'Conteúdo', 'proenem-wordpress-theme' ),
			'title'  => __( 'Aprenda com os melhores professores', 'proenem-wordpress-theme' ),
			'body'   => __( 'Aulas gravadas para estudar no seu ritmo e, a partir de fevereiro de 2027, aulas ao vivo de segunda a sexta. Conte também com plano personalizado e aprofundamentos em Matemática e Natureza.', 'proenem-wordpress-theme' ),
			'image'  => 'pillar-conteudo',
		),
		array(
			'number' => '02',
			'label'  => __( 'Performance', 'proenem-wordpress-theme' ),
			'title'  => __( 'Transforme conhecimento em estratégia', 'proenem-wordpress-theme' ),
			'body'   => __( 'Mais de 50 mil questões e 10 simulados modelo Enem com TRI. A partir de fevereiro de 2027, metodologia Arrase na TRI e Mentoria de Performance VIP para orientar sua preparação.', 'proenem-wordpress-theme' ),
			'image'  => 'pillar-performance',
		),
		array(
			'number' => '03',
			'label'  => __( 'Redação', 'proenem-wordpress-theme' ),
			'title'  => __( 'Entenda como melhorar seu próximo texto', 'proenem-wordpress-theme' ),
			'body'   => __( '4 correções de redação por mês para orientar seus próximos treinos. A partir de fevereiro de 2027, amplie seu repertório com a Comunidade de Redação.', 'proenem-wordpress-theme' ),
			'image'  => 'pillar-redacao',
		),
		array(
			'number' => '04',
			'label'  => __( 'Resultado', 'proenem-wordpress-theme' ),
			'title'  => __( 'Acompanhe sua evolução', 'proenem-wordpress-theme' ),
			'body'   => __( 'Check-ups semanais com boletim de desempenho para identificar seus avanços e os pontos que precisam de mais atenção na preparação.', 'proenem-wordpress-theme' ),
			'image'  => 'pillar-resultado',
		),
	);
}

/**
 * Get the PROMOBLACK plan comparison rows.
 *
 * @return array<int,array{title:string,rows:array<int,array{label:string,enem_pro:bool|string|array<string,string>,med:bool|string|array<string,string>}>}>
 */
function proenem_get_promoblack_comparison() {
	return array(
		array(
			'title' => __( 'Tudo do ENEM Pro — também no Med', 'proenem-wordpress-theme' ),
			'rows'  => array(
				array(
					'label'    => __( 'Aulas gravadas', 'proenem-wordpress-theme' ),
					'enem_pro' => __( '576 módulos', 'proenem-wordpress-theme' ),
					'med'      => __( '576 módulos', 'proenem-wordpress-theme' ),
				),
				array(
					'label'    => __( 'Curso de Nivelamento', 'proenem-wordpress-theme' ),
					'enem_pro' => true,
					'med'      => true,
				),
				array(
					'label'    => __( 'Plano de estudos personalizado', 'proenem-wordpress-theme' ),
					'enem_pro' => true,
					'med'      => true,
				),
				array(
					'label'    => __( 'Apostilas completas em PDF', 'proenem-wordpress-theme' ),
					'enem_pro' => __( 'todos os módulos', 'proenem-wordpress-theme' ),
					'med'      => __( 'todos os módulos', 'proenem-wordpress-theme' ),
				),
				array(
					'label'    => __( 'Banco com +50 mil questões', 'proenem-wordpress-theme' ),
					'enem_pro' => true,
					'med'      => true,
				),
				array(
					'label'    => __( 'Simulados modelo ENEM com TRI', 'proenem-wordpress-theme' ),
					'enem_pro' => '10',
					'med'      => '10',
				),
				array(
					'label'    => __( 'Redação corrigida por banca', 'proenem-wordpress-theme' ),
					'enem_pro' => __( '2 créditos/mês', 'proenem-wordpress-theme' ),
					'med'      => __( '4 créditos/mês', 'proenem-wordpress-theme' ),
				),
				array(
					'label'    => __( 'Acesso à plataforma nova', 'proenem-wordpress-theme' ),
					'enem_pro' => true,
					'med'      => true,
				),
			),
		),
		array(
			'title' => __( '+ Exclusivo do Med Performance', 'proenem-wordpress-theme' ),
			'rows'  => array(
				array(
					'label'    => __( 'Aulas ao vivo', 'proenem-wordpress-theme' ),
					'enem_pro' => false,
					'med'      => array( 'note' => __( 'a partir de fev/2027', 'proenem-wordpress-theme' ) ),
				),
				array(
					'label'    => __( 'Aula exclusiva TRI na Prática', 'proenem-wordpress-theme' ),
					'enem_pro' => false,
					'med'      => __( 'mensal', 'proenem-wordpress-theme' ),
				),
				array(
					'label'    => __( 'Simulados TRI na Prática', 'proenem-wordpress-theme' ),
					'enem_pro' => false,
					'med'      => '4',
				),
				array(
					'label'    => __( 'Check-ups semanais com boletim', 'proenem-wordpress-theme' ),
					'enem_pro' => false,
					'med'      => true,
				),
				array(
					'label'    => __( 'EscreveAI: tutor de redação', 'proenem-wordpress-theme' ),
					'enem_pro' => false,
					'med'      => true,
				),
			),
		),
	);
}

/**
 * Get the PROMOBLACK FAQ.
 *
 * @return array<int,array{question:string,answer:string}>
 */
function proenem_get_promoblack_faq() {
	return array(
		array(
			'question' => __( 'Qual a diferença entre o ENEM Pro e o Med Performance 2027?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'O ENEM Pro reúne plano de estudos, aulas gravadas, questões, simulados com TRI e redação corrigida. O Med Performance 2027 tem tudo isso e soma aulas ao vivo, simulados exclusivos, mentoria VIP em TRI e check-ups semanais com boletim.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'O que são os simulados com TRI?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'TRI é a Teoria de Resposta ao Item, o método de correção do Enem. A nota considera quais questões você acertou e o nível de cada uma, não só a quantidade de acertos. Nos simulados do ProEnem você treina com essa correção.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Como funciona a correção de redação?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'Sua redação é corrigida por especialistas da banca do Enem, com base nas cinco competências do Enem. Você tem créditos mensais de correção conforme a turma escolhida.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Quando começam as aulas ao vivo do Med Performance 2027?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'A partir de fevereiro de 2027.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Posso parcelar?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'Sim, em até 12x.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Por quanto tempo tenho acesso?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'Enem Pro - 12 meses / Med Performance até dez 2027', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Como recebo o acesso depois de comprar?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'A compra é feita pela Hotmart e as instruções de acesso chegam no e-mail usado na compra.', 'proenem-wordpress-theme' ),
		),
		array(
			'question' => __( 'Até quando vale o valor atual?', 'proenem-wordpress-theme' ),
			'answer'   => __( 'O valor vale até o fim do lote vigente, indicado no contador da página. Quando o lote vira, o valor muda.', 'proenem-wordpress-theme' ),
		),
	);
}

/**
 * Get the URI of a PROMOBLACK image.
 *
 * @param string $filename Image file name.
 * @return string
 */
function proenem_promoblack_asset_uri( $filename ) {
	return PROENEM_THEME_URI . '/assets/images/promoblack/' . ltrim( $filename, '/' );
}

/**
 * Render a PROMOBLACK lot countdown.
 *
 * @param string $modifier BEM modifier for the countdown size.
 * @return void
 */
function proenem_render_promoblack_countdown( $modifier = '' ) {
	$deadline = proenem_get_promoblack_deadline_iso();

	if ( '' === $deadline ) {
		return;
	}

	$classes = 'pro-pb-countdown' . ( '' !== $modifier ? ' pro-pb-countdown--' . $modifier : '' );
	$units   = array(
		'days'    => __( 'dias', 'proenem-wordpress-theme' ),
		'hours'   => __( 'horas', 'proenem-wordpress-theme' ),
		'minutes' => __( 'minutos', 'proenem-wordpress-theme' ),
		'seconds' => __( 'segundos', 'proenem-wordpress-theme' ),
	);
	?>
	<time
		class="<?php echo esc_attr( $classes ); ?>"
		datetime="<?php echo esc_attr( $deadline ); ?>"
		data-pro-countdown="<?php echo esc_attr( $deadline ); ?>"
		data-pro-countdown-expired="<?php esc_attr_e( 'Lote encerrado', 'proenem-wordpress-theme' ); ?>"
	>
		<span class="pro-pb-countdown__fallback" data-pro-countdown-fallback>
			<?php
			printf(
				/* translators: %s: Lot closing date and time. */
				esc_html__( 'Até %s', 'proenem-wordpress-theme' ),
				esc_html( wp_date( 'd/m H\hi', strtotime( $deadline ) ) )
			);
			?>
		</span>
		<span class="pro-pb-countdown__units" data-pro-countdown-units hidden>
			<?php
			$index = 0;
			foreach ( $units as $unit => $label ) :
				if ( $index > 0 ) :
					?>
					<span class="pro-pb-countdown__separator" aria-hidden="true">:</span>
					<?php
				endif;
				++$index;
				?>
				<span class="pro-pb-countdown__unit">
					<strong data-pro-countdown-<?php echo esc_attr( $unit ); ?>>00</strong>
					<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
				</span>
			<?php endforeach; ?>
		</span>
	</time>
	<?php
}

/**
 * Get the PROMOBLACK section copy.
 *
 * @return array<string,string>
 */
function proenem_get_promoblack_copy() {
	return array(
		'plans_subtitle'   => __( 'Dois caminhos para o mesmo objetivo: estudar com método e acompanhar a sua performance até o Enem 2027.', 'proenem-wordpress-theme' ),
		'lots_note'        => __( 'Os lotes viram por data. Quando o lote vira, o valor muda.', 'proenem-wordpress-theme' ),
		'compare_subtitle' => __( 'O ENEM Pro tem tudo para estudar com método. O Med Performance 2027 soma aulas ao vivo, mentoria e acompanhamento semanal para quem quer ir além.', 'proenem-wordpress-theme' ),
		'compare_caption'  => __( 'O que cada plano inclui:', 'proenem-wordpress-theme' ),
		'final_body'       => __( 'Escolha o seu plano e comece a estudar para o Enem 2027 com método.', 'proenem-wordpress-theme' ),
		'legal'            => __( 'Valores do lote vigente, sujeitos a alteração. Pagamento processado pela Hotmart. Os resultados dependem da dedicação de cada aluno.', 'proenem-wordpress-theme' ),
	);
}
