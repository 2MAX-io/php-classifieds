#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PROJECT_DIR=$DIR/../../..

INSTALLER_DIR=zz_engine/var/installer/installer_$(date +%Y-%m-%d_%H%M%S)
git checkout-index -a -f --prefix=${INSTALLER_DIR}/

rm -r $INSTALLER_DIR/.idea
rm -r $INSTALLER_DIR/zz_engine/dev
rm -r $INSTALLER_DIR/zz_engine/docker

rm $INSTALLER_DIR/perf.php
rm $INSTALLER_DIR/echo.php
rm $INSTALLER_DIR/echo.html
rm $INSTALLER_DIR/redirection_test.html
rm $INSTALLER_DIR/.gitignore
rm $INSTALLER_DIR/static/system/category/original_images.zip

mkdir $INSTALLER_DIR/zz_engine/var/cache
mkdir $INSTALLER_DIR/zz_engine/var/cache/upgrade
mkdir $INSTALLER_DIR/zz_engine/var/cache/prod

cd $INSTALLER_DIR

composer install --no-scripts --classmap-authoritative --quiet --no-dev --no-interaction -d zz_engine

INSTALLER_ZIP=installer_$(date +%Y-%m-%d_%H%M%S).zip

zip -rq9 $INSTALLER_ZIP .

echo $PROJECT_DIR/zz_engine/var/installer

mv $INSTALLER_ZIP ${PROJECT_DIR}/zz_engine/var/installer
rm -r ${PROJECT_DIR}/${INSTALLER_DIR}
