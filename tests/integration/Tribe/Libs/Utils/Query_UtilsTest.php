<?php

namespace Tribe\Libs\Utils;

class Query_UtilsTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_string_list(): void {
		$strings = [ 'one', 'two' ];

		$this->assertEquals( "'one','two'", Query_Utils::get_quoted_string_list( $strings ) );
	}

	public function test_escapes_strings(): void {
		$strings = [ "o'ne", 'two' ];

		$this->assertEquals( "'o\\'ne','two'", Query_Utils::get_quoted_string_list( $strings ) );
	}

	public function test_alternate_quote(): void {
		$strings = [ 'one', 'two' ];

		$this->assertEquals( '"one","two"', Query_Utils::get_quoted_string_list( $strings, '"' ) );
	}

	public function test_int_list(): void {
		$ints = [ "1", 2, 3, "four", "alphabet" ];

		$this->assertEquals( '1,2,3,0,0', Query_Utils::get_int_list( $ints ) );
	}
}
