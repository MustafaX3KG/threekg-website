<?php
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function (): void {
		if ( ! threekg_core_has_acf_pro() ) {
			return;
		}
		$slugs = array( 'hero', 'showreel', 'marquee', 'featured-work', 'clients', 'studio', 'services-story', 'contact-cta' );
		foreach ( $slugs as $slug ) {
			register_block_type( THREEKG_CORE_PATH . 'blocks/' . $slug );
		}

		register_block_pattern_category( 'threekg', array( 'label' => __( 'ThreeKG', 'threekg-core' ) ) );
		register_block_pattern(
			'threekg/landing-page',
			array(
				'title'       => __( 'ThreeKG Landing Page', 'threekg-core' ),
				'description' => __( 'The canonical editable ThreeKG landing-page section order.', 'threekg-core' ),
				'categories'  => array( 'threekg' ),
				'blockTypes'  => array( 'core/post-content' ),
				'content'     => "<!-- wp:threekg/hero /-->\n<!-- wp:threekg/showreel /-->\n<!-- wp:threekg/marquee /-->\n<!-- wp:threekg/featured-work /-->\n<!-- wp:threekg/clients /-->\n<!-- wp:threekg/studio /-->\n<!-- wp:threekg/services-story /-->\n<!-- wp:threekg/contact-cta /-->",
			)
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	function (): void {
		wp_enqueue_style( 'threekg-core-editor', THREEKG_CORE_URL . 'assets/editor.css', array(), THREEKG_CORE_VERSION );
	}
);

function threekg_core_render_block( array $block, string $content = '', bool $is_preview = false, int $post_id = 0 ): string {
	if ( ! threekg_core_has_acf_pro() ) {
		return $is_preview ? '<div class="threekg-editor-missing">' . esc_html__( 'Activate ACF Pro to edit this ThreeKG section.', 'threekg-core' ) . '</div>' : '';
	}
	$slug = str_replace( 'acf/', '', (string) ( $block['name'] ?? '' ) );
	$slug = str_replace( 'threekg/', '', $slug );
	$file = THREEKG_CORE_PATH . 'templates/' . sanitize_file_name( $slug ) . '.php';
	if ( ! is_readable( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	return (string) ob_get_clean();
}

