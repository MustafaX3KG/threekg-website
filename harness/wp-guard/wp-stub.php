<?php
/**
 * Minimal WordPress function stubs for rendering the ThreeKG theme
 * templates through real PHP CLI (no WP runtime available locally).
 * Built evidence only — never shipped in the theme ZIP.
 *
 * @package threekg-build-harness
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

// ---------------------------------------------------------------- generic
function __( $text, $domain = null ) { return $text; }
function esc_html__( $text, $domain = null ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8', false ); }
function esc_attr__( $text, $domain = null ) { return esc_html__( $text ); }
function esc_html_e( $text, $domain = null ) { echo esc_html__( $text ); }
function esc_attr_e( $text, $domain = null ) { echo esc_html__( $text ); }
function _e( $text, $domain = null ) { echo esc_html__( $text ); }

function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8', false ); }
function esc_attr( $text ) { return esc_html( $text ); }
function esc_url( $url ) { $url = (string) $url; return htmlentities( $url, ENT_QUOTES, 'UTF-8', false ); }
function esc_url_raw( $url ) { return filter_var( (string) $url, FILTER_SANITIZE_URL ) ?: ''; }
function esc_js( $text ) { return json_encode( (string) $text ); }
function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ); }
function sanitize_email( $email ) { return filter_var( (string) $email, FILTER_VALIDATE_EMAIL ) ? (string) $email : ''; }
function sanitize_text_field( $text ) { return trim( strip_tags( (string) $text ) ); }
function wp_strip_all_tags( $text ) { return trim( preg_replace( '/<[^>]*>/', ' ', (string) $text ) ); }
function wp_kses_post( $text ) { return (string) $text; }

// ---------------------------------------------------------------- hooks
$GLOBALS['threekg_hooks'] = array();
function add_action( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['threekg_hooks'][ $hook ][] = $cb;
	return true;
}
function add_filter( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['threekg_hooks'][ $hook ][] = $cb;
	return true;
}
function do_action( $hook ) {
	foreach ( ( $GLOBALS['threekg_hooks'][ $hook ] ?? array() ) as $cb ) {
		call_user_func_array( $cb, array_slice( func_get_args(), 1 ) );
	}
}
function apply_filters( $hook, $value ) {
	foreach ( ( $GLOBALS['threekg_hooks'][ $hook ] ?? array() ) as $cb ) {
		$value = call_user_func_array( $cb, array_slice( func_get_args(), 1 ) );
	}
	return $value;
}
function has_action( $hook ) { return ! empty( $GLOBALS['threekg_hooks'][ $hook ] ); }

// ---------------------------------------------------------------- theme paths
$GLOBALS['threekg_theme_dir'] = realpath( __DIR__ . '/../../wp-theme/threekg' ) . '/';
function get_theme_file_path( $file = '' ) { return $GLOBALS['threekg_theme_dir'] . ltrim( (string) $file, '/' ); }
function get_theme_file_uri( $file = '' ) {
	$subdir = ! empty( $GLOBALS['threekg_subdir_uri'] ) ? $GLOBALS['threekg_subdir_uri'] : '';
	return $subdir . '/wp-content/themes/threekg/' . ltrim( (string) $file, '/' );
}

// ---------------------------------------------------------------- query state
$GLOBALS['threekg_post'] = array(
	'ID'           => 42,
	'title'        => 'Welcome to ThreeKG',
	'content'      => "<p>Placeholder entry content rendered from WordPress.</p>\n<h2>Second-level heading</h2>\n<p>More editor content follows here.</p>",
	'permalink'    => '/welcome-to-threekg/',
	'date'         => 'October 28, 2026',
	'author'       => 'ThreeKG Studio',
	'excerpt'      => 'Placeholder excerpt for the archive list.',
	'thumbnail'    => false,
	'type'         => 'post',
	'classes'      => '',
	'edit_link'    => '/wp-admin/post.php?post=42',
);
$GLOBALS['threekg_have_posts'] = true;
$GLOBALS['threekg_loop_done'] = false;

function have_posts() {
	$have = ! $GLOBALS['threekg_loop_done'] && $GLOBALS['threekg_have_posts'];
	$GLOBALS['threekg_loop_done'] = true;
	return $have;
}
function the_post() { /* no-op single-post loop */ }
function is_singular( $type = '' ) { return true; }
function get_the_ID() { return $GLOBALS['threekg_post']['ID']; }
function get_the_title() { return $GLOBALS['threekg_post']['title']; }
function the_title() { echo esc_html( $GLOBALS['threekg_post']['title'] ); }
function get_the_content() { return $GLOBALS['threekg_post']['content']; }
function the_content() { echo apply_filters( 'the_content', $GLOBALS['threekg_post']['content'] ); }
function get_the_permalink() { return $GLOBALS['threekg_post']['permalink']; }
function the_permalink() { echo esc_url( $GLOBALS['threekg_post']['permalink'] ); }
function get_the_date() { return $GLOBALS['threekg_post']['date']; }
function the_date() { echo esc_html( $GLOBALS['threekg_post']['date'] ); }
function get_the_author() { return $GLOBALS['threekg_post']['author']; }
function the_author() { echo esc_html( $GLOBALS['threekg_post']['author'] ); }
function get_the_excerpt() { return $GLOBALS['threekg_post']['excerpt']; }
function has_excerpt() { return ! empty( $GLOBALS['threekg_post']['excerpt'] ); }
function has_post_thumbnail() { return (bool) $GLOBALS['threekg_post']['thumbnail']; }
function the_post_thumbnail( $size = '' ) { echo '<img src="/wp-content/uploads/sample.jpg" alt="" />' . "\n"; }
function post_class( $extra = '' ) {
	$classes = trim( 'hentry ' . ( is_array( $extra ) ? implode( ' ', $extra ) : (string) $extra ) );
	echo ' class="' . esc_attr( $classes ) . '"';
}
function body_class( $extra = '' ) {
	$classes = apply_filters(
		'body_class',
		is_array( $extra ) ? $extra : array_filter( explode( ' ', (string) $extra ) )
	);
	echo ' class="' . esc_attr( trim( implode( ' ', array_unique( $classes ) ) ) ) . '"';
}
function comments_open() { return false; }
function get_comments_number() { return 0; }
function comments_template() {}
function get_edit_post_link() { return $GLOBALS['threekg_post']['edit_link']; }
function edit_post_link( $text = 'Edit' ) {
	printf( '<a href="%s">%s</a>', esc_url( $GLOBALS['threekg_post']['edit_link'] ), esc_html( $text ) );
}
function wp_link_pages( $args = array() ) {}
function the_posts_pagination( $args = array() ) {
	$GLOBALS['threekg_pagination'] = $args;
	echo "<nav class=\"pagination mono\"><a href=\"&larr;\">&larr;</a><a href=\"/page/2/\">2</a><a href=\"&rarr;\">&rarr;</a></nav>\n";
}
function get_the_archive_title() { return 'Journal'; }

