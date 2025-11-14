<?php
/**
 * Plugin Name: Force Login
 * Description: Requires users to be logged in to view any page or post. Redirects to the login page if not logged in.
 * Version: 1.2
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
	// Allow logged-in users, admin area, cron, and CLI
	if ( is_user_logged_in() || is_admin() || defined('DOING_CRON') || defined('WP_CLI') ) {
		return;
	}

	// Allow the login page itself and related actions
	if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		return;
	}

	// Safely encode the current URI for redirect
	$redirect_to = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';
	
	// Redirect to login page with the current page as redirect target
	wp_safe_redirect( wp_login_url( $redirect_to ) );
	exit;
}
add_action( 'template_redirect', 'safl_force_login' );

/**
 * Protect REST API endpoints.
 */
function safl_force_login_rest_api( $result ) {
	// If there's already an error or user is logged in, pass through
	if ( is_wp_error( $result ) || is_user_logged_in() ) {
		return $result;
	}

	// Block non-logged-in users
	return new WP_Error(
		'rest_not_logged_in',
		'You must be logged in to access the REST API.',
		array( 'status' => 401 )
	);
}
add_filter( 'rest_authentication_errors', 'safl_force_login_rest_api', 100 );

/**
 * Block XML-RPC access for non-logged-in users.
 */
function safl_force_login_xmlrpc( $methods ) {
	if ( ! is_user_logged_in() ) {
		return array();
	}
	return $methods;
}
add_filter( 'xmlrpc_methods', 'safl_force_login_xmlrpc' );
