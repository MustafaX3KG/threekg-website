<?php
/**
 * Renders each ThreeKG theme template with real PHP CLI against minimal
 * WP stubs and writes the output HTML into build/render/.
 *
 * Usage: tools\php\php.exe build\wp-guard\render.php
 *
 * @package threekg-build-harness
 */

require_once __DIR__ . '/wp-stub.php';

$GLOBALS['threekg_theme_dir_shared'] = $GLOBALS['threekg_theme_dir'];
$GLOBALS['threekg_render_out_dir'] = __DIR__ . '/../render';

@mkdir( $GLOBALS['threekg_render_out_dir'], 0777, true );

// Load the theme exactly as WordPress does: functions.php pulls in all
// modules (setup, enqueue, customizer, template-tags).
require_once $GLOBALS['threekg_theme_dir'] . 'functions.php';

/**
 * Reset per-document state.
 */
function threekg_reset_doc_state(): void {
	$GLOBALS['threekg_enqueued_styles'] = array();
	$GLOBALS['threekg_enqueued_scripts'] = array();
	$GLOBALS['threekg_loop_done'] = false;
	$GLOBALS['threekg_is_front_page'] = false;
	$GLOBALS['threekg_have_posts'] = true;
	$GLOBALS['threekg_subdir_uri'] = '';
	$GLOBALS['threekg_post']['type'] = 'post';
}

/**
 * Render one document.
 *
 * @param string   $docKey Output filename without extension.
 * @param callable $setup  Sets the query/front-page state.
 * @param callable $body   Emits the main template content.
 */
function threekg_render_doc( string $docKey, callable $setup, callable $body ): void {
	threekg_reset_doc_state();
	$setup();

	ob_start();
	require $GLOBALS['threekg_theme_dir'] . 'header.php';
	$head = ob_get_clean();

	ob_start();
	$body();
	$main = ob_get_clean();

	ob_start();
	require $GLOBALS['threekg_theme_dir'] . 'footer.php';
	$foot = ob_get_clean();

	$path = $GLOBALS['threekg_render_out_dir'] . '/' . $docKey . '.html';
	file_put_contents( $path, $head . $main . $foot );
	echo "rendered {$docKey}.html\n";
}

// ------------------------------------------------ front page
threekg_render_doc( 'front-page', function () {
	$GLOBALS['threekg_is_front_page'] = true;
}, function () {
	do_action( 'wp_enqueue_scripts' );
	foreach ( array( 'hero', 'showreel', 'marquee', 'work', 'clients', 'studio', 'services' ) as $section ) {
		echo "\n";
		get_template_part( 'template-parts/section/' . $section );
	}
} );

// ------------------------------------------------ generic index/archive
threekg_render_doc( 'index', function () {
	$GLOBALS['threekg_is_front_page'] = false;
}, function () {
	do_action( 'wp_enqueue_scripts' );
} );

// ------------------------------------------------ page
threekg_render_doc( 'page', function () {
	$GLOBALS['threekg_is_front_page'] = false;
	$GLOBALS['threekg_post']['type'] = 'page';
}, function () {
	do_action( 'wp_enqueue_scripts' );
	while ( have_posts() ) {
		the_post();
		echo "<article class=\"page\">\n";
		echo "<header class=\"section-head reveal\"><span class=\"mono\">[page]</span><h1>";
		the_title();
		echo "</h1></header>\n";
		echo "<div class=\"entry-content\">\n";
		the_content();
		echo "</div>\n";
		echo "</article>\n";
	}
} );

// ------------------------------------------------ single post
threekg_render_doc( 'single', function () {
	$GLOBALS['threekg_is_front_page'] = false;
	$GLOBALS['threekg_post']['type'] = 'post';
}, function () {
	do_action( 'wp_enqueue_scripts' );
	while ( have_posts() ) {
		the_post();
		echo "<article class=\"post hentry\">\n";
		echo "<header class=\"section-head reveal\"><span class=\"mono\">[post]</span><h1>";
		the_title();
		echo "</h1></header>\n";
		echo "<div class=\"entry-content\">\n";
		the_content();
		echo "</div>\n";
		echo "</article>\n";
	}
} );

// ------------------------------------------------ subdirectory install variant
threekg_render_doc( 'subdir-front-page', function () {
	$GLOBALS['threekg_is_front_page'] = true;
	$GLOBALS['threekg_subdir_uri'] = '/studio-site';
}, function () {
	foreach ( array( 'hero', 'showreel', 'marquee', 'work', 'clients', 'studio', 'services' ) as $section ) {
		get_template_part( 'template-parts/section/' . $section );
	}
} );

threekg_render_doc( '404', function () {
	$GLOBALS['threekg_is_front_page'] = false;
	$GLOBALS['threekg_have_posts'] = false;
}, function () {
	do_action( 'wp_enqueue_scripts' );
	echo "<section class=\"error404\">\n";
	echo "<p class=\"mono error404__kicker\">(lost signal)</p>\n";
	echo "<h1 class=\"error404__code\">404</h1>\n";
	echo "<p class=\"error404__copy\">The page you asked for is not here — but the studio is.</p>\n";
	echo "</section>\n";
} );
