<?php

add_action('wp_head', 'wpst_insert_header_script', 0);
add_action('wp_body_open', 'wpst_insert_body_script', 0);
add_action('wp_footer', 'add_schema_to_footer');

function wpst_insert_header_script() {
    $header_script = get_option('wpst_header_script');
    if (!empty($header_script)) {
        echo wp_unslash($header_script);
    }
}

function wpst_insert_body_script() {
    $body_script = get_option('wpst_body_script');
    if (!empty($body_script)) {
        echo wp_unslash($body_script);
    }
}

// Add footer schema data
function add_schema_to_footer() {
    $footer_script = get_option('wpst_footer_script');
    if (!empty($footer_script)) {
        echo wp_unslash($footer_script);
    }
}
