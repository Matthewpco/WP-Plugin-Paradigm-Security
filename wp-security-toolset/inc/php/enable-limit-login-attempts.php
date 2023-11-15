<?php
if (get_option('wpst_enable_limit_login_attempts')) {
    add_action( 'wp_login_failed', 'increment_failed_login_attempts' );
    add_filter( 'authenticate', 'limit_login_attempts', 100, 3 );
} else {
    remove_action( 'wp_login_failed', 'increment_failed_login_attempts' );
    remove_filter( 'authenticate', 'limit_login_attempts', 100, 3 );
}

// Increment the failed attempts counter on each failed login attempt
function increment_failed_login_attempts( $username ) {
    // Get the user's IP address
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Get the current number of failed login attempts for this IP address
    $failed_attempts = get_transient( 'failed_login_attempts_' . $user_ip );

    // Increment the number of failed attempts
    $failed_attempts++;

    // Set the new value of the transient
    set_transient( 'failed_login_attempts_' . $user_ip, $failed_attempts, 5 * MINUTE_IN_SECONDS );
}

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
