<?php
/**
 * Elementor landing page widgets.
 *
 * @package Proenem
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound, WordPress.Files.FileName.InvalidClassFileName -- Elementor landing page widgets share a base and a guarded loader.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared base for the generic Proenem landing page widgets.
 *
 * The widgets that extend this class are meant to be composed freely on any
 * sales page. The home widgets in `Proenem_Elementor_Home_Widget_Base` are not:
 * they carry the home copy inside the control structure and stay exclusive to
 * the home.
 */
abstract class Proenem_Elementor_Lp_Widget_Base extends Proenem_Elementor_Sales_Widget_Base {
	/**
	 * Get widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'proenem-lp' );
	}

	/**
	 * Get shared widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'proenem', 'pro', 'lp', 'landing', 'campanha' );
	}
}

/**
 * Pro LP metrics widget.
 */
class Proenem_Elementor_Lp_Metrics_Widget extends Proenem_Elementor_Lp_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_lp_metrics';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro LP Métricas', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-counter';
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
				'label' => esc_html__( 'Métricas', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'eyebrow' => '',
				'title'   => '',
				'body'    => '',
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'icon',
			array(
				'label'       => esc_html__( 'Ícone', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'description' => esc_html__( 'Opcional. Sem ícone escolhido, nada é exibido acima do número.', 'proenem-wordpress-theme' ),
			)
		);
		$accent_options = array();

		foreach ( proenem_get_brand_accents() as $accent_key => $accent ) {
			$accent_options[ $accent_key ] = $accent['label'];
		}

		$repeater->add_control(
			'icon_accent',
			array(
				'label'       => esc_html__( 'Cor do selo', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'yellow',
				'options'     => $accent_options,
				'description' => esc_html__( 'Somente cores publicadas da Proenem. Cada opção já traz a cor de ícone que garante contraste.', 'proenem-wordpress-theme' ),
				'condition'   => array(
					'icon[value]!' => '',
				),
			)
		);
		$repeater->add_control(
			'value',
			array(
				'label'   => esc_html__( 'Número', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '+44.000', 'proenem-wordpress-theme' ),
			)
		);
		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Descrição', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'alunos aprovados nas melhores universidades', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Itens', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ value }}}',
				'default'     => array(
					array(
						'value' => esc_html__( '+44.000', 'proenem-wordpress-theme' ),
						'label' => esc_html__( 'alunos aprovados nas melhores universidades', 'proenem-wordpress-theme' ),
					),
					array(
						'value' => esc_html__( '4,9/5', 'proenem-wordpress-theme' ),
						'label' => esc_html__( 'avaliação média dos alunos', 'proenem-wordpress-theme' ),
					),
					array(
						'value' => esc_html__( '12 anos', 'proenem-wordpress-theme' ),
						'label' => esc_html__( 'de experiência aprovando alunos no ENEM', 'proenem-wordpress-theme' ),
					),
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
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$this->add_section_render_attributes( $settings, 'pro-lp-metrics', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<ul class="pro-lp-metrics__grid">
					<?php foreach ( $items as $item ) : ?>
						<?php if ( empty( $item['value'] ) && empty( $item['label'] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
							<li class="pro-lp-metric">
							<?php if ( ! empty( $item['icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) : ?>
									<span class="pro-lp-metric__icon <?php echo esc_attr( $this->accent_class( $item, 'icon_accent' ) ); ?>" aria-hidden="true">
										<?php \Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="pro-lp-metric__value"><?php echo esc_html( $item['value'] ?? '' ); ?></span>
								<span class="pro-lp-metric__label"><?php echo esc_html( $item['label'] ?? '' ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
	}
}

/**
 * Pro LP offer highlight widget.
 */
class Proenem_Elementor_Lp_Offer_Highlight_Widget extends Proenem_Elementor_Lp_Widget_Base {
	/**
	 * Keep the widget out of the editor panel.
	 *
	 * Obsolete: this is a plan card without the price fields, so
	 * `pro_pricing_grid` covers the case. The class stays registered so pages
	 * that already use this widget keep rendering.
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
		return 'pro_lp_offer_highlight';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro LP Destaque de Oferta (obsoleto)', 'proenem-wordpress-theme' );
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
				'label' => esc_html__( 'Destaque de oferta', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'   => esc_html__( 'Selo de urgência', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Início hoje!', 'proenem-wordpress-theme' ),
			)
		);
		$this->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Nome da oferta', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Turma Intensiva ENEM 2026', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'summary',
			array(
				'label'       => esc_html__( 'Resumo', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Tudo o que você precisa para a reta final, em um único plano.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'features',
			array(
				'label'       => esc_html__( 'Itens incluídos', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( "Cronograma completo até o dia da prova\n4 correções de redação\nAulas com os melhores professores\nSimulados com nota TRI", 'proenem-wordpress-theme' ),
				'description' => esc_html__( 'Um item por linha.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Entrar na Turma Intensiva', 'proenem-wordpress-theme' ),
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
		$features = $this->split_lines( $settings['features'] ?? '' );
		$this->add_section_render_attributes( $settings, 'pro-lp-offer-highlight', ! empty( $settings['name'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<article class="pro-sales-card pro-lp-offer-highlight__card">
					<?php if ( ! empty( $settings['badge'] ) ) : ?>
							<p class="pro-sales-badge"><?php echo esc_html( $settings['badge'] ); ?></p>
						<?php endif; ?>
					<?php if ( ! empty( $settings['name'] ) ) : ?>
							<h2 id="<?php echo esc_attr( $this->section_heading_id() ); ?>" class="pro-lp-offer-highlight__name"><?php echo esc_html( $settings['name'] ); ?></h2>
						<?php endif; ?>
					<?php if ( ! empty( $settings['summary'] ) ) : ?>
							<p class="pro-lp-offer-highlight__summary"><?php echo esc_html( $settings['summary'] ); ?></p>
						<?php endif; ?>
					<?php if ( $features ) : ?>
							<ul class="pro-sales-list">
							<?php foreach ( $features as $feature ) : ?>
									<li><?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pen-button pen-button--primary' ); ?>
					</article>
				</div>
			</section>
			<?php
	}
}

/**
 * Pro LP spotlight widget.
 */
class Proenem_Elementor_Lp_Spotlight_Widget extends Proenem_Elementor_Lp_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_lp_spotlight';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro LP Spotlight', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-box';
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
				'label' => esc_html__( 'Spotlight', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'eyebrow'    => esc_html__( 'Plano de estudos', 'proenem-wordpress-theme' ),
				'title'      => esc_html__( 'Você não precisa mais decidir o que estudar hoje.', 'proenem-wordpress-theme' ),
				'title_type' => 'textarea',
				'body'       => esc_html__( 'Cada dia da sua semana já vem preparado: as matérias certas, na ordem certa, na intensidade certa.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'bullets',
			array(
				'label'       => esc_html__( 'Destaques', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( "Cronograma semanal até a prova\nBarras de progresso por matéria e por competência\nSaiba o que estudar em cada dia para não perder o foco", 'proenem-wordpress-theme' ),
				'description' => esc_html__( 'Um item por linha.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label' => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
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
			'image',
			array(
				'label' => esc_html__( 'Imagem', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'image_alt',
			array(
				'label'       => esc_html__( 'Texto alternativo da imagem', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Deixe vazio quando a imagem for apenas decorativa.', 'proenem-wordpress-theme' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'media_position',
			array(
				'label'   => esc_html__( 'Posição da imagem', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'end',
				'options' => array(
					'end'   => esc_html__( 'Depois do texto', 'proenem-wordpress-theme' ),
					'start' => esc_html__( 'Antes do texto', 'proenem-wordpress-theme' ),
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
		$settings   = $this->get_settings_for_display();
		$bullets    = $this->split_lines( $settings['bullets'] ?? '' );
		$image_url  = ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$class_name = 'pro-lp-spotlight';

		if ( 'start' === ( $settings['media_position'] ?? 'end' ) ) {
			$class_name .= ' pro-lp-spotlight--media-start';
		}

		if ( '' === $image_url ) {
			$class_name .= ' pro-lp-spotlight--no-media';
		}

		$this->add_section_render_attributes( $settings, $class_name, ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div class="pro-lp-spotlight__content">
					<?php $this->render_section_header( $settings, array( 'title_class' => 'pro-lp-spotlight__title' ) ); ?>
					<?php if ( $bullets ) : ?>
							<ul class="pro-sales-list pro-lp-spotlight__bullets">
							<?php foreach ( $bullets as $bullet ) : ?>
									<li><?php echo esc_html( $bullet ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pen-button pen-button--primary' ); ?>
					</div>
				<?php if ( $image_url ) : ?>
						<figure class="pro-lp-spotlight__media">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $settings['image_alt'] ?? '' ); ?>" loading="lazy" decoding="async">
						</figure>
					<?php endif; ?>
				</div>
			</section>
			<?php
	}
}

/**
 * Pro LP video story widget.
 */
class Proenem_Elementor_Lp_Video_Story_Widget extends Proenem_Elementor_Lp_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_lp_video_story';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro LP Depoimento em Vídeo', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-play-o';
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
				'label' => esc_html__( 'Depoimento em vídeo', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'eyebrow'    => esc_html__( 'História real', 'proenem-wordpress-theme' ),
				'title'      => esc_html__( 'Ela passou em Medicina conciliando com o trabalho.', 'proenem-wordpress-theme' ),
				'title_type' => 'textarea',
				'body'       => esc_html__( 'Veja como a rotina certa e a correção de redação fizeram a diferença na reta final.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_video_facade_controls();
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Quero minha vaga na Turma Intensiva', 'proenem-wordpress-theme' ),
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
		$this->add_section_render_attributes( $settings, 'pro-lp-video-story', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
					<div class="pro-lp-video-story__content">
					<?php $this->render_section_header( $settings, array( 'title_class' => 'pro-lp-video-story__title' ) ); ?>
					<?php $this->render_link( 'button_url', $settings['button_url'], $settings['button_label'], 'pen-button pen-button--primary' ); ?>
					</div>
				<?php $this->render_video_facade( $settings, '', 'pro-sales-video-stage pro-lp-video-story__stage' ); ?>
				</div>
			</section>
			<?php
	}
}

/**
 * Pro LP testimonials widget.
 */
class Proenem_Elementor_Lp_Testimonials_Widget extends Proenem_Elementor_Lp_Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'pro_lp_testimonials';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Pro LP Aprovados', 'proenem-wordpress-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-testimonial';
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
				'label' => esc_html__( 'Aprovados', 'proenem-wordpress-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_section_header_controls(
			array(
				'eyebrow'    => '',
				'title'      => esc_html__( 'Aprovados que já passaram por aqui.', 'proenem-wordpress-theme' ),
				'title_type' => 'textarea',
				'body'       => esc_html__( 'Histórias reais de quem transformou o cansaço em aprovação.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'testimonial_ids',
			array(
				'label'       => esc_html__( 'Depoimentos verificados', 'proenem-wordpress-theme' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => proenem_get_home_testimonial_options(),
				'description' => esc_html__( 'Sem seleção, os registros elegíveis mais recentes são usados. Só entram depoimentos verificados, autorizados e com relato.', 'proenem-wordpress-theme' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => esc_html__( 'Quantidade máxima', 'proenem-wordpress-theme' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'step'    => 1,
				'default' => 3,
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

		$this->add_control(
			'more_label',
			array(
				'label' => esc_html__( 'Botão', 'proenem-wordpress-theme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'more_url',
			array(
				'label' => esc_html__( 'Link do botão', 'proenem-wordpress-theme' ),
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
		$settings     = $this->get_settings_for_display();
		$limit        = max( 1, absint( $settings['limit'] ?? 3 ) );
		$testimonials = proenem_get_home_testimonials( $settings['testimonial_ids'] ?? array(), $limit );

		if ( empty( $testimonials ) ) {
			return;
		}

		$columns = in_array( (string) ( $settings['columns'] ?? '3' ), array( '2', '3', '4' ), true )
			? (string) $settings['columns']
			: '3';

		$this->add_section_render_attributes( $settings, 'pro-lp-testimonials', ! empty( $settings['title'] ) );
		?>
			<section <?php $this->print_render_attribute_string( 'section' ); ?>>
				<div <?php $this->print_render_attribute_string( 'section_inner' ); ?>>
				<?php $this->render_section_header( $settings ); ?>
					<ul class="pro-lp-testimonials__grid pro-lp-testimonials__grid--cols-<?php echo esc_attr( $columns ); ?>">
					<?php foreach ( $testimonials as $testimonial ) : ?>
							<li>
								<?php proenem_render_testimonial_card( $testimonial->ID, array(), 3 ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php $this->render_link( 'more_url', $settings['more_url'] ?? array(), $settings['more_label'] ?? '', 'pen-button pen-button--primary' ); ?>
				</div>
			</section>
			<?php
	}
}
