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
					'image'   => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				),
				'description' => esc_html__( 'As três primeiras opções usam as cores publicadas da Proenem em vez de cor livre.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'tone_image',
			array(
				'label'     => esc_html__( 'Imagem de fundo', 'proenem-wordpress-theme' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'tone' => 'image',
				),
			)
		);

		$this->add_control(
			'tone_scrim',
			array(
				'label'       => esc_html__( 'Camada de leitura', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'dark',
				'options'     => array(
					'dark'  => esc_html__( 'Escurecer a imagem, texto claro', 'proenem-wordpress-theme' ),
					'light' => esc_html__( 'Clarear a imagem, texto escuro', 'proenem-wordpress-theme' ),
					'none'  => esc_html__( 'Sem camada', 'proenem-wordpress-theme' ),
				),
				'description' => esc_html__( 'A imagem não garante contraste sozinha. Sem camada, confira a legibilidade do texto.', 'proenem-wordpress-theme' ),
				'condition'   => array(
					'tone' => 'image',
				),
			)
		);

		$this->add_section_anchor_control();

		$this->end_controls_section();
	}

	/**
	 * Check whether a media control URL is Elementor's placeholder image.
	 *
	 * @param string $url Media URL.
	 * @return bool
	 */
	protected function is_elementor_placeholder( $url ) {
		return false !== strpos( (string) $url, '/elementor/assets/images/placeholder' );
	}

	/**
	 * Register the price and reassurance controls of a plan.
	 *
	 * @param \Elementor\Controls_Stack|\Elementor\Repeater $target Control host.
	 * @return void
	 */
	protected function add_plan_price_controls( $target ) {
		$target->add_control(
			'price_prefix',
			array(
				'label'       => esc_html__( 'Prefixo do preço', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Exemplo: 12x de.', 'proenem-wordpress-theme' ),
			)
		);
		$target->add_control(
			'price_details',
			array(
				'label'       => esc_html__( 'Preço à vista', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Exemplo: ou R$ 306,90 à vista.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$target->add_control(
			'trust_items',
			array(
				'label'       => esc_html__( 'Selos de confiança', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'description' => esc_html__( 'Um por linha. Exemplo: Pagamento 100% seguro.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
	}

	/**
	 * Render one plan card.
	 *
	 * Shared by the pricing grid and the standalone pricing card so the plan
	 * contract lives in one place.
	 *
	 * @param array  $plan Plan data.
	 * @param string $link_key Render attribute key of the plan link.
	 * @param array  $args {
	 *     Optional.
	 *
	 *     @type string $classes     Extra classes for the article.
	 *     @type string $heading_tag Heading tag of the plan name.
	 *     @type string $heading_id  Heading id.
	 * }
	 * @return void
	 */
	protected function render_plan_card( array $plan, $link_key, array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'classes'     => '',
				'heading_tag' => 'h3',
				'heading_id'  => '',
			)
		);

		$heading_tag = tag_escape( $args['heading_tag'] );
		$features    = $this->split_lines( $plan['features'] ?? '' );
		$trust_items = $this->split_lines( $plan['trust_items'] ?? '' );
		$class_name  = trim( 'pro-sales-card pro-sales-plan ' . $args['classes'] );
		?>
		<article class="<?php echo esc_attr( $class_name ); ?>">
			<?php if ( ! empty( $plan['badge'] ) ) : ?>
				<p class="pro-sales-badge"><?php echo esc_html( $plan['badge'] ); ?></p>
			<?php endif; ?>
			<?php
			printf(
				'<%1$s%2$s>%3$s</%1$s>',
				esc_html( $heading_tag ),
				'' !== $args['heading_id'] ? ' id="' . esc_attr( $args['heading_id'] ) . '"' : '',
				esc_html( $plan['name'] ?? '' )
			);
			?>
			<?php if ( ! empty( $plan['description'] ) ) : ?>
				<p class="pro-sales-plan__description"><?php echo esc_html( $plan['description'] ); ?></p>
			<?php endif; ?>
			<p class="pro-sales-plan__price">
				<?php if ( ! empty( $plan['price_prefix'] ) ) : ?>
					<small class="pro-sales-plan__price-prefix"><?php echo esc_html( $plan['price_prefix'] ); ?></small>
				<?php endif; ?>
				<span><?php echo esc_html( $plan['price'] ?? '' ); ?></span>
				<?php if ( ! empty( $plan['recurrence'] ) ) : ?>
					<small><?php echo esc_html( $plan['recurrence'] ); ?></small>
				<?php endif; ?>
			</p>
			<?php if ( ! empty( $plan['price_details'] ) ) : ?>
				<p class="pro-sales-plan__price-details"><?php echo esc_html( $plan['price_details'] ); ?></p>
			<?php endif; ?>
			<?php if ( $features ) : ?>
				<ul class="pro-sales-list">
					<?php foreach ( $features as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php $this->render_link( $link_key, $plan['button_url'] ?? array(), $plan['button_label'] ?? '', 'pro-sales-button pro-sales-button--primary' ); ?>
			<?php if ( $trust_items ) : ?>
				<ul class="pro-sales-plan__trust">
					<?php foreach ( $trust_items as $trust_item ) : ?>
						<li><?php echo esc_html( $trust_item ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</article>
		<?php
	}

	/**
	 * Register the controls of a video facade.
	 *
	 * @param array $args {
	 *     Optional.
	 *
	 *     @type string $prefix    Control name prefix.
	 *     @type array  $condition Elementor condition applied to every control.
	 * }
	 * @return void
	 */
	protected function add_video_facade_controls( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'prefix'    => '',
				'condition' => array(),
			)
		);

		$prefix    = $args['prefix'];
		$condition = $args['condition'];

		$this->add_control(
			$prefix . 'video_url',
			array(
				'label'       => esc_html__( 'Link do vídeo', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'description' => esc_html__( 'O vídeo só é carregado depois que a pessoa clica em reproduzir.', 'proenem-wordpress-theme' ),
				'condition'   => $condition,
			)
		);
		$this->add_control(
			$prefix . 'poster',
			array(
				'label'       => esc_html__( 'Imagem de capa', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Use uma imagem da própria biblioteca. Sem capa local, nenhuma imagem externa é carregada antes do clique.', 'proenem-wordpress-theme' ),
				'condition'   => $condition,
			)
		);
		$this->add_control(
			$prefix . 'poster_alt',
			array(
				'label'       => esc_html__( 'Texto alternativo da capa', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => $condition,
			)
		);
		$this->add_control(
			$prefix . 'play_label',
			array(
				'label'     => esc_html__( 'Rótulo do botão de reproduzir', 'proenem-wordpress-theme' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Reproduzir o vídeo', 'proenem-wordpress-theme' ),
				'condition' => $condition,
			)
		);
	}

	/**
	 * Render a video facade.
	 *
	 * The provider embed is only requested after the click, so no third party is
	 * contacted while the page loads.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $prefix Control name prefix.
	 * @param string $class_name Stage class.
	 * @return void
	 */
	protected function render_video_facade( $settings, $prefix = '', $class_name = 'pro-sales-video-stage' ) {
		$video_url  = $settings[ $prefix . 'video_url' ]['url'] ?? '';
		$embed_url  = $video_url ? proenem_get_testimonial_video_embed_url( $video_url ) : '';
		$poster_url = ! empty( $settings[ $prefix . 'poster' ]['url'] ) && ! $this->is_elementor_placeholder( $settings[ $prefix . 'poster' ]['url'] )
			? $settings[ $prefix . 'poster' ]['url']
			: '';
		$play_label = trim( (string) ( $settings[ $prefix . 'play_label' ] ?? '' ) );

		if ( '' === $play_label ) {
			$play_label = esc_html__( 'Reproduzir o vídeo', 'proenem-wordpress-theme' );
		}

		if ( '' === $embed_url && '' === $poster_url ) {
			return;
		}
		?>
		<div class="<?php echo esc_attr( $class_name ); ?>" data-pro-lp-video>
			<?php if ( $embed_url ) : ?>
				<button
					class="pro-sales-video-stage__play"
					type="button"
					data-pro-lp-video-play
					data-embed-url="<?php echo esc_url( $embed_url ); ?>"
					aria-label="<?php echo esc_attr( $play_label ); ?>"
				>
					<?php if ( $poster_url ) : ?>
						<img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $settings[ $prefix . 'poster_alt' ] ?? '' ); ?>" loading="lazy" decoding="async">
					<?php endif; ?>
					<span class="pro-sales-video-stage__icon" aria-hidden="true"></span>
				</button>
			<?php else : ?>
				<img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $settings[ $prefix . 'poster_alt' ] ?? '' ); ?>" loading="lazy" decoding="async">
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Register a heading level control for the section title.
	 *
	 * A landing page needs exactly one `h1`, and which section owns it is an
	 * editorial decision.
	 *
	 * @param string $default_value Default tag.
	 * @return void
	 */
	protected function add_section_heading_level_control( $default_value = 'h2' ) {
		$this->add_control(
			'heading_level',
			array(
				'label'       => esc_html__( 'Nível do título', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => $default_value,
				'options'     => array(
					'h1' => esc_html__( 'h1, título principal da página', 'proenem-wordpress-theme' ),
					'h2' => esc_html__( 'h2, título de seção', 'proenem-wordpress-theme' ),
				),
				'description' => esc_html__( 'Use h1 apenas uma vez por página.', 'proenem-wordpress-theme' ),
			)
		);
	}

	/**
	 * Get the heading tag chosen for this section.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $fallback Tag used when the control is absent.
	 * @return string
	 */
	protected function section_heading_tag( $settings, $fallback = 'h2' ) {
		$level = isset( $settings['heading_level'] ) ? (string) $settings['heading_level'] : '';

		return in_array( $level, array( 'h1', 'h2' ), true ) ? $level : $fallback;
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

		$tone       = isset( $settings['tone'] ) ? (string) $settings['tone'] : '';
		$tone_image = ! empty( $settings['tone_image']['url'] ) && ! $this->is_elementor_placeholder( $settings['tone_image']['url'] )
			? $settings['tone_image']['url']
			: '';

		if ( 'image' === $tone && '' === $tone_image ) {
			$tone = 'default';
		}

		if ( '' !== $tone && 'default' !== $tone ) {
			$this->add_render_attribute( $key, 'class', 'pro-sales-section--tone-' . sanitize_html_class( $tone ) );
		}

		if ( 'image' === $tone ) {
			$scrim = isset( $settings['tone_scrim'] ) ? (string) $settings['tone_scrim'] : 'dark';
			$scrim = in_array( $scrim, array( 'dark', 'light', 'none' ), true ) ? $scrim : 'dark';

			$this->add_render_attribute( $key, 'class', 'pro-sales-section--scrim-' . $scrim );
			$this->add_render_attribute( $key, 'style', '--pro-sales-section-image: url(' . esc_url( $tone_image ) . ');' );
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
					'lp'        => esc_html__( 'Landing page: logo e um CTA', 'proenem-wordpress-theme' ),
					'menu'      => esc_html__( 'Menu WordPress', 'proenem-wordpress-theme' ),
				),
			)
		);

		$this->add_control(
			'cta_label',
			array(
				'label'       => esc_html__( 'Texto do CTA', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Garantir minha vaga', 'proenem-wordpress-theme' ),
				'label_block' => true,
				'condition'   => array(
					'mode' => 'lp',
				),
			)
		);

		$this->add_control(
			'cta_url',
			array(
				'label'       => esc_html__( 'Destino do CTA', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array(
					'url' => '#oferta',
				),
				'description' => esc_html__( 'Aceita âncora interna, como #oferta.', 'proenem-wordpress-theme' ),
				'label_block' => true,
				'condition'   => array(
					'mode' => 'lp',
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
		$cta      = array();

		if ( 'lp' === $mode ) {
			$cta = array(
				'label' => $settings['cta_label'] ?? '',
				'url'   => $settings['cta_url']['url'] ?? '',
			);
		}

		proenem_render_site_navbar(
			array(
				'aria_label' => $settings['aria_label'] ?? __( 'Navegação da página de vendas', 'proenem-wordpress-theme' ),
				'context'    => 'elementor-sales',
				'cta'        => $cta,
				'logo_only'  => 'menu' !== $mode,
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
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Rodapé', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'mode',
			array(
				'label'   => esc_html__( 'Modo', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'full',
				'options' => array(
					'full'    => esc_html__( 'Rodapé completo do site', 'proenem-wordpress-theme' ),
					'minimal' => esc_html__( 'Mínimo: logo e copyright', 'proenem-wordpress-theme' ),
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
		proenem_render_site_footer(
			array(
				'minimal' => 'minimal' === ( $this->get_settings_for_display()['mode'] ?? 'full' ),
			)
		);
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
		$this->add_section_heading_level_control( 'h1' );
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
			'microcopy',
			array(
				'label'       => esc_html__( 'Linha de confiança', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Vagas limitadas • Início hoje • Acesso imediato', 'proenem-wordpress-theme' ),
				'description' => esc_html__( 'Aparece abaixo dos botões.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'side_content',
			array(
				'label'       => esc_html__( 'Conteúdo ao lado do texto', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'cards',
				'options'     => array(
					'cards' => esc_html__( 'Cards de prova', 'proenem-wordpress-theme' ),
					'image' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
					'video' => esc_html__( 'Vídeo', 'proenem-wordpress-theme' ),
				),
				'description' => esc_html__( 'Para imagem de fundo da seção, use Fundo da seção no painel Seção.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'side_content' => 'image',
				),
			)
		);
		$this->add_control(
			'image_alt',
			array(
				'label'       => esc_html__( 'Texto alternativo da imagem', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Deixe vazio quando a imagem for apenas decorativa.', 'proenem-wordpress-theme' ),
				'label_block' => true,
				'condition'   => array(
					'side_content' => 'image',
				),
			)
		);

		$this->add_video_facade_controls(
			array(
				'condition' => array(
					'side_content' => 'video',
				),
			)
		);

		$proof_repeater = new \Elementor\Repeater();
		$proof_repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Título do card', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Cronograma pronto', 'proenem-wordpress-theme' ),
			)
		);
		$proof_repeater->add_control(
			'value',
			array(
				'label'   => esc_html__( 'Dado do card', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Semana 1 de 12', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'proof_cards',
			array(
				'label'       => esc_html__( 'Cards de prova', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $proof_repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(),
				'description' => esc_html__( 'Cartões curtos que mostram o produto em uso.', 'proenem-wordpress-theme' ),
				'condition'   => array(
					'side_content' => 'cards',
				),
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
		$settings     = $this->get_settings_for_display();
		$side_content = $settings['side_content'] ?? 'cards';
		$image_url    = ! empty( $settings['image']['url'] ) && ! $this->is_elementor_placeholder( $settings['image']['url'] )
			? $settings['image']['url']
			: '';
		$proof_cards  = ! empty( $settings['proof_cards'] ) && is_array( $settings['proof_cards'] ) ? $settings['proof_cards'] : array();
		$has_side     = ( 'cards' === $side_content && $proof_cards )
			|| ( 'image' === $side_content && '' !== $image_url )
			|| ( 'video' === $side_content && ( ! empty( $settings['video_url']['url'] ) || ! empty( $settings['poster']['url'] ) ) );
		$class_name   = 'pro-sales-hero';

		if ( ! $has_side ) {
			$class_name .= ' pro-sales-hero--no-media';
		}

		$this->add_section_render_attributes( $settings, $class_name, ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div class="pro-sales-hero__content">
					<?php
					$this->render_section_header(
						$settings,
						array(
							'title_tag'   => $this->section_heading_tag( $settings, 'h1' ),
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
					<?php if ( ! empty( $settings['microcopy'] ) ) : ?>
							<p class="pro-sales-hero__microcopy"><?php echo esc_html( $settings['microcopy'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( 'cards' === $side_content && $proof_cards ) : ?>
						<ul class="pro-sales-hero__proof">
						<?php foreach ( $proof_cards as $proof_card ) : ?>
							<?php if ( empty( $proof_card['label'] ) && empty( $proof_card['value'] ) ) : ?>
								<?php continue; ?>
							<?php endif; ?>
								<li class="pro-sales-hero__proof-card">
									<span class="pro-sales-hero__proof-label"><?php echo esc_html( $proof_card['label'] ?? '' ); ?></span>
									<strong class="pro-sales-hero__proof-value"><?php echo esc_html( $proof_card['value'] ?? '' ); ?></strong>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php elseif ( 'image' === $side_content && $image_url ) : ?>
						<figure class="pro-sales-hero__media">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $settings['image_alt'] ?? '' ); ?>">
						</figure>
					<?php elseif ( 'video' === $side_content ) : ?>
						<?php $this->render_video_facade( $settings, '', 'pro-sales-video-stage pro-sales-hero__video' ); ?>
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
				'description' => esc_html__( 'A contagem usa o fuso configurado no WordPress. Sem JavaScript, a data formatada é exibida.', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'expired_label',
			array(
				'label'       => esc_html__( 'Texto após o encerramento', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Oferta encerrada', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->end_controls_section();

		$this->add_section_layout_controls();
	}

	/**
	 * Parse the deadline control using the site timezone.
	 *
	 * @param string $value Raw control value.
	 * @return DateTimeImmutable|null
	 */
	protected function parse_deadline( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return null;
		}

		foreach ( array( 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d' ) as $format ) {
			$date = DateTimeImmutable::createFromFormat( $format, $value, wp_timezone() );

			if ( $date instanceof DateTimeImmutable ) {
				return $date;
			}
		}

		return null;
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$deadline = $this->parse_deadline( $settings['deadline'] ?? '' );
		$this->add_section_render_attributes( $settings, 'pro-sales-countdown', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div>
					<?php $this->render_section_header( $settings ); ?>
					</div>
				<?php if ( $deadline ) : ?>
						<time
							class="pro-sales-countdown__date"
							datetime="<?php echo esc_attr( $deadline->format( DATE_ATOM ) ); ?>"
							data-pro-countdown="<?php echo esc_attr( $deadline->format( DATE_ATOM ) ); ?>"
							data-pro-countdown-expired="<?php echo esc_attr( $settings['expired_label'] ?? '' ); ?>"
						>
							<span class="pro-sales-countdown__fallback" data-pro-countdown-fallback>
								<?php echo esc_html( wp_date( 'd/m/Y H\hi', $deadline->getTimestamp() ) ); ?>
							</span>
							<span class="pro-sales-countdown__units" data-pro-countdown-units hidden>
								<span class="pro-sales-countdown__unit">
									<strong data-pro-countdown-days>00</strong>
									<small><?php esc_html_e( 'dias', 'proenem-wordpress-theme' ); ?></small>
								</span>
								<span class="pro-sales-countdown__unit">
									<strong data-pro-countdown-hours>00</strong>
									<small><?php esc_html_e( 'horas', 'proenem-wordpress-theme' ); ?></small>
								</span>
								<span class="pro-sales-countdown__unit">
									<strong data-pro-countdown-minutes>00</strong>
									<small><?php esc_html_e( 'min', 'proenem-wordpress-theme' ); ?></small>
								</span>
							</span>
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
				'eyebrow' => '',
				'title'   => esc_html__( 'Escolha seu plano', 'proenem-wordpress-theme' ),
				'body'    => '',
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
		$this->add_plan_price_controls( $repeater );
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
					<div class="pro-sales-pricing__grid<?php echo 1 === count( $plans ) ? ' pro-sales-pricing__grid--single' : ''; ?>">
					<?php foreach ( $plans as $index => $plan ) : ?>
						<?php $this->render_plan_card( $plan, 'plan_button_' . $index ); ?>
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
	 * Keep the widget out of the editor panel.
	 *
	 * Obsolete: `pro_pricing_grid` with a single plan renders the same card and
	 * also brings the section header. The class stays registered so pages that
	 * already use this widget keep rendering.
	 *
	 * @return bool
	 */
	public function show_in_panel(): bool {
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
		return esc_html__( 'Pro Card de Plano (obsoleto)', 'proenem-wordpress-theme' );
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
		$this->add_plan_price_controls( $this );
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

		$this->render_plan_card(
			$settings,
			'button_url',
			array(
				'classes'     => 'pro-sales-widget pro-sales-plan--standalone',
				'heading_tag' => 'h2',
				'heading_id'  => $this->section_heading_id(),
			)
		);
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
				'eyebrow' => '',
				'title'   => esc_html__( 'O que está incluído', 'proenem-wordpress-theme' ),
				'body'    => '',
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => esc_html__( 'Colunas', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => esc_html__( '2 colunas', 'proenem-wordpress-theme' ),
					'3' => esc_html__( '3 colunas', 'proenem-wordpress-theme' ),
					'4' => esc_html__( '4 colunas', 'proenem-wordpress-theme' ),
				),
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
		$repeater->add_control(
			'icon',
			array(
				'label'       => esc_html__( 'Ícone', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'description' => esc_html__( 'Escolha na biblioteca de ícones ou envie um SVG próprio. Sem ícone, o item mantém o marcador padrão.', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'highlight',
			array(
				'label'        => esc_html__( 'Item em destaque', 'proenem-wordpress-theme' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$repeater->add_control(
			'badge',
			array(
				'label'     => esc_html__( 'Selo do destaque', 'proenem-wordpress-theme' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Destaque', 'proenem-wordpress-theme' ),
				'condition' => array(
					'highlight' => 'yes',
				),
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
		$columns  = in_array( (string) ( $settings['columns'] ?? '3' ), array( '2', '3', '4' ), true ) ? (string) $settings['columns'] : '3';
		$this->add_section_render_attributes( $settings, 'pro-sales-benefits', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<div class="pro-sales-benefits__grid pro-sales-benefits__grid--cols-<?php echo esc_attr( $columns ); ?>">
					<?php foreach ( $items as $item ) : ?>
						<?php
						$is_highlight   = 'yes' === ( $item['highlight'] ?? '' );
						$benefit_class  = 'pro-sales-card pro-sales-benefit';
						$benefit_class .= $is_highlight ? ' pro-sales-benefit--highlight' : '';
						$has_icon       = ! empty( $item['icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' );
						?>
							<article class="<?php echo esc_attr( $benefit_class ); ?>">
								<?php if ( $is_highlight && ! empty( $item['badge'] ) ) : ?>
									<p class="pro-sales-badge"><?php echo esc_html( $item['badge'] ); ?></p>
								<?php endif; ?>
								<span class="pro-sales-benefit__icon" aria-hidden="true">
									<?php
									if ( $has_icon ) {
										\Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
									} else {
										echo '&#10003;';
									}
									?>
								</span>
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
				'eyebrow'    => '',
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
		$this->add_control(
			'microcopy',
			array(
				'label'       => esc_html__( 'Linha de confiança', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Aparece abaixo do botão.', 'proenem-wordpress-theme' ),
				'label_block' => true,
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
					<div class="pro-sales-cta__content">
					<?php
					$this->render_section_header(
						$settings,
						array(
							'title_class' => 'pro-sales-cta__title',
							'body_class'  => 'pro-sales-cta__body',
						)
					);
					?>
					</div>
					<div class="pro-sales-cta__action">
					<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pro-sales-button pro-sales-button--inverse pro-sales-button--lg' ); ?>
					<?php if ( ! empty( $settings['microcopy'] ) ) : ?>
							<p class="pro-sales-cta__microcopy"><?php echo esc_html( $settings['microcopy'] ); ?></p>
						<?php endif; ?>
					</div>
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
