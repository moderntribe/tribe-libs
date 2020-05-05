Tribe Libs is a collection of libraries created by Modern Tribe
for use with Square One-based WordPress projects. It is required by the
Square One core plugin.

## Installation

```
composer require moderntribe/tribe-libs:dev-master
```

## Usage

All usage documentation lives in the [Square One repository](https://github.com/moderntribe/square-one/tree/master/docs).

## Support

Usage of Tribe Libs is not actively supported by Modern Tribe outside of client contracts. Pull requests and suggestions are welcome and will be addressed based on business need.

## Release Process

This library comprises a large collection of smaller libraries that can be included
in whole or in part on Square One projects. These libraries are developed
following the [monorepo model](https://gomonorepo.org/). All changes are
committed to this, the parent project. Maintenance of the individual packages
is managed using the [Monorepo Builder](https://github.com/Symplify/MonorepoBuilder) utility.

### Releasing a new version

1. Ensure that all code for the release is merged to `master`
1. Ensure that all updates for the release are logged in `CHANGELOG.md`
1. Run the release script, with the version number for the release (format: `v<major>.<minor>.<patch>`):
   ```
   ./monorepo.sh release v3.0.0
   ```
1. The script will handle several steps for you automatically:
   1. Set any package interdependencies to the new version
   1. Update `CHANGELOG.md` with the appropriate version number
   1. Create the git tag and push it to GitHub
   1. Bump the `master` branch version to the next minor version number
1. When the tag is pushed to GitHub, an Action there will automatically split the monorepo and deploy the tag
   to all of the package repos.

### Adding Packages

1. Create a new directory for the package in `src`. Create your code
   there, include an independent `composer.json` for the package, and
   commit it to tribe-libs.
1. Create an empty public GitHub repository for the package. Follow the
   naming convention `moderntribe/square1-*`. Ensure that the user `tr1b0t`
   has write access to the rpo.
1. Add the directory and repo to the `directories_to_repositories` map
   in `monorepo-builder.yml`.
1. Run the script to merge the package `composer.json` files to the root
   `composer.json` file:
   ```
   ./monorepo.sh merge
   ```
1. After the next release, Register the package on [Packagist](https://packagist.org/packages/submit).

### Adding Composer Dependencies

1. Add the dependency to `composer.json` in the package(s) that needs it.
1. Run the `merge` command to merge dependencies up to the root `composer.json`
   ```
   ./monorepo.sh merge
   ```

### Update the development version

You will rarely need to do this, but it is documented here just in case.

1. Set the `master` branch alias for all packages
   ```
   ./monorepo.sh set-alias 3.0
   ```
1. Bump the interdependencies among the packages to the same version.
   ```
   ./monorepo.sh bump-interdependency "^3.0"
   ```

### Run a release without GitHub Actions

A release should be automatically deployed to all package repos by GitHub Actions whenever a tag
is pushed to GitHub. That is really just running this command (but with appropriate permissions to push to all repos):

```
./monorepo.sh split --tag="v3.0.0"
```