// ---------------------------------------------------------------- templates
$GLOBALS['threekg_stack'] = array( 'header' => true );
function get_header( $name = '' ) { require $GLOBALS['threekg_theme_dir'] . 'header.php'; }
function get_footer( $name = '' ) { require $GLOBALS['threekg_theme_dir'] . 'footer.php'; }
function get_template_part( $slug, $name = '' ) {
	$file = get_theme_file_path( ( $name ? "{$slug}-{$name}" : $slug ) . '.php' );
	require $file;
}

// ---------------------------------------------------------------- head/body
function wp_head() {
	do_action( 'wp_head' );
	do_action( 'wp_enqueue_scripts' );
	threekg_print_head_assets();
}
function wp_footer() {
	do_action( 'wp_footer' );
	threekg_print_footer_assets();
}
function wp_body_open() { do_action( 'wp_body_open' ); }
function language_attributes() { echo 'lang="en-GB"'; }
function bloginfo( $show = '' ) {
	echo $show === 'charset' ? 'UTF-8' : 'ThreeKG — WordPress';
}
function get_bloginfo( $show = '' ) { return 'ThreeKG — WordPress'; }

// ---------------------------------------------------------------- settings
function get_theme_mod( $id, $default = '' ) {
	$over = $GLOBALS['threekg_theme_mods'][ $id ] ?? null;
	return $over !== null ? $over : $default;
}
function has_custom_logo() { return ! empty( $GLOBALS['threekg_custom_logo'] ); }
function the_custom_logo() {
	echo '<a class="custom-logo-link is-visible" href="/"><img class="custom-logo" src="/wp-content/uploads/logo.png" alt="ThreeKG" /></a>' . "\n";
}
function get_option( $name ) {
	$opts = array(
		'page_for_posts' => 7,
	);
	return $opts[ $name ] ?? false;
}
function home_url( $path = '' ) {
	$subdir = ! empty( $GLOBALS['threekg_subdir_uri'] ) ? $GLOBALS['threekg_subdir_uri'] : '';
	return 'http://localhost:8123' . $subdir . '/' . ltrim( (string) $path, '/' );
}
function user_trailingslashit( $path = '' ) {
	return rtrim( (string) $path, '/' ) . '/';
}

