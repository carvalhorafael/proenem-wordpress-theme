<?php
/**
 * GitHub Releases theme updater integration.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROENEM_THEME_SLUG', 'proenem-wordpress-theme' );
define( 'PROENEM_THEME_REPOSITORY', 'carvalhorafael/proenem-wordpress-theme' );
define( 'PROENEM_THEME_UPDATE_URI', 'https://github.com/carvalhorafael/proenem-wordpress-theme' );
define( 'PROENEM_THEME_RELEASE_ASSET', 'proenem-wordpress-theme.zip' );
define( 'PROENEM_THEME_RELEASE_CACHE_KEY', 'proenem_theme_github_latest_release' );

add_filter( 'update_themes_github.com', 'proenem_filter_github_theme_update', 10, 4 );

/**
 * Provide WordPress with update data from GitHub Releases.
 *
 * @param array|false $update           Existing update data.
 * @param array       $theme_data       Theme headers.
 * @param string      $theme_stylesheet Theme stylesheet directory.
 * @param string[]    $locales          Installed locales.
 * @return array|false
 */
function proenem_filter_github_theme_update( $update, $theme_data, $theme_stylesheet, $locales ) {
	unset( $locales );

	if ( PROENEM_THEME_SLUG !== $theme_stylesheet ) {
		return $update;
	}

	if ( empty( $theme_data['UpdateURI'] ) || PROENEM_THEME_UPDATE_URI !== $theme_data['UpdateURI'] ) {
		return $update;
	}

	$release = proenem_get_latest_github_release();

	if ( ! $release ) {
		return $update;
	}

	$release_update = proenem_theme_update_from_release( $release, PROENEM_THEME_VERSION );

	return $release_update ? $release_update : $update;
}

/**
 * Fetch and cache the latest public GitHub release.
 *
 * @return array<string,mixed>|false
 */
function proenem_get_latest_github_release() {
	$cached_release = get_site_transient( PROENEM_THEME_RELEASE_CACHE_KEY );

	if ( is_array( $cached_release ) ) {
		return $cached_release;
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . PROENEM_THEME_REPOSITORY . '/releases/latest',
		array(
			'headers' => array(
				'Accept'               => 'application/vnd.github+json',
				'User-Agent'           => PROENEM_THEME_SLUG . '/' . PROENEM_THEME_VERSION,
				'X-GitHub-Api-Version' => '2022-11-28',
			),
			'timeout' => 10,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $release ) ) {
		return false;
	}

	set_site_transient( PROENEM_THEME_RELEASE_CACHE_KEY, $release, HOUR_IN_SECONDS );

	return $release;
}

/**
 * Convert a GitHub release payload into a WordPress theme update response.
 *
 * @param array<string,mixed> $release         GitHub release payload.
 * @param string              $current_version Currently installed theme version.
 * @return array<string,string>|false
 */
function proenem_theme_update_from_release( array $release, $current_version ) {
	$new_version = proenem_normalize_github_release_version( $release );

	if ( ! $new_version || version_compare( $new_version, $current_version, '<=' ) ) {
		return false;
	}

	$package_url = proenem_find_github_release_asset_url( $release, PROENEM_THEME_RELEASE_ASSET );

	if ( ! $package_url ) {
		return false;
	}

	return array(
		'theme'        => PROENEM_THEME_SLUG,
		'version'      => $new_version,
		'new_version'  => $new_version,
		'url'          => isset( $release['html_url'] ) && is_string( $release['html_url'] ) ? $release['html_url'] : PROENEM_THEME_UPDATE_URI,
		'package'      => $package_url,
		'requires'     => '6.5',
		'tested'       => '6.6',
		'requires_php' => '8.2',
	);
}

/**
 * Normalize a GitHub release tag into a semver-like WordPress version.
 *
 * @param array<string,mixed> $release GitHub release payload.
 * @return string
 */
function proenem_normalize_github_release_version( array $release ) {
	if ( empty( $release['tag_name'] ) || ! is_string( $release['tag_name'] ) ) {
		return '';
	}

	return ltrim( $release['tag_name'], 'vV' );
}

/**
 * Find the packaged theme ZIP in a GitHub release payload.
 *
 * @param array<string,mixed> $release    GitHub release payload.
 * @param string              $asset_name Expected release asset name.
 * @return string
 */
function proenem_find_github_release_asset_url( array $release, $asset_name ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}

	foreach ( $release['assets'] as $asset ) {
		if ( ! is_array( $asset ) ) {
			continue;
		}

		if (
			isset( $asset['name'], $asset['browser_download_url'] )
			&& $asset_name === $asset['name']
			&& is_string( $asset['browser_download_url'] )
		) {
			return $asset['browser_download_url'];
		}
	}

	return '';
}
