<?php
require get_theme_file_path('/inc/search-route.php');

function university_custom_rest()
{
    register_rest_field('post', 'authorName', array(
        'get_callback' => function () {
            return get_the_author();
        }
    ));
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL)
{
    // php logic will live here
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['photo']) {
        if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }

?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php
                                                                        echo $args['photo'];
                                                                        ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'] ?></p>
            </div>
        </div>
    </div>


<?php }

function university_files()
{
    wp_enqueue_style('costum-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awsome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    if (strstr($_SERVER['SERVER_NAME'], 'wordpress-test.local')) {
        wp_enqueue_script('main-university-js', 'http://localhost:3000/bundled.js', NULL, '1.0', true);
    } else {
        wp_enqueue_script('our-vendor-js', get_theme_file_uri('/bundled-assets/vendors~scripts.9678b4003190d41dd438.js'), NULL, '1.0', true);
        wp_enqueue_script('main-university-js', get_theme_file_uri('/bundled-assets/scripts.9ad6cfcc2958781d2597.js'), NULL, '1.0', true);
        wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.9ad6cfcc2958781d2597.css'));
    }
    wp_localize_script('main-university-js', 'univeristyData', array(
        'root_url' => get_site_url(),
    ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
    add_theme_support('title-tag');
    // Enabling image relations with posts, featured images
    add_theme_support('post-thumbnails');
    // Specified image sizes
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');


// Hooke into the query before wordpress queryes the posts
function university_adjust_queries($query)
{
    $today = date('Ymd');

    // Change the query for EVENTS
    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }

    // Change the query for PROGRAMS
    if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
        $query->set('post_per_page', -1);
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }
}
add_action('pre_get_posts', 'university_adjust_queries');

// Redirect subscriber accounts out of admin and onto homepage
function redirectSubsToFrontend()
{
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) === 1 and $currentUser->roles[0] === 'subscriber') {
        wp_redirect(site_url('/'));
    }
}
add_action('admin_init', 'redirectSubsToFrontend');

// Hide addmin bar for subscribers
function hideAdminBar()
{
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) === 1 and $currentUser->roles[0] === 'subscriber') {
        show_admin_bar(false);
    }
}
add_action('wp_loaded', 'hideAdminBar');

// Customize logn screen
function ourHeaderUrl()
{
    return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'ourHeaderUrl');

// Load scripts and css onto lofin screen
function loginCSS()
{
    wp_enqueue_style('costum-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.9ad6cfcc2958781d2597.css'));
}
add_action('login_enqueue_scripts', 'loginCSS');

// Changing the title of the login screen
function ourLoginTitle()
{
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'ourLoginTitle');
// function initCors( $value ) {
//     $origin_url = get_http_origin();

//     header( 'Access-Control-Allow-Origin: ' . esc_url_raw($origin_url) );
//     header( 'Access-Control-Allow-Methods: GET' );
//     header( 'Access-Control-Allow-Credentials: true' );
//     return $value;
//   }
// add_action('init', 'initCors');

// function rest_filter_incoming_connections($errors) {
//     $request_server = $_SERVER['REMOTE_ADDR'];
//     $origin = $_SERVER['HTTP_ORIGIN'];
//     if ($origin !== 'wordpress-test.local') return new WP_Error('forbidden_access', $origin, array(
//         'status' => 403,
//         'message' => $origin,
//     ));
//     return $errors;
// }
// add_filter('rest_authentication_errors', 'rest_filter_incoming_connections');

?>