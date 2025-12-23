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
define( 'DB_NAME', 'arrow8ei_wpcure' );

/** Database username */
define( 'DB_USER', 'arrow8ei_wpcure' );

/** Database password */
define( 'DB_PASSWORD', '458p3wK.S-' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         'xsnqekgodmrxzfz5kah5qhmlcwdvrba2ouddhsjwwqltnfcdbng9mdnrz28rczcg' );
define( 'SECURE_AUTH_KEY',  'yfnnlxteamrwbxfgxzhuxa8cfqdj5m0kn9hgwuqxebwvxnzhxraiiubzpxnyqlr6' );
define( 'LOGGED_IN_KEY',    '9ty0zzbzoswmllx95on6gpr1je7roywz9nhlwcatax89cso7kus8tsjwcfpk9sz8' );
define( 'NONCE_KEY',        'goqvauy0ud4qbwjzk8yfddhnndyedec8ub1vlawnfzqybgkzec0cjak9brzgvooj' );
define( 'AUTH_SALT',        'w6xvsvshdmiit1n2lxl6pfu7n7rbgft1x8gdlsymsb49qivanqm3qrygwxifbvym' );
define( 'SECURE_AUTH_SALT', 'd1a7wtyjizur4cwhoov92gitswnckdsyhwhe6x83nkmyo4zl5nwaqi2ygzbystvq' );
define( 'LOGGED_IN_SALT',   'kln04wyhzxvhaahwzbws20ytipxysq3mhggo6dris54vsvypbhixf2rirvl6qskk' );
define( 'NONCE_SALT',       'ypnzyv1cppq6sxesxpsdprmqijcvhxwkn7m2z6e4jucs1ushgtpml2pmdvwr6eac' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpcure_';

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
