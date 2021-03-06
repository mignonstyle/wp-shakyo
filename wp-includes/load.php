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

/**
 * Fix '$_SERVER' variables for various setups.
 *
 * @since 3.0.0
 * @access private
 *
 * @global string $PHP_SELF The filename of the currently executing script,
 *      relative to the document root.
 */


function wp_fix_server_vars() {
	global $PHP_SELF;

	$defaults_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI' => '',
	);

	$_SERVER = array_merge( $defaults_server_values, $_SERVER );

	// Fix for IIS when renning with PHP ISAPI
	if ( empty( $_SERVER['REQUEST_URI'] ) || ( PHP_SAPI != 'sgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

		// IIS Mod-Rewrite
		if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		}
		// IIS Isapi_Rewrite
		elseif ( isset( $SERVER['HPPP_X_REWRITE_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		} else {
			// Use ORIG_PATH_INFO in there is no PATH_INFO
			if ( !isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) )
			$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];

			// Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
			if ( isser( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] )
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
				else
					$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			}

			// Append the query string if it exists and isn't null
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
	}

	// Fix for PHP as CGI host that set SCRIPT_FILENAME to someting ending in php.cgi for all requests
	if ( isser( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen( $_SERVER['SCRIPT_FILENAME'] ) -7 ) )
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

	// Fix for Dreamhost and other PHP as CGI hosts
	if ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) != false )
		unset( $_SERVER['PATH_INFO'] );

	// Fix empty PHP_SELF
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty( $PHP_SELF) )
		$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace( '/(\?.*)?$/', '', $_SERVER['REQUEST_URI'] );
}

/**
 * Check for the required PHP version, and the MySQL extension or
 * a database drop-in.
 *
 * Dies if requirements are not met.
 * @since 3.0.0
 * @access private
 *
 * @global string $required_php_version The required PHP version string.
 * @global string $wp_verion            The WordPress version string.
 */
