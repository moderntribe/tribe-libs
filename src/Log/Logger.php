<?php declare(strict_types=1);

namespace Tribe\Libs\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as MonoLogger;
use Psr\Log\LoggerInterface;

/**
 * PSR Logger using MonoLogger.
 */
class Logger implements LoggerInterface {

	/**
	 * The MonoLogger Instance
	 *
	 * @var MonoLogger
	 */
	protected $log;

	/**
	 * Logger constructor.
	 *
	 * @param  \Monolog\Logger  $log
	 */
	public function __construct( MonoLogger $log ) {
		$this->log = $log;
	}

	/**
	 * Adds additional push handlers
	 *
	 * @param   AbstractProcessingHandler  $handler
	 *
	 * @return LoggerInterface
	 */
	public function add_push_handler( AbstractProcessingHandler $handler ): LoggerInterface {
		return $this->log->pushHandler( $handler );
	}

	/**
	 * System is unusable.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function emergency( $message, array $context = [] ) {
		$this->log->emergency( $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function alert( $message, array $context = [] ) {
		$this->log->alert( $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function critical( $message, array $context = [] ) {
		$this->log->critical( $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function error( $message, array $context = [] ) {
		$this->log->error( $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function warning( $message, array $context = [] ) {
		$this->log->warning( $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function notice( $message, array $context = [] ) {
		$this->log->notice( $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function info( $message, array $context = [] ) {
		$this->log->info( $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 */
	public function debug( $message, array $context = [] ) {
		$this->log->debug( $message, $context );
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param   mixed   $level
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return void
	 *
	 * @throws \Psr\Log\InvalidArgumentException
	 */
	public function log( $level, $message, array $context = [] ) {
		$this->log->log( $level, $message, $context );
	}

}
