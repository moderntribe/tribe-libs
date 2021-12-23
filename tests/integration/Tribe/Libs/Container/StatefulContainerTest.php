<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use Codeception\TestCase\WPTestCase;
use Tribe\Libs\Support\SampleClass;

final class StatefulContainerTest extends WPTestCase {

	public function test_it_makes_fresh_instances_of_the_same_class() {
		$container = new Container();

		// Create a fresh instance from the container, fresh because it's the first time
		// we're fetching it.
		$instance_1 = $container->get( SampleClass::class );
		$id_1       = $instance_1->get_object_id();
		$this->assertGreaterThan( 0, $id_1 );

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

		// Use our custom refresh() method to clear the container state and make a new instance.
		$instance_4 = $container->refresh()->make( SampleClass::class );
		$id_4       = $instance_4->get_object_id();

		$this->assertNotEquals( $id_3, $id_4 );

		// Now our underlying dependencies have changed, and we have completely fresh instances, and
		// we can still use the container to fulfill complex instance creation (useful in queues, or when
		// running in any single, long-running process where we need to refresh state).
		$this->assertNotEquals( $instance_3->get_sub_object_id(), $instance_4->get_sub_object_id() );
	}
}
