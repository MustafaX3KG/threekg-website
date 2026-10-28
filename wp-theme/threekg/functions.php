<?php
/**
 * ThreeKG theme bootstrap.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

require get_theme_file_path( 'inc/setup.php' );
require get_theme_file_path( 'inc/enqueue.php' );
require get_theme_file_path( 'inc/customizer.php' );
require get_theme_file_path( 'inc/template-tags.php' );

/**
 * Add the canonical loading state ONLY where the loader markup is
 * guaranteed to render (front page; see header.php). Inner pages never
 * receive `is-loading`, so no template can be scroll-locked without a
 * loader to remove it.
 */
add_filter(
	'body_class',
	function ( array $classes ): array {
		$loader_enabled = ! function_exists( 'threekg_core_option' ) || (bool) threekg_core_option( 'loader_enabled', true );
		if ( is_front_page() && $loader_enabled ) {
			$classes[] = 'is-loading';
		}
		return array_values( array_unique( $classes ) );
	}
);

/** Whether the current page has at least one saved ThreeKG section block. */
function threekg_has_saved_sections( ?int $post_id = null ): bool {
	if ( ! function_exists( 'threekg_core_has_acf_pro' ) || ! threekg_core_has_acf_pro() ) {
		return false;
	}
	$post_id = $post_id ?: (int) get_queried_object_id();
	$content = $post_id ? (string) get_post_field( 'post_content', $post_id ) : '';
	return '' !== $content && (bool) preg_match( '/<!--\s+wp:threekg\//', $content );
}

/** Test a saved page for a specific ThreeKG block. */
function threekg_has_saved_block( string $name, ?int $post_id = null ): bool {
	if ( ! threekg_has_saved_sections( $post_id ) ) {
		return false;
	}
	$post_id = $post_id ?: (int) get_queried_object_id();
	return has_block( 'threekg/' . $name, (string) get_post_field( 'post_content', $post_id ) );
}
