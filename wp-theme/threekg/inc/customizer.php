<?php
/**
 * Customizer global settings (contact, header, socials).
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel(
			'threekg',
			array(
				'title'       => __( 'ThreeKG site', 'threekg' ),
				'description' => __( 'Contact details, header copy and social links for the global shell.', 'threekg' ),
			)
		);

		$settings = array(
			'threekg_email' => array(
				'default'           => 'hello@threekg.studio',
				'label'             => __( 'Contact e-mail', 'threekg' ),
				'type'              => 'email',
				'section'           => 'threekg_contact',
				'section_label'     => __( 'Contact', 'threekg' ),
			),
			'threekg_cta_text' => array(
				'default'       => 'Start a project',
				'label'         => __( 'Footer CTA text', 'threekg' ),
				'type'          => 'text',
				'section'       => 'threekg_contact',
				'section_label' => null,
			),
			'threekg_availability' => array(
				'default'       => 'Available for projects',
				'label'         => __( 'Availability badge', 'threekg' ),
				'type'          => 'text',
				'section'       => 'threekg_header',
				'section_label' => __( 'Header', 'threekg' ),
			),
			'threekg_descriptor' => array(
				'default'       => 'minimalism · space · motion',
				'label'         => __( 'Header descriptor', 'threekg' ),
				'type'          => 'text',
				'section'       => 'threekg_header',
				'section_label' => null,
			),
			'threekg_location' => array(
				'default'       => 'working worldwide',
				'label'         => __( 'Location label', 'threekg' ),
				'type'          => 'text',
				'section'       => 'threekg_header',
				'section_label' => null,
			),
			'threekg_instagram' => array(
				'default'       => '',
				'label'         => __( 'Instagram URL', 'threekg' ),
				'type'          => 'url',
				'section'       => 'threekg_social',
				'section_label' => __( 'Social', 'threekg' ),
			),
			'threekg_linkedin' => array(
				'default'       => '',
				'label'         => __( 'LinkedIn URL', 'threekg' ),
				'type'          => 'url',
				'section'       => 'threekg_social',
				'section_label' => null,
			),
			'threekg_behance' => array(
				'default'       => '',
				'label'         => __( 'Behance URL', 'threekg' ),
				'type'          => 'url',
				'section'       => 'threekg_social',
				'section_label' => null,
			),
		);

		$sections_done = array();

		foreach ( $settings as $id => $config ) {
			if ( $config['section_label'] && ! in_array( $config['section'], $sections_done, true ) ) {
				$wp_customize->add_section(
					$config['section'],
					array(
						'title' => $config['section_label'],
						'panel' => 'threekg',
					)
				);
				$sections_done[] = $config['section'];
			}

			$wp_customize->add_setting(
				$id,
				array(
					'default'           => $config['default'],
					'type'              => 'theme_mod',
					'sanitize_callback' => 'threekg_sanitize_option',
					'validate'          => array(),
				)
			);

			$wp_customize->add_control(
				$id,
				array(
					'label'   => $config['label'],
					'type'    => $config['type'],
					'section' => $config['section'],
				)
			);
		}
	}
);

/**
 * Shared sanitize callback honouring the declared setting type.
 */
function threekg_sanitize_option( ?string $value, WP_Customize_Setting $setting ): ?string {
	switch ( $setting->id ) {
		case 'threekg_email':
			return (string) sanitize_email( $value ?? '' );
		case 'threekg_instagram':
		case 'threekg_linkedin':
		case 'threekg_behance':
			return (string) esc_url_raw( $value ?? '' );
		default:
			return $value ?? '';
	}
}
