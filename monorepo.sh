#!/usr/bin/env bash

SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

cd "$SCRIPTDIR"

php -dxdebug.remote_enable=1 -dxdebug.remote_autostart=1 ./vendor/bin/monorepo-builder "$@"
