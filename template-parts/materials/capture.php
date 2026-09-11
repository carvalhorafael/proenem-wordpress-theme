<?php
/**
 * Free material capture panel.
 *
 * Rendered more than once on the same page, so every id carries the instance
 * name. Keep it that way: duplicated ids break the label and aria wiring that
 * the inline validation depends on.
 *
 * Expected args:
 * - material_id int    Material being offered.
 * - instance    string Unique slug for this occurrence, e.g. 'hero'.
 * - heading     string Panel heading.
 * - eyebrow     string Small label above the heading.
 * - anchor      string Optional id for the panel element.
 *
 * @package Proenem
 */

$capture_material = isset( $args['material_id'] ) ? (int) $args['material_id'] : 0;

if ( ! $capture_material ) {
	return;
}

$capture_instance = isset( $args['instance'] ) ? sanitize_html_class( $args['instance'] ) : 'hero';
$capture_heading  = isset( $args['heading'] ) && '' !== $args['heading']
	? $args['heading']
	: __( 'Receba o material agora', 'proenem-wordpress-theme' );
$capture_eyebrow  = isset( $args['eyebrow'] ) && '' !== $args['eyebrow']
	? $args['eyebrow']
	: __( 'Acesso imediato', 'proenem-wordpress-theme' );
$capture_anchor   = isset( $args['anchor'] ) ? $args['anchor'] : '';
$capture_field    = static function ( $name ) use ( $capture_instance ) {
	return 'pro-material-capture-' . $capture_instance . '-' . $name;
};
$capture_title_id = $capture_field( 'title' );
$capture_privacy  = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
?>
<aside
	<?php echo $capture_anchor ? 'id="' . esc_attr( $capture_anchor ) . '"' : ''; ?>
	class="pro-material-capture pro-material-capture--<?php echo esc_attr( $capture_instance ); ?>"
	aria-labelledby="<?php echo esc_attr( $capture_title_id ); ?>"
	data-pro-material-capture
>
	<span class="pro-material-capture__eyebrow"><?php echo esc_html( $capture_eyebrow ); ?></span>
	<h2 id="<?php echo esc_attr( $capture_title_id ); ?>"><?php echo esc_html( $capture_heading ); ?></h2>
	<p><?php esc_html_e( 'Enviamos o link de download para o contato que você informar.', 'proenem-wordpress-theme' ); ?></p>

	<form class="pro-material-capture__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-pro-material-capture-form>
		<input type="hidden" name="action" value="crm_leads_capture_free_material">
		<?php
		// wp_nonce_field() derives the id from the field name, which would
		// duplicate id="_wpnonce" across the two forms on this page.
		?>
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'crm_leads_capture_free_material' ) ); ?>">
		<?php wp_referer_field(); ?>
		<input type="hidden" name="material_id" value="<?php echo esc_attr( (string) $capture_material ); ?>">
		<input class="pro-material-capture__honeypot" type="text" name="crm_leads_capture_website" value="" autocomplete="off" tabindex="-1" aria-hidden="true">
		<?php
		if ( function_exists( 'crm_leads_capture_render_free_material_error_message' ) ) {
			crm_leads_capture_render_free_material_error_message();
		}
		?>
		<label class="pro-material-capture__field">
			<span><?php esc_html_e( 'Nome', 'proenem-wordpress-theme' ); ?></span>
			<input
				type="text"
				id="<?php echo esc_attr( $capture_field( 'name' ) ); ?>"
				name="name"
				autocomplete="name"
				placeholder="<?php esc_attr_e( 'Seu nome completo', 'proenem-wordpress-theme' ); ?>"
				aria-describedby="<?php echo esc_attr( $capture_field( 'name-error' ) ); ?>"
				required
			>
			<em class="pro-material-capture__error" id="<?php echo esc_attr( $capture_field( 'name-error' ) ); ?>" data-pro-capture-error hidden></em>
		</label>
		<label class="pro-material-capture__field">
			<span><?php esc_html_e( 'Email', 'proenem-wordpress-theme' ); ?></span>
			<input
				type="email"
				id="<?php echo esc_attr( $capture_field( 'email' ) ); ?>"
				name="email"
				autocomplete="email"
				placeholder="<?php esc_attr_e( 'voce@exemplo.com', 'proenem-wordpress-theme' ); ?>"
				aria-describedby="<?php echo esc_attr( $capture_field( 'email-error' ) ); ?>"
				required
			>
			<em class="pro-material-capture__error" id="<?php echo esc_attr( $capture_field( 'email-error' ) ); ?>" data-pro-capture-error hidden></em>
		</label>
		<label class="pro-material-capture__field">
			<span>
				<?php esc_html_e( 'WhatsApp', 'proenem-wordpress-theme' ); ?>
				<small><?php esc_html_e( '(opcional)', 'proenem-wordpress-theme' ); ?></small>
			</span>
			<input
				type="tel"
				id="<?php echo esc_attr( $capture_field( 'whatsapp' ) ); ?>"
				name="whatsapp"
				autocomplete="tel"
				inputmode="numeric"
				maxlength="16"
				placeholder="<?php esc_attr_e( '(00) 00000-0000', 'proenem-wordpress-theme' ); ?>"
				aria-describedby="<?php echo esc_attr( $capture_field( 'whatsapp-error' ) ); ?>"
				data-pro-capture-phone
			>
			<em class="pro-material-capture__error" id="<?php echo esc_attr( $capture_field( 'whatsapp-error' ) ); ?>" data-pro-capture-error hidden></em>
		</label>
		<button class="pen-button pen-button--primary pen-button--md pro-material-capture__button" type="submit">
			<?php esc_html_e( 'Baixar material gratuito', 'proenem-wordpress-theme' ); ?>
		</button>
	</form>

	<small class="pro-material-capture__privacy">
		<?php
		if ( $capture_privacy ) {
			printf(
				/* translators: %s: Link to the privacy policy. */
				esc_html__( 'Sem pagamento. Usamos seus dados para enviar o material e conteúdos de estudo, e você pode sair quando quiser. Veja a %s.', 'proenem-wordpress-theme' ),
				'<a href="' . esc_url( $capture_privacy ) . '">' . esc_html__( 'política de privacidade', 'proenem-wordpress-theme' ) . '</a>'
			);
		} else {
			esc_html_e( 'Sem pagamento. Usamos seus dados para enviar o material e conteúdos de estudo, e você pode sair quando quiser.', 'proenem-wordpress-theme' );
		}
		?>
	</small>
</aside>
