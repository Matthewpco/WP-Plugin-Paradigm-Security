<?php
// Display the WPST Scripts page content
function wpst_scripts_page_content() {
    // Check if the user has editor permissions
    if (!current_user_can('edit_pages')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }    

    // Display any error messages
    settings_errors( 'wpst' );

    // Check if the form has been submitted
    if (isset($_POST['submit'])) {

        // Update the options in the database
        $header_script = isset($_POST['wpst_header_script']) ? $_POST['wpst_header_script'] : '';
        update_option('wpst_header_script', $header_script);

        $body_script = isset($_POST['wpst_body_script']) ? $_POST['wpst_body_script'] : '';
        update_option('wpst_body_script', $body_script);

        $footer_script = isset($_POST['wpst_footer_script']) ? $_POST['wpst_footer_script'] : '';
        update_option('wpst_footer_script', $footer_script);

    }

    // Display the form
    ?>
    <div class="wrap">
        <h2>WP Security Toolset</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Header Script</th>
                    <td><textarea name="wpst_header_script" rows="10" cols="100"><?php echo wp_unslash(get_option('wpst_header_script')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Body Script</th>
                    <td><textarea name="wpst_body_script" rows="10" cols="100"><?php echo wp_unslash(get_option('wpst_body_script')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Footer Script</th>
                    <td><textarea name="wpst_footer_script" rows="10" cols="100"><?php echo wp_unslash(get_option('wpst_footer_script')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
