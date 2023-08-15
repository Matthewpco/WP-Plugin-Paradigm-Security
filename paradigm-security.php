<?php
/*
Plugin Name: Paradigm Security
Description: Paradigm security standards. Includes wp-admin url change, limit login attempts, disabling file editors, and incident reports by mail.
Plugin URI: https://github.com/Matthewpco/
Version: 1.1.0 
Author: Gary Matthew Payne
Author URI: https://wpwebdevelopment.com/
License: GPL2
Instructions: Copy paradigm-admin.php file to root directory and delete wp-login.php then uncomment login url filter.
*/


// Check for environment constant and if not defined, define it
if (!defined('ENVIRONMENT')) {
    $current_url = get_site_url();
    if (strpos($current_url, 'cloudways') !== false || strpos($current_url, 'local') !== false) {
        define('ENVIRONMENT', 'dev');
    } else {
        define('ENVIRONMENT', 'prod');
    }
}

// Disable file editor
define( 'DISALLOW_FILE_EDIT', true );

// Change WP Login file URL using "login_url" filter hook - uncomment after uploading paradigm-admin.php file to root directory.
function custom_login_url( $login_url ) {
	$login_url = site_url( 'paradigm-admin.php', 'login' );	
    return $login_url;
}
add_filter( 'login_url', 'custom_login_url', PHP_INT_MAX );


// Increment the failed attempts counter on each failed login attempt
add_action( 'wp_login_failed', function( $username ) {

    // Get the user's IP address
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Get the current number of failed login attempts for this IP address
    $failed_attempts = get_transient( 'failed_login_attempts_' . $user_ip );

    // Increment the failed attempts counter
    set_transient( 'failed_login_attempts_' . $user_ip, ++$failed_attempts, 5 * MINUTE_IN_SECONDS );

});


// Ban a user after 5 failed login attempts for 5 minutes
function limit_login_attempts( $user, $username, $password ) {
    // Only check login attempts if a username was entered
    if ( ! empty( $username ) ) {
        // Set the maximum number of failed login attempts
        $max_attempts = 4;

        // Get the user's IP address
        $user_ip = $_SERVER['REMOTE_ADDR'];

        // Get the current number of failed login attempts for this IP address
        $failed_attempts = get_transient( 'failed_login_attempts_' . $user_ip );

        // If the maximum number of failed attempts has been reached, show an error message
        if ( $failed_attempts >= $max_attempts ) {

            // Log the user's IP address to an error log file
            error_log( 'Failed login attempt from IP address: ' . $user_ip . ' on username ' . $username );

            // Send data to email
            $to = 'gary.payne@paradigmoralhealth.com';
            $subject = 'New ban on user';
            $message = 'New ban on user from too many login attempts from ' . $username . ' at ip address ' . $user_ip .  "\r\n";
            wp_mail($to, $subject, $message);

            // Show error on screen
            $error = new WP_Error();
            $error->add( 'too_many_attempts', '<strong>ERROR:</strong> You have reached the maximum number of login attempts. Please try again later.' );
            return $error;
        }

        // If the login is successful, reset the failed attempts counter
        add_action( 'wp_login', function() use ( $user_ip ) {
            delete_transient( 'failed_login_attempts_' . $user_ip );
        });
    }

    return $user;
}
add_filter( 'authenticate', 'limit_login_attempts', 100, 3 );


// Send various headers for better security
function add_security_headers() {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header("Referrer-Policy: no-referrer-when-downgrade");
}
add_action('send_headers', 'add_security_headers');


// Remove version tags from metadata
function remove_version() {
    return '';
}
add_filter('the_generator', 'remove_version');


// Remove version tags from scripts
function remove_script_version( $src ){
    return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );


// Disable comments
add_action('admin_init', function () {
	// Redirect any user trying to access comments page
	global $pagenow;

	if ($pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php') {
		wp_redirect(admin_url());
		exit;
	}

	// Remove comments metabox from dashboard
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

	// Disable support for comments and trackbacks in post types
	foreach (get_post_types() as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
  remove_menu_page('edit-comments.php');
	remove_submenu_page('options-general.php', 'options-discussion.php');
});

// Remove comments links from admin bar
add_action('init', function () {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
});

// Remove comments icon from admin bar
add_action('wp_before_admin_bar_render', function() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
});

// Return a comment count of zero to hide existing comment entry link.
function zero_comment_count($count){
	return 0;
}
add_filter('get_comments_number', 'zero_comment_count');