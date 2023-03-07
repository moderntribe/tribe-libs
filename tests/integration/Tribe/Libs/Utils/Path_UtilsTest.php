<?php declare(strict_types=1);

namespace Tribe\Libs\Utils;

use Tribe\Libs\Tests\Test_Case;

class Path_UtilsTest extends Test_Case {
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
