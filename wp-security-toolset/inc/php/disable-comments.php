<?php
// Disable all comments if the option is checked
if (get_option('wpst_disable_comments')) {
    add_action('init', 'wpst_disable_comments');
} else {
    remove_action('init', 'wpst_disable_comments');
}

function wpst_disable_comments() {
    // Disable comments on posts and pages
    add_filter('comments_open', '__return_false');
    add_filter('pings_open', '__return_false');

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array');

    // Remove comments from admin menu and dashboard
    add_action('admin_menu', 'wpst_remove_comments_menu');
    add_action('admin_init', 'wpst_remove_comments_dashboard');
}

function wpst_remove_comments_menu() {
    remove_menu_page('edit-comments.php');
}

function wpst_remove_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
