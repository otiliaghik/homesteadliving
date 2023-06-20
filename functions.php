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

function frost_fonts_url() {
    // Allow child themes to disable the default Frost fonts.
    $dequeue_fonts = apply_filters( 'frost_dequeue_fonts', false );

    if ( $dequeue_fonts ) {
        return '';
    }

    $fonts = array(
        'family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap',
    );

    // Make a single request for all Google Fonts.
    return esc_url_raw( 'https://fonts.googleapis.com/css2?' . implode( '&', array_unique( $fonts ) ) );
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

add_action( 'init', 'pv_custom_post_custom_issue' );
// The custom function to register a custom article post type
function pv_custom_post_custom_issue() {
    // Set the labels. This variable is used in the $args array
    $labels = array(
        'name'               => __( 'Magazines' ),
        'singular_name'      => __( 'Issue' ),
		'menu_name'             => __( 'Magazines' ),
        'name_admin_bar'        => __( 'Issue' ),
        'archives'              => __( 'Magazine Archives' ),
        'add_new'            => __( 'Add Issue & Article' ),
        'add_new_item'       => __( 'Add Issue & Article' ),
        'edit_item'          => __( 'Edit Issue' ),
        'new_item'           => __( 'New Issue' ),
        'all_items'          => __( 'All Issues' ),
        'view_item'          => __( 'View Issue' ),
        'search_items'       => __( 'Search Issue' )
    );
// The arguments for our post type, to be entered as parameter 2 of register_post_type()
    $args = array(
        'labels'            => $labels,
        'description'       => 'Holds our custom issue post specific data',
        'public'            => true,
        'menu_position'     => 5,
		'menu_icon'   => 'dashicons-book',
        'has_archive'       => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'query_var'         => true,
		'hierarchical' => true,
		// 'has_archive'           => 'magazines',
		'has_archive' => true,
        'capability_type'    => 'page',
		'exclude_from_search'   => false,
        'supports' => array(
                        'title',
                        'editor',
                        'excerpt',
                        'thumbnail',
                        'custom-fields',
                        'page-attributes',
						'featured_image',
        				'set_featured_image'

		),
		'taxonomies'        => array( 'issue_category' ) // Change this to a custom name for your category type
    );
    register_post_type( 'issue', $args );
    // Register custom taxonomy for issue post type
    register_taxonomy(
        'issue_category', // Change this to a custom name for your category type
        'issue', // Post type to associate with the taxonomy
        array(
            'label'        => __( 'Issue Categories' ),
            'rewrite'      => array( 'slug' => 'issue-category' ),
            'hierarchical' => true
        )
    );
}