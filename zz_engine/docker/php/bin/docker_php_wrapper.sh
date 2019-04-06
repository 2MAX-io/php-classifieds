#!/bin/bash

# /var/www/html/zz_engine/docker/php/bin/docker_php_wrapper.sh

originalArgs=${@};
ARGS=$originalArgs
ARGS=${ARGS/172.17.0.1/192.168.205.1}
ARGS=${ARGS/-dxdebug.mode=debug/-dxdebug.mode=debug -dxdebug.start_with_request=yes}
ARGS="PHP_IDE_CONFIG=serverName=classifieds.localhost php ${ARGS}"

sudo -u www-data-docker-host $ARGS
