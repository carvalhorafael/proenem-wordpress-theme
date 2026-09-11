<?php
/**
 * Theme setup tests.
 *
 * @package Proenem
 */

/**
 * Tests core theme setup contracts.
 */
class ThemeSetupTest extends WP_UnitTestCase {
	/**
	 * Theme supports should be registered.
	 *
	 * @return void
	 */
	public function test_theme_supports_are_registered() {
		$this->assertTrue( current_theme_supports( 'title-tag' ) );
		$this->assertTrue( current_theme_supports( 'post-thumbnails' ) );
		$this->assertTrue( current_theme_supports( 'editor-styles' ) );
	}

	/**
	 * Navigation locations should be registered.
	 *
	 * @return void
	 */
	public function test_navigation_locations_are_registered() {
		$locations = get_registered_nav_menus();

		$this->assertArrayHasKey( 'primary', $locations );
		$this->assertArrayHasKey( 'footer-subjects', $locations );
		$this->assertArrayHasKey( 'footer-answer-keys', $locations );
		$this->assertArrayHasKey( 'footer-tools', $locations );
		$this->assertArrayHasKey( 'footer-classes', $locations );
		$this->assertArrayHasKey( 'footer-legal', $locations );
		$this->assertArrayNotHasKey( 'footer', $locations );
	}

	/**
	 * Footer widget areas should be registered for configurable mixed content.
	 *
	 * @return void
	 */
	public function test_footer_widget_areas_are_registered() {
		global $wp_registered_sidebars;

		$this->assertArrayHasKey( 'footer-social', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-trust', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-payment', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'footer-company-info', $wp_registered_sidebars );
		$this->assertArrayHasKey( 'testimonial-page-footer', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-1', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-2', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-3', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'footer-bottom', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'home-footer-platform', $wp_registered_sidebars );
		$this->assertArrayNotHasKey( 'home-footer-support', $wp_registered_sidebars );
	}

	/**
	 * Footer columns should expose the expected configurable menu locations.
	 *
	 * @return void
	 */
	public function test_footer_menu_columns_are_declared() {
		$this->assertSame(
			array(
				'footer-subjects'    => 'Matérias lecionadas',
				'footer-answer-keys' => 'Gabaritos',
				'footer-tools'       => 'Ferramentas',
			),
			proenem_get_footer_menu_columns()
		);
	}

	/**
	 * Footer scripts should be configurable through the Customizer.
	 *
	 * @return void
	 */
	public function test_footer_scripts_customizer_hooks_are_registered() {
		$this->assertSame( 10, has_action( 'customize_register', 'proenem_customize_register' ) );
		$this->assertSame( 20, has_action( 'wp_footer', 'proenem_render_footer_scripts' ) );
	}

	/**
	 * Footer scripts should render from the saved theme mod.
	 *
	 * @return void
	 */
	public function test_footer_scripts_are_rendered_on_wp_footer() {
		$script = '<script id="proenem-support-button">window.proenemSupport = true;</script>';

		set_theme_mod( 'proenem_footer_scripts', $script );

		ob_start();
		proenem_render_footer_scripts();
		$output = ob_get_clean();

		remove_theme_mod( 'proenem_footer_scripts' );

		$this->assertStringContainsString( $script, $output );
	}

	/**
	 * Testimonials links should use the public approval listing slug.
	 *
	 * @return void
	 */
	public function test_testimonials_url_uses_approved_students_slug() {
		$this->assertSame( '/aprovados/', wp_parse_url( proenem_get_testimonials_url(), PHP_URL_PATH ) );
	}

	/**
	 * Testimonial image orientation should follow attachment metadata.
	 *
	 * @return void
	 */
	public function test_testimonial_portrait_detection_uses_featured_image_dimensions() {
		$post_id       = self::factory()->post->create();
		$attachment_id = self::factory()->post->create(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
			)
		);

		$this->assertFalse( proenem_testimonial_has_portrait_image( $post_id ) );

		update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
		wp_update_attachment_metadata(
			$attachment_id,
			array(
				'width'  => 480,
				'height' => 720,
			)
		);
		$this->assertTrue( proenem_testimonial_has_portrait_image( $post_id ) );

		wp_update_attachment_metadata(
			$attachment_id,
			array(
				'width'  => 1280,
				'height' => 720,
			)
		);
		$this->assertFalse( proenem_testimonial_has_portrait_image( $post_id ) );
	}

	/**
	 * Testimonial cards should expose the heading level required by their section.
	 *
	 * @return void
	 */
	public function test_testimonial_card_supports_related_section_heading_level() {
		$post_id = self::factory()->post->create(
			array(
				'post_title' => 'Estudante Teste',
			)
		);

		ob_start();
		proenem_render_testimonial_card( $post_id );
		$listing_card = ob_get_clean();

		ob_start();
		proenem_render_testimonial_card( $post_id, array(), 3 );
		$related_card = ob_get_clean();

		$this->assertStringContainsString( '<h4>Estudante Teste</h4>', $listing_card );
		$this->assertStringContainsString( '<h3>Estudante Teste</h3>', $related_card );
		$this->assertStringNotContainsString( '<h4>Estudante Teste</h4>', $related_card );
	}

	/**
	 * Free Materials fallbacks should expose the expected portable identifiers.
	 *
	 * @return void
	 */
	public function test_free_materials_contract_fallbacks_are_available() {
		$this->assertSame( 'material_gratuito', proenem_get_free_materials_post_type() );
		$this->assertSame( 'material_categoria', proenem_get_free_materials_taxonomy() );
		$this->assertSame( '_executive_signal_material_capture_label', proenem_get_free_materials_cta_label_meta_key() );
		$this->assertSame( '_brevo_leads_capture_delivery_url', proenem_get_free_materials_delivery_url_meta_key() );
	}

	/**
	 * Testimonials fallbacks should expose the expected portable identifiers.
	 *
	 * @return void
	 */
	public function test_testimonials_contract_fallbacks_are_available() {
		$this->assertSame( 'depoimento', proenem_get_testimonials_post_type() );
		$this->assertSame( 'depoimento_categoria', proenem_get_testimonials_taxonomy() );
		$this->assertSame( '_testimonials_video_url', proenem_get_testimonials_video_url_meta_key() );
		$this->assertSame( '_testimonials_course', proenem_get_testimonials_course_meta_key() );
		$this->assertSame( '_testimonials_institution', proenem_get_testimonials_institution_meta_key() );
		$this->assertSame( '_testimonials_approval_year', proenem_get_testimonials_approval_year_meta_key() );
		$this->assertSame( '_testimonials_preparation_time', proenem_get_testimonials_preparation_time_meta_key() );
		$this->assertSame( '_testimonials_main_tip', proenem_get_testimonials_main_tip_meta_key() );
		$this->assertSame( '_testimonials_home_proof_enabled', proenem_get_testimonials_home_proof_enabled_meta_key() );
		$this->assertSame( home_url( '/aprovados/' ), proenem_get_testimonials_url() );
		$this->assertFalse( proenem_testimonials_home_proof_is_available() );
		$this->assertNull( proenem_get_featured_testimonial() );
		$this->assertSame( array(), proenem_get_testimonials_hero_selection() );
	}

