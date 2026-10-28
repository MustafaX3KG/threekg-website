<?php
/**
 * Plugin Name: ThreeKG Core
 * Description: Elementor widgets, native ACF blocks and global settings for the ThreeKG theme.
 * Version: 2.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: ThreeKG
 * Text Domain: threekg-core
 */

defined( 'ABSPATH' ) || exit;

define( 'THREEKG_CORE_VERSION', '2.0.0' );
define( 'THREEKG_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'THREEKG_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once THREEKG_CORE_PATH . 'inc/helpers.php';
require_once THREEKG_CORE_PATH . 'inc/blocks.php';
require_once THREEKG_CORE_PATH . 'inc/fields.php';
require_once THREEKG_CORE_PATH . 'inc/options.php';
require_once THREEKG_CORE_PATH . 'inc/elementor.php';

add_action( 'admin_notices', 'threekg_core_notice' );

/** Show a dependency notice without affecting public rendering. */
function threekg_core_notice(): void {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$needs = array();
	if ( ! threekg_core_elementor_active() ) {
		$needs[] = __( 'Elementor (for drag-and-drop editing of ThreeKG sections)', 'threekg-core' );
	}
	if ( ! threekg_core_has_acf_pro() ) {
		$needs[] = __( 'Advanced Custom Fields Pro (for the editable global ThreeKG Settings page and ACF blocks)', 'threekg-core' );
	}

	if ( ! $needs ) {
		return;
	}

	?>
	<div class="notice notice-warning is-dismissible">
		<p><strong><?php esc_html_e( 'ThreeKG Core needs plugin dependencies for full editing:', 'threekg-core' ); ?></strong></p>
		<p>
			<?php foreach ( $needs as $needed ) : ?>
				<span class="block">• <?php echo esc_html( $needed ); ?></span>
			<?php endforeach; ?>
		</p>
		<p><?php esc_html_e( 'The public site keeps rendering the canonical defaults until the plugins are installed — no broken pages.', 'threekg-core' ); ?></p>
	</div>
	<?php
}

/** Whether the Elementor editor plugin is loaded. */
function threekg_core_elementor_active(): bool {
	return did_action( 'elementor/loaded' ) > 0 || class_exists( '\Elementor\Plugin' );
}
