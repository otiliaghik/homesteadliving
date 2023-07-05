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

// Add the shortcode to functions.php or your custom plugin file
add_shortcode('like_button', 'like_button_shortcode');

// Define the shortcode callback function
function like_button_shortcode($atts) {
    // Get post ID
    $post_id = get_the_ID();

    // Check if the user has already liked the post
    $user_id = get_current_user_id();
    $liked = false;
    if ($user_id) {
        $liked = get_user_meta($user_id, 'liked_posts', true);
        if ($liked && in_array($post_id, $liked)) {
            $liked = true;
        }
    }

    // Output the like button
    ob_start();
    ?>
    <form method="post" class="like-form">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <input type="hidden" name="action" value="like_post">
        <?php wp_nonce_field('like_post', 'like_post_nonce'); ?>
        <button type="submit" class="like-button <?php echo $liked ? 'liked' : ''; ?>">
            <?php echo $liked ? '<svg width="21" height="17" viewBox="0 0 21 17" xmlns="http://www.w3.org/2000/svg">
<path d="M17.7422 1.46484C19.957 3.36328 20.0625 6.73828 18.0938 8.77734L11.2734 15.8086C11.0625 16.0195 10.7812 16.125 10.4648 16.125C10.1836 16.125 9.90234 16.0195 9.69141 15.8086L2.87109 8.77734C0.902344 6.73828 1.00781 3.36328 3.22266 1.46484C4.06641 0.726562 5.12109 0.375 6.17578 0.375C7.47656 0.375 8.8125 0.9375 9.79688 1.92188L10.5 2.66016L11.168 1.92188C12.1523 0.9375 13.4883 0.375 14.7891 0.375C15.8438 0.375 16.8984 0.726562 17.7422 1.46484ZM16.8633 7.61719C18.0234 6.42188 18.2695 4.13672 16.6523 2.76562C15.2461 1.53516 13.3125 2.16797 12.3984 3.11719L10.5 5.08594L8.56641 3.11719C7.65234 2.13281 5.71875 1.53516 4.3125 2.76562C2.69531 4.13672 2.97656 6.42188 4.10156 7.61719L10.5 14.1914L16.8633 7.61719Z" fill="black"/></svg>' : '<svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.7422 1.46484C19.957 3.36328 20.0625 6.73828 18.0938 8.77734L11.2734 15.8086C11.0625 16.0195 10.7812 16.125 10.4648 16.125C10.1836 16.125 9.90234 16.0195 9.69141 15.8086L2.87109 8.77734C0.902344 6.73828 1.00781 3.36328 3.22266 1.46484C4.06641 0.726562 5.12109 0.375 6.17578 0.375C7.47656 0.375 8.8125 0.9375 9.79688 1.92188L10.5 2.66016L11.168 1.92188C12.1523 0.9375 13.4883 0.375 14.7891 0.375C15.8438 0.375 16.8984 0.726562 17.7422 1.46484ZM16.8633 7.61719C18.0234 6.42188 18.2695 4.13672 16.6523 2.76562C15.2461 1.53516 13.3125 2.16797 12.3984 3.11719L10.5 5.08594L8.56641 3.11719C7.65234 2.13281 5.71875 1.53516 4.3125 2.76562C2.69531 4.13672 2.97656 6.42188 4.10156 7.61719L10.5 14.1914L16.8633 7.61719Z" fill="black"/>
</svg>'; ?>
        </button>
    </form>
    <?php
    return ob_get_clean();
}

// Handle the form submission
add_action('init', 'handle_like_post');

function handle_like_post() {
    if (isset($_POST['action']) && $_POST['action'] === 'like_post' && isset($_POST['like_post_nonce'])) {
        // Verify the nonce
        if (!wp_verify_nonce($_POST['like_post_nonce'], 'like_post')) {
            die('Invalid nonce');
        }

        // Get the post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        // Get the current user ID
        $user_id = get_current_user_id();

        // Update the liked posts meta for the user
        $liked_posts = get_user_meta($user_id, 'liked_posts', true);
        if (!$liked_posts) {
            $liked_posts = array();
        }

        // Check if the post is already liked and update accordingly
        if (in_array($post_id, $liked_posts)) {
            $liked_posts = array_diff($liked_posts, array($post_id));
            update_user_meta($user_id, 'liked_posts', $liked_posts);
        } else {
            $liked_posts[] = $post_id;
            update_user_meta($user_id, 'liked_posts', $liked_posts);
        }

        // Redirect back to the post
        wp_redirect(get_permalink($post_id));
        exit;
    }
}

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
  