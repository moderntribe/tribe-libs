<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\FieldValidator;
use Spatie\DataTransferObject\FlexibleDataTransferObject;
use Spatie\DataTransferObject\ValueCaster;
use Throwable;

class Field_Model extends FlexibleDataTransferObject {

	/**
	 * Override the castValue method and automatically cast values to their type.
	 *
	 * @param  \Spatie\DataTransferObject\ValueCaster     $valueCaster
	 * @param  \Spatie\DataTransferObject\FieldValidator  $fieldValidator
	 * @param  mixed                                      $value
	 *
	 * @return mixed
	 */
	protected function castValue( ValueCaster $valueCaster, FieldValidator $fieldValidator, $value ) {
		$value = $this->castType( $valueCaster, $fieldValidator, $value );

		return parent::castValue( $valueCaster, $fieldValidator, $value );
	}

	/**
	 * Attempt to automatically cast values before the DTO is validated upstream which
	 * would normally fail. If the type isn't valid, we'll attempt to cast it to the correct type.
	 * If we expect an array and the type doesn't match, we'll just reset the value, so we don't
	 * pass unexpected values to nested DTO's.
	 *
	 * @param  \Spatie\DataTransferObject\ValueCaster     $valueCaster
	 * @param  \Spatie\DataTransferObject\FieldValidator  $fieldValidator
	 * @param  mixed                                      $value
	 *
	 * @return mixed
	 */
	protected function castType( ValueCaster $valueCaster, FieldValidator $fieldValidator, $value ) {
		if ( $fieldValidator->isValidType( $value ) ) {
			return $value;
		}

		foreach ( $fieldValidator->allowedTypes as $key => $type ) {
			if ( is_subclass_of( $type, DataTransferObject::class ) ) {
				try {
					$value = new $type( (array) $value );
					break;
				} catch ( Throwable $e ) {
					continue;
				}
			} else {
				// This is supposed to be an array of models, e.g. \Some_Model[].
				if ( ! empty( $fieldValidator->allowedArrayTypes[ $key ] ) ) {
					// Ensure all empty values are an array.
					if ( empty( $value ) ) {
						$value = [];
					}

					// Try to cast to a collection first
					$values     = $valueCaster->castCollection( $value, $fieldValidator->allowedArrayTypes );
					$collection = $valueCaster->collectionType( $fieldValidator->allowedTypes );
					$value      = $collection ? new $collection( $values ) : $values;

					// Pass arrays back up to the parent class which handles casting arrays to other DTO's.
					$value = parent::castValue( $valueCaster, $fieldValidator, $value );

					break;
				}

				// ACF passed some random type, reset the value to an empty array, so we don't
				// get unexpected values.
				if ( $type === 'array' && ! is_array( $value ) ) {
					$value = [];
				}

				settype( $value, $type );
				break;
			}
		}

		return $value;
	}

}
