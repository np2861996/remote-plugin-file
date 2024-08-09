<?php
/**
 * Plugin Name: Remote Plugins
 * Plugin URI: https://example.com/remote-plugins
 * Description: Provides a REST API for managing plugins.
 * Version: 1.0.0
 * Author: KamalDhari Infotech
 * Author URI: https://www.kamaldhari.com/
 * License: GPLv2 or later
 * Text Domain: remote-plugins
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constants for API version, namespace, and authentication token
define( 'REMOTE_PLUGINS_API_VERSION', 'v1' );
define( 'REMOTE_PLUGINS_API_NAMESPACE', 'kd' );
define( 'REMOTE_PLUGINS_AUTH_TOKEN', 'WxScDzJOYXEAcCmDSSDuieM48WpTeZGC' ); // Static token for testing

// Register REST API routes
add_action( 'rest_api_init', 'remote_plugins_register_routes' );

function remote_plugins_register_routes() {
    $namespace = REMOTE_PLUGINS_API_NAMESPACE . '/' . REMOTE_PLUGINS_API_VERSION;

    // Register route for listing plugins
    register_rest_route( $namespace, '/listplugins', array(
        'methods' => 'GET',
        'callback' => 'remote_plugins_list_plugins',
        'permission_callback' => 'remote_plugins_check_auth', // Ensure authentication is checked
    ) );

    // Register route for activating a plugin
    register_rest_route( $namespace, '/activate', array(
        'methods' => 'POST',
        'callback' => 'remote_plugins_activate_plugin',
        'permission_callback' => 'remote_plugins_check_auth', // Ensure authentication is checked
        'args' => array(
            'plugin' => array(
                'required' => true,
                'validate_callback' => 'remote_plugins_validate_plugin', // Validate plugin parameter
            ),
        ),
    ) );

    // Register route for deactivating a plugin
    register_rest_route( $namespace, '/deactivate', array(
        'methods' => 'POST',
        'callback' => 'remote_plugins_deactivate_plugin',
        'permission_callback' => 'remote_plugins_check_auth', // Ensure authentication is checked
        'args' => array(
            'plugin' => array(
                'required' => true,
                'validate_callback' => 'remote_plugins_validate_plugin', // Validate plugin parameter
            ),
        ),
    ) );
}

// Callback function to list all plugins
function remote_plugins_list_plugins( WP_REST_Request $request ) {
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Ensure plugin functions are available
    }

    $plugins = get_plugins(); // Get all plugins
    return rest_ensure_response( $plugins ); // Return response in REST format
}

// Callback function to activate a plugin
function remote_plugins_activate_plugin( WP_REST_Request $request ) {
    $plugin_slug = sanitize_text_field( $request->get_param( 'plugin' ) ); // Sanitize plugin slug

    if ( ! $plugin_slug ) {
        return new WP_Error( 'invalid_plugin', 'Invalid plugin name provided.', array( 'status' => 400 ) ); // Error if plugin name is invalid
    }

    $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php'; // Build plugin file path

    if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
        return new WP_Error( 'plugin_not_found', 'Plugin not found.', array( 'status' => 404 ) ); // Error if plugin file does not exist
    }

    if ( ! is_plugin_active( $plugin_file ) ) {
        activate_plugin( $plugin_file ); // Activate the plugin
        return rest_ensure_response( array( 'message' => 'Plugin activated successfully.' ) ); // Success response
    } else {
        return new WP_Error( 'plugin_already_active', 'Plugin is already activated.', array( 'status' => 409 ) ); // Error if plugin is already active
    }
}

// Callback function to deactivate a plugin
function remote_plugins_deactivate_plugin( WP_REST_Request $request ) {
    $plugin_slug = sanitize_text_field( $request->get_param( 'plugin' ) ); // Sanitize plugin slug

    if ( ! $plugin_slug ) {
        return new WP_Error( 'invalid_plugin', 'Invalid plugin name provided.', array( 'status' => 400 ) ); // Error if plugin name is invalid
    }

    $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php'; // Build plugin file path

    if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
        return new WP_Error( 'plugin_not_found', 'Plugin not found.', array( 'status' => 404 ) ); // Error if plugin file does not exist
    }

    if ( is_plugin_active( $plugin_file ) ) {
        deactivate_plugins( $plugin_file ); // Deactivate the plugin
        return rest_ensure_response( array( 'message' => 'Plugin deactivated successfully.' ) ); // Success response
    } else {
        return new WP_Error( 'plugin_already_deactivated', 'Plugin is already deactivated.', array( 'status' => 409 ) ); // Error if plugin is already deactivated
    }
}

// Validation callback for plugin parameter
function remote_plugins_validate_plugin( $param, $request, $key ) {
    return ! empty( $param ) && is_string( $param ); // Check if parameter is not empty and is a string
}

// Authorization callback to check if the request has a valid token
function remote_plugins_check_auth( WP_REST_Request $request ) {
    $headers = $request->get_headers();
    $auth_header = isset( $headers['authorization'] ) ? $headers['authorization'][0] : '';

    error_log('Headers: ' . print_r($headers, true));
    error_log('Auth header: ' . $auth_header);

    if ( stripos( $auth_header, 'Bearer ' ) === 0 ) {
        $auth_token = substr( $auth_header, 7 ); // Extract token from the header
        error_log('Extracted token: ' . $auth_token);
        return $auth_token === REMOTE_PLUGINS_AUTH_TOKEN; // Validate token
    }

    return false;
}
