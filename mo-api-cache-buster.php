<?php
/*
Plugin Name: miniOrange WP REST API Cache Buster
Plugin URI: https://github.com/JoshuaGoode/mo-api-cache-buster
Description: Prevents caching for specific REST API endpoints under the 'moserver' namespace. It applies nocache headers and cancels Batcache caching for these endpoints.
Version: 1.0.0
Author: Joshua Goode
Author URI: https://goode.pro
*/

/**
 * Prevents GET caching for miniOrange's Login using WordPress Users (miniorange-wp-saml-idp-premium) plugin when hosted on WP Cloud-based hosts.
 * Specifically written for versions 13.1.2 of the miniorange-wp-saml-idp-premiu and lower. May not be necessary in future versions of the plugin.
 * This iteration is intended to be used as an mu-plugin.
 */

// Check if the WP_REST_Request class exists to prevent errors in non-REST request contexts
if ( ! class_exists( 'WP_REST_Request' ) ) {
    return;
}

/**
 * Applies no-cache headers and cancels Batcache for specific REST API namespace routes.
 *
 * @param mixed           $result  Response to replace the requested version with. Can be anything
 *                                 a normal endpoint can return, or null to not hijack the request.
 * @param WP_REST_Server  $server  Server instance.
 * @param WP_REST_Request $request Request used to generate the response.
 *
 * @return mixed The unmodified $result, ensuring default processing continues if not our target route.
 */
function apply_nocache_headers_for_moserver_namespace( $result, $server, WP_REST_Request $request ) {
    $namespace = 'moserver';
    $request_route = $request->get_route();

    if ( strpos( $request_route, '/' . $namespace . '/' ) === 0 ) {
      // Optional Error Logging for testing purposes
      //      error_log( 'Applying nocache headers and cancelling batcache for REST API Namespace: ' . $request_route );

        nocache_headers(); // Sets the HTTP headers to prevent caching for the different browsers. https://developer.wordpress.org/reference/functions/nocache_headers/

        if ( function_exists( 'batcache_cancel' ) ) {
            batcache_cancel(); // Prevents batcache page caching if triggered https://github.com/Automattic/batcache/blob/master/advanced-cache.php
        }
    }

    return $result;
}

add_filter( 'rest_pre_dispatch', 'apply_nocache_headers_for_moserver_namespace', 10, 3 );
