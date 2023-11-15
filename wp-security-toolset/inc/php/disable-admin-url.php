<?php
if (get_option('wpst_disable_admin_url')) {
    add_filter( 'login_url', 'custom_login_url', PHP_INT_MAX );
    $source_file = plugin_dir_path( __FILE__ ) . 'paradigm-admin.php';
    $destination_file = ABSPATH . 'paradigm-admin.php';
    if(file_exists($source_file)) {
        copy( $source_file, $destination_file );
    }
    add_action( 'init', 'wpst_insert_htaccess_rules' );
} else {
    remove_filter( 'login_url', 'custom_login_url', PHP_INT_MAX );
    $file_to_delete = ABSPATH . 'paradigm-admin.php';
    if(file_exists($file_to_delete)) {
        unlink($file_to_delete);
    }
    add_action( 'init', 'wpst_restore_htaccess_file' );
}

require_once( ABSPATH . 'wp-admin/includes/template.php' );
require_once( ABSPATH . 'wp-admin/includes/misc.php' );

// Change WP Login file URL using "login_url" filter hook
function custom_login_url( $login_url ) {
	$login_url = site_url( 'paradigm-admin.php', 'login' );	
    return $login_url;
}

function wpst_insert_htaccess_rules() {
    // Set the path to the .htaccess file
    $htaccess_file = ABSPATH . '.htaccess';

    // Get the path to the current plugin's directory
    $plugin_dir = plugin_dir_path( __FILE__ );

    // Set the path to the backup file
    $backup_file = dirname( dirname( $plugin_dir ) ) . '/.htaccess-backup';

    // Set the desired permissions (read and write for owner, read for group and others)
    $permissions = 0644;

    // Create a backup of the .htaccess file
    if (file_exists($htaccess_file) && is_writable($htaccess_file)) {
        // Create a backup of the .htaccess file if it doesn't exist
        if (!file_exists($backup_file)) {
            if (copy($htaccess_file, $backup_file)) {
                add_settings_error(
                    'wpst',
                    'wpst_message',
                    'SUCCESS: Created backup of .htaccess file',
                    'success'
                );
            } else {
                error_log('ERROR: Could not create backup of .htaccess file');
                add_settings_error(
                    'wpst',
                    'wpst_message',
                    'ERROR: Could not create backup of .htaccess file',
                    'error'
                );
            }
        }
    } 

    $rules = array(
        'RewriteEngine On',
        'RewriteCond %{QUERY_STRING} !^action=lostpassword',
        'RewriteCond %{QUERY_STRING} !^action=rp',
        'RewriteCond %{QUERY_STRING} !^action=resetpass',
        'RewriteCond %{QUERY_STRING} !^checkemail=confirm',
        'RewriteCond %{QUERY_STRING} !^action=logout',
        'RewriteRule ^wp-login.php$ - [F,L]',
        'RewriteCond %{REQUEST_URI} ^/wp-admin/',
        'RewriteCond %{QUERY_STRING} !^action=logout',
        'RewriteCond %{QUERY_STRING} !^action=lostpassword',
        'RewriteCond %{QUERY_STRING} !^action=rp',
        'RewriteCond %{QUERY_STRING} !^action=resetpass',
        'RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in.*$ [NC]',
        'RewriteCond %{REQUEST_URI} !^/wp-admin/admin-ajax.php',
        'RewriteRule . - [F,L]',
    );

    if (file_exists($htaccess_file)) {

        $existing_rules = extract_from_markers($htaccess_file, 'WP Security Toolset');

        if ($existing_rules === $rules) {
            // The rules have already been inserted, so return early.
            return;
        }

        // Insert the rules and display the success message.

        $result = insert_with_markers( $htaccess_file, 'WP Security Toolset', $rules );
        if ($result === false) {
            error_log('ERROR: Could not insert rules into .htaccess file');
            add_settings_error(
                'wpst',
                'wpst_message',
                'ERROR: Could not insert rules into .htaccess file',
                'error'
            );
        } else {
            add_settings_error(
                'wpst',
                'wpst_message',
                'SUCCESS: Inserted rules to disable default login in .htaccess file',
                'success'
            );
        }
    } 
}


function wpst_restore_htaccess_file() {
    // Set the path to the .htaccess file
    $htaccess_file = ABSPATH . '.htaccess';

    // Get the path to the current plugin's directory
    $plugin_dir = plugin_dir_path( __FILE__ );

    // Set the path to the backup file
    $backup_file = dirname( dirname( $plugin_dir ) ) . '/backup/.htaccess-backup';

    // Set the desired permissions (read and write for owner, read for group and others)
    $permissions = 0644;
    
    // Restore the original .htaccess file from the backup
    if (file_exists($backup_file) && is_writable($backup_file)) {

        if (copy($backup_file, $htaccess_file)) {
            add_settings_error(
                'wpst',
                'wpst_message',
                'SUCCESS: Copied backup to htaccess file',
                'success'
            );
        } else {
            error_log('ERROR: Could not restore .htaccess file from backup');
            add_settings_error(
                'wpst',
                'wpst_message',
                'ERROR: Could not restore .htaccess file from backup',
                'error'
            );
        }
        
        // Delete the backup file
        if (unlink($backup_file)) {
            add_settings_error(
                'wpst',
                'wpst_message',
                'SUCCESS: Copied backup to htaccess file',
                'success'
            );
        } else {
            error_log('ERROR: Could not delete backup file');
            add_settings_error(
                'wpst',
                'wpst_message',
                'ERROR: Could not delete backup',
                'error'
            );
        }
        
    } 
}