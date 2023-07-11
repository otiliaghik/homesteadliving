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

function enqueue_clipboard_js() {
    wp_enqueue_script('clipboard-js', 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js', array(), '2.0.8', true);
}
add_action('wp_enqueue_scripts', 'enqueue_clipboard_js');


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
                        'author',
                        'custom-fields',
                        'page-attributes',
						'featured_image',
        				'set_featured_image',
                        'author'

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

add_filter('wp_dropdown_users', 'include_authors_contributors_in_dropdown');

function include_authors_contributors_in_dropdown($output) {
    $args = array(
        'role__in' => array('author', 'contributor'),
        'orderby' => 'display_name',
        'order' => 'ASC',
        'echo' => false
    );

    $users = get_users($args);

    $output = wp_dropdown_users(array(
        'name' => 'post_author',
        'exclude' => '1', // Exclude the administrator
        'selected' => get_post_field('post_author'),
        'include_selected' => true,
        'show_option_none' => __('— Select Author —'),
        'option_none_value' => '0',
        'echo' => false
    ));

    // Append the contributors to the dropdown
    $output = preg_replace('/<\/select>/', '', $output);
    $output .= '<optgroup label="' . esc_attr__('Contributors') . '">';
    foreach ($users as $user) {
        $output .= '<option value="' . esc_attr($user->ID) . '"' . selected($user->ID, get_post_field('post_author'), false) . '>' . esc_html($user->display_name) . '</option>';
    }
    $output .= '</optgroup></select>';

    return $output;
}


function custom_excerpt_more($more) {
    return ''; // Remove the ellipsis ("...") from the excerpt.
}
add_filter('excerpt_more', 'custom_excerpt_more');

function custom_excerpt_filter($excerpt) {
    $first_sentence = preg_match('/^.*?[a-z0-9][.!?]/i', $excerpt, $matches);
    if ($first_sentence) {
        $excerpt = $matches[0];
    }
    return $excerpt;
}
add_filter('get_the_excerpt', 'custom_excerpt_filter');


function custom_apb_excerpt_filter($excerpt) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Disable libxml errors
    $dom->loadHTML(mb_convert_encoding($excerpt, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);
    $first_sentence = $xpath->evaluate('string(//text()[normalize-space()][1])');
    $first_sentence = preg_replace('/\s+/', ' ', $first_sentence); // Trim excess whitespace

    return $first_sentence;
}

add_filter('advanced_post_block_excerpt', 'custom_apb_excerpt_filter');

function issue_post_type_archive_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'issue'
    ), $atts);

    $post_type_archive_link = get_post_type_archive_link($atts['post_type']);

    if (!$post_type_archive_link) {
        return '';
    }

    $post_type_object = get_post_type_object($atts['post_type']);
    $archive_label = $post_type_object->labels->archives;

    return sprintf(
        '<a href="%s">%s</a>',
        esc_url($post_type_archive_link),
        esc_html($archive_label)
    );
}
add_shortcode('issue_post_type_archive', 'issue_post_type_archive_shortcode');

function get_issue_custom_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => -1,
        'post_type'      => 'issue'
    ), $atts);

    $query = new WP_Query(array(
        'post_type'      => $atts['post_type'],
        'posts_per_page' => $atts['posts_per_page'],
        'post_parent'    => 0 // Only fetch parent pages
    ));

    $output = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<p>' . get_the_title() . '</p>';
            // You can customize the output as per your requirement
        }
        wp_reset_postdata();
    }

    return $output;
}
add_shortcode('issue_custom_posts', 'get_issue_custom_posts_shortcode');

function add_read_more_link($content) {
    global $post;
  
    // Check if the post content is the "Biographical Info" textarea
    if (is_author() && isset($_GET['author']) && get_the_ID() == $_GET['author']) {
      $content = get_the_author_meta('description');
      if (strpos($content, 'wp-block-post-author__bio') !== false) {
        $excerpt = substr($content, 0, 150); // Change 150 to your desired character limit
        $trimmedContent = substr($excerpt, 0, strrpos($excerpt, ' '));
        $content = $trimmedContent . '...' . '<a href="' . get_author_posts_url(get_the_ID()) . '" class="read-more">Read More</a>';
      }
    }
  
    return $content;
  }
  add_filter('the_content', 'add_read_more_link');
  

  function author_users_shortcode() {
    $users = get_users( array( 'role' => 'author' ) );

    $output = '<ul>';

    foreach ( $users as $user ) {
        $user_profile_url = get_author_posts_url( $user->ID );
        $user_profile_picture = get_avatar( $user->ID, 32 );

        $output .= '<li><a href="' . $user_profile_url . '">' . $user_profile_picture . ' ' . $user->display_name . '</a></li>';
    }

    $output .= '</ul>';

    return $output;
}
add_shortcode( 'author_users', 'author_users_shortcode' );


