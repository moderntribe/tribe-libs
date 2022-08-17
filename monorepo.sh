#!/usr/bin/env bash

SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

cd "$SCRIPTDIR"

php -dxdebug.mode=debug ./vendor/bin/monorepo-builder "$@"
