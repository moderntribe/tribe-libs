# SquareOne Field Models

First-party array-hydrated models for ACF field data (replacing the abandoned Spatie Data Transfer Objects v2 dependency).

`Field_Model` keeps the same public API as tribe-libs 4.x: pass ACF arrays into the constructor, use typed public properties, and nest models/collections. Extension points stay camelCase (`castValue`, `castType`) for back compatibility.

## Requirements

- PHP 8.1+
- Advanced Custom Fields (PRO for repeaters)

## Basic usage

Create a class that extends [Field_Model](Field_Model.php), declare properties to match ACF array keys, and hydrate from field data.

### Built-in models

Imagine you have an [ACF User Field](https://www.advancedcustomfields.com/resources/user/). Pass the ACF field data to the existing [User Model](Models/User.php):

```php
use Tribe\Libs\Field_Models\Models\User;

$user = new User( (array) get_field( 'user_field' ) );

echo $user->ID;            // e.g. 22
echo $user->user_firstname; // e.g. Steve
```

Link and CTA fields map the same way:

```php
use Tribe\Libs\Field_Models\Models\Cta;
use Tribe\Libs\Field_Models\Models\Link;

$link = new Link( (array) get_field( 'primary_link' ) );
echo $link->url;

$cta = new Cta( (array) get_field( 'hero_cta' ) );
echo $cta->link->title; // nested Link model is hydrated automatically
```

### Custom models

```php
namespace Tribe\Project\Blocks\Hero;

use Tribe\Libs\Field_Models\Field_Model;
use Tribe\Libs\Field_Models\Models\Image;
use Tribe\Libs\Field_Models\Models\Link;

class Hero_Fields extends Field_Model {

	public string $heading = '';
	public string $body = '';
	public Image $image;
	public Link $cta;

}

// In a block / template:
$fields = new Hero_Fields( (array) get_fields() );

echo esc_html( $fields->heading );
echo esc_url( $fields->cta->url );
```

Unknown ACF keys are ignored. Missing nested models fall back to empty defaults (for example an empty `Link` with `url === ''`).

## Collections

Collections hydrate ACF repeater-style lists:

```php
use Tribe\Libs\Field_Models\Collections\User_Collection;

$user_collection = User_Collection::create( (array) get_field( 'user_repeater' ) );

foreach ( $user_collection as $user ) {
	echo $user->ID;
}
```

Or declare a collection property on a parent model (empty / `false` ACF values become an empty collection):

```php
use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Field_Models\Field_Model;

class Team_Block extends Field_Model {

	public User_Collection $members;

}

$block = new Team_Block( [
	'members' => [
		[ 'ID' => 1, 'user_firstname' => 'Ada' ],
		[ 'ID' => 2, 'user_firstname' => 'Grace' ],
	],
] );

$block->members[0]->user_firstname; // Ada
```

Swatch palettes also expose helpers for ACF / block editor output:

```php
use Tribe\Libs\Field_Models\Collections\Swatch_Collection;

$palette = Swatch_Collection::create( [
	'black' => [ 'name' => 'Black', 'slug' => 'black', 'color' => '#000000' ],
	'white' => [ 'name' => 'White', 'slug' => 'white', 'color' => '#FFFFFF' ],
] );

$acf_choices = $palette->format_for_acf();   // [ '#000000' => 'Black', ... ]
$block_colors = $palette->format_for_blocks();
$subset       = $palette->get_subset( [ 'black' ] );
```

## Custom casting (4.x → 5.x compatible)

Override `castValue` / `castType` the same way as with Spatie DTO v2. Typehints may use either the first-party classes or the Spatie aliases:

```php
use Tribe\Libs\Field_Models\DTO\Field_Validator;
use Tribe\Libs\Field_Models\DTO\Value_Caster;
use Tribe\Libs\Field_Models\Field_Model;

class Price_Field extends Field_Model {

	public int $cents = 0;

	protected function castValue( Value_Caster $valueCaster, Field_Validator $fieldValidator, $value ) {
		if ( $fieldValidator->fieldName === 'cents' && is_string( $value ) ) {
			$value = (int) round( (float) $value * 100 );
		}

		return parent::castValue( $valueCaster, $fieldValidator, $value );
	}

}
```

Projects that still typehint Spatie classes continue to work when Spatie is **not** installed — tribe-libs registers aliases:

```php
use Spatie\DataTransferObject\FlexibleDataTransferObject;
use Spatie\DataTransferObject\ValueCaster;
use Spatie\DataTransferObject\FieldValidator;

// Same runtime class as Tribe\Libs\Field_Models\Field_Model / Data_Transfer_Object
class Legacy_Fields extends FlexibleDataTransferObject {

	public string $title = '';

	protected function castValue( ValueCaster $valueCaster, FieldValidator $fieldValidator, $value ) {
		return parent::castValue( $valueCaster, $fieldValidator, $value );
	}

}
```

Prefer `Tribe\Libs\Field_Models\*` namespaces for new code.
