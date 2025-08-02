<?php
/**
 * Plugin Name: Force Login
 * Description: Requires users to be logged in to view any page or post. Redirects to the login page if not logged in.
 * Version: 1.0
 * Author: Siim Aarmaa
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Redirect non-logged-in users to the login page.
 *
 * This function is hooked into the 'template_redirect' action.
 */
function my_force_login() {
	// Exceptions for the login page, admin area, and cron jobs.
	if ( is_user_logged_in() || is_admin() || defined('DOING_CRON') || defined('WP_CLI') ) {
		return;
	}

	// Redirect to the login page.
	wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
	exit;
}
add_action( 'template_redirect', 'my_force_login' );
