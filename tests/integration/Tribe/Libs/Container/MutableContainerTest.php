<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use DI;
use DI\ContainerBuilder;
use Tribe\Libs\Tests\Fixtures\SampleClass;
use Tribe\Libs\Tests\Test_Case;

final class MutableContainerTest extends Test_Case {

	public function test_it_makes_fresh_instances_of_the_same_class() {
		$builder   = new ContainerBuilder();
		$builder->addDefinitions( [
			SampleClass::class => DI\autowire( SampleClass::class ),
		] );

		// Create an existing container, like SquareOne does.
		$di = $builder->build();

		$di_instance    = $di->get( SampleClass::class );
		$di_instance_id = $di_instance->get_object_id();

		// Wrap the SquareOne container in the Scoped Container
		$container = ( new Container() )->wrap( $di );

		// Create a fresh instance from the container, fresh because it's the first time
		// we're fetching it.
		$instance_1 = $container->get( SampleClass::class );
		$id_1       = $instance_1->get_object_id();
		$this->assertGreaterThan( 0, $id_1 );

		// The original container and new container instances should not match
		$this->assertNotEquals( $id_1, $di_instance_id );

		// Grab from the container again, should be the same instance.
		$this->assertSame( $id_1, $container->get( SampleClass::class )->get_object_id() );

		// Use the container to make a brand new instance.
		$instance_2 = $container->make( SampleClass::class );
		$id_2  = $instance_2->get_object_id();

		// The parent instance is different.
		$this->assertNotEquals( $id_1, $id_2 );

		// But...PHP-DI does not change the underlying dependencies, they remain exactly the same.
		$this->assertSame( $instance_1->get_sub_object_id(), $instance_2->get_sub_object_id() );

		$instance_3 = $container->make( SampleClass::class );
		$id_3       = $instance_3->get_object_id();

		// Again, the parent instance is new.
		$this->assertNotEquals( $id_2, $id_3 );

		// And again, the underlying instances are still cached and still the same.
		$this->assertSame( $instance_2->get_sub_object_id(), $instance_3->get_sub_object_id() );

		// Even the same as the first instance we created.
		$this->assertSame( $instance_3->get_sub_object_id(), $instance_1->get_sub_object_id() );

		// Make a completely fresh instance, note this is slow and should be used sparingly.
		$instance_4 = $container->makeFresh( SampleClass::class );
		$id_4       = $instance_4->get_object_id();

		$this->assertNotEquals( $id_3, $id_4 );

		// Now our underlying dependencies have changed, and we have completely fresh instances, and
		// we can still use the container to fulfill complex instance creation (useful in queues, or when
		// running in any single, long-running process where we need to refresh state).
		$this->assertNotEquals( $instance_3->get_sub_object_id(), $instance_4->get_sub_object_id() );
	}

	public function test_it_flushes_the_container() {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			SampleClass::class => DI\autowire( SampleClass::class ),
		] );

		// Create an existing container, like SquareOne does.
		$di = $builder->build();

		$di_instance = $di->get( SampleClass::class );
		$di_id       = $di_instance->get_object_id();
		$di_sub_id   = $di_instance->get_sub_object_id();

		$container = ( new Container() )->wrap( $di );

		$instance_1 = $container->make( SampleClass::class );
		$id_1       = $instance_1->get_object_id();
		$sub_id_1   = $instance_1->get_sub_object_id();

		// We used make above, so the parent instance should be different.
		$this->assertNotEquals( $di_id, $id_1 );
		// Resolved dependencies are used because we wrapped the container.
		$this->assertEquals( $di_sub_id, $sub_id_1 );

		$instance_2 = $container->get( SampleClass::class );
		$id_2       = $instance_2->get_object_id();
		$sub_id_2   = $instance_2->get_sub_object_id();

		$this->assertNotEquals( $id_1, $id_2 );
		$this->assertSame( $sub_id_1, $sub_id_2 );

		// Clear all resolved entries.
		$container->flush();

		$instance_3 = $container->get( SampleClass::class );
		$id_3       = $instance_3->get_object_id();
		$sub_id_3   = $instance_3->get_sub_object_id();

		$this->assertNotEquals( $id_3, $di_id );
		$this->assertNotEquals( $id_3, $id_1 );
		$this->assertNotEquals( $id_3, $id_2 );
		$this->assertNotEquals( $sub_id_3, $di_sub_id );
		$this->assertNotEquals( $sub_id_3, $sub_id_1 );
		$this->assertNotEquals( $sub_id_3, $sub_id_2 );
	}

}
