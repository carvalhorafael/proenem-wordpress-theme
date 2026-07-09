<?php
/**
 * Elementor home section widgets.
 *
 * @package Proenem
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound, WordPress.Files.FileName.InvalidClassFileName, Squiz.Commenting.FunctionComment.Missing -- Elementor section widgets share helpers and defaults.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared base for Proenem home Elementor widgets.
 */
abstract class Proenem_Elementor_Home_Widget_Base extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'proenem-sales' );
	}

	/**
	 * Get shared widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'home', 'lp', 'enem' );
	}

	/**
	 * Build the URL for a bundled home asset.
	 *
	 * @param string $filename Asset filename.
	 * @return string
	 */
	protected function home_asset_uri( $filename ) {
		return PROENEM_THEME_URI . '/assets/images/home/' . ltrim( (string) $filename, '/' );
	}

	/**
	 * Get a media control URL or fallback asset URL.
	 *
	 * @param array  $media Media setting.
	 * @param string $fallback_filename Fallback home asset filename.
	 * @return string
	 */
	protected function media_url( $media, $fallback_filename = '' ) {
		if ( is_array( $media ) && ! empty( $media['url'] ) ) {
			$url = (string) $media['url'];

			if ( ! $this->is_elementor_placeholder_url( $url ) ) {
				return $url;
			}
		}

		return $fallback_filename ? $this->home_asset_uri( $fallback_filename ) : '';
	}

	/**
	 * Check whether a media control URL is Elementor's placeholder image.
	 *
	 * @param string $url Media URL.
	 * @return bool
	 */
	protected function is_elementor_placeholder_url( $url ) {
		return false !== strpos( $url, '/elementor/assets/images/placeholder' );
	}

	/**
	 * Wrap a home section so scoped home styles work inside Elementor pages.
	 *
	 * @return void
	 */
	protected function open_home_wrapper() {
		echo '<div class="pro-home pro-home-elementor-section">';
	}

	/**
	 * Close the home wrapper.
	 *
	 * @return void
	 */
	protected function close_home_wrapper() {
		echo '</div>';
	}

	/**
	 * Add a text control.
	 *
	 * @param string $name Control name.
	 * @param string $label Control label.
	 * @param string $default_value Default value.
	 * @return void
	 */
	protected function add_text_control( $name, $label, $default_value = '' ) {
		$this->add_control(
			$name,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $default_value,
				'label_block' => true,
			)
		);
	}

	/**
	 * Add a textarea control.
	 *
	 * @param string $name Control name.
	 * @param string $label Control label.
	 * @param string $default_value Default value.
	 * @return void
	 */
	protected function add_textarea_control( $name, $label, $default_value = '' ) {
		$this->add_control(
			$name,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => $default_value,
				'label_block' => true,
			)
		);
	}

	/**
	 * Add a URL control with a default hash link.
	 *
	 * @param string $name Control name.
	 * @param string $label Control label.
	 * @param string $default_value Default URL.
	 * @return void
	 */
	protected function add_url_control( $name, $label, $default_value = '' ) {
		$this->add_control(
			$name,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array(
					'url' => $default_value,
				),
				'label_block' => true,
			)
		);
	}

	/**
	 * Add a media control with a theme asset fallback.
	 *
	 * @param string $name Control name.
	 * @param string $label Control label.
	 * @param string $filename Default asset filename.
	 * @return void
	 */
	protected function add_home_media_control( $name, $label, $filename ) {
		$this->add_control(
			$name,
			array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => $this->home_asset_uri( $filename ),
				),
			)
		);
	}

	/**
	 * Render a button using Elementor link settings.
	 *
	 * @param string $key Attribute key.
	 * @param array  $link Link settings.
	 * @param string $label Button label.
	 * @param string $class_name Button class.
	 * @param string $badge Optional badge.
	 * @return void
	 */
	protected function render_home_button( $key, $link, $label, $class_name, $badge = '' ) {
		if ( '' === trim( (string) $label ) ) {
			return;
		}

		$this->add_render_attribute( $key, 'class', $class_name );

		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		}

		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<?php echo esc_html( $label ); ?>
			<?php if ( $badge ) : ?>
				<span class="pen-button__badge"><?php echo esc_html( $badge ); ?></span>
			<?php else : ?>
				<span class="pen-button__arrow" aria-hidden="true">-&gt;</span>
			<?php endif; ?>
		</a>
		<?php
	}

	/**
	 * Render a small inline icon used by home cards.
	 *
	 * @param string $icon Icon slug.
	 * @return string
	 */
	protected function platform_icon_svg( $icon ) {
		$icons = array(
			'clock' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v5l3 2"></path></svg>',
			'book'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 5.5h7a4 4 0 0 1 4 4v9a4 4 0 0 0-4-4H5z"></path><path d="M19 5.5h-3a4 4 0 0 0-4 4"></path><path d="M19 5.5v11.7"></path><path d="m16 15 1.5-1.5L19 15"></path></svg>',
			'brain' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M9 5.3a3.2 3.2 0 0 0-4 3.1 3 3 0 0 0 .8 5.8 3 3 0 0 0 3.8 4.3"></path><path d="M15 5.3a3.2 3.2 0 0 1 4 3.1 3 3 0 0 1-.8 5.8 3 3 0 0 1-3.8 4.3"></path><path d="M9 5.3v13.2"></path><path d="M15 5.3v13.2"></path><path d="M9 9.2H7.2"></path><path d="M15 9.2h1.8"></path><path d="M9 14.2H7.2"></path><path d="M15 14.2h1.8"></path></svg>',
			'robot' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><rect x="6" y="8" width="12" height="10" rx="2"></rect><path d="M12 5v3"></path><path d="M9 12h.01"></path><path d="M15 12h.01"></path><path d="M9 16h6"></path><path d="M4 12h2"></path><path d="M18 12h2"></path></svg>',
			'edit'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19h14"></path><path d="m7 16 1-4 7.5-7.5a2.1 2.1 0 0 1 3 3L11 15z"></path></svg>',
			'chart' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19V5"></path><path d="M5 19h14"></path><path d="m8 15 3-3 2 2 4-6"></path></svg>',
		);

		return $icons[ $icon ] ?? $icons['clock'];
	}

	/**
	 * Render a subject icon.
	 *
	 * @param string $icon Icon slug.
	 * @return string
	 */
	protected function subject_icon_svg( $icon ) {
		$icons = array(
			'chemistry'  => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M9 3h6"></path><path d="M10 3v5l-5.2 9.2A2.6 2.6 0 0 0 7 21h10a2.6 2.6 0 0 0 2.2-3.8L14 8V3"></path><path d="M7.5 16h9"></path></svg>',
			'biology'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19c9.5 0 14-4.5 14-14C9.5 5 5 9.5 5 19z"></path><path d="M5 19 15 9"></path></svg>',
			'math'       => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M8 7h8"></path><path d="M8 11h2"></path><path d="M14 11h2"></path><path d="M8 15h2"></path><path d="M14 15h2"></path></svg>',
			'history'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M4 9h16"></path><path d="m5 9 7-5 7 5"></path><path d="M6 9v9"></path><path d="M10 9v9"></path><path d="M14 9v9"></path><path d="M18 9v9"></path><path d="M4 18h16"></path></svg>',
			'english'    => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 5h14v10H8l-3 3z"></path><path d="M8 9h8"></path><path d="M8 12h5"></path></svg>',
			'portuguese' => '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 19h14"></path><path d="m7 16 1-4 7.5-7.5a2.1 2.1 0 0 1 3 3L11 15z"></path></svg>',
		);

		return $icons[ $icon ] ?? $icons['portuguese'];
	}
}

/**
 * Pro home hero widget.
 */
