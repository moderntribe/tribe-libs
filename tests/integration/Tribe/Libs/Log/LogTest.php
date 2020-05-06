<?php

namespace Tribe\Libs\Log;

use Codeception\TestCase\WPTestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

class LogTest extends WPTestCase {

	protected $log_file;

	public function setUp(): void {
		parent::setUp();

		$this->log_file = codecept_output_dir( 'sq1.log' );
		$handler        = new StreamHandler( $this->log_file, MonoLogger::DEBUG );
		$logger         = new Logger( $handler, 'square-one' );

		( new Log_Actions( $logger ) )->init();

	}

	public function tearDown(): void {
		parent::tearDown();
		unlink( $this->log_file );
	}

	public function test_it_can_log_to_a_file(): void {
		do_action( Log_Actions::EMERGENCY, 'emergency', [ 'emergency' ] );
		do_action( Log_Actions::ALERT, 'alert', [ 'alert' ] );
		do_action( Log_Actions::CRITICAL, 'critical', [ 'critical' ] );
		do_action( Log_Actions::ERROR, 'error', [ 'error' ] );
		do_action( Log_Actions::WARNING, 'warning', [ 'warning' ] );
		do_action( Log_Actions::NOTICE, 'notice', [ 'notice' ] );
		do_action( Log_Actions::INFO, 'info', [ 'info' ] );
		do_action( Log_Actions::DEBUG, 'debug', [ 'debug' ] );

		$log = file_get_contents( $this->log_file );

		$this->assertStringContainsString( 'square-one.EMERGENCY: emergency ["emergency"] []', $log );
		$this->assertStringContainsString( 'square-one.ALERT: alert ["alert"] []', $log );
		$this->assertStringContainsString( 'square-one.CRITICAL: critical ["critical"] []', $log );
		$this->assertStringContainsString( 'square-one.ERROR: error ["error"] []', $log );
		$this->assertStringContainsString( 'square-one.WARNING: warning ["warning"] []', $log );
		$this->assertStringContainsString( 'square-one.NOTICE: notice ["notice"] []', $log );
		$this->assertStringContainsString( 'square-one.INFO: info ["info"] []', $log );
		$this->assertStringContainsString( 'square-one.DEBUG: debug ["debug"] []', $log );
	}

}
