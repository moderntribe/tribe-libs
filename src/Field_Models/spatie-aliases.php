<?php declare(strict_types=1);

/**
 * Back-compat class aliases for projects that still typehint Spatie DTO v2 classes
 * in Field_Model overrides (`castValue`, `castType`, etc.).
 *
 * Only registered when Spatie DTO is not installed, so a leftover project dependency
 * does not conflict.
 */

use Tribe\Libs\Field_Models\DTO\Data_Transfer_Object;
use Tribe\Libs\Field_Models\DTO\Data_Transfer_Object_Collection;
use Tribe\Libs\Field_Models\DTO\Field_Validator;
use Tribe\Libs\Field_Models\DTO\Value_Caster;

if ( ! class_exists( \Spatie\DataTransferObject\DataTransferObject::class, false ) ) {
	class_alias( Data_Transfer_Object::class, \Spatie\DataTransferObject\DataTransferObject::class );
}

if ( ! class_exists( \Spatie\DataTransferObject\FlexibleDataTransferObject::class, false ) ) {
	class_alias( Data_Transfer_Object::class, \Spatie\DataTransferObject\FlexibleDataTransferObject::class );
}

if ( ! class_exists( \Spatie\DataTransferObject\DataTransferObjectCollection::class, false ) ) {
	class_alias( Data_Transfer_Object_Collection::class, \Spatie\DataTransferObject\DataTransferObjectCollection::class );
}

if ( ! class_exists( \Spatie\DataTransferObject\ValueCaster::class, false ) ) {
	class_alias( Value_Caster::class, \Spatie\DataTransferObject\ValueCaster::class );
}

if ( ! class_exists( \Spatie\DataTransferObject\FieldValidator::class, false ) ) {
	class_alias( Field_Validator::class, \Spatie\DataTransferObject\FieldValidator::class );
}
