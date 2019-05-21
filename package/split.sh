#!/usr/bin/env bash

set -e

SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
PROJECTDIR=$( cd "$SCRIPTDIR" && cd ../.. && pwd )

cd "$SCRIPTDIR"

OWNER="moderntribe"
BRANCH="feature/monorepo"

docker build -t tribe-libs-package:latest .

if ! [ -e "$SCRIPTDIR/.subsplit" ]
then
	docker run --rm \
		-v "$SCRIPTDIR":/project \
		-v ~/.ssh:/root/.ssh \
		-w "/project" \
		tribe-libs-package:latest \
		bash -c '( git subsplit init "git@github.com:moderntribe/tribe-libs.git" )'
fi

docker run --rm \
	-v "$SCRIPTDIR/":/project \
	-v ~/.ssh:/root/.ssh \
	-w "/project" \
	tribe-libs-package:latest \
	bash -c '( git subsplit update --heads="feature/monorepo" )'

docker run --rm \
	-v "$SCRIPTDIR/":/project \
	-v ~/.ssh:/root/.ssh \
	-w "/project" \
	tribe-libs-package:latest \
	bash -c "( git subsplit publish '
		src/ACF:git@github.com:${OWNER}/square1-acf.git
		src/Assets:git@github.com:${OWNER}/square1-assets.git
		src/Blog_Copier:git@github.com:${OWNER}/square1-blog-copier.git
		src/Cache:git@github.com:${OWNER}/square1-cache.git
		src/CLI:git@github.com:${OWNER}/square1-cli.git
		src/Container:git@github.com:${OWNER}/square1-container.git
		src/Generators:git@github.com:${OWNER}/square1-generators.git
		src/Nav:git@github.com:${OWNER}/square1-nav.git
		src/Object_Meta:git@github.com:${OWNER}/square1-object-meta.git
		src/Oembed:git@github.com:${OWNER}/square1-oembed.git
		src/P2P:git@github.com:${OWNER}/square1-p2p.git
		src/Post_Meta:git@github.com:${OWNER}/square1-post-meta.git
		src/Post_Type:git@github.com:${OWNER}/square1-post-type.git
		src/Queues:git@github.com:${OWNER}/square1-queues.git
		src/Schema:git@github.com:${OWNER}/square1-schema.git
		src/Settings:git@github.com:${OWNER}/square1-settings.git
		src/Taxonomy:git@github.com:${OWNER}/square1-taxonomy.git
		src/User:git@github.com:${OWNER}/square1-user.git
		src/Utils:git@github.com:${OWNER}/square1-utils.git
	 ' --heads='${BRANCH}' --debug )"