function contributor_users_shortcode() {
    $users = get_users( array( 'role' => 'contributor' ) );

    $output = '<ul>';

    foreach ( $users as $user ) {
        $user_profile_url = get_author_posts_url( $user->ID );
        $user_contact_info = '';

        // Check if contact information is available
        if ( ! empty( $user->linkedin ) ) {
            $user_contact_info .= '<a href="' . $user->linkedin . '">Linkedin</a> ';
        }
        if ( ! empty( $user->facebook ) ) {
            $user_contact_info .= '<a href="' . $user->facebook . '">Facebook</a> ';
        }
        if ( ! empty( $user->instagram ) ) {
            $user_contact_info .= '<a href="' . $user->instagram . '">Instagram</a> ';
        }
        if ( ! empty( $user->twitter ) ) {
            $user_contact_info .= '<a href="' . $user->twitter . '">Twitter</a> ';
        }

        $output .= '<li><a href="' . $user_profile_url . '">' . $user->display_name . '</a>' . ($user_contact_info ? ' - ' . $user_contact_info : '') . '</li>';
    }

    $output .= '</ul>';

    return $output;
}
add_shortcode( 'contributor_users', 'contributor_users_shortcode' );


function author_image_shortcode($atts) {
    // Get the author ID
    $author_id = get_queried_object_id();

    // Get the author avatar HTML
    $author_avatar = get_avatar($author_id, array('size' => 'full'));

    // Return the HTML for displaying the author image
    ob_start();
    ?>
    <div class="wp-block-post-author__avatar">
        <?php echo $author_avatar; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('author_image', 'author_image_shortcode');


function author_name_shortcode($atts) {
    // Get the author ID
    $author_id = get_queried_object_id();
    
    // Get the author's display name
    $author_name = get_the_author_meta('display_name', $author_id);
    
    // Return the author's name
    return $author_name;
}
add_shortcode('author_name', 'author_name_shortcode');

function author_bio_shortcode($atts) {
    // Get the author ID
    $author_id = get_queried_object_id();
    
    // Get the author's biography
    $author_bio = get_the_author_meta('description', $author_id);
    
    // Return the author's biography
    return $author_bio;
}
add_shortcode('author_bio', 'author_bio_shortcode');


function author_socials_shortcode() {
    $author_id = get_queried_object_id();
    $author_profile_url = get_author_posts_url($author_id);
    $author_contact_info = '';

    // Check if contact information is available for the author
    if (!empty(get_the_author_meta('linkedin', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('linkedin', $author_id)) . '">Linkedin</a> ';
    }
    if (!empty(get_the_author_meta('facebook', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('facebook', $author_id)) . '">Facebook</a> ';
    }
    if (!empty(get_the_author_meta('instagram', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('instagram', $author_id)) . '">Instagram</a> ';
    }
    if (!empty(get_the_author_meta('twitter', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('twitter', $author_id)) . '">Twitter</a> ';
    }

    $output = '<ul>';
    $output .= '<li><a href="' . esc_url($author_profile_url) . '">' . get_the_author_meta('display_name', $author_id) . '</a>' . ($author_contact_info ? ' - ' . $author_contact_info : '') . '</li>';
    $output .= '</ul>';

    return $output;
}
add_shortcode('author_socials', 'author_socials_shortcode');


function author_more_description_shortcode() {
    $author_id = get_queried_object_id();
    $more_description = get_field('more_description', 'user_' . $author_id);

    return $more_description;
}
add_shortcode('author_more_description', 'author_more_description_shortcode');


function books_by_author_shortcode() {
    $author_id = get_queried_object_id();
    $books = get_field('books_by_author', 'user_' . $author_id);

    $output = '';

    if ($books) {
        $output .='<h2>Author books</h2>';
        foreach ($books as $book) {
            $product_id = $book->ID;
            $product_title = get_the_title($product_id);
            $product_image = get_the_post_thumbnail($product_id, 'thumbnail');
            $product_link = get_permalink($product_id);
            $product_button = '<a href="' . $product_link . '" class="button">Buy Now</a>';

            $output .= '<div class="product">';
            $output .= $product_image;
            $output .= '<h3>' . $product_title . '</h3>';
            $output .= $product_button;
            $output .= '</div>';
        }
    } 

    return $output;
}
add_shortcode('books_by_author', 'books_by_author_shortcode');

