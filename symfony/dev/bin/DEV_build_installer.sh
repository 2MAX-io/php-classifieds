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
rm $INSTALLER_DIR/static/system/category/original_images.zip

mkdir $INSTALLER_DIR/symfony/var/cache
mkdir $INSTALLER_DIR/symfony/var/cache/upgrade
mkdir $INSTALLER_DIR/symfony/var/cache/prod

cd $INSTALLER_DIR


composer install --no-scripts --classmap-authoritative -d symfony

pwd
INSTALLER_ZIP=installer_$(date +%Y-%m-%d_%H%M%S).zip
zip -rq9 $INSTALLER_ZIP .
mv $INSTALLER_ZIP ../

#rm -r $INSTALLER_DIR
