<?php
/**
 * Elementor sales page widget classes.
 *
 * @package Proenem
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- Elementor widget classes share a guarded loader.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for Proenem Elementor sales widgets.
 */
abstract class Proenem_Elementor_Sales_Widget_Base extends \Elementor\Widget_Base {
	/**
	 * Get widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'proenem-sales' );
	}

	/**
	 * Keep Elementor markup lean for controlled sections.
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Get shared widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'pro', 'vendas', 'oferta' );
	}

	/**
	 * Split textarea lines into clean items.
	 *
	 * @param string $value Textarea value.
	 * @return string[]
	 */
	protected function split_lines( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
		$lines = array_map( 'trim', is_array( $lines ) ? $lines : array() );

		return array_values( array_filter( $lines ) );
	}

	/**
	 * Render a widget link when a label exists.
	 *
	 * @param string $key Elementor render attribute key.
	 * @param array  $link Elementor link setting.
	 * @param string $label Link label.
	 * @param string $class_name Link class.
	 * @return void
	 */
	protected function render_link( $key, $link, $label, $class_name ) {
		if ( '' === trim( (string) $label ) ) {
			return;
		}

		$this->add_render_attribute( $key, 'class', $class_name );

		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		}

		?>
			<a <?php $this->print_render_attribute_string( $key ); ?>>
			<?php echo esc_html( $label ); ?>
			</a>
			<?php
	}

	/**
	 * Whether this widget renders a full width page section.
	 *
	 * Section hosts get the `pro-section-host` marker so the theme can release
	 * the Elementor container gutter on sales pages. Widgets meant to be
	 * composed inside a column, like the pricing card, return false.
	 *
	 * @return bool
	 */
	protected function is_section_host(): bool {
		return true;
	}

	/**
	 * Add the section host marker to the Elementor widget wrapper.
	 *
	 * @return string
	 */
	public function get_html_wrapper_class(): string {
		$class_name = parent::get_html_wrapper_class();

		if ( $this->is_section_host() ) {
			$class_name .= ' pro-section-host';
		}

		return $class_name;
	}

	/**
	 * Build a DOM id scoped to this widget instance.
	 *
	 * Elementor renders the same widget more than once per page, so ids that
	 * come from a constant produce duplicated markup.
	 *
	 * @param string $suffix Id suffix.
	 * @return string
	 */
	protected function widget_dom_id( $suffix ) {
		$parts     = array( str_replace( '_', '-', (string) $this->get_name() ), (string) $suffix );
		$widget_id = (string) $this->get_id();

		if ( '' !== $widget_id ) {
			$parts[] = $widget_id;
		}

		return sanitize_html_class( implode( '-', $parts ) );
	}

	/**
	 * Get the heading id of the section rendered by this widget instance.
	 *
	 * @return string
	 */
	protected function section_heading_id() {
		return $this->widget_dom_id( 'title' );
	}

	/**
	 * Register the shared section header controls.
	 *
	 * Only the keys passed in $args are registered, so each widget keeps the
	 * editor panel order it already had while sharing the same contract.
	 *
	 * @param array $args {
	 *     Optional. Control defaults.
	 *
	 *     @type string|null $eyebrow    Eyebrow default. Null skips the control.
	 *     @type string|null $title      Title default. Null skips the control.
	 *     @type string      $title_type Title control type, `text` or `textarea`.
	 *     @type string|null $body       Body default. Null skips the control.
	 *     @type string      $body_type  Body control type, `textarea` or `wysiwyg`.
	 * }
	 * @return void
	 */
	protected function add_section_header_controls( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'    => null,
				'title'      => null,
				'title_type' => 'text',
				'body'       => null,
				'body_type'  => 'textarea',
			)
		);

		if ( null !== $args['eyebrow'] ) {
			$this->add_control(
				'eyebrow',
				array(
					'label'   => esc_html__( 'Selo', 'proenem-wordpress-theme' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => $args['eyebrow'],
				)
			);
		}

		if ( null !== $args['title'] ) {
			$this->add_control(
				'title',
				array(
					'label'       => esc_html__( 'Título', 'proenem-wordpress-theme' ),
					'type'        => 'textarea' === $args['title_type'] ? \Elementor\Controls_Manager::TEXTAREA : \Elementor\Controls_Manager::TEXT,
					'default'     => $args['title'],
					'label_block' => 'textarea' === $args['title_type'],
				)
			);
		}

		if ( null !== $args['body'] ) {
			$this->add_control(
				'body',
				array(
					'label'   => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
					'type'    => 'wysiwyg' === $args['body_type'] ? \Elementor\Controls_Manager::WYSIWYG : \Elementor\Controls_Manager::TEXTAREA,
					'default' => $args['body'],
				)
			);
		}
	}

	/**
	 * Register the shared section layout controls in a dedicated panel section.
	 *
	 * @return void
	 */
	protected function add_section_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Seção', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'tone',
			array(
				'label'       => esc_html__( 'Fundo da seção', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => array(
					'default' => esc_html__( 'Transparente', 'proenem-wordpress-theme' ),
					'surface' => esc_html__( 'Superfície', 'proenem-wordpress-theme' ),
					'brand'   => esc_html__( 'Marca', 'proenem-wordpress-theme' ),
				),
				'description' => esc_html__( 'Usa as cores publicadas da Proenem em vez de cor livre.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_section_anchor_control();

		$this->end_controls_section();
	}

	/**
	 * Register the shared section anchor control.
	 *
	 * @param string $default_value Default anchor, used by sections that already
	 *                              have a published anchor.
	 * @return void
	 */
	protected function add_section_anchor_control( $default_value = '' ) {
		$this->add_control(
			'anchor_id',
			array(
				'label'       => esc_html__( 'Âncora', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $default_value,
				'description' => esc_html__( 'Identificador para links internos, como oferta. Sem o caractere #.', 'proenem-wordpress-theme' ),
			)
		);
	}

	/**
	 * Get the sanitized section anchor of this widget instance.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $fallback Anchor used when the control is empty.
	 * @return string
	 */
	protected function section_anchor( $settings, $fallback = '' ) {
		$anchor = isset( $settings['anchor_id'] ) ? (string) $settings['anchor_id'] : '';

		if ( '' === trim( $anchor ) ) {
			$anchor = $fallback;
		}

		return sanitize_title( $anchor );
	}

	/**
	 * Register the shared attributes of a widget section wrapper.
	 *
	 * The outer element is the band: it spans the full width and owns the
	 * background. The `<key>_inner` element carries the layout classes the
	 * widget already used and keeps the content width.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $class_name Widget specific section class.
	 * @param bool   $has_title Whether the section renders its own heading.
	 * @param string $key Render attribute key.
	 * @return string
	 */
	protected function add_section_render_attributes( $settings, $class_name, $has_title = true, $key = 'section' ) {
		$this->add_render_attribute( $key, 'class', 'pro-sales-section' );
		$this->add_render_attribute( $key . '_inner', 'class', array( 'pro-sales-widget', 'pro-sales-section__inner', $class_name ) );

		$tone = isset( $settings['tone'] ) ? (string) $settings['tone'] : '';

		if ( '' !== $tone && 'default' !== $tone ) {
			$this->add_render_attribute( $key, 'class', 'pro-sales-section--tone-' . sanitize_html_class( $tone ) );
		}

		$anchor = $this->section_anchor( $settings );

		if ( '' !== $anchor ) {
			$this->add_render_attribute( $key, 'id', $anchor );
		}

		if ( $has_title ) {
			$this->add_render_attribute( $key, 'aria-labelledby', $this->section_heading_id() );
		}

		return $key;
	}

	/**
	 * Render the shared section header.
	 *
	 * @param array $settings Widget settings.
	 * @param array $args {
	 *     Optional. Markup configuration kept per widget so the current layout
	 *     does not change.
	 *
	 *     @type string $eyebrow_class Eyebrow class.
	 *     @type string $title_tag     Title tag.
	 *     @type string $title_class   Title class. Empty renders no class.
	 *     @type string $body_tag      Body tag.
	 *     @type string $body_class    Body class. Empty renders no class.
	 *     @type bool   $body_html     Whether the body accepts inline HTML.
	 * }
	 * @return void
	 */
	protected function render_section_header( $settings, array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow_class' => 'pro-sales-eyebrow',
				'title_tag'     => 'h2',
				'title_class'   => 'pro-sales-section-title',
				'body_tag'      => 'p',
				'body_class'    => '',
				'body_html'     => false,
			)
		);

		$title_tag = tag_escape( $args['title_tag'] );
		$body_tag  = tag_escape( $args['body_tag'] );

		if ( ! empty( $settings['eyebrow'] ) ) {
			printf(
				'<p class="%1$s">%2$s</p>',
				esc_attr( $args['eyebrow_class'] ),
				esc_html( $settings['eyebrow'] )
			);
		}

		if ( ! empty( $settings['title'] ) ) {
			printf(
				'<%1$s id="%2$s"%3$s>%4$s</%1$s>',
				esc_html( $title_tag ),
				esc_attr( $this->section_heading_id() ),
				'' !== $args['title_class'] ? ' class="' . esc_attr( $args['title_class'] ) . '"' : '',
				esc_html( $settings['title'] )
			);
		}

		if ( ! empty( $settings['body'] ) ) {
			printf(
				'<%1$s%2$s>%3$s</%1$s>',
				esc_html( $body_tag ),
				'' !== $args['body_class'] ? ' class="' . esc_attr( $args['body_class'] ) . '"' : '',
				$args['body_html'] ? wp_kses_post( $settings['body'] ) : esc_html( $settings['body'] )
			);
		}
	}
}

