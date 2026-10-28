<?php
defined( 'ABSPATH' ) || exit;

/** Build a consistently named ACF field. */
function threekg_core_acf_field( string $key, string $label, string $name, string $type = 'text', array $extra = array() ): array {
	return array_merge( array( 'key' => 'field_threekg_' . $key, 'label' => $label, 'name' => $name, 'type' => $type ), $extra );
}

/** Register version-controlled field groups; no wp-admin field creation is required. */
add_action(
	'acf/include_fields',
	function (): void {
		if ( ! threekg_core_has_acf_pro() ) {
			return;
		}
		$groups = array(
			'hero' => array(
				threekg_core_acf_field( 'hero_kicker', 'Kicker', 'kicker', 'text', array( 'default_value' => 'ThreeKG — independent design studio, est. 2021' ) ),
				threekg_core_acf_field( 'hero_line_1', 'Headline line 1', 'line_1', 'text', array( 'default_value' => 'We build quiet,' ) ),
				threekg_core_acf_field( 'hero_line_2', 'Headline line 2', 'line_2', 'text', array( 'default_value' => 'confident work' ) ),
				threekg_core_acf_field( 'hero_line_3', 'Headline line 3', 'line_3', 'text', array( 'default_value' => 'for brands that whisper' ) ),
				threekg_core_acf_field( 'hero_accent', 'Italic accent', 'accent', 'text', array( 'default_value' => 'loudly.' ) ),
				threekg_core_acf_field( 'hero_scroll', 'Scroll label', 'scroll_label', 'text', array( 'default_value' => 'scroll to explore ↓' ) ),
				threekg_core_acf_field( 'hero_location', 'Location label', 'location_label', 'text', array( 'instructions' => 'Leave empty to inherit ThreeKG Settings.' ) ),
				threekg_core_acf_field( 'hero_bg', 'Optional background image', 'background_image', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all' ) ),
			),
			'showreel' => array(
				threekg_core_acf_field( 'showreel_video', 'Video', 'video', 'file', array( 'return_format' => 'id', 'mime_types' => 'mp4,webm' ) ),
				threekg_core_acf_field( 'showreel_poster', 'Poster image', 'poster', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
				threekg_core_acf_field( 'showreel_year', 'Year', 'year', 'text', array( 'default_value' => gmdate( 'Y' ) ) ),
				threekg_core_acf_field( 'showreel_duration', 'Duration label', 'duration', 'text', array( 'default_value' => '0:10' ) ),
				threekg_core_acf_field( 'showreel_caption', 'Caption', 'caption', 'text', array( 'default_value' => 'reel placeholder' ) ),
				threekg_core_acf_field( 'showreel_play', 'Play label', 'play_label', 'text', array( 'default_value' => 'play reel' ) ),
			),
			'marquee' => array(
				threekg_core_acf_field( 'marquee_items', 'Marquee items', 'items', 'repeater', array( 'instructions' => 'Add at least two items. The duplicate animation track is generated automatically.', 'min' => 2, 'max' => 12, 'layout' => 'table', 'button_label' => 'Add item', 'sub_fields' => array( threekg_core_acf_field( 'marquee_item', 'Text', 'text', 'text', array( 'required' => 1 ) ) ) ) ),
			),
			'featured-work' => array(
				threekg_core_acf_field( 'work_heading', 'Heading', 'heading', 'text', array( 'default_value' => 'Featured work.' ) ),
				threekg_core_acf_field( 'work_eyebrow', 'Eyebrow', 'eyebrow', 'text', array( 'default_value' => '[03 — featured]' ) ),
				threekg_core_acf_field( 'work_years', 'Year range', 'year_range', 'text', array( 'default_value' => '2019 → ' . gmdate( 'Y' ) ) ),
				threekg_core_acf_field( 'work_mode', 'Source mode', 'source_mode', 'select', array( 'choices' => array( 'manual' => 'Manual cards', 'cms' => 'Projects CMS (available in Phase 4)' ), 'default_value' => 'manual', 'instructions' => 'Manual cards remain authoritative until Phase 4.' ) ),
				threekg_core_acf_field( 'work_cards', 'Manual cards', 'cards', 'repeater', array( 'min' => 1, 'max' => 12, 'button_label' => 'Add card', 'sub_fields' => array(
					threekg_core_acf_field( 'work_card_title', 'Title', 'title', 'text', array( 'required' => 1 ) ),
					threekg_core_acf_field( 'work_card_tag', 'Service/category', 'tag', 'text' ),
					threekg_core_acf_field( 'work_card_image', 'Image', 'image', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
					threekg_core_acf_field( 'work_card_alt', 'Image alt text', 'alt', 'text', array( 'instructions' => 'Required when the Media Library image has no alt text.' ) ),
					threekg_core_acf_field( 'work_card_url', 'Link', 'url', 'url' ),
					threekg_core_acf_field( 'work_card_layout', 'Layout', 'layout', 'select', array( 'choices' => array( 'full' => 'Full', 'right' => 'Right', 'left' => 'Left', 'side' => 'Side' ), 'default_value' => 'full' ) ),
				) ) ),
			),
			'clients' => array(
				threekg_core_acf_field( 'clients_heading', 'Heading', 'heading', 'text', array( 'default_value' => 'Clients we helped' ) ),
				threekg_core_acf_field( 'clients_year', 'Year', 'year', 'text', array( 'default_value' => (string) ( (int) gmdate( 'Y' ) - 1 ) ) ),
				threekg_core_acf_field( 'clients_items', 'Client tiles', 'clients', 'repeater', array( 'max' => 30, 'button_label' => 'Add client', 'sub_fields' => array(
					threekg_core_acf_field( 'client_name', 'Name', 'name', 'text', array( 'required' => 1 ) ),
					threekg_core_acf_field( 'client_sector', 'Sector', 'sector', 'text' ),
					threekg_core_acf_field( 'client_logo', 'Logo', 'logo', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
					threekg_core_acf_field( 'client_logo_alt', 'Logo alt text', 'logo_alt', 'text', array( 'instructions' => 'Required for a meaningful logo; use an empty logo field for text-only tiles.' ) ),
				) ) ),
			),
			'studio' => array(
				threekg_core_acf_field( 'studio_heading', 'Heading', 'heading', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'default_value' => "Enough range to win\nthe brief. Small enough\nto care about it." ) ),
				threekg_core_acf_field( 'studio_lead', 'Lead copy', 'lead', 'textarea', array( 'rows' => 5 ) ),
				threekg_core_acf_field( 'studio_body', 'Body copy', 'body', 'textarea', array( 'rows' => 5 ) ),
				threekg_core_acf_field( 'studio_image', 'Optional studio image', 'image', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
				threekg_core_acf_field( 'studio_stats', 'Statistics', 'stats', 'repeater', array( 'max' => 6, 'button_label' => 'Add statistic', 'sub_fields' => array(
					threekg_core_acf_field( 'studio_stat_value', 'Value', 'value', 'text', array( 'required' => 1 ) ),
					threekg_core_acf_field( 'studio_stat_label', 'Label', 'label', 'text', array( 'required' => 1 ) ),
				) ) ),
			),
			'services-story' => array(
				threekg_core_acf_field( 'services_heading', 'Heading', 'heading', 'text', array( 'default_value' => 'Six ways in.' ) ),
				threekg_core_acf_field( 'services_items', 'Services', 'services', 'repeater', array( 'instructions' => 'Exactly six published rows are required. Each symbol key must be unique.', 'min' => 6, 'max' => 6, 'button_label' => 'Add service', 'sub_fields' => array(
					threekg_core_acf_field( 'service_name', 'Name', 'name', 'text', array( 'required' => 1 ) ),
					threekg_core_acf_field( 'service_hint', 'Hint', 'hint', 'text' ),
					threekg_core_acf_field( 'service_symbol', 'Symbol key', 'symbol_key', 'select', array( 'required' => 1, 'choices' => array( 'brand' => 'Brand', 'design' => 'Design', 'studio' => 'Studio', 'campaigns' => 'Campaigns', 'production' => 'Production', 'xperience' => 'Experience' ) ) ),
				) ) ),
			),
			'contact-cta' => array(
				threekg_core_acf_field( 'contact_override', 'Use page-specific contact details', 'override_global', 'true_false', array( 'ui' => 1 ) ),
				threekg_core_acf_field( 'contact_label', 'Label', 'label', 'text', array( 'default_value' => 'get in touch' ) ),
				threekg_core_acf_field( 'contact_text', 'CTA text', 'cta_text', 'text', array( 'instructions' => 'Leave empty to inherit ThreeKG Settings.' ) ),
				threekg_core_acf_field( 'contact_link', 'CTA URL or mailto link', 'cta_link', 'url', array( 'instructions' => 'Leave empty to inherit the global contact email.' ) ),
				threekg_core_acf_field( 'contact_columns', 'Optional local footer columns', 'columns', 'repeater', array( 'max' => 3, 'button_label' => 'Add column', 'sub_fields' => array(
					threekg_core_acf_field( 'contact_col_heading', 'Heading', 'heading', 'text' ),
					threekg_core_acf_field( 'contact_col_text', 'Text', 'text', 'textarea', array( 'rows' => 3, 'new_lines' => 'br' ) ),
				) ) ),
			),
		);

		foreach ( $groups as $slug => $fields ) {
			acf_add_local_field_group( array( 'key' => 'group_threekg_' . str_replace( '-', '_', $slug ), 'title' => 'ThreeKG — ' . ucwords( str_replace( '-', ' ', $slug ) ), 'fields' => $fields, 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'threekg/' . $slug ) ) ), 'position' => 'normal', 'style' => 'default', 'active' => true ) );
		}

		acf_add_local_field_group( array(
			'key' => 'group_threekg_settings', 'title' => 'ThreeKG global options',
			'fields' => array(
				threekg_core_acf_field( 'opt_email', 'Contact email', 'email', 'email', array( 'default_value' => 'hello@threekg.studio' ) ),
				threekg_core_acf_field( 'opt_cta', 'Footer contact text', 'cta_text', 'text', array( 'default_value' => 'Start a project' ) ),
				threekg_core_acf_field( 'opt_availability', 'Availability', 'availability', 'text', array( 'default_value' => 'Available for projects' ) ),
				threekg_core_acf_field( 'opt_descriptor', 'Descriptor', 'descriptor', 'text', array( 'default_value' => 'minimalism · space · motion' ) ),
				threekg_core_acf_field( 'opt_location', 'Location', 'location', 'text', array( 'default_value' => 'working worldwide' ) ),
				threekg_core_acf_field( 'opt_instagram', 'Instagram URL', 'instagram', 'url' ),
				threekg_core_acf_field( 'opt_linkedin', 'LinkedIn URL', 'linkedin', 'url' ),
				threekg_core_acf_field( 'opt_behance', 'Behance URL', 'behance', 'url' ),
				threekg_core_acf_field( 'opt_loader_enabled', 'Enable front-page loader', 'loader_enabled', 'true_false', array( 'default_value' => 1, 'ui' => 1 ) ),
				threekg_core_acf_field( 'opt_loader_video', 'Loader video', 'loader_video', 'file', array( 'return_format' => 'id', 'mime_types' => 'mp4,webm' ) ),
			),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'threekg-settings' ) ) ), 'active' => true,
		) );
	}
);

add_filter( 'acf/validate_value/key=field_threekg_services_items', 'threekg_core_validate_services', 10, 4 );
function threekg_core_validate_services( $valid, $value, $field, $input ) {
	if ( true !== $valid || ! is_array( $value ) || 6 !== count( $value ) ) {
		return __( 'Services Story requires exactly six services.', 'threekg-core' );
	}
	$keys = array();
	foreach ( $value as $row ) {
		$key = (string) ( $row['field_threekg_service_symbol'] ?? '' );
		if ( '' === $key || in_array( $key, $keys, true ) ) {
			return __( 'Each service needs a unique symbol key.', 'threekg-core' );
		}
		$keys[] = $key;
	}
	return $valid;
}

