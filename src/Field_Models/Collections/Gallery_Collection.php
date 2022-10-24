<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Collections;

use Spatie\DataTransferObject\DataTransferObjectCollection;
use Tribe\Libs\Field_Models\Models\Image;

class Gallery_Collection extends DataTransferObjectCollection {

	public static function create( array $attachments ): Gallery_Collection {
		return new static( Image::arrayOf( $attachments ) );
	}

	public function current(): Image {
		return parent::current();
	}

	public function offsetGet( $offset ): ?Image {
		return parent::offsetGet( $offset );
	}

}
