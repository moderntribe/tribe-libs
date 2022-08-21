# Changelog

All notable changes to this project will be documented in this file.

## Unreleased

## 4.0.13 - 2022-08-21

## 4.0.12 - 2022-08-21

## 4.0.11 - 2022-08-21

## 4.0.10 - 2022-08-21

## 4.0.9 - 2022-08-21
- Updated: monorepo release GitHub workflow to use https://github.com/symplify/monorepo-split-github-action
- Updated: monorepo-builder library to the 11.1 version
- Fixed: phpstan no longer detecting monorepo-builder packages

## 4.0.7 - 2022-08-19
- Released: `4.x` branch

## 4.0.5 - 2022-08-19
- Fixed: Added the missing https://github.com/moderntribe/square1-routes sub-repo that was never originally created.
- Added: [Create branches in sub-repos GitHub workflow](.github/workflows/sub-repo-branch-create.yml) - in order for the monorepo to create tags on specific branches, those branches need to first exist. This workflow will create a branch in all the sub-repos.

## 4.0.4 - 2022-08-18
- Bugfix: Use correct class-string docblock for Taxonomy_Subscriber.

## 4.0.3 - 2022-08-17
- Changed: Create the `4.x` branch/major version, which has a minimum version of `PHP7.4` for use with **legacy PHP7.4 Square One projects where their hosts will be force upgrading to PHP8.0**. This is intermediate release to allow an ease of upgrading, however new projects should use the upcoming `5.x` releases which will be optimized for PHP8.0+.
- Changed: `composer.lock` has been removed to be more in line with other monorepo libraries and to allow more flexible versions of packages to be installed when using Tribe Libs in the Square One framework.
- Changed: Force `phpcompatibility/php-compatibility` to a development version to prevent PHPCS errors when using PHP8.0+.
- Added: New repo: [Square1 Field Models](https://github.com/moderntribe/square1-field-models) now that we are on PHP7.4+
- Added: New repo: [Square1 WP Downloader](https://github.com/moderntribe/square1-wp-downloader) that adds a CLI tool to download WordPress and plugins for automated testing purposes.
- Added: `composer test:setup` to automate downloading test dependencies, `composer test:integration` to run integration tests.


## 3.6.0 - 2022-08-09
- Added: `wp s1 generate block <name> --with-post-loop-middleware` that gives a base configuration for a block with Post Loop Middleware.
- Changed: The `Block_Config` class now uses the `With_Field_Prefix` out of the box so those methods are available to all blocks when needed.
- Changed: `Block_Config`'s generated with the block generator now use `\Tribe\Project\Blocks\Block_Category::CUSTOM_BLOCK_CATEGORY_SLUG` if the project has it available, otherwise it uses the `text` category.

## 3.5.1 - 2022-08-05
- Added: `With_Field_Prefix` trait to make fetching full key names easier for conditional logic and block middleware.

## 3.5.0 - 2022-07-20
- Added: `wp s1 generate block:middleware <name>` CLI command to create sample block middleware.
- Added: `wp s1 generate block <name> --with-middleware` that adds the appropriate interface and a stub middleware parameters method to a generated block config.
- Updated: Block model generator template `model.php.tmpl` will use a different method name depending on if block middleware is detected in the version of Square One this library is included in.
- Added: `With_Field_Finder` trait, that can search a block object's fields for a specific field key.

## 3.4.18 - 2022-06-30
- Updated: `so wp s1 generate block <name>` will use `esc_html__()` instead of `__()` for field labels.

## 3.4.17 - 2022-06-20
- Updated: Block_Config class to allow for mutation for upcoming block middleware.

## 3.4.16 - 2022-06-09
- Updated: security updates for mustache / guzzle.

## 3.4.15 - 2022-05-25
- Added: `With_Get_Field` ACF trait.
- Fixed: Added missing `thenReturn()` method to the Pipeline interface.

## 3.4.14 - 2022-04-26
- Fixed: tests that use `wp_mail` with the latest WordPress, making validation fail with `Invalid address:  (From): wordpress@localhost` by adjusting the test domains we use to build a valid email address.

## 3.4.13 - 2022-04-05
- Updated the `wp s1 genereate block` command to generate block model views using the container as a companion update to [#970](https://github.com/moderntribe/square-one/pull/970).

## 3.4.12 - 2022-03-24
- Fixed Missing titles and missing post type prefixes in P2P metaboxes (take 2)

## 3.4.11 - 2022-03-23
- Fixed missing post titles in the P2P metabox
- Added command constants to make building WP CLI commands with arguments and options easier.
- Added a "mutable container", to allow us to flush the PHP-DI container in specific situations and create completely fresh instances.
- Added `wp s1 queues run` command, which is now used by `wp s1 queues process <name>` command to run each queue task as a child PHP process.
- Updated the Cron Queue processing to also create task instances from the container.
- Updated the MySQL Queue backend to force use UTC time for comparing jobs.

## 3.4.10 - 2022-02-11
- Fixed nesting issue when using ACF classes to build Flexible Content Fields with Layout Fields in [#105](https://github.com/moderntribe/tribe-libs/pull/105)
- Allow composer plugins in [#108](https://github.com/moderntribe/tribe-libs/pull/108)

## 3.4.9 - 2022-02-10
- Update block templates to match coding standards (#98)

## 3.4.8 - 2021-11-29
- Added `.gitattributes` file to make package smaller
- Added the Pipeline feature
- Added custom Log package to use WordPress actions to log via Monolog.
- Fixed allowing composer v1 + v2.
- Fixed Routes having the wrong PHP version.
- Added informative message when running the `wp s1 queues process` CLI command.

## 3.4.7 - 2021-11-22
- Added `--timeout=<time in seconds>` to the `wp s1 queues process` command
- Updated Queue Tasks to be created by the container so that dependency injection works.
- Update Queues documentation with examples.

## 3.4.6 - 2021-09-10

## 3.4.5 - 2021-09-10
- Update wp-cli to ^2.5
- Replace deleted repo https://github.com/hautelook/phpass with https://github.com/bordoni/phpass
- Updated wp-browser to 3.0.9
- Updated wp-config-environment.php from square-one

## 3.4.4 - 2021-07-14

- Add composer v2 support
- Fix cron queues from cleaning up before finishing

## 3.4.3 - 2021-07-14

- Fix router query vars

## v3.4.3 - 2021-07-14

- Fix query vars in routing package

## v3.4.2 - 2021-04-29

 - Adds docs for routing

## v3.4.1 - 2021-04-22

 - Adds update for routing

## v3.4.0 - 2020-11-23

- Added a CLI block generator

## v3.3.0 - 2020-11-04

- Reworks Block_Config to remove sections and add method if doing this in the plugin instead
- Adds Field_Section

## v3.2.1 - 2020-10-27

- Force Composer v1 instead of v2 to avoid failing installs and tests due to unsupported dependencies.
- Adds CLI helper method for freeing up memory during long running processes.

## v3.2.0 - 2020-10-23

- Added SVG support to the Media package. This replaces the Safe SVG plugin with a more
  robust implementation that efficiently handles sanitization, scaling, and metadata
  regeneration. This is backwards compatible, but to take advantage of SVG minification,
  the new `Media_Definer` should be registered with the DI container.

## v3.1.2 - 2020-09-24

- Added Field_Group class
- Improvements to Repeater and Block_Config

## v3.1.1 - 2020-09-15

- Added missing packages to the monorepo config

## v3.1.0 - 2020-09-03

- Update release script for monorepo-builder 8.0 compatibility
- Minimum PHP version bump to 7.2

## v3.0.0 - 2020-09-01

- Many updates to libs in support of the SquareOne framework Fidgety Feet epic.

## v2.1.2 - 2020-04-02

- Added post type registration for blog copier internal post type for compatibility with WP 5.1+

## v2.1.1 - 2020-02-22

- Ignored and removed OS/IDE files

## v2.1.0 - 2020-02-22

- Added support for ACF Gutenberg Blocks

## v2.0.0 - 2019-10-07

- Migrated repository to a monorepo structure
- Migrated queues package from Square One
