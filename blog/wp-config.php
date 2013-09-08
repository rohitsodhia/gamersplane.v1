<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'gamersplane');

/** MySQL database username */
define('DB_USER', 'gamersplane');

/** MySQL database password */
define('DB_PASSWORD', 'Ep2NXZ0Atv6MThNtsa2h');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '>ahJjthCKt/No{ d ;DTLLXHY8uaT&_>]sS:QT(WMJxPw&Cn&=/QR$j,}Dqifm9}');
define('SECURE_AUTH_KEY',  '<#.XRb*}MPWQAD?H-45qq]q+qMC&JqU`OyBU.>V&H|y,=VcOK1i#7D}Z+gP&UG2^');
define('LOGGED_IN_KEY',    'DsW_X?Re10/}.zfn,T.<xkfE/s&X*MHxq?2(8jbBqTO$f_?!tsaKYGmx})*d!FdR');
define('NONCE_KEY',        'WByR}td#{p*ET_{sI[+h:zn*XH{-b~XyLiRPj=&m}R}iZ,lrq0raht98Q/rda/ip');
define('AUTH_SALT',        '/cnk[!l_04 WTE|?wd]=~w @DNc5q6F7-C}K$)0A#I5}0&S**7z#CUUzF]N=%(M,');
define('SECURE_AUTH_SALT', 'cXb,t]+wW?xzSGhDU(3.z U/h ^}*HC[gzJ&V_~IF?A#idE/D`TvI9hz7oYW G&C');
define('LOGGED_IN_SALT',   '08zc<wd,dd:9P?,},MjC[)px/3)-+A!KGOY:?0#+YxJ210xqZ0oqEABr6d:P==.@');
define('NONCE_SALT',       'rdq!XTgpDq?O.5ZC.~?|yv]SY[k#6t;dG[ZoR7=w9E^S+1E1TbeoJ%1X3i*]w$W[');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
