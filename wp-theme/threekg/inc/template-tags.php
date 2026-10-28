<?php
/**
 * Output helpers - every dynamic value escapes at the call site.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact e-mail address.
 */
function threekg_email(): string {
	return function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'email', 'hello@threekg.studio' ) : (string) get_theme_mod( 'threekg_email', 'hello@threekg.studio' );
}

/**
 * mailto: URL for the contact e-mail.
 */
function threekg_email_url(): string {
	return 'mailto:' . threekg_email();
}

/**
 * Footer CTA label.
 */
function threekg_cta_text(): string {
	$default  = 'Start a project';
	$mod = function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'cta_text', $default ) : (string) get_theme_mod( 'threekg_cta_text', $default );
	return '' === trim( $mod ) ? $default : $mod;
}

/**
 * Availability badge text.
 */
function threekg_availability(): string {
	return function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'availability', 'Available for projects' ) : (string) get_theme_mod( 'threekg_availability', 'Available for projects' );
}

/**
 * Header descriptor line.
 */
function threekg_descriptor(): string {
	return function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'descriptor', 'minimalism · space · motion' ) : (string) get_theme_mod( 'threekg_descriptor', 'minimalism · space · motion' );
}

/**
 * Location label (header + footer columns).
 */
function threekg_location(): string {
	return function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'location', 'working worldwide' ) : (string) get_theme_mod( 'threekg_location', 'working worldwide' );
}

/**
 * All non-empty social links, keyed by label.
 *
 * @return array<string, string>
 */
function threekg_social_links(): array {
	$socials = array(
		'Instagram' => function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'instagram', '' ) : (string) get_theme_mod( 'threekg_instagram', '' ),
		'LinkedIn'  => function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'linkedin', '' ) : (string) get_theme_mod( 'threekg_linkedin', '' ),
		'Behance'   => function_exists( 'threekg_core_option' ) ? (string) threekg_core_option( 'behance', '' ) : (string) get_theme_mod( 'threekg_behance', '' ),
	);

	foreach ( $socials as $label => $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			unset( $socials[ $label ] );
		}
	}

	return $socials;
}

/**
 * Brand/wordmark output: custom logo when available, text otherwise.
 */
function threekg_the_brand(): void {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	printf(
		'<a class="brand" href="%s">ThreeKG<span class="brand__reg">&reg;</span></a>',
		esc_url( threekg_section_url( 'top' ) )
	);
}

/**
 *琅 Fallback navigation render.
 *
 * @param string $location primary|footer|social
 */
function threekg_the_menu( string $location ): void {
	wp_nav_menu( array(
		'theme_location' => $location,
		'container'      => false,
		'depth'          => 1,
		'fallback_cb'    => 'threekg_' . $location . '_menu_fallback',
	) );
}
