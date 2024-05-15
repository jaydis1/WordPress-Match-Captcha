<?php
/*
Plugin Name: Math Captcha Login
Description: Add a text captcha to the login page.
Version: 0.0.3
Author: Jay Wright
*/

function text_captcha_login_enqueue_scripts() {
    wp_enqueue_style('text-captcha-login-css', plugins_url('assets/style.css', __FILE__));
    wp_enqueue_script('text-captcha-login-js', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('login_enqueue_scripts', 'text_captcha_login_enqueue_scripts');

function text_captcha_login_display() {
    $num1 = rand(1, 100); // Increase the range of numbers
    $num2 = rand(1, 100);

    // Generate a random number (0 to 3) to determine the operation.
    $operation = rand(0, 3);
    switch ($operation) {
        case 0:
            $symbol = '+';
            $expected_result = $num1 + $num2;
            break;
        case 1:
            $symbol = '-';
            $expected_result = $num1 - $num2;
            break;
        case 2:
            $symbol = '*';
            $expected_result = $num1 * $num2;
            break;
        case 3:
            $symbol = '/';
            $expected_result = $num1 / $num2;
            break;
    }

    echo '<p>Please solve this math question: ' . $num1 . ' ' . $symbol . ' ' . $num2 . ' = <input type="text" name="text_captcha_result" /></p>';
}

add_action('login_form', 'text_captcha_login_display');

function text_captcha_login_validation($user) {
    if (isset($_POST['text_captcha_result'])) {
        $entered_result = intval($_POST['text_captcha_result']);

        // Retrieve the operation symbol from the hidden field.
        $operation = sanitize_text_field($_POST['text_captcha_operation']);

        // Retrieve the operands based on the operation.
        $num1 = intval($_POST['log']);
        $num2 = intval($_POST['pwd']);

        // Calculate the expected result based on the operation.
        switch ($operation) {
            case '+':
                $expected_result = $num1 + $num2;
                break;
            case '-':
                $expected_result = $num1 - $num2;
                break;
            case '*':
                $expected_result = $num1 * $num2;
                break;
            case '/':
                // For division, make sure $num2 is not 0 to avoid division by zero.
                if ($num2 === 0) {
                    return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Incorrect captcha.'));
                }
                $expected_result = $num1 / $num2;
                break;
        }

        if ($entered_result !== $expected_result) {
            // Add 'incorrect' class to the captcha field for incorrect answer
            echo '<script>document.querySelector(".text-captcha input[type=text]").classList.add("incorrect");</script>';
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Incorrect captcha.'));
        } else {
            // Add 'correct' class to the captcha field for correct answer
            echo '<script>document.querySelector(".text-captcha input[type=text]").classList.add("correct");</script>';
        }
    }

    return $user;
}

add_filter('wp_authenticate_user', 'text_captcha_login_validation', 10, 1);
