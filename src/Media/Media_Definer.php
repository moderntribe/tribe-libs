<?php declare(strict_types=1);

namespace Tribe\Libs\Media;

use DI;
use enshrined\svgSanitize\Sanitizer;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Media\Svg\Store\Contracts\Svg_Store;
use Tribe\Libs\Media\Svg\Store\Stores\Svg_Meta_Store;

class Media_Definer implements Definer_Interface {

	public const SVG_INLINE_META_KEY = '_tribe_svg_source';

	public function define(): array {
		return [
			Sanitizer::class      => static function () {
				$sanitizer = new Sanitizer();
				// This could cause potential issues with non UTF-8/UTF-16 SVGs.
				$sanitizer->removeXMLTag( true );
				$sanitizer->minify( true );
				$sanitizer->setXMLOptions( 0 );

				return $sanitizer;
			},
			Svg_Meta_Store::class => DI\autowire()
				->constructorParameter( 'meta_key', self::SVG_INLINE_META_KEY ),
			Svg_Store::class      => DI\get( Svg_Meta_Store::class ),
		];
	}

}
