<?php declare(strict_types=1);

namespace Tribe\Libs\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Tribe\Libs\Tests\Test_Case;

final class LogTest extends Test_Case {

	/**
	 * The path to log to during tests.
	 */
	protected string $log_file;

	protected function setUp(): void {
		parent::setUp();

		$this->log_file = codecept_output_dir( 'sq1.log' );
		$handler        = new StreamHandler( $this->log_file, MonoLogger::DEBUG );
		$logger         = new Logger( new MonoLogger( 'square-one-tests', [ $handler ] ) );

		( new Log_Actions( $logger ) )->init();
	}

	protected function tearDown(): void {
		parent::tearDown();

		@unlink( $this->log_file );
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

		$this->assertStringContainsString( 'square-one-tests.EMERGENCY: emergency ["emergency"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.ALERT: alert ["alert"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.CRITICAL: critical ["critical"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.ERROR: error ["error"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.WARNING: warning ["warning"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.NOTICE: notice ["notice"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.INFO: info ["info"] []', $log );
		$this->assertStringContainsString( 'square-one-tests.DEBUG: debug ["debug"] []', $log );
	}

}
