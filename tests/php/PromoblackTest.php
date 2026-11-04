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
	}

	/**
	 * The countdown target carries the site time zone offset.
	 *
	 * @return void
	 */
	public function test_deadline_uses_site_timezone() {
		update_option( 'timezone_string', 'America/Sao_Paulo' );
		set_theme_mod( proenem_promoblack_setting_id( 'lot_deadline' ), '2026-11-12 23:59' );

		$this->assertSame( '2026-11-12T23:59:00-03:00', proenem_get_promoblack_deadline_iso() );
		$this->assertSame( '12/11 às 23h59', proenem_get_promoblack_deadline_label() );
	}

	/**
	 * Checkout buttons ship with the campaign links and fall back to the plans anchor when cleared.
	 *
	 * @return void
	 */
	public function test_checkout_urls_default_to_campaign_and_fall_back_to_plans_anchor() {
		$plans = proenem_get_promoblack_plans();

		$this->assertStringStartsWith( 'https://pay.hotmart.com/T102416176R?', $plans['enem-pro']['url'] );
		$this->assertStringContainsString( 'utm_campaign=vendas_bf_promoblack', $plans['enem-pro']['url'] );
		$this->assertStringStartsWith( 'https://pay.hotmart.com/E108194423D?', $plans['med']['url'] );
		$this->assertStringContainsString( 'utm_campaign=vendas_bf_promoblack', $plans['med']['url'] );

		set_theme_mod( proenem_promoblack_setting_id( 'med_checkout_url' ), '' );
		set_theme_mod( proenem_promoblack_setting_id( 'enem_pro_checkout_url' ), 'https://pay.hotmart.com/outro' );

		$plans = proenem_get_promoblack_plans();

		$this->assertSame( '#planos', $plans['med']['url'] );
		$this->assertSame( 'https://pay.hotmart.com/outro', $plans['enem-pro']['url'] );
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
		set_theme_mod( proenem_promoblack_setting_id( 'lot_deadline' ), '2026-11-12 23:59' );

		$html = $this->render_template();

		$this->assertSame( 2, substr_count( $html, 'data-pro-countdown="2026-11-12T23:59:00-03:00"' ) );
		$this->assertStringContainsString( '<strong>12/11 às 23h59</strong>', $html );
	}

	/**
	 * The SISU guarantee band follows its settings and carries its note to the footer.
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

		$this->assertStringContainsString( 'pro-pb-sisu', $html );
		$this->assertStringNotContainsString( 'href="#pro-pb-sisu-note"', $html );
		$this->assertStringNotContainsString( 'id="pro-pb-sisu-note"', $html );

		remove_theme_mod( proenem_promoblack_setting_id( 'sisu_note' ) );
		set_theme_mod( proenem_promoblack_setting_id( 'sisu_text' ), '' );

		$html = $this->render_template();

		$this->assertStringNotContainsString( 'pro-pb-sisu', $html );
		$this->assertStringNotContainsString( 'id="pro-pb-sisu-note"', $html );
	}
}
