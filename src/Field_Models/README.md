# SquareOne Field Models

Utilizes [Spatie Data Transfer Objects v2](https://github.com/spatie/data-transfer-object/tree/v2) to provide models for ACF fields that support arrays, avoiding needing to remember these field structures and providing autocompletion for your IDE.

## Usage

Simply create a class that extends [Field_Model](Field_Model.php), set your properties and types to match what ACF returns when it fetches the array of field data, pass that array to your model and it will get automatically populated, allowing you to use the class properties to access the data.

## Examples

Imagine you have an [ACF User Field](https://www.advancedcustomfields.com/resources/user/). Simply pass the ACF field data to the existing [User Model](Models/User.php), for example:

```php

$user = new \Tribe\Libs\Field_Models\Models\User( (array) get_field( 'user_field' ) );

echo $user->ID; // e.g. 22
echo $user->user_firstname; // e.g. Steve
// etc...
```

You can also make Collections, which will take [ACF Repeater](https://www.advancedcustomfields.com/resources/repeater/) data. Expanding on the User example above, imagine you have a repeater of User fields, simply pass the repeater data directly to the collection's create method:

```php
$user_collection = \Tribe\Libs\Field_Models\Collections\User_Collection::create( (array) get_field( 'user_repeater' ) );

foreach ( $user_collection as $user ) {
    echo $user->ID; 
}
```

## Requirements

- PHP 7.4+
- Advanced Custom Fields Or, the PRO version for repeaters
