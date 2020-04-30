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

TODO: write documentation for the post type generator

# Taxonomy Generator

TODO: write documentation for the taxonomy generator

# Settings Page Generator

TODO: write documentation for the settings page generator

# CLI Command Generator

TODO: write documentation for the CLI command generator
