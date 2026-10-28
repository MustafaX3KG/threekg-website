<?php
/** Plugin active / ACF Pro inactive guard smoke test. */
require_once __DIR__ . '/wp-stub.php';
require_once __DIR__ . '/../../wp-plugins/threekg-core/threekg-core.php';
require_once $GLOBALS['threekg_theme_dir'] . 'functions.php';

$GLOBALS['threekg_is_front_page'] = true;
$GLOBALS['threekg_post']['content'] = '<!-- wp:threekg/hero /-->';
$GLOBALS['threekg_loop_done'] = false;

ob_start();
do_action( 'admin_notices' );
$notice = (string) ob_get_clean();
if ( false === strpos( $notice, 'Advanced Custom Fields Pro' ) || false === strpos( $notice, 'is-dismissible' ) ) {
	throw new RuntimeException( 'Missing actionable dismissible ACF Pro notice.' );
}

ob_start();
require $GLOBALS['threekg_theme_dir'] . 'front-page.php';
$html = (string) ob_get_clean();
if ( false === strpos( $html, 'data-threekg-hero="canonical"' ) || false !== strpos( $html, 'data-threekg-hero="hero-' ) ) {
	throw new RuntimeException( 'ACF-inactive front page did not use canonical fallback exclusively.' );
}
file_put_contents( __DIR__ . '/../render/acf-inactive-fallback.html', $html );
echo "rendered acf-inactive-fallback.html and verified dismissible notice\n";

