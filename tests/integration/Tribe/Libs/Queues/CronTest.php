<?php declare(strict_types=1);

namespace Tribe\Libs\Queues;

use Codeception\TestCase\WPTestCase;
use DI\ContainerBuilder;
use Tribe\Libs\Queues\Backends\Mock_Backend;
use Tribe\Libs\Queues\Contracts\Task;

final class CronTest extends WPTestCase {

	public function test_it_schedules_the_queue_cron(): void {
		$cron = new Cron( ( new ContainerBuilder() )->build() );

		add_filter( 'cron_schedules', static function ( $schedules ) use ( $cron ) {
			return $cron->add_interval( $schedules );
		}, 10, 1 );

		$schedules = wp_get_schedules();

		$this->assertArrayHasKey( Cron::FREQUENCY, $schedules );

		$cron->schedule_cron();

		$events = _get_cron_array();

		foreach ( $events as $hooks ) {
			if ( isset( $hooks[ Cron::CRON_ACTION ] ) ) {
				$this->assertTrue( true );
				break;
			}

			$this->assertFalse( false );
		}
	}

	public function test_it_processes_the_queue(): void {
		$task = new class implements Task {
			public function handle( array $args ): bool {
				return true;
			}
		};

		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			'TestTask' => $task,
		] );

		$backend = new Mock_Backend();
		$cron    = new Cron( $builder->build() );

		$message = new Message( 'TestTask', [], 10, '0' );

		$backend->enqueue( DefaultQueue::NAME, $message );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );

		$queue = new DefaultQueue( $backend );

		$cron->process_queues( $queue );

		$this->assertSame( 0, $backend->count( DefaultQueue::NAME ) );
	}

	public function test_the_queue_stops_processing_when_task_cannot_be_instantiated(): void {
		$task = new class implements Task {
			public function handle( array $args ): bool {
				return true;
			}
		};

		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			'TestTask' => $task,
		] );

		$backend = new Mock_Backend();
		$cron    = new Cron( $builder->build() );

		$message = new Message( 'TaskDoesNotExist', [], 10, '0' );

		$backend->enqueue( DefaultQueue::NAME, $message );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );

		$queue = new DefaultQueue( $backend );

		$cron->process_queues( $queue );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );
	}

	public function test_it_cleans_up_the_queue(): void {
		$cron    = new Cron( ( new ContainerBuilder() )->build() );
		$backend = new Mock_Backend();
		$message = new Message( 'TestTask', [], 10, 'test_task' );
		$queue   = new DefaultQueue( $backend );

		$backend->enqueue( DefaultQueue::NAME, $message );
		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );
		$cron->cleanup( $queue );
		$this->assertSame( 0, $backend->count( DefaultQueue::NAME ) );
	}

}
