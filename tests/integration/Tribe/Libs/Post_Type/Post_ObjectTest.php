<?php

namespace Tribe\Libs\Post_Type;

use Tribe\Libs\Object_Meta\Meta_Group;
use Tribe\Libs\Object_Meta\Meta_Map;

class Post_ObjectTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_delegates_meta() {
		$map   = new Meta_Map( 'page' );
		$group = new class ( [ 'post_types' => [ 'page' ] ] ) extends Meta_Group {
			public function get_keys() {
				return [ 'key_one', 'key_two' ];
			}

			public function get_value( $object_id, $key ) {
				switch ( $key ) {
					case 'key_one':
						return 'one';
					case 'key_two':
						return 'two';
					default:
						return '';
				}
			}
		};

		$map->add( $group );

		$page = new class( 0, $map ) extends Post_Object {
		};

		$this->assertEquals( 'one', $page->key_one );
		$this->assertEquals( 'two', $page->get_meta( 'key_two' ) );
	}
}
