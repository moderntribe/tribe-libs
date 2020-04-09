<?php

namespace Tribe\Libs\Object_Meta;

class Meta_MapTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function tests_handles_key_override() {
		$map    = new Meta_Map( 'page' );
		$group1 = new class ( [ 'post_types' => [ 'page' ] ] ) extends Meta_Group {
			public function get_keys() {
				return [ 'key_one', 'key_two' ];
			}

			public function get_value( $object_id, $key ) {
				switch ( $key ) {
					case 'key_one':
						return 'one-one';
					case 'key_two':
						return 'one-two';
					default:
						return '';
				}
			}
		};
		$group2 = new class ( [ 'post_types' => [ 'page' ] ] ) extends Meta_Group {
			public function get_keys() {
				return [ 'key_two', 'key_three' ];
			}

			public function get_value( $object_id, $key ) {
				switch ( $key ) {
					case 'key_two':
						return 'two-two';
					case 'key_three':
						return 'two-three';
					default:
						return '';
				}
			}
		};

		$map->add( $group1 );
		$map->add( $group2 );

		$this->assertEquals( 'one-one', $map->get_value( 0, 'key_one' ) );
		$this->assertEquals( 'two-two', $map->get_value( 0, 'key_two' ) ); // the group added later takes precedence
		$this->assertEquals( 'two-three', $map->get_value( 0, 'key_three' ) );
	}
}