// ---------------------------------------------------------------- taxonomies
function is_front_page() { return ! empty( $GLOBALS['threekg_is_front_page'] ); }
function is_home() { return empty( $GLOBALS['threekg_is_front_page'] ); }

// ---------------------------------------------------------------- enqueue
$GLOBALS['threekg_enqueued_styles'] = array();
$GLOBALS['threekg_enqueued_scripts'] = array();
function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false ) {
	$GLOBALS['threekg_enqueued_styles'][ $handle ] = array( 'src' => $src, 'ver' => $ver );
}
function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $footer = false ) {
	$GLOBALS['threekg_enqueued_scripts'][ $handle ] = array( 'src' => $src, 'ver' => $ver, 'footer' => $footer );
}

/** Prints head links from the recorded enqueues. */
function threekg_print_head_assets() {
	foreach ( $GLOBALS['threekg_enqueued_styles'] as $handle => $script ) {
		$src = $script['src'] . ( $script['ver'] ? '?ver=' . $script['ver'] : '' );
		printf( "<link rel=\"stylesheet\" id=\"%s-css\" href=\"%s\" media=\"all\" />\n", esc_attr( $handle ), esc_url( $src ) );
	}
}

/** Prints footer scripts from the recorded enqueues. */
function threekg_print_footer_assets() {
	foreach ( $GLOBALS['threekg_enqueued_scripts'] as $handle => $script ) {
		if ( empty( $script['footer'] ) ) continue;
		$src = $script['src'] . ( $script['ver'] ? '?ver=' . $script['ver'] : '' );
		printf( "<script id=\"%s\" src=\"%s\"></script>\n", esc_attr( $handle ), esc_url( $src ) );
	}
}

// ---------------------------------------------------------------- menus
function wp_nav_menu( $args = array() ) {
	$defaults = array(
		'theme_location' => '',
		'container'      => 'div',
		'fallback_cb'    => 'wp_page_menu',
		'depth'          => 0,
	);
	$args = array_merge( $defaults, $args );
	// Simulate "no menu assigned" so fallback_cb runs (as WP does).
	if ( ! empty( $args['fallback_cb'] ) && is_callable( $args['fallback_cb'] ) ) {
		call_user_func( $args['fallback_cb'] );
	}
}

// ---------------------------------------------------------------- misc admin
function load_theme_textdomain( $domain, $path = false ) { return true; }
function add_theme_support( $feature, ...$args ) {
	$GLOBALS['threekg_theme_supports'][ $feature ] = $args;
	return true;
}
function register_nav_menus( $locations ) {
	$GLOBALS['threekg_menu_locations'] = array_merge( $GLOBALS['threekg_menu_locations'] ?? array(), $locations );
	return true;
}
defined( 'ABSPATH' ) || define( 'ABSPATH', $GLOBALS['threekg_theme_dir'] );
