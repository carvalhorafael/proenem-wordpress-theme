<?php
/**
 * Required plugin dependency notices.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugins required for full theme functionality.
 *
 * @return array<string,array{name:string,file:string}>
 */
function proenem_get_required_plugins() {
	return array(
		'free-materials'    => array(
			'name' => __( 'Free Materials', 'proenem-wordpress-theme' ),
			'file' => 'free-materials/free-materials.php',
		),
		'testimonials'      => array(
			'name' => __( 'Testimonials', 'proenem-wordpress-theme' ),
			'file' => 'testimonials/testimonials.php',
		),
		'crm-leads-capture' => array(
			'name' => __( 'CRM Leads Capture', 'proenem-wordpress-theme' ),
			'file' => 'crm-leads-capture/crm-leads-capture.php',
		),
	);
}

/**
 * Load WordPress plugin administration helpers when needed.
 *
 * @return void
 */
function proenem_load_plugin_dependency_helpers() {
	if ( function_exists( 'is_plugin_active' ) && function_exists( 'get_plugins' ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Check whether a required plugin is active.
 *
 * @param string $plugin_file Plugin file path relative to the plugins directory.
 * @return bool
 */
function proenem_is_required_plugin_active( $plugin_file ) {
	proenem_load_plugin_dependency_helpers();

	return is_plugin_active( $plugin_file );
}

/**
 * Get required plugins that are missing or inactive.
 *
 * @return array<string,array{name:string,file:string,installed:bool}>
 */
function proenem_get_unmet_required_plugins() {
	proenem_load_plugin_dependency_helpers();

	$installed_plugins = get_plugins();
	$unmet_plugins     = array();

	foreach ( proenem_get_required_plugins() as $plugin_slug => $plugin ) {
		if ( proenem_is_required_plugin_active( $plugin['file'] ) ) {
			continue;
		}

		$plugin['installed']           = array_key_exists( $plugin['file'], $installed_plugins );
		$unmet_plugins[ $plugin_slug ] = $plugin;
	}

	return $unmet_plugins;
}

/**
 * Render an admin notice when required plugins are not active.
 *
 * @return void
 */
function proenem_render_required_plugins_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$unmet_plugins = proenem_get_unmet_required_plugins();

	if ( empty( $unmet_plugins ) ) {
		return;
	}

	$plugin_names = wp_list_pluck( $unmet_plugins, 'name' );
	?>
	<div class="notice notice-warning">
		<p>
			<strong><?php esc_html_e( 'Tema Proenem: plugins obrigatórios ausentes ou inativos.', 'proenem-wordpress-theme' ); ?></strong>
		</p>
		<p>
			<?php
			printf(
				/* translators: %s: Required plugin names. */
				esc_html__( 'Para funcionar corretamente, este tema precisa que os seguintes plugins estejam instalados e ativos: %s.', 'proenem-wordpress-theme' ),
				esc_html( implode( wp_get_list_item_separator(), $plugin_names ) )
			);
			?>
		</p>
		<p>
			<?php esc_html_e( 'Instale ou ative os plugins necessários para remover este aviso.', 'proenem-wordpress-theme' ); ?>
			<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Ir para Plugins', 'proenem-wordpress-theme' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'proenem_render_required_plugins_notice' );
