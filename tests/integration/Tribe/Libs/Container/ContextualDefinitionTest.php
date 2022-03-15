<?php declare( strict_types=1 );

namespace Tribe\Libs\Container;

use Codeception\TestCase\WPTestCase;
use DI\ContainerBuilder;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Tribe\Libs\Container;
use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Libs\Support\Contextual\MultipleDependencyClass;
use Tribe\Libs\Support\Contextual\Strategy\Color;
use Tribe\Libs\Support\Contextual\Strategy\ColorManager;
use Tribe\Libs\Support\Contextual\Strategy\Colors\Blue;
use Tribe\Libs\Support\Contextual\Strategy\Colors\Green;
use Tribe\Libs\Support\Contextual\Strategy\Colors\Red;
use Tribe\Libs\Support\Contextual\Strategy\Managers\BlueColorManager;
use Tribe\Libs\Support\Contextual\Strategy\Managers\GreenColorManager;
use Tribe\Libs\Support\Contextual\Strategy\Managers\RedColorManager;
use Tribe\Libs\Support\SampleTask;

final class ContextualDefinitionTest extends WPTestCase {

	/**
	 * @var ContainerBuilder
	 */
	private $builder;

	protected function setUp(): void {
		parent::setUp();

		$this->builder = new ContainerBuilder();
	}

	public function test_it_maps_concrete_instance_to_an_interface(): void {
		$this->builder->addDefinitions( [
			ColorManager::class => Container\autowire()
				->contextualParameter( Color::class, Red::class ),
		] );

		$container = $this->builder->build();
		$color     = $container->get( ColorManager::class )->get_color();

		$this->assertSame( 'red', $color );
	}

	public function test_it_maps_a_callable_to_an_interface(): void {
		$this->builder->addDefinitions( [
			ColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function () {
					return new Blue();
				} ),
		] );

		$container = $this->builder->build();
		$color     = $container->get( ColorManager::class )->get_color();

		$this->assertSame( 'blue', $color );
	}

	public function test_it_maps_a_callable_to_an_interface_via_parameter_injection(): void {
		$this->builder->addDefinitions( [
			ColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function ( ContainerInterface $c ) {
					return $c->get( Green::class );
				} ),
		] );

		$container = $this->builder->build();
		$color     = $container->get( ColorManager::class )->get_color();

		$this->assertSame( 'green', $color );
	}

	public function test_it_maps_a_callable_to_an_interface_via_factory(): void {
		$color_state = 'blue';

		$this->builder->addDefinitions( [
			ColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function ( ContainerInterface $c ) use ( $color_state ) {
					// Mimic a simple factory
					switch ( $color_state ) {
						case 'red':
							return $c->get( Red::class );
						case 'blue':
							return $c->get( Blue::class );
						case 'green':
							return $c->get( Green::class );
						default:
							throw new InvalidArgumentException( 'Invalid color' );
					}
				} ),
		] );

		$container = $this->builder->build();
		$color     = $container->get( ColorManager::class )->get_color();

		$this->assertSame( $color_state, $color );
	}

	public function test_maps_multiple_strategies_at_once_using_class_constants(): void {
		$this->builder->addDefinitions( [
			RedColorManager::class => Container\autowire()
				->contextualParameter( Color::class, Red::class ),

			BlueColorManager::class => Container\autowire()
				->contextualParameter( Color::class, Blue::class ),

			GreenColorManager::class => Container\autowire()
				->contextualParameter( Color::class, Green::class ),
		] );

		$container = $this->builder->build();

		$red   = $container->get( RedColorManager::class )->get_color();
		$blue  = $container->get( BlueColorManager::class )->get_color();
		$green = $container->get( GreenColorManager::class )->get_color();

		$this->assertSame( 'red', $red );
		$this->assertSame( 'blue', $blue );
		$this->assertSame( 'green', $green );
	}

	public function test_maps_multiple_strategies_at_once_using_callables(): void {
		$this->builder->addDefinitions( [
			RedColorManager::class   => Container\autowire()
				->contextualParameter( Color::class, static function () {
					return new Red();
				} ),
			BlueColorManager::class  => Container\autowire()
				->contextualParameter( Color::class, static function () {
					return new Blue();
				} ),
			GreenColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function () {
					return new Green();
				} ),
		] );

		$container = $this->builder->build();

		$red   = $container->get( RedColorManager::class )->get_color();
		$blue  = $container->get( BlueColorManager::class )->get_color();
		$green = $container->get( GreenColorManager::class )->get_color();

		$this->assertSame( 'red', $red );
		$this->assertSame( 'blue', $blue );
		$this->assertSame( 'green', $green );
	}

	public function test_maps_multiple_strategies_at_once_using_callables_with_container_injection(): void {
		$this->builder->addDefinitions( [
			RedColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function ( ContainerInterface $c ) {
					return $c->get( Red::class );
				} ),

			BlueColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function ( ContainerInterface $c ) {
					return $c->get( Blue::class );
				} ),

			GreenColorManager::class => Container\autowire()
				->contextualParameter( Color::class, static function ( ContainerInterface $c ) {
					return $c->get( Green::class );
				} ),
		] );

		$container = $this->builder->build();

		$red   = $container->get( RedColorManager::class )->get_color();
		$blue  = $container->get( BlueColorManager::class )->get_color();
		$green = $container->get( GreenColorManager::class )->get_color();

		$this->assertSame( 'red', $red );
		$this->assertSame( 'blue', $blue );
		$this->assertSame( 'green', $green );
	}

	public function test_it_maps_multiple_dependencies(): void {
		$this->builder->addDefinitions( [
			MultipleDependencyClass::class => Container\autowire()
				->contextualParameter( Color::class, Red::class )
				->contextualParameter( Task::class, SampleTask::class )
				->constructorParameter( 'test_string', 'hello' )
		] );

		$container = $this->builder->build();
		$instance  = $container->get( MultipleDependencyClass::class );

		$this->assertInstanceOf( SampleTask::class, $instance->get_task() );
		$this->assertInstanceOf( Red::class, $instance->get_color() );
		$this->assertSame( 'hello', $instance->get_test_string() );
	}

	public function test_it_maps_multiple_dependencies_with_method_setting(): void {
		$this->builder->addDefinitions( [
			MultipleDependencyClass::class => Container\autowire()
				->contextualParameter( Color::class, Red::class )
				->contextualParameter( Task::class, SampleTask::class )
				->constructorParameter( 'test_string', 'hello' )
				->method( 'set_color', new Green() ),
		] );

		$container = $this->builder->build();
		$instance  = $container->get( MultipleDependencyClass::class );

		$this->assertInstanceOf( SampleTask::class, $instance->get_task() );
		$this->assertInstanceOf( Green::class, $instance->get_color() );
		$this->assertSame( 'hello', $instance->get_test_string() );
	}

}
