<?php declare(strict_types=1);

namespace Tribe\Libs\Twig;

use Twig\Cache\FilesystemCache;

class Twig_Cache extends FilesystemCache {

	protected string $path;

	public function __construct( string $path, int $options = 0 ) {
		$this->path = $path;
		parent::__construct( $path, $options );
	}

	public function generateKey( string $name, string $className ): string {
		$hash = hash( 'sha256', $className );

		return $this->path . $hash[0] . $hash[1] . '/' . $hash . '.html';
	}

}
