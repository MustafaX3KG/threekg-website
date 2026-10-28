<?php
/**
 * Asset enqueues. Styles and scripts are never hardcoded into templates.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

const THREEKG_FONTS_URL = 'https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap';

add_filter(
	'wp_resource_hints',
	function ( array $urls, string $relation_type ): array {
		if ( 'preconnect' === $relation_type ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = 'https://fonts.gstatic.com';
		}
		return $urls;
	},
	10,
	2
);

/**
 * File-modified timestamp used as the enqueue version string.
 */
function threekg_asset_version( string $relative_path ): string {
	$path = get_theme_file_path( $relative_path );
	return file_exists( $path ) ? (string) filemtime( $path ) : '';
}

add_action(
	'wp_enqueue_scripts',
	function () {
		/*
		 * Google Fonts is loaded exactly once for the whole request -
		 * *never* self-hosted and linked at the same time (plan 1.8).
		 * Fonts can be moved to assets/fonts later by giving
		 * assets/fonts/README.md a `@font-face` sheet and enqueueing it
		 * here instead.
		 */
		wp_enqueue_style( 'threekg-fonts', THREEKG_FONTS_URL, array(), null );

		wp_enqueue_style(
			'threekg-main',
			get_theme_file_uri( 'assets/css/main.css' ),
			array( 'threekg-fonts' ),
			threekg_asset_version( 'assets/css/main.css' )
		);

		wp_enqueue_script(
			'threekg-main',
			get_theme_file_uri( 'assets/js/main.js' ),
			array(),
			threekg_asset_version( 'assets/js/main.js' ),
			true
		);

		/*
		 * Homepage-only animation bundles stay off generic templates.
		 */
		if ( is_front_page() ) {
			foreach ( array( 'hero-canvas', 'services-morph', 'showreel' ) as $module ) {
				wp_enqueue_script(
					"threekg-{$module}",
					get_theme_file_uri( "assets/js/{$module}.js" ),
					array(),
					threekg_asset_version( "assets/js/{$module}.js" ),
					true
				);
			}
		}
	}
);
