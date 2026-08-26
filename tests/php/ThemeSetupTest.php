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
		$this->assertStringContainsString( 'Ver plano e preço', $markup );
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
				'Proenem_Elementor_Pricing_Card_Widget',
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
				'Proenem_Elementor_Lp_Offer_Highlight_Widget',
				'Proenem_Elementor_Lp_Spotlight_Widget',
				'Proenem_Elementor_Lp_Video_Story_Widget',
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
}
