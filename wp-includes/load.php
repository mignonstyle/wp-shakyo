<?php
/**
 * These functions are needed to load WordPress.
 *
 * @package WordPress
 */

/**
 * Return the HTTP protocol send by the server.
 *
 * @since 4.4.0
 *
 * @return string The HTTP protocol. Default: HTTP/1.0
 */
function wp_get_server_protocol() {
    $protocol = $_SERVER['SERVER_PROTOCOL'];
    if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0') ) ) {
        $protocol = 'HTTP/1.0';
    }
    return $protocol;
}


echo('hogehoge');
