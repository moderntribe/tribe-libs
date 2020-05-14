# Square One Generator

A code generator utility to automate some of the repetitive tasks of building out a project
with Square One. The WP CLI commands provided by this package will create stub files to set
you on the right path for your custom work.

## Component Generator

```
wp s1 generate component <component> --properties=<properties>
```

Generates the assorted files required for a theme component.

Example usage:

```
wp s1 generate component link --properties=url,target,aria_label,content --css --js
```

This will create six files for you in the theme:

* `components/link/link.twig`
* `components/link/Link.php`
* `components/link/index.pcss`
* `components/link/css/link.pcss`
* `components/link/index.js`
* `components/link/js/link.js`

The Twig template (`link.twig`) and the Context class (`Link.php`) will be stubbed out with
the properties you provided.

If you also want to create a Controller class in the core plugin, add the `--controller` flag
to the command.

Use `--no-template` to skip the Twig template.

Use `--no-context` to skip the Context class.

The `--dry-run` flag will show you the files the command would create, without writing to the file system.

You can use this command to generate components in nested directories. For example,
`wp s1 generate component content/event ...` would create the component in `components/content/event`.
All PHP classes will have their namespaces adjusted to reflect their position in the hierarchy.

# Post Type Generator

```
wp s1 generate cpt <cpt> [--single=<single>] [--plural=<plural>]
```

Generates the files required for a custom post type.

Example usage:

```
wp s1 generate cpt document --single="Document" --plural="Documents"
```

This will create three files for you in the core plugin:

* `src/Post_Types/Document/Document.php`
* `src/Post_Types/Document/Config.php`
* `src/Post_Types/Document/Subscriber.php`

And it will add a reference to the subscriber in `Tribe\Project\Core`.

# Taxonomy Generator

```
wp s1 generate tax <taxonomy> [--post-types=<post-types>] [--single=<single>] [--plural=<plural>]
```

Generates the files required for a custom taxonomy.

Example usage:

wp s1 generate tax classification --post-types="page,post" --single="Classification" --plural="Classifications"


This will create three files for you in the core plugin:

* `src/Taxonomies/Classification/Classification.php`
* `src/Taxonomies/Classification/Config.php`
* `src/Taxonomies/Classification/Subscriber.php`

And it will add a reference to the subscriber in `Tribe\Project\Core`.

# Settings Page Generator

TODO: write documentation for the settings page generator

# CLI Command Generator

TODO: write documentation for the CLI command generator

# Image Size Generator

```
wp s1 generate image-size <name> [--width=<width>] [--height=<height>] [--ratio=<ratio>] [--crop=<crop>]
```

Adds an image size to the core plugin's `Image_Sizes` class.

Example usage:

```
wp s1 generate image-size test-size --width=1000 --ratio=0.75 --crop=left,top
```

This will add the constant `TEST_SIZE` to the `Image_Sizes` class and add this definition to the
`$sizes` array:

```
self::TEST_SIZE => [
	'width'  => 1000,
	'height' => 750,
	'crop'   => [ 'left', 'top' ],
]
```

