<?php
// Disable all comments if the option is checked
if (get_option('wpst_disable_version_tags')) {
    add_filter('the_generator', 'remove_version');
    add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
    add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );
} else {
    remove_filter('the_generator', 'remove_version');
    remove_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
    remove_filter( 'style_loader_src', 'remove_script_version', 15, 1 );
}

// Remove version tags from metadata
function remove_version() {
    return '';
}

// Remove version tags from scripts
function remove_script_version( $src ){
    return remove_query_arg( 'ver', $src );
}
