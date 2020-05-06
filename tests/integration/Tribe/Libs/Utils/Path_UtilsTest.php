<?php

namespace Tribe\Libs\Utils;

class Path_UtilsTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_get_extension_from_url(): void {
		$url = 'http://example.org/file.jpg';
		$this->assertEquals( 'jpg', Path_Utils::file_extension( $url ) );
	}

	public function test_no_extension_from_url(): void {
		$url = 'http://example.org/file';
		$this->assertEquals( '', Path_Utils::file_extension( $url ) );
	}

	public function test_get_extension_from_filesystem_path(): void {
		$path = '/path/to/file.txt';
		$this->assertEquals( 'txt', Path_Utils::file_extension( $path ) );
	}

	public function test_get_extension_from_relative_path(): void {
		$path = '../..//path/to/file.txt';
		$this->assertEquals( 'txt', Path_Utils::file_extension( $path ) );
	}
}
