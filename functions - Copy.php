<?php
/**
 * This file adds functions to the Frost theme for WordPress.
 *
 * @package Frost
 * @author  WP Engine
 * @license GNU General Public License v2 or later
 * @link    https://frostwp.com/
 */

if ( ! function_exists( 'frost_setup' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function frost_setup() {

		// Make theme available for translation.
		load_theme_textdomain( 'frost', get_template_directory() . '/languages' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles and fonts.
		add_editor_style(
			array(
				'./style.css',
				frost_fonts_url(),
			)
		);
	
		// Disable loading core block inline styles.
		add_filter( 'should_load_separate_core_block_assets', '__return_false' );

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

	}
}
add_action( 'after_setup_theme', 'frost_setup' );

// Enqueue style sheet.
add_action( 'wp_enqueue_scripts', 'frost_enqueue_style_sheet' );
function frost_enqueue_style_sheet() {

	wp_enqueue_style( 'frost', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );

}

//add class for body
add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
	$word = "social";
	$mystring = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
 
	// Check if url contains the word 
	if(strpos($mystring, $word) !== false){
		if ( is_page_template( 'sidebar-memberpressproduct' ) ) {
			$classes[] = 'social_campaign';
		}
		if ( is_page_template( 'no-title' ) ) {
			$classes[] = 'social_campaign';
		}
	}
    return $classes;
}


// Enqueue fonts.
add_action( 'wp_enqueue_scripts', 'frost_enqueue_fonts' );
function frost_enqueue_fonts() {

	wp_enqueue_style( 'frost-fonts', frost_fonts_url(), array(), null );

}

// Define fonts.
function frost_fonts_url() {

	// Allow child themes to disable to the default Frost fonts.
	$dequeue_fonts = apply_filters( 'frost_dequeue_fonts', false );

	if ( $dequeue_fonts ) {
		return '';
	}

	$fonts = array(
		'family=Cardo:ital,wght@0,400;0,700;1,400',
	);

	// Make a single request for all Google Fonts.
	return esc_url_raw( 'https://fonts.googleapis.com/css2?' . implode( '&', array_unique( $fonts ) ) . '&display=swap' );

}

// Include block styles.
require get_template_directory() . '/inc/block-styles.php';

// Include block patterns.
require get_template_directory() . '/inc/block-patterns.php';

// Disable WooCommer product zoom.
remove_theme_support( 'wc-product-gallery-zoom' );
add_filter( 'woocommerce_single_product_zoom_enabled', '__return_false' );

// Disable unique WooCommerce SKU
add_filter( 'wc_product_has_unique_sku', '__return_false' ); 

// Remove Coupon Notice from Checkout
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 ); 

// Disable Login Information Emails
remove_action( 'register_new_user', 'wp_send_new_user_notifications' );

