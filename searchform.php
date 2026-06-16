<?php
/**
 * Search form template.
 *
 * @package Proenem
 */

$proenem_unique_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="search-form__label" for="<?php echo esc_attr( $proenem_unique_id ); ?>">
		<?php esc_html_e( 'Search', 'proenem-wordpress-theme' ); ?>
	</label>
	<div class="search-form__controls">
		<input id="<?php echo esc_attr( $proenem_unique_id ); ?>" class="search-form__field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'proenem-wordpress-theme' ); ?>">
		<button class="search-form__submit" type="submit"><?php esc_html_e( 'Search', 'proenem-wordpress-theme' ); ?></button>
	</div>
</form>
