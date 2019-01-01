<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'smarttask2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '`HS,IP~j-!NCSrq?)(OpzofxWn} &:Zo#j6{0hWw(ZQes6_xw;Sq@_w8J[l=?}__');
define('SECURE_AUTH_KEY',  '}j9Be9AZhe$7#R6X(msVE{+B#)r:giG`T}<pJ:CY0!EqhoH#w.zjAj(zHOxz_g-s');
define('LOGGED_IN_KEY',    '}BtHn_R;i?}]2deHd06B?IB;$Y,;_=25V>Az)A-$bWcSR*6-fUA@Q$$QG(Gm]$q2');
define('NONCE_KEY',        'Y`,f2>~U,viXMu7zOGx&7^+5q=Hgj[oTd*h PfA.PnIFp.}cKM>_l.P2P5NMQDzN');
define('AUTH_SALT',        'o_oAC|8a:;/2GRk>$~~dg)_v1a=hZYw68U1C=Y]S~rB1uOmRl5p=9[$P.Q[8t4.N');
define('SECURE_AUTH_SALT', 'wB]ilJ)V>ue~$$FSbPDjeM?B|w#3,{~9V4FEtQu,Ti5j$f3`m~~*WX{vAvBo>9#&');
define('LOGGED_IN_SALT',   'ri-%I{4/{uKkoef#*d1=q|6UN4Lf$Y$06 ,&WJ]-d^`(H#^!WtPmniE{`X(Ho?P]');
define('NONCE_SALT',       'O7Exu9+u]UzR#853ZMVr?^oG~^O yqrE2#^]2SwO4;8aRTHXpsuz^Xm>M#/}h t.');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
