<?php
/**
 * Theme setup: supports, menu locations and fallbacks.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'threekg', get_theme_file_path( 'languages' ) );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', array(
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'customize-selective-refresh-widgets' );

		register_nav_menus( array(
			'primary' => __( 'Primary (overlay menu)', 'threekg' ),
			'footer'  => __( 'Footer (site column)', 'threekg' ),
			'social'  => __( 'Social links (menu cents)', 'threekg' ),
		) );
	}
);

/**
 * URL for a homepage section fragment.
 *
 * On the front page returns `#fragment`; on every other page returns
 * the frontend page URL followed by `#fragment` so inner pages can link
 * to homepage sections directly.
 *
 * @param string $fragment Section id, without the `#`.
 * @return string Unescaped URL - call esc_url() at the output site.
 */
function threekg_section_url( string $fragment ): string {
	$fragment = sanitize_key( $fragment );

	if ( is_front_page() ) {
		return '#' . $fragment;
	}

	return home_url( user_trailingslashit( '/' ) ) . '#' . $fragment;
}

/**
 * Fallback for the primary (overlay) menu when none is set.
 */
function threekg_primary_menu_fallback(): void {
	$links = array(
		'01' => array( 'Index', 'top' ),
		'02' => array( 'Showreel', 'showreel' ),
		'03' => array( 'Work', 'work' ),
		'04' => array( 'Clients', 'clients' ),
		'05' => array( 'Studio', 'studio' ),
		'06' => array( 'Contact', 'contact' ),
	);

	echo '<ul class="menu__list">';
	foreach ( $links as $number => $link ) {
		printf(
			'<li><a href="%s"><span class="mono">%s</span> %s</a></li>',
			esc_url( threekg_section_url( $link[1] ) ),
			esc_html( $number ),
			esc_html( $link[0] )
		);
	}
	echo '</ul>';
}

/**
 * Fallback for the footer (site column) menu when none is set.
 */
function threekg_footer_menu_fallback(): void {
	$links = array(
		'Back to top' => 'top',
		'Index'       => 'top',
		'Work'        => 'work',
	);

	echo '<ul class="footer__list">';
	foreach ( $links as $label => $fragment ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( threekg_section_url( $fragment ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Fallback for the social links menu when none is set.
 *
 * Emits nothing when every social setting is empty so no placeholder
 * links are shown on production.
 */
function threekg_social_menu_fallback(): void {
	$socials = threekg_social_links();
	if ( ! $socials ) {
		return;
	}

	echo '<ul class="socials__list">';
	foreach ( $socials as $label => $url ) {
		printf( '<li><a href="%s" rel="noopener" target="_blank">%s &#8599;</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
