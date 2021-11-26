<?php declare(strict_types=1);

namespace Tribe\Libs\Pipeline;

use Closure;
use Codeception\TestCase\WPTestCase;
use DI\ContainerBuilder;

final class PipelineTest extends WPTestCase {

	/**
	 * @var \Tribe\Libs\Pipeline\Pipeline
	 */
	private $pipeline;

	protected function setUp(): void {
		parent::setUp();

		$this->pipeline = new Pipeline( ( new ContainerBuilder() )->build() );
	}

	public function test_it_runs_a_pipeline_with_closures() {
		$result = $this->pipeline->send( 'a sample string that is passed through to all pipes.' )
						->through(
							static function ( string $passable, Closure $next ) {
								$passable = ucwords( $passable );

								return $next( $passable );
							},
							static function ( string $passable, Closure $next ) {
								$passable = str_ireplace( 'All', 'All The', $passable );

								return $next( $passable );
							}
						)->thenReturn();

		$this->assertSame( 'A Sample String That Is Passed Through To All The Pipes.', $result );
	}

	public function test_it_runs_a_pipeline_using_object_handlers() {
		$stage1 = new class() {
			public function handle( string $passable, Closure $next ) {
				$passable = ucwords( $passable );

				return $next( $passable );
			}
		};

		$stage2 = new class() {
			public function handle( string $passable, Closure $next ) {
				$passable = str_ireplace( 'All', 'All The', $passable );

				return $next( $passable );
			}
		};

		$result = $this->pipeline->send( 'a sample string that is passed through to all pipes.' )
						 ->through(
							 $stage1,
							 $stage2
						 )->thenReturn();

		$this->assertSame( 'A Sample String That Is Passed Through To All The Pipes.', $result );
	}

	public function test_it_runs_a_pipeline_using_custom_object_handlers() {
		$stage1 = new class() {
			public function run( string $passable, Closure $next ) {
				$passable = ucwords( $passable );

				return $next( $passable );
			}
		};

		$stage2 = new class() {
			public function run( string $passable, Closure $next ) {
				$passable = str_ireplace( 'All', 'All The', $passable );

				return $next( $passable );
			}
		};

		// Tell the pipeline to use the "run" method instead of the default "handle" on all stages.
		$result = $this->pipeline->via( 'run' )
						 ->send( 'a sample string that is passed through to all pipes.' )
						 ->through(
							 $stage1,
							 $stage2
						 )->thenReturn();

		$this->assertSame( 'A Sample String That Is Passed Through To All The Pipes.', $result );
	}

	public function test_stages_can_accept_additional_parameters_using_closures() {
		$cache = new class( [] ) {

			/**
			 * @var mixed[]
			 */
			private $items;

			public function __construct( array $items ) {
				$this->items = $items;
			}

			public function add( $item ) {
				$this->items[] = $item;
			}

			public function all(): array {
				return $this->items;
			}

		};

		/*
		 * Additional Parameters will be automatically passed through to
		 * closures and Stage::handle() methods.
		 *
		 * Note: only objects are passed by reference. Modifying an object's
		 * state within a stage will directly modify that object. All other variables
		 * you would need to pass by reference or return their changed state to the
		 * next stage.
		 */
		$additionalParameters = [
			$cache,
		];

		$result = $this->pipeline->send( 1, $additionalParameters )
						 ->through(
							 static function ( int $passable, Closure $next, $cache ) {
								 $passable = $passable + 4;

								 $cache->add( $passable );

								 return $next( $passable );
							 },
							 static function ( int $passable, Closure $next, $cache ) {
								 $passable = $passable + 5;

								 $cache->add( $passable );

								 return $next( $passable );
							 },
							 static function ( int $passable, Closure $next, $cache ) {
								 $passable = $passable + 10;

								 $cache->add( $passable );

								 return $next( $passable );
							 }
						 )->thenReturn();

		$this->assertSame( 20, $result );
		$this->assertSame( [ 5, 10, 20 ], $cache->all() );
	}

	public function test_stages_can_accept_additional_parameters_using_object_handlers() {
		$cache = new class( [] ) {

			/**
			 * @var mixed[]
			 */
			private $items;

			public function __construct( array $items ) {
				$this->items = $items;
			}

			public function add( $item ) {
				$this->items[] = $item;
			}

			public function all(): array {
				return $this->items;
			}

		};

		$stage1 = new class() {
			public function handle( int $passable, Closure $next, $cache ) {
				$passable = $passable + 4;

				$cache->add( $passable );

				return $next( $passable );
			}
		};

		$stage2 = new class() {
			public function handle( int $passable, Closure $next, $cache ) {
				$passable = $passable + 5;

				$cache->add( $passable );

				return $next( $passable );
			}
		};

		$stage3 = new class() {
			public function handle( int $passable, Closure $next, $cache ) {
				$passable = $passable + 10;

				$cache->add( $passable );

				return $next( $passable );
			}
		};

		$additionalParameters = [
			$cache,
		];

		$result = $this->pipeline->send( 1, $additionalParameters )
						 ->through(
							 $stage1,
							 $stage2,
							 $stage3
						 )->thenReturn();

		$this->assertSame( 20, $result );
		$this->assertSame( [ 5, 10, 20 ], $cache->all() );
	}

}
