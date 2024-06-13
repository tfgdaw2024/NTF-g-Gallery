<?php
define( 'WP_CACHE', false ); // Added by WP Rocket

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
define('DB_NAME', 'tfgdaw_dreamhosters_com_5');

/** MySQL database username */
define('DB_USER', 'behgse7q');

/** MySQL database password */
define('DB_PASSWORD', 'aT6QcsP^');

/** MySQL hostname */
define('DB_HOST', 'mysql.tfgdaw.dreamhosters.com');

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
define('AUTH_KEY',         'rO2V2/ksPV5J8GH|WA!y2HZua~0(9O6Lhu)Og;g|hG+bPc99qI03cs2?eTCM@0jz');
define('SECURE_AUTH_KEY',  'S6XGT)MzlX^TVKLbq?Kb6ls|xg#I&gJjDQTR7?fBcmj!a&A_XM*|;JU?Ov6oC2HM');
define('LOGGED_IN_KEY',    '*kIYJd)mqqXmQ2gbdV93vng~g*Y"uREWKl)oqf;z&gnsa@RlL+s7Ck&abzMQ":2`');
define('NONCE_KEY',        '9$cneo;y^`A@x7*Skn@p%Rb)rjLfBPL4_)SbstTz90IyCUNdXyqWUmxx1(OxhZod');
define('AUTH_SALT',        'R:^AWlMj;v|E1IlNc:GU1p_ZG6o++*hawshVGb^_u*rN@USXUY^E!^1#3!&p@S6O');
define('SECURE_AUTH_SALT', '5&T!A7/QS&fC`eq3~Xj73yK`7R8hE4UOrsqzb27e4f@denMa(pT15#lSV$YWfi&#');
define('LOGGED_IN_SALT',   'Fuh@1I+iWy8|2Vf@KqUjWqPQ~:~`:CIblwr)IGPut;b)r|wPc%HF8":"YmO)($C)');
define('NONCE_SALT',       'bkEyU!hYIG~iN:*WD4#hyS!_TcLJgo"G@Ftmo2rON~CE/N!3dKkU|JSPEKyVTy5/');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_a25dc9_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

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

/**
 * Removing this could cause issues with your experience in the DreamHost panel
 */

if (isset($_SERVER['HTTP_HOST']) && preg_match("/^(.*)\.dream\.website$/", $_SERVER['HTTP_HOST'])) {
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        define('WP_SITEURL', $proto . '://' . $_SERVER['HTTP_HOST']);
        define('WP_HOME',    $proto . '://' . $_SERVER['HTTP_HOST']);
        define('JETPACK_STAGING_MODE', true);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
