<?php
if (!defined('ABSPATH')) {
    exit;
}

function threekg_revamp_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('responsive-embeds');
    register_nav_menus(array('primary' => __('Primary navigation', 'threekg-revamp')));
}
add_action('after_setup_theme', 'threekg_revamp_setup');

function threekg_revamp_assets() {
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style(
        'threekg-fonts',
        'https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap',
        array(),
        null
    );
    wp_enqueue_style('threekg-revamp', get_template_directory_uri() . '/assets/style.css', array('threekg-fonts'), $version);
    wp_enqueue_script('threekg-revamp', get_template_directory_uri() . '/assets/script.js', array(), $version, true);
}
add_action('wp_enqueue_scripts', 'threekg_revamp_assets');

function threekg_revamp_body_classes($classes) {
    $classes[] = 'is-loading';
    return $classes;
}
add_filter('body_class', 'threekg_revamp_body_classes');

function threekg_revamp_customize($customizer) {
    $customizer->add_section('threekg_contact', array(
        'title' => __('ThreeKG contact details', 'threekg-revamp'),
        'priority' => 30,
    ));
    $customizer->add_setting('threekg_contact_email', array(
        'default' => 'hello@threekg.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    $customizer->add_control('threekg_contact_email', array(
        'label' => __('Contact email', 'threekg-revamp'),
        'section' => 'threekg_contact',
        'type' => 'email',
    ));
}
add_action('customize_register', 'threekg_revamp_customize');

function threekg_project_meta($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function threekg_fallback_image($index) {
    $files = array('halcyon.jpg', 'meridian.jpg', 'northlight.jpg', 'onto.jpg', 'ferro.jpg', 'vellum.jpg');
    return get_template_directory_uri() . '/assets/work/' . $files[$index % count($files)];
}

function threekg_archive_columns($index) {
    $classes = array('work-block--full', 'work-block--right', 'work-block--left', 'work-block--side');
    return $classes[$index % count($classes)];
}

