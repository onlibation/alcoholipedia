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
//define('WP_CACHE', true); //Added by WP-Cache Manager
define('DB_NAME', 'alcoholipedia');#wp_prod');#colora17_altest');

/** MySQL database username */
define('DB_USER', 'root');#wp_al');#colora17_wptest');

/** MySQL database password */
define('DB_PASSWORD', 'root');#Determined1!');#Wakeboard8');

/** MySQL hostname */
define('DB_HOST', 'localhost');#alcoholipediacom.ipagemysql.com');#localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_SITEURL','/');
define('WP_HOME','/');
// define('RELOCATE',true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Z{GW8/$fc=nRU[uiT#uy+</lv1WfGtaNgN`31I{|iUn$W-Kw_Hp3j[F*Q;`t/=^-');
define('SECURE_AUTH_KEY',  '--uy[Sd-ofTgq)6bb3 M^@dTi?d?J+wch 2;l.]u%h{6{C:*G+:s:sE:qE6O;z]a');
define('LOGGED_IN_KEY',    '_#w:4bRdS-@1JTFE/?&zX?3[XCcs&:&(:5-+I!}vRw?R5#lxUhi$L=*ihm>j[=v9');
define('NONCE_KEY',        '4[?tZI|^GKQ?xzA_SE&Q/?,+H?fbk{@G;L1u(RxQ{0sn!W1>Ui}!@(9c]2deyv1]');
define('AUTH_SALT',        'G,{SC:a  r]68KBa7N*@GC4:+[J-AC|: !nOi ,?A2;)nh-~QTu$lIC6Tq-,0R?:');
define('SECURE_AUTH_SALT', 'pYvwMjo(L0#+uTn<cfBstS^qJ7]RN0=4cACyf)P1Kk)+D/$oft%ufTp.Yk(^MN- ');
define('LOGGED_IN_SALT',   'KfX(6wyRorBlf|pZk,Jv7Uj:7c4he0.H-#C%XhD-=*+]0+}oP,Jty]4z1 Z5T?,b');
define('NONCE_SALT',       'Y]#x>L{%`.MjX^|jxTi]CKbKz=#+*ub ~ P1JyyO:WURNcjkLF)C2^<CKgB07kHO');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wqqzc_';

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

@ini_set('log_errors','On');
@ini_set('display_errors','Off');
@ini_set('error_log','/hermes/bosweb25b/b708/ipg.alcoholipediacom/logs/php_error.log');
/* That's all, stop editing! Happy blogging. */

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
