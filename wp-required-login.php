<?php
/**
 * Plugin Name: Force Login
 * Description: Requires users to be logged in to view any page or post. Redirects to the login page if not logged in.
 * Version: 1.1
 * Author: Siim Aarmaa
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Redirect non-logged-in users to the login page (for front-end pages).
 */
function safl_force_login() {
	// Exceptions for logged-in users, admin area, and system processes.
	if ( is_user_logged_in() || is_admin() || defined('DOING_CRON') || defined('WP_CLI') ) {
		return;
	}

	// Redirect to the login page, passing the current URL as the redirect target.
	wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
	exit;
}
add_action( 'template_redirect', 'safl_force_login' );

/**
 * Protect REST API endpoints.
 *
 * This function is hooked into 'rest_api_init'.
 */
function safl_force_login_rest_api( $result ) {
    // If the user is already logged in, don't do anything.
    if ( is_user_logged_in() ) {
        return $result;
    }

    // Not logged in. Return an error.
    return new WP_Error(
        'rest_not_logged_in',
        'You are not currently logged in.',
        array( 'status' => 401 ) // 401 Unauthorized
    );
}
// Using a priority of 100 to run after most other checks.
add_filter( 'rest_authentication_errors', 'safl_force_login_rest_api', 100 );
