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
		if ( is_front_page() ) {
			$classes[] = 'is-loading';
		}
		return array_values( array_unique( $classes ) );
	}
);
