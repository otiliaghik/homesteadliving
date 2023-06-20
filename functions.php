<?php
// Enqueue parent and child theme stylesheets
function frost_child_enqueue_styles() {
    $parent_style = 'frost-style'; // Parent theme stylesheet handle

    // Enqueue jQuery
    wp_enqueue_script( 'jquery' );

    // Enqueue parent and child theme stylesheets
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'frost-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    // Enqueue child theme custom CSS
    wp_enqueue_style( 'frost-child-custom',
        get_stylesheet_directory_uri() . '/custom.css',
        array( $parent_style, 'frost-child-style' ),
        filemtime( get_stylesheet_directory() . '/custom.css' ) // Use file modification time as version
    );

    // Enqueue child theme custom JavaScript
        wp_enqueue_script( 'frost-child-custom-js',
        get_stylesheet_directory_uri() . '/custom.js',
        array( 'jquery' ), // Dependencies (jQuery)
        filemtime( get_stylesheet_directory() . '/custom.js' ), // Use file modification time as version
        true // Enqueue the script in the footer
        );

}
add_action( 'wp_enqueue_scripts', 'frost_child_enqueue_styles' );

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

