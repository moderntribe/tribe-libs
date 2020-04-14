<?php

namespace Tribe\Libs\Utils;

class Markup_UtilsTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_concats_attributes(): void {
		$attributes = [
			'first'  => 'one',
			'second' => 'two',
		];
		$this->assertEquals( 'first="one" second="two"', Markup_Utils::concat_attrs( $attributes ) );
	}

	public function test_concats_attribute_arrays(): void {
		$attributes = [
			'first' => [ 'one' => 'uno', 'two' => 'dos', 'three' => 'tres' ],
		];
		$this->assertEquals( 'first-one="uno" first-two="dos" first-three="tres"', Markup_Utils::concat_attrs( $attributes ) );
	}

	public function test_escapes_attributes(): void {
		$attributes = [
			'data-js'  => '[ "one" ]',
		];
		$this->assertEquals( 'data-js="[ &quot;one&quot; ]"', Markup_Utils::concat_attrs( $attributes ) );
	}

	public function test_concats_classes(): void {
		$classes = [ 'one', 'two', 'three' ];
		$this->assertEquals( 'one two three', Markup_Utils::class_attribute( $classes, false ) );
	}

	public function test_class_attribute(): void {
		$classes = [ 'one', 'two', 'three' ];
		$this->assertEquals( ' class="one two three"', Markup_Utils::class_attribute( $classes, true ) );
	}

	public function test_escapes_classnames(  ): void {
		$classes = [ 'first one', 'second one' ];
		$this->assertEquals( 'firstone secondone', Markup_Utils::class_attribute( $classes, false ) );
	}
}