/**
 * Pro navbar widget.
 */
class Proenem_Elementor_Navbar_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_navbar';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Navbar', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-nav-menu';
	}

	/**
	 * Get widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'pro', 'navbar', 'menu', 'logo' );
	}

	/**
	 * Get available WordPress menus.
	 *
	 * @return array<int,string>
	 */
	protected function get_menu_options() {
		$options = array(
			0 => esc_html__( 'Menu principal do tema', 'proenem-wordpress-theme' ),
		);
		$menus   = wp_get_nav_menus();

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Menu', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'mode',
			array(
				'label'   => esc_html__( 'Modo', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'logo_only',
				'options' => array(
					'logo_only' => esc_html__( 'Somente logo', 'proenem-wordpress-theme' ),
					'menu'      => esc_html__( 'Menu WordPress', 'proenem-wordpress-theme' ),
				),
			)
		);

		$this->add_control(
			'menu_id',
			array(
				'label'     => esc_html__( 'Menu WordPress', 'proenem-wordpress-theme' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 0,
				'options'   => $this->get_menu_options(),
				'condition' => array(
					'mode' => 'menu',
				),
			)
		);

		$this->add_control(
			'aria_label',
			array(
				'label'   => esc_html__( 'Rótulo de acessibilidade', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Navegação da página de vendas', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'mobile_cta_enabled',
			array(
				'label'        => esc_html__( 'CTA persistente no mobile', 'proenem-wordpress-theme' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'mobile_cta_label',
			array(
				'label'       => esc_html__( 'Texto do CTA mobile', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Ver plano e preço', 'proenem-wordpress-theme' ),
				'label_block' => true,
				'condition'   => array(
					'mobile_cta_enabled' => 'yes',
				),
			)
		);

		$this->add_control(
			'mobile_cta_url',
			array(
				'label'       => esc_html__( 'Destino do CTA mobile', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array(
					'url' => proenem_get_home_cta_destination( 'plans' ),
				),
				'label_block' => true,
				'condition'   => array(
					'mobile_cta_enabled' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$mode     = $settings['mode'] ?? 'logo_only';

		proenem_render_site_navbar(
			array(
				'aria_label' => $settings['aria_label'] ?? __( 'Navegação da página de vendas', 'proenem-wordpress-theme' ),
				'context'    => 'elementor-sales',
				'logo_only'  => 'logo_only' === $mode,
				'menu_id'    => absint( $settings['menu_id'] ?? 0 ),
			)
		);

		if ( 'yes' === ( $settings['mobile_cta_enabled'] ?? '' ) ) {
			$mobile_cta_url = proenem_upgrade_home_cta_link( $settings['mobile_cta_url'] ?? array(), 'plans' );

			proenem_render_mobile_persistent_action(
				array(
					'label' => $settings['mobile_cta_label'] ?? __( 'Ver plano e preço', 'proenem-wordpress-theme' ),
					'url'   => is_array( $mobile_cta_url ) ? ( $mobile_cta_url['url'] ?? '' ) : $mobile_cta_url,
				)
			);
		}
	}
}

/**
 * Pro footer widget.
 */
class Proenem_Elementor_Footer_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_footer';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Footer', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-footer';
	}

	/**
	 * Get widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'pro', 'footer', 'rodape', 'rodapé' );
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		proenem_render_site_footer();
	}
}

/**
 * Pro offer hero widget.
 */
class Proenem_Elementor_Offer_Hero_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_offer_hero';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Hero de Oferta', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-site-title';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'eyebrow'    => esc_html__( 'Oferta especial', 'proenem-wordpress-theme' ),
				'title'      => esc_html__( 'Estude para o Enem com uma rotina completa', 'proenem-wordpress-theme' ),
				'title_type' => 'textarea',
				'body'       => esc_html__( 'Cursos, questões, simulados e acompanhamento para acelerar sua preparação.', 'proenem-wordpress-theme' ),
				'body_type'  => 'wysiwyg',
			)
		);
		$this->add_control(
			'primary_label',
			array(
				'label'   => esc_html__( 'Botão principal', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Ver planos', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'primary_url',
			array(
				'label' => esc_html__( 'Link principal', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$this->add_control(
			'secondary_label',
			array(
				'label' => esc_html__( 'Botão secundário', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'secondary_url',
			array(
				'label' => esc_html__( 'Link secundário', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$this->add_control(
			'image',
			array(
				'label' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings  = $this->get_settings_for_display();
		$image_url = ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$this->add_section_render_attributes( $settings, 'pro-sales-hero', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div class="pro-sales-hero__content">
					<?php
					$this->render_section_header(
						$settings,
						array(
							'title_class' => 'pro-sales-hero__title',
							'body_tag'    => 'div',
							'body_class'  => 'pro-sales-hero__body',
							'body_html'   => true,
						)
					);
					?>
						<div class="pro-sales-actions">
						<?php
						$this->render_link( 'primary_url', $settings['primary_url'], $settings['primary_label'], 'pro-sales-button pro-sales-button--primary' );
						$this->render_link( 'secondary_url', $settings['secondary_url'], $settings['secondary_label'], 'pro-sales-button pro-sales-button--secondary' );
						?>
						</div>
					</div>
					<?php if ( $image_url ) : ?>
						<figure class="pro-sales-hero__media">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="">
						</figure>
					<?php endif; ?>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro offer countdown widget.
	 */
class Proenem_Elementor_Offer_Countdown_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_offer_countdown';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Contador de Oferta', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-countdown';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Conteúdo', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_section_header_controls(
			array(
				'title' => esc_html__( 'Oferta por tempo limitado', 'proenem-wordpress-theme' ),
				'body'  => esc_html__( 'Garanta as condições especiais antes do encerramento da campanha.', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'deadline',
			array(
				'label'       => esc_html__( 'Data final', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'description' => esc_html__( 'Usada como informação exibida. A contagem dinâmica pode ser adicionada em uma próxima etapa.', 'proenem-wordpress-theme' ),
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->add_section_render_attributes( $settings, 'pro-sales-countdown', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div>
					<?php $this->render_section_header( $settings ); ?>
					</div>
				<?php if ( ! empty( $settings['deadline'] ) ) : ?>
						<time class="pro-sales-countdown__date" datetime="<?php echo esc_attr( $settings['deadline'] ); ?>">
							<?php echo esc_html( $settings['deadline'] ); ?>
						</time>
					<?php endif; ?>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro pricing grid widget.
	 */
class Proenem_Elementor_Pricing_Grid_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_pricing_grid';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Grade de Planos', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-price-table';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Planos', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'title' => esc_html__( 'Escolha seu plano', 'proenem-wordpress-theme' ),
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Nome', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Plano Pro', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'badge',
			array(
				'label' => esc_html__( 'Selo', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'price',
			array(
				'label'   => esc_html__( 'Preço', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'R$ 99', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'recurrence',
			array(
				'label'   => esc_html__( 'Recorrência', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '/mês', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'features',
			array(
				'label'       => esc_html__( 'Benefícios', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Um benefício por linha.', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Assinar agora', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'button_url',
			array(
				'label' => esc_html__( 'Link', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);

		$this->add_control(
			'plans',
			array(
				'label'       => esc_html__( 'Planos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$plans    = ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ? $settings['plans'] : array();
		$this->add_section_render_attributes( $settings, 'pro-sales-pricing', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<div class="pro-sales-pricing__grid">
					<?php foreach ( $plans as $index => $plan ) : ?>
							<article class="pro-sales-card pro-sales-plan">
								<?php if ( ! empty( $plan['badge'] ) ) : ?>
									<p class="pro-sales-badge"><?php echo esc_html( $plan['badge'] ); ?></p>
								<?php endif; ?>
								<h3><?php echo esc_html( $plan['name'] ?? '' ); ?></h3>
								<p class="pro-sales-plan__price">
									<span><?php echo esc_html( $plan['price'] ?? '' ); ?></span>
									<?php if ( ! empty( $plan['recurrence'] ) ) : ?>
										<small><?php echo esc_html( $plan['recurrence'] ); ?></small>
									<?php endif; ?>
								</p>
								<?php $features = $this->split_lines( $plan['features'] ?? '' ); ?>
								<?php if ( $features ) : ?>
									<ul class="pro-sales-list">
										<?php foreach ( $features as $feature ) : ?>
											<li><?php echo esc_html( $feature ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php
								$this->render_link(
									'plan_button_' . $index,
									$plan['button_url'] ?? array(),
									$plan['button_label'] ?? '',
									'pro-sales-button pro-sales-button--primary'
								);
								?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro pricing card widget.
	 */
class Proenem_Elementor_Pricing_Card_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * This widget is a card meant to be composed inside a column.
	 *
	 * @return bool
	 */
	protected function is_section_host(): bool {
		return false;
	}

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_pricing_card';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Card de Plano', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-price-list';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Plano', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'badge',
			array(
				'label' => esc_html__( 'Selo', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Nome', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Plano Pro', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'description',
			array(
				'label' => esc_html__( 'Descrição', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'price',
			array(
				'label'   => esc_html__( 'Preço', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'R$ 99', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'recurrence',
			array(
				'label'   => esc_html__( 'Recorrência', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '/mês', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'features',
			array(
				'label'       => esc_html__( 'Benefícios', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Um benefício por linha.', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Assinar agora', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'button_url',
			array(
				'label' => esc_html__( 'Link', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$features = $this->split_lines( $settings['features'] ?? '' );
		?>
			<article class="pro-sales-widget pro-sales-card pro-sales-plan">
			<?php if ( ! empty( $settings['badge'] ) ) : ?>
					<p class="pro-sales-badge"><?php echo esc_html( $settings['badge'] ); ?></p>
				<?php endif; ?>
				<h2><?php echo esc_html( $settings['name'] ?? '' ); ?></h2>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>
				<p class="pro-sales-plan__price">
					<span><?php echo esc_html( $settings['price'] ?? '' ); ?></span>
				<?php if ( ! empty( $settings['recurrence'] ) ) : ?>
						<small><?php echo esc_html( $settings['recurrence'] ); ?></small>
					<?php endif; ?>
				</p>
			<?php if ( $features ) : ?>
					<ul class="pro-sales-list">
						<?php foreach ( $features as $feature ) : ?>
							<li><?php echo esc_html( $feature ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pro-sales-button pro-sales-button--primary' ); ?>
			</article>
			<?php
	}
}

	/**
	 * Pro benefits list widget.
	 */
class Proenem_Elementor_Benefits_List_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_benefits_list';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Lista de Benefícios', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-check-circle';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Benefícios', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_section_header_controls(
			array(
				'title' => esc_html__( 'O que está incluído', 'proenem-wordpress-theme' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Benefício', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Benefício da oferta', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'body',
			array(
				'label' => esc_html__( 'Descrição', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Itens', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$this->add_section_render_attributes( $settings, 'pro-sales-benefits', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<div class="pro-sales-benefits__grid">
					<?php foreach ( $items as $item ) : ?>
							<article class="pro-sales-card pro-sales-benefit">
								<span aria-hidden="true">✓</span>
								<h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
								<?php if ( ! empty( $item['body'] ) ) : ?>
									<p><?php echo esc_html( $item['body'] ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro plans comparison widget.
	 */
class Proenem_Elementor_Plans_Comparison_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_plans_comparison';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro Comparativo de Planos', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-table';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Comparativo', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_section_header_controls(
			array(
				'title' => esc_html__( 'Compare os planos', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__( 'Colunas de planos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Um nome de plano por linha.', 'proenem-wordpress-theme' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'feature',
			array(
				'label' => esc_html__( 'Recurso', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'values',
			array(
				'label'       => esc_html__( 'Valores', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Um valor por linha, na mesma ordem das colunas.', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'rows',
			array(
				'label'       => esc_html__( 'Linhas', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ feature }}}',
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$columns  = $this->split_lines( $settings['columns'] ?? '' );
		$rows     = ! empty( $settings['rows'] ) && is_array( $settings['rows'] ) ? $settings['rows'] : array();
		$this->add_section_render_attributes( $settings, 'pro-sales-comparison', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<div class="pro-sales-comparison__scroll">
						<table>
							<thead>
								<tr>
									<th><?php esc_html_e( 'Recurso', 'proenem-wordpress-theme' ); ?></th>
								<?php foreach ( $columns as $column ) : ?>
										<th><?php echo esc_html( $column ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
							<?php foreach ( $rows as $row ) : ?>
									<?php $values = $this->split_lines( $row['values'] ?? '' ); ?>
									<tr>
										<th scope="row"><?php echo esc_html( $row['feature'] ?? '' ); ?></th>
										<?php foreach ( $columns as $index => $column ) : ?>
											<td><?php echo esc_html( $values[ $index ] ?? '' ); ?></td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro CTA widget.
	 */
class Proenem_Elementor_Cta_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_cta';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro CTA', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-call-to-action';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Chamada', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_section_header_controls(
			array(
				'title'      => esc_html__( 'Comece sua preparação hoje', 'proenem-wordpress-theme' ),
				'title_type' => 'textarea',
				'body'       => '',
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Escolher plano', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'button_url',
			array(
				'label' => esc_html__( 'Link', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$this->add_section_render_attributes( $settings, 'pro-sales-cta', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div>
					<?php $this->render_section_header( $settings, array( 'title_class' => '' ) ); ?>
					</div>
				<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pro-sales-button pro-sales-button--inverse' ); ?>
				</div>
			</section>
			<?php
	}
}

	/**
	 * Pro FAQ widget.
	 */
class Proenem_Elementor_Faq_Widget extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_faq';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro FAQ', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-help-o';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Perguntas', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_section_header_controls(
			array(
				'title' => esc_html__( 'Perguntas frequentes', 'proenem-wordpress-theme' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'question',
			array(
				'label' => esc_html__( 'Pergunta', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'answer',
			array(
				'label' => esc_html__( 'Resposta', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Itens', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ question }}}',
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$this->add_section_render_attributes( $settings, 'pro-sales-faq', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<div class="pro-sales-faq__items">
					<?php foreach ( $items as $item ) : ?>
							<details class="pro-sales-faq__item">
								<summary><?php echo esc_html( $item['question'] ?? '' ); ?></summary>
								<div><?php echo wp_kses_post( $item['answer'] ?? '' ); ?></div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
	}
}
