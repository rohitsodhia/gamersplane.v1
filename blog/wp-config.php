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
define('AUTH_KEY',         '<Y-v0HCDT()8->L@`$43;*(oA5[RC(w]>pW9iMO{?,u1`a<{FK$Q e-4C&{UK<e0');
define('SECURE_AUTH_KEY',  'pnIr>G+YvI3y-/yK.c3J,l#$y9ZIX3r#>:J!.v{OdGz=DFo3S3F~sH0 R.[[D9IA');
define('LOGGED_IN_KEY',    'm) 0Bz#K%u%5!4#o.)<(t=?BpGF=K`_$/ARhOa5,@]hM;tnP;0i!{PR:#$MEHqNS');
define('NONCE_KEY',        '17JHxp)vy9f!m=]fFD2p@%N6:sQMlQrY`nY:50avH)c.GQ? /fP 8T6%%D*@dZ[*');
define('AUTH_SALT',        'P,Les=6+dW4pEM3iG}V|!#VT[dhs:R~uuLNW&nxVP ,73!`19XWtF~6aR )-L5{!');
define('SECURE_AUTH_SALT', 'KGjdR8gsJcJ861&}Po)3{yVTb5KmsL4:c(]7{1Of0OKvF~~1QJ`KS>J&}Av3{4va');
define('LOGGED_IN_SALT',   '`NSzKOu1?u#g$3/e{1SsMQ0>xc1fymgtkkW|N5@c9?lUXm;!RprbV*X9s&B0xPQI');
define('NONCE_SALT',       're|wR^KyPr4EGAIqzpzzZ3N`WCH[*#s=o5&94,xJaXG3v:w$?Y}v-IQZHzI}<umd');

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

/* Multisite */
define('SUNRISE', TRUE);
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'gamersplane.local');
define('PATH_CURRENT_SITE', '/blog/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1)
;
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
