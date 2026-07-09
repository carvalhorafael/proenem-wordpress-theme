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

		$this->add_control(
			'eyebrow',
			array(
				'label'   => esc_html__( 'Selo', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Oferta especial', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Estude para o Enem com uma rotina completa', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'body',
			array(
				'label'   => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Cursos, questões, simulados e acompanhamento para acelerar sua preparação.', 'proenem-wordpress-theme' ),
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings  = $this->get_settings_for_display();
		$image_url = ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		?>
			<section class="pro-sales-widget pro-sales-hero">
				<div class="pro-sales-hero__content">
				<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
						<p class="pro-sales-eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></p>
					<?php endif; ?>
				<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h2 class="pro-sales-hero__title"><?php echo esc_html( $settings['title'] ); ?></h2>
					<?php endif; ?>
				<?php if ( ! empty( $settings['body'] ) ) : ?>
						<div class="pro-sales-hero__body"><?php echo wp_kses_post( $settings['body'] ); ?></div>
					<?php endif; ?>
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
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Oferta por tempo limitado', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'body',
			array(
				'label'   => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Garanta as condições especiais antes do encerramento da campanha.', 'proenem-wordpress-theme' ),
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		?>
			<section class="pro-sales-widget pro-sales-countdown">
				<div>
				<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h2 class="pro-sales-section-title"><?php echo esc_html( $settings['title'] ); ?></h2>
					<?php endif; ?>
				<?php if ( ! empty( $settings['body'] ) ) : ?>
						<p><?php echo esc_html( $settings['body'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php if ( ! empty( $settings['deadline'] ) ) : ?>
					<time class="pro-sales-countdown__date" datetime="<?php echo esc_attr( $settings['deadline'] ); ?>">
						<?php echo esc_html( $settings['deadline'] ); ?>
					</time>
				<?php endif; ?>
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

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Escolha seu plano', 'proenem-wordpress-theme' ),
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$plans    = ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ? $settings['plans'] : array();
		?>
			<section class="pro-sales-widget pro-sales-pricing">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="pro-sales-section-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>
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
			</section>
			<?php
	}
}

	/**
	 * Pro pricing card widget.
	 */
class Proenem_Elementor_Pricing_Card_Widget extends Proenem_Elementor_Sales_Widget_Base {
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
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'O que está incluído', 'proenem-wordpress-theme' ),
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		?>
			<section class="pro-sales-widget pro-sales-benefits">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="pro-sales-section-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>
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
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Compare os planos', 'proenem-wordpress-theme' ),
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
		?>
			<section class="pro-sales-widget pro-sales-comparison">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="pro-sales-section-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>
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
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Comece sua preparação hoje', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'body',
			array(
				'label' => esc_html__( 'Texto', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		?>
			<section class="pro-sales-widget pro-sales-cta">
				<div>
				<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h2><?php echo esc_html( $settings['title'] ); ?></h2>
					<?php endif; ?>
				<?php if ( ! empty( $settings['body'] ) ) : ?>
						<p><?php echo esc_html( $settings['body'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pro-sales-button pro-sales-button--inverse' ); ?>
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
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Título', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Perguntas frequentes', 'proenem-wordpress-theme' ),
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		?>
			<section class="pro-sales-widget pro-sales-faq">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="pro-sales-section-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>
				<div class="pro-sales-faq__items">
				<?php foreach ( $items as $item ) : ?>
						<details class="pro-sales-faq__item">
							<summary><?php echo esc_html( $item['question'] ?? '' ); ?></summary>
							<div><?php echo wp_kses_post( $item['answer'] ?? '' ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
			<?php
	}
}
