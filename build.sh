#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag --sort=-creatordate | head -1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf FfuenfCommon FfuenfCommon-*.zip

# Build new release
mkdir -p FfuenfCommon
git archive ${commit} | tar -x -C FfuenfCommon
composer install --no-dev -n -o -d FfuenfCommon
( find ./FfuenfCommon -type d -name ".git" && find ./FfuenfCommon -name ".gitignore" && find ./FfuenfCommon -name ".gitmodules" && find ./FfuenfCommon -name ".php_cs.dist" && find ./FfuenfCommon -name "phpspec.yml" && find ./FfuenfCommon -name ".travis.yml" && find ./FfuenfCommon -name "build.sh" && find ./FfuenfCommon -name "phpunit.xml.dist" && find ./FfuenfCommon -name "tests") | xargs rm -r
zip -r FfuenfCommon-${commit}.zip FfuenfCommon