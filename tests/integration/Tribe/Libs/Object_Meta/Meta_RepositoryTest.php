<?php

namespace Tribe\Libs\Object_Meta;

class Meta_RepositoryTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_sets_meta_map() {
		$group      = new class ( [ 'post_types' => [ 'page' ] ] ) extends Meta_Group {
			public function get_keys() {
				return [ 'key_one', 'key_two' ];
			}

			public function get_value( $object_id, $key ) {
				return 'one';
			}
		};
		$repository = new Meta_Repository( [ $group ] );

		$map = $repository->get( 'page' );

		$this->assertEquals( 'one', $map->get_value( 0, 'key_one' ) );
	}
}
