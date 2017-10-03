<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the wp-config.php file. The wp-config.php
 * file will then load the wp-settings.php file, which
 * will then set up the WordPress environment.
 *
 * If the wp-config.php file is not found then an error
 * will be displayd asking the visitor to set up the
 * wp-config.php file.
 *
 * Will alse search for wp-config.php in WordPress' parent
 * directory to allow the WordPress directory to remain
 * untouched.
 *
 *@package Wordpress
 */

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH') ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );

/*
 * If wp-config.php exists in the WordPress root, or if it exists in the root and wp-settings.php
 * doesn't, load wp-config.php. The secondary check for wp-settings.php has the added benefit
 * of avoiding cases where the current directory is a nested installation, e.q. / is WordPress(a)
 * and /blog/ is WordPress(b).
 *
 * If neither set of conditions is true, initiate loading the setup process.
 */
if ( file_exists( ABSPATH . 'wp-config.php') ) {

    /** The config file resides in ABSPATH */
    require_once( ABSPATH . 'wp-config.php' );

} elseif ( @file_exists( dirname( ABSPATH ) . 'wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {

    /** The config file resides one lavel above ABSPATH but is not part of another install */
    // requre_once( firname( ABSPATH ) . 'wp-config.php' );
} else {

}
