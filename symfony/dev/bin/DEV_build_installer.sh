#!/usr/bin/env bash

INSTALLER_DIR=symfony/var/installer/installer_$(date +%Y-%m-%d_%H%M%S)
echo $INSTALLER_DIR
git checkout-index -a -f --prefix=$INSTALLER_DIR/

rm -r $INSTALLER_DIR/.idea
rm -r $INSTALLER_DIR/symfony/dev
rm -r $INSTALLER_DIR/symfony/docker

rm $INSTALLER_DIR/perf.php
rm $INSTALLER_DIR/echo.php
rm $INSTALLER_DIR/echo.html
rm $INSTALLER_DIR/redirection_test.html
rm $INSTALLER_DIR/.gitignore

mkdir $INSTALLER_DIR/symfony/var/cache

INSTALLER_ZIP=symfony/var/installer/installer_$(date +%Y-%m-%d_%H%M%S).zip
echo $INSTALLER_ZIP
zip -r $INSTALLER_ZIP $INSTALLER_DIR

rm -r $INSTALLER_DIR
