<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


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
define('FS_METHOD','direct');
define( 'DB_NAME', 'seth_db' );

/** MySQL database username */
define( 'DB_USER', 'seth_user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'sethsdbpassword' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '?KJU0gUq5E?;+M~s>oa5i?e-I8)0%>)iwzC0rT^c,e ArF22lpV{G5(d<$z}TI%f' );
define( 'SECURE_AUTH_KEY',   'Gw{S0E5fw>eYIqi4UcswQE6Q88^9n#6A1jQQS]*@GjCISyVS8XIYR@EaC_i%Lm<F' );
define( 'LOGGED_IN_KEY',     'zFMs!=@jW%]X{q$1uIy!i)d?DytF,Wm (~N`#K3WVf[C#Wq3/8mA,?Su`iYeqWmp' );
define( 'NONCE_KEY',         'z><MTl@Q<O>wCPub;hlN]e^6&e@8$9qmkcM2(P;|h{Ip9{2}:GkCw<=C@lR6[q~x' );
define( 'AUTH_SALT',         'aZHAg5OD&w6|bGgRwRY/FKv,d2>PJB3JAW^gX$;3Hw~RZ}k9LDAK{GcMkq9lV>$6' );
define( 'SECURE_AUTH_SALT',  '$hS*Z|a0b=]u(P3qJs2L$Qo;< W>fvO@5gGg)CNU?MZ)Qauw*~-3-iM(10<r:86j' );
define( 'LOGGED_IN_SALT',    'YRg:(?lh]VLZUgYS/BpK|wyAUW{Pxn/Q/rurv,aNnWl@s7FC;1m38>l(%y$alvnT' );
define( 'NONCE_SALT',        ']}fbPd|ZBU$mMP.NPw^JIjz&a*S8q5.vPZtOlgpZbi}pjbtTGGeWSi=k=meCS6}Z' );
define( 'WP_CACHE_KEY_SALT', 'GeHs0tv!aFI*e[l$R3C+7Jzp`#76SSwMRm%v`FBXnk9a(xhBHLP+IeByS7qfTq/7' );
define( 'DISALLOW_FILE_EDIT', true );
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