class Proenem_Elementor_Home_Hero_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_hero';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Hero', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-image-rollover';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_home_media_control( 'image', esc_html__( 'Imagem principal', 'proenem-wordpress-theme' ), 'hero-student.webp' );
		$this->add_text_control( 'sticker_1', esc_html__( 'Sticker', 'proenem-wordpress-theme' ), esc_html__( 'Diagnóstico', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'sticker_2', esc_html__( 'Sticker', 'proenem-wordpress-theme' ), esc_html__( 'Performance', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'sticker_3', esc_html__( 'Sticker', 'proenem-wordpress-theme' ), esc_html__( 'Meta', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'sticker_4', esc_html__( 'Sticker', 'proenem-wordpress-theme' ), esc_html__( 'Execução', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_1', esc_html__( 'Linha 1', 'proenem-wordpress-theme' ), esc_html__( 'Sua', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_2', esc_html__( 'Linha 2', 'proenem-wordpress-theme' ), esc_html__( 'aprovação', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_3_prefix', esc_html__( 'Linha 3 antes do destaque', 'proenem-wordpress-theme' ), esc_html__( 'não é', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_3_emphasis', esc_html__( 'Linha 3 destaque', 'proenem-wordpress-theme' ), esc_html__( 'sorte', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_4_prefix', esc_html__( 'Linha 4 antes do destaque', 'proenem-wordpress-theme' ), esc_html__( 'é', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'line_4_emphasis', esc_html__( 'Linha 4 destaque', 'proenem-wordpress-theme' ), esc_html__( 'método', 'proenem-wordpress-theme' ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section class="pen-hero-section" aria-labelledby="pro-home-title">
			<div class="pen-hero-section__stage">
				<img class="pen-hero-section__image" src="<?php echo esc_url( $this->media_url( $settings['image'] ?? array(), 'hero-student.webp' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com cadernos nas mãos.', 'proenem-wordpress-theme' ); ?>">
				<span class="pen-hero-sticker pen-hero-sticker--pink"><?php echo esc_html( $settings['sticker_1'] ?? '' ); ?></span>
				<span class="pen-hero-sticker pen-hero-sticker--yellow"><?php echo esc_html( $settings['sticker_2'] ?? '' ); ?></span>
				<span class="pen-hero-sticker pen-hero-sticker--green"><?php echo esc_html( $settings['sticker_3'] ?? '' ); ?></span>
				<span class="pen-hero-sticker pen-hero-sticker--orange"><?php echo esc_html( $settings['sticker_4'] ?? '' ); ?></span>
				<h1 id="pro-home-title" class="pen-hero-section__title">
					<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php echo esc_html( $settings['line_1'] ?? '' ); ?></span>
					<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php echo esc_html( $settings['line_2'] ?? '' ); ?></span>
					<span class="pen-hero-section__title-line pen-hero-section__title-line--center"><?php echo esc_html( $settings['line_3_prefix'] ?? '' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--blue"><?php echo esc_html( $settings['line_3_emphasis'] ?? '' ); ?></strong></span>
					<span class="pen-hero-section__title-line pen-hero-section__title-line--right"><?php echo esc_html( $settings['line_4_prefix'] ?? '' ); ?> <strong class="pen-hero-section__emphasis pen-hero-section__emphasis--stroke pen-hero-section__emphasis--yellow"><?php echo esc_html( $settings['line_4_emphasis'] ?? '' ); ?></strong></span>
				</h1>
			</div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home action bar widget.
 */
class Proenem_Elementor_Home_Action_Bar_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_action_bar';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Barra de Ação', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-call-to-action';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'strong_text', esc_html__( 'Texto em destaque', 'proenem-wordpress-theme' ), esc_html__( 'O Método PRO', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( 'não é apenas um cronograma, é uma engenharia de resultados dividida em 4 pilares: Meta. Diagnóstico. Execução. Performance.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão', 'proenem-wordpress-theme' ), esc_html__( 'Conheça o Método PRO', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link do botão', 'proenem-wordpress-theme' ), '#metodo' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<aside class="pen-hero-action-bar">
			<p><strong><?php echo esc_html( $settings['strong_text'] ?? '' ); ?></strong> <?php echo esc_html( $settings['body'] ?? '' ); ?></p>
			<div class="pen-hero-action-bar__action">
				<?php $this->render_home_button( 'button_url', $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-button pen-button--secondary pen-button--md' ); ?>
			</div>
		</aside>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home marquee widget.
 */
class Proenem_Elementor_Home_Marquee_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_marquee';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Marquee', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-animation-text';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Frases', 'proenem-wordpress-theme' ) ) );
		$this->add_textarea_control( 'items', esc_html__( 'Frases, uma por linha', 'proenem-wordpress-theme' ), esc_html__( "A engenharia da sua aprovação\nTroque a sorte pela estratégia\nConheça o Método PRO", 'proenem-wordpress-theme' ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$items = $this->split_lines( $this->get_settings_for_display()['items'] ?? '' );
		$this->open_home_wrapper();
		?>
		<div class="pen-marquee" aria-hidden="true">
			<div class="pen-marquee__track">
				<?php for ( $iteration = 0; $iteration < 2; $iteration++ ) : ?>
					<?php foreach ( $items as $item ) : ?>
						<span><?php echo esc_html( $item ); ?></span>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home pillars widget.
 */
class Proenem_Elementor_Home_Pillars_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_pillars';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Pilares', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'eyebrow', esc_html__( 'Selo', 'proenem-wordpress-theme' ), esc_html__( 'Método Pro', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title', esc_html__( 'Título', 'proenem-wordpress-theme' ), esc_html__( 'Os 4 pilares que organizam a sua aprovação', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body_1', esc_html__( 'Texto 1', 'proenem-wordpress-theme' ), esc_html__( 'O Método PRO não é apenas um cronograma, é uma engenharia de resultados dividida em 4 pilares: Meta. Diagnóstico. Execução. Performance.', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body_2', esc_html__( 'Texto 2', 'proenem-wordpress-theme' ), esc_html__( 'O ENEM não é prova de quem estuda mais. É de quem estuda com estratégia.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão', 'proenem-wordpress-theme' ), esc_html__( 'Começar gratuitamente', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link do botão', 'proenem-wordpress-theme' ), '#planos' );

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'number',
			array(
				'label'   => esc_html__( 'Número', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '01',
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Meta', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'body',
			array(
				'label' => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$repeater->add_control(
			'tone',
			array(
				'label'   => esc_html__( 'Tom visual', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'blue',
				'options' => array(
					'blue'     => 'Blue',
					'featured' => 'Featured',
					'red'      => 'Red',
					'pink'     => 'Pink',
				),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Pilares', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'number' => '01',
						'title'  => esc_html__( 'Meta', 'proenem-wordpress-theme' ),
						'body'   => esc_html__( 'Transformamos objetivo em plano de estudo claro, com prioridades e metas semanais.', 'proenem-wordpress-theme' ),
						'tone'   => 'blue',
						'image'  => array( 'url' => $this->home_asset_uri( 'pillar-meta.webp' ) ),
					),
					array(
						'number' => '02',
						'title'  => esc_html__( 'Diagnóstico', 'proenem-wordpress-theme' ),
						'body'   => esc_html__( 'Mapeamos suas forças e lacunas com simulados adaptativos. Você vê exatamente onde está.', 'proenem-wordpress-theme' ),
						'tone'   => 'featured',
						'image'  => array( 'url' => $this->home_asset_uri( 'pillar-diagnostico.webp' ) ),
					),
					array(
						'number' => '03',
						'title'  => esc_html__( 'Execução', 'proenem-wordpress-theme' ),
						'body'   => esc_html__( 'Você sabe o que estudar, quando revisar e como corrigir rota sem perder ritmo.', 'proenem-wordpress-theme' ),
						'tone'   => 'red',
						'image'  => array( 'url' => $this->home_asset_uri( 'pillar-execucao.webp' ) ),
					),
					array(
						'number' => '04',
						'title'  => esc_html__( 'Performance', 'proenem-wordpress-theme' ),
						'body'   => esc_html__( 'Acompanhamos sua evolução para trocar esforço solto por resultado mensurável.', 'proenem-wordpress-theme' ),
						'tone'   => 'pink',
						'image'  => array( 'url' => $this->home_asset_uri( 'pillar-meta.webp' ) ),
					),
				),
				'title_field' => '{{{ number }}} - {{{ title }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$cards    = is_array( $settings['cards'] ?? null ) ? $settings['cards'] : array();
		$images   = array(
			'pillar-meta.webp',
			'pillar-diagnostico.webp',
			'pillar-execucao.webp',
			'pillar-meta.webp',
		);
		$this->open_home_wrapper();
		?>
		<section id="metodo" class="pen-pillars-section" aria-labelledby="pro-pillars-title">
			<div class="pen-pillars-section__copy">
				<p class="pen-section-pill"><?php echo esc_html( $settings['eyebrow'] ?? '' ); ?></p>
				<h2 id="pro-pillars-title"><?php echo esc_html( $settings['title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $settings['body_1'] ?? '' ); ?></p>
				<p><?php echo esc_html( $settings['body_2'] ?? '' ); ?></p>
				<?php $this->render_home_button( 'button_url', $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-button pen-button--primary pen-button--md' ); ?>
			</div>
			<div class="pen-pillars-section__cards" data-pro-home-pillars-slider>
				<div class="pro-home-pillars-badge" aria-hidden="true"></div>
				<div class="pro-home-pillars-control" aria-label="<?php esc_attr_e( 'Navegação dos pilares', 'proenem-wordpress-theme' ); ?>">
					<button type="button" data-pro-home-pillars-prev aria-label="<?php esc_attr_e( 'Pilar anterior', 'proenem-wordpress-theme' ); ?>">‹</button><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><button type="button" data-pro-home-pillars-next aria-label="<?php esc_attr_e( 'Próximo pilar', 'proenem-wordpress-theme' ); ?>">›</button>
				</div>
				<?php foreach ( $cards as $index => $card ) : ?>
					<?php $tone = in_array( $card['tone'] ?? '', array( 'blue', 'featured', 'red', 'pink' ), true ) ? $card['tone'] : 'blue'; ?>
					<article class="pen-step-card pen-step-card--<?php echo esc_attr( $tone ); ?><?php echo 'featured' === $tone ? ' is-active' : ''; ?>" data-pro-home-pillar-card>
						<img class="pen-step-card__image" src="<?php echo esc_url( $this->media_url( $card['image'] ?? array(), $images[ $index ] ?? 'pillar-meta.webp' ) ); ?>" alt="">
						<span><?php echo esc_html( $card['number'] ?? str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div><h3><?php echo esc_html( $card['title'] ?? '' ); ?></h3><p><?php echo esc_html( $card['body'] ?? '' ); ?></p></div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home proof widget.
 */
class Proenem_Elementor_Home_Proof_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_proof';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Prova Social', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial-carousel';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'badge_line_1', esc_html__( 'Badge linha 1', 'proenem-wordpress-theme' ), esc_html__( 'Nossos', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'badge_line_2', esc_html__( 'Badge linha 2', 'proenem-wordpress-theme' ), esc_html__( 'Alunos!', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title', esc_html__( 'Título', 'proenem-wordpress-theme' ), esc_html__( '+ de 40.000 aprovados em universidades públicas', 'proenem-wordpress-theme' ) );

		$students = new \Elementor\Repeater();
		$students->add_control(
			'image',
			array(
				'label' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'student_images',
			array(
				'label'       => esc_html__( 'Imagens de alunos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $students->get_controls(),
				'default'     => array_map(
					fn( $file ) => array( 'image' => array( 'url' => $this->home_asset_uri( $file ) ) ),
					array( 'proof-students-1.webp', 'proof-students-2.png', 'proof-students-3.png', 'proof-students-4.png', 'proof-students-5.png', 'proof-students-6.png' )
				),
				'title_field' => esc_html__( 'Aluno', 'proenem-wordpress-theme' ),
			)
		);

		$logos = new \Elementor\Repeater();
		$logos->add_control(
			'name',
			array(
				'label' => esc_html__( 'Nome', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$logos->add_control(
			'image',
			array(
				'label' => esc_html__( 'Logo', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'logos',
			array(
				'label'       => esc_html__( 'Logos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $logos->get_controls(),
				'default'     => array(
					array(
						'name'  => 'UFRJ',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-ufrj.png' ) ),
					),
					array(
						'name'  => 'UFRGS',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-ufrgs.png' ) ),
					),
					array(
						'name'  => 'Unicamp',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-unicamp.png' ) ),
					),
					array(
						'name'  => 'UERJ',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-uerj.png' ) ),
					),
					array(
						'name'  => 'USP',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-usp.png' ) ),
					),
					array(
						'name'  => 'Unifesp',
						'image' => array( 'url' => $this->home_asset_uri( 'proof-logo-unifesp.png' ) ),
					),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$students = array(
			'proof-students-1.webp',
			'proof-students-2.png',
			'proof-students-3.png',
			'proof-students-4.png',
			'proof-students-5.png',
			'proof-students-6.png',
		);
		$logos    = array(
			'proof-logo-ufrj.png',
			'proof-logo-ufrgs.png',
			'proof-logo-unicamp.png',
			'proof-logo-uerj.png',
			'proof-logo-usp.png',
			'proof-logo-unifesp.png',
		);
		$this->open_home_wrapper();
		?>
		<section id="aprovados" class="pen-proof-section" aria-labelledby="pro-proof-title">
			<div class="pen-proof-section__students">
				<p class="pen-proof-section__badge"><span><?php echo esc_html( $settings['badge_line_1'] ?? '' ); ?></span><span><?php echo esc_html( $settings['badge_line_2'] ?? '' ); ?></span></p>
				<?php foreach ( (array) ( $settings['student_images'] ?? array() ) as $index => $student ) : ?>
					<img class="pen-proof-section__image" src="<?php echo esc_url( $this->media_url( $student['image'] ?? array(), $students[ $index ] ?? 'proof-students-1.webp' ) ); ?>" alt="<?php esc_attr_e( 'Aluno aprovado exibindo aprovação.', 'proenem-wordpress-theme' ); ?>">
				<?php endforeach; ?>
			</div>
			<div class="pen-proof-section__strip">
				<h2 id="pro-proof-title"><?php echo esc_html( $settings['title'] ?? '' ); ?></h2>
				<div class="pen-proof-section__logos" aria-label="<?php esc_attr_e( 'Universidades públicas com alunos aprovados pela Proenem', 'proenem-wordpress-theme' ); ?>">
					<?php foreach ( (array) ( $settings['logos'] ?? array() ) as $index => $logo ) : ?>
						<img class="pen-proof-section__logo" src="<?php echo esc_url( $this->media_url( $logo['image'] ?? array(), $logos[ $index ] ?? 'proof-logo-ufrj.png' ) ); ?>" alt="<?php echo esc_attr( $logo['name'] ?? '' ); ?>">
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home pain widget.
 */
class Proenem_Elementor_Home_Pain_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_pain';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Dores', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-info-box';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'eyebrow', esc_html__( 'Selo', 'proenem-wordpress-theme' ), esc_html__( 'Você se identifica?', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line_1', esc_html__( 'Título linha 1', 'proenem-wordpress-theme' ), esc_html__( 'Já sentiu que', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis_1', esc_html__( 'Destaque 1', 'proenem-wordpress-theme' ), esc_html__( 'estuda muito,', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line_2', esc_html__( 'Título linha 2', 'proenem-wordpress-theme' ), esc_html__( 'mas a nota', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis_2', esc_html__( 'Destaque 2', 'proenem-wordpress-theme' ), esc_html__( 'não sobe?', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'statement_1', esc_html__( 'Frase final 1', 'proenem-wordpress-theme' ), esc_html__( 'O problema não é o esforço.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'statement_2', esc_html__( 'Frase final 2', 'proenem-wordpress-theme' ), esc_html__( 'É estudar', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'statement_emphasis', esc_html__( 'Destaque final', 'proenem-wordpress-theme' ), esc_html__( 'sem método.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão', 'proenem-wordpress-theme' ), esc_html__( 'Comece agora!', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_badge', esc_html__( 'Badge do botão', 'proenem-wordpress-theme' ), esc_html__( 'É gratuito', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link do botão', 'proenem-wordpress-theme' ), '#planos' );

		$cards = new \Elementor\Repeater();
		$cards->add_control(
			'title_prefix',
			array(
				'label' => esc_html__( 'Texto antes', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$cards->add_control(
			'title_emphasis',
			array(
				'label' => esc_html__( 'Destaque', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$cards->add_control(
			'title_suffix',
			array(
				'label' => esc_html__( 'Texto depois', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$cards->add_control(
			'body',
			array(
				'label' => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$cards->add_control(
			'tone',
			array(
				'label'   => esc_html__( 'Tom visual', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'blue',
				'options' => array(
					'blue'   => 'Blue',
					'yellow' => 'Yellow',
					'red'    => 'Red',
				),
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $cards->get_controls(),
				'default'     => array(
					array(
						'title_prefix'   => esc_html__( 'Estuda', 'proenem-wordpress-theme' ),
						'title_emphasis' => esc_html__( 'sem direção', 'proenem-wordpress-theme' ),
						'body'           => esc_html__( 'Cronogramas bonitos, mas sem priorização. Estuda tudo igual e o resultado não aparece.', 'proenem-wordpress-theme' ),
						'tone'           => 'blue',
					),
					array(
						'title_emphasis' => esc_html__( 'Trava', 'proenem-wordpress-theme' ),
						'title_suffix'   => esc_html__( 'em simulados', 'proenem-wordpress-theme' ),
						'body'           => esc_html__( 'A nota não sai do lugar. Faz simulados, mas não analisa os erros. Repete os mesmos equívocos.', 'proenem-wordpress-theme' ),
						'tone'           => 'yellow',
					),
					array(
						'title_emphasis' => esc_html__( 'Ansiedade', 'proenem-wordpress-theme' ),
						'title_suffix'   => esc_html__( '& insegurança', 'proenem-wordpress-theme' ),
						'body'           => esc_html__( 'Pressão familiar, medo de não passar e sensação de estar sempre atrasado em relação aos outros.', 'proenem-wordpress-theme' ),
						'tone'           => 'red',
					),
				),
				'title_field' => '{{{ title_emphasis }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$images   = array(
			'proof-students-1.webp',
			'proof-students-3.png',
			'proof-students-4.png',
			'proof-students-5.png',
		);
		$this->open_home_wrapper();
		?>
		<section class="pen-feature-grid-section" aria-labelledby="pro-pain-title">
			<p class="pen-section-pill"><?php echo esc_html( $settings['eyebrow'] ?? '' ); ?></p>
			<h2 id="pro-pain-title"><?php echo esc_html( $settings['title_line_1'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis_1'] ?? '' ); ?></strong><br><?php echo esc_html( $settings['title_line_2'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis_2'] ?? '' ); ?></strong></h2>
			<div class="pen-feature-grid">
				<?php foreach ( (array) ( $settings['cards'] ?? array() ) as $card ) : ?>
					<article class="pro-home-pain-card pro-home-pain-card--<?php echo esc_attr( $card['tone'] ?? 'blue' ); ?>">
						<h3>
						<?php
						if ( ! empty( $card['title_prefix'] ) ) :
							?>
							<span><?php echo esc_html( $card['title_prefix'] ); ?></span><?php endif; ?> <strong><?php echo esc_html( $card['title_emphasis'] ?? '' ); ?></strong> 
							<?php
							if ( ! empty( $card['title_suffix'] ) ) :
								?>
							<span><?php echo esc_html( $card['title_suffix'] ); ?></span><?php endif; ?></h3>
						<p><?php echo esc_html( $card['body'] ?? '' ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="pro-home-pain-section__statement"><span><?php echo esc_html( $settings['statement_1'] ?? '' ); ?></span><span><?php echo esc_html( $settings['statement_2'] ?? '' ); ?> <strong><?php echo esc_html( $settings['statement_emphasis'] ?? '' ); ?></strong></span></p>
			<?php $this->render_home_button( 'button_url', $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-button pen-button--primary pen-button--md pro-home-pain-section__cta', $settings['button_badge'] ?? '' ); ?>
			<span class="pro-home-pain-section__shape pro-home-pain-section__shape--blue" aria-hidden="true"></span><span class="pro-home-pain-section__shape pro-home-pain-section__shape--pink" aria-hidden="true"></span>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home platform widget.
 */
class Proenem_Elementor_Home_Platform_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_platform';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Plataforma', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-tabs';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'title_strong', esc_html__( 'Título destaque', 'proenem-wordpress-theme' ), esc_html__( 'Explore', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_span', esc_html__( 'Título colorido', 'proenem-wordpress-theme' ), esc_html__( 'por dentro', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_tail', esc_html__( 'Título final', 'proenem-wordpress-theme' ), esc_html__( 'cada detalhe', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'note', esc_html__( 'Nota', 'proenem-wordpress-theme' ), esc_html__( 'Clique em qualquer item à esquerda e veja exatamente como funciona — direto na plataforma.', 'proenem-wordpress-theme' ) );

		$items = new \Elementor\Repeater();
		$items->add_control(
			'label',
			array(
				'label' => esc_html__( 'Aba', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Ícone', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'clock',
				'options' => array(
					'clock' => 'Clock',
					'book'  => 'Book',
					'brain' => 'Brain',
					'robot' => 'Robot',
					'edit'  => 'Edit',
					'chart' => 'Chart',
				),
			)
		);
		$items->add_control(
			'tone',
			array(
				'label'   => esc_html__( 'Tom visual', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'blue',
				'options' => array(
					'blue'   => 'Blue',
					'yellow' => 'Yellow',
					'green'  => 'Green',
					'red'    => 'Red',
					'active' => 'Active',
				),
			)
		);
		$items->add_control(
			'title',
			array(
				'label' => esc_html__( 'Título da tela', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$items->add_control(
			'body',
			array(
				'label' => esc_html__( 'Texto da tela', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$items->add_control(
			'url',
			array(
				'label' => esc_html__( 'URL exibida', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'bullets',
			array(
				'label' => esc_html__( 'Bullets, um por linha', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Recursos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array(
						'label'   => esc_html__( 'Aulas ao vivo todos os dias', 'proenem-wordpress-theme' ),
						'icon'    => 'clock',
						'tone'    => 'blue',
						'title'   => esc_html__( 'Aulas ao vivo para manter sua rotina em movimento.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'Entre em salas guiadas por professores e acompanhe os temas mais importantes da semana.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/aulas-ao-vivo',
						'bullets' => esc_html__( "Agenda diária de aulas\nRevisões próximas das provas\nRegistro do que você já assistiu", 'proenem-wordpress-theme' ),
					),
					array(
						'label'   => esc_html__( '+50 mil questões', 'proenem-wordpress-theme' ),
						'icon'    => 'book',
						'tone'    => 'yellow',
						'title'   => esc_html__( 'Mais de 50 mil questões para treinar com intenção.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'Filtre por disciplina, assunto e dificuldade para transformar prática em diagnóstico.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/questoes',
						'bullets' => esc_html__( "Questões por área do conhecimento\nResoluções comentadas\nHistórico de acertos e erros", 'proenem-wordpress-theme' ),
					),
					array(
						'label'   => esc_html__( 'Plano personalizado', 'proenem-wordpress-theme' ),
						'icon'    => 'brain',
						'tone'    => 'green',
						'title'   => esc_html__( 'Plano personalizado para estudar o que mais importa agora.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'A plataforma organiza prioridades a partir da sua meta, tempo disponível e evolução.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/plano',
						'bullets' => esc_html__( "Rotina ajustada por meta\nPrioridade por lacuna\nPróximas ações sempre visíveis", 'proenem-wordpress-theme' ),
					),
					array(
						'label'   => esc_html__( 'Tutor com IA', 'proenem-wordpress-theme' ),
						'icon'    => 'robot',
						'tone'    => 'red',
						'title'   => esc_html__( 'Tutor com IA para tirar dúvidas no seu ritmo.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'Receba explicações guiadas e volte para o estudo sem perder o contexto da tarefa.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/tutor-ia',
						'bullets' => esc_html__( "Explicação passo a passo\nApoio em questões difíceis\nDisponível durante a rotina", 'proenem-wordpress-theme' ),
					),
					array(
						'label'   => esc_html__( 'Correção de redação', 'proenem-wordpress-theme' ),
						'icon'    => 'edit',
						'tone'    => 'blue',
						'title'   => esc_html__( 'Correção de redação com devolutiva objetiva.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'Entenda competência por competência onde melhorar para escrever com mais segurança.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/redacao',
						'bullets' => esc_html__( "Comentários por competência\nPlano de reescrita\nEvolução por envio", 'proenem-wordpress-theme' ),
					),
					array(
						'label'   => esc_html__( 'Simulados com TRI', 'proenem-wordpress-theme' ),
						'icon'    => 'chart',
						'tone'    => 'active',
						'title'   => esc_html__( 'Simulados com a mesma lógica de correção do ENEM.', 'proenem-wordpress-theme' ),
						'body'    => esc_html__( 'Veja sua nota real, evolução por área e onde focar agora.', 'proenem-wordpress-theme' ),
						'url'     => 'proenem.com.br/app/simulados-com-tri',
						'bullets' => esc_html__( "Nota real estimada pelo TRI\nComparativo com aprovados\nDiagnóstico por área e tópico", 'proenem-wordpress-theme' ),
					),
				),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$items       = (array) ( $settings['items'] ?? array() );
		$active_item = $items ? $items[ count( $items ) - 1 ] : array();
		$this->open_home_wrapper();
		?>
		<section class="pen-platform-showcase" aria-labelledby="pro-platform-title" data-pro-home-platform-tabs>
			<div class="pen-platform-showcase__panel">
				<header class="pro-home-platform-header">
					<h2 id="pro-platform-title"><strong><?php echo esc_html( $settings['title_strong'] ?? '' ); ?></strong> <span><?php echo esc_html( $settings['title_span'] ?? '' ); ?></span><br><?php echo esc_html( $settings['title_tail'] ?? '' ); ?></h2>
					<p class="pro-home-platform-note"><img src="<?php echo esc_url( $this->home_asset_uri( 'sticker_explore_por_dentro.svg' ) ); ?>" alt="" aria-hidden="true"><span class="pro-home-platform-note__text"><?php echo esc_html( $settings['note'] ?? '' ); ?></span></p>
				</header>
				<div class="pro-home-platform-body">
					<ul class="pro-home-platform-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Recursos da plataforma', 'proenem-wordpress-theme' ); ?>">
						<?php foreach ( $items as $index => $item ) : ?>
							<?php $is_active = count( $items ) - 1 === $index; ?>
							<li role="presentation"><button type="button" class="pro-home-platform-tab pro-home-platform-tab--<?php echo esc_attr( $item['tone'] ?? 'blue' ); ?><?php echo $is_active ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo esc_attr( $is_active ? 'true' : 'false' ); ?>" data-pro-home-platform-tab data-title="<?php echo esc_attr( $item['title'] ?? '' ); ?>" data-body="<?php echo esc_attr( $item['body'] ?? '' ); ?>" data-url="<?php echo esc_attr( $item['url'] ?? '' ); ?>" data-bullets="<?php echo esc_attr( wp_json_encode( $this->split_lines( $item['bullets'] ?? '' ) ) ); ?>"><span class="pro-home-platform-tab__icon" aria-hidden="true"><?php echo $this->platform_icon_svg( $item['icon'] ?? 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="pro-home-platform-tab__label"><?php echo esc_html( $item['label'] ?? '' ); ?></span><span class="pro-home-platform-tab__arrow" aria-hidden="true">→</span></button></li>
						<?php endforeach; ?>
					</ul>
					<div class="pen-platform-showcase__screen"><div class="pro-home-platform-mock" data-pro-home-platform-screen><div class="pro-home-platform-browser" aria-hidden="true"><span></span><span></span><span></span><small data-pro-home-platform-url><?php echo esc_html( $active_item['url'] ?? '' ); ?></small></div><div class="pro-home-platform-mock__dashboard" aria-hidden="true"><span></span><span></span><span></span><span></span></div><h3 data-pro-home-platform-title><?php echo esc_html( $active_item['title'] ?? '' ); ?></h3><p data-pro-home-platform-body><?php echo esc_html( $active_item['body'] ?? '' ); ?></p><ul class="pro-home-platform-mock__bullets" data-pro-home-platform-bullets>
					<?php
					foreach ( $this->split_lines( $active_item['bullets'] ?? '' ) as $bullet ) :
						?>
						<li><?php echo esc_html( $bullet ); ?></li><?php endforeach; ?></ul></div></div>
				</div>
				<span class="pro-home-platform-star" aria-hidden="true"></span>
			</div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home questions widget.
 */
class Proenem_Elementor_Home_Questions_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_questions';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Banco de Questões', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-post-list';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'title_prefix', esc_html__( 'Título antes', 'proenem-wordpress-theme' ), esc_html__( 'Explore', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis', esc_html__( 'Título destaque', 'proenem-wordpress-theme' ), esc_html__( '+50 mil questões', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_suffix', esc_html__( 'Título depois', 'proenem-wordpress-theme' ), esc_html__( 'sem precisar criar conta.', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( 'Questões do ENEM e dos principais vestibulares, com resolução em vídeo. Escolha uma disciplina e comece agora.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão', 'proenem-wordpress-theme' ), esc_html__( 'Comece agora!', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_badge', esc_html__( 'Badge do botão', 'proenem-wordpress-theme' ), esc_html__( 'É gratuito', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link padrão dos cards', 'proenem-wordpress-theme' ), '#planos' );
		$subjects = new \Elementor\Repeater();
		$subjects->add_control(
			'name',
			array(
				'label' => esc_html__( 'Disciplina', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$subjects->add_control(
			'category',
			array(
				'label' => esc_html__( 'Categoria', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$subjects->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Ícone', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'portuguese',
				'options' => array(
					'chemistry'  => 'Chemistry',
					'biology'    => 'Biology',
					'math'       => 'Math',
					'history'    => 'History',
					'english'    => 'English',
					'portuguese' => 'Portuguese',
				),
			)
		);
		$subjects->add_control(
			'tone',
			array(
				'label'   => esc_html__( 'Tom visual', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'pink',
				'options' => array(
					'yellow' => 'Yellow',
					'pink'   => 'Pink',
				),
			)
		);
		$subjects->add_control(
			'questions',
			array(
				'label'   => esc_html__( 'Meta questões', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '512 questões', 'proenem-wordpress-theme' ),
			)
		);
		$subjects->add_control(
			'classes',
			array(
				'label'   => esc_html__( 'Meta aulas', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '40 aulas', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'subjects',
			array(
				'label'       => esc_html__( 'Disciplinas', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $subjects->get_controls(),
				'default'     => array(
					array(
						'name'     => esc_html__( 'Química', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
						'icon'     => 'chemistry',
						'tone'     => 'yellow',
					),
					array(
						'name'     => esc_html__( 'Biologia', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Ciências da Natureza', 'proenem-wordpress-theme' ),
						'icon'     => 'biology',
						'tone'     => 'pink',
					),
					array(
						'name'     => esc_html__( 'Matemática', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Matemática', 'proenem-wordpress-theme' ),
						'icon'     => 'math',
						'tone'     => 'pink',
					),
					array(
						'name'     => esc_html__( 'História', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Ciências Humanas', 'proenem-wordpress-theme' ),
						'icon'     => 'history',
						'tone'     => 'pink',
					),
					array(
						'name'     => esc_html__( 'Inglês', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Linguagens', 'proenem-wordpress-theme' ),
						'icon'     => 'english',
						'tone'     => 'pink',
					),
					array(
						'name'     => esc_html__( 'Português', 'proenem-wordpress-theme' ),
						'category' => esc_html__( 'Linguagens', 'proenem-wordpress-theme' ),
						'icon'     => 'portuguese',
						'tone'     => 'pink',
					),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section id="questoes" class="pen-question-bank-section" aria-labelledby="pro-questions-title">
			<img class="pro-home-question-bank__background" src="<?php echo esc_url( $this->home_asset_uri( 'sticker_explore_questions.svg' ) ); ?>" alt="" aria-hidden="true">
			<h2 id="pro-questions-title"><?php echo esc_html( $settings['title_prefix'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis'] ?? '' ); ?></strong><br><?php echo esc_html( $settings['title_suffix'] ?? '' ); ?></h2>
			<p><?php echo esc_html( $settings['body'] ?? '' ); ?></p>
			<div class="pen-subject-grid">
				<?php foreach ( (array) ( $settings['subjects'] ?? array() ) as $subject ) : ?>
					<a class="pro-home-subject-card pro-home-subject-card--<?php echo esc_attr( $subject['tone'] ?? 'pink' ); ?>" href="<?php echo esc_url( $settings['button_url']['url'] ?? '#planos' ); ?>"><span class="pro-home-subject-card__icon" aria-hidden="true"><?php echo $this->subject_icon_svg( $subject['icon'] ?? 'portuguese' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="pro-home-subject-card__body"><strong><?php echo esc_html( $subject['name'] ?? '' ); ?></strong><small><?php echo esc_html( $subject['category'] ?? '' ); ?></small><span class="pro-home-subject-card__meta"><?php echo esc_html( $subject['questions'] ?? '' ); ?></span><span class="pro-home-subject-card__meta"><?php echo esc_html( $subject['classes'] ?? '' ); ?></span></span><span class="pro-home-subject-card__arrow" aria-hidden="true">→</span></a>
				<?php endforeach; ?>
			</div>
			<?php $this->render_home_button( 'button_url', $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-button pen-button--secondary pen-button--sm pro-home-question-bank__cta', $settings['button_badge'] ?? '' ); ?>
			<img class="pro-home-question-bank__shape" src="<?php echo esc_url( $this->home_asset_uri( 'blue_3_semi-spheres.svg' ) ); ?>" alt="" aria-hidden="true">
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home pricing widget.
 */
class Proenem_Elementor_Home_Pricing_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_pricing';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Planos', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-price-table';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'title_line_1', esc_html__( 'Título linha 1', 'proenem-wordpress-theme' ), esc_html__( 'Investimento que se', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line_2', esc_html__( 'Título linha 2', 'proenem-wordpress-theme' ), esc_html__( 'paga em', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis', esc_html__( 'Destaque', 'proenem-wordpress-theme' ), esc_html__( 'uma vaga.', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( "Comece grátis. Cancele com 1 clique.\n7 dias de garantia em todos os planos.", 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão dos planos', 'proenem-wordpress-theme' ), esc_html__( 'Quero o Método PRO', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link dos planos', 'proenem-wordpress-theme' ), '#faq' );

		$plans = new \Elementor\Repeater();
		$plans->add_control(
			'name',
			array(
				'label' => esc_html__( 'Nome', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$plans->add_control(
			'price',
			array(
				'label' => esc_html__( 'Preço', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$plans->add_control(
			'summary',
			array(
				'label' => esc_html__( 'Resumo', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$plans->add_control(
			'features',
			array(
				'label' => esc_html__( 'Benefícios, um por linha', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$plans->add_control(
			'featured',
			array(
				'label'        => esc_html__( 'Destacado', 'proenem-wordpress-theme' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'plans',
			array(
				'label'       => esc_html__( 'Planos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $plans->get_controls(),
				'default'     => array(
					array(
						'name'     => esc_html__( 'Essencial', 'proenem-wordpress-theme' ),
						'price'    => '39',
						'summary'  => esc_html__( 'Para começar a estudar com método.', 'proenem-wordpress-theme' ),
						'features' => esc_html__( "Banco com +50k questões\nAulas gravadas completas\nCronograma básico\nComunidade de alunos", 'proenem-wordpress-theme' ),
					),
					array(
						'name'     => esc_html__( 'Método PRO', 'proenem-wordpress-theme' ),
						'price'    => '99',
						'summary'  => esc_html__( 'O método completo, com IA e mentoria.', 'proenem-wordpress-theme' ),
						'features' => esc_html__( "Tudo do Essencial\nTutor IA ilimitado 24/7\nRedação corrigida em 48h\nSimulados TRI semanais\nAulas ao vivo todos os dias\nPlano adaptativo por IA", 'proenem-wordpress-theme' ),
						'featured' => 'yes',
					),
					array(
						'name'     => esc_html__( 'Elite', 'proenem-wordpress-theme' ),
						'price'    => '199',
						'summary'  => esc_html__( 'Mentoria 1:1 com aprovados.', 'proenem-wordpress-theme' ),
						'features' => esc_html__( "Tudo do Método PRO\nMentor pessoal aprovado em Medicina\n2 sessões 1:1 por semana\nRevisão de redação prioritária", 'proenem-wordpress-theme' ),
					),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section id="planos" class="pen-pricing-section" aria-labelledby="pro-pricing-title">
			<img class="pro-home-pricing__strokes" src="<?php echo esc_url( $this->home_asset_uri( 'price_vector_strokes.svg' ) ); ?>" alt="" aria-hidden="true">
			<div class="pro-home-pricing__header"><div class="pro-home-pricing__seal" aria-hidden="true"><img class="pro-home-pricing__seal-bg" src="<?php echo esc_url( $this->home_asset_uri( 'Ellipse-fundo-price.svg' ) ); ?>" alt="" aria-hidden="true"><img class="pro-home-pricing__seal-text" src="<?php echo esc_url( $this->home_asset_uri( 'Cancele-quando-voce-quiser.svg' ) ); ?>" alt="" aria-hidden="true"><img class="pro-home-pricing__seal-check" src="<?php echo esc_url( $this->home_asset_uri( 'check-verified-01.svg' ) ); ?>" alt="" aria-hidden="true"></div><div class="pro-home-pricing__intro"><h2 id="pro-pricing-title"><?php echo esc_html( $settings['title_line_1'] ?? '' ); ?><br><?php echo esc_html( $settings['title_line_2'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis'] ?? '' ); ?></strong></h2>
			<?php
			foreach ( $this->split_lines( $settings['body'] ?? '' ) as $line ) :
				?>
				<p><?php echo esc_html( $line ); ?></p><?php endforeach; ?></div></div>
			<div class="pen-plan-grid">
				<?php foreach ( (array) ( $settings['plans'] ?? array() ) as $index => $plan ) : ?>
					<article class="pen-plan-card<?php echo ! empty( $plan['featured'] ) ? ' is-featured' : ''; ?>">
						<?php
						if ( ! empty( $plan['featured'] ) ) :
							?>
							<span class="pro-home-plan-card__label"><?php esc_html_e( 'Mais escolhido', 'proenem-wordpress-theme' ); ?></span><?php endif; ?>
						<header><h3><?php echo esc_html( $plan['name'] ?? '' ); ?></h3><p><?php echo esc_html( $plan['summary'] ?? '' ); ?></p><strong <?php echo ! empty( $plan['featured'] ) ? 'style="' . esc_attr( '--pro-home-pricing-star: url(' . esc_url( $this->home_asset_uri( 'pricing_star.svg' ) ) . ');' ) . '"' : ''; ?>><span><?php esc_html_e( 'R$', 'proenem-wordpress-theme' ); ?></span><?php echo esc_html( $plan['price'] ?? '' ); ?><small><?php esc_html_e( 'ao mês', 'proenem-wordpress-theme' ); ?></small></strong></header>
						<ul>
						<?php
						foreach ( $this->split_lines( $plan['features'] ?? '' ) as $feature ) :
							?>
							<li><?php echo esc_html( $feature ); ?></li><?php endforeach; ?></ul>
						<?php $this->render_home_button( 'plan_button_' . $index, $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-action-link pen-action-link--primary' ); ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home testimonials widget.
 */
class Proenem_Elementor_Home_Testimonials_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_testimonials';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Depoimentos', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'eyebrow', esc_html__( 'Selo', 'proenem-wordpress-theme' ), esc_html__( 'Aprovados', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line', esc_html__( 'Título', 'proenem-wordpress-theme' ), esc_html__( 'Quem seguiu o método,', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis', esc_html__( 'Título destaque', 'proenem-wordpress-theme' ), esc_html__( 'conquistou a vaga.', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( 'Mais de 40 mil alunos já foram aprovados com a ProEnem. Conheça algumas histórias.', 'proenem-wordpress-theme' ) );
		$items = new \Elementor\Repeater();
		$items->add_control(
			'quote',
			array(
				'label' => esc_html__( 'Depoimento', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$items->add_control(
			'name',
			array(
				'label' => esc_html__( 'Nome', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'role',
			array(
				'label' => esc_html__( 'Descrição', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'image',
			array(
				'label' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'testimonials',
			array(
				'label'       => esc_html__( 'Depoimentos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array(
						'quote' => esc_html__( 'O Método PRO me deu clareza para estudar o que realmente importava. Parei de acumular tarefas e comecei a enxergar evolução semana a semana.', 'proenem-wordpress-theme' ),
						'name'  => esc_html__( 'Mariana Costa', 'proenem-wordpress-theme' ),
						'role'  => esc_html__( 'Aprovada em Medicina', 'proenem-wordpress-theme' ),
						'image' => array( 'url' => $this->home_asset_uri( 'proof-students-1.webp' ) ),
					),
					array(
						'quote' => esc_html__( 'A rotina ficou simples de seguir. Os simulados, o diagnóstico e a correção de redação mostravam exatamente onde eu precisava insistir.', 'proenem-wordpress-theme' ),
						'name'  => esc_html__( 'Lucas Almeida', 'proenem-wordpress-theme' ),
						'role'  => esc_html__( 'Aprovado em Engenharia', 'proenem-wordpress-theme' ),
						'image' => array( 'url' => $this->home_asset_uri( 'proof-students-3.png' ) ),
					),
					array(
						'quote' => esc_html__( 'Eu estudava muito, mas sem direção. Com o método, consegui organizar minhas prioridades e chegar na prova muito mais confiante.', 'proenem-wordpress-theme' ),
						'name'  => esc_html__( 'Beatriz Rocha', 'proenem-wordpress-theme' ),
						'role'  => esc_html__( 'Aprovada em Direito', 'proenem-wordpress-theme' ),
						'image' => array( 'url' => $this->home_asset_uri( 'proof-students-4.png' ) ),
					),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section id="depoimentos" class="pro-home-testimonials" aria-labelledby="pro-testimonials-title" data-pro-home-testimonials-slider>
			<div class="pro-home-testimonials__header"><span class="pen-section-pill"><?php echo esc_html( $settings['eyebrow'] ?? '' ); ?></span><h2 id="pro-testimonials-title"><span><?php echo esc_html( $settings['title_line'] ?? '' ); ?></span><strong><?php echo esc_html( $settings['title_emphasis'] ?? '' ); ?></strong></h2><p><?php echo esc_html( $settings['body'] ?? '' ); ?></p></div>
			<div class="pro-home-testimonials__viewport"><div class="pro-home-testimonials__track" data-pro-home-testimonials-track>
			<?php
			foreach ( (array) ( $settings['testimonials'] ?? array() ) as $index => $testimonial ) :
				?>
				<article class="pro-home-testimonial-card<?php echo 1 === $index ? ' is-active' : ''; ?>" data-pro-home-testimonial-card><div class="pro-home-testimonial-card__quote"><span aria-hidden="true">“</span><p><?php echo esc_html( $testimonial['quote'] ?? '' ); ?></p></div><footer><img src="<?php echo esc_url( $this->media_url( $testimonial['image'] ?? array(), $images[ $index ] ?? 'proof-students-1.webp' ) ); ?>" alt="<?php echo esc_attr( $testimonial['name'] ?? '' ); ?>"><span><strong><?php echo esc_html( $testimonial['name'] ?? '' ); ?></strong><small><?php echo esc_html( $testimonial['role'] ?? '' ); ?></small></span></footer></article><?php endforeach; ?></div></div>
			<div class="pro-home-testimonials__controls" aria-label="<?php esc_attr_e( 'Controles dos depoimentos', 'proenem-wordpress-theme' ); ?>"><button type="button" data-pro-home-testimonials-prev aria-label="<?php esc_attr_e( 'Depoimento anterior', 'proenem-wordpress-theme' ); ?>">←</button><button type="button" data-pro-home-testimonials-next aria-label="<?php esc_attr_e( 'Próximo depoimento', 'proenem-wordpress-theme' ); ?>">→</button></div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home schools widget.
 */
class Proenem_Elementor_Home_Schools_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_schools';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home Escolas', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-site-identity';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_textarea_control( 'marquee', esc_html__( 'Marquee, uma frase por linha', 'proenem-wordpress-theme' ), esc_html__( "A engenharia da sua aprovação\nTroque a sorte pela estratégia\nConheça o Método PRO", 'proenem-wordpress-theme' ) );
		$this->add_home_media_control( 'primary_image', esc_html__( 'Imagem principal', 'proenem-wordpress-theme' ), 'student_school_1.png' );
		$this->add_home_media_control( 'secondary_image', esc_html__( 'Imagem secundária', 'proenem-wordpress-theme' ), 'student_school_2.png' );
		$this->add_text_control( 'title_prefix', esc_html__( 'Título antes', 'proenem-wordpress-theme' ), esc_html__( 'Leve o', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis_1', esc_html__( 'Destaque 1', 'proenem-wordpress-theme' ), esc_html__( 'Método PRO', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_middle', esc_html__( 'Título meio', 'proenem-wordpress-theme' ), esc_html__( 'para a', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_emphasis_2', esc_html__( 'Destaque 2', 'proenem-wordpress-theme' ), esc_html__( 'sua escola.', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( 'Planos especiais para instituições que querem oferecer a melhor preparação para o ENEM. Plataforma, material didático e acompanhamento em um único pacote.', 'proenem-wordpress-theme' ) );
		$cards = new \Elementor\Repeater();
		$cards->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Ícone', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'book',
				'options' => array(
					'book'  => 'Book',
					'chart' => 'Chart',
					'brain' => 'Brain',
					'robot' => 'Robot',
				),
			)
		);
		$cards->add_control(
			'title',
			array(
				'label' => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$cards->add_control(
			'body',
			array(
				'label' => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $cards->get_controls(),
				'default'     => array(
					array(
						'icon'  => 'book',
						'title' => esc_html__( 'Combo plataforma + apostilas', 'proenem-wordpress-theme' ),
						'body'  => esc_html__( 'Acesso completo à plataforma, mais o kit de apostilas exclusivas entregue na escola.', 'proenem-wordpress-theme' ),
					),
					array(
						'icon'  => 'chart',
						'title' => esc_html__( 'Acompanhe cada aluno', 'proenem-wordpress-theme' ),
						'body'  => esc_html__( 'Painel exclusivo para coordenadores e professores com desempenho, simulados e frequência em tempo real.', 'proenem-wordpress-theme' ),
					),
					array(
						'icon'  => 'brain',
						'title' => esc_html__( 'Acesso para todos os alunos', 'proenem-wordpress-theme' ),
						'body'  => esc_html__( 'Licenças geradas para todas as turmas. Cada aluno tem seu perfil individual e plano personalizado.', 'proenem-wordpress-theme' ),
					),
					array(
						'icon'  => 'robot',
						'title' => esc_html__( 'Onboarding dedicado', 'proenem-wordpress-theme' ),
						'body'  => esc_html__( 'Um especialista cuida da implantação, treina os professores e acompanha os primeiros ciclos.', 'proenem-wordpress-theme' ),
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section class="pen-audience-section pro-home-school-section" aria-labelledby="pro-school-title">
			<div class="pro-home-school-section__marquee" aria-hidden="true"><div class="pro-home-school-section__marquee-track">
			<?php
			for ( $i = 0; $i < 2; $i++ ) :
				foreach ( $this->split_lines( $settings['marquee'] ?? '' ) as $item ) :
					?>
				<span><?php echo esc_html( $item ); ?></span>
					<?php
endforeach;
endfor;
			?>
			</div></div>
			<figure class="pro-home-school-section__photo pro-home-school-section__photo--primary"><img src="<?php echo esc_url( $this->media_url( $settings['primary_image'] ?? array(), 'student_school_1.png' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo em ambiente escolar.', 'proenem-wordpress-theme' ); ?>"></figure>
			<div class="pen-audience-section__intro pro-home-school-section__intro"><div><h2 id="pro-school-title"><?php echo esc_html( $settings['title_prefix'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis_1'] ?? '' ); ?></strong><br><?php echo esc_html( $settings['title_middle'] ?? '' ); ?> <strong><?php echo esc_html( $settings['title_emphasis_2'] ?? '' ); ?></strong></h2><p><?php echo esc_html( $settings['body'] ?? '' ); ?></p></div><img class="pro-home-school-section__photo-secondary" src="<?php echo esc_url( $this->media_url( $settings['secondary_image'] ?? array(), 'student_school_2.png' ) ); ?>" alt="<?php esc_attr_e( 'Estudante sorrindo com livros ao fundo.', 'proenem-wordpress-theme' ); ?>"><span class="pro-home-school-section__burst" aria-hidden="true"></span></div>
			<div class="pen-feature-grid pen-feature-grid--school">
			<?php
			foreach ( (array) ( $settings['cards'] ?? array() ) as $card ) :
				?>
				<article><span class="pro-home-school-section__card-icon" aria-hidden="true"><?php echo $this->platform_icon_svg( $card['icon'] ?? 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php echo esc_html( $card['title'] ?? '' ); ?></h3><p><?php echo esc_html( $card['body'] ?? '' ); ?></p></article><?php endforeach; ?></div>
		</section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home final CTA widget.
 */
class Proenem_Elementor_Home_Final_Cta_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_final_cta';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home CTA Final', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-button';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'title', esc_html__( 'Título', 'proenem-wordpress-theme' ), esc_html__( 'Pronto para transformar a preparação ENEM na sua escola?', 'proenem-wordpress-theme' ) );
		$this->add_textarea_control( 'body', esc_html__( 'Texto', 'proenem-wordpress-theme' ), esc_html__( 'Converse com nossa equipe e receba uma proposta personalizada de acordo com o tamanho e perfil da sua instituição.', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'button_label', esc_html__( 'Botão', 'proenem-wordpress-theme' ), esc_html__( 'Começar gratuitamente', 'proenem-wordpress-theme' ) );
		$this->add_url_control( 'button_url', esc_html__( 'Link do botão', 'proenem-wordpress-theme' ), '#faq' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section class="pen-marketing-cta pro-home__final-cta" aria-labelledby="pro-final-title"><div class="pen-marketing-cta__content"><h2 id="pro-final-title"><?php echo esc_html( $settings['title'] ?? '' ); ?></h2><p><?php echo esc_html( $settings['body'] ?? '' ); ?></p></div><div class="pen-marketing-cta__actions"><?php $this->render_home_button( 'button_url', $settings['button_url'] ?? array(), $settings['button_label'] ?? '', 'pen-button pen-button--primary pen-button--lg' ); ?></div></section>
		<?php
		$this->close_home_wrapper();
	}
}

/**
 * Pro home FAQ widget.
 */
class Proenem_Elementor_Home_Faq_Widget extends Proenem_Elementor_Home_Widget_Base {
	public function get_name(): string {
		return 'pro_home_faq';
	}

	public function get_title(): string {
		return esc_html__( 'Pro Home FAQ', 'proenem-wordpress-theme' );
	}

	public function get_icon(): string {
		return 'eicon-help-o';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ) ) );
		$this->add_text_control( 'eyebrow', esc_html__( 'Selo', 'proenem-wordpress-theme' ), esc_html__( 'Perguntas frequentes', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line_1', esc_html__( 'Título linha 1', 'proenem-wordpress-theme' ), esc_html__( 'Já sentiu que estuda muito,', 'proenem-wordpress-theme' ) );
		$this->add_text_control( 'title_line_2', esc_html__( 'Título linha 2', 'proenem-wordpress-theme' ), esc_html__( 'mas a nota não sobe?', 'proenem-wordpress-theme' ) );
		$items = new \Elementor\Repeater();
		$items->add_control(
			'question',
			array(
				'label' => esc_html__( 'Pergunta', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'answer',
			array(
				'label' => esc_html__( 'Resposta', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Perguntas', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array(
						'question' => esc_html__( 'O que é o MÉTODO PRO?', 'proenem-wordpress-theme' ),
						'answer'   => esc_html__( 'É uma estrutura de preparação que combina meta, diagnóstico, execução e performance para organizar seus estudos até a aprovação.', 'proenem-wordpress-theme' ),
					),
					array(
						'question' => esc_html__( 'Posso começar a estudar de graça?', 'proenem-wordpress-theme' ),
						'answer'   => esc_html__( 'Sim. Você pode criar uma conta gratuita e acessar o banco de questões, listas e um diagnóstico simplificado.', 'proenem-wordpress-theme' ),
					),
					array(
						'question' => esc_html__( 'O que é a Aceleração PRO?', 'proenem-wordpress-theme' ),
						'answer'   => esc_html__( 'É o plano para quem quer acompanhamento mais próximo, simulados com TRI, redação corrigida e rotina adaptada por IA.', 'proenem-wordpress-theme' ),
					),
					array(
						'question' => esc_html__( 'Posso entrar em qualquer época do ano?', 'proenem-wordpress-theme' ),
						'answer'   => esc_html__( 'Pode. O diagnóstico inicial ajusta o plano ao seu momento e ao tempo disponível até a prova.', 'proenem-wordpress-theme' ),
					),
					array(
						'question' => esc_html__( 'E se eu não gostar?', 'proenem-wordpress-theme' ),
						'answer'   => esc_html__( 'Você pode cancelar quando quiser. A ideia é experimentar o método sem burocracia e seguir apenas se fizer sentido para sua rotina.', 'proenem-wordpress-theme' ),
					),
				),
				'title_field' => '{{{ question }}}',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->open_home_wrapper();
		?>
		<section id="faq" class="pen-faq-section" aria-labelledby="pro-faq-title"><div class="pen-faq-section__header"><span class="pen-pill-eyebrow"><?php echo esc_html( $settings['eyebrow'] ?? '' ); ?></span><h2 id="pro-faq-title"><?php echo esc_html( $settings['title_line_1'] ?? '' ); ?><br><?php echo esc_html( $settings['title_line_2'] ?? '' ); ?></h2></div><div class="pen-faq-section__items">
		<?php
		foreach ( (array) ( $settings['items'] ?? array() ) as $index => $item ) :
			?>
			<details class="pen-faq-item" <?php echo 1 === $index ? 'open' : ''; ?>><summary><?php echo esc_html( $item['question'] ?? '' ); ?></summary><p><?php echo esc_html( $item['answer'] ?? '' ); ?></p></details><?php endforeach; ?></div></section>
		<?php
		$this->close_home_wrapper();
	}
}
