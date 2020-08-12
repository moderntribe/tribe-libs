<?php declare( strict_types=1 );

namespace Tribe\Libs\Log;

use Psr\Log\LoggerInterface;

/**
 * Class Log_Actions
 *
 * Log something with WordPress actions
 *
 * do_action( Log_Actions::WARNING, 'Something is wrong!', [ 'additional data' ] );
 *
 * @package Tribe\Libs\Log
 */
class Log_Actions {

	const EMERGENCY = 'tribe/log/emergency';
	const ALERT     = 'tribe/log/alert';
	const CRITICAL  = 'tribe/log/critical';
	const ERROR     = 'tribe/log/error';
	const WARNING   = 'tribe/log/warning';
	const NOTICE    = 'tribe/log/notice';
	const INFO      = 'tribe/log/info';
	const DEBUG     = 'tribe/log/debug';

	/**
	 * The logger instance
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * Log_Actions constructor.
	 *
	 * @param   LoggerInterface  $logger
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Initialize the actions
	 */
	public function init() {
		add_action( self::EMERGENCY, [ $this->logger, 'emergency' ], 10, 2 );
		add_action( self::ALERT, [ $this->logger, 'alert' ], 10, 2 );
		add_action( self::CRITICAL, [ $this->logger, 'critical' ], 10, 2 );
		add_action( self::ERROR, [ $this->logger, 'error' ], 10, 2 );
		add_action( self::WARNING, [ $this->logger, 'warning' ], 10, 2 );
		add_action( self::NOTICE, [ $this->logger, 'notice' ], 10, 2 );
		add_action( self::INFO, [ $this->logger, 'info' ], 10, 2 );
		add_action( self::DEBUG, [ $this->logger, 'debug' ], 10, 2 );
	}

}
