<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use DI;
use DI\ContainerBuilder;
use Tribe\Libs\Tests\Fixtures\SampleClass;
use Tribe\Libs\Tests\Unit;

/**
 * PHP-DI 7 compatible wrap/flush behaviour without WordPress bootstrap.
 */
class ContainerWrapTest extends Unit {

	public function test_wrap_accepts_empty_definitions_array_for_php_di_seven(): void {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			SampleClass::class => DI\autowire( SampleClass::class ),
		] );

		$inner     = $builder->build();
		$container = ( new Container() )->wrap( $inner );

		$this->assertInstanceOf( Container::class, $container );
		$this->assertInstanceOf( SampleClass::class, $container->get( SampleClass::class ) );
	}

	public function test_make_fresh_returns_new_dependency_graph(): void {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			SampleClass::class => DI\autowire( SampleClass::class ),
		] );

		$container = ( new Container() )->wrap( $builder->build() );
		$first     = $container->get( SampleClass::class );
		$fresh     = $container->makeFresh( SampleClass::class );

		$this->assertNotSame( $first->get_object_id(), $fresh->get_object_id() );
		$this->assertNotSame( $first->get_sub_object_id(), $fresh->get_sub_object_id() );
	}

	public function test_flush_clears_resolved_entries_on_wrapped_container(): void {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			SampleClass::class => DI\autowire( SampleClass::class ),
		] );

		$inner     = $builder->build();
		$container = ( new Container() )->wrap( $inner );

		$before = $container->get( SampleClass::class );
		$container->flush();
		$after = $container->get( SampleClass::class );

		$this->assertNotSame( $before->get_object_id(), $after->get_object_id() );
		$this->assertNotSame( $before->get_sub_object_id(), $after->get_sub_object_id() );
	}

}