	/**
	 * Unavailable proof data should produce no anonymous fallback markup.
	 *
	 * @return void
	 */
	public function test_home_proof_requires_the_verified_plugin_contract() {
		$this->assertSame( array(), proenem_get_home_proof_testimonials() );
		$this->assertSame( array(), proenem_get_home_testimonials() );
		$this->assertSame( '+ de 40.000 aprovados em universidades públicas', proenem_normalize_home_proof_copy( 'Aprovações verificadas de alunos da Proenem', 'title' ) );
		$this->assertSame( 'Conheça histórias de alunos que estudaram com a Proenem.', proenem_normalize_home_proof_copy( 'Mais de 40 mil alunos já foram aprovados com a Proenem. Conheça algumas histórias.', 'testimonials' ) );
		$this->assertSame( 'Conheça histórias de alunos que estudaram com a Proenem.', proenem_normalize_home_proof_copy( 'Mais de 40 mil alunos já foram aprovados com a ProEnem. Conheça algumas histórias.', 'testimonials' ) );

		ob_start();
		proenem_render_home_proof_section( array() );
		$output = ob_get_clean();

		$this->assertSame( '', $output );

		ob_start();
		proenem_render_home_testimonials_section( array() );
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * The front page must honour a template chosen in the editor.
	 *
	 * A hierarquia do WordPress consulta `front-page.php` antes do modelo da
	 * pagina, entao um `front-page.php` incondicional torna o seletor de modelo
	 * inerte na home: trocar o modelo no editor nao tem efeito nenhum. Foi o que
	 * aconteceu com as variantes de conversao, que existem justamente para serem
	 * trocadas sem deploy. Este teste guarda a condicao.
	 *
	 * @return void
	 */
	public function test_front_page_honours_the_assigned_page_template() {
		$source = (string) file_get_contents( PROENEM_THEME_DIR . '/front-page.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString(
			'get_page_template_slug',
			$source,
			'front-page.php deve consultar o modelo escolhido na pagina'
		);

		$this->assertStringContainsString(
			'locate_template( $proenem_front_template',
			$source,
			'front-page.php deve carregar o modelo escolhido quando ele existe'
		);

		$this->assertStringContainsString(
			"locate_template( 'page-templates/home.php'",
			$source,
			'front-page.php deve manter a home como padrao quando nao ha modelo escolhido'
		);
	}

	/**
	 * Get the files that declare Elementor widgets.
	 *
	 * @return string[]
	 */
	private function get_widget_source_files() {
		return array(
			PROENEM_THEME_DIR . '/inc/class-proenem-elementor-sales-widget-base.php',
			PROENEM_THEME_DIR . '/inc/class-proenem-elementor-lp-widget-base.php',
			PROENEM_THEME_DIR . '/inc/class-proenem-elementor-home-widget-base.php',
		);
	}

	/**
	 * Get the widget technical names declared by the theme.
	 *
	 * Elementor is not loaded in the PHPUnit environment, so the widget classes
	 * cannot be instantiated. The names are read from the sources instead, which
	 * keeps a single source of truth.
	 *
	 * @return string[]
	 */
	private function get_declared_widget_names() {
		$names = array();

		foreach ( $this->get_widget_source_files() as $file ) {
			$source = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			preg_match_all( "/return '(pro_[a-z0-9_]+)';/", $source, $matches );

			$names = array_merge( $names, $matches[1] );
		}

		return array_values( array_unique( $names ) );
	}

	/**
	 * Get the widget technical names of every widget in a landing page kit.
	 *
	 * @param string $kit_file Kit file name inside docs/elementor.
	 * @return string[]
	 */
	private function get_kit_widget_names( $kit_file ) {
		$kit = json_decode(
			(string) file_get_contents( PROENEM_THEME_DIR . '/docs/elementor/' . $kit_file ), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			true
		);

		$this->assertIsArray( $kit, $kit_file . ' deve ser um JSON valido.' );
		$this->assertSame( 'elementor_canvas', $kit['page_settings']['template'] ?? '', $kit_file );

		$stack   = $kit['content'] ?? array();
		$widgets = array();

		$this->assertNotEmpty( $stack, $kit_file . ' deve ter conteudo.' );

		while ( $stack ) {
			$element = array_pop( $stack );

			if ( 'widget' === ( $element['elType'] ?? '' ) ) {
				$widgets[] = $element['widgetType'] ?? '';
			}

			$stack = array_merge( $stack, $element['elements'] ?? array() );
		}

		return $widgets;
	}

	/**
	 * Landing page kits should only reference widgets the theme declares.
	 *
	 * A kit that names a widget the theme does not declare imports as an empty
	 * section, which is the failure this guards against.
	 *
	 * @return void
	 */
	public function test_elementor_lp_kits_only_use_declared_widgets() {
		$declared = $this->get_declared_widget_names();

		$this->assertNotEmpty( $declared );

		foreach ( array( 'proenem-lp-oferta-completa.json', 'proenem-lp-diferencial-em-foco.json' ) as $kit_file ) {
			$this->assertFileExists( PROENEM_THEME_DIR . '/docs/elementor/' . $kit_file );

			$widgets = $this->get_kit_widget_names( $kit_file );

			$this->assertNotEmpty( $widgets, $kit_file . ' deve conter widgets.' );

			foreach ( $widgets as $widget_name ) {
				$this->assertContains(
					$widget_name,
					$declared,
					$kit_file . ' referencia o widget nao declarado ' . $widget_name
				);
			}
		}
	}

	/**
	 * No widget should be retired in place, hidden from the panel.
	 *
	 * Dois widgets ficaram assim por um tempo: com "(obsoleto)" no titulo e
	 * `show_in_panel()` falso. O efeito foi pior que o pretendido, porque um
	 * widget invisivel no painel continua no codigo, nos testes e na
	 * documentacao sem ninguem exercitar. Aposentar agora significa remover.
	 *
	 * @return void
	 */
	public function test_no_widget_is_hidden_from_the_panel() {
		foreach ( $this->get_widget_source_files() as $source_file ) {
			$source = (string) file_get_contents( $source_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			$this->assertStringNotContainsString(
				'show_in_panel',
				$source,
				basename( $source_file ) . ' nao deve esconder widget do painel: aposentar e remover'
			);

			$this->assertStringNotContainsString(
				'obsoleto',
				$source,
				basename( $source_file ) . ' nao deve marcar widget como obsoleto: aposentar e remover'
			);
		}
	}

	/**
	 * Landing page kits should not ship home only widgets.
	 *
	 * @return void
	 */
	public function test_elementor_lp_kits_avoid_home_widgets() {
		foreach ( array( 'proenem-lp-oferta-completa.json', 'proenem-lp-diferencial-em-foco.json' ) as $kit_file ) {
			$widgets = $this->get_kit_widget_names( $kit_file );

			foreach ( $widgets as $widget_name ) {
				$this->assertStringStartsNotWith(
					'pro_home_',
					$widget_name,
					$kit_file . ' nao deve usar widget exclusivo da home: ' . $widget_name
				);
			}
		}
	}

	/**
	 * Elementor import data should not ship anonymous proof media or claims.
	 *
	 * @return void
	 */
	public function test_elementor_home_model_uses_the_verified_proof_contract() {
		$model = json_decode( (string) file_get_contents( PROENEM_THEME_DIR . '/docs/elementor/proenem-home.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$stack = $model['content'] ?? array();
		$proof = null;

		while ( $stack ) {
			$element = array_pop( $stack );

			if ( 'pro_home_proof' === ( $element['widgetType'] ?? '' ) ) {
				$proof = $element;
				break;
			}

			$stack = array_merge( $stack, $element['elements'] ?? array() );
		}

		$this->assertIsArray( $proof );
		$this->assertArrayNotHasKey( 'student_images', $proof['settings'] );
		$this->assertArrayNotHasKey( 'logos', $proof['settings'] );
		$this->assertSame( array(), $proof['settings']['testimonial_ids'] );
		$this->assertSame( '+ de 40.000 aprovados em universidades públicas', $proof['settings']['title'] );
	}

	/**
	 * Elementor import data should expose only the current paid home plan.
	 *
	 * @return void
	 */
	public function test_elementor_home_model_uses_the_current_plan_contract() {
		$model   = json_decode( (string) file_get_contents( PROENEM_THEME_DIR . '/docs/elementor/proenem-home.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$stack   = $model['content'] ?? array();
		$pricing = null;

		while ( $stack ) {
			$element = array_pop( $stack );

			if ( 'pro_home_pricing' === ( $element['widgetType'] ?? '' ) ) {
				$pricing = $element;
				break;
			}

			$stack = array_merge( $stack, $element['elements'] ?? array() );
		}

		$this->assertIsArray( $pricing );
		$this->assertSame( array( 'Turma Intensiva 2026' ), array_column( $pricing['settings']['plans'], 'name' ) );
	}

	/**
	 * Elementor testimonial data should come from eligible plugin records.
	 *
	 * @return void
	 */
	public function test_elementor_home_model_uses_the_verified_testimonials_contract() {
		$model        = json_decode( (string) file_get_contents( PROENEM_THEME_DIR . '/docs/elementor/proenem-home.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$stack        = $model['content'] ?? array();
		$testimonials = null;

		while ( $stack ) {
			$element = array_pop( $stack );

			if ( 'pro_home_testimonials' === ( $element['widgetType'] ?? '' ) ) {
				$testimonials = $element;
				break;
			}

			$stack = array_merge( $stack, $element['elements'] ?? array() );
		}

		$this->assertIsArray( $testimonials );
		$this->assertArrayNotHasKey( 'testimonials', $testimonials['settings'] );
		$this->assertSame( array(), $testimonials['settings']['testimonial_ids'] );
	}

	/**
	 * The free materials listing should be exposed as an explicit page template.
	 *
	 * @return void
	 */
	public function test_free_materials_page_template_exists() {
		$this->assertFileExists( PROENEM_THEME_DIR . '/page-templates/free-materials.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/single-material_gratuito.php' );
	}

	/**
	 * Navbar actions should preserve direct submenu items from WordPress menus.
	 *
	 * @return void
	 */
	public function test_navbar_actions_include_submenu_items() {
		$menu_id = wp_create_nav_menu( 'Proenem test menu' );
		$parent  = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'   => 'Entrar',
				'menu-item-url'     => '#',
				'menu-item-status'  => 'publish',
				'menu-item-classes' => 'pen-navbar-action pen-navbar-action-secondary',
			)
		);

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => 'Acessar Proenem',
				'menu-item-url'       => 'https://app.proenem.com.br/',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => 'Acessar Promedicina',
				'menu-item-url'       => 'https://app.promedicina.com.br/',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
			)
		);

		$navigation = proenem_get_primary_navigation_items( 'site', $menu_id );

		$this->assertCount( 1, $navigation['actions'] );
		$this->assertSame( 'Entrar', $navigation['actions'][0]['label'] );
		$this->assertSame( '#', $navigation['actions'][0]['url'] );
		$this->assertCount( 2, $navigation['actions'][0]['children'] );
		$this->assertSame( 'Acessar Proenem', $navigation['actions'][0]['children'][0]['label'] );
		$this->assertSame( 'https://app.proenem.com.br/', $navigation['actions'][0]['children'][0]['url'] );
		$this->assertSame( 'Acessar Promedicina', $navigation['actions'][0]['children'][1]['label'] );
		$this->assertSame( 'https://app.promedicina.com.br/', $navigation['actions'][0]['children'][1]['url'] );
	}

	/**
	 * Navbar should not invent items when the primary menu location is empty.
	 *
	 * @return void
	 */
	public function test_navbar_does_not_fallback_when_location_is_empty() {
		set_theme_mod( 'nav_menu_locations', array() );

		$navigation = proenem_get_primary_navigation_items( 'site' );

		$this->assertSame( array(), $navigation['links'] );
		$this->assertSame( array(), $navigation['actions'] );
	}

	/**
	 * Navbar actions should only come from menu items with action classes.
	 *
	 * @return void
	 */
	public function test_navbar_does_not_fallback_when_action_classes_are_missing() {
		$menu_id = wp_create_nav_menu( 'Proenem menu without actions' );

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Planos',
				'menu-item-url'    => '#planos',
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Entrar',
				'menu-item-url'    => '#entrar',
				'menu-item-status' => 'publish',
			)
		);

		$navigation = proenem_get_primary_navigation_items( 'site', $menu_id );

		$this->assertSame( array(), $navigation['actions'] );
		$this->assertSame( 'Planos', $navigation['links'][0]['label'] );
		$this->assertSame( 'Entrar', $navigation['links'][1]['label'] );
	}

	/**
	 * Navbar should preserve literal hashes saved in the WordPress menu.
	 *
	 * @return void
	 */
	public function test_navbar_preserves_persisted_hash_destinations() {
		$menu_id = wp_create_nav_menu( 'Proenem conversion menu' );

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Planos',
				'menu-item-url'    => '#',
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'   => 'Comece grátis',
				'menu-item-url'     => '#',
				'menu-item-status'  => 'publish',
				'menu-item-classes' => 'pen-navbar-action pen-navbar-action-primary',
			)
		);

		$navigation = proenem_get_primary_navigation_items( 'site', $menu_id );

		$this->assertSame( '#', $navigation['links'][0]['url'] );
		$this->assertSame( '#', $navigation['actions'][0]['url'] );
	}

