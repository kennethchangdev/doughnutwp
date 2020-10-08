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
define( 'DB_NAME', 'rogue_db' );

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
define( 'AUTH_KEY',         'MSS5S(j|ff|qoc]lQNkOB{TQ8irrYbl(5j:d%8{}[EaKtAo:EHBrirn~7R+C/[LP' );
define( 'SECURE_AUTH_KEY',  ']j:]6Q/V#%d%;>r%Nb.takD${f|9D1d]@piQf9`o~b_KC2iD>bwJy].8!sOYj3iM' );
define( 'LOGGED_IN_KEY',    'ug>t$rX&!Nx3kX<`jV:QdUI|Oz]c,E[FEU`$ZmlRe*o^{t{gXPr:a&1XamxX|,W!' );
define( 'NONCE_KEY',        'N0%:&d KGEoMm?]D@FwkkV^roH)5nEf_)]_7>ffoZ*YZxgK<B#u!Z{vk+{2(XU_w' );
define( 'AUTH_SALT',        'qbbYAuu<(t)5 9c>%DI:jbDD]q5MQ-VS:/a8&NizG$f;@}Vc<94E(aU5jWmD.:Qv' );
define( 'SECURE_AUTH_SALT', 'K-;Xiq)]<{I.9z79%[jtX;Y79%$*GPmQFP)0P-;~NN8=J`wDUp.^fRR37SLYZnY~' );
define( 'LOGGED_IN_SALT',   '{a@AdU]/Ot_1&M9q6y8jj*&r|#UzVGL?5mBxG[%<:l]W9b xsc9-]Hha<@*ned%g' );
define( 'NONCE_SALT',       '- ^{M*FUs+.,.lN*IG@7$6I`[H#weo_beZHM]S`aAcik(!UBF2Mx;v<-~es=sa>k' );

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
