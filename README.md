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

### Maintaining packages

Merge the packages' composer.json files into the parent composer.json
```
vendor/bin/monorepo-builder merge
```

Bump the version numbers for package interdependencies
```
vendor/bin/monorepo-builder bump-interdependency "^2.0"
```

Update the `master` branch alias to the latest dev version
```
vendor/bin/monorepo-builder package-alias
```

Push the latest tag and the `master` branch to all the package repositories.
```
vendor/bin/monorepo-builder split
```

### Releasing a new version

All of the above commands are wrapped up into a `release` command to help
with tagging a new version.

```
vendor/bin/monorepo-builder release 2.0.0
```
