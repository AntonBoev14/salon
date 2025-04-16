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
define( 'DB_NAME', 'MySite15' );

/** Database username */
define( 'DB_USER', 'admin_' );

/** Database password */
define( 'DB_PASSWORD', 'anton14042006!' );

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
define( 'AUTH_KEY',         '!d!RIM2nlF[kCaKXn//Xi`.~`s>+xajOUUo;ONl6(EyN{,z`scu|r4H-pwAJl1/r' );
define( 'SECURE_AUTH_KEY',  'ER)K7&r].xhT,mUuCoxOI<Z%+j0akH`[g%Jq* 1y#x>;F?)xuq-^~Is^PXT.%~1r' );
define( 'LOGGED_IN_KEY',    '_j_ot-=i&A.}eJM8Ju;6L[HoT+>~s_pVnD8XTmZ>+6#)-o.AO+7~4ZO1U}wLRJ1L' );
define( 'NONCE_KEY',        '.SI*>Ut0l,+WI?<PHq#j}x{Dj0+gvE+^d@J>XKg<D7 X%&=}?bn=%+d[RK(R,sM%' );
define( 'AUTH_SALT',        ':3vd>w:*)rPw>%.r}SM0/jr{1tQVcMw|1C1pwxH(w,zAw6yYP<Ju ~]<8lB}9i!*' );
define( 'SECURE_AUTH_SALT', 'G^jki{K]bf8k<fT1r:*v|l>l-~n*,XYFAF.ys/7(?v~J:SF:/b1Q=ON5RG{B|-)Q' );
define( 'LOGGED_IN_SALT',   '7Z|>z`[TXjpqR+d}m:@FClm1U_6fhK.`&7kRW`F_g-K5;DA.riG{aYT;FhCN7nE|' );
define( 'NONCE_SALT',       ':kpKq(16emRjM}B~Gal)gM7~/B4pN*BC4hOA+na<J3J61taR%32PYW!>YY7f(wXb' );

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
