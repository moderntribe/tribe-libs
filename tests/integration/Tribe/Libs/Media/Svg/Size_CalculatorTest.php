<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg;

use Tribe\Libs\Tests\Test_Case;

final class Size_CalculatorTest extends Test_Case {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	public function test_scales_with_zero_unit(): void {
		$calculator = new Size_Calculator( 150, 100 );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 300, 0 ) );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 0, 200 ) );
		self::assertEquals( [ 150, 100 ], $calculator->scale( 0, 0 ) );
	}

	public function test_scales_with_oversize_unit(): void {
		$calculator = new Size_Calculator( 150, 100 );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 300, 9999 ) );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 9999, 200 ) );
		self::assertEquals( [ 150, 100 ], $calculator->scale( 9999, 9999 ) );
	}

	public function test_scales_to_fit(): void {
		$calculator = new Size_Calculator( 150, 100 );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 300, 300 ) );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 300, 400 ) );
		self::assertEquals( [ 300, 200 ], $calculator->scale( 400, 200 ) );
		self::assertEquals( [ 75, 50 ], $calculator->scale( 100, 50 ) );
	}

	public function test_expands_to_crop(): void {
		$calculator = new Size_Calculator( 150, 100 );
		self::assertEquals( [ 300, 200 ], $calculator->crop( 300, 200 ) );
		self::assertEquals( [ 300, 300 ], $calculator->crop( 300, 300 ) );
		self::assertEquals( [ 200, 300 ], $calculator->crop( 200, 300 ) );
		self::assertEquals( [ 75, 75 ], $calculator->crop( 75, 75 ) );
	}

	public function test_crop_redirects_to_scale(): void {
		$calculator = new Size_Calculator( 150, 100 );
		self::assertEquals( $calculator->scale( 300, 0 ), $calculator->crop( 300, 0 ) );
		self::assertEquals( $calculator->scale( 0, 200 ), $calculator->crop( 0, 200 ) );
		self::assertEquals( $calculator->scale( 0, 0 ), $calculator->crop( 0, 0 ) );
		self::assertEquals( $calculator->scale( 300, 9999 ), $calculator->crop( 300, 9999 ) );
		self::assertEquals( $calculator->scale( 9999, 200 ), $calculator->crop( 9999, 200 ) );
		self::assertEquals( $calculator->scale( 9999, 9999 ), $calculator->crop( 9999, 9999 ) );
	}

	public function test_handles_missing_original_dimensions(): void {
		$calculator = new Size_Calculator( 100, 0 );
		self::assertEquals( [ 200, 200 ], $calculator->scale( 200, 400 ) );

		$calculator = new Size_Calculator( 0, 0 );
		self::assertEquals( [ 0, 0 ], $calculator->scale( 200, 400 ) );
	}
}
