<?php
/**
 * Test double for the free-materials metadata contract.
 *
 * The plugin owns the material metadata and this test environment does not
 * mount it. The theme depends only on these function names, which the plugin
 * publishes as a stable contract, so declaring them here lets the theme's own
 * helpers be exercised. Every declaration is guarded, so the real plugin wins
 * whenever it is present.
 *
 * @package Proenem
 */

if ( ! function_exists( 'free_materials_format_meta_key' ) ) {
	/**
	 * Meta key for the material format.
	 *
	 * @return string
	 */
	function free_materials_format_meta_key() {
		return '_free_materials_format';
	}
}

if ( ! function_exists( 'free_materials_pages_meta_key' ) ) {
	/**
	 * Meta key for the material page or item count.
	 *
	 * @return string
	 */
	function free_materials_pages_meta_key() {
		return '_free_materials_pages';
	}
}

if ( ! function_exists( 'free_materials_file_size_meta_key' ) ) {
	/**
	 * Meta key for the material file size.
	 *
	 * @return string
	 */
	function free_materials_file_size_meta_key() {
		return '_free_materials_file_size';
	}
}

if ( ! function_exists( 'free_materials_level_meta_key' ) ) {
	/**
	 * Meta key for who the material is for.
	 *
	 * @return string
	 */
	function free_materials_level_meta_key() {
		return '_free_materials_level';
	}
}

if ( ! function_exists( 'free_materials_highlights_meta_key' ) ) {
	/**
	 * Meta key for the list of topics inside the material.
	 *
	 * @return string
	 */
	function free_materials_highlights_meta_key() {
		return '_free_materials_highlights';
	}
}

if ( ! function_exists( 'free_materials_featured_meta_key' ) ) {
	/**
	 * Meta key for the catalog feature flag.
	 *
	 * @return string
	 */
	function free_materials_featured_meta_key() {
		return '_free_materials_featured';
	}
}

if ( ! function_exists( 'free_materials_downloads_meta_key' ) ) {
	/**
	 * Meta key for the download count shown as social proof.
	 *
	 * @return string
	 */
	function free_materials_downloads_meta_key() {
		return '_free_materials_downloads';
	}
}

if ( ! function_exists( 'free_materials_formats' ) ) {
	/**
	 * Known material formats as slug keyed labels.
	 *
	 * @return array<string, string>
	 */
	function free_materials_formats() {
		return array(
			'pdf'       => 'PDF',
			'planner'   => 'Planner',
			'checklist' => 'Checklist',
		);
	}
}

if ( ! function_exists( 'free_materials_format_label' ) ) {
	/**
	 * Label for a material format slug.
	 *
	 * @param string $slug Format slug.
	 * @return string
	 */
	function free_materials_format_label( $slug ) {
		$formats = free_materials_formats();

		return isset( $formats[ $slug ] ) ? $formats[ $slug ] : '';
	}
}
