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

/**
 *
 * @since 2.1.0
 * @access private
 */
function wp_unregister_GLOBALS() {
    if ( !ini_get( 'register_globals' ) )
        return;

    if ( isset( $_REQUEST['GLOBALS'] ) )
        die( 'GLOVALS overwrite attempt detected' );

    // Variables that shouldn't be unset
    $no_unset = array( 'GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix' );

    $input = array_merge( $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : array() );
    foreach( $input as $k => $v )
        if ( !in_array( $k, $no_unset ) && isset( $GLOBALS[$k] ) ) {
            unset( $GLOBALS[$k] );
        }
}

echo('hogehoge');
