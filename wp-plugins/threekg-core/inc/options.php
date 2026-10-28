<?php
defined( 'ABSPATH' ) || exit;

add_action(
	'acf/init',
	function (): void {
		if ( ! threekg_core_has_acf_pro() ) {
			return;
		}
		acf_add_options_page(
			array(
				'page_title' => __( 'ThreeKG Settings', 'threekg-core' ),
				'menu_title' => __( 'ThreeKG Settings', 'threekg-core' ),
				'menu_slug'  => 'threekg-settings',
				'capability' => 'manage_options',
				'redirect'   => false,
			)
		);
	}
);

