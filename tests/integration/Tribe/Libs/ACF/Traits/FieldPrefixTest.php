<?php declare(strict_types=1);

namespace Tribe\Libs\ACF\Traits;

use Codeception\TestCase\WPTestCase;
use InvalidArgumentException;
use Tribe\Libs\ACF\Block;
use Tribe\Libs\ACF\Block_Config;
use Tribe\Libs\ACF\Field;
use Tribe\Libs\ACF\Field_Group;
use Tribe\Libs\ACF\Field_Section;
use Tribe\Libs\ACF\Flexible_Content;
use Tribe\Libs\ACF\Layout;

final class FieldPrefixTest extends WPTestCase {

	public function test_it_prefixes_keys_in_a_block_config(): void {
		$block = new class extends Block_Config {

			use With_Field_Prefix;

			public const NAME = 'test_block';

			public const SECTION_CONFIG        = 's-config';
			public const FIELD_TITLE           = 'title';
			public const GROUP_EXTENDED_CONFIG = 'extended_config';
			public const LAYOUT_DEFAULT        = 'layout_default';

			public function add_block() {
				$this->set_block( new Block( self::NAME, [
					'title' => 'Test Block',
					'description' => 'A test block',
				] ) );
			}

			public function add_fields() {
				$this->add_section( new Field_Section( self::SECTION_CONFIG, 'Config', 'accordion' ) )
				     ->add_field( new Field( self::NAME . '_' . self::FIELD_TITLE, [
					     'label' => 'Title',
					     'name'  => self::FIELD_TITLE,
					     'type'  => 'text',
				     ] ) )
				     ->add_field( new Field_Group( self::NAME . '_' . self::GROUP_EXTENDED_CONFIG ) )
				     ->add_field( ( new Flexible_Content( self::NAME . '_whatever' ) )->add_layout(
					     new Layout( self::NAME . '_' . self::LAYOUT_DEFAULT )
				     ) );
			}

			public function get_keys(): array {
				return [
					'field'   => $this->get_field_key( self::FIELD_TITLE ),
					'group'   => $this->get_field_key( self::GROUP_EXTENDED_CONFIG ),
					'section' => $this->get_section_key( self::SECTION_CONFIG ),
					'layout'  => $this->get_layout_key( self::LAYOUT_DEFAULT ),
				];
			}

		};

		$valid_keys = [
			'field_test_block_title'           => true,
			'field_test_block_extended_config' => true,
			'section__s-config'                => true,
			'layout_test_block_layout_default' => true,
		];

		foreach ( $block->get_fields() as $f ) {
			foreach ( $f->get_attributes() as $attributes ) {
				if ( isset( $attributes['layouts'] ) ) {
					foreach ( $attributes['layouts'] as $layout ) {
						$this->assertTrue( $valid_keys[ $layout['key'] ] );
					}
				} else {
					$this->assertTrue( $valid_keys[ $attributes['key'] ] );
				}
			}
		}

		$this->assertSame( [
			'field'   => 'field_test_block_title',
			'group'   => 'field_test_block_extended_config',
			'section' => 'section__s-config',
			'layout'  => 'layout_test_block_layout_default',
		], $block->get_keys() );

	}

	public function test_it_throws_exception_when_used_in_an_invalid_class(): void {
		$this->expectException( InvalidArgumentException::class );

		$block = new class {
			use With_Field_Prefix;

			public function get_keys(): array {
				return [
					'field' => $this->get_field_key( 'title' ),
				];
			}
		};

		$block->get_keys();
	}

	public function test_no_exception_is_thrown_when_ignoring_name_prefix(): void {
		$block = new class {
			use With_Field_Prefix;

			public function get_keys(): array {
				return [
					'field' => $this->get_field_key( 'title', false ),
				];
			}
		};

		$this->assertSame( [
			'field' => 'field_title',
		], $block->get_keys() );
	}

}
