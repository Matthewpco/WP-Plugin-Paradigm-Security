<?php
// Disable file editor
if (get_option('wpst_disable_theme_editor')) {
    define( 'DISALLOW_FILE_EDIT', true );
} else {
    define( 'DISALLOW_FILE_EDIT', false );
}