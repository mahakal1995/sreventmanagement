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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sreventmanagement' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'fVcfm84GjZTiPWa0Ov}uPcIon@W0;@/CXc4Lg&E=|; WmZlYw*DwUz73cmPFUj,i' );
define( 'SECURE_AUTH_KEY',  'pP^V!16]DsM*(Q/_QT9PI4bD-ssN$DweqX@n@MP4/W{:+x/b/ En}{vL fX( Q)b' );
define( 'LOGGED_IN_KEY',    'FS[u>;m,bjU%>4#|}#o,<y.<?#}%3UC`ZZG -m.)z,0+z%/`X(8l]Dft/5[`;dXO' );
define( 'NONCE_KEY',        't:+P[m?*yq&[Zg&oe8A2&>hU9/!c> dUHo`vC$F5buy6zCn3*xjgUc&K{(zD&&5 ' );
define( 'AUTH_SALT',        ',VSZAO{9q71lqtDYQ]^P>@Co~<,A(483WqnBLIc>AH0%p20~:xV8[/1534I[<a?l' );
define( 'SECURE_AUTH_SALT', 't;B1VF0&^49a]`ot^jYTe>F(?jXTGlGH[H,f%j}2.dmvvKcKFIKbq@r+P}<pPrC#' );
define( 'LOGGED_IN_SALT',   'xD;Z*2kd-+FDF6xz;4zRp,O|&~PmYK`7k!j,BSCD+QMB/.gy3!>VzxN`7N2-0F}_' );
define( 'NONCE_SALT',       '-h&~na^OfK 9,P{:8`CAAne+e-Gb8HQL}N-M5cZ)2Hjad(=2G(W305?w@gscR%^;' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
