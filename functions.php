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

    $output = '<ul class="authors-list">';

    foreach ( $users as $user ) {
        $user_profile_url = get_author_posts_url( $user->ID );
        $user_profile_picture = get_avatar( $user->ID, 32 );

        $output .= '<li><a href="' . $user_profile_url . '">' . $user_profile_picture . '<div class="author-box"><h5>Author</h5> <span class="author-name">' . $user->display_name . '</span></div></a></li>';
    }

    $output .= '</ul>';

    return $output;
}
add_shortcode( 'author_users', 'author_users_shortcode' );


function contributor_users_shortcode() {
    $users = get_users( array( 'role' => 'contributor' ) );

    $output = '<ul class="contributors">';

    foreach ( $users as $user ) {
        $user_profile_url = get_author_posts_url( $user->ID );
        $user_contact_info = '';

        // Check if contact information is available
               if ( ! empty( $user->facebook ) ) {
            $user_contact_info .= '<a class="fb" href="' . $user->facebook . '"><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19.6875 10.5C19.6875 15.3438 16.1328 19.3672 11.4844 20.0703V13.3125H13.75L14.1797 10.5H11.4844V8.70312C11.4844 7.92188 11.875 7.17969 13.0859 7.17969H14.2969V4.79688C14.2969 4.79688 13.2031 4.60156 12.1094 4.60156C9.92188 4.60156 8.47656 5.96875 8.47656 8.39062V10.5H6.01562V13.3125H8.47656V20.0703C3.82812 19.3672 0.3125 15.3438 0.3125 10.5C0.3125 5.14844 4.64844 0.8125 10 0.8125C15.3516 0.8125 19.6875 5.14844 19.6875 10.5Z" fill="#0F3548"/></svg></a> ';
        }
        if ( ! empty( $user->instagram ) ) {
            $user_contact_info .= '<a class="insta" href="' . $user->instagram . '"><svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.75 5.00781C12.2109 5.00781 14.2422 7.03906 14.2422 9.5C14.2422 12 12.2109 13.9922 9.75 13.9922C7.25 13.9922 5.25781 12 5.25781 9.5C5.25781 7.03906 7.25 5.00781 9.75 5.00781ZM9.75 12.4297C11.3516 12.4297 12.6406 11.1406 12.6406 9.5C12.6406 7.89844 11.3516 6.60938 9.75 6.60938C8.10938 6.60938 6.82031 7.89844 6.82031 9.5C6.82031 11.1406 8.14844 12.4297 9.75 12.4297ZM15.4531 4.85156C15.4531 4.26562 14.9844 3.79688 14.3984 3.79688C13.8125 3.79688 13.3438 4.26562 13.3438 4.85156C13.3438 5.4375 13.8125 5.90625 14.3984 5.90625C14.9844 5.90625 15.4531 5.4375 15.4531 4.85156ZM18.4219 5.90625C18.5 7.35156 18.5 11.6875 18.4219 13.1328C18.3438 14.5391 18.0312 15.75 17.0156 16.8047C16 17.8203 14.75 18.1328 13.3438 18.2109C11.8984 18.2891 7.5625 18.2891 6.11719 18.2109C4.71094 18.1328 3.5 17.8203 2.44531 16.8047C1.42969 15.75 1.11719 14.5391 1.03906 13.1328C0.960938 11.6875 0.960938 7.35156 1.03906 5.90625C1.11719 4.5 1.42969 3.25 2.44531 2.23438C3.5 1.21875 4.71094 0.90625 6.11719 0.828125C7.5625 0.75 11.8984 0.75 13.3438 0.828125C14.75 0.90625 16 1.21875 17.0156 2.23438C18.0312 3.25 18.3438 4.5 18.4219 5.90625ZM16.5469 14.6562C17.0156 13.5234 16.8984 10.7891 16.8984 9.5C16.8984 8.25 17.0156 5.51562 16.5469 4.34375C16.2344 3.60156 15.6484 2.97656 14.9062 2.70312C13.7344 2.23438 11 2.35156 9.75 2.35156C8.46094 2.35156 5.72656 2.23438 4.59375 2.70312C3.8125 3.01562 3.22656 3.60156 2.91406 4.34375C2.44531 5.51562 2.5625 8.25 2.5625 9.5C2.5625 10.7891 2.44531 13.5234 2.91406 14.6562C3.22656 15.4375 3.8125 16.0234 4.59375 16.3359C5.72656 16.8047 8.46094 16.6875 9.75 16.6875C11 16.6875 13.7344 16.8047 14.9062 16.3359C15.6484 16.0234 16.2734 15.4375 16.5469 14.6562Z" fill="#0F3548"/></svg></a> ';
        }
        if ( ! empty( $user->twitter ) ) {
            $user_contact_info .= '<a class="tweet" href="' . $user->twitter . '"><svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.9297 4.4375C17.9297 4.63281 17.9297 4.78906 17.9297 4.98438C17.9297 10.4141 13.8281 16.625 6.28906 16.625C3.94531 16.625 1.79688 15.9609 0 14.7891C0.3125 14.8281 0.625 14.8672 0.976562 14.8672C2.89062 14.8672 4.64844 14.2031 6.05469 13.1094C4.25781 13.0703 2.73438 11.8984 2.22656 10.2578C2.5 10.2969 2.73438 10.3359 3.00781 10.3359C3.35938 10.3359 3.75 10.2578 4.0625 10.1797C2.1875 9.78906 0.78125 8.14844 0.78125 6.15625V6.11719C1.32812 6.42969 1.99219 6.58594 2.65625 6.625C1.52344 5.88281 0.820312 4.63281 0.820312 3.22656C0.820312 2.44531 1.01562 1.74219 1.36719 1.15625C3.39844 3.61719 6.44531 5.25781 9.84375 5.45312C9.76562 5.14062 9.72656 4.82812 9.72656 4.51562C9.72656 2.25 11.5625 0.414062 13.8281 0.414062C15 0.414062 16.0547 0.882812 16.8359 1.70312C17.7344 1.50781 18.6328 1.15625 19.4141 0.6875C19.1016 1.66406 18.4766 2.44531 17.6172 2.95312C18.4375 2.875 19.2578 2.64062 19.9609 2.32812C19.4141 3.14844 18.7109 3.85156 17.9297 4.4375Z" fill="#0F3548"/></svg></a> ';
        }
        if ( ! empty( $user->linkedin ) ) {
            $user_contact_info .= '<a class="linkd" href="' . $user->linkedin . '"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.875 0.625C18.6055 0.625 19.25 1.26953 19.25 2.04297V18.5C19.25 19.2734 18.6055 19.875 17.875 19.875H1.33203C0.601562 19.875 0 19.2734 0 18.5V2.04297C0 1.26953 0.601562 0.625 1.33203 0.625H17.875ZM5.80078 17.125V7.97266H2.96484V17.125H5.80078ZM4.38281 6.68359C5.28516 6.68359 6.01562 5.95312 6.01562 5.05078C6.01562 4.14844 5.28516 3.375 4.38281 3.375C3.4375 3.375 2.70703 4.14844 2.70703 5.05078C2.70703 5.95312 3.4375 6.68359 4.38281 6.68359ZM16.5 17.125V12.0977C16.5 9.64844 15.9414 7.71484 13.0625 7.71484C11.6875 7.71484 10.7422 8.48828 10.3555 9.21875H10.3125V7.97266H7.60547V17.125H10.4414V12.6133C10.4414 11.4102 10.6562 10.25 12.1602 10.25C13.6211 10.25 13.6211 11.625 13.6211 12.6562V17.125H16.5Z" fill="#0F3548"/></svg></a> ';
        }

        $output .= '<li><a class="name-contributor" href="' . $user_profile_url . '">' . $user->display_name . '</a><div class="contributor-info">' . ($user_contact_info ? ' ' . $user_contact_info : '') . '</div></li>';
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
    
    // Build the output HTML
    $output = '<div class="author-name-banner">';
    $output .= '<span class="top-name">Author</span>';
    $output .= '<h1>' . $author_name . '</h1>';
    $output .= '</div>';
    
    // Return the output
    return $output;
}

// Filter to disable automatic paragraph wrapping for the shortcode
add_filter('the_content', 'shortcode_unautop');
add_filter('widget_text_content', 'shortcode_unautop');
add_filter('widget_text_content', 'do_shortcode');

// Register the shortcode
add_shortcode('author_name', 'author_name_shortcode');




function author_bio_shortcode($atts) {
    // Get the author ID
    $author_id = get_queried_object_id();
    
    // Get the author's biography
    $author_bio = get_the_author_meta('description', $author_id);
    
    // Wrap the biography in a <p> element with class
    $output = '<p class="author-bio-banner">' . $author_bio . '</p>';
    
    // Return the author's biography
    return $output;
}
add_shortcode('author_bio', 'author_bio_shortcode');


function author_socials_shortcode() {
    $author_id = get_queried_object_id();
    $author_profile_url = get_author_posts_url($author_id);
    $author_contact_info = '';

    // Check if contact information is available for the author
    if (!empty(get_the_author_meta('twitter', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('twitter', $author_id)) . '"><svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19.7227 4.78125C19.7227 4.99609 19.7227 5.16797 19.7227 5.38281C19.7227 11.3555 15.2109 18.1875 6.91797 18.1875C4.33984 18.1875 1.97656 17.457 0 16.168C0.34375 16.2109 0.6875 16.2539 1.07422 16.2539C3.17969 16.2539 5.11328 15.5234 6.66016 14.3203C4.68359 14.2773 3.00781 12.9883 2.44922 11.1836C2.75 11.2266 3.00781 11.2695 3.30859 11.2695C3.69531 11.2695 4.125 11.1836 4.46875 11.0977C2.40625 10.668 0.859375 8.86328 0.859375 6.67188V6.62891C1.46094 6.97266 2.19141 7.14453 2.92188 7.1875C1.67578 6.37109 0.902344 4.99609 0.902344 3.44922C0.902344 2.58984 1.11719 1.81641 1.50391 1.17188C3.73828 3.87891 7.08984 5.68359 10.8281 5.89844C10.7422 5.55469 10.6992 5.21094 10.6992 4.86719C10.6992 2.375 12.7188 0.355469 15.2109 0.355469C16.5 0.355469 17.6602 0.871094 18.5195 1.77344C19.5078 1.55859 20.4961 1.17188 21.3555 0.65625C21.0117 1.73047 20.3242 2.58984 19.3789 3.14844C20.2812 3.0625 21.1836 2.80469 21.957 2.46094C21.3555 3.36328 20.582 4.13672 19.7227 4.78125Z" fill="black"/></svg></a> ';
    }
    if (!empty(get_the_author_meta('instagram', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('instagram', $author_id)) . '"><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10.625 5.30859C13.332 5.30859 15.5664 7.54297 15.5664 10.25C15.5664 13 13.332 15.1914 10.625 15.1914C7.875 15.1914 5.68359 13 5.68359 10.25C5.68359 7.54297 7.875 5.30859 10.625 5.30859ZM10.625 13.4727C12.3867 13.4727 13.8047 12.0547 13.8047 10.25C13.8047 8.48828 12.3867 7.07031 10.625 7.07031C8.82031 7.07031 7.40234 8.48828 7.40234 10.25C7.40234 12.0547 8.86328 13.4727 10.625 13.4727ZM16.8984 5.13672C16.8984 4.49219 16.3828 3.97656 15.7383 3.97656C15.0938 3.97656 14.5781 4.49219 14.5781 5.13672C14.5781 5.78125 15.0938 6.29688 15.7383 6.29688C16.3828 6.29688 16.8984 5.78125 16.8984 5.13672ZM20.1641 6.29688C20.25 7.88672 20.25 12.6562 20.1641 14.2461C20.0781 15.793 19.7344 17.125 18.6172 18.2852C17.5 19.4023 16.125 19.7461 14.5781 19.832C12.9883 19.918 8.21875 19.918 6.62891 19.832C5.08203 19.7461 3.75 19.4023 2.58984 18.2852C1.47266 17.125 1.12891 15.793 1.04297 14.2461C0.957031 12.6562 0.957031 7.88672 1.04297 6.29688C1.12891 4.75 1.47266 3.375 2.58984 2.25781C3.75 1.14062 5.08203 0.796875 6.62891 0.710938C8.21875 0.625 12.9883 0.625 14.5781 0.710938C16.125 0.796875 17.5 1.14062 18.6172 2.25781C19.7344 3.375 20.0781 4.75 20.1641 6.29688ZM18.1016 15.9219C18.6172 14.6758 18.4883 11.668 18.4883 10.25C18.4883 8.875 18.6172 5.86719 18.1016 4.57812C17.7578 3.76172 17.1133 3.07422 16.2969 2.77344C15.0078 2.25781 12 2.38672 10.625 2.38672C9.20703 2.38672 6.19922 2.25781 4.95312 2.77344C4.09375 3.11719 3.44922 3.76172 3.10547 4.57812C2.58984 5.86719 2.71875 8.875 2.71875 10.25C2.71875 11.668 2.58984 14.6758 3.10547 15.9219C3.44922 16.7812 4.09375 17.4258 4.95312 17.7695C6.19922 18.2852 9.20703 18.1562 10.625 18.1562C12 18.1562 15.0078 18.2852 16.2969 17.7695C17.1133 17.4258 17.8008 16.7812 18.1016 15.9219Z" fill="black"/></svg></a> ';
    }
    if (!empty(get_the_author_meta('facebook', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('facebook', $author_id)) . '"><svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21.6562 11.25C21.6562 16.5781 17.7461 21.0039 12.6328 21.7773V14.3438H15.125L15.5977 11.25H12.6328V9.27344C12.6328 8.41406 13.0625 7.59766 14.3945 7.59766H15.7266V4.97656C15.7266 4.97656 14.5234 4.76172 13.3203 4.76172C10.9141 4.76172 9.32422 6.26562 9.32422 8.92969V11.25H6.61719V14.3438H9.32422V21.7773C4.21094 21.0039 0.34375 16.5781 0.34375 11.25C0.34375 5.36328 5.11328 0.59375 11 0.59375C16.8867 0.59375 21.6562 5.36328 21.6562 11.25Z" fill="black"/></svg></a> ';
    }
    if (!empty(get_the_author_meta('linkedin', $author_id))) {
        $author_contact_info .= '<a href="' . esc_url(get_the_author_meta('linkedin', $author_id)) . '"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.875 0.625C18.6055 0.625 19.25 1.26953 19.25 2.04297V18.5C19.25 19.2734 18.6055 19.875 17.875 19.875H1.33203C0.601562 19.875 0 19.2734 0 18.5V2.04297C0 1.26953 0.601562 0.625 1.33203 0.625H17.875ZM5.80078 17.125V7.97266H2.96484V17.125H5.80078ZM4.38281 6.68359C5.28516 6.68359 6.01562 5.95312 6.01562 5.05078C6.01562 4.14844 5.28516 3.375 4.38281 3.375C3.4375 3.375 2.70703 4.14844 2.70703 5.05078C2.70703 5.95312 3.4375 6.68359 4.38281 6.68359ZM16.5 17.125V12.0977C16.5 9.64844 15.9414 7.71484 13.0625 7.71484C11.6875 7.71484 10.7422 8.48828 10.3555 9.21875H10.3125V7.97266H7.60547V17.125H10.4414V12.6133C10.4414 11.4102 10.6562 10.25 12.1602 10.25C13.6211 10.25 13.6211 11.625 13.6211 12.6562V17.125H16.5Z" fill="black"/></svg></a> ';
    }
    
    $output .= '<div class="author-socials-banner">' . ($author_contact_info ? '' . $author_contact_info : '') . '</div>';

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
        $output .='<div class="authorlist-books">';
        foreach ($books as $book) {
            $product_id = $book->ID;
            $product_title = get_the_title($product_id);
            $product_image = get_the_post_thumbnail($product_id, 'thumbnail');
            $product_link = get_permalink($product_id);
            $product_button = '<a href="' . $product_link . '" class="button transparent">Buy</a>';

            $output .= '<div class="product">';
            $output .= $product_image;
            $output .= '<span>' . $product_title . '</span>';
            $output .= $product_button;
            $output .= '</div>';
        }

        $output .='</div>';
    } 

    return $output;
}
add_shortcode('books_by_author', 'books_by_author_shortcode');

// Custom shortcode to display author's posts with featured image, category, and title
function author_posts_shortcode() {
    // Retrieve author's ID of the current page
    $author_id = get_queried_object_id();

    // Retrieve author's posts based on the author ID
    $args = array(
        'author'         => $author_id,
        'post_type'      => 'post',
        'posts_per_page' => -1,
    );

    // Query the posts
    $query = new WP_Query($args);

    // Start output buffering
    ob_start();

    // Display posts
    if ($query->have_posts()) { ?>
    <h2 class="heading-articles">Author articles</h2>
    <div class="author-articles">
     <?php  while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="author-post">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="featured-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('full'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="post-details">
                    <span class="post-category"><?php the_category(', '); ?></span>
                    <span class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
                </div>
            </div>
            <?php
        } ?>
        </div>
        <?php } 
    // Restore original post data
    wp_reset_postdata();

    // End output buffering and return content
    return ob_get_clean();
}
add_shortcode('author_posts', 'author_posts_shortcode');


// Custom shortcode to display other authors on author page
function other_authors_shortcode() {
    $current_author_id = get_queried_object_id();

    // Retrieve other authors with the "Author" role
    $args = array(
        'role'    => 'author',
        'exclude' => $current_author_id,
        'number'  => 3,
    );

    $authors = get_users($args);

    // Start output buffering
    ob_start();

    // Display other authors
    if (!empty($authors)) {
        ?>
        <h2>See other authors</h2>
        <div class="others-container">
        <?php
        foreach ($authors as $author) {
            $author_name = $author->display_name;
            $author_id   = $author->ID;
            $author_url  = get_author_posts_url($author_id);
            $author_avatar = get_avatar($author_id, 96);

            ?>
            <div class="author-card">
                <a href="<?php echo $author_url; ?>">
                    <?php echo $author_avatar; ?>
                    <div class="author-box"><h5>Author</h5> 
                        <span class="author-name"><?php echo $author_name; ?></span>
                    </div>
                </a>
            </div>
            <?php
        } ?>
        </div>
        <?php } 

    // End output buffering and return content
    return ob_get_clean();
}
add_shortcode('other_authors', 'other_authors_shortcode');

function category_title_shortcode( $atts ) {
    if ( is_category() ) {
        $category = get_queried_object();
        return '<h1>' . $category->name . '</h1>';
    }
}
add_shortcode( 'category_title', 'category_title_shortcode' );

