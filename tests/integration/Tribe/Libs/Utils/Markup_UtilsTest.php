<?php declare(strict_types=1);

namespace Tribe\Libs\Utils;

use Tribe\Libs\Tests\Test_Case;

final class Markup_UtilsTest extends Test_Case {
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
			'data-js' => '[ "one" ]',
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

	public function test_escapes_classnames(): void {
		$classes = [ 'first one', 'second one' ];
		$this->assertEquals( 'firstone secondone', Markup_Utils::class_attribute( $classes, false ) );
	}

	public function test_truncates_html(): void {
		$text = 'this is a string that has eight words';
		$this->assertEquals( 'this is a string', Markup_Utils::truncate_html( $text, 4, '', false ) );
	}

	public function test_strips_shortcodes(): void {
		$text = 'this is a [gallery] that has eight words';
		$this->assertEquals( 'this is a that', Markup_Utils::truncate_html( $text, 4, '', false ) );
	}

	public function test_leaves_unregistered_shortcodes(): void {
		$text = 'this is a [string] that has eight words';
		$this->assertEquals( 'this is a [string]', Markup_Utils::truncate_html( $text, 4, '', false ) );
	}

	public function test_strips_html_markup(): void {
		$text = '<p>this is a <strong>string</strong> that has eight words</p>';
		$this->assertEquals( 'this is a string', Markup_Utils::truncate_html( $text, 4, '', false ) );
	}

	public function test_applies_autop(): void {
		$text = "this is a string\n\nit has two paragraphs";
		$this->assertEquals("<p>this is a string it has&hellip;</p>\n", Markup_Utils::truncate_html( $text, 6 ) );
	}
}
