#!/bin/bash

originalArgs=${@};
ARGS=$originalArgs
ARGS=${ARGS//172.17.0.1/192.168.205.1}
ARGS=${ARGS//-dxdebug.mode=debug/-dxdebug.mode=debug -dxdebug.start_with_request=yes}

sudo -u www-data-docker-host PHP_IDE_CONFIG=serverName=classifieds.localhost php $ARGS
