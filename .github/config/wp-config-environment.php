<?php
/**
 * Base environment configuration, loaded for all test environments
 */

function tribe_isSSL() {
	return ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' );
}

function tribe_getenv( $name, $default = null ) {
	$env = getenv( $name );
	if ( $env === false ) {
		return $default;
	}

	$env_str = strtolower( trim( $env ) );
	if ( $env_str === 'false' || $env_str === 'true' ) {
		return filter_var( $env_str, FILTER_VALIDATE_BOOLEAN );
	}

	if ( is_numeric( $env ) ) {
		return ( $env - 0 );
	}

	return $env;
}

require_once __DIR__ . '/vendor/autoload.php';

if ( file_exists( __DIR__ . '/.env' ) ) {
	$dotenv = Dotenv\Dotenv::create( __DIR__ );
	$dotenv->load();
}

// ==============================================================
// Assign default constant values
// ==============================================================

if ( ! isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) {
	$_SERVER['HTTP_X_FORWARDED_PROTO'] = '';
}
if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
	$_SERVER['HTTP_HOST'] = 'local-cli';
}

if ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS']       = 'on';
	$_SERVER['SERVER_PORT'] = 443;
}

// ==============================================================
// If a Load Balancer or Proxy is used, X-Forwarded-For HTTP Header to get the users real IP address
// ==============================================================

if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	$http_x_headers = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );

	$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
}

$config_defaults = [

	// Paths
	'ABSPATH'                        => tribe_getenv( 'ABSPATH', __DIR__ . '/wordpress/' ),

	// DB settings
	'DB_CHARSET'                     => 'utf8',
	'DB_COLLATE'                     => '',

	// Language
	'WPLANG'                         => tribe_getenv( 'WPLANG', '' ),

	// Performance
	'WP_CACHE'                       => tribe_getenv( 'WP_CACHE', false ),
	'DISABLE_WP_CRON'                => tribe_getenv( 'DISABLE_WP_CRON', true ),
	'WP_MEMORY_LIMIT'                => tribe_getenv( 'WP_MEMORY_LIMIT', '96M' ),
	'WP_MAX_MEMORY_LIMIT'            => tribe_getenv( 'WP_MAX_MEMORY_LIMIT', '256M' ),
	'EMPTY_TRASH_DAYS'               => tribe_getenv( 'EMPTY_TRASH_DAYS', 7 ),
	'WP_APC_KEY_SALT'                => tribe_getenv( 'WP_APC_KEY_SALT', 'tribe' ),
	'WP_MEMCACHED_KEY_SALT'          => tribe_getenv( 'WP_MEMCACHED_KEY_SALT', 'tribe' ),

	// Debug
	'WP_DEBUG'                       => tribe_getenv( 'WP_DEBUG', true ),
	'WP_DEBUG_LOG'                   => tribe_getenv( 'WP_DEBUG_LOG', true ),
	'WP_DEBUG_DISPLAY'               => tribe_getenv( 'WP_DEBUG_DISPLAY', true ),
	'SAVEQUERIES'                    => tribe_getenv( 'SAVEQUERIES', true ),
	'SCRIPT_DEBUG'                   => tribe_getenv( 'SCRIPT_DEBUG', false ),
	'CONCATENATE_SCRIPTS'            => tribe_getenv( 'CONCATENATE_SCRIPTS', false ),
	'COMPRESS_SCRIPTS'               => tribe_getenv( 'COMPRESS_SCRIPTS', false ),
	'COMPRESS_CSS'                   => tribe_getenv( 'COMPRESS_CSS', false ),
	'WP_DISABLE_FATAL_ERROR_HANDLER' => tribe_getenv( 'WP_DISABLE_FATAL_ERROR_HANDLER', true ),

	// Miscellaneous
	'WP_POST_REVISIONS'              => tribe_getenv( 'WP_POST_REVISIONS', true ),
	'WP_DEFAULT_THEME'               => tribe_getenv( 'WP_DEFAULT_THEME', 'core' ),
];

// ==============================================================
// Use defaults array to define constants where applicable
// ==============================================================

foreach ( $config_defaults AS $config_default_key => $config_default_value ) {
	if ( ! defined( $config_default_key ) ) {
		define( $config_default_key, $config_default_value );
	}
}

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================

if ( empty( $table_prefix ) ) {
	$table_prefix = tribe_getenv( 'DB_TABLE_PREFIX', 'tribe_' );
}
