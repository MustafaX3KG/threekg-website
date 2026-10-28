<?php
/** Phase 3 plugin-active render and registration smoke harness. */
require_once __DIR__ . '/wp-stub.php';

$GLOBALS['threekg_acf_values'] = array();
$GLOBALS['threekg_acf_groups'] = array();
function get_field( $name, $scope = false ) { return $GLOBALS['threekg_acf_values'][ ( $scope ? $scope . ':' : '' ) . $name ] ?? false; }
function acf_add_local_field_group( $group ) { $GLOBALS['threekg_acf_groups'][] = $group; }
function acf_add_options_page( $args ) { $GLOBALS['threekg_options_page'] = $args; }
function acf_add_local_field() { return true; }

require_once __DIR__ . '/../../wp-plugins/threekg-core/threekg-core.php';
require_once $GLOBALS['threekg_theme_dir'] . 'functions.php';

do_action( 'init' );
do_action( 'acf/init' );
do_action( 'acf/include_fields' );

add_filter( 'the_content', function ( $content ) {
	return preg_replace_callback( '/<!-- wp:threekg\/([a-z-]+) \/-->/', static function ( $match ) {
		return threekg_core_render_block( array( 'name' => 'threekg/' . $match[1], 'id' => 'block_' . $match[1] ) );
	}, (string) $content );
} );

if ( 8 !== count( $GLOBALS['threekg_registered_blocks'] ?? array() ) ) {
	throw new RuntimeException( 'Expected eight registered blocks.' );
}
if ( 9 !== count( $GLOBALS['threekg_acf_groups'] ) ) {
	throw new RuntimeException( 'Expected eight block field groups plus global options.' );
}
if ( empty( $GLOBALS['threekg_patterns']['threekg/landing-page'] ) ) {
	throw new RuntimeException( 'Landing-page pattern was not registered.' );
}

$out_dir = __DIR__ . '/../render';
@mkdir( $out_dir, 0777, true );

function threekg_harness_saved_page( string $name, string $subdir = '' ): void {
	global $out_dir;
	$GLOBALS['threekg_is_front_page'] = true;
	$GLOBALS['threekg_subdir_uri'] = $subdir;
	$GLOBALS['threekg_post']['content'] = '<!-- wp:threekg/hero /--><!-- wp:threekg/showreel /--><!-- wp:threekg/marquee /--><!-- wp:threekg/featured-work /--><!-- wp:threekg/clients /--><!-- wp:threekg/studio /--><!-- wp:threekg/services-story /--><!-- wp:threekg/contact-cta /-->';
	$GLOBALS['threekg_enqueued_styles'] = array();
	$GLOBALS['threekg_enqueued_scripts'] = array();
	$GLOBALS['threekg_loop_done'] = false;
	ob_start();
	require $GLOBALS['threekg_theme_dir'] . 'front-page.php';
	$html = (string) ob_get_clean();
	if ( 1 !== substr_count( $html, 'data-threekg-contact' ) || 1 !== substr_count( $html, 'class="footer__cta reveal"' ) ) {
		throw new RuntimeException( 'Saved contact CTA was duplicated by the template footer.' );
	}
	file_put_contents( $out_dir . '/' . $name . '.html', $html );
	echo "rendered {$name}.html\n";
}

threekg_harness_saved_page( 'plugin-active-saved' );
threekg_harness_saved_page( 'plugin-active-saved-subdir', '/studio-site' );

// Active plugin + empty page must retain canonical defaults.
$GLOBALS['threekg_post']['content'] = '';
$GLOBALS['threekg_loop_done'] = false;
$GLOBALS['threekg_subdir_uri'] = '';
ob_start();
require $GLOBALS['threekg_theme_dir'] . 'front-page.php';
$empty = (string) ob_get_clean();
if ( false === strpos( $empty, 'data-threekg-hero="canonical"' ) || false !== strpos( $empty, 'data-threekg-contact' ) ) {
	throw new RuntimeException( 'Active-plugin empty-page fallback failed.' );
}
file_put_contents( $out_dir . '/plugin-active-empty.html', $empty );
echo "rendered plugin-active-empty.html\n";

// Multiple instances must have distinct roots and no legacy singleton IDs.
$a = threekg_core_render_block( array( 'name' => 'threekg/hero', 'id' => 'block_a' ) );
$b = threekg_core_render_block( array( 'name' => 'threekg/hero', 'id' => 'block_b' ) );
if ( false === strpos( $a, 'data-threekg-hero="hero-a"' ) || false === strpos( $b, 'data-threekg-hero="hero-b"' ) || false !== strpos( $a . $b, 'id="field"' ) ) {
	throw new RuntimeException( 'Hero instance scoping failed.' );
}

