#!/usr/bin/env bash

chmod -R 775 static/
chown -R 1000:www-data static/

# for xdbug profiling to work
chown -R 1000:www-data symfony/docker/php/xdebug_out