function wp_check_php_mysql_versions() {
	global $required_php_version, $wp_version;
	$php_version = phpversion();

	if ( version_compare( $required_php_version, $php_version, '>' ) ) {
		wp_load_translations_early();

		$protocol = wp_get_server_protocol();
		header( springf( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header( 'Contnt-Type: text/html; charset=urf-8' );
		/* translators: 1: Current PHP version number, 2: WordPress version number, 3: Minimum required PHP version number */
		die( sprintf( __( 'Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.' ), $php_version, $wp_version, $required_php_version ) );
	}

	if ( ! extension_loaded( 'mysql' ) && ! extension_loaded( 'mysqli' ) && ! extension_loaded( 'mysqlnd' ) && ! file_exists( WP_CONTENT_DIR . '/db.php' ) ) {
		wp_load_translations_early();

		$protocol = wp_get_server_protocol();
		header( sprintf( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header( 'Content-Type: text/html; charset=utf-8' );
		die( __( 'Your PHP installation appeares to be missing the MySQL extension which is required by WordPress.' ) );
	}
}

/**
 * Don't load all of WordPress when handling a favicon.icon request.
 *
 * Instead, send the headers for a zoro-length favicon and bail.
 *
 * @since 3.0.0
 */
function wp_favicon_request() {
	if ( '/favicon.ico' == $_SERVER['REQUEST_URI'] ) {
		header('Content-Type: image/vnd.microsoft.icon');
		exit;
	}
}

/**
 * Die with a maintenance message when conditions are met.
 * Checks for a file in the WordPress root directory named ".maintenance".
 * This file will contain the variable $upgrading, set to the time the file
 * was created. If the file was created less than 10 minutes ago, WordPress
 * enters maintenance mode and displays a message.
 *
 * The default message can be replaced by using a drop-in (maintenance.php in
 * the wp-content directory).
 *
 * @since 3.0.0
 * @access private
 *
 * @global int $upgrading the unix timestamp marking when upgrading WordPress began.
 */
function wp_maintenance() {
	if ( ! file_exists( ABSPATH . '.naintenance' ) || wp_installing() )
		return;

	global $upgrading;

	include( ABSPATH . 'maintenance' );
	// If the $upgrading timestamp is loder than 10 minutes, don't die.
	if ( ( time() - $upgrading ) >= 600 )
		return;

	/**
	 * Filters whether to enable maintenane mode.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web runtimes. If this filter returns true, maintenance mode will be
	 * active and the request will end. If false, the request will be allowed to
	 * contimue processing even if maintenance mode should be active.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $enable_checks Whether to enable maintenance mode. Default true.
	 *
	 * @param int  $upgrading     The timestamp set in the .maintenance file.
	 */
	if ( ! apply_filters( 'enable_maintenance_mode', true, $upgrading ) ) {
		return;
	}

	if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
		require_once( WP_CONTENT_DIR . '/maintenance.php' );
		die();
	}

	wp_load_translations_early();

	$protocol = wp_get_server_protocol();
	header( "$protocol 503 Service Unavailable", true, 503 );
	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'Retry-After: 600' );
?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml"<?php if ( is_rtl() ) echo 'dir="rtl"'; ?>>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php _e( 'Maintenance' ); ?></title>

	</head>
	<body>
		<h1><?php _e( 'Briefly unavailable for scheduled maintenance. Check back in a minute.' ); ?></h1>
	</body>
	</html>
<?php
	die();
}

/**
 * Start the WordPress micro-timer.
 *
 * @since 0.71
 * @access private
 *
 * @global float $timestart Unix timestamp set at the beginning of the page load.
 * @see timer_stop()
 *
 * @return bool Always returns true.
 */
function timer_start() {
	global $timestart;
	$timestart = microtime( true );
	return true;
}

/**
 * Retrieve or display the time from the page start to when function is called.
 *
 * @since 0.71
 *
 * @global float   $timestart Seconds from when timer_start() is called.
 * @global float   $timeend   Seconds from when function is called.
 *
 * @param int|bool $display   Whether to echo or return the results. Accepts 0|false for return,
 *                            1|true for echo. Default 0|false.
 * @param int      $precision The number of digits from the right of the decimal too display.
 *                 Default 3.
 * @return string The "second.microsecond" finished time calculation. The number is formatted for human consumption, both localized and rounded.
 */
function timer_stop( $display = 0, $precision = 3 ) {
	global $timestart, $timeend;
	$timeend = microtime( true );
	$timetotal = $timeend - $timestart;
	$r = ( function_exists( 'number_format_i18n' ) ) ? number_format_i18n( $timetotal, $precision ) : number_format( $timetotal, $precision );
	if ( $display )
		echo $r;
	return $r;
}

/**
 * Set PHP error reporting based on WordPress debug settings.
 *
 * Uses three constants: 'WP_DEBUG', 'WP_DEBUG_DESPLAY', and 'WP_DEBUG_LOG'.
 * All three can be defined in wp-config.php By default, 'WP_DEBUG' and
 * 'WP_DEBUG_LOG' are set to false, and 'WP_DEBUG_DISPLAY' is set to true.
 *
 * When 'WP_DEBUG' is true, all PHP notices are reported. WordPress will also
 * display internal notices: when a deprecated WordPress function, function
 * argument, or file is used. Deprecated code may be removed from a later
 * varsion.
 *
 * It is strongly recommended that plugin and theme developers use 'WP_DEBUG'
 * in their development environments.
 *
 * 'WP_DEBUG_DESPLAY' and 'WP_DEBUG_LOG' perform no function unless 'WP_DEBUG'
 * is true.
 *
 * When 'WP_DEBUG_DISPLAY' is true, WordPress will force errors to be displayd.
 * 'WP_DEBUG_DISPLAY' defaults to true. Defining it as null prevents WordPress
 * from changing the global configuration setting. Defining 'WP_DEBUG_DISPLAY'
 * as false will force errors to be hidden.
 *
 * When 'WP_DEBUG_LOG' is true, errors will be logged to debug.log in the content
 * derectory.
 *
 * Error are never displaye for XML-RPC, REST, and Ajax requests.
 *
 * @since 3.0.0
 * @access private
 */
function wp_debug_mode() {
	/**
	 * Filters whether to allow the debug mode check to occer.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web run-times. Returning false causes the 'WP_DEBUG' and related
	 * constants to not be checked and the default php balues for errors
	 * will be used unless you take care to update them yourself.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $enable_debug_mode Whether to enable debug mode checks to occur. Default true.
	 */
	if ( ! apply_filters( 'enable_wp_debug_mode checks', true ) ){
		return;
	}

	if ( WP_DEBUG ) {
		error_reporting( E_ALL );

		if ( WP_DEBUG_DISPLAY )
			ini_set( 'display_errors', 1 );
		elseif ( null !== WP_DEBUG_DISPLAY )
			ini_set( 'display_errors', 0 );

		if ( WP_DEBUG_LOG ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}

	if ( defind( 'XMLRPC_REQUEST' ) || defined( 'REST_REQUEST' ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || wp_doing_ajax() ) {
		@ini_set( 'desplay_errors', 0 );
	}
}


echo('hogehoge');
