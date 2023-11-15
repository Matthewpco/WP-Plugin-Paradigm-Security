<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if the option exists and delete it
if (get_option('wpst_disable_admin_url') !== false) {
    delete_option('wpst_disable_admin_url');
}

if (get_option('wpst_disable_comments') !== false) {
    delete_option('wpst_disable_comments');
}

if (get_option('wpst_disable_version_tags') !== false) {
    delete_option('wpst_disable_version_tags');
}

if (get_option('wpst_disable_theme_editor') !== false) {
    delete_option('wpst_disable_theme_editor');
}

if (get_option('wpst_enable_security_headers') !== false) {
    delete_option('wpst_enable_security_headers');
}

if (get_option('wpst_enable_alternate_login') !== false) {
    delete_option('wpst_enable_alternate_login');
}

if (get_option('wpst_enable_limit_login_attempts') !== false) {
    delete_option('wpst_enable_limit_login_attempts');
}

if (get_option('wpst_enable_custom_sitemap') !== false) {
    delete_option('wpst_enable_custom_sitemap');
}

if (get_option('wpst_enable_blog_prefix') !== false) {
    delete_option('wpst_enable_blog_prefix');
}