	/**
	 * Mobile persistent action should use the canonical plans contract.
	 *
	 * @return void
	 */
	public function test_mobile_persistent_action_uses_plans_destination() {
		ob_start();
		proenem_render_mobile_persistent_action();
		$markup = ob_get_clean();

		$this->assertStringContainsString( 'data-pro-mobile-persistent-action', $markup );
		$this->assertStringContainsString( 'data-scroll-threshold="600"', $markup );
		$this->assertStringContainsString( home_url( '/#planos' ), $markup );
		$this->assertStringContainsString( 'Ver planos e preços', $markup );
	}

	/**
	 * Legacy advanced-plan checkout URLs should resolve to the approved page.
	 *
	 * @return void
	 */
	public function test_legacy_advanced_checkout_is_upgraded() {
		$legacy_link  = array( 'url' => 'https://pay.hotmart.com/X99453521F?off=legacy' );
		$updated_link = proenem_upgrade_home_cta_link( $legacy_link, 'advanced' );

		$this->assertSame( 'https://medicina.proenem.com.br/', proenem_get_home_cta_destination( 'advanced' ) );
		$this->assertSame( 'https://medicina.proenem.com.br/', $updated_link['url'] );
	}

	/**
	 * Legacy free destinations should converge on the paid-plan section.
	 *
	 * @return void
	 */
	public function test_legacy_free_destinations_are_upgraded_to_plans() {
		$signup_link    = array( 'url' => 'https://estude.proenem.com.br/signup' );
		$questions_link = array( 'url' => 'https://estude.proenem.com.br/treino/questoes' );
		$plans_url      = proenem_get_home_cta_destination( 'plans' );

		$this->assertSame( $plans_url, proenem_upgrade_home_cta_link( $signup_link, 'plans' )['url'] );
		$this->assertSame( $plans_url, proenem_upgrade_home_cta_link( $questions_link, 'plans' )['url'] );
	}

	/**
	 * The testimonials listing should be exposed as an explicit page template.
	 *
	 * @return void
	 */
	public function test_testimonials_page_template_exists() {
		$this->assertFileExists( PROENEM_THEME_DIR . '/page-templates/testimonials.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/single-depoimento.php' );
	}

