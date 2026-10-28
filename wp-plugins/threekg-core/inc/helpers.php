<?php
defined( 'ABSPATH' ) || exit;

function threekg_core_has_acf_pro(): bool {
	return function_exists( 'acf_add_local_field_group' ) && function_exists( 'acf_add_options_page' ) && function_exists( 'get_field' );
}

function threekg_core_field( string $name, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $name );
	return null === $value || false === $value || '' === $value ? $default : $value;
}

function threekg_core_option( string $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, 'option' );
		if ( null !== $value && false !== $value && '' !== $value ) {
			return $value;
		}
	}
	$legacy = get_theme_mod( 'threekg_' . $name, null );
	return null !== $legacy && '' !== $legacy ? $legacy : $default;
}

function threekg_core_media_url( $attachment, string $fallback = '' ): string {
	$id = is_array( $attachment ) ? (int) ( $attachment['ID'] ?? 0 ) : (int) $attachment;
	$url = $id ? wp_get_attachment_url( $id ) : '';
	return $url ? (string) $url : $fallback;
}

function threekg_core_image( $attachment, string $class, string $fallback = '', string $fallback_alt = '' ): string {
	$id = is_array( $attachment ) ? (int) ( $attachment['ID'] ?? 0 ) : (int) $attachment;
	if ( $id ) {
		return (string) wp_get_attachment_image( $id, 'full', false, array( 'class' => $class ) );
	}
	if ( ! $fallback ) {
		return '';
	}
	return sprintf( '<img class="%s" src="%s" alt="%s" />', esc_attr( $class ), esc_url( $fallback ), esc_attr( $fallback_alt ) );
}

function threekg_core_theme_asset( string $path ): string {
	return function_exists( 'get_theme_file_uri' ) ? (string) get_theme_file_uri( $path ) : '';
}

function threekg_core_instance_id( array $block, string $prefix ): string {
	$raw = isset( $block['id'] ) ? (string) $block['id'] : wp_unique_id( $prefix . '-' );
	return sanitize_html_class( $prefix . '-' . preg_replace( '/^block_/', '', $raw ) );
}

