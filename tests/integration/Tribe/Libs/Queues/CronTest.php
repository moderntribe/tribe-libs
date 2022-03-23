<?php declare(strict_types=1);

namespace Tribe\Libs\Queues;

use Codeception\TestCase\WPTestCase;
use Tribe\Libs\Container\Container;
use Tribe\Libs\Queues\Backends\Mock_Backend;
use Tribe\Libs\Support\SampleTask;

final class CronTest extends WPTestCase {

	/**
	 * @var \DI\FactoryInterface|\Psr\Container\ContainerInterface
	 */
	private $container;

	protected function setUp(): void {
		parent::setUp();

		$this->container = new Container();
	}

	public function test_it_schedules_the_queue_cron(): void {
		$cron = new Cron( $this->container );

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
		$backend   = new Mock_Backend();
		$cron      = new Cron( $this->container );

		$message = new Message( SampleTask::class, [ 5 ], 10, '0' );

		$backend->enqueue( DefaultQueue::NAME, $message );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );

		$queue = new DefaultQueue( $backend );

		$cron->process_queues( $queue );

		$this->assertSame( 0, $backend->count( DefaultQueue::NAME ) );

		$task = $this->container->get( SampleTask::class );

		$this->assertSame( 5, $task->get_number() );
	}

	public function test_it_processes_multiple_queue_tasks_with_unique_instances(): void {
		$backend   = new Mock_Backend();
		$cron      = new Cron( $this->container );

		$message1 = new Message( SampleTask::class, [ 5 ], 10, '0' );
		$message2 = new Message( SampleTask::class, [ 10 ], 10, '1' );

		$backend->enqueue( DefaultQueue::NAME, $message1 );
		$backend->enqueue( DefaultQueue::NAME, $message2 );
		$this->assertSame( 2, $backend->count( DefaultQueue::NAME ) );

		$task     = $this->container->get( SampleTask::class );
		$class_id = spl_object_id( $task );
		$sub_id   = $task->get_sub_object_id();
		$this->assertSame( 0, $task->get_number() );

		$queue = new DefaultQueue( $backend );

		$cron->process_queues( $queue );

		$this->assertSame( 0, $backend->count( DefaultQueue::NAME ) );

		$task = $this->container->get( SampleTask::class );
		$this->assertSame( 10, $task->get_number() );
		$this->assertNotEquals( $class_id, spl_object_id( $task ) );
		$this->assertNotEquals( $sub_id, $task->get_sub_object_id() );
	}

	public function test_it_stops_processing_when_task_cannot_be_instantiated(): void {
		$backend   = new Mock_Backend();
		$cron      = new Cron( $this->container );

		$message = new Message( 'TaskDoesNotExist', [], 10, '0' );

		$backend->enqueue( DefaultQueue::NAME, $message );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );

		$queue = new DefaultQueue( $backend );

		$cron->process_queues( $queue );

		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );

		$task = $this->container->get( SampleTask::class );

		$this->assertSame( 0, $task->get_number() );
	}

	public function test_it_cleans_up_the_queue(): void {
		$cron      = new Cron( $this->container );
		$backend   = new Mock_Backend();
		$message   = new Message( SampleTask::class, [ 5 ], 10, '0' );
		$queue     = new DefaultQueue( $backend );

		$backend->enqueue( DefaultQueue::NAME, $message );
		$this->assertSame( 1, $backend->count( DefaultQueue::NAME ) );
		$cron->cleanup( $queue );
		$this->assertSame( 0, $backend->count( DefaultQueue::NAME ) );

		$task = $this->container->get( SampleTask::class );

		$this->assertSame( 0, $task->get_number() );
	}

}
