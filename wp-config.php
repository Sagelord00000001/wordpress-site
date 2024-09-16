<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'real_estate' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ugwl,oSfW_`LyKHo*S>y#z9+fhQt:9b/T%vdsmS^RDb2zd1{;0s*d=.phncO6kv9' );
define( 'SECURE_AUTH_KEY',  ' C>!!0l39|a]@G`vcB(Cd6>* WjNuHD)mTLslc8jJAQf-4:ktW!&C1#2[S9g!RBK' );
define( 'LOGGED_IN_KEY',    '|co,Y_@L!F+GKU?ftst=&~89*!R,gM5XS{TyiY9EPQy0{`,ste+qD2n*B0E+a(.l' );
define( 'NONCE_KEY',        '~(rjWmc1[GngNqyj;[z]<bmgSo=]-Ry`H&[A+%TV9qK2oFH^?x]X&~3$%Ot2{[vJ' );
define( 'AUTH_SALT',        'AK8o- X;CV|Sl73;gUhcWUdRD2T- y^0?A*%U;c/r>n-Y6`!~390~Nm_ozp#b?B9' );
define( 'SECURE_AUTH_SALT', '3`pzk|s:EcYmn<8/,v;a1JdX~~M&:y3`[5/DLH.%.0ksUIW!B9=9/`V,8j^`N_L#' );
define( 'LOGGED_IN_SALT',   '%gk^%N[P.is{)k|W5jy>Yy><h+@NyOUSB1nh1GKD>V}+HX4]dL|KU;%=lKR#|G.z' );
define( 'NONCE_SALT',       '~*/S/m;w${E|/AHs,BvX7,qXRKr~0--8fahJs =.)FRT(YR:PhC#];zzsn1nw.TQ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_real_estate';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
