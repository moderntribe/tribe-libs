# Log

The Log package is a [psr/log](https://github.com/php-fig/log) compatible logger implementation using WordPress actions to log messages using [Monolog](https://github.com/Seldaek/monolog) behind the scenes.

As this uses WordPress actions, it doesn't couple any logger to your code, and simply won't fire any log
messages if no logger is configured.

## Quick Start

Register `Log_Definer.php` and `Log_Subscriber.php` in Square One Core.php. Call the Log Actions below
in your code.

## Log Action Definition

```php
/**
 * @param string $logger_hook Use on of the constants defined in Log_Actions.
 * @param string $message The message to log.
 * @param mixed[] $additional_data_to_log Additional data to log. 
 */
do_action( string $logger_hook, string $message, array $additional_data_to_log );
```

## Example Usage

```php
do_action( \Tribe\Libs\Log\Log_Actions::INFO, 'Starting connection...' );

// ... more logic

if ( $status !== 200 ) {
    do_action( \Tribe\Libs\Log\Log_Actions::ERROR, 'Invalid status returned from API.', [ 'status' => $status ] );
}

```
## Log Action List

```php
do_action( Log_Actions::EMERGENCY, 'This is an emergency!', [ 'emergency' ] );
do_action( Log_Actions::ALERT, 'This is an alert.', [ 'alert' ] );
do_action( Log_Actions::CRITICAL, 'This is a critical!', [ 'critical' ] );
do_action( Log_Actions::ERROR, 'This is an error.', [ 'error' ] );
do_action( Log_Actions::WARNING, 'This is a warning.', [ 'warning' ] );
do_action( Log_Actions::NOTICE, 'This is a notice.', [ 'notice' ] );
do_action( Log_Actions::INFO, 'This is an info message.', [ 'info' ] );
do_action( Log_Actions::DEBUG, 'This is a debug message.', [ 'debug' ] );
```

## Configuration

Logs are written to the `/wp-content` folder, in a file called `square-one-<today's date>.log`. 

> **NOTE** be cautious of what is being written to log files and where they are stored to ensure no sensitive information is leaked if used on a production server.

**Change Log Path and Name**

```php
add_filter( 'tribe/log/path', static fn ( $path ) => '/server/path/to/my.log' );
```

**Change the Default Log Level**

The default level is `DEBUG`, you can pass a [LogLevel Constant](https://github.com/php-fig/log/blob/master/src/LogLevel.php) in a define to configure a different level.

in wp-config.php

```php
define( 'TRIBE_LOG_LEVEL', \Psr\Log\LogLevel::EMERGENCY );
```

**Change the Log Channel**

The log channel is a descriptive name attached to all log message. The default is `square-one`. Change with:

```php
add_filter( 'tribe/log/channel', static fn ( $channel ) => 'my-client-name' );
```

## WP CLI Logging

Custom WP CLI commands can also use this logging system. 

In your wp-config.php

```php
// Enable CLI Logging
define( 'TRIBE_LOG_CLI', true );
```

Then in your command code, instead of calling `WP_CLI::warning( 'This is a warning message' );` you can call
`do_action( Log_Actions::WARNING, 'This is a warning message' );`.

This is powered by the [Monolog WP-CLI Handler](https://github.com/mhcg/monolog-wp-cli). Just be mindful that WP_CLI does not support all log levels.

## Adding Monolog Handlers

Monolog supports many [Handlers](https://github.com/Seldaek/monolog/blob/main/doc/02-handlers-formatters-processors.md#handlers), in order to send a log message to a destination. Monolog can send a log message to multiple destinations at once.

Create a custom definer in your Square One application and add the configured Handler class to `Log_Definer::HANDLERS`.

```php
// ... namespace, use statements etc...

class My_Log_Definer implements Definer_Interface {
    
    public function define(): array {
        return [
            // Append additional log handlers for Monolog
            \Tribe\Libs\Log\Log_Definer::HANDLERS => DI\add( [
                SendGridHandle::class,
            ] ),
        ];
    }
    
}
```
