<?php



/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'mondodir_wp149new' );
/** MySQL database username */
define( 'DB_USER', 'mondodir_wp149new' );
/** MySQL database password */
define( 'DB_PASSWORD', 'tPP&=lM?Mk2B' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/** JWT Authentication for WP REST API. Don't change this!*/
        define('JWT_AUTH_SECRET_KEY', 'xsFCAhOyYD1rD9zBtuSpEYAt6cFfKNdTiCDJoJ4VgcwzF9mJTaCPqlfjldrC');
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
define( 'AUTH_KEY',         'xdzesznsrqytw8p2ezybsws1xngj1zfyksb9dwze8wutu6ws7uprfsqz0uun7lya' );
define( 'SECURE_AUTH_KEY',  'fqnzaabchaxvwxcke1fiezz4x5aqblcrijbtzlawlhifmqwtpsrhaho4oqpceff7' );
define( 'LOGGED_IN_KEY',    '1iia3pyggqgp5jbuadkymiznxpqqvekcyqk1jwm5luimfsvnjtt3ftfbxqxiwejq' );
define( 'NONCE_KEY',        'fpgng1uv9rmukx0tzej2aaxrpz9vd1ej5zrrlhwtzgqr0evff2qlhm9wamewyp5p' );
define( 'AUTH_SALT',        '0dx1vakcsrz1accpzfgihllqpy3elctc9nr5aal93ne3wco2uphr6a4wcxpj42fm' );
define( 'SECURE_AUTH_SALT', '54ytmui7hibqhr0lzctyabom161afcdd9i3yvsqdhbzzqpi6830rwasvyjg1nz3u' );
define( 'LOGGED_IN_SALT',   'cdp0vvoazqpittkzkgyro4lqrjdcw7obht8khrxbxbehiyrqik7cicioo8fegmrl' );
define( 'NONCE_SALT',       'hok0uju7jzvciid3qapanpujnbavygfeziiblkfyhbbjnyasjig0qq3in2ruuapi' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wppv_';
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
/* Add any custom values between this line and the "stop editing" line. */
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_DEBUG', false );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
