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
rm $INSTALLER_DIR/.gitattributes
rm $INSTALLER_DIR/LICENSE_LICENCJA_UnRsurDMXRqbXu3XeNPys.txt

cp ${PROJECT_DIR}/install/data/example/listing.large.sql $INSTALLER_DIR/install/data/example/listing.large.sql

mkdir $INSTALLER_DIR/zz_engine/var/cache
mkdir $INSTALLER_DIR/zz_engine/var/cache/upgrade
mkdir $INSTALLER_DIR/zz_engine/var/cache/prod

cd $INSTALLER_DIR

composer install --no-scripts --classmap-authoritative --quiet --no-dev --no-interaction -d zz_engine

INSTALLER_ZIP=installer_$(date +%Y-%m-%d_%H%M%S)_$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 7 | head -n 1).zip

zip -rq9 $INSTALLER_ZIP .

mv $INSTALLER_ZIP ${PROJECT_DIR}/zz_engine/var/installer
rm -r ${PROJECT_DIR}/${INSTALLER_DIR}