	/**
	 * Testimonial filters should not create a complementary landmark inside the results region.
	 *
	 * @return void
	 */
	public function test_testimonials_filters_avoid_nested_landmarks() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/page-templates/testimonials.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( '<div class="pro-materials-layout__sidebar"', $template );
		$this->assertStringNotContainsString( '<aside class="pro-materials-layout__sidebar"', $template );
	}

	/**
	 * The approved-students listing should not load styles and scripts it cannot use.
	 *
	 * @return void
	 */
	public function test_testimonials_listing_dequeues_unrelated_assets() {
		$page_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);

		update_post_meta( $page_id, '_wp_page_template', 'page-templates/testimonials.php' );
		$this->go_to( get_permalink( $page_id ) );

		wp_enqueue_style( 'wp-block-library', 'https://example.com/block-library.css', array(), '1.0.0' );
		wp_enqueue_style( 'crm-leads-capture-free-material', 'https://example.com/capture.css', array(), '1.0.0' );
		wp_enqueue_script( 'crm-leads-capture-free-material', 'https://example.com/capture.js', array(), '1.0.0', true );

		proenem_dequeue_custom_template_block_assets();
		proenem_dequeue_unused_capture_assets();

		$this->assertFalse( wp_style_is( 'wp-block-library', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'crm-leads-capture-free-material', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'crm-leads-capture-free-material', 'enqueued' ) );

		add_action( 'wp_head', 'print_emoji_detection_script', 7 );
		add_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
		add_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	/**
	 * Individual stories with Gutenberg content should retain block styles.
	 *
	 * @return void
	 */
	public function test_testimonial_single_with_blocks_retains_block_styles() {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => '<!-- wp:paragraph --><p>História em blocos.</p><!-- /wp:paragraph -->',
				'post_status'  => 'publish',
			)
		);

		$this->assertTrue( proenem_testimonial_uses_blocks( $post_id ) );
	}

	/**
	 * Individual stories without Gutenberg blocks should drop block styles.
	 *
	 * @return void
	 */
	public function test_testimonial_single_without_blocks_dequeues_block_styles() {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'História em texto clássico.',
				'post_status'  => 'publish',
			)
		);

		$this->assertFalse( proenem_testimonial_uses_blocks( $post_id ) );
	}

	/**
	 * Approved-students surfaces should use native emoji rendering.
	 *
	 * @return void
	 */
	public function test_testimonials_disable_wordpress_emoji_assets() {
		$page_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);

		update_post_meta( $page_id, '_wp_page_template', 'page-templates/testimonials.php' );
		$this->go_to( get_permalink( $page_id ) );

		proenem_disable_approved_students_emoji_assets();

		$this->assertFalse( has_action( 'wp_head', 'print_emoji_detection_script' ) );
		$this->assertFalse( has_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' ) );
		$this->assertFalse( has_action( 'wp_print_styles', 'print_emoji_styles' ) );

		add_action( 'wp_head', 'print_emoji_detection_script', 7 );
		add_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
		add_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	/**
	 * The individual testimonial should show an explicit excerpt only in its hero.
	 *
	 * @return void
	 */
	public function test_testimonial_single_uses_explicit_excerpt_without_repeating_it_in_aside() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/single-depoimento.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringNotContainsString( 'proenem_get_testimonial_quote', $template );
		$this->assertStringContainsString( 'has_excerpt( $testimonial_id )', $template );
		$this->assertSame( 1, substr_count( $template, 'pro-testimonial-single__excerpt' ) );
		$this->assertSame( 1, substr_count( $template, 'the_content();' ) );
		$this->assertStringContainsString( "esc_html_e( 'Mural de aprovados'", $template );
		$this->assertStringContainsString( 'href="#pro-testimonial-story"', $template );
		$this->assertStringContainsString( "__( '%1\$s em %2\$s'", $template );
		$this->assertStringContainsString( "__( 'A história de %s'", $template );
		$this->assertStringContainsString( 'id="pro-testimonial-story"', $template );
	}

	/**
	 * The individual testimonial should expose its configurable footer widget area.
	 *
	 * @return void
	 */
	public function test_testimonial_single_renders_footer_widget_area() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/single-depoimento.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( "is_active_sidebar( 'testimonial-page-footer' )", $template );
		$this->assertStringContainsString( "dynamic_sidebar( 'testimonial-page-footer' )", $template );
	}

	/**
	 * The testimonial approval card should use the structured plugin fields.
	 *
	 * @return void
	 */
	public function test_testimonial_single_renders_structured_approval_card() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/single-depoimento.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$script   = (string) file_get_contents( PROENEM_THEME_DIR . '/src/main.js' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'proenem_get_testimonial_placement', $template );
		$this->assertStringContainsString( 'proenem_get_testimonial_course', $template );
		$this->assertStringContainsString( 'proenem_get_testimonial_institution', $template );
		$this->assertStringContainsString( 'proenem_get_testimonial_approval_year', $template );
		$this->assertStringContainsString( 'proenem_get_testimonial_preparation_time', $template );
		$this->assertStringContainsString( 'proenem_get_testimonial_main_tip', $template );
		$this->assertStringContainsString( 'proenem_get_related_testimonial_ids', $template );
		$this->assertStringContainsString( 'proenem_render_testimonial_card( $related_testimonial_id, array(), 3 )', $template );
		$this->assertStringContainsString( 'Outras histórias para continuar acreditando.', $template );
		$this->assertStringContainsString( 'id="pro-testimonial-next-title"', $template );
		$this->assertStringContainsString( "proenem_get_home_cta_destination( 'plans' )", $template );
		$this->assertStringContainsString( 'Agora é a sua vez de construir uma história para este mural.', $template );
		$this->assertStringContainsString( '$has_structured_approval', $template );
		$this->assertStringContainsString( 'data-pro-testimonial-share', $template );
		$this->assertStringContainsString( 'Compartilhar esta conquista', $template );
		$this->assertStringNotContainsString( 'Assistir à entrevista', $template );
		$this->assertStringContainsString( 'navigator.share', $script );
		$this->assertStringContainsString( 'copyTextToClipboard', $script );
	}

	/**
	 * Required plugin dependency contracts should be declared by plugin file.
	 *
	 * @return void
	 */
	public function test_required_plugin_dependencies_are_declared() {
		$required_plugins = proenem_get_required_plugins();

		$this->assertSame( 'free-materials/free-materials.php', $required_plugins['free-materials']['file'] );
		$this->assertSame( 'testimonials/testimonials.php', $required_plugins['testimonials']['file'] );
		$this->assertSame( 'crm-leads-capture/crm-leads-capture.php', $required_plugins['crm-leads-capture']['file'] );
		$this->assertSame( 'sales-page/sales-page.php', $required_plugins['sales-page']['file'] );
		$this->assertSame( array( 'sales-pages/sales-page.php' ), $required_plugins['sales-page']['aliases'] );
	}

	/**
	 * Elementor widgets should expose stable technical names.
	 *
	 * @return void
	 */
	public function test_elementor_sales_widget_contracts_are_declared() {
		$this->assertSame(
			array(
				'Proenem_Elementor_Navbar_Widget',
				'Proenem_Elementor_Footer_Widget',
				'Proenem_Elementor_Offer_Hero_Widget',
				'Proenem_Elementor_Offer_Countdown_Widget',
				'Proenem_Elementor_Pricing_Grid_Widget',
				'Proenem_Elementor_Benefits_List_Widget',
				'Proenem_Elementor_Plans_Comparison_Widget',
				'Proenem_Elementor_Cta_Widget',
				'Proenem_Elementor_Faq_Widget',
				'Proenem_Elementor_Home_Hero_Widget',
				'Proenem_Elementor_Home_Action_Bar_Widget',
				'Proenem_Elementor_Home_Marquee_Widget',
				'Proenem_Elementor_Home_Pillars_Widget',
				'Proenem_Elementor_Home_Proof_Widget',
				'Proenem_Elementor_Home_Pain_Widget',
				'Proenem_Elementor_Home_Platform_Widget',
				'Proenem_Elementor_Home_Questions_Widget',
				'Proenem_Elementor_Home_Pricing_Widget',
				'Proenem_Elementor_Home_Testimonials_Widget',
				'Proenem_Elementor_Home_Schools_Widget',
				'Proenem_Elementor_Home_Final_Cta_Widget',
				'Proenem_Elementor_Home_Faq_Widget',
				'Proenem_Elementor_Lp_Metrics_Widget',
				'Proenem_Elementor_Lp_Spotlight_Widget',
				'Proenem_Elementor_Lp_Video_Story_Widget',
				'Proenem_Elementor_Lp_Testimonials_Widget',
			),
			proenem_get_elementor_sales_widget_classes()
		);
	}

	/**
	 * Active required plugins should not be reported as unmet.
	 *
	 * @return void
	 */
	public function test_active_required_plugins_are_not_reported_as_unmet() {
		$previous_active_plugins = get_option( 'active_plugins', array() );

		update_option(
			'active_plugins',
			array(
				'free-materials/free-materials.php',
				'testimonials/testimonials.php',
				'crm-leads-capture/crm-leads-capture.php',
				'sales-page/sales-page.php',
			)
		);

		$this->assertSame( array(), proenem_get_unmet_required_plugins() );

		update_option( 'active_plugins', $previous_active_plugins );
	}

	/**
	 * Local required plugin aliases should also satisfy dependency notices.
	 *
	 * @return void
	 */
	public function test_required_plugin_aliases_are_not_reported_as_unmet() {
		$previous_active_plugins = get_option( 'active_plugins', array() );

		update_option(
			'active_plugins',
			array(
				'free-materials/free-materials.php',
				'testimonials/testimonials.php',
				'crm-leads-capture/crm-leads-capture.php',
				'sales-pages/sales-page.php',
			)
		);

		$this->assertSame( array(), proenem_get_unmet_required_plugins() );

		update_option( 'active_plugins', $previous_active_plugins );
	}

	/**
	 * The free materials surfaces should be exposed as explicit templates.
	 *
	 * Without the taxonomy template the category URL registered by the
	 * free-materials plugin falls through to archive.php, which renders the
	 * blog index.
	 *
	 * @return void
	 */
	public function test_free_materials_templates_exist() {
		$this->assertFileExists( PROENEM_THEME_DIR . '/page-templates/free-materials.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/single-material_gratuito.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/taxonomy-material_categoria.php' );
		$this->assertFileExists( PROENEM_THEME_DIR . '/template-parts/materials/catalog.php' );
	}

	/**
	 * The theme must provide the template WordPress looks for on the material
	 * category archive.
	 *
	 * The expected name is derived from the plugin contract, so renaming the
	 * taxonomy fails here instead of silently falling through to archive.php,
	 * which is the blog index. The end-to-end suite covers the rendered page.
	 *
	 * @return void
	 */
	public function test_theme_provides_the_material_category_template() {
		$taxonomy = proenem_get_free_materials_taxonomy();

		$this->assertSame( 'material_categoria', $taxonomy );
		$this->assertFileExists( PROENEM_THEME_DIR . '/taxonomy-' . $taxonomy . '.php' );

		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/taxonomy-' . $taxonomy . '.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'template-parts/materials/catalog', $template );
		$this->assertStringContainsString( 'proenem_build_free_materials_query_args', $template );
		$this->assertStringNotContainsString( 'get_the_archive_title', $template );
	}

	/**
	 * The catalog query should filter by the selected categories.
	 *
	 * @return void
	 */
	public function test_catalog_query_args_apply_the_category_filter() {
		$unfiltered = proenem_build_free_materials_query_args( array() );

		$this->assertSame( proenem_get_free_materials_post_type(), $unfiltered['post_type'] );
		$this->assertArrayNotHasKey( 'tax_query', $unfiltered );

		$filtered = proenem_build_free_materials_query_args( array( 'redacao', 'simulados' ) );

		$this->assertArrayHasKey( 'tax_query', $filtered );
		$this->assertSame( proenem_get_free_materials_taxonomy(), $filtered['tax_query'][0]['taxonomy'] );
		$this->assertSame( 'slug', $filtered['tax_query'][0]['field'] );
		$this->assertSame( array( 'redacao', 'simulados' ), $filtered['tax_query'][0]['terms'] );
	}

	/**
	 * The catalog must filter through links, not a form.
	 *
	 * The rendered markup and the active state are covered end to end in
	 * tests/e2e/free-materials.spec.js, which runs against the real taxonomy.
	 *
	 * @return void
	 */
	public function test_catalog_filters_through_links_instead_of_a_form() {
		$part = (string) file_get_contents( PROENEM_THEME_DIR . '/template-parts/materials/catalog.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'pen-blog-filter-bar', $part );
		$this->assertStringNotContainsString( 'pro-materials-layout__sidebar', $part );
		$this->assertFalse( function_exists( 'proenem_render_material_category_filters' ) );

		// The categories live in the hero of both surfaces, where they are the
		// artwork as well as the filter.
		foreach ( array( '/page-templates/free-materials.php', '/taxonomy-material_categoria.php' ) as $file ) {
			$template = (string) file_get_contents( PROENEM_THEME_DIR . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			$this->assertStringContainsString( 'proenem_render_material_category_tabs', $template, $file );
		}

		$this->assertStringNotContainsString( 'proenem_render_material_category_tabs', $part );
	}

	/**
	 * Only categories that lead somewhere should get a tab.
	 *
	 * @return void
	 */
	public function test_category_tabs_skip_empty_categories() {
		$terms = array(
			(object) array(
				'slug'  => 'redacao',
				'name'  => 'Redação',
				'count' => 3,
			),
			(object) array(
				'slug'  => 'vazia',
				'name'  => 'Vazia',
				'count' => 0,
			),
		);

		$this->assertSame(
			array( 'redacao' ),
			wp_list_pluck( proenem_get_material_category_tabs_terms( $terms, array() ), 'slug' )
		);

		// The category being viewed stays visible even when it is empty, so the
		// active tab does not disappear.
		$this->assertSame(
			array( 'redacao', 'vazia' ),
			wp_list_pluck( proenem_get_material_category_tabs_terms( $terms, array( 'vazia' ) ), 'slug' )
		);

		$this->assertSame( array(), proenem_get_material_category_tabs_terms( 'nao e lista', array() ) );
	}

	/**
	 * The results count must be pluralised and announced to assistive technology.
	 *
	 * @return void
	 */
	public function test_catalog_count_is_pluralised_and_announced() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/template-parts/materials/catalog.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'aria-live="polite"', $template );
		$this->assertStringContainsString( "_n( '%s material disponível', '%s materiais disponíveis'", $template );
	}

	/**
	 * Material card headings must sit below the results heading.
	 *
	 * @return void
	 */
	public function test_material_card_heading_level_is_nested() {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Checklist de revisão',
			)
		);

		ob_start();
		proenem_render_material_card( $post_id );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( '<h3>', $markup );
		$this->assertStringNotContainsString( '<h2>', $markup );
	}

	/**
	 * The capture form must block an empty submission on the client.
	 *
	 * @return void
	 */
	public function test_capture_form_marks_required_fields() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/template-parts/materials/capture.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'name="name"', $template );
		$this->assertStringContainsString( 'name="email"', $template );
		$this->assertSame( 2, substr_count( $template, 'required' ) );
		$this->assertStringContainsString( 'inputmode="numeric"', $template );

		// A pattern attribute compiled in the browser's strict regex mode threw
		// and silently killed the whole submit handler. See the Node guard in
		// tests/e2e/free-materials.spec.js.
		$this->assertStringNotContainsString( 'pattern=', $template );
	}

	/**
	 * Every id in the capture panel must carry its instance, because the page
	 * renders the panel twice.
	 *
	 * @return void
	 */
	public function test_capture_panel_ids_are_scoped_to_the_instance() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/template-parts/materials/capture.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( '$capture_field', $template );
		$this->assertStringNotContainsString( 'id="pro-material-capture-name"', $template );

		// The nonce is written by hand because the WordPress helper derives the
		// id from the field name, duplicating id="_wpnonce" across both forms.
		$this->assertStringContainsString( 'wp_create_nonce', $template );
		$this->assertStringContainsString( 'wp_referer_field', $template );

		// Rendering the panel twice must not repeat a single id. The end to end
		// suite checks the whole page; this checks the panel in isolation.
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$markup  = '';

		foreach ( array( 'hero', 'footer' ) as $instance ) {
			ob_start();
			$args = array(
				'instance'    => $instance,
				'material_id' => $post_id,
			);
			require PROENEM_THEME_DIR . '/template-parts/materials/capture.php';
			$markup .= (string) ob_get_clean();
		}

		preg_match_all( '/\sid="([^"]+)"/', $markup, $matches );

		$this->assertNotEmpty( $matches[1] );
		$this->assertSame( $matches[1], array_unique( $matches[1] ) );
	}

	/**
	 * The page must end with a form, not with a link back up to one.
	 *
	 * @return void
	 */
	public function test_material_page_ends_with_a_second_form() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/single-material_gratuito.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertSame( 2, substr_count( $template, "'template-parts/materials/capture'" ) );
		$this->assertStringContainsString( "'instance'    => 'hero'", $template );
		$this->assertStringContainsString( "'instance'    => 'footer'", $template );

		// Both of these pointed at the form above them, with the arrow drawn up.
		$this->assertStringNotContainsString( 'pro-material-download', $template );
		$this->assertStringNotContainsString( 'pro-material-footer-cta', $template );
	}

	/**
	 * The form must say what happens to the data it collects.
	 *
	 * @return void
	 */
	public function test_capture_form_carries_a_privacy_notice() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/template-parts/materials/capture.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'pro-material-capture__privacy', $template );
		$this->assertStringContainsString( 'get_privacy_policy_url', $template );

		// The notice still has to make sense before a privacy page is set.
		$this->assertSame( 2, substr_count( $template, 'Sem pagamento.' ) );
	}

	/**
	 * The material metadata helpers must degrade cleanly when the plugin that
	 * owns the metadata is not active.
	 *
	 * @return void
	 */
	public function test_material_metadata_helpers_tolerate_missing_metadata() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		// No metadata stored: every helper must return its empty value rather
		// than a placeholder label.

		$this->assertSame( '', proenem_get_material_format_label( $post_id ) );
		$this->assertSame( 0, proenem_get_material_pages( $post_id ) );
		$this->assertSame( '', proenem_get_material_file_size( $post_id ) );
		$this->assertSame( '', proenem_get_material_level( $post_id ) );
		$this->assertSame( array(), proenem_get_material_highlights( $post_id ) );
		$this->assertFalse( proenem_material_is_featured( $post_id ) );
		$this->assertSame( array(), proenem_get_material_specs( $post_id ) );
	}

	/**
	 * Card specs should list only the fields an editor filled in.
	 *
	 * @return void
	 */
	public function test_material_specs_skip_empty_fields() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		update_post_meta( $post_id, free_materials_pages_meta_key(), 1 );

		$this->assertSame( array( '1 página' ), proenem_get_material_specs( $post_id ) );

		update_post_meta( $post_id, free_materials_pages_meta_key(), 14 );
		update_post_meta( $post_id, free_materials_file_size_meta_key(), '1,8 MB' );

		$this->assertSame( array( '14 páginas', '1,8 MB' ), proenem_get_material_specs( $post_id ) );
	}

	/**
	 * Highlights should come back as a clean list of strings.
	 *
	 * @return void
	 */
	public function test_material_highlights_are_returned_as_clean_strings() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		update_post_meta( $post_id, free_materials_highlights_meta_key(), array( 'Um', '', 'Dois' ) );

		$this->assertSame( array( 'Um', 'Dois' ), proenem_get_material_highlights( $post_id ) );

		update_post_meta( $post_id, free_materials_highlights_meta_key(), 'nao e lista' );

		$this->assertSame( array(), proenem_get_material_highlights( $post_id ) );
	}

	/**
	 * Material images must come from WordPress so they carry srcset, sizes,
	 * intrinsic dimensions and lazy loading.
	 *
	 * @return void
	 */
	public function test_material_image_is_rendered_by_wordpress() {
		$post_id       = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$attachment_id = self::factory()->attachment->create_upload_object(
			DIR_TESTDATA . '/images/canola.jpg',
			$post_id
		);

		set_post_thumbnail( $post_id, $attachment_id );

		ob_start();
		proenem_render_material_image( $post_id, 'medium_large', '420px' );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( '<img', $markup );
		$this->assertStringContainsString( 'srcset=', $markup );
		$this->assertStringContainsString( 'sizes=', $markup );
		$this->assertStringContainsString( 'width=', $markup );
		$this->assertStringContainsString( 'height=', $markup );
		$this->assertStringNotContainsString( 'pro-material-placeholder', $markup );
	}

	/**
	 * An above-the-fold material image should opt out of lazy loading.
	 *
	 * @return void
	 */
	public function test_material_cover_image_is_eager() {
		$post_id       = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$attachment_id = self::factory()->attachment->create_upload_object(
			DIR_TESTDATA . '/images/canola.jpg',
			$post_id
		);

		set_post_thumbnail( $post_id, $attachment_id );

		ob_start();
		proenem_render_material_image( $post_id, 'large', '800px', true );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'loading="eager"', $markup );
		$this->assertStringContainsString( 'fetchpriority="high"', $markup );
		$this->assertStringNotContainsString( 'loading="lazy"', $markup );
	}

	/**
	 * A material without a cover must not borrow a photo of a student.
	 *
	 * @return void
	 */
	public function test_material_without_cover_renders_a_typographic_placeholder() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		ob_start();
		proenem_render_material_image( $post_id );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'pro-material-placeholder', $markup );
		$this->assertStringNotContainsString( '<img', $markup );
		$this->assertStringNotContainsString( 'student_school', $markup );
		$this->assertStringNotContainsString( 'hero-student', $markup );
	}

	/**
	 * The placeholder should name the format when the editor set one.
	 *
	 * @return void
	 */
	public function test_material_placeholder_prefers_the_format_label() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		update_post_meta( $post_id, free_materials_format_meta_key(), 'pdf' );

		ob_start();
		proenem_render_material_image( $post_id );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'PDF', $markup );
	}

	/**
	 * The card must say what the visitor gets, and stay quiet about fields the
	 * editor left empty.
	 *
	 * @return void
	 */
	public function test_material_card_shows_only_the_metadata_that_exists() {
		$bare_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Sem metadados',
			)
		);

		ob_start();
		proenem_render_material_card( $bare_id );
		$bare = (string) ob_get_clean();

		$this->assertStringNotContainsString( 'pro-material-card__specs', $bare );
		$this->assertStringNotContainsString( 'pro-material-card__level', $bare );

		$filled_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Com metadados',
			)
		);

		update_post_meta( $filled_id, free_materials_format_meta_key(), 'pdf' );
		update_post_meta( $filled_id, free_materials_pages_meta_key(), 14 );
		update_post_meta( $filled_id, free_materials_file_size_meta_key(), '1,8 MB' );
		update_post_meta( $filled_id, free_materials_level_meta_key(), 'Quem já fez simulado' );

		ob_start();
		proenem_render_material_card( $filled_id );
		$filled = (string) ob_get_clean();

		$this->assertStringContainsString( 'PDF · 14 páginas · 1,8 MB', $filled );
		$this->assertStringContainsString( 'pro-material-card__level', $filled );
	}

	/**
	 * The default call to action must promise the download.
	 *
	 * @return void
	 */
	public function test_material_cta_defaults_to_promising_the_download() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		$this->assertSame( 'Baixar grátis', proenem_get_material_cta_label( $post_id ) );

		// A per-material label still wins.
		update_post_meta( $post_id, proenem_get_free_materials_cta_label_meta_key(), 'Baixar checklist' );

		$this->assertSame( 'Baixar checklist', proenem_get_material_cta_label( $post_id ) );
	}

	/**
	 * The catalog hero must stay short enough to leave the first material
	 * inside the first screen.
	 *
	 * @return void
	 */
	public function test_catalog_hero_uses_the_compact_modifier() {
		foreach ( array( '/page-templates/free-materials.php', '/taxonomy-material_categoria.php' ) as $file ) {
			$template = (string) file_get_contents( PROENEM_THEME_DIR . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			$this->assertStringContainsString( 'pro-materials-hero--catalog', $template, $file );
		}

		$css = (string) file_get_contents( PROENEM_THEME_DIR . '/src/styles/theme.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// The modifier has to win over the shared .pro-materials-hero rules,
		// which have the same specificity, so cascade order is the contract.
		$this->assertLessThan(
			strpos( $css, '.pro-materials-hero--catalog {' ),
			strpos( $css, '.pro-materials-hero h1,' ),
			'.pro-materials-hero--catalog must come after the shared hero rules.'
		);

		// The card title moved to h3 in #243; the styles must follow.
		$this->assertStringContainsString( '.pro-material-card h3 {', $css );
		$this->assertStringNotContainsString( '.pro-material-card h2 {', $css );
	}

	/**
	 * The catalog highlight is editorial and optional.
	 *
	 * @return void
	 */
	public function test_featured_material_selection_is_deterministic() {
		$helpers = (string) file_get_contents( PROENEM_THEME_DIR . '/inc/template-tags.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$start   = strpos( $helpers, 'function proenem_get_featured_material()' );
		$query   = substr( $helpers, $start, 900 );

		// Two editors can each flag a material. Without a tie-break the catalog
		// promotes a different one between requests.
		$this->assertStringContainsString( "'orderby'", $query );
		$this->assertStringContainsString( "'ID'   => 'DESC'", $query );
	}

	/**
	 * The catalog highlight is editorial and optional.
	 *
	 * @return void
	 */
	public function test_featured_material_is_opt_in() {
		$this->assertNull( proenem_get_featured_material() );

		// Nothing renders when the editors have not picked one.
		ob_start();
		proenem_render_featured_material( null );
		$this->assertSame( '', (string) ob_get_clean() );
	}

	/**
	 * The highlight promotes a material without removing it from the list.
	 *
	 * @return void
	 */
	public function test_featured_material_stays_in_the_catalog_count() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/page-templates/free-materials.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// Excluding it would make "Todos os materiais" and the count lie.
		$this->assertStringNotContainsString( 'post__not_in', $template );
		$this->assertStringContainsString( 'proenem_render_featured_material', $template );
		$this->assertStringContainsString( "'featured_id'", $template );
	}

	/**
	 * The highlighted material should be marked in the grid.
	 *
	 * @return void
	 */
	public function test_featured_material_card_is_flagged() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		ob_start();
		proenem_render_material_card( $post_id );
		$plain = (string) ob_get_clean();

		$this->assertStringNotContainsString( 'pro-material-card--featured', $plain );
		$this->assertStringNotContainsString( 'pro-material-card__flag', $plain );

		ob_start();
		proenem_render_material_card( $post_id, true );
		$flagged = (string) ob_get_clean();

		$this->assertStringContainsString( 'pro-material-card--featured', $flagged );
		$this->assertStringContainsString( 'Em destaque', $flagged );
	}

	/**
	 * The highlight must render the material's own promise.
	 *
	 * @return void
	 */
	public function test_featured_material_band_renders_the_material() {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Transforme o resultado do simulado em plano de estudo.',
				'post_status'  => 'publish',
				'post_title'   => 'Mapa de análise',
			)
		);

		update_post_meta( $post_id, free_materials_format_meta_key(), 'pdf' );
		update_post_meta( $post_id, free_materials_highlights_meta_key(), array( 'Um', 'Dois' ) );

		ob_start();
		proenem_render_featured_material( get_post( $post_id ) );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'Material em destaque', $markup );
		$this->assertStringContainsString( 'Mapa de análise', $markup );
		$this->assertStringContainsString( 'PDF', $markup );
		$this->assertSame( 2, substr_count( $markup, '<li>' ) );

		// The decorative cover link must not duplicate the title for screen
		// readers or take a second tab stop.
		$this->assertStringContainsString( 'aria-hidden="true"', $markup );
		$this->assertStringContainsString( 'tabindex="-1"', $markup );
	}

	/**
	 * Only the capture page gets the reduced header.
	 *
	 * @return void
	 */
	public function test_reduced_header_is_limited_to_the_capture_surface() {
		$page_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);

		update_post_meta( $page_id, '_wp_page_template', 'page-templates/free-materials.php' );
		$this->go_to( get_permalink( $page_id ) );

		// The catalog still needs the full navigation.
		$this->assertFalse( proenem_is_material_capture_surface() );

		$this->go_to( home_url( '/' ) );

		$this->assertFalse( proenem_is_material_capture_surface() );

		$header = (string) file_get_contents( PROENEM_THEME_DIR . '/header.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( 'proenem_is_material_capture_surface', $header );
		$this->assertStringContainsString( "'logo_only'  => true", $header );
	}

	/**
	 * The breadcrumb should only offer the steps back the reader cannot reach
	 * otherwise: the logo already goes home and the h1 already names the
	 * material, so neither belongs in the trail.
	 *
	 * @return void
	 */
	public function test_material_breadcrumb_renders_the_trail() {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Mapa de análise',
			)
		);

		ob_start();
		proenem_render_material_breadcrumb( $post_id );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'Materiais gratuitos', $markup );
		$this->assertStringNotContainsString( 'Início', $markup );
		$this->assertStringNotContainsString( 'Mapa de análise', $markup );

		// Every crumb leads somewhere, so none of them is the current page.
		$this->assertStringNotContainsString( 'aria-current', $markup );
		$this->assertSame( 1, substr_count( $markup, '<a href' ) );
	}

	/**
	 * Related materials should prefer the same category and never repeat the
	 * material being viewed.
	 *
	 * @return void
	 */
	public function test_related_materials_prefer_the_same_category() {
		if ( ! proenem_free_materials_is_available() ) {
			$this->assertSame( array(), proenem_get_related_material_ids( 1 ) );

			return;
		}

		$current = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_type'   => proenem_get_free_materials_post_type(),
			)
		);
		$related = proenem_get_related_material_ids( $current, 3 );

		$this->assertNotContains( $current, $related );
	}

	/**
	 * The share bar must consume the design system contract.
	 *
	 * @return void
	 */
	public function test_material_share_uses_the_design_system_contract() {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Mapa de análise',
			)
		);

		ob_start();
		proenem_render_material_share( $post_id );
		$markup = (string) ob_get_clean();

		$this->assertStringContainsString( 'pen-article-share-bar', $markup );
		$this->assertStringContainsString( 'pen-article-share--green', $markup );

		// WhatsApp comes first: the audience is students sharing with students.
		$this->assertLessThan(
			strpos( $markup, 'facebook.com' ),
			strpos( $markup, 'wa.me' )
		);

		// Every share link must be labelled and safe to open.
		$this->assertSame( 3, substr_count( $markup, 'rel="noopener noreferrer"' ) );
		$this->assertSame( 3, substr_count( $markup, 'aria-label=' ) );
	}

	/**
	 * The related section is absent rather than empty.
	 *
	 * @return void
	 */
	public function test_related_materials_section_is_absent_when_there_is_nothing_to_show() {
		ob_start();
		proenem_render_related_materials( 0 );

		$this->assertSame( '', (string) ob_get_clean() );
	}

	/**
	 * The catalog must stop loading every material at once.
	 *
	 * @return void
	 */
	public function test_catalog_query_is_paged_and_ordered() {
		$args = proenem_build_free_materials_query_args( array() );

		$this->assertNotSame( -1, $args['posts_per_page'] );
		$this->assertSame( proenem_get_materials_per_page(), $args['posts_per_page'] );
		$this->assertSame( 1, $args['paged'] );
		$this->assertSame( 'date', $args['orderby'] );
		$this->assertSame( 'DESC', $args['order'] );
	}

	/**
	 * The page size should be tunable without patching the theme.
	 *
	 * @return void
	 */
	public function test_catalog_page_size_is_filterable() {
		$smaller = static function () {
			return 4;
		};

		add_filter( 'proenem_materials_per_page', $smaller );

		$this->assertSame( 4, proenem_get_materials_per_page() );

		remove_filter( 'proenem_materials_per_page', $smaller );

		// A nonsense value must not produce an empty query.
		$zero = static function () {
			return 0;
		};

		add_filter( 'proenem_materials_per_page', $zero );

		$this->assertSame( 1, proenem_get_materials_per_page() );

		remove_filter( 'proenem_materials_per_page', $zero );
	}

	/**
	 * Only the orders the catalog offers may reach the query.
	 *
	 * @return void
	 */
	public function test_catalog_order_is_constrained_to_the_offered_options() {
		$this->assertSame(
			array(
				'order'   => 'ASC',
				'orderby' => 'title',
			),
			proenem_get_materials_order_args( 'az' )
		);

		// Anything unknown falls back to the default rather than reaching the
		// query as-is.
		$this->assertSame(
			array(
				'order'   => 'DESC',
				'orderby' => 'date',
			),
			proenem_get_materials_order_args( 'DROP TABLE' )
		);

		$this->assertArrayHasKey( 'recentes', proenem_get_materials_orders() );
	}

	/**
	 * Catalog pagination has to keep the filter and the order.
	 *
	 * @return void
	 */
	public function test_catalog_pagination_links_preserve_the_context() {
		$_GET['ordenar'] = 'az';

		$link = proenem_get_materials_page_link( proenem_get_free_materials_url() );

		$this->assertStringNotContainsString( 'pagina=', call_user_func( $link, 1 ) );
		$this->assertStringContainsString( 'pagina=3', call_user_func( $link, 3 ) );

		// Paging must not silently reset the order the visitor chose.
		$this->assertStringContainsString( 'ordenar=az', call_user_func( $link, 1 ) );
		$this->assertStringContainsString( 'ordenar=az', call_user_func( $link, 3 ) );

		unset( $_GET['ordenar'] );
	}

	/**
	 * An unknown order must not reach the query.
	 *
	 * @return void
	 */
	public function test_unknown_order_falls_back_to_the_default() {
		$_GET['ordenar'] = 'az';

		$this->assertSame( 'az', proenem_get_selected_materials_order() );

		$_GET['ordenar'] = 'nao-existe';

		$this->assertSame( 'recentes', proenem_get_selected_materials_order() );

		unset( $_GET['ordenar'] );
	}

	/**
	 * The catalog page and the material post type share a slug, so pagination
	 * cannot use a pretty /page/N/ URL.
	 *
	 * @return void
	 */
	public function test_catalog_pagination_uses_a_query_argument() {
		$_GET['pagina'] = '3';

		$this->assertSame( 3, proenem_get_materials_paged() );

		$_GET['pagina'] = '-2';

		$this->assertSame( 1, proenem_get_materials_paged() );

		unset( $_GET['pagina'] );

		$this->assertSame( 1, proenem_get_materials_paged() );
	}

	/**
	 * The highlight belongs to the first page only.
	 *
	 * @return void
	 */
	public function test_featured_material_is_limited_to_the_first_page() {
		$template = (string) file_get_contents( PROENEM_THEME_DIR . '/page-templates/free-materials.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertStringContainsString( '1 === proenem_get_materials_paged()', $template );
	}

	/**
	 * Social proof must only appear when somebody put a number in.
	 *
	 * @return void
	 */
	public function test_social_proof_needs_real_data() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		$this->assertSame( 0, proenem_get_material_downloads( $post_id ) );

		ob_start();
		proenem_render_material_reassurance( $post_id );
		$without = (string) ob_get_clean();

		// The questions still render; the proof block does not.
		$this->assertStringNotContainsString( 'pro-material-proof__count', $without );
		$this->assertStringContainsString( 'pen-faq-item', $without );

		update_post_meta( $post_id, free_materials_downloads_meta_key(), 1847 );

		ob_start();
		proenem_render_material_reassurance( $post_id );
		$with = (string) ob_get_clean();

		$this->assertStringContainsString( 'pro-material-proof__count', $with );
		$this->assertStringContainsString( number_format_i18n( 1847 ), $with );
	}

	/**
	 * The default questions describe this flow, so they hold for any material,
	 * and a site can still replace them.
	 *
	 * @return void
	 */
	public function test_material_faq_has_defaults_and_is_filterable() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$faq     = proenem_get_material_faq( $post_id );

		$this->assertCount( 4, $faq );

		foreach ( $faq as $item ) {
			$this->assertNotEmpty( $item['question'] );
			$this->assertNotEmpty( $item['answer'] );
		}

		$replace = static function () {
			return array(
				array(
					'question' => 'Pergunta do site',
					'answer'   => 'Resposta do site',
				),
			);
		};

		add_filter( 'proenem_material_faq', $replace );

		$this->assertCount( 1, proenem_get_material_faq( $post_id ) );

		remove_filter( 'proenem_material_faq', $replace );
	}

	/**
	 * An entry missing a question or an answer must not render an empty item.
	 *
	 * @return void
	 */
	public function test_material_faq_skips_incomplete_entries() {
		$post_id    = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$incomplete = static function () {
			return array(
				array(
					'question' => 'Completa',
					'answer'   => 'Com resposta',
				),
				array(
					'question' => 'Sem resposta',
					'answer'   => '',
				),
			);
		};

		add_filter( 'proenem_material_faq', $incomplete );

		ob_start();
		proenem_render_material_reassurance( $post_id );
		$markup = (string) ob_get_clean();

		remove_filter( 'proenem_material_faq', $incomplete );

		$this->assertSame( 1, substr_count( $markup, '<summary>' ) );
		$this->assertStringNotContainsString( 'Sem resposta', $markup );
	}



	/**
	 * The category chips are the artwork of this hero, so every tone has to be
	 * readable with ink text.
	 *
	 * @return void
	 */
	public function test_category_tones_are_readable_with_ink_text() {
		$tones = proenem_get_material_category_tones();

		$this->assertNotEmpty( $tones );

		// purple (3.89:1) and platform blue (3.37:1) fail against ink.
		$this->assertNotContains( 'purple', $tones );
		$this->assertNotContains( 'blue', $tones );

		// A category keeps its colour from one page to the next.
		$this->assertSame( proenem_get_material_category_tone( 0 ), proenem_get_material_category_tone( 0 ) );
		$this->assertSame(
			proenem_get_material_category_tone( 0 ),
			proenem_get_material_category_tone( count( $tones ) )
		);
	}

	/**
	 * The catalog hero must not repeat the approved students one.
	 *
	 * @return void
	 */
	public function test_catalog_hero_does_not_reuse_the_testimonials_treatment() {
		$css   = (string) file_get_contents( PROENEM_THEME_DIR . '/src/styles/theme.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$start = strpos( $css, '.pro-materials-hero--catalog {' );
		$rule  = substr( $css, $start, strpos( $css, '}', $start ) - $start );

		// Light ground with an ink headline, not a solid brand band.
		$this->assertStringContainsString( 'canvas-white', $rule );
		$this->assertStringNotContainsString( 'proenem-red', $rule );

		// The photo stage belongs to the approved students page.
		$this->assertStringNotContainsString( '.pro-materials-hero__stage', $css );
		$this->assertStringContainsString( '.pro-testimonials-hero__stage', $css );
	}

	/**
	 * The footer title must not be beaten by the section heading rule.
	 *
	 * @return void
	 */
	public function test_footer_title_wins_over_the_section_heading_rule() {
		$css = (string) file_get_contents( PROENEM_THEME_DIR . '/src/styles/theme.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// The design system groups `.pen-site-footer h2` with the section
		// headings, at (0,1,1). A bare `.pen-site-footer__title` is (0,1,0) and
		// loses, which rendered the footer at 70.4px on every page but the home.
		$this->assertStringContainsString( '.pen-site-footer .pen-site-footer__title', $css );
	}

	/**
	 * The unused delivery URL helper should be gone.
	 *
	 * @return void
	 */
	public function test_unused_material_delivery_helper_is_removed() {
		$this->assertFalse( function_exists( 'proenem_get_material_delivery_url' ) );
	}
}
