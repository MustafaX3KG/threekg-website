<?php
/**
 * Plugin Name: ThreeKG Core
 * Description: Native Block Editor sections and global settings for the ThreeKG theme.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: ThreeKG
 * Text Domain: threekg-core
 */

defined( 'ABSPATH' ) || exit;

define( 'THREEKG_CORE_VERSION', '1.0.0' );
define( 'THREEKG_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'THREEKG_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once THREEKG_CORE_PATH . 'inc/helpers.php';
require_once THREEKG_CORE_PATH . 'inc/blocks.php';
require_once THREEKG_CORE_PATH . 'inc/fields.php';
require_once THREEKG_CORE_PATH . 'inc/options.php';

add_action( 'admin_notices', 'threekg_core_acf_notice' );

/** Show a dependency notice without affecting public rendering. */
function threekg_core_acf_notice(): void {
	if ( threekg_core_has_acf_pro() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning is-dismissible">
		<p><strong><?php esc_html_e( 'ThreeKG Core needs Advanced Custom Fields Pro for editing.', 'threekg-core' ); ?></strong>
			<?php esc_html_e( 'Install and activate your licensed ACF Pro copy to edit ThreeKG blocks and global settings. The public site will continue using its canonical defaults.', 'threekg-core' ); ?></p>
	</div>
	<?php
}

