<?php
/*
Plugin Name: Text Captcha Login
Description: Add a text captcha to the login page.
Version: 0.0.1
Author: Jay Wright
*/

function text_captcha_login_enqueue_scripts() {
    wp_enqueue_style('text-captcha-login-css', plugins_url('assets/style.css', __FILE__));
    wp_enqueue_script('text-captcha-login-js', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('login_enqueue_scripts', 'text_captcha_login_enqueue_scripts');

function text_captcha_login_display() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $result = $num1 + $num2;

    echo '<p>Please solve this math question: ' . $num1 . ' + ' . $num2 . ' = <input type="text" name="text_captcha_result" /></p>';
}
add_action('login_form', 'text_captcha_login_display');

function text_captcha_login_validation($user) {
    if (isset($_POST['text_captcha_result'])) {
        $entered_result = intval($_POST['text_captcha_result']);
        $num1 = intval($_POST['log']);
        $num2 = intval($_POST['pwd']);
        $expected_result = $num1 + $num2;

        if ($entered_result !== $expected_result) {
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Incorrect captcha.'));
        }
    }

    return $user;
}
add_filter('wp_authenticate_user', 'text_captcha_login_validation', 10, 1);
