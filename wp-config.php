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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_healthray');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         '5)`=LjZm=>-iGp76>0?fGx3^WWkgVh@i_qg-Xz`yv9>gj<snks&}x9Lz4;V6Y/YS');
define('SECURE_AUTH_KEY',  '~_fu%`QP9}n#uSR!OS2<U,a48ss($8X6*>[m6w!sW1QX^KQa5=2  L0X7o` z-5B');
define('LOGGED_IN_KEY',    'Lyh^$bW5Q>k_=Y^Ws^II%0(tQB$0zYU]L^j+OGv0euY<K23a4XzcF<:lP@xv|,2;');
define('NONCE_KEY',        'AnDD>nJY|,37t3p/ls7Sn+|OCvHH*Xl5& $r:gykom2cpwuD2G(8vws|Tl}hP-[V');
define('AUTH_SALT',        'LMcWJj@^b6OYW@[<*5jA{-O|FJ&GLkPOQUKn^1L5Aeq-S>?<{)#w}2k>Af )5w0h');
define('SECURE_AUTH_SALT', 'GNgmnn:yyQ:<YSeKH)bEwaRmg(tdGF/a:Lc?]>6Opv?w}RDxdHyvk[wK[{yns$pN');
define('LOGGED_IN_SALT',   '+[-#;(WL?/jHLl1xw9{mQ:unLw6<{hG[a3x%D:[!*Pay}={.b:e2NZiWy! ={gjT');
define('NONCE_SALT',       'Y5l[`ofHC?T6{]YSXl-~J|:V3cQ<Iwf$=#yNORqyiKXw!&x;Ce)N_8W+^}*s/!BU');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
