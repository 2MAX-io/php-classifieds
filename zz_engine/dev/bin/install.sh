#!/usr/bin/env bash

chmod -R 775 static/
chown -R 1000:www-data static/

# for xdebug profiling to work
chown -R 1000:www-data zz_engine/docker/php/xdebug_out

git update-index --assume-unchanged install/data/test.php
