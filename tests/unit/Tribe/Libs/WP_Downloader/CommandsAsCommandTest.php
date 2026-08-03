<?php declare(strict_types=1);

namespace Tribe\Libs\WP_Downloader;

use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Tribe\Libs\Tests\Unit;
use Tribe\Libs\WP_Downloader\Commands\File_Copier;
use Tribe\Libs\WP_Downloader\Commands\Plugin_Downloader;
use Tribe\Libs\WP_Downloader\Commands\WordPress_Downloader;

class CommandsAsCommandTest extends Unit {

	/**
	 * @dataProvider command_attribute_provider
	 *
	 * @param class-string $class
	 */
	public function test_commands_declare_as_command_attribute( string $class, string $name ): void {
		$attributes = ( new ReflectionClass( $class ) )->getAttributes( AsCommand::class );

		$this->assertNotEmpty( $attributes, sprintf( '%s is missing #[AsCommand]', $class ) );

		/** @var AsCommand $attribute */
		$attribute = $attributes[0]->newInstance();

		$this->assertSame( $name, $attribute->name );
		$this->assertNotSame( '', $attribute->description );
	}

	public static function command_attribute_provider(): array {
		return [
			'wordpress' => [ WordPress_Downloader::class, 'wp' ],
			'plugin'    => [ Plugin_Downloader::class, 'plugin' ],
			'copy'      => [ File_Copier::class, 'copy' ],
		];
	}

}
