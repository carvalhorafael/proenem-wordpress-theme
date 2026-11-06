<?php
/**
 * PROMOBLACK home tests.
 *
 * @package Proenem
 */

/**
 * Tests the PROMOBLACK campaign home contracts.
 */
class PromoblackTest extends WP_UnitTestCase {
	/**
	 * Reset the campaign settings between tests.
	 *
	 * @return void
	 */
	public function tear_down() {
		foreach ( array_keys( proenem_get_promoblack_settings() ) as $key ) {
			remove_theme_mod( proenem_promoblack_setting_id( $key ) );
		}

		update_option( 'timezone_string', '' );

		parent::tear_down();
	}

	/**
	 * Render the PROMOBLACK template for a page that uses it.
	 *
	 * @return string
	 */
	private function render_template() {
		$page_id = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Home PROMOBLACK',
			)
		);
		update_post_meta( $page_id, '_wp_page_template', proenem_get_promoblack_template() );

		$this->go_to( get_permalink( $page_id ) );

		ob_start();
		require PROENEM_THEME_DIR . '/page-templates/home-promoblack.php';

		return (string) ob_get_clean();
	}

	/**
	 * The template should be treated as a home surface so the site header and footer stay out.
	 *
	 * @return void
	 */
	public function test_template_is_registered_as_home_surface() {
		$this->assertContains( 'page-templates/home-promoblack.php', proenem_get_home_templates() );
		$this->assertFileExists( PROENEM_THEME_DIR . '/' . proenem_get_promoblack_template() );
	}

	/**
	 * The lot deadline accepts the documented format and the datetime-local format.
	 *
	 * @return void
	 */
	public function test_deadline_sanitization() {
		$this->assertSame( '2026-11-12 23:59', proenem_sanitize_promoblack_deadline( '2026-11-12 23:59' ) );
		$this->assertSame( '2026-11-12 23:59', proenem_sanitize_promoblack_deadline( '2026-11-12T23:59' ) );
		$this->assertSame( '', proenem_sanitize_promoblack_deadline( '12/11/2026 23:59' ) );
		$this->assertSame( '', proenem_sanitize_promoblack_deadline( '' ) );
		$this->assertSame( '', proenem_sanitize_promoblack_deadline( array( '2026-11-12 23:59' ) ) );
		$this->assertSame( '', proenem_sanitize_promoblack_deadline( '2026-11-31 23:59' ) );
		$this->assertSame( '', proenem_sanitize_promoblack_deadline( '2026-11-30 24:00' ) );
	}

	/**
	 * The Customizer rejects a deadline the sanitizer would discard instead of saving it empty.
	 *
	 * @return void
	 */
	public function test_deadline_validation_reports_invalid_dates() {
		$this->assertFalse( proenem_validate_promoblack_deadline( new WP_Error(), '2026-11-12 23:59' )->has_errors() );
		$this->assertFalse( proenem_validate_promoblack_deadline( new WP_Error(), '' )->has_errors() );
		$this->assertTrue( proenem_validate_promoblack_deadline( new WP_Error(), '30/11/2026 23:59' )->has_errors() );
		$this->assertTrue( proenem_validate_promoblack_deadline( new WP_Error(), '2026-11-31 23:59' )->has_errors() );
	}

	/**
	 * A lot that already closed does not keep announcing itself as open.
	 *
	 * @return void
	 */
	public function test_expired_deadline_hides_lot_urgency() {
		set_theme_mod( proenem_promoblack_setting_id( 'lot_deadline' ), '2020-01-01 00:00' );

		$this->assertSame( '', proenem_get_promoblack_deadline_iso() );
		$this->assertSame( '', proenem_get_promoblack_deadline_label() );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'pro-pb-lots', $html );
		$this->assertStringNotContainsString( 'data-pro-countdown=', $html );
		$this->assertStringNotContainsString( 'acaba em', $html );
	}

	/**
	 * The countdown target carries the site time zone offset.
	 *
	 * @return void
	 */
	public function test_deadline_uses_site_timezone() {
		update_option( 'timezone_string', 'America/Sao_Paulo' );
		set_theme_mod( proenem_promoblack_setting_id( 'lot_deadline' ), '2030-11-12 23:59' );

		$this->assertSame( '2030-11-12T23:59:00-03:00', proenem_get_promoblack_deadline_iso() );
		$this->assertSame( '12/11 às 23h59', proenem_get_promoblack_deadline_label() );
	}

	/**
	 * Checkout buttons ship with the campaign links, and a cleared link removes the dead button.
	 *
	 * @return void
	 */
	public function test_checkout_urls_default_to_campaign_and_clear_without_dead_buttons() {
		$plans = proenem_get_promoblack_plans();

		$this->assertStringStartsWith( 'https://pay.hotmart.com/T102416176R?', $plans['enem-pro']['url'] );
		$this->assertStringContainsString( 'utm_campaign=vendas_bf_promoblack', $plans['enem-pro']['url'] );
		$this->assertStringStartsWith( 'https://pay.hotmart.com/E108194423D?', $plans['med']['url'] );
		$this->assertStringContainsString( 'utm_campaign=vendas_bf_promoblack', $plans['med']['url'] );
		$this->assertStringEndsWith( '&sck=site_homePlanosPro', $plans['enem-pro']['url'] );
		$this->assertStringEndsWith( '&sck=site_homePlanosMed', $plans['med']['url'] );

		set_theme_mod( proenem_promoblack_setting_id( 'med_checkout_url' ), '' );
		set_theme_mod( proenem_promoblack_setting_id( 'enem_pro_checkout_url' ), 'https://pay.hotmart.com/outro' );

		$plans = proenem_get_promoblack_plans();

		$this->assertSame( '', $plans['med']['url'] );
		$this->assertSame( 'https://pay.hotmart.com/outro', $plans['enem-pro']['url'] );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'data-pro-promoblack-action="checkout-med"', $html );
		$this->assertStringContainsString( 'data-pro-promoblack-action="checkout-enem-pro"', $html );
		$this->assertMatchesRegularExpression( '/href="#planos" data-pro-promoblack-action="final-med"/', $html );
	}

	/**
	 * Every comparison row must fill both plan columns.
	 *
	 * @return void
	 */
	public function test_comparison_rows_fill_both_plans() {
		foreach ( proenem_get_promoblack_comparison() as $group ) {
			$this->assertNotEmpty( $group['rows'] );

			foreach ( $group['rows'] as $row ) {
				$this->assertNotSame( '', $row['label'] );
				$this->assertArrayHasKey( 'enem_pro', $row );
				$this->assertArrayHasKey( 'med', $row );
			}
		}
	}

	/**
	 * Without a deadline the page hides the countdowns and the lots bar.
	 *
	 * @return void
	 */
	public function test_template_hides_countdowns_without_deadline() {
		$html = $this->render_template();

		$this->assertStringContainsString( 'id="planos"', $html );
		$this->assertStringContainsString( 'id="faq"', $html );
		$this->assertStringNotContainsString( 'data-pro-countdown=', $html );
		$this->assertStringNotContainsString( 'pro-pb-lots', $html );
		$this->assertSame( 1, substr_count( $html, '<h1' ) );
	}

	/**
	 * With a deadline both countdowns render and the final copy names the date.
	 *
	 * @return void
	 */
	public function test_template_renders_countdowns_with_deadline() {
		update_option( 'timezone_string', 'America/Sao_Paulo' );
		set_theme_mod( proenem_promoblack_setting_id( 'lot_deadline' ), '2030-11-12 23:59' );

		$html = $this->render_template();

		$this->assertSame( 2, substr_count( $html, 'data-pro-countdown="2030-11-12T23:59:00-03:00"' ) );
		$this->assertStringContainsString( '<strong>12/11 às 23h59</strong>', $html );
	}

	/**
	 * The promotional price block shows the original, cash and installment prices with the discount or the coupon.
	 *
	 * @return void
	 */
	public function test_price_block_follows_settings() {
		$settings = array(
			'enem_pro_price'      => 'R$ 32,89',
			'enem_pro_list_price' => 'R$ 563,83',
			'enem_pro_cash_price' => 'R$ 394,68',
			'enem_pro_discount'   => '30% OFF',
			'med_price'           => 'R$ 25,66',
			'med_list_price'      => 'R$ 1.026,51',
			'med_cash_price'      => 'R$ 307,95',
			'med_coupon'          => '70OFF',
		);

		foreach ( $settings as $key => $value ) {
			set_theme_mod( proenem_promoblack_setting_id( $key ), $value );
		}

		$html = $this->render_template();

		$this->assertSame( 2, substr_count( $html, 'class="pro-pb-price"' ) );
		$this->assertStringContainsString( 'De R$ 563,83', $html );
		$this->assertStringContainsString( 'Por R$ 394,68 à vista', $html );
		$this->assertStringContainsString( 'ou 12x de R$ 32,89', $html );
		$this->assertStringContainsString( '<span class="pro-pb-price__discount">30% OFF</span>', $html );
		$this->assertStringContainsString( 'De R$ 1.026,51', $html );
		$this->assertStringContainsString( 'Por R$ 307,95 à vista', $html );
		$this->assertStringContainsString( 'ou 12x de R$ 25,66', $html );
		$this->assertStringContainsString( '<strong class="pro-pb-price__coupon-code">70OFF</strong>', $html );
		$this->assertSame( 1, substr_count( $html, 'pro-pb-price__discount' ) );
		$this->assertSame( 1, substr_count( $html, 'class="pro-pb-price__coupon"' ) );
		$this->assertStringNotContainsString( 'pro-pb-plan__price-prefix', $html );

		set_theme_mod( proenem_promoblack_setting_id( 'med_cash_price' ), '' );

		$html = $this->render_template();

		$this->assertSame( 1, substr_count( $html, 'class="pro-pb-price"' ) );
		$this->assertStringContainsString( '<span class="pro-pb-plan__price-value">R$ 25,66</span>', $html );
	}

	/**
	 * The Login menu offers both platforms and disappears when both links are cleared.
	 *
	 * @return void
	 */
	public function test_login_menu_follows_settings() {
		$html = $this->render_template();

		$this->assertStringContainsString( 'data-pro-pb-login', $html );
		$this->assertStringContainsString( 'href="https://app.proenem.com.br" data-pro-promoblack-action="login-proenem"', $html );
		$this->assertStringContainsString( 'href="https://app.promedicina.com.br/" data-pro-promoblack-action="login-promedicina"', $html );

		set_theme_mod( proenem_promoblack_setting_id( 'login_med_url' ), '' );

		$html = $this->render_template();

		$this->assertStringContainsString( 'data-pro-promoblack-action="login-proenem"', $html );
		$this->assertStringNotContainsString( 'data-pro-promoblack-action="login-promedicina"', $html );

		set_theme_mod( proenem_promoblack_setting_id( 'login_url' ), '' );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'data-pro-pb-login', $html );
	}

	/**
	 * The SISU guarantee only shows with its conditions, linked from the asterisk to the footer.
	 *
	 * @return void
	 */
	public function test_sisu_band_follows_settings() {
		$html = $this->render_template();

		$this->assertStringContainsString( 'pro-pb-sisu', $html );
		$this->assertStringContainsString( 'devolveremos o seu investimento', $html );
		$this->assertStringContainsString( 'href="#pro-pb-sisu-note"', $html );
		$this->assertStringContainsString( 'id="pro-pb-sisu-note"', $html );
		$this->assertStringContainsString( '* Leia o regulamento em', $html );
		$this->assertStringContainsString( '<a href="https://proenem.com.br/termos-de-uso/">proenem.com.br/termos-de-uso</a>', $html );

		set_theme_mod( proenem_promoblack_setting_id( 'sisu_note' ), '' );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'pro-pb-sisu', $html );
		$this->assertStringNotContainsString( 'devolveremos o seu investimento', $html );

		remove_theme_mod( proenem_promoblack_setting_id( 'sisu_note' ) );
		set_theme_mod( proenem_promoblack_setting_id( 'sisu_text' ), '' );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'pro-pb-sisu', $html );
		$this->assertStringNotContainsString( 'id="pro-pb-sisu-note"', $html );
	}
}
