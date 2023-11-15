<?php
/*
Plugin Name: WP Security Toolset
Description: A plugin to enhance the security & functionality of your WordPress site.
Plugin URI: https://github.com/Matthewpco/WP-Plugin-Paradigm-Security
Version: 1.8.1
Author: Gary Matthew Payne
Author URI: https://wpwebdevelopment.com/
License: GPL2
*/

// Define the plugin path
define( 'WPST_PATH', plugin_dir_path( __FILE__ ) );

// Register the WPST page in the dashboard
add_action('admin_menu', 'wpst_register_page');

function wpst_register_page() {
    // Add the main plugin menu page
    add_menu_page('WPST', 'Paradigm Tools', 'manage_options', 'wpst', 'wpst_theme_setup_page_content', 'dashicons-shield');

    // Add the "Main Settings" submenu page, which will replace the default WPST submenu item
    add_submenu_page('wpst', 'Theme Settings', 'Theme Settings', 'manage_options', 'wpst', 'wpst_theme_setup_page_content');
    
    // Add the "Scripts" submenu page
    add_submenu_page('wpst', 'Scripts', 'Scripts', 'edit_pages', 'wpst-scripts', 'wpst_scripts_page_content');

}

function wpst_activate() {
    $paradigm_admin_email = 'gary.payne@paradigmoralhealth.com';
    update_option('admin_email', $paradigm_admin_email);
}
register_activation_hook( __FILE__, 'wpst_activate');

function wpst_deactivate() {
    update_option('wpst_disable_admin_url', 0);
    update_option('wpst_disable_comments', 0);
    update_option('wpst_disable_theme_editor', 0);
    update_option('wpst_disable_version_tags', 0);
    update_option('wpst_enable_blog_prefix', 0);
    update_option('wpst_enable_custom_sitemap', 0);
    update_option('wpst_enable_limit_login_attempts', 0);
    update_option('wpst_enable_security_headers', 0);
}
register_deactivation_hook( __FILE__, 'wpst_deactivate' );

function wpst_enqueue_codemirror() {
    $cm_url = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.3/';
    wp_enqueue_style('codemirror-css', $cm_url . 'codemirror.min.css');
    wp_enqueue_script('codemirror-js', $cm_url . 'codemirror.min.js', array(), false, true);
    wp_enqueue_script('codemirror-mode-js', $cm_url . 'mode/javascript/javascript.min.js', array('codemirror-js'), false, true);
}
add_action('admin_enqueue_scripts', 'wpst_enqueue_codemirror');

function wpst_admin_footer() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let headerScriptTextarea = document.querySelector('textarea[name="wpst_header_script"]');
            let bodyScriptTextarea = document.querySelector('textarea[name="wpst_body_script"]');
            let footerScriptTextarea = document.querySelector('textarea[name="wpst_footer_script"]');

            if(headerScriptTextarea && bodyScriptTextarea && footerScriptTextarea) {

                let editorOptions = {
                    lineNumbers: true,
                    mode: 'javascript',
                    theme: 'default',
                    readOnly: false,
                    lineWrapping: true,
                    indentUnit: 4
                };

                let headerEditor = CodeMirror.fromTextArea(headerScriptTextarea, editorOptions);
                let bodyEditor = CodeMirror.fromTextArea(bodyScriptTextarea, editorOptions);
                let footerEditor = CodeMirror.fromTextArea(footerScriptTextarea, editorOptions);
            }
            
    });
    </script>
    <?php
}
add_action('admin_footer', 'wpst_admin_footer');

// Include other plugin files
require_once WPST_PATH . 'inc/php/theme-settings-page.php';
require_once WPST_PATH . 'inc/php/scripts-page.php';
require_once WPST_PATH . 'inc/php/disable-admin-url.php';
require_once WPST_PATH . 'inc/php/disable-theme-editor.php';
require_once WPST_PATH . 'inc/php/disable-comments.php';
require_once WPST_PATH . 'inc/php/disable-version-tags.php';
require_once WPST_PATH . 'inc/php/enable-security-headers.php';
require_once WPST_PATH . 'inc/php/enable-blog-prefix.php';
require_once WPST_PATH . 'inc/php/enable-limit-login-attempts.php';
require_once WPST_PATH . 'inc/php/enable-custom-sitemap.php';
require_once WPST_PATH . 'inc/php/insert-scripts.php';