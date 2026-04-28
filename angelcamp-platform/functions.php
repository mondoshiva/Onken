<?php
/**
 * AngelCamp Platform Theme functions.
 *
 * @package AngelCampPlatform
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup theme supports.
 */
function angelcamp_platform_setup() {
	load_theme_textdomain( 'angelcamp-platform', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );

	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'angelcamp_platform_setup' );

/**
 * Enqueue theme assets.
 */
function angelcamp_platform_enqueue_assets() {
	wp_enqueue_style(
		'angelcamp-platform-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'angelcamp_platform_enqueue_assets' );

/**
 * Register custom block pattern category.
 */
function angelcamp_platform_register_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'angelcamp-platform',
			array(
				'label' => esc_html__( 'AngelCamp Platform', 'angelcamp-platform' ),
			)
		);
	}
}
add_action( 'init', 'angelcamp_platform_register_pattern_category' );
