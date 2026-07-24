<?php declare(strict_types=1);

namespace Tribe\Libs\Log;

use Monolog\Handler\TestHandler;
use Monolog\Logger as MonoLogger;
use Psr\Log\LoggerInterface;
use Stringable;
use Tribe\Libs\Tests\Unit;

class LoggerPsrTest extends Unit {

	public function test_it_implements_psr_logger_interface(): void {
		$logger = new Logger( new MonoLogger( 'test' ) );

		$this->assertInstanceOf( LoggerInterface::class, $logger );
	}

	public function test_it_accepts_stringable_messages_for_all_levels(): void {
		$handler = new TestHandler();
		$logger  = new Logger( new MonoLogger( 'psr-tests', [ $handler ] ) );
		$message = new class implements Stringable {
			public function __toString(): string {
				return 'stringable-message';
			}
		};

		$logger->emergency( $message );
		$logger->alert( $message );
		$logger->critical( $message );
		$logger->error( $message );
		$logger->warning( $message );
		$logger->notice( $message );
		$logger->info( $message );
		$logger->debug( $message );
		$logger->log( MonoLogger::INFO, $message, [ 'ctx' => true ] );

		$this->assertTrue( $handler->hasEmergency( 'stringable-message' ) );
		$this->assertTrue( $handler->hasAlert( 'stringable-message' ) );
		$this->assertTrue( $handler->hasCritical( 'stringable-message' ) );
		$this->assertTrue( $handler->hasError( 'stringable-message' ) );
		$this->assertTrue( $handler->hasWarning( 'stringable-message' ) );
		$this->assertTrue( $handler->hasNotice( 'stringable-message' ) );
		$this->assertTrue( $handler->hasInfo( 'stringable-message' ) );
		$this->assertTrue( $handler->hasDebug( 'stringable-message' ) );
		$this->assertTrue( $handler->hasInfoThatContains( 'stringable-message' ) );
	}

}
