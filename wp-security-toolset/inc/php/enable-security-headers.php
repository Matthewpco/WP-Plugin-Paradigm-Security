<?php
// Send various headers for better security
if (get_option('wpst_enable_security_headers')) {
    add_action('send_headers', 'add_security_headers');
} else {
    remove_action('send_headers', 'add_security_headers');
}

function add_security_headers() {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header("Referrer-Policy: no-referrer-when-downgrade");
